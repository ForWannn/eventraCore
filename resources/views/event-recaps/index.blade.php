@extends('layouts.app')

@section('title', $isHistory ? 'Riwayat Rekapitulasi Event' : 'Rekapitulasi Event')

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
        border: 1px solid #dbeafe;
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

    /* ── Stats Cards Grid ── */
    .recap-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 992px) {
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
    .recap-stat-card .recap-stat-icon.amber   { background: rgba(245,158,11,0.06);  color: #f59e0b; }
    .recap-stat-card .recap-stat-icon.purple  { background: rgba(139,92,246,0.06); color: #8b5cf6; }

    .recap-stat-card .recap-stat-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 6px;
    }
    .recap-stat-card .recap-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.1;
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

    .recap-select {
        height: 40px;
        font-size: 13px;
        border-radius: 10px;
        width: 150px;
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

    /* ── User & Event cell ── */
    .recap-user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .recap-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid var(--border-color);
        flex-shrink: 0;
    }
    .recap-user-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-main);
    }
    .recap-user-role {
        font-size: 11px;
        color: var(--text-muted);
    }

    .recap-event-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .recap-event-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--primary-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563EB;
        flex-shrink: 0;
    }
    .recap-event-icon svg {
        width: 20px;
        height: 20px;
    }
    .recap-event-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
    }
    .recap-event-date {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
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
    .badge-status.draft { background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; }
    .badge-status.dalam_rekap { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
    .badge-status.menunggu_finance { background: #FEF3C7; color: #D97706; border: 1px solid #FDE68A; }
    .badge-status.direvisi { background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; }
    .badge-status.selesai { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }

    [data-theme="dark"] .badge-status.draft { background: rgba(71,85,105,0.1); }
    [data-theme="dark"] .badge-status.dalam_rekap { background: rgba(37,99,235,0.1); }
    [data-theme="dark"] .badge-status.menunggu_finance { background: rgba(245,158,11,0.1); }
    [data-theme="dark"] .badge-status.direvisi { background: rgba(220,38,38,0.1); }
    [data-theme="dark"] .badge-status.selesai { background: rgba(16,185,129,0.1); }

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

{{-- ═══ TOP HEADER ═══ --}}
<div class="recap-header-card">
    <div class="recap-header-left">
        <div class="recap-header-icon">
            <i data-feather="bar-chart-2"></i>
        </div>
        <div class="recap-header-text">
            <h2>{{ $isHistory ? 'Riwayat Rekapitulasi Keuangan Event' : 'Rekapitulasi Keuangan Event' }}</h2>
            <p>Kelola dan pantau pengumpulan bukti nota belanja serta kalkulasi keuangan event.</p>
        </div>
    </div>
</div>

{{-- ═══ STAT CARDS ═══ --}}
@php
    $totalEventsCount = $events->total();
    $activeCount = 0;
    $totalBudgetSum = 0;
    $totalSpendSum = 0;
    
    foreach($events as $ev) {
        if ($ev->recap) {
            $totalBudgetSum += (float) $ev->recap->initial_nominal;
            $totalSpendSum += (float) $ev->recap->total_spent;
            if ($ev->recap->status !== 'selesai') {
                $activeCount++;
            }
        }
    }
@endphp
<div class="recap-stats-grid">
    <div class="recap-stat-card">
        <div class="recap-stat-icon blue"><i data-feather="calendar"></i></div>
        <div class="recap-stat-label">Total Event Terdata</div>
        <div class="recap-stat-value">{{ $totalEventsCount }}</div>
    </div>

    <div class="recap-stat-card">
        <div class="recap-stat-icon amber"><i data-feather="loader"></i></div>
        <div class="recap-stat-label">Rekap Sedang Berjalan</div>
        <div class="recap-stat-value">{{ $isHistory ? 0 : $activeCount }}</div>
    </div>

    <div class="recap-stat-card">
        <div class="recap-stat-icon emerald"><i data-feather="dollar-sign"></i></div>
        <div class="recap-stat-label">Total Alokasi Anggaran</div>
        <div class="recap-stat-value">Rp {{ number_format($totalBudgetSum, 0, ',', '.') }}</div>
    </div>

    <div class="recap-stat-card">
        <div class="recap-stat-icon purple"><i data-feather="shopping-bag"></i></div>
        <div class="recap-stat-label">Total Belanja Nota</div>
        <div class="recap-stat-value">Rp {{ number_format($totalSpendSum, 0, ',', '.') }}</div>
    </div>
</div>

{{-- ═══ FILTER & LIST ═══ --}}
<div class="recap-section-card">
    <form action="{{ url()->current() }}" method="GET" id="filterForm">
        <div class="recap-toolbar">
            <div class="recap-toolbar-left">
                {{-- Search Box --}}
                <div class="recap-search-box">
                    <i data-feather="search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama event..." value="{{ $search }}">
                </div>

                {{-- Month --}}
                <select name="month" class="form-control recap-select" onchange="document.getElementById('filterForm').submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>

                {{-- Year --}}
                <select name="year" class="form-control recap-select" onchange="document.getElementById('filterForm').submit()">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="recap-toolbar-right">
                <button type="submit" class="btn-filter-action blue">
                    <i data-feather="filter"></i> Filter
                </button>
                <button type="button" class="btn-filter-action reset" onclick="resetFilters()">
                    <i data-feather="rotate-ccw"></i> Reset
                </button>
            </div>
        </div>
    </form>

    {{-- Recap Table --}}
    <div class="recap-table-wrapper">
        <table class="recap-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Tanggal Pelaksanaan</th>
                    <th>PIC Event</th>
                    <th>Anggaran Awal</th>
                    <th>Total Pengeluaran</th>
                    <th>Status Rekap</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                @php
                    $recap = $event->recap;
                    $status = $recap ? $recap->status : 'draft';
                    $pic = $event->participants->where('pivot.is_pic', true)->first();
                    
                    $statusLabel = 'Draft';
                    $statusClass = 'draft';
                    if ($status === 'dalam_rekap') {
                        $statusLabel = 'Dalam Rekap';
                        $statusClass = 'dalam_rekap';
                    } elseif ($status === 'menunggu_finance') {
                        $statusLabel = 'Menunggu Finance';
                        $statusClass = 'menunggu_finance';
                    } elseif ($status === 'direvisi') {
                        $statusLabel = 'Direvisi';
                        $statusClass = 'direvisi';
                    } elseif ($status === 'selesai') {
                        $statusLabel = 'Selesai';
                        $statusClass = 'selesai';
                    }
                @endphp
                <tr>
                    <td>
                        <div class="recap-event-cell">
                            
                            <div>
                                <div class="recap-event-name">{{ $event->name }}</div>
                                <div class="recap-event-date">{{ $event->location ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color: var(--text-muted); font-weight: 500;">
                        @php
                            $dates = $event->event_dates ?? [];
                            sort($dates);
                        @endphp
                        @if(!empty($dates))
                            {{ \Carbon\Carbon::parse($dates[0])->translatedFormat('d M Y') }}
                            @if(count($dates) > 1)
                                - {{ \Carbon\Carbon::parse(end($dates))->translatedFormat('d M Y') }}
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($pic)
                        <div class="recap-user-cell">
                            <img src="{{ $pic->photo_url }}" class="recap-user-avatar" alt="{{ $pic->name }}">
                            <div>
                                <div class="recap-user-name">{{ $pic->name }}</div>
                                <div class="recap-user-role">{{ optional($pic->division)->name ?? '-' }}</div>
                            </div>
                        </div>
                        @else
                        <span style="color: var(--text-muted); font-style: italic;">Belum ada PIC</span>
                        @endif
                    </td>
                    <td style="font-weight: 700; color: var(--text-main);">
                        Rp {{ number_format($recap ? $recap->initial_nominal : 0, 0, ',', '.') }}
                    </td>
                    <td style="font-weight: 700; color: #2563EB;">
                        Rp {{ number_format($recap ? $recap->total_spent : 0, 0, ',', '.') }}
                    </td>
                    <td>
                        <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        <a href="{{ route('event-recaps.show', $event->id) }}" class="btn-review-action">
                            Kelola Rekap <i data-feather="chevron-right"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="recap-empty-state">
                        <i data-feather="inbox"></i>
                        <p>Tidak ada data rekapitulasi event ditemukan untuk periode ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="recap-footer">
        <div class="recap-footer-left">
            Menampilkan {{ $events->firstItem() ?? 0 }} - {{ $events->lastItem() ?? 0 }} dari {{ $events->total() }} event
        </div>
        <div class="recap-footer-right">
            <div class="pagination-wrapper">
                {{ $events->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<script>
    function resetFilters() {
        window.location.href = "{{ url()->current() }}";
    }

    window.addEventListener('DOMContentLoaded', () => {
        feather.replace();
    });
</script>
@endsection
