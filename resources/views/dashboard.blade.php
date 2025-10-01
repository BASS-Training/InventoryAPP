@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Reset & Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Color Variables */
    :root {
        --primary: #5046e5;
        --primary-light: #6366f1;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --dark: #0f172a;
        --gray: #64748b;
        --light-gray: #f1f5f9;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.1);
    }

    /* Dashboard Container */
    .dashboard-container {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        min-height: 100vh;
        padding: 1.5rem;
    }

    /* Floating Header Card */
    .dashboard-header {
        background: var(--white);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .header-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .header-subtitle {
        font-size: 0.875rem;
        color: var(--gray);
    }

    /* Floating Widget Grid */
    .widget-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    /* Floating Widgets */
    .widget-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .widget-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .widget-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: var(--primary);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .widget-card:hover::before {
        opacity: 1;
    }

    .widget-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }

    .widget-icon.primary { background: rgba(80, 70, 229, 0.1); color: var(--primary); }
    .widget-icon.success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .widget-icon.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .widget-icon.danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
    .widget-icon.info { background: rgba(59, 130, 246, 0.1); color: var(--info); }

    .widget-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .widget-label {
        font-size: 0.75rem;
        color: var(--gray);
        font-weight: 500;
    }

    .widget-trend {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.125rem 0.375rem;
        border-radius: 4px;
    }

    .widget-trend.up {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .widget-trend.down {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Chart Card */
    .chart-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .chart-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--dark);
    }

    .chart-options {
        display: flex;
        gap: 0.5rem;
    }

    .chart-option {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 6px;
        border: 1px solid var(--light-gray);
        background: var(--white);
        color: var(--gray);
        cursor: pointer;
        transition: all 0.2s;
    }

    .chart-option:hover {
        background: var(--light-gray);
    }

    .chart-option.active {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary);
    }

    /* Quick Actions Floating Buttons */
    .quick-actions {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 1000;
    }

    .action-fab {
        width: 56px;
        height: 56px;
        border-radius: 28px;
        background: var(--primary);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-lg);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        font-size: 1.25rem;
    }

    .action-fab:hover {
        transform: scale(1.1);
        background: var(--primary-light);
    }

    .action-menu {
        position: absolute;
        bottom: 70px;
        right: 0;
        display: none;
        flex-direction: column-reverse;
        gap: 0.75rem;
    }

    .action-menu.active {
        display: flex;
    }

    .action-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: fadeInUp 0.3s ease;
    }

    .action-label {
        background: var(--dark);
        color: var(--white);
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        white-space: nowrap;
        box-shadow: var(--shadow-md);
    }

    .action-btn {
        width: 44px;
        height: 44px;
        border-radius: 22px;
        background: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-md);
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        font-size: 1.125rem;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .action-btn.add { color: var(--info); }
    .action-btn.in { color: var(--success); }
    .action-btn.out { color: var(--danger); }
    .action-btn.report { color: var(--warning); }

    /* Activity Feed */
    .activity-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .activity-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .activity-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--dark);
    }

    .view-all-link {
        font-size: 0.75rem;
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .view-all-link:hover {
        color: var(--primary-light);
    }

    .activity-list {
        max-height: 320px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    .activity-item {
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        background: var(--light-gray);
        transition: all 0.2s;
        cursor: pointer;
    }

    .activity-item:hover {
        background: #e2e8f0;
        transform: translateX(4px);
    }

    .activity-item-title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .activity-badge {
        padding: 0.125rem 0.375rem;
        font-size: 0.625rem;
        border-radius: 4px;
        font-weight: 600;
    }

    .activity-badge.new { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .activity-badge.critical { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
    .activity-badge.approved { background: rgba(16, 185, 129, 0.1); color: var(--success); }

    .activity-meta {
        font-size: 0.75rem;
        color: var(--gray);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Info Cards */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .info-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .info-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .info-icon.teal { background: rgba(20, 184, 166, 0.1); color: #14b8a6; }

    .info-title {
        font-size: 0.75rem;
        color: var(--gray);
        font-weight: 500;
    }

    .info-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
    }

    /* Scrollbar Styling */
    .activity-list::-webkit-scrollbar {
        width: 4px;
    }

    .activity-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .activity-list::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 2px;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    /* Loading states */
    .widget-card.loading {
        animation: shimmer 2s infinite;
        background: linear-gradient(90deg, #f0f0f0 25%, #f8f8f8 50%, #f0f0f0 75%);
        background-size: 200% 100%;
    }

    /* Print styles */
    @media print {
        .quick-actions,
        .action-menu,
        .chart-option {
            display: none !important;
        }
        
        .dashboard-container {
            padding: 0;
        }
        
        .widget-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Dark mode styles (optional) */
    body.dark-mode {
        --white: #1e293b;
        --light-gray: #334155;
        --dark: #f1f5f9;
        --gray: #cbd5e0;
    }

    body.dark-mode .dashboard-container {
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .widget-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .quick-actions {
            bottom: 1rem;
            right: 1rem;
        }
        
        .dashboard-container {
            padding: 1rem;
        }
    }

    @media (max-width: 480px) {
        .widget-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .widget-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .widget-icon {
            order: 2;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Accessibility improvements */
    .widget-card:focus,
    .action-btn:focus,
    .action-fab:focus {
        outline: 2px solid var(--primary);
        outline-offset: 2px;
    }

    /* High contrast mode */
    @media (prefers-contrast: high) {
        .widget-card {
            border: 2px solid var(--dark);
        }
        
        .chart-card,
        .activity-card,
        .info-card {
            border: 2px solid var(--dark);
        }
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Compact Header --}}
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="header-title">Dashboard Inventaris</h1>
                <p class="header-subtitle">{{ Auth::user()->name }} • {{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <button class="chart-option active">Hari Ini</button>
                <button class="chart-option">Minggu Ini</button>
                <button class="chart-option">Bulan Ini</button>
            </div>
        </div>
    </div>

    {{-- Floating Widget Grid --}}
    <div class="widget-grid">
        <div class="widget-card">
            <div class="widget-icon primary">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="widget-value">{{ $totalBarang ?? 0 }}</div>
            <div class="widget-label">Total Barang</div>
        </div>

        <div class="widget-card">
            <div class="widget-icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="widget-value">{{ $barangAktif ?? 0 }}</div>
            <div class="widget-label">Barang Aktif</div>
            <span class="widget-trend up">+12%</span>
        </div>

        <div class="widget-card">
            <div class="widget-icon danger">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="widget-value">{{ $barangStokKritis->count() ?? 0 }}</div>
            <div class="widget-label">Stok Kritis</div>
            @if($barangStokKritis->count() > 0)
            <span class="widget-trend down">Perlu Perhatian</span>
            @endif
        </div>

        @can('pengajuan-barang-approve')
        <div class="widget-card">
            <div class="widget-icon warning">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="widget-value">{{ $pengajuanDiajukan ?? 0 }}</div>
            <div class="widget-label">Menunggu</div>
        </div>
        @endcan

        @can('pengajuan-barang-process')
        <div class="widget-card">
            <div class="widget-icon info">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="widget-value">{{ $pengajuanDisetujui ?? 0 }}</div>
            <div class="widget-label">Diproses</div>
        </div>
        @endcan

        <div class="widget-card">
            <div class="widget-icon success">
                <i class="bi bi-arrow-down"></i>
            </div>
            <div class="widget-value">{{ $barangMasuk30Hari ?? 0 }}</div>
            <div class="widget-label">Masuk (30hr)</div>
        </div>

        <div class="widget-card">
            <div class="widget-icon danger">
                <i class="bi bi-arrow-up"></i>
            </div>
            <div class="widget-value">{{ $barangKeluar30Hari ?? 0 }}</div>
            <div class="widget-label">Keluar (30hr)</div>
        </div>

        <div class="widget-card">
            <div class="widget-icon primary">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="widget-value">{{ ($barangMasukBulanIni ?? 0) + ($barangKeluarBulanIni ?? 0) }}</div>
            <div class="widget-label">Perputaran</div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="content-grid">
        {{-- Chart Section --}}
        <div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Pergerakan Stok</h3>
                    <div class="chart-options">
                        <button class="chart-option active">6 Bulan</button>
                        <button class="chart-option">1 Tahun</button>
                    </div>
                </div>
                <div style="position: relative; height: 280px;">
                    <canvas id="pergerakanStokChart"></canvas>
                </div>
            </div>

            {{-- Info Cards Grid --}}
            <div class="info-grid mt-3">
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="info-icon purple">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div>
                            <div class="info-title">Kategori</div>
                            <div class="info-value">{{ $totalKategori ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header">
                        <div class="info-icon teal">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <div class="info-title">Pengguna</div>
                            <div class="info-value">{{ $totalPengguna ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Section --}}
        <div>
            {{-- Status Distribution --}}
            <div class="chart-card mb-3">
                <div class="chart-header">
                    <h3 class="chart-title">Distribusi Status</h3>
                </div>
                <div style="position: relative; height: 200px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            {{-- Activity Feed --}}
            <div class="activity-card">
                <div class="activity-header">
                    <h3 class="activity-title">Aktivitas Terbaru</h3>
                    <a href="{{ route('admin.pengajuan.barang.index') }}" class="view-all-link">Lihat Semua →</a>
                </div>
                <div class="activity-list">
                    @if($barangStokKritis && $barangStokKritis->isNotEmpty())
                        @foreach($barangStokKritis->take(3) as $barang)
                        <div class="activity-item">
                            <div class="activity-item-title">
                                {{ $barang->nama_barang }}
                                <span class="activity-badge critical">Kritis</span>
                            </div>
                            <div class="activity-meta">
                                <span><i class="bi bi-box"></i> Stok: {{ $barang->stok }}</span>
                                <span><i class="bi bi-arrow-down"></i> Min: {{ $barang->stok_minimum }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endif

                    @if($pengajuanMenunggu && $pengajuanMenunggu->isNotEmpty())
                        @foreach($pengajuanMenunggu->take(3) as $request)
                        <div class="activity-item">
                            <div class="activity-item-title">
                                {{ $request->barang->nama_barang ?? 'N/A' }}
                                <span class="activity-badge new">Baru</span>
                            </div>
                            <div class="activity-meta">
                                <span><i class="bi bi-person"></i> {{ $request->pemohon->name ?? 'N/A' }}</span>
                                <span><i class="bi bi-clock"></i> {{ $request->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Action Button --}}
    <div class="quick-actions">
        <div class="action-menu" id="actionMenu">
            @can('view-laporan-stok')
            <div class="action-item">
                <span class="action-label">Laporan</span>
                <button class="action-btn report" onclick="window.location='{{ route('laporan.stok.barang') }}'">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </button>
            </div>
            @endcan
            
            @can('stok-keluar-create')
            <div class="action-item">
                <span class="action-label">Barang Keluar</span>
                <button class="action-btn out" onclick="window.location='{{ route('stok.keluar.create') }}'">
                    <i class="bi bi-box-arrow-up"></i>
                </button>
            </div>
            @endcan
            
            @can('stok-masuk-create')
            <div class="action-item">
                <span class="action-label">Barang Masuk</span>
                <button class="action-btn in" onclick="window.location='{{ route('stok.masuk.create') }}'">
                    <i class="bi bi-box-arrow-down"></i>
                </button>
            </div>
            @endcan
            
            @can('barang-create')
            <div class="action-item">
                <span class="action-label">Tambah Barang</span>
                <button class="action-btn add" onclick="window.location='{{ route('barang.create') }}'">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
            @endcan
        </div>
        <button class="action-fab" id="fabBtn">
            <i class="bi bi-plus-lg" id="fabIcon"></i>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAB Menu Toggle
    const fabBtn = document.getElementById('fabBtn');
    const actionMenu = document.getElementById('actionMenu');
    const fabIcon = document.getElementById('fabIcon');
    
    fabBtn.addEventListener('click', function() {
        actionMenu.classList.toggle('active');
        if (actionMenu.classList.contains('active')) {
            fabIcon.className = 'bi bi-x-lg';
            fabBtn.style.transform = 'rotate(45deg)';
        } else {
            fabIcon.className = 'bi bi-plus-lg';
            fabBtn.style.transform = 'rotate(0deg)';
        }
    });

    // Click outside to close
    document.addEventListener('click', function(event) {
        if (!fabBtn.contains(event.target) && !actionMenu.contains(event.target)) {
            actionMenu.classList.remove('active');
            fabIcon.className = 'bi bi-plus-lg';
            fabBtn.style.transform = 'rotate(0deg)';
        }
    });

    // Chart Configuration
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.legend.display = false;

    // Data dari Controller
    const statusData = @json($statusData ?? []);
    const pergerakanStokData = @json($pergerakanStokData ?? []);

    // Status Chart (Donut)
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx && statusData.length > 0) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.status_barang),
                datasets: [{
                    data: statusData.map(item => item.jumlah),
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6', 
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            usePointStyle: true,
                            font: { size: 10 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { size: 11 },
                        bodyFont: { size: 11 },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return ` ${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }

    // Pergerakan Stok Chart (Line/Area)
    const pergerakanCtx = document.getElementById('pergerakanStokChart');
    if (pergerakanCtx && pergerakanStokData.length > 0) {
        const bulanLabels = pergerakanStokData.map(item => 
            new Date(item.bulan + '-02').toLocaleString('id-ID', { month: 'short' })
        );
        
        new Chart(pergerakanCtx, {
            type: 'line',
            data: {
                labels: bulanLabels,
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: pergerakanStokData.map(item => item.total_masuk),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Barang Keluar',
                        data: pergerakanStokData.map(item => item.total_keluar),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 10 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 11 },
                        bodyFont: { size: 11 },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { 
                            display: false 
                        },
                        ticks: { 
                            font: { size: 10 },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: 'rgba(0,0,0,0.04)',
                            drawBorder: false
                        },
                        ticks: { 
                            precision: 0,
                            font: { size: 10 },
                            color: '#94a3b8',
                            padding: 8
                        }
                    }
                }
            }
        });
    }

    // Dashboard Time Filter
    const timeFilters = document.querySelectorAll('.chart-option');
    timeFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            // Remove active class from all
            timeFilters.forEach(f => f.classList.remove('active'));
            // Add active to clicked
            this.classList.add('active');
            
            // Here you would typically reload data based on the selected filter
            // For now, just a visual change
        });
    });

    // Auto-refresh widgets every 30 seconds (optional)
    let refreshInterval;
    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            // Animate widget values
            document.querySelectorAll('.widget-value').forEach(el => {
                el.style.opacity = '0.5';
                setTimeout(() => {
                    el.style.opacity = '1';
                }, 300);
            });
            // Here you would fetch new data via AJAX
        }, 30000);
    }

    // Start auto-refresh
    startAutoRefresh();

    // Stop refresh when page is hidden
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            startAutoRefresh();
        }
    });

    // Widget click animations
    document.querySelectorAll('.widget-card').forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Activity item click handler
    document.querySelectorAll('.activity-item').forEach(item => {
        item.addEventListener('click', function() {
            // You can add navigation or modal logic here
            console.log('Activity clicked');
        });
    });

    // Smooth scroll for activity list
    const activityList = document.querySelector('.activity-list');
    if (activityList) {
        let isScrolling;
        activityList.addEventListener('scroll', () => {
            clearTimeout(isScrolling);
            activityList.style.pointerEvents = 'none';
            
            isScrolling = setTimeout(() => {
                activityList.style.pointerEvents = 'auto';
            }, 100);
        });
    }

    // Initialize tooltips for abbreviated text
    const initTooltips = () => {
        document.querySelectorAll('[title]').forEach(el => {
            el.style.cursor = 'help';
        });
    };
    initTooltips();

    // Handle responsive layout
    const checkResponsive = () => {
        const width = window.innerWidth;
        const container = document.querySelector('.dashboard-container');
        
        if (width < 768) {
            container.classList.add('mobile-view');
        } else {
            container.classList.remove('mobile-view');
        }
    };

    window.addEventListener('resize', checkResponsive);
    checkResponsive();

    // Number animation on load
    const animateValue = (el, start, end, duration) => {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                el.textContent = end;
                clearInterval(timer);
            } else {
                el.textContent = Math.round(current);
            }
        }, 16);
    };

    // Animate widget values on page load
    window.addEventListener('load', () => {
        document.querySelectorAll('.widget-value').forEach(el => {
            const value = parseInt(el.textContent);
            if (!isNaN(value)) {
                animateValue(el, 0, value, 500);
            }
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Alt + N for new item
        if (e.altKey && e.key === 'n') {
            e.preventDefault();
            @can('barang-create')
            window.location.href = '{{ route('barang.create') }}';
            @endcan
        }
        
        // Alt + R for reports
        if (e.altKey && e.key === 'r') {
            e.preventDefault();
            @can('view-laporan-stok')
            window.location.href = '{{ route('laporan.stok.barang') }}';
            @endcan
        }
        
        // Escape to close FAB menu
        if (e.key === 'Escape') {
            actionMenu.classList.remove('active');
            fabIcon.className = 'bi bi-plus-lg';
            fabBtn.style.transform = 'rotate(0deg)';
        }
    });

    // Dark mode toggle (optional - uncomment if you want to add dark mode)
    /*
    const toggleDarkMode = () => {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    };
    
    // Check for saved dark mode preference
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
    }
    */

    // Print dashboard function
    window.printDashboard = () => {
        window.print();
    };

    // Export data function (placeholder)
    window.exportData = (format) => {
        console.log(`Exporting data as ${format}`);
        // Implement export logic here
    };

    // Notification badge animation
    const addNotificationBadge = (element, count) => {
        const badge = document.createElement('span');
        badge.className = 'notification-badge';
        badge.textContent = count;
        badge.style.cssText = `
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 600;
            animation: pulse 2s infinite;
        `;
        element.style.position = 'relative';
        element.appendChild(badge);
    };

    // Add notification badges where needed
    @if($barangStokKritis->count() > 0)
    const kritisWidget = document.querySelector('.widget-card:nth-child(3)');
    if (kritisWidget && {{ $barangStokKritis->count() }} > 5) {
        addNotificationBadge(kritisWidget, '!');
    }
    @endif

    // Performance monitoring
    const perfData = window.performance.timing;
    const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
    console.log(`Dashboard loaded in ${pageLoadTime}ms`);

    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        clearInterval(refreshInterval);
    });
});
</script>
@endpush