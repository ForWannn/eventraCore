@extends('layouts.app')

@section('title', 'History Weekly Report')

@section('content')
@php
    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
@endphp

<style>
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    /* .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    } */
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .icon-submitted { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .icon-late { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .icon-ontime { background: rgba(6, 182, 212, 0.1); color: #06b6d4; }
    .icon-average { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

    .stat-info {
        display: flex;
        flex-direction: column;
    }
    .stat-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
    }
    .stat-desc {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* Filters block */
    .filters-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .filters-left {
        display: flex;
        flex-direction: column;
    }
    .filters-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-container {
        position: relative;
        min-width: 260px;
    }
    .search-container input {
        width: 100%;
        padding: 9px 12px 9px 38px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 13px;
        background: var(--bg-color);
        color: var(--text-main);
        outline: none;
        transition: border-color 0.2s;
    }
    .search-container input:focus {
        border-color: #2563eb;
    }
    .search-container i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        width: 16px;
        height: 16px;
    }
    .filter-select {
        padding: 9px 12px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 13px;
        background: var(--bg-color);
        color: var(--text-main);
        outline: none;
        min-width: 130px;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .filter-select:focus {
        border-color: #2563eb;
    }

    /* Card list row */
    .report-card-row {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
    }
    /* .report-card-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    } */
    .report-card-row::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
    }
    

    /* Left section */
    .row-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1.2;
        min-width: 240px;
    }
    .status-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .status-icon-success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-icon-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-icon-info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .status-icon-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .week-info {
        display: flex;
        flex-direction: column;
    }
    .week-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 2px;
    }
    .week-date {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 6px;
    }
    .badge-status {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        width: max-content;
    }
    .badge-status-submitted { background: #dcfce7; color: #166534; }
    .badge-status-draft { background: #eff6ff; color: #1e40af; }
    .badge-status-late { background: #fee2e2; color: #991b1b; }

    /* Middle section */
    .row-middle {
        flex: 2;
        min-width: 300px;
        padding-right: 16px;
    }
    .section-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
    }
    .objective-text {
        font-size: 13px;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* Right section */
    .row-right {
        flex: 1.5;
        min-width: 250px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .progress-bar-container {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }
    .progress-track {
        flex: 1;
        height: 6px;
        background: var(--hover-bg);
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 10px;
    }
    .fill-success { background: #10b981; }
    .fill-warning { background: #f59e0b; }
    .fill-info { background: #3b82f6; }
    .fill-danger { background: #ef4444; }

    .progress-percent {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
        width: 32px;
        text-align: right;
    }
    .meta-details {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 11px;
        color: var(--text-muted);
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .meta-item i {
        width: 14px;
        height: 14px;
    }

    /* Detail button */
    .btn-detail-cta {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #2563eb;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.2s, border-color 0.2s;
        white-space: nowrap;
    }
    .btn-detail-cta:hover {
        background: #dbeafe;
        border-color: #93c5fd;
    }

    @media (max-width: 992px) {
        .dashboard-stats {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }

    @media (max-width: 768px) {
        .dashboard-stats {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
            margin-bottom: 16px !important;
        }
        .stat-card {
            padding: 12px !important;
            border-radius: 10px !important;
            min-height: auto !important;
            margin-bottom: 0 !important;
            gap: 8px !important;
        }
        .stat-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
        }
        .stat-icon svg {
            width: 14px !important;
            height: 14px !important;
        }
        .stat-label {
            font-size: 10px !important;
        }
        .stat-value {
            font-size: 18px !important;
            margin-top: 0 !important;
        }
        .stat-desc {
            font-size: 9.5px !important;
            margin-top: 1px !important;
        }
        .report-card-row {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
            padding: 16px;
        }
        .row-left, .row-middle, .row-right {
            min-width: auto !important;
            width: 100% !important;
        }
        .row-middle {
            border-top: 1px dashed var(--border-color);
            padding-top: 12px;
        }
        .row-right {
            border-top: 1px dashed var(--border-color);
            padding-top: 12px;
        }
        .btn-detail-cta {
            width: 100%;
            justify-content: center;
            margin-left: 0 !important;
            margin-top: 8px;
        }
    }

    @media (max-width: 480px) {
        .meta-details {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
</style>

<!-- Top Statistics Dashboard -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon icon-submitted">
            <i data-feather="file-text"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Submitted</span>
            <span class="stat-value">{{ $totalSubmitted }}</span>
            <span class="stat-desc">Total laporan diserahkan</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-late">
            <i data-feather="alert-triangle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Terlambat Submit</span>
            <span class="stat-value">{{ $totalLate }}</span>
            <span class="stat-desc">Melewati batas waktu</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-ontime">
            <i data-feather="check-circle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Tepat Waktu</span>
            <span class="stat-value">{{ $totalOnTime }}</span>
            <span class="stat-desc">Diserahkan tepat waktu</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-average">
            <i data-feather="trending-up"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Rata-rata Progress</span>
            <span class="stat-value">{{ $averageCompletion }}%</span>
            <span class="stat-desc">Tingkat penyelesaian</span>
        </div>
    </div>
</div>
<div class="card">
    <div class="filters-wrapper">
        <div class="filters-left">
            <h3 style="margin-bottom: 4px;">{{ $isDirector ? 'Riwayat Laporan Mingguan Seluruh Staf' : 'Riwayat Laporan Mingguan Saya' }}</h3>
            <p style="font-size: 13px; color: var(--text-muted);">{{ $isDirector ? 'Manajemen dan peninjauan riwayat laporan mingguan yang diserahkan staf.' : 'Daftar riwayat plan, progress, dan pencapaian laporan kerja mingguan Anda.' }}</p>
        </div>
        <form method="GET" action="{{ route('weekly.history') }}" class="filters-right">
            <div class="search-container">
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ $isDirector ? 'Cari nama, divisi, atau isi' : 'Cari isi Weekly Report' }}" onkeydown="if(event.key === 'Enter') this.form.submit()">
            </div>
            @if(!$isDirector)
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="submitted" {{ $status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            @endif
            <select name="month" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Bulan</option>
                @foreach($months as $mNum => $mName)
                    <option value="{{ $mNum }}" {{ $month == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                @endforeach
            </select>

            <select name="year" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <!-- Weekly Report Stack List -->
    <div style="margin-top: 16px;">
        @forelse($reports as $r)
            @php
                $totalObjs = $r->items->where('type', 'objective')->count();
                $completedObjs = $r->items->where('type', 'objective')->where('is_completed', true)->count();
                $progressPercent = $r->completion_percentage;
                // Determine border and colors based on status and completion
                if ($r->status === 'draft') {
                    $borderClass = 'border-info';
                    $iconClass = 'status-icon-info';
                    $iconName = 'edit-3';
                    $badgeClass = 'badge-status-draft';
                    $badgeText = 'Draft';
                    $fillClass = 'fill-info';
                } elseif ($r->is_late_plan) {
                    $borderClass = 'border-danger';
                    $iconClass = 'status-icon-danger';
                    $iconName = 'alert-circle';
                    $badgeClass = 'badge-status-late';
                    $badgeText = 'Terlambat';
                    $fillClass = 'fill-danger';
                } else {
                    if ($progressPercent >= 90) {
                        $borderClass = 'border-success';
                        $iconClass = 'status-icon-success';
                        $iconName = 'check-circle';
                        $badgeClass = 'badge-status-submitted';
                        $badgeText = 'Submitted';
                        $fillClass = 'fill-success';
                    } elseif ($progressPercent >= 70) {
                        $borderClass = 'border-warning';
                        $iconClass = 'status-icon-warning';
                        $iconName = 'alert-circle';
                        $badgeClass = 'badge-status-submitted';
                        $badgeText = 'Submitted';
                        $fillClass = 'fill-warning';
                    } else {
                        $borderClass = 'border-danger';
                        $iconClass = 'status-icon-danger';
                        $iconName = 'alert-circle';
                        $badgeClass = 'badge-status-late';
                        $badgeText = 'Submitted';
                        $fillClass = 'fill-danger';
                    }
                }
                $weekStartDate = \Carbon\Carbon::parse($r->week_start_date);
                $weekEndDate = $weekStartDate->copy()->addDays(4);
            @endphp
            
            <div class="report-card-row {{ $borderClass }}">
                <!-- Left Section (Week Details) -->
                <div class="row-left">
                    
                    <div class="week-info">
                        <span class="week-title">Week {{ ceil($weekStartDate->day / 7) }}</span>
                        <span class="week-date">{{ $weekStartDate->translatedFormat('d') }} - {{ $weekEndDate->translatedFormat('d M Y') }}</span>
                        <span class="badge-status {{ $badgeClass }}">{{ $badgeText }}</span>
                        
                        @if($isDirector)
                            <div style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
                                <img src="{{ $r->user->photo_url }}" style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--border-color);">
                                <div>
                                    <span style="font-size: 11px; font-weight: 700; color: var(--text-main); display: block; line-height: 1.2;">{{ $r->user->name }}</span>
                                    <span style="font-size: 9px; color: var(--text-muted);">{{ optional($r->user->division)->name ?? '-' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Middle Section (Objective Summary) -->
                <div class="row-middle">
                    <span class="section-label">Objective</span>
                    <div class="objective-text">
                        {{ Str::limit($r->items->where('type', 'objective')->pluck('content')->implode(', '), 110, '...') ?: 'Belum mengisi target utama.' }}
                    </div>
                </div>

                <!-- Right Section (Progress Track & Completion Details) -->
                <div class="row-right">
                    <span class="section-label">Progress</span>
                    <div class="progress-bar-container">
                        <div class="progress-track">
                            <div class="progress-fill {{ $fillClass }}" style="width: {{ $progressPercent }}%;"></div>
                        </div>
                        <span class="progress-percent">{{ $progressPercent }}%</span>
                    </div>
                    <div class="meta-details">
                        <div class="meta-item">
                            <i data-feather="check-square"></i>
                            <span>Tasks {{ $completedObjs }} / {{ $totalObjs }}</span>
                        </div>
                        <div class="meta-item">
                            @if($r->status === 'submitted')
                                <i data-feather="calendar"></i>
                                <span>Submitted {{ $r->final_submitted_at ? $r->final_submitted_at->translatedFormat('d M Y, H:i') : '-' }}</span>
                            @else
                                <i data-feather="clock"></i>
                                <span>Updated {{ $r->updated_at ? $r->updated_at->translatedFormat('d M Y, H:i') : '-' }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div style="margin-left: 12px;">
                    <a href="{{ route('weekly.show_user', [$r->user_id, $r->week_start_date->format('Y-m-d')]) }}" class="btn-detail-cta">
                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                        <span>Lihat Detail</span>
                        <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
                    </a>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 60px 40px; color: var(--text-muted); background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 16px;">
                <i data-feather="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                <h4 style="margin-bottom: 4px; font-weight: 600; color: var(--text-main);">Tidak Ada Laporan</h4>
                <p style="font-size: 13px;">Tidak ada laporan mingguan yang sesuai dengan filter pencarian Anda.</p>
            </div>
        @endforelse
    </div>

    <!-- Custom Pagination Section -->
    @if($reports->total() > 0)
        <div class="custom-pagination-container" style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; flex-wrap: wrap; gap: 16px; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <!-- Records Count -->
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">
                Menampilkan {{ $reports->firstItem() ?? 0 }} - {{ $reports->lastItem() ?? 0 }} dari {{ $reports->total() }} laporan
            </div>

            <!-- Page Links -->
            <div class="pagination-buttons" style="display: flex; align-items: center; gap: 8px;">
                @if ($reports->onFirstPage())
                    <span class="page-btn disabled" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-muted); cursor: not-allowed; opacity: 0.5;">
                        <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                    </span>
                @else
                    <a href="{{ $reports->previousPageUrl() }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; transition: all 0.2s;">
                        <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                    </a>
                @endif

                @foreach ($reports->getUrlRange(max(1, $reports->currentPage() - 2), min($reports->lastPage(), $reports->currentPage() + 2)) as $page => $url)
                    @if ($page == $reports->currentPage())
                        <span class="page-btn active" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #2563eb; color: white; font-weight: 600; font-size: 13px;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if ($reports->hasMorePages())
                    <a href="{{ $reports->nextPageUrl() }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; transition: all 0.2s;">
                        <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                    </a>
                @else
                    <span class="page-btn disabled" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-muted); cursor: not-allowed; opacity: 0.5;">
                        <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
