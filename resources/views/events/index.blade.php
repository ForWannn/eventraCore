@extends('layouts.app')

@section('title', 'Daftar Event')

@section('content')
<style>
    /* Stats Cards Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
            margin-bottom: 16px !important;
        }
        .stat-card {
            padding: 12px !important;
            border-radius: 10px !important;
            min-height: auto !important;
            gap: 8px !important;
        }
        .stat-card .stat-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            font-size: 14px !important;
        }
        .stat-card .stat-icon svg,
        .stat-card .stat-icon i {
            width: 16px !important;
            height: 16px !important;
        }
        .stat-card .stat-label {
            font-size: 10px !important;
        }
        .stat-card .stat-value {
            font-size: 20px !important;
            margin-top: 2px !important;
        }
        .stat-card .stat-sub {
            font-size: 8px !important;
            margin-top: 1px !important;
        }

        /* Card Content */
        .card {
            padding: 16px !important;
            border-radius: 12px !important;
        }
        .card > div:first-of-type {
            margin-bottom: 16px !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .card h2 {
            font-size: 14px !important;
        }
        .card form {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            width: 100% !important;
        }
        .card form select.filter-select {
            width: 100% !important;
            padding: 8px !important;
            font-size: 10px !important;
            border-radius: 8px !important;
        }
        .card form a.btn-create {
            grid-column: span 2 !important;
            width: 100% !important;
            justify-content: center !important;
            padding: 8px 12px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
            margin-top: 4px !important;
        }

        /* Table to Stack Card layout */
        .table-container {
            border: none !important;
            overflow: visible !important;
        }
        table, thead, tbody, th, td, tr {
            display: block !important;
        }
        thead {
            display: none !important;
        }
        .event-row {
            background: var(--bg-color) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            padding: 14px !important;
            margin-bottom: 12px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
        }
        [data-theme="dark"] .event-row {
            background: rgba(30, 41, 59, 0.2) !important;
        }
        .event-row td {
            padding: 0 !important;
            border: none !important;
            width: 100% !important;
        }
        
        .event-row td:nth-child(1) {
            order: 1;
        }
        .event-row td:nth-child(1) span {
            font-size: 13.5px !important;
        }
        .event-row td:nth-child(2) {
            order: 2;
            padding-top: 8px !important;
            border-top: 1px dashed var(--border-color) !important;
            font-size: 12.5px !important;
        }
        .event-row td:nth-child(3) {
            order: 3;
            padding-top: 8px !important;
        }
        .event-row td:nth-child(4) {
            order: 4;
            font-size: 12.5px !important;
        }
        .event-row td:nth-child(5) {
            order: 5;
        }
        .event-row td:nth-child(6) {
            order: 6;
            padding-top: 8px !important;
            border-top: 1px solid var(--border-color) !important;
            display: flex !important;
            justify-content: flex-end !important;
        }
        .event-row td:nth-child(6) a {
            width: 100% !important;
            justify-content: center !important;
            background: var(--hover-bg) !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
            text-align: center !important;
        }

        .pagination-container {
            margin-top: 16px !important;
            padding-top: 12px !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 12px !important;
        }
        .empty-state p{
            font-size: 10px !important;;
        }
        .btn-create span{
            font-size: 10px !important;
        }
    }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 108px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card-content {
        display: flex;
        flex-direction: column;
    }
    .stat-card .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .stat-card .stat-icon.blue    { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .stat-card .stat-icon.emerald { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
    .stat-card .stat-icon.amber   { background: #fff7ed; color: #f59e0b; border: 1px solid #fed7aa; }
    .stat-card .stat-icon.violet  { background: #faf5ff; color: #8b5cf6; border: 1px solid #f3e8ff; }

    [data-theme="dark"] .stat-card .stat-icon.blue    { background: rgba(37,99,235,0.15); color: #60a5fa; border-color: rgba(37,99,235,0.2); }
    [data-theme="dark"] .stat-card .stat-icon.emerald { background: rgba(16,185,129,0.15); color: #34d399; border-color: rgba(16,185,129,0.2); }
    [data-theme="dark"] .stat-card .stat-icon.amber   { background: rgba(245,158,11,0.15); color: #fbbf24; border-color: rgba(245,158,11,0.2); }
    [data-theme="dark"] .stat-card .stat-icon.violet  { background: rgba(139,92,246,0.15); color: #a78bfa; border-color: rgba(139,92,246,0.2); }

    .stat-card .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: capitalize;   
        letter-spacing: 0.5px;
    }
    .stat-card .stat-value {
        font-size: 26px;
        font-weight: 700;
        color: var(--text-main);
        margin-top: 4px;
        line-height: 1.1;
    }
    .stat-card .stat-sub {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
        margin-top: 2px;
    }

    /* Controls Dropdowns & Filters */
    .filter-select {
        padding: 10px ;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-color);
        color: var(--text-main);
        font-size: 13.5px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-select:focus {
        border-color: #2563eb;
        background: var(--card-bg);
    }

    /* Table styling */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { color: var(--text-muted); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 10px;
        background: #2563eb;
        color: #fff;
        text-decoration: none;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .btn-create:hover { opacity: 0.9; }

    .pic-cell { display: flex; align-items: center; gap: 12px; }
    .avatar-xs { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--border-color); flex-shrink: 0; }

    /* Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 11.5px;
        font-weight: 600;
        width: fit-content;
    }
    .badge-status.success {
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #10b981;
    }
    .badge-status.success .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
    }
    .badge-status.warning {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }
    .badge-status.warning .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #f59e0b;
    }
    .badge-status.info {
        background: rgba(59, 130, 246, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }
    .badge-status.info .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #3b82f6;
    }

    /* Pagination controls */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
        flex-wrap: wrap;
        gap: 16px;
    }
    .pagination-info {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .pagination-buttons {
        display: flex;
        gap: 6px;
    }
    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 8px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pagination-btn:hover:not(:disabled) {
        background: var(--hover-bg);
        border-color: var(--text-muted);
    }
    .pagination-btn.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .empty-state { text-align: center; padding: 48px 24px; color: var(--text-muted); }
    .empty-state svg { width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.3; }
</style>
<div>
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Event</h1>
    <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px; font-weight: 500;">Ringkasan performa event.</p>
</div>
<!-- 4 Top KPI Cards -->
<div class="stats-grid">
    <!-- Card 1: Total Event -->
    <div class="stat-card">
        <div class="stat-icon blue"><i data-feather="calendar"></i></div>
        <div class="stat-card-content">
            <span class="stat-label">Total Event</span>
            <span class="stat-value">{{ $events->count() }} <span class="stat-sub">event bulan ini</span></span>
            
        </div>
    </div>
    <!-- Card 2: Ongoing -->
    <div class="stat-card">
        <div class="stat-icon amber"><i data-feather="play-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-label">Ongoing</span>
            <span class="stat-value">{{ $events->filter(fn($e) => $e->status === 'ongoing')->count() }} <span class="stat-sub">sedang berjalan</span></span>
             
        </div>
    </div>

    <!-- Card 3: Upcoming -->
    <div class="stat-card">
        <div class="stat-icon violet"><i data-feather="clock"></i></div>
        <div class="stat-card-content">
            <span class="stat-label">Upcoming</span>
            <span class="stat-value">{{ $events->filter(fn($e) => $e->status === 'upcoming')->count() }} <span class="stat-sub">akan datang</span></span>
             
        </div>
    </div>

    <!-- Card 4: Completed -->
    <div class="stat-card">
        <div class="stat-icon emerald"><i data-feather="check-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-label">Completed</span>
            <span class="stat-value">{{ $events->filter(fn($e) => $e->status === 'completed')->count() }} <span class="stat-sub">telah selesai</span></span>
             
        </div>
    </div>
</div>

<div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 24px;">
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0;">Daftar Event</h2>
            <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px; font-weight: 500; margin-bottom: 0;">
                @if(Auth::user()->can('crud_events') || Auth::user()->hasAnyRole(['Director', 'GM']))
                    <!-- Daftar event berdasarkan bulan yang dipilih. -->
                @else
                    <!-- Event yang Anda ditugaskan pada bulan ini. -->
                @endif
            </p>
        </div>
        
        <form method="GET" action="{{ route('events.index') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <select name="month" onchange="this.form.submit()" class="filter-select">
                @foreach(range(1, 12) as $m)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endforeach
            </select>
            
            <select name="year" onchange="this.form.submit()" class="filter-select">
                @foreach(range(date('Y') - 2, date('Y') + 2) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            @can('crud_events')
            <a href="{{ route('events.create') }}" class="btn-create">
                <span>Buat Event Baru</span>
            </a>
            @endcan
        </form>
    </div>

    @if($events->isEmpty())
        <div class="empty-state">
            <i data-feather="calendar" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.3; color: var(--text-muted);"></i>
            <p style="font-size: 13.5px; font-weight: 500; color: var(--text-muted);">
                @if(Auth::user()->can('crud_events') || Auth::user()->hasAnyRole(['Director', 'GM']))
                    Belum ada event. Mulai dengan membuat event baru.
                @else
                    Anda belum ditugaskan pada event apapun.
                @endif
            </p>
        </div>
    @else
    <div class="table-container">
        <table>
            <thead>
                <tr style="border-bottom: 1.5px solid var(--border-color);">
                    <th style="width: 25%;">Nama Event</th>
                    <th style="width: 25%;">Jadwal</th>
                    <th style="width: 20%;">PIC Event</th>
                    <th style="width: 12%;">Posisi</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 8%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                @php
                    $pic = $event->participants->where('pivot.is_pic', true)->first();
                @endphp
                <tr class="event-row" style="border-bottom: 1px solid var(--border-color);">
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 3px;">
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="font-weight: 600; color: var(--text-main); font-size: 14.5px;">{{ $event->name }}</span>
                            </div>
                            @if($event->description)
                                <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">{{ Str::limit($event->description, 60) }}</div>
                            @endif
                            @if($event->location)
                                <div style="font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; font-weight: 500;">
                                    <i data-feather="map-pin" style="width: 12px; height: 12px;"></i>
                                    <span>{{ $event->location }}</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        @php
                            $dates = $event->event_dates ?? [];
                            $count = count($dates);
                        @endphp
                        @if($count > 0)
                            @php
                                sort($dates);
                                $displayDates = collect($dates)->map(fn($d) => \Carbon\Carbon::parse($d)->translatedFormat('d M'));
                                $yearStr = \Carbon\Carbon::parse($dates[0])->format('Y');
                            @endphp
                            <span style="font-weight: 600; color: var(--text-main);">{{ $displayDates->implode(', ') }} {{ $yearStr }}</span>
                        @else
                            <span style="color: var(--text-muted);">-</span>
                        @endif
                        @if($event->start_time && $event->end_time)
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 3px; font-weight: 500;">Jam: {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</div>
                        @endif
                    </td>
                    <td>
                        @if($pic)
                            <div class="pic-cell">
                                <img src="{{ $pic->photo_url }}" class="avatar-xs" alt="{{ $pic->name }}">
                                <span style="font-weight: 600; color: var(--text-main);">{{ $pic->name }}</span>
                            </div>
                        @else
                            <span style="color: var(--text-muted); font-size: 12.5px; font-weight: 500;">Belum ditentukan</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight: 600; color: var(--text-main);">{{ $event->positions->count() }} posisi</span>
                    </td>
                    <td>
                        @if($event->status === 'completed')
                            <span class="badge-status success">
                                <span class="dot"></span>
                                <span>Completed</span>
                            </span>
                        @elseif($event->status === 'ongoing')
                            <span class="badge-status warning">
                                <span class="dot"></span>
                                <span>Ongoing</span>
                            </span>
                        @else
                            <span class="badge-status info">
                                <span class="dot"></span>
                                <span>Upcoming</span>
                            </span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('events.show', $event->id) }}" style="color: #2563eb; text-decoration: none; font-size: 13.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                            <span>Detail</span>
                            <span>→</span>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination-container">
        <div class="pagination-info" id="paginationInfo">
            Menampilkan 1 - 10 dari {{ $events->count() }} event
        </div>
        <div class="pagination-buttons" id="paginationButtons">
            <!-- Dynamically populated via JS -->
        </div>
    </div>
    @endif
</div>

<script>
    let currentPage = 1;
    const itemsPerPage = 10;
    let eventRows = [];

    function initTable() {
        eventRows = Array.from(document.querySelectorAll('.event-row'));
        renderTable();
    }

    function renderTable() {
        // Hide all rows
        eventRows.forEach(r => r.style.display = 'none');

        const totalItems = eventRows.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIdx = (currentPage - 1) * itemsPerPage;
        const endIdx = Math.min(startIdx + itemsPerPage, totalItems);

        // Show only matching rows on the current page
        for (let i = startIdx; i < endIdx; i++) {
            eventRows[i].style.display = '';
        }

        renderPagination(totalItems, totalPages, startIdx + 1, endIdx);
    }

    function renderPagination(totalItems, totalPages, displayStart, displayEnd) {
        const info = document.getElementById('paginationInfo');
        const buttonsContainer = document.getElementById('paginationButtons');
        if (!info || !buttonsContainer) return;

        if (totalItems === 0) {
            info.textContent = 'Tidak ada data event yang cocok';
            buttonsContainer.innerHTML = '';
            buttonsContainer.style.display = 'none';
            if (typeof feather !== 'undefined') feather.replace();
            return;
        } else {
            info.textContent = `Menampilkan ${displayStart} - ${displayEnd} dari ${totalItems} event`;
            buttonsContainer.style.display = 'flex';
        }

        buttonsContainer.innerHTML = '';

        // Prev Button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn';
        prevBtn.innerHTML = '<i data-feather="chevron-left" style="width: 14px; height: 14px;"></i>';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            currentPage--;
            renderTable();
        };
        buttonsContainer.appendChild(prevBtn);

        // Page Number Buttons
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, currentPage + 1);

        if (currentPage === 1) {
            endPage = Math.min(totalPages, 3);
        } else if (currentPage === totalPages) {
            startPage = Math.max(1, totalPages - 2);
        }

        for (let p = startPage; p <= endPage; p++) {
            const pBtn = document.createElement('button');
            pBtn.className = `pagination-btn ${p === currentPage ? 'active' : ''}`;
            pBtn.textContent = p;
            pBtn.onclick = () => {
                currentPage = p;
                renderTable();
            };
            buttonsContainer.appendChild(pBtn);
        }

        // Next Button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn';
        nextBtn.innerHTML = '<i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => {
            currentPage++;
            renderTable();
        };
        buttonsContainer.appendChild(nextBtn);

        if (typeof feather !== 'undefined') feather.replace();
    }

    document.addEventListener('DOMContentLoaded', initTable);
</script>
@endsection
