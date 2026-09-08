<?php

namespace App\Http\Controllers;

use App\Models\{Barang, ItemRequest, Kategori, StockMovement};
use Illuminate\Support\Facades\Auth;

class OperationsDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->hasAnyRole(['Admin', 'StafGudang']) && $user->can('view-dashboard'), 403);
        $canManage = $user->can('pengajuan-barang-list-all');
        $requests = ItemRequest::query();
        if (!$canManage) {
            $requests->where('user_id', $user->id);
        }
        $loans = (clone $requests)->where('tipe_pengajuan', 'peminjaman')->where('status', 'Diproses');
        $critical = Barang::where('status', 'aktif')->where(function ($q) {
            $q->where('stok', '<=', 0)->orWhere(function ($q) {
                $q->where('stok_minimum', '>', 0)->whereColumn('stok', '<=', 'stok_minimum');
            });
        });
        // Count transactions, not quantities with incompatible units (pcs, boxes, kg).
        $months = collect(range(5, 0))->map(function ($offset) {
            $month = now()->startOfMonth()->subMonths($offset);
            $movements = StockMovement::whereBetween('tanggal_pergerakan', [$month->toDateString(), $month->copy()->endOfMonth()->toDateTimeString()]);
            return [
                'label' => $month->format('M Y'),
                'incoming' => (clone $movements)->whereIn('tipe_pergerakan', ['masuk', 'pengembalian'])->count(),
                'outgoing' => (clone $movements)->where('tipe_pergerakan', 'keluar')->count(),
            ];
        });
        $mapRequest = fn ($r) => [
            'id' => $r->id, 'item' => $r->barang?->nama_barang ?? 'Barang dihapus',
            'person' => $r->pemohon?->name ?? '-', 'status' => $r->status,
            'due' => $r->jatuh_tempo_pengembalian?->format('d M Y'),
            'url' => $canManage ? route('admin.pengajuan.barang.show', $r) : route('pengajuan.barang.index'),
        ];
        $queue = (clone $requests)->where('user_id', '!=', $user->id)->whereIn('status', ['Diajukan', 'Disetujui']);
        $dashboardData = [
            'name' => $user->name, 'admin' => $user->hasRole('Admin'), 'date' => now()->format('d M Y'),
            'stats' => [
                ['label' => 'Jenis barang aktif', 'value' => Barang::where('status', 'aktif')->count(), 'hint' => 'Seluruh katalog inventaris'],
                ['label' => 'Perlu restock', 'value' => (clone $critical)->count(), 'hint' => 'Stok habis atau mencapai minimum'],
                ['label' => 'Menunggu approval', 'value' => (clone $queue)->where('status', 'Diajukan')->count(), 'hint' => 'Pengajuan dari pengguna lain'],
                ['label' => 'Peminjaman berjalan', 'value' => (clone $loans)->count(), 'hint' => 'Sudah diserahkan, belum kembali'],
            ],
            'months' => $months,
            'categories' => Kategori::withCount('barangs')->orderByDesc('barangs_count')->get()->map(fn ($c) => ['label' => $c->nama_kategori, 'value' => $c->barangs_count]),
            'queue' => $queue->with(['barang', 'pemohon'])->oldest()->limit(6)->get()->map($mapRequest),
            'overdue' => (clone $loans)->whereDate('jatuh_tempo_pengembalian', '<', today())->with(['barang', 'pemohon'])->orderBy('jatuh_tempo_pengembalian')->limit(5)->get()->map($mapRequest),
            'critical' => $critical->with('unit')->orderBy('stok')->limit(6)->get()->map(fn ($b) => [
                'id' => $b->id, 'name' => $b->nama_barang, 'stock' => $b->stok,
                'minimum' => $b->stok_minimum, 'unit' => $b->unit?->singkatan_unit,
                'url' => $user->can('barang-show') ? route('barang.show', $b) : null,
            ]),
            'personal' => ItemRequest::where('user_id', $user->id)->with(['barang', 'pemohon'])->latest()->limit(3)->get()->map($mapRequest),
            'links' => [
                'request' => $user->can('pengajuan-barang-create') ? route('pengajuan.barang.create', 'permintaan') : null,
                'loan' => $user->can('pengajuan-barang-create') ? route('pengajuan.barang.create', 'peminjaman') : null,
                'personal' => $user->can('pengajuan-barang-list-own') ? route('pengajuan.barang.index') : null,
                'incoming' => $user->can('stok-masuk-create') ? route('stok.masuk.create') : null,
                'queue' => $canManage ? route('admin.pengajuan.barang.index') : null,
            ],
        ];
        return view('dashboard-operations', compact('dashboardData'));
    }
}
