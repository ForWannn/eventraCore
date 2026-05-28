@extends('layouts.app')

@section('title', 'Dasbor Utama')

@section('content')
<style>
   
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
        .stats-grid { grid-template-columns: 1fr; }
    }

    .stat-card {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.25s cubic-bezier(.4,0,.2,1), box-shadow 0.25s cubic-bezier(.4,0,.2,1);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px -8px rgba(0,0,0,0.1);
    }
    .stat-card .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        font-size: 20px;
    }
    .stat-card .stat-icon.blue    { background: rgba(37,99,235,0.1);  color: #2563eb; }
    .stat-card .stat-icon.emerald { background: rgba(16,185,129,0.1); color: #10b981; }
    .stat-card .stat-icon.amber   { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .stat-card .stat-icon.violet  { background: rgba(139,92,246,0.1); color: #8b5cf6; }

    [data-theme="dark"] .stat-card .stat-icon.blue    { background: rgba(37,99,235,0.2); }
    [data-theme="dark"] .stat-card .stat-icon.emerald { background: rgba(16,185,129,0.2); }
    [data-theme="dark"] .stat-card .stat-icon.amber   { background: rgba(245,158,11,0.2); }
    [data-theme="dark"] .stat-card .stat-icon.violet  { background: rgba(139,92,246,0.2); }

    .stat-card .stat-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 8px;
    }
    .stat-card .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -1px;
        line-height: 1;
    }
    .stat-card .stat-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 6px;
        font-weight: 400;
    }
    .stat-card .stat-glow {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.08;
        pointer-events: none;
    }

    .dashboard-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }
    @media (max-width: 960px) {
        .dashboard-cols { grid-template-columns: 1fr; }
    }

    .section-card {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 28px;
    }
    .section-card .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .section-card .section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-main);
    }
    .section-card .section-badge {
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 999px;
        background: var(--hover-bg);
        color: var(--text-muted);
        font-weight: 500;
    }

    .calendar-wrapper {
        margin-bottom: 28px;
    }
    .fc {
        font-family: 'Inter', sans-serif !important;
    }
    .fc .fc-toolbar-title {
        font-size: 16px !important;
        font-weight: 600 !important;
        color: var(--text-main) !important;
    }

    .fc-button-group{
        gap: 10px !important;
    }

    .fc .fc-button {
        background: var(--hover-bg) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        border-radius: 10px !important;
        padding: 6px 14px !important;
        box-shadow: none !important;
        text-transform: capitalize !important;
    }
    .fc .fc-button:hover {
        background: var(--border-color) !important;
    }
    .fc .fc-button-active {
        background: var(--primary) !important;
        color: var(--primary-text) !important;
    }
    .fc .fc-daygrid-day-number {
        font-size: 13px;
        color: var(--text-main);
        font-weight: 500;
        padding: 6px 10px;
    }
    .fc .fc-col-header-cell-cushion {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .fc .fc-daygrid-event {
        border-radius: 6px !important;
        padding: 2px 6px !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        border: none !important;
    }
    .fc td, .fc th {
        border-color: var(--border-color) !important;
    }
    .fc .fc-scrollgrid {
        border-color: var(--border-color) !important;
    }
    .fc .fc-day-today {
        background: rgba(37,99,235,0.06) !important;
    }
    [data-theme="dark"] .fc .fc-day-today {
        background: rgba(37,99,235,0.12) !important;
    }

    .chart-container {
        position: relative;
        height: 280px;
    }

    .event-list-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid var(--border-color);
        transition: background 0.15s;
    }
    .event-list-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .event-list-item:first-child {
        padding-top: 0;
    }
    .event-list-item .event-indicator {
        width: 4px;
        height: 48px;
        border-radius: 4px;
        flex-shrink: 0;
    }
    .event-list-item .event-indicator.ongoing  { background: #2563eb; }
    .event-list-item .event-indicator.upcoming { background: #f59e0b; }
    .event-list-item .event-info {
        flex: 1;
        min-width: 0;
    }
    .event-list-item .event-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .event-list-item .event-meta {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .event-list-item .event-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
        flex-shrink: 0;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .badge-ongoing  { background: #dbeafe; color: #1e40af; }
    .badge-upcoming { background: #fef3c7; color: #92400e; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    [data-theme="dark"] .badge-ongoing  { background: rgba(37,99,235,0.2); color: #93c5fd; }
    [data-theme="dark"] .badge-upcoming { background: rgba(245,158,11,0.2); color: #fcd34d; }
    [data-theme="dark"] .badge-completed { background: rgba(16,185,129,0.2); color: #6ee7b7; }

    .pic-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
    }
    .pic-info {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pic-info span {
        font-size: 12px;
        color: var(--text-muted);
    }

    .status-bar {
        display: flex;
        height: 8px;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 16px;
        margin-bottom: 12px;
    }
    .status-bar div {
        transition: width 0.6s cubic-bezier(.4,0,.2,1);
    }
    .status-bar .s-ongoing  { background: #2563eb; }
    .status-bar .s-upcoming { background: #f59e0b; }
    .status-bar .s-completed { background: #10b981; }
    .status-legend {
        display: flex;
        gap: 20px;
    }
    .status-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--text-muted);
    }
    .status-legend-item .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

        @media (max-width: 640px) {
        .stat-card .stat-value { font-size: 26px; }
        .welcome-banner { padding: 24px; }
        .welcome-banner h3 { font-size: 18px; }
    }

    /* New Dashboard Widget Styles */
    .widget-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .smart-banner {
        padding: 16px 20px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        animation: slideIn 0.3s ease-out;
    }
    .smart-banner.warning { background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; }
    .smart-banner.info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
    [data-theme="dark"] .smart-banner.warning { background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.3); }
    [data-theme="dark"] .smart-banner.info    { background: rgba(37,99,235,0.1); border-color: rgba(37,99,235,0.3); }

    .attendance-panel {
        text-align: center;
        padding: 32px 24px;
        border-radius: 20px;
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        position: relative;
    }
    .attendance-panel.success {
        background: rgba(16,185,129,0.05);
        border-color: #10b981;
    }

    .digital-clock {
        font-size: 40px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        letter-spacing: -1px;
        margin-bottom: 8px;
        color: var(--text-main);
    }

    .task-list-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px;
        border-radius: 12px;
        transition: background 0.2s;
        cursor: pointer;
    }
    .task-list-item:hover { background: var(--hover-bg); }
    .task-checkbox-btn {
        width: 20px;
        height: 20px;
        border: 2px solid var(--text-muted);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: 0.2s;
        background: none;
        padding: 0;
        cursor: pointer;
    }
    .task-checkbox-btn.checked {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Overlay styles */
    #gpsOverlay {
        position: absolute;
        bottom: 14px;
        left: 14px;
        display: inline-flex;
        align-items: flex-start;
        gap: 10px;
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(50px);
        -webkit-backdrop-filter: blur(50px);
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        border-radius: 12px;
        padding: 9px 14px 9px 9px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,0.3);
        max-width: 68%;
        z-index: 10;
    }
</style>

@role('CEO|GM')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-glow" style="background: #2563eb;"></div>
            <div class="stat-icon blue"><i data-feather="check"></i></div>
            <div class="stat-label">Total Event Aktif</div>
            <div class="stat-value">{{ $activeEventsCount ?? 0 }}</div>
            <div class="stat-sub">dari {{ $totalEvents ?? 0 }} event</div>
        </div>

        <div class="stat-card">
            <div class="stat-glow" style="background: #10b981;"></div>
            <div class="stat-icon emerald"><i data-feather="chevrons-right"></i></div>
            <div class="stat-label">Sedang Berjalan</div>
            <div class="stat-value">{{ $ongoingEventsCount ?? 0 }}</div>
            <div class="stat-sub">event on-going</div>
        </div>

        <div class="stat-card">
            <div class="stat-glow" style="background: #f59e0b;"></div>
            <div class="stat-icon amber"><i data-feather="users"></i></div>
            <div class="stat-label">Runner Event</div>
            <div class="stat-value">{{ $activeEmployeesCount ?? 0 }}</div>
            <div class="stat-sub">karyawan bertugas event</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-glow" style="background: #8b5cf6;"></div>
            <div class="stat-icon violet"><i data-feather="user-check"></i></div>
            <div class="stat-label">Kehadiran Hari Ini</div>
            <div class="stat-value">{{ $todayAttendancesCount ?? 0 }}</div>
            <div class="stat-sub">karyawan sudah absen</div>
        </div>
    </div>

    @php
        $total = max(($statusCounts['upcoming'] ?? 0) + ($statusCounts['ongoing'] ?? 0) + ($statusCounts['completed'] ?? 0), 1);
        $pUpcoming  = (($statusCounts['upcoming']  ?? 0) / $total) * 100;
        $pOngoing   = (($statusCounts['ongoing']   ?? 0) / $total) * 100;
        $pCompleted = (($statusCounts['completed'] ?? 0) / $total) * 100;
    @endphp
    <div class="section-card" style="margin-bottom: 28px;">
        <div class="section-header">
            <span class="section-title">Status Event</span>
            <span class="section-badge">{{ $totalEvents ?? 0 }} Total</span>
        </div>
        <div class="status-bar">
            <div class="s-upcoming"  style="width: {{ $pUpcoming }}%;"></div>
            <div class="s-ongoing"   style="width: {{ $pOngoing }}%;"></div>
            <div class="s-completed" style="width: {{ $pCompleted }}%;"></div>
        </div>
        <div class="status-legend">
            <div class="status-legend-item">
                <div class="dot" style="background: #f59e0b;"></div>
                Upcoming ({{ $statusCounts['upcoming'] ?? 0 }})
            </div>
            <div class="status-legend-item">
                <div class="dot" style="background: #2563eb;"></div>
                On-Going ({{ $statusCounts['ongoing'] ?? 0 }})
            </div>
            <div class="status-legend-item">
                <div class="dot" style="background: #10b981;"></div>
                Completed ({{ $statusCounts['completed'] ?? 0 }})
            </div>
        </div>
    </div>

    <div class="dashboard-cols">
        <div class="section-card calendar-wrapper" style="display: flex; flex-direction: column;">
            <div class="section-header" style="flex: none;">
                <span class="section-title">Kalender Event</span>
                <span class="section-badge">Interaktif</span>
            </div>
            <div id="eventCalendar" style="flex: 1; min-height: 0;"></div>
        </div>

        <div class="section-card" style="display: flex; flex-direction: column;">
            <div class="section-header" style="align-items: center;">
                <div>
                    <span class="section-title">Tren Event Bulanan</span>
                    <span class="section-badge" style="margin-left: 6px;">Tahunan</span>
                </div>
                <select id="trendYearSelect" style="padding: 4px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--hover-bg); color: var(--text-main); font-size: 13px; font-weight: 500; cursor: pointer; outline: none;">
                    @php $currentYear = request('year', date('Y')); @endphp
                    @for($y = date('Y') + 1; $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="chart-container" style="flex: none; height: 280px; margin-bottom: 24px;">
                <canvas id="eventTrendChart"></canvas>
            </div>
            
            <div class="top-employees-box" style="flex: 1; border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; background: var(--hover-bg); display: flex; flex-direction: column; justify-content: flex-start;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <h5 style="font-size: 14px; font-weight: 600; color: var(--text-main); margin: 0;">Top Karyawan</h5>
                </div>
                
                @if(isset($topEmployees) && count($topEmployees) > 0)
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($topEmployees as $index => $emp)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: 10px; background: var(--sidebar-bg); border: 1px solid var(--border-color); box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                <div style="width: 24px; height: 24px; border-radius: 50%; background: {{ $index == 0 ? '#fef08a' : ($index == 1 ? '#e5e7eb' : '#ffedd5') }}; color: {{ $index == 0 ? '#854d0e' : ($index == 1 ? '#374151' : '#9a3412') }}; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0;">
                                    {{ $index + 1 }}
                                </div>
                                @if(isset($emp['user']->photo_url) && $emp['user']->photo_url)
                                    <img src="{{ $emp['user']->photo_url }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);" alt="Avatar">
                                @else
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--hover-bg); display: flex; align-items: center; justify-content: center; font-size: 14px; border: 1px solid var(--border-color);">👤</div>
                                @endif
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 13px; font-weight: 600; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $emp['user']->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        {{ $emp['user']->roles->first() ? \Illuminate\Support\Str::ucfirst($emp['user']->roles->first()->name) : 'Staff' }}
                                    </div>
                                </div>
                                <div style="font-size: 12px; font-weight: 600; color: var(--text-main); background: var(--hover-bg); padding: 4px 10px; border-radius: 999px;">
                                    {{ $emp['count'] }} Event
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: var(--text-muted);">
                        <div style="font-size: 24px; margin-bottom: 8px; opacity: 0.3;">👥</div>
                        <p style="font-size: 12px; margin: 0;">Belum ada data partisipasi karyawan aktif.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <span class="section-title">Event Terdekat</span>
            <a href="{{ route('events.index') }}" style="font-size: 13px; color: var(--text-muted); text-decoration: none; font-weight: 500;">
                Lihat Semua →
            </a>
        </div>

        @if(isset($upcomingEventsList) && $upcomingEventsList->count() > 0)
            @foreach($upcomingEventsList as $event)
                <div class="event-list-item">
                    <div class="event-indicator {{ $event['status'] }}"></div>
                    <div class="event-info">
                        <div class="event-name">{{ $event['name'] }}</div>
                        <div class="event-meta">
                            <span>{{ $event['date_start'] }}{{ $event['date_end'] ? ' — '.$event['date_end'] : '' }}</span>
                            <span>{{ $event['members_count'] }} staff · {{ $event['positions_count'] }} posisi</span>
                        </div>
                    </div>
                    <div class="event-right">
                        <span class="badge badge-{{ $event['status'] }}">{{ strtoupper($event['status']) }}</span>
                        <div class="pic-info">
                            @if($event['pic_photo'])
                                <img src="{{ $event['pic_photo'] }}" class="pic-avatar" alt="PIC">
                            @endif
                            <span>{{ $event['pic_name'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                <div style="font-size: 36px; margin-bottom: 12px; opacity: 0.3;"></div>
                <p style="font-size: 14px;">Belum ada event yang dijadwalkan.</p>
            </div>
        @endif
    </div>
@else
    <div class="widget-container">
        {{-- Widget 1: Rapid Attendance Panel --}}
        <div class="attendance-panel {{ $todayAttendance ? 'success' : '' }}">
            @if($todayAttendance)
                <div style="font-size: 48px; margin-bottom: 16px;">✅</div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px; color: #065f46;">
                    Sudah Absen Masuk
                </h3>
                <p style="font-size: 14px; color: #059669; margin-bottom: 24px;">
                    Anda telah absen masuk pada pukul {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} WIB.
                </p>
                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: rgba(16,185,129,0.1); border-radius: 99px; color: #065f46; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                    @if($todayAttendance->attendance_type === 'kantor')
                        <i data-feather="home" style="width: 14px; height: 14px;"></i> Gedung
                    @else
                        <i data-feather="map-pin" style="width: 14px; height: 14px;"></i> Map Pin
                    @endif
                </div>
            @else
                <div class="digital-clock" id="digitalClock">00:00:00</div>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">
                    Silakan lakukan absensi luar kantor di bawah ini.
                </p>
                <button class="btn" style="width: 100%; justify-content: center; height: 50px; border-radius: 14px; font-weight: 700;" onclick="openAttendanceModal()">
                    📍 Absen Luar Kantor (Sekarang)
                </button>
            @endif
        </div>

        {{-- Widget 2: Smart Banner --}}
        @if($showBanner)
            @if($bannerType === 'plan')
                <div class="smart-banner warning">
                    <i data-feather="alert-triangle"></i>
                    <div style="font-size: 13px; font-weight: 500;">
                        Anda belum mengisi Target Mingguan. Segera buat Rencana Kerja Anda sebelum Selasa sore!
                    </div>
                </div>
            @elseif($bannerType === 'final')
                <div class="smart-banner info">
                    <i data-feather="bell"></i>
                    <div style="font-size: 13px; font-weight: 500;">
                        Waktunya evaluasi! Jangan lupa submit laporan final Weekly Report Anda hari ini.
                    </div>
                </div>
            @endif
        @endif

        {{-- Widget 3: My Active Tasks --}}
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">To Do List Saya</span>
                <span class="section-badge">{{ $personalTasks->count() }} Pending</span>
            </div>
            
            <div style="display: flex; flex-direction: column;" id="dashboardTasksList">
                @forelse($personalTasks as $task)
                    <div class="task-list-item" id="task-card-{{ $task->id }}">
                        <button class="task-checkbox-btn" onclick="toggleTask({{ $task->id }}, this)">
                            <i data-feather="check" style="width: 12px; height: 12px; visibility: hidden;"></i>
                        </button>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 14px; font-weight: 500; color: var(--text-main); margin-bottom: 2px;">
                                {{ $task->task_name }}
                                <span style="font-size: 11px; color: var(--text-muted); font-weight: 400; margin-left: 6px;">
                                    [Event: {{ $task->event->name }}]
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">
                        Tidak ada tugas tertunda.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Widget 4: Upcoming Events --}}
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">Event Saya Mendatang</span>
                <a href="{{ route('events.index') }}" style="font-size: 12px; color: var(--text-muted); text-decoration: none;">Lihat Semua</a>
            </div>

            <div style="display: flex; flex-direction: column;">
                @forelse($upcomingList as $event)
                    <a href="{{ route('events.show', $event['id']) }}" class="event-list-item" style="text-decoration: none; padding: 12px 0;">
                        <div class="event-indicator {{ $event['status'] }}" style="height: 40px;"></div>
                        <div class="event-info">
                            <div class="event-name" style="font-size: 14px;">{{ $event['name'] }}</div>
                            <div class="event-meta" style="font-size: 11px;">
                                <span>{{ $event['date_start'] }}</span>
                                <span style="text-transform: capitalize;">{{ $event['status'] }}</span>
                            </div>
                        </div>
                        <i data-feather="chevron-right" style="width: 16px; color: var(--text-muted);"></i>
                    </a>
                @empty
                    <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">
                        Belum ada penugasan baru.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="attendanceModal" style="display: none; position: fixed; inset: 0; z-index: 100; background: rgba(0,0,0,0.8); backdrop-filter: blur(4px); padding: 20px; flex-direction: column; align-items: center; justify-content: center;">
        <div style="background: var(--sidebar-bg); width: 100%; max-width: 500px; border-radius: 20px; overflow: hidden; position: relative;">
            <button onclick="closeAttendanceModal()" style="position: absolute; top: 12px; right: 12px; z-index: 110; background: rgba(0,0,0,0.5); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">✕</button>
            
            <div style="position: relative; aspect-ratio: 4/3; background: #000;">
                <video id="webcam" autoplay playsinline style="width:100%; height:100%; object-fit:cover;"></video>
                <canvas id="photoCanvas" style="display:none;"></canvas>

                <div id="gpsOverlay">
                    <div style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; border: 1.5px solid white;">
                        <div id="miniMap" style="width: 100%; height: 100%;"></div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <div id="gpsAddress" style="font-size: 10px; font-weight: 700; color: #fff; line-height: 1.2;">🛰️ Mendeteksi lokasi...</div>
                        <div id="gpsCoords" style="font-size: 9px; font-family: monospace; color: rgba(255,255,255,0.8);">—</div>
                        <div id="gpsClock" style="font-size: 9px; color: rgba(255,255,255,0.7);">00:00 WIB</div>
                    </div>
                </div>
            </div>

            <div style="padding: 20px;">
                <button id="btnSubmitAbsen" class="btn" style="width:100%; justify-content:center; height: 48px; border-radius: 12px; font-weight: 600;">
                    <i data-feather="camera" style="width:16px; margin-right:8px;"></i> Ambil Foto & Absen
                </button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endrole

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

@role('CEO|GM')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tren Event Chart
        const ctxTrend = document.getElementById('eventTrendChart');
        if (ctxTrend) {
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: {!! json_encode($months ?? []) !!},
                    datasets: [{
                        label: 'Total Event',
                        data: {!! json_encode($trends ?? []) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Calendar
        const calEl = document.getElementById('eventCalendar');
        if (calEl) {
            new FullCalendar.Calendar(calEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
                events: {!! $calendarEvents ?? '[]' !!},
                height: 'auto'
            }).render();
        }
    });
</script>
@else
<script>
function openAttendanceModal() {
    document.getElementById('attendanceModal').style.display = 'flex';
    initCameraAndGps();
}

function closeAttendanceModal() {
    document.getElementById('attendanceModal').style.display = 'none';
    if (window.localStream) {
        window.localStream.getTracks().forEach(track => track.stop());
    }
}

function toggleTask(taskId, btn) {
    const icon = btn.querySelector('svg');
    btn.disabled = true;
    
    fetch(`/event-tasks/${taskId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            btn.classList.add('checked');
            if (icon) icon.style.visibility = 'visible';
            
            // Soft remove animation
            setTimeout(() => {
                const card = document.getElementById(`task-card-${taskId}`);
                if (card) {
                    card.style.opacity = '0.5';
                    card.style.textDecoration = 'line-through';
                }
            }, 300);
        }
    })
    .catch(() => {
        alert('Gagal memperbarui tugas.');
        btn.disabled = false;
    });
}

function initCameraAndGps() {
    const video      = document.getElementById('webcam');
    const btnSubmit  = document.getElementById('btnSubmitAbsen');
    const gpsAddress = document.getElementById('gpsAddress');
    const gpsCoords  = document.getElementById('gpsCoords');
    const gpsClock   = document.getElementById('gpsClock');
    let userCoords   = null;
    let miniMapInst  = null;
    let addressCache = '';
    let coordsCache  = '';

    // ── Live clock ──
    setInterval(() => {
        const now = new Date();
        if(gpsClock) gpsClock.textContent = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) + ' WIB';
    }, 1000);

    // ── Camera ──
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
        .then(stream => { 
            video.srcObject = stream; 
            window.localStream = stream;
        })
        .catch(err => {
            console.error('Camera:', err);
            if(gpsAddress) gpsAddress.textContent = '❌ Kamera tidak dapat diakses.';
        });

    // ── Geolocation ──
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            userCoords = { lat, lng };
            coordsCache = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            if(gpsCoords) gpsCoords.textContent = coordsCache;

            // Reverse Geocode
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(r => r.json())
                .then(d => {
                    addressCache = d.display_name.split(',').slice(0,2).join(',');
                    if(gpsAddress) gpsAddress.textContent = '📍 ' + addressCache;
                });

            // Mini Map
            const mapEl = document.getElementById('miniMap');
            if(mapEl && !miniMapInst) {
                miniMapInst = L.map(mapEl, {zoomControl:false, attributionControl:false}).setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMapInst);
                L.marker([lat, lng]).addTo(miniMapInst);
            }
        });
    }

    btnSubmit.onclick = function() {
        if(!userCoords) return alert('Tunggu lokasi...');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Mengirim...';

        const canvas = document.getElementById('photoCanvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        // Simple watermark
        ctx.fillStyle = 'rgba(0,0,0,0.5)';
        ctx.fillRect(0, canvas.height - 60, canvas.width, 60);
        ctx.fillStyle = 'white';
        ctx.font = '14px sans-serif';
        ctx.fillText(addressCache, 20, canvas.height - 35);
        ctx.fillText(new Date().toLocaleString(), 20, canvas.height - 15);

        const photo = canvas.toDataURL('image/png');
        
        fetch('{{ route("attendance.storeLuar") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ photo, latitude: userCoords.lat, longitude: userCoords.lng })
        })
        .then(r => r.json())
        .then(d => { alert(d.message); location.reload(); })
        .catch(() => { alert('Gagal absen.'); btnSubmit.disabled = false; btnSubmit.innerHTML = 'Ambil Foto & Absen'; });
    };
}

document.addEventListener('DOMContentLoaded', function() {
    // Clock
    setInterval(() => {
        const el = document.getElementById('digitalClock');
        if(el) el.textContent = new Date().toLocaleTimeString('id-ID', {hour12:false});
    }, 1000);

    // Calendar
    const calEl = document.getElementById('eventCalendar');
    if (calEl) {
        new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            events: {!! $calendarEvents ?? '[]' !!},
            height: 'auto'
        }).render();
    }
    const styleEl = document.createElement('style');
        styleEl.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
        document.head.appendChild(styleEl);
});
</script>
@endrole


@endsection