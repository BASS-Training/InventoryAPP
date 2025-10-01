@extends('layouts.app')

@section('title', 'Laporan Maintenance & Pembayaran')

@section('content')
<div class="container-fluid">
    {{-- Header Section with Quick Stats --}}
    <div class="row align-items-center mb-4">
        <div class="col-lg-6">
            <h1 class="fw-bold mb-1" style="color: #1f2937; font-size: 1.75rem;">Laporan Maintenance & Pembayaran</h1>
            <p class="text-muted mb-0 small">Monitor dan analisis data maintenance serta pembayaran rutin</p>
        </div>
        <div class="col-lg-6">
            <div class="d-flex justify-content-lg-end gap-2">
                <button class="btn btn-light btn-sm" onclick="window.print()" style="border-radius: 6px; border: 1px solid #e5e7eb;">
                    <i class="bi bi-printer me-1"></i> Cetak
                </button>
                <button class="btn btn-light btn-sm" style="border-radius: 6px; border: 1px solid #e5e7eb;">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <button class="btn btn-primary btn-sm" style="border-radius: 6px;">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    {{-- Compact Filter Panel --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body p-3">
            <form action="{{ route('laporan.maintenance') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label text-white small mb-1">Jenis</label>
                        <select class="form-select form-select-sm" name="jenis" style="border-radius: 6px;">
                            <option value="semua" {{ $filterJenis == 'semua' ? 'selected' : '' }}>Semua</option>
                            <option value="maintenance" {{ $filterJenis == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="pembayaran" {{ $filterJenis == 'pembayaran' ? 'selected' : '' }}>Pembayaran</option>
                        </select>
                    </div>
                    
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label text-white small mb-1">Barang</label>
                        <select class="form-select form-select-sm" name="barang_id" style="border-radius: 6px;">
                            <option value="">Semua</option>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}" {{ $filterBarangId == $barang->id ? 'selected' : '' }}>
                                    {{ Str::limit($barang->nama_barang, 15) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label text-white small mb-1">Status</label>
                        <select class="form-select form-select-sm" name="status" style="border-radius: 6px;">
                            <option value="">Semua</option>
                            <option value="Dijadwalkan" {{ $filterStatus == 'Dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                            <option value="Selesai" {{ $filterStatus == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="aktif" {{ $filterStatus == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $filterStatus == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                    </div>
                    
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label text-white small mb-1">Dari</label>
                        <input type="date" class="form-control form-control-sm" name="tanggal_mulai" value="{{ $filterTanggalMulai }}" style="border-radius: 6px;">
                    </div>
                    
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label text-white small mb-1">Sampai</label>
                        <input type="date" class="form-control form-control-sm" name="tanggal_akhir" value="{{ $filterTanggalAkhir }}" style="border-radius: 6px;">
                    </div>
                    
                    <div class="col-6 col-md-4 col-lg-2">
                        <button type="submit" class="btn btn-light btn-sm w-100" style="border-radius: 6px;">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Compact Summary Widgets Grid --}}
    <div class="row g-3 mb-4">
        {{-- Total Keseluruhan --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 h-100 shadow-sm widget-card" style="border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="widget-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 small">Total</span>
                    </div>
                    <h4 class="text-white fw-bold mb-1" style="font-size: 1.25rem;">
                        {{ number_format(($grandTotalTerealisasi + $grandTotalWillCome) / 1000000, 1) }}jt
                    </h4>
                    <p class="text-white-50 mb-0" style="font-size: 0.75rem;">Total Keseluruhan</p>
                </div>
            </div>
        </div>

        {{-- Terealisasi --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 h-100 shadow-sm widget-card" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="widget-icon success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <span class="trend-badge up">
                            <i class="bi bi-arrow-up"></i> 12%
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #10b981; font-size: 1.25rem;">
                        {{ number_format($grandTotalTerealisasi / 1000000, 1) }}jt
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Terealisasi</p>
                </div>
            </div>
        </div>

        {{-- Akan Datang --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 h-100 shadow-sm widget-card" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="widget-icon warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <span class="trend-badge down">
                            <i class="bi bi-arrow-down"></i> 5%
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #f59e0b; font-size: 1.25rem;">
                        {{ number_format($grandTotalWillCome / 1000000, 1) }}jt
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Akan Datang</p>
                </div>
            </div>
        </div>

        {{-- Maintenance --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 h-100 shadow-sm widget-card" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="widget-icon primary">
                            <i class="bi bi-tools"></i>
                        </div>
                        <span class="badge bg-light text-primary px-2 py-1 small">
                            {{ $maintenances->total() }}
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #3b82f6; font-size: 1.25rem;">
                        {{ number_format($totalMaintenanceTerealisasi / 1000000, 1) }}jt
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Maintenance</p>
                </div>
            </div>
        </div>

        {{-- Pembayaran --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 h-100 shadow-sm widget-card" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="widget-icon info">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <span class="badge bg-light text-info px-2 py-1 small">
                            {{ $payments->total() }}
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #06b6d4; font-size: 1.25rem;">
                        {{ number_format($totalPaymentTerealisasi / 1000000, 1) }}jt
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Pembayaran</p>
                </div>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 h-100 shadow-sm widget-card" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="widget-icon purple">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <span class="badge bg-light text-purple px-2 py-1 small">Auto</span>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #8b5cf6; font-size: 1.25rem;">
                        {{ number_format($totalMaintenanceScheduleTerealisasi / 1000000, 1) }}jt
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Berulang</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Mini Stats Cards --}}
    <div class="row g-3 mb-4 clearfix">  <!-- Tambah class="clearfix" -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 36px; height: 36px; background-color: #3b82f615;">
                            <i class="bi bi-wrench" style="color: #3b82f6; font-size: 16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $maintenanceHariIni }}</h5>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Maintenance Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 36px; height: 36px; background-color: #ef444415;">
                            <i class="bi bi-exclamation-triangle" style="color: #ef4444; font-size: 16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $pembayaranJatuhTempo }}</h5>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Pembayaran Jatuh Tempo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 36px; height: 36px; background-color: #10b98115;">
                            <i class="bi bi-check2-all" style="color: #10b981; font-size: 16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $selesaiMingguIni }}</h5>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Selesai Minggu Ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 36px; height: 36px; background-color: #6b728015;">
                            <i class="bi bi-building" style="color: #6b7280; font-size: 16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $vendorAktif }}</h5>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Vendor Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section with Compact Layout --}}
    <div class="row g-3 mb-4 clearfix">  <!-- Tambah class="clearfix" -->
        {{-- Trend Chart --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0" style="color: #1f2937;">Tren Biaya 12 Bulan</h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active">Bulan</button>
                            <button type="button" class="btn btn-outline-secondary">Kuartal</button>
                            <button type="button" class="btn btn-outline-secondary">Tahun</button>
                        </div>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="chartBiayaGabungan"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Pie Charts --}}
        <div class="col-lg-4">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                        <div class="card-body p-3">
                            <h6 class="fw-semibold mb-3" style="color: #1f2937;">Status Maintenance</h6>
                            <div style="height: 130px;">
                                <canvas id="chartMaintenanceStatus"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                        <div class="card-body p-3">
                            <h6 class="fw-semibold mb-3" style="color: #1f2937;">Kategori Pembayaran</h6>
                            <div style="height: 130px;">
                                <canvas id="chartPaymentKategori"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Activity Timeline & Recent Items --}}
    <div class="row g-3 mb-4 clearfix">  <!-- Tambah class="clearfix" -->
        {{-- Activity Timeline --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <h6 class="fw-semibold mb-3" style="color: #1f2937;">Aktivitas Terkini</h6>
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $activity->activity_type }}"></div>
                            <div class="timeline-content">
                                <p class="mb-0 small fw-medium">{{ $activity->text }}</p>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($activity->time)->diffForHumans() }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Maintenance --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0" style="color: #1f2937;">Maintenance Terbaru</h6>
                        <a href="#" class="text-primary text-decoration-none small">Lihat Semua</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($maintenances->take(5) as $item)
                        <div class="list-group-item px-0 py-2 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="mb-0 fw-medium small">{{ Str::limit($item->nama_perbaikan, 25) }}</p>
                                    <span class="text-muted" style="font-size: 0.7rem;">
                                        {{ $item->barang->nama_barang ?? 'Umum' }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <span class="badge rounded-pill px-2 py-1 small
                                        @if($item->status == 'Selesai') bg-success
                                        @elseif($item->status == 'Dijadwalkan') bg-warning text-dark
                                        @else bg-secondary @endif">
                                        {{ $item->status }}
                                    </span>
                                    <p class="mb-0 mt-1 small fw-semibold">{{ number_format($item->biaya/1000, 0) }}k</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0" style="color: #1f2937;">Pembayaran Terbaru</h6>
                        <a href="#" class="text-primary text-decoration-none small">Lihat Semua</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($payments->take(5) as $item)
                        <div class="list-group-item px-0 py-2 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="mb-0 fw-medium small">{{ Str::limit($item->nama_pembayaran, 25) }}</p>
                                    <span class="text-muted" style="font-size: 0.7rem;">
                                        {{ $item->penerima ?? '-' }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <span class="badge rounded-pill px-2 py-1 small
                                        @if($item->status == 'aktif') bg-success
                                        @else bg-secondary @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                    <p class="mb-0 mt-1 small fw-semibold">{{ number_format($item->nominal/1000, 0) }}k</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Tables --}}
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-0">
            <ul class="nav nav-pills px-3 pt-3 mb-0" id="reportTabs" role="tablist">
                <li class="nav-item me-2" role="presentation">
                    <button class="nav-link active btn-sm" id="maintenance-tab" data-bs-toggle="tab" data-bs-target="#maintenance-content" type="button" style="border-radius: 6px; font-size: 0.875rem;">
                        <i class="bi bi-tools me-1"></i>Maintenance
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment-content" type="button" style="border-radius: 6px; font-size: 0.875rem;">
                        <i class="bi bi-credit-card me-1"></i>Pembayaran
                    </button>
                </li>
            </ul>
            
            <div class="tab-content p-3">
                {{-- Compact Table Design --}}
                <div class="tab-pane fade show active" id="maintenance-content">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr style="font-size: 0.8rem;">
                                    <th>Tanggal</th>
                                    <th>Perbaikan</th>
                                    <th>Barang</th>
                                    <th class="text-end">Biaya</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.85rem;">
                                @foreach ($maintenances as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_maintenance)->format('d/m') }}</td>
                                    <td class="fw-medium">{{ Str::limit($item->nama_perbaikan, 30) }}</td>
                                    <td class="text-muted">{{ Str::limit($item->barang->nama_barang ?? 'Umum', 20) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($item->biaya/1000, 0) }}k</td>
                                    <td>
                                        <span class="badge rounded-pill px-2 py-1 small
                                            @if($item->status == 'Selesai') bg-success
                                            @elseif($item->status == 'Dijadwalkan') bg-warning text-dark
                                            @else bg-secondary @endif">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="payment-content">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr style="font-size: 0.8rem;">
                                    <th>Mulai</th>
                                    <th>Pembayaran</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.85rem;">
                                @foreach ($payments as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m') }}</td>
                                    <td class="fw-medium">{{ Str::limit($item->nama_pembayaran, 30) }}</td>
                                    <td>
                                        <span class="badge rounded-pill px-2 py-1 small bg-light text-dark">
                                            {{ ucfirst($item->kategori) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format($item->nominal/1000, 0) }}k</td>
                                    <td>
                                        <span class="badge rounded-pill px-2 py-1 small
                                            @if($item->status == 'aktif') bg-success
                                            @else bg-secondary @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
    }

    main {
        margin-top: 56px; /* Adjust based on navbar height */
        clear: both;
    }
    
    /* Widget Cards */
    .widget-card {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .widget-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
    }
    
    .widget-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
        background: rgba(255,255,255,0.3);
    }
    
    .widget-icon.success { background-color: #10b98120; color: #10b981; }
    .widget-icon.warning { background-color: #f59e0b20; color: #f59e0b; }
    .widget-icon.primary { background-color: #3b82f620; color: #3b82f6; }
    .widget-icon.info { background-color: #06b6d420; color: #06b6d4; }
    .widget-icon.purple { background-color: #8b5cf620; color: #8b5cf6; }
    
    /* Trend Badges */
    .trend-badge {
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
    
    .trend-badge.up {
        background-color: #10b98120;
        color: #10b981;
    }
    
    .trend-badge.down {
        background-color: #ef444420;
        color: #ef4444;
    }
    
    /* Timeline - Tambah overflow dan contain absolute */
    .timeline {
        position: relative;
        padding-left: 25px;
        overflow: hidden; /* Tambah ini untuk contain absolute */
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #e5e7eb;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        overflow: hidden; /* Tambah ini */
    }
    
    .timeline-marker {
        position: absolute;
        left: -20px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid white;
        background: #6b7280;
    }
    
    .timeline-marker.success { background: #10b981; }
    .timeline-marker.info { background: #06b6d4; }
    .timeline-marker.warning { background: #f59e0b; }
    .timeline-marker.primary { background: #3b82f6; }
    
    /* Tab Pills */
    .nav-pills .nav-link {
        color: #6b7280;
        background: transparent;
        padding: 8px 16px;
        transition: all 0.2s;
    }
    
    .nav-pills .nav-link:hover {
        background: #f3f4f6;
        color: #1f2937;
    }
    
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    /* Table Styling */
    .table thead th {
        background: #f9fafb;
        color: #6b7280;
        font-weight: 600;
        border-bottom: 2px solid #e5e7eb;
        padding: 12px 8px;
    }
    
    .table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s;
    }
    
    .table tbody tr:hover {
        background: #f9fafb;
    }
    
    /* List Group Items */
    .list-group-item {
        transition: background 0.2s;
    }
    
    .list-group-item:hover {
        background: #f9fafb;
    }
    
    /* Print Styles */
    @media print {
        .btn, .nav-pills { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .widget-card h4 { font-size: 1rem !important; }
        .widget-icon { width: 28px; height: 28px; font-size: 14px; }
    }

    /* Tambah clearfix untuk force clear floats/positions */
    .clearfix::after {
        content: "";
        display: table;
        clear: both;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Jalankan semua JS setelah DOM fully loaded untuk hindari layout shift
    document.addEventListener('DOMContentLoaded', function() {
        // Data real dari PHP
        const biayaGabunganData = {!! json_encode($biayaGabunganPerBulan) !!};
        const maintenanceStatusData = {!! json_encode($maintenancePerStatus) !!};
        const paymentKategoriData = {!! json_encode($paymentPerKategori) !!};

        // Proses labels dan data untuk chart biaya (bulan)
        const labelsBiaya = biayaGabunganData.map(item => {
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            return monthNames[item.bulan - 1];
        });
        const maintenanceBiayaData = biayaGabunganData.map(item => item.total_maintenance);
        const pembayaranBiayaData = biayaGabunganData.map(item => item.total_pembayaran);

        // Chart Biaya Gabungan (Line Chart)
        const ctxBiaya = document.getElementById('chartBiayaGabungan').getContext('2d');
        let chartBiaya = new Chart(ctxBiaya, {
            type: 'line',
            data: {
                labels: labelsBiaya,
                datasets: [{
                    label: 'Maintenance',
                    data: maintenanceBiayaData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Pembayaran',
                    data: pembayaranBiayaData,
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { boxWidth: 12, padding: 15, font: { size: 11 } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
        
        // Chart Maintenance Status (Doughnut)
        const labelsStatus = maintenanceStatusData.map(item => item.status);
        const dataStatus = maintenanceStatusData.map(item => item.jumlah);
        const ctxStatus = document.getElementById('chartMaintenanceStatus').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: labelsStatus,
                datasets: [{
                    data: dataStatus,
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6'], // Sesuaikan warna jika status lebih banyak
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 8, font: { size: 10 } }
                    }
                }
            }
        });
        
        // Chart Payment Kategori (Pie)
        const labelsKategori = paymentKategoriData.map(item => item.kategori);
        const dataKategori = paymentKategoriData.map(item => item.jumlah);
        const ctxKategori = document.getElementById('chartPaymentKategori').getContext('2d');
        new Chart(ctxKategori, {
            type: 'pie',
            data: {
                labels: labelsKategori,
                datasets: [{
                    data: dataKategori,
                    backgroundColor: ['#667eea', '#764ba2', '#06b6d4'], // Sesuaikan warna jika kategori lebih banyak
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 8, font: { size: 10 } }
                    }
                }
            }
        });

        // ===== FUNGSI TOMBOL ===== (tetap sama, placeholder)
        
        // Tombol Export
        document.querySelector('.btn-light.btn-sm[style*="border-radius: 6px; border: 1px solid #e5e7eb;"]:nth-of-type(2)').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show export options
            const exportOptions = `
                <div class="modal fade" id="exportModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-header border-0 pb-0">
                                <h6 class="modal-title">Pilih Format Export</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-success" onclick="exportToExcel()">
                                        <i class="bi bi-file-excel me-2"></i>Excel
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="exportToPDF()">
                                        <i class="bi bi-file-pdf me-2"></i>PDF
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="exportToCSV()">
                                        <i class="bi bi-file-csv me-2"></i>CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('exportModal');
            if (existingModal) existingModal.remove();
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', exportOptions);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('exportModal'));
            modal.show();
        });

        // Export Functions (placeholder; implementasikan di controller jika perlu)
        window.exportToExcel = function() {
            alert('Export ke Excel akan segera diproses...');
            // Implementasi real: redirect ke route export di controller
            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        };

        window.exportToPDF = function() {
            alert('Export ke PDF akan segera diproses...');
            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        };

        window.exportToCSV = function() {
            alert('Export ke CSV akan segera diproses...');
            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        };

        // Tombol Refresh
        document.querySelector('.btn-primary.btn-sm[style*="border-radius: 6px;"]').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add loading state
            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memuat...';
            btn.disabled = true;
            
            // Simulate refresh
            setTimeout(() => {
                location.reload();
            }, 1000);
        });

        // Time Period Buttons (Bulan, Kuartal, Tahun) - Update chart berdasarkan period
        const periodButtons = document.querySelectorAll('.btn-group[role="group"] .btn');
        periodButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                periodButtons.forEach(b => b.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update chart based on period
                const period = this.textContent.trim();
                updateChartByPeriod(period);
            });
        });

        function updateChartByPeriod(period) {
            let labels = [];
            let maintenanceData = [];
            let pembayaranData = [];
            
            if (period === 'Bulan') {
                labels = biayaGabunganData.map(item => {
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                    return monthNames[item.bulan - 1];
                });
                maintenanceData = biayaGabunganData.map(item => item.total_maintenance);
                pembayaranData = biayaGabunganData.map(item => item.total_pembayaran);
            } else if (period === 'Kuartal') {
                // Aggregasi per kuartal (logika sederhana, adjust jika perlu)
                const kuartalGroups = biayaGabunganData.reduce((acc, item) => {
                    const kuartal = Math.ceil(item.bulan / 3);
                    const key = `Q${kuartal}`;
                    if (!acc[key]) acc[key] = { maintenance: 0, pembayaran: 0 };
                    acc[key].maintenance += item.total_maintenance;
                    acc[key].pembayaran += item.total_pembayaran;
                    return acc;
                }, {});
                labels = Object.keys(kuartalGroups);
                maintenanceData = labels.map(key => kuartalGroups[key].maintenance);
                pembayaranData = labels.map(key => kuartalGroups[key].pembayaran);
            } else { // Tahun
                // Aggregasi per tahun
                const tahunGroups = biayaGabunganData.reduce((acc, item) => {
                    if (!acc[item.tahun]) acc[item.tahun] = { maintenance: 0, pembayaran: 0 };
                    acc[item.tahun].maintenance += item.total_maintenance;
                    acc[item.tahun].pembayaran += item.total_pembayaran;
                    return acc;
                }, {});
                labels = Object.keys(tahunGroups).sort();
                maintenanceData = labels.map(year => tahunGroups[year].maintenance);
                pembayaranData = labels.map(year => tahunGroups[year].pembayaran);
            }
            
            chartBiaya.data.labels = labels;
            chartBiaya.data.datasets[0].data = maintenanceData;
            chartBiaya.data.datasets[1].data = pembayaranData;
            chartBiaya.update();
        }

        // Lihat Semua Links
        document.querySelectorAll('a[href="#"].text-primary').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const cardTitle = this.closest('.card-body').querySelector('h6').textContent;
                
                if (cardTitle.includes('Maintenance')) {
                    document.getElementById('maintenance-tab').click();
                } else if (cardTitle.includes('Pembayaran')) {
                    document.getElementById('payment-tab').click();
                }
                
                // Smooth scroll to table
                document.querySelector('.nav-pills').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // Add hover effects for interactive elements
        document.querySelectorAll('.widget-card, .card').forEach(card => {
            card.style.cursor = 'default';
        });

        // Table row click for detail view
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function() {
                // Get row data
                const cells = this.querySelectorAll('td');
                const rowData = Array.from(cells).map(cell => cell.textContent.trim());
                
                // Show detail modal
                showDetailModal(rowData, this.closest('.tab-pane').id);
            });
        });

        function showDetailModal(data, tabId) {
            const isMaintenanceTab = tabId === 'maintenance-content';
            const modalContent = `
                <div class="modal fade" id="detailModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">${isMaintenanceTab ? 'Detail Maintenance' : 'Detail Pembayaran'}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tanggal</small>
                                        <strong>${data[0]}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Status</small>
                                        <strong>${data[4]}</strong>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block">${isMaintenanceTab ? 'Perbaikan' : 'Pembayaran'}</small>
                                        <strong>${data[1]}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">${isMaintenanceTab ? 'Barang' : 'Kategori'}</small>
                                        <strong>${data[2]}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">${isMaintenanceTab ? 'Biaya' : 'Nominal'}</small>
                                        <strong class="text-primary">${data[3]}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary">Edit</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal
            const existingModal = document.getElementById('detailModal');
            if (existingModal) existingModal.remove();
            
            // Add and show modal
            document.body.insertAdjacentHTML('beforeend', modalContent);
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            const toastHTML = `
                <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                    <div class="toast show align-items-center text-white bg-${type} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle me-2"></i>${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', toastHTML);
            
            setTimeout(() => {
                const toastEl = document.querySelector('.toast');
                if (toastEl) toastEl.remove();
            }, 3000);
        }

        // Print button already has onclick="window.print()" in HTML
        
        console.log('✓ Semua fungsi tombol telah diaktifkan');
    });
</script>
@endpush