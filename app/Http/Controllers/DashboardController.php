<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\User;
use App\Models\ItemRequest;
use App\Models\Unit;
use App\Models\Lokasi;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermissionTo('view-dashboard')) {
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki izin untuk melihat dashboard.');
        }

        if (Auth::user()->hasAnyRole(['Admin', 'StafGudang'])) {
            return app(OperationsDashboardController::class)->index();
        }

        // Dashboard Viewer sengaja dibatasi sebagai katalog barang. Viewer tidak
        // memerlukan statistik operasional maupun data pengajuan pengguna lain.
        if (Auth::user()->hasRole('Viewer')) {
            $kategoris = Kategori::query()
                ->with(['barangs' => function ($query) {
                    $query->where('status', 'aktif')
                        ->with(['unit', 'lokasi'])
                        ->orderBy('nama_barang');
                }])
                ->orderBy('nama_kategori')
                ->get()
                ->map(fn (Kategori $kategori) => [
                    'id' => $kategori->id,
                    'name' => $kategori->nama_kategori,
                    'items' => $kategori->barangs->map(fn (Barang $barang) => [
                        'id' => $barang->id,
                        'name' => $barang->nama_barang,
                        'code' => $barang->kode_barang,
                        'type' => $barang->tipe_item === 'aset' ? 'Aset' : 'Habis Pakai',
                        'location' => $barang->lokasi?->nama_lokasi,
                        'stock' => $barang->stok,
                        'minimumStock' => $barang->stok_minimum,
                        'unit' => $barang->unit?->singkatan_unit ?? $barang->unit?->nama_unit,
                    ])->values(),
                ])->values();

            $lowStockCount = Barang::query()
                ->where('status', 'aktif')
                ->where('stok_minimum', '>', 0)
                ->whereColumn('stok', '<=', 'stok_minimum')
                ->count();

            $requestCounts = ItemRequest::query()
                ->where('user_id', Auth::id())
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            $requestSummary = collect(['Diajukan', 'Disetujui', 'Diproses', 'Ditolak', 'Selesai'])
                ->map(fn (string $status) => [
                    'status' => $status,
                    'count' => $requestCounts->get($status, 0),
                ])
                ->values();

            $recentRequests = ItemRequest::query()
                ->where('user_id', Auth::id())
                ->with('barang:id,nama_barang')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (ItemRequest $request) => [
                    'id' => $request->id,
                    'item' => $request->barang?->nama_barang ?? 'Barang tidak tersedia',
                    'type' => $request->tipe_pengajuan === 'peminjaman' ? 'Peminjaman' : 'Permintaan',
                    'status' => $request->status,
                    'submittedAt' => $request->created_at?->translatedFormat('d M Y'),
                ])
                ->values();

            $dashboardData = [
                'categories' => $kategoris,
                'overview' => [
                    ['label' => 'Barang tersedia', 'value' => Barang::where('status', 'aktif')->count(), 'icon' => 'bi-box-seam', 'theme' => 'emerald'],
                    ['label' => 'Kategori', 'value' => $kategoris->count(), 'icon' => 'bi-grid-3x3-gap', 'theme' => 'violet'],
                    ['label' => 'Stok menipis', 'value' => $lowStockCount, 'icon' => 'bi-exclamation-triangle', 'theme' => 'orange'],
                    ['label' => 'Pengajuan aktif', 'value' => $requestCounts->only(['Diajukan', 'Disetujui', 'Diproses'])->sum(), 'icon' => 'bi-clipboard-check', 'theme' => 'cyan'],
                ],
                'requestSummary' => $requestSummary,
                'recentRequests' => $recentRequests,
                'quickLinks' => [
                    'newRequest' => route('pengajuan.barang.pilihTipe'),
                    'myRequests' => route('pengajuan.barang.index'),
                ],
            ];

            return view('dashboard-viewer', compact('dashboardData'));
        }

        // --- Data Statistik untuk Kartu ---
        $totalBarang = Barang::count();
        $totalKategori = Kategori::count();
        $totalUnit = Unit::count();
        $totalLokasi = Lokasi::count();
        $totalPengguna = User::count();
        $pengajuanDiajukan = ItemRequest::where('status', 'Diajukan')->count();
        $pengajuanDisetujui = ItemRequest::where('status', 'Disetujui')->count();
        $barangAktif = Barang::where('status', 'aktif')->count();

        // Barang Keluar 30 Hari Terakhir
        $barangKeluar30Hari = StockMovement::where('tipe_pergerakan', 'keluar')
                                           ->where('tanggal_pergerakan', '>=', now()->subDays(30))
                                           ->sum('kuantitas');

        // Barang Masuk 30 Hari Terakhir
        $barangMasuk30Hari = StockMovement::whereIn('tipe_pergerakan', ['masuk', 'koreksi-tambah', 'pengembalian'])
                                          ->where('tanggal_pergerakan', '>=', now()->subDays(30))
                                          ->sum('kuantitas');

        // Data Bulan Ini
        $barangMasukBulanIni = StockMovement::whereIn('tipe_pergerakan', ['masuk', 'koreksi-tambah', 'pengembalian'])
                                            ->whereYear('tanggal_pergerakan', now()->year)
                                            ->whereMonth('tanggal_pergerakan', now()->month)
                                            ->sum('kuantitas');

        $barangKeluarBulanIni = StockMovement::where('tipe_pergerakan', 'keluar')
                                             ->whereYear('tanggal_pergerakan', now()->year)
                                             ->whereMonth('tanggal_pergerakan', now()->month)
                                             ->sum('kuantitas');

        $totalTransaksiBulanIni = StockMovement::whereYear('tanggal_pergerakan', now()->year)
                                               ->whereMonth('tanggal_pergerakan', now()->month)
                                               ->count();

        // === DATA BARU UNTUK PANEL AKSI CEPAT & GRAFIK ===

        // 1. Ambil 5 pengajuan terbaru yang menunggu persetujuan (untuk Admin/Staf)
        $pengajuanMenunggu = null;
        if (Auth::user()->hasPermissionTo('pengajuan-barang-approve')) {
            $pengajuanMenunggu = ItemRequest::where('status', 'Diajukan')
                                            ->with('pemohon', 'barang')
                                            ->latest()
                                            ->take(5)
                                            ->get();
        }

        // 2. Ambil 5 barang yang stoknya kritis
        $barangStokKritis = Barang::where('status', 'aktif')
                                  ->where('stok_minimum', '>', 0)
                                  ->whereColumn('stok', '<=', 'stok_minimum')
                                  ->with('kategori')
                                  ->orderBy('stok', 'asc')
                                  ->take(5)
                                  ->get();

        // 3. Data untuk Grafik Status Barang (Donut Chart)
        $statusData = Barang::query()
                           ->select('status as status_barang', DB::raw('count(*) as jumlah'))
                           ->groupBy('status')
                           ->get();

        // 4. Data untuk Grafik Pergerakan Stok 6 Bulan Terakhir (Bar Chart)
        $monthExpression = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', tanggal_pergerakan)"
            : 'DATE_FORMAT(tanggal_pergerakan, "%Y-%m")';

        $pergerakanStokData = StockMovement::query()
            ->select(
                DB::raw("{$monthExpression} as bulan"),
                                    DB::raw('SUM(CASE WHEN tipe_pergerakan IN ("masuk", "koreksi-tambah", "pengembalian") THEN kuantitas ELSE 0 END) as total_masuk'),
                                    DB::raw('SUM(CASE WHEN tipe_pergerakan IN ("keluar", "koreksi-kurang") THEN kuantitas ELSE 0 END) as total_keluar')
                                )
                                ->where('tanggal_pergerakan', '>=', now()->subMonths(6))
                                ->groupBy('bulan')
                                ->orderBy('bulan', 'asc')
                                ->get();

        // Kirim semua variabel ke view
        return view('dashboard', compact(
            'totalBarang', 
            'totalKategori',
            'totalUnit',
            'totalLokasi',
            'totalPengguna', 
            'pengajuanDiajukan', 
            'pengajuanDisetujui',
            'barangAktif',
            'barangKeluar30Hari',
            'barangMasuk30Hari',
            'statusData', 
            'pergerakanStokData',
            'pengajuanMenunggu',
            'barangStokKritis'
        ));
    }
}
