@extends('layouts.app')

@section('title', 'Rekapitulasi Laporan Mingguan')

@section('content')
<style>
    /* ── Header Card ── */
    .recap-header-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .recap-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .recap-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--primary-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563EB;
        flex-shrink: 0;
    }
    .recap-header-icon svg {
        width: 24px;
        height: 24px;
    }
    .recap-header-text h2 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
    }
    .recap-header-text p {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--card-bg);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-export:hover {
        background: var(--hover-bg);
    }
    .btn-export svg {
        width: 16px;
        height: 16px;
    }

    /* ── Stats Cards Grid ── */
    .recap-stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        margin-bottom: 12px;
    }
    @media (max-width: 1200px) {
        .recap-stats-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .recap-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .recap-stats-grid { grid-template-columns: 1fr; }
    }

    .recap-stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
    }
    .recap-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
    }
    .recap-stat-card .recap-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .recap-stat-card .recap-stat-icon svg {
        width: 18px;
        height: 18px;
    }
    .recap-stat-card .recap-stat-icon.blue    { background: rgba(37,99,235,0.06);  color: #2563eb; }
    .recap-stat-card .recap-stat-icon.emerald { background: rgba(16,185,129,0.06); color: #10b981; }
    .recap-stat-card .recap-stat-icon.rose    { background: rgba(249,115,22,0.06);  color: #f97316; }
    .recap-stat-card .recap-stat-icon.red     { background: rgba(239,68,68,0.06);   color: #ef4444; }
    .recap-stat-card .recap-stat-icon.violet  { background: rgba(139,92,246,0.06); color: #8b5cf6; }

    .recap-stat-card .recap-stat-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 6px;
    }
    .recap-stat-card .recap-stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.1;
    }
    .recap-stat-card .recap-stat-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 400;
    }

    .stats-info-note {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 24px;
    }
    .stats-info-note svg {
        width: 14px;
        height: 14px;
        color: var(--text-muted);
    }

    /* ── Filters & Toolbar ── */
    .recap-section-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .recap-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .recap-toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        flex: 1;
    }
    .recap-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-control {
        border: 1px solid var(--border-color);
        background: var(--input-bg);
        color: var(--text-main);
        outline: none;
        transition: border-color 0.15s;
    }
    .form-control:focus {
        border-color: #2563EB;
    }

    .recap-search-box {
        position: relative;
        width: 280px;
    }
    @media (max-width: 640px) {
        .recap-search-box { width: 100%; }
    }
    .recap-search-box svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .recap-search-box .form-control {
        padding-left: 38px;
        height: 40px;
        font-size: 13px;
        border-radius: 10px;
    }
    
    .recap-input-date {
        position: relative;
        width: 180px;
    }
    .recap-input-date svg {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .recap-input-date .form-control {
        padding: 0px 10px;
        height: 40px;
        font-size: 13px;
        border-radius: 10px;
    }

    .recap-select {
        height: 40px;
        font-size: 13px;
        border-radius: 10px;
        width: 180px;
    }

    .btn-filter-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0 16px;
        height: 40px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .btn-filter-action.blue {
        background: #2563EB;
        color: #FFFFFF;
    }
    .btn-filter-action.blue:hover {
        background: #1D4ED8;
    }
    .btn-filter-action.reset {
        background: var(--card-bg);
        color: #2563EB;
        border: 1px solid #2563EB;
    }
    .btn-filter-action.reset:hover {
        background: var(--hover-bg);
    }
    .btn-filter-action svg {
        width: 14px;
        height: 14px;
    }

    /* ── Status Legend ── */
    .legend-list {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    .legend-item.terkirim { background: #ECFDF5; color: #059669; border: 1px solid rgba(5,150,105,0.15); }
    .legend-item.terlambat { background: #FFF7ED; color: #D97706; border: 1px solid rgba(217,119,6,0.15); }
    .legend-item.belum { background: #FEF2F2; color: #DC2626; border: 1px solid rgba(220,38,38,0.15); }
    .legend-item.draft { background: #F8FAFC; color: #475569; border: 1px solid var(--border-color); }
    
    [data-theme="dark"] .legend-item.terkirim { background: rgba(16,185,129,0.1); }
    [data-theme="dark"] .legend-item.terlambat { background: rgba(249,115,22,0.1); }
    [data-theme="dark"] .legend-item.belum { background: rgba(239,68,68,0.1); }
    [data-theme="dark"] .legend-item.draft { background: rgba(71,85,105,0.1); }

    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .legend-dot.terkirim { background: #10B981; }
    .legend-dot.terlambat { background: #F97316; }
    .legend-dot.belum { background: #EF4444; }
    .legend-dot.draft { background: #94A3B8; }

    /* ── Table Layout ── */
    .recap-table-wrapper {
        overflow-x: auto;
    }
    .recap-table {
        width: 100%;
        border-collapse: collapse;
    }
    .recap-table th {
        text-align: left;
        padding: 14px 16px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
    }
    .recap-table td {
        padding: 16px;
        font-size: 13.5px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .recap-table tbody tr {
        transition: background 0.15s;
    }
    .recap-table tbody tr:hover {
        background: rgba(37,99,235,0.01);
    }

    /* ── User cell ── */
    .recap-user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .recap-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid var(--border-color);
        flex-shrink: 0;
    }
    .recap-user-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
    }
    .recap-user-division {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    /* ── Status badges ── */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .badge-status.terkirim { background: #ECFDF5; color: #047857; border: 1px solid rgba(4,120,87,0.15); }
    .badge-status.terlambat { background: #FFF7ED; color: #C2410C; border: 1px solid rgba(194,65,12,0.15); }
    .badge-status.belum { background: #FEF2F2; color: #B91C1C; border: 1px solid rgba(185,28,28,0.15); }
    .badge-status.draft { background: #F8FAFC; color: #475569; border: 1px solid var(--border-color); }
    
    [data-theme="dark"] .badge-status.terkirim { background: rgba(16,185,129,0.1); }
    [data-theme="dark"] .badge-status.terlambat { background: rgba(249,115,22,0.1); }
    [data-theme="dark"] .badge-status.belum { background: rgba(239,68,68,0.1); }
    [data-theme="dark"] .badge-status.draft { background: rgba(71,85,105,0.1); }

    /* ── Action Buttons ── */
    .btn-review-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--hover-bg);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-review-action:hover {
        background: rgba(37,99,235,0.06);
        border-color: #2563EB;
        color: #2563EB;
    }
    .btn-review-action svg {
        width: 14px;
        height: 14px;
    }

    /* ── Footer & Pagination ── */
    .recap-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .recap-footer-left {
        font-size: 13px;
        color: var(--text-muted);
    }
    .recap-footer-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .per-page-select {
        padding: 6px 28px 6px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 13px;
        outline: none;
        cursor: pointer;
        appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;16&quot; height=&quot;16&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;%2364748B&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><polyline points=&quot;6 9 12 15 18 9&quot;></polyline></svg>');
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 12px;
    }
    
    .recap-footer .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .recap-footer .page-item .page-link {
        padding: 6px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-main);
        background: var(--card-bg);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .recap-footer .page-item.active .page-link {
        background: #2563EB;
        color: #FFFFFF;
        border-color: #2563EB;
    }
    .recap-footer .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .recap-footer .page-link:hover:not(.disabled) {
        background: var(--hover-bg);
    }

    /* ── Empty State ── */
    .recap-empty-state {
        text-align: center;
        padding: 48px;
        color: var(--text-muted);
    }
    .recap-empty-state svg {
        width: 40px;
        height: 40px;
        opacity: 0.2;
        margin-bottom: 8px;
    }
</style>

{{-- ═══ TOP HEADER & EXPORT ═══ --}}
<div class="recap-header-card">
    <div class="recap-header-left">
        <div class="recap-header-icon">
            <i data-feather="file-text"></i>
        </div>
        <div class="recap-header-text">
            <h2>Monitoring Log Kerja Staf</h2>
            <p>Pantau perencanaan tujuan dan hasil akhir kerja mingguan karyawan.</p>
        </div>
    </div>
    <a href="{{ route('weekly.recap.export', request()->query()) }}" class="btn-export">
        <i data-feather="download"></i>
        Ekspor
    </a>
</div>

{{-- ═══ KPI STAT CARDS ═══ --}}
<div class="recap-stats-grid">
    <div class="recap-stat-card">
        <div class="recap-stat-icon blue"><i data-feather="users"></i></div>
        <div class="recap-stat-label">Total Karyawan</div>
        <div class="recap-stat-value">{{ $totalStaff }}</div>
        <div class="recap-stat-sub">staf aktif</div>
    </div>

    <div class="recap-stat-card">
        <div class="recap-stat-icon emerald"><i data-feather="file-text"></i></div>
        <div class="recap-stat-label">Plan Terkirim</div>
        <div class="recap-stat-value">{{ $planSubmittedCount }}</div>
        <div class="recap-stat-sub">{{ $stats['plan_pct'] }}% dari total</div>
    </div>

    <div class="recap-stat-card">
        <div class="recap-stat-icon rose"><i data-feather="alert-triangle"></i></div>
        <div class="recap-stat-label">Plan Terlambat</div>
        <div class="recap-stat-value">{{ $planLateCount }}</div>
        <div class="recap-stat-sub">{{ $stats['plan_late_pct'] }}% dari rencana</div>
    </div>

    <div class="recap-stat-card">
        <div class="recap-stat-icon violet"><i data-feather="check-square"></i></div>
        <div class="recap-stat-label">Laporan Selesai</div>
        <div class="recap-stat-value">{{ $finalSubmittedCount }}</div>
        <div class="recap-stat-sub">{{ $stats['final_pct'] }}% dari total</div>
    </div>

    <div class="recap-stat-card">
        <div class="recap-stat-icon blue"><i data-feather="trending-up"></i></div>
        <div class="recap-stat-label">Rata-rata Progres</div>
        <div class="recap-stat-value">{{ $averageCompletion }}%</div>
        <div class="recap-stat-sub">tingkat penyelesaian</div>
    </div>
</div>

<!-- <div class="stats-info-note">
    <i data-feather="info"></i>
    <span>Statistik dihitung berdasarkan laporan mingguan aktif pada Senin {{ \Carbon\Carbon::parse($weekStart)->locale('id')->translatedFormat('d M Y') }}</span>
</div> -->

{{-- ═══ MAIN DASHBOARD SECTION ═══ --}}
<div class="recap-section-card">
    <form action="{{ route('weekly.recap') }}" method="GET" id="filterForm">
        <div class="recap-toolbar">
            <div class="recap-toolbar-left">
                {{-- Search Box --}}
                <div class="recap-search-box">
                    <i data-feather="search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama staf..." value="{{ $filters['search'] }}">
                </div>

                {{-- Week selector input --}}
                <div class="recap-input-date">
                    <input type="date" name="week" class="form-control" value="{{ $weekStart }}" required>
                    <!-- <i data-feather="calendar" style="width: 14px; height: 14px;"></i> -->
                </div>

                {{-- Division Filter --}}
                <select name="division_id" class="form-control recap-select">
                    <option value="all">Semua Departemen</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d->id }}" {{ (string)$filters['division_id'] === (string)$d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Status Filter --}}
                <select name="status" class="form-control recap-select">
                    <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="belum_setor_plan" {{ $filters['status'] === 'belum_setor_plan' ? 'selected' : '' }}>Belum Setor Plan</option>
                    <option value="plan_terkirim" {{ $filters['status'] === 'plan_terkirim' ? 'selected' : '' }}>Plan Terkirim</option>
                    <option value="plan_terlambat" {{ $filters['status'] === 'plan_terlambat' ? 'selected' : '' }}>Plan Terlambat</option>
                    <option value="laporan_draft" {{ $filters['status'] === 'laporan_draft' ? 'selected' : '' }}>Laporan Akhir Draft</option>
                    <option value="laporan_selesai" {{ $filters['status'] === 'laporan_selesai' ? 'selected' : '' }}>Laporan Akhir Selesai</option>
                </select>
            </div>

            <div class="recap-toolbar-right">
                <button type="submit" class="btn-filter-action blue">
                    <i data-feather="filter"></i> Filter
                </button>
                <button type="button" class="btn-filter-action reset" onclick="resetFilters()">
                    <i data-feather="rotate-ccw"></i> Reset Filter
                </button>
            </div>
        </div>
    </form>

    {{-- Recap Table --}}
    <div class="recap-table-wrapper">
        <table class="recap-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Status Weekly Plan</th>
                    <th>Status Weekly Report</th>
                    <th>Progress</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $item)
                @php 
                    $u = $item['user'];
                    $userReport = $item['userReport'];
                @endphp
                <tr>
                    <td>
                        <div class="recap-user-cell">
                            <img src="{{ $u->photo_url }}" class="recap-user-avatar" alt="{{ $u->name }}">
                            <div>
                                <div class="recap-user-name">{{ $u->name }}</div>
                                <div class="recap-user-division">NIK: {{ $u->nik ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-weight: 500; color: var(--text-main);">
                        {{ optional($u->division)->name ?? 'Tanpa Divisi' }}
                    </td>
                    <td>
                        @if($item['plan_status'] === 'terkirim')
                            <span class="badge-status terkirim">Terkirim</span>
                        @elseif($item['plan_status'] === 'terlambat')
                            <span class="badge-status terlambat">Terlambat</span>
                        @else
                            <span class="badge-status belum">Belum Kirim</span>
                        @endif
                    </td>
                    <td>
                        @if($item['final_status'] === 'selesai')
                            <span class="badge-status terkirim">Selesai</span>
                        @elseif($item['final_status'] === 'draft')
                            <span class="badge-status draft">Draft</span>
                        @else
                            <span class="badge-status belum">Belum Kirim</span>
                        @endif
                    </td>
                    <td>
                        @if($item['final_status'] === 'selesai')
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="flex: 1; background: var(--hover-bg); height: 6px; width: 80px; border-radius: 999px; overflow: hidden;">
                                    <div style="background: #10b981; height: 100%; width: {{ $item['completion'] }}%;"></div>
                                </div>
                                <span style="font-weight: 700; font-size: 12.5px; color: var(--text-main);">{{ $item['completion'] }}%</span>
                            </div>
                        @else
                            <span style="color: var(--text-muted); font-weight: 500;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($userReport)
                            <a href="{{ route('weekly.show_user', [$u->id, $weekStart]) }}" class="btn-review-action">
                                Review Laporan <i data-feather="chevron-right"></i>
                            </a>
                        @else
                            <span style="color: var(--text-muted); font-weight: 500; font-style: italic;">Belum ada data</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="recap-empty-state">
                        <i data-feather="inbox"></i>
                        <p>Tidak ada data laporan mingguan staf ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination & Footer --}}
    <div class="recap-footer">
        <div class="recap-footer-left">
            Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} staf
        </div>
        <div class="recap-footer-right">
            <select class="per-page-select" onchange="changePerPage(this)">
                <option value="10" {{ $filters['per_page'] == 10 ? 'selected' : '' }}>10 / halaman</option>
                <option value="20" {{ $filters['per_page'] == 20 ? 'selected' : '' }}>20 / halaman</option>
                <option value="50" {{ $filters['per_page'] == 50 ? 'selected' : '' }}>50 / halaman</option>
            </select>
            
            <div class="pagination-wrapper">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<script>
    function resetFilters() {
        window.location.href = "{{ route('weekly.recap') }}";
    }

    function changePerPage(select) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('per_page', select.value);
        urlParams.set('page', 1); // Reset page to 1
        window.location.search = urlParams.toString();
    }

    window.addEventListener('DOMContentLoaded', () => {
        feather.replace();
        
        // Auto-submit form when date picker changes
        const dateInput = document.querySelector('input[name="week"]');
        if (dateInput) {
            dateInput.addEventListener('change', () => {
                document.getElementById('filterForm').submit();
            });
        }
    });
</script>
@endsection