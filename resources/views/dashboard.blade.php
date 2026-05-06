@extends('layouts.app')

@section('title', 'Dasbor Utama')

@section('content')
<style>
    /* ═══════════════════════════════════════════════════════
       DIRECTOR DASHBOARD — Premium Design System
       ═══════════════════════════════════════════════════════ */

    /* ── Welcome Header ─────────────────────────────────── */
    .welcome-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        border-radius: 20px;
        padding: 32px 36px;
        margin-bottom: 28px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -40px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 70%);
        border-radius: 50%;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: 30%;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(14,165,233,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .welcome-banner h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 6px;
        position: relative;
        z-index: 1;
    }
    .welcome-banner p {
        font-size: 14px;
        color: #94a3b8;
        position: relative;
        z-index: 1;
    }
    .welcome-banner .welcome-date {
        font-size: 13px;
        color: #64748b;
        margin-top: 8px;
        position: relative;
        z-index: 1;
    }

    /* ── Stat Cards Grid ────────────────────────────────── */
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

    /* ── Two-Column Layout ──────────────────────────────── */
    .dashboard-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }
    @media (max-width: 960px) {
        .dashboard-cols { grid-template-columns: 1fr; }
    }

    /* ── Card Sections ──────────────────────────────────── */
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

    /* ── Calendar ────────────────────────────────────────── */
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

    /* ── Chart ───────────────────────────────────────────── */
    .chart-container {
        position: relative;
        height: 280px;
    }

    /* ── Event List ──────────────────────────────────────── */
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

    /* ── Status Badges ──────────────────────────────────── */
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

    /* ── PIC Avatar ──────────────────────────────────────── */
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

    /* ── Status Distribution mini-chart ──────────────────── */
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

    /* ── Entrance animations ────────────────────────────── */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-in {
        animation: fadeInUp 0.5s cubic-bezier(.4,0,.2,1) both;
    }
    .delay-1 { animation-delay: 0.05s; }
    .delay-2 { animation-delay: 0.1s; }
    .delay-3 { animation-delay: 0.15s; }
    .delay-4 { animation-delay: 0.2s; }
    .delay-5 { animation-delay: 0.25s; }
    .delay-6 { animation-delay: 0.3s; }
    .delay-7 { animation-delay: 0.35s; }

    /* ── Responsive typography ───────────────────────────── */
    @media (max-width: 640px) {
        .stat-card .stat-value { font-size: 26px; }
        .welcome-banner { padding: 24px; }
        .welcome-banner h3 { font-size: 18px; }
    }
</style>

@role('CEO|GM')
    {{-- ═══ Welcome Banner ═══ --}}
    <div class="welcome-banner animate-in">
        <h3>Selamat Datang, {{ Auth::user()->name }} 👋</h3>
        <p>Berikut ringkasan operasional <strong style="color:#e2e8f0;">eventraCore</strong> — pantau event, tim, dan biaya secara real-time.</p>
        <div class="welcome-date">📅 {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y — H:i') }} WIB</div>
    </div>

    {{-- ═══ Stat Cards ═══ --}}
    <div class="stats-grid">
        <div class="stat-card animate-in delay-1">
            <div class="stat-glow" style="background: #2563eb;"></div>
            <div class="stat-icon blue">📋</div>
            <div class="stat-label">Total Event Aktif</div>
            <div class="stat-value">{{ $activeEventsCount ?? 0 }}</div>
            <div class="stat-sub">dari {{ $totalEvents ?? 0 }} event keseluruhan</div>
        </div>

        <div class="stat-card animate-in delay-2">
            <div class="stat-glow" style="background: #10b981;"></div>
            <div class="stat-icon emerald">🔴</div>
            <div class="stat-label">Sedang Berjalan</div>
            <div class="stat-value">{{ $ongoingEventsCount ?? 0 }}</div>
            <div class="stat-sub">event berstatus on-going</div>
        </div>

        <div class="stat-card animate-in delay-3">
            <div class="stat-glow" style="background: #f59e0b;"></div>
            <div class="stat-icon amber">👥</div>
            <div class="stat-label">Staff Bertugas</div>
            <div class="stat-value">{{ $activeEmployeesCount ?? 0 }}</div>
            <div class="stat-sub">karyawan terlibat event aktif</div>
        </div>

        <div class="stat-card animate-in delay-4">
            <div class="stat-glow" style="background: #8b5cf6;"></div>
            <div class="stat-icon violet">💰</div>
            <div class="stat-label">Est. Fee Bulan Ini</div>
            <div class="stat-value" style="font-size: 26px;">Rp {{ number_format($estimatedFee ?? 0, 0, ',', '.') }}</div>
            <div class="stat-sub">estimasi total pengeluaran fee</div>
        </div>
    </div>

    {{-- ═══ Status Distribution Bar ═══ --}}
    @php
        $total = max(($statusCounts['upcoming'] ?? 0) + ($statusCounts['ongoing'] ?? 0) + ($statusCounts['completed'] ?? 0), 1);
        $pUpcoming  = (($statusCounts['upcoming']  ?? 0) / $total) * 100;
        $pOngoing   = (($statusCounts['ongoing']   ?? 0) / $total) * 100;
        $pCompleted = (($statusCounts['completed'] ?? 0) / $total) * 100;
    @endphp
    <div class="section-card animate-in delay-5" style="margin-bottom: 28px;">
        <div class="section-header">
            <span class="section-title">Distribusi Status Event</span>
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

    {{-- ═══ Calendar & Chart Row ═══ --}}
    <div class="dashboard-cols">
        {{-- Interactive Calendar --}}
        <div class="section-card calendar-wrapper animate-in delay-5">
            <div class="section-header">
                <span class="section-title">📅 Kalender Event</span>
                <span class="section-badge">Interaktif</span>
            </div>
            <div id="eventCalendar"></div>
        </div>

        {{-- Monthly Trend Chart --}}
        <div class="section-card animate-in delay-6">
            <div class="section-header">
                <span class="section-title">📊 Tren Event Bulanan</span>
                <span class="section-badge">12 Bulan Terakhir</span>
            </div>
            <div class="chart-container">
                <canvas id="eventTrendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ═══ Upcoming Events List ═══ --}}
    <div class="section-card animate-in delay-7">
        <div class="section-header">
            <span class="section-title">🗓️ 5 Event Terdekat</span>
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
                            <span>🗓 {{ $event['date_start'] }}{{ $event['date_end'] ? ' — '.$event['date_end'] : '' }}</span>
                            <span>👥 {{ $event['members_count'] }} staff · {{ $event['positions_count'] }} posisi</span>
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
                <div style="font-size: 36px; margin-bottom: 12px; opacity: 0.3;">📋</div>
                <p style="font-size: 14px;">Belum ada event yang dijadwalkan.</p>
            </div>
        @endif
    </div>
@else
    {{-- ═══ Non-Director Welcome ═══ --}}
    <div class="welcome-banner animate-in" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <h3>Selamat Datang, {{ Auth::user()->name }} 👋</h3>
        <p>Dashboard karyawan akan segera tersedia. Silakan cek <strong style="color:#e2e8f0;">Daftar Event</strong> untuk jadwal penugasan Anda.</p>
        <div class="welcome-date">📅 {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y — H:i') }} WIB</div>
    </div>
@endrole

{{-- ═══ External Libraries (CDN) ═══ --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

@role('CEO|GM')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Calendar ────────────────────────────────────────────
    const calendarEl = document.getElementById('eventCalendar');
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                list: 'Daftar'
            },
            events: {!! $calendarEvents ?? '[]' !!},
            eventClick: function (info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            },
            height: 'auto',
            dayMaxEvents: 3,
            eventDisplay: 'block',
        });
        calendar.render();
    }

    // ── Trend Chart ─────────────────────────────────────────
    const trendData = {!! $monthlyTrend ?? '[]' !!};
    const ctx = document.getElementById('eventTrendChart');
    if (ctx && trendData.length) {
        const isDark = document.body.hasAttribute('data-theme');
        const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
        const textColor = isDark ? '#9ca3af' : '#6b7280';

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: trendData.map(d => d.label),
                datasets: [{
                    label: 'Jumlah Event',
                    data: trendData.map(d => d.count),
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx: c, chartArea} = chart;
                        if (!chartArea) return '#2563eb';
                        const gradient = c.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(37,99,235,0.3)');
                        gradient.addColorStop(1, 'rgba(99,102,241,0.8)');
                        return gradient;
                    },
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 36,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1f2937' : '#0f172a',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 13, weight: '600' },
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(ctx) {
                                return ctx.parsed.y + ' Event';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: textColor,
                            font: { family: 'Inter', size: 11, weight: '500' },
                            maxRotation: 45,
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            font: { family: 'Inter', size: 11 },
                            stepSize: 1,
                            precision: 0,
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endrole

@endsection
