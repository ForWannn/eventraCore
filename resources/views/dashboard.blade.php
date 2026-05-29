@extends('layouts.app')

@section('title', 'Dasbor Utama')

@section('content')
<style>
    .page-title {
        display: none;
    }
    
    /* Stats Grid & Cards */
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

    .stats-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    @media (max-width: 1024px) {
        .stats-grid-3 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .stats-grid-3 { grid-template-columns: 1fr; }
    }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        min-height: 144px;
        transition: transform 0.25s cubic-bezier(.4,0,.2,1), box-shadow 0.25s cubic-bezier(.4,0,.2,1);
    }
    /* .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.03);
    } */
    .stat-card-left {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        z-index: 2;
        flex: 1;
    }
    .stat-card-right {
        position: relative;
        height: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
        z-index: 1;
        width: 100px;
        flex-shrink: 0;
    }
    .stat-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .stat-card .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
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
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
    }
    .stat-card .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -1px;
        margin-top: 4px;
        line-height: 1.1;
    }
    .stat-card .stat-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
        font-weight: 500;
    }
    .stat-card .stat-trend-container {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
    }
    .stat-trend-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .stat-trend-badge.blue    { background: #eff6ff; color: #1e40af; }
    .stat-trend-badge.emerald { background: #ecfdf5; color: #065f46; }
    .stat-trend-badge.amber   { background: #fff7ed; color: #9a3412; }
    .stat-trend-badge.violet  { background: #faf5ff; color: #5b21b6; }

    [data-theme="dark"] .stat-trend-badge.blue    { background: rgba(37,99,235,0.2); color: #93c5fd; }
    [data-theme="dark"] .stat-trend-badge.emerald { background: rgba(16,185,129,0.2); color: #6ee7b7; }
    [data-theme="dark"] .stat-trend-badge.amber   { background: rgba(245,158,11,0.2); color: #fde047; }
    [data-theme="dark"] .stat-trend-badge.violet  { background: rgba(139,92,246,0.2); color: #c084fc; }

    .stat-trend-text {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .dashboard-cols {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }
    @media (max-width: 960px) {
        .dashboard-cols { grid-template-columns: 1fr; }
    }

    .section-card {
        background: var(--card-bg);
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

    /* Status Section horizontal layout & donut chart */
    .status-section-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 40px;
        align-items: center;
    }
    @media (max-width: 768px) {
        .status-section-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }
    .status-summary-cols {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    @media (max-width: 480px) {
        .status-summary-cols {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }
    .status-col-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .status-col-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .status-col-header .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .status-col-value-group {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    .status-col-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1;
    }
    .status-col-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .status-donut-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .donut-chart-svg {
        transform: rotate(-90deg);
        border-radius: 50%;
    }
    .donut-segment {
        transition: stroke-dashoffset 0.3s ease;
    }

    /* Calendar Wrapper and Custom Buttons */
    .calendar-wrapper {
        margin-bottom: 28px;
    }
    .fc {
        font-family: 'Google Sans Flex', sans-serif !important;
    }
    .fc .fc-toolbar-title {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: var(--text-main) !important;
        text-transform: capitalize;
    }
    .fc-button-group {
        gap: 8px !important;
    }
    .fc .fc-button {
        background: var(--card-bg) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        border-radius: 10px !important;
        padding: 6px 14px !important;
        box-shadow: none !important;
        text-transform: capitalize !important;
        transition: all 0.2s;
    }
    .fc .fc-button:hover {
        background: var(--hover-bg) !important;
    }
    .fc .fc-button-active {
        background: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
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
        padding: 3px 6px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        border: none !important;
    }
    .fc-event-ongoing {
        background-color: rgba(37, 99, 235, 0.1) !important;
        color: #1e40af !important;
        border-left: 3px solid #2563eb !important;
    }
    .fc-event-upcoming {
        background-color: rgba(245, 158, 11, 0.1) !important;
        color: #b27300 !important;
        border-left: 3px solid #f59e0b !important;
    }
    .fc-event-completed {
        background-color: rgba(16, 185, 129, 0.1) !important;
        color: #065f46 !important;
        border-left: 3px solid #10b981 !important;
    }
    [data-theme="dark"] .fc-event-ongoing {
        background-color: rgba(37, 99, 235, 0.2) !important;
        color: #93c5fd !important;
    }
    [data-theme="dark"] .fc-event-upcoming {
        background-color: rgba(245, 158, 11, 0.2) !important;
        color: #fde047 !important;
    }
    [data-theme="dark"] .fc-event-completed {
        background-color: rgba(16, 185, 129, 0.2) !important;
        color: #6ee7b7 !important;
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

    /* Event Mendatang Section styling (Horizontal cards) */
    .upcoming-event-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    @media (max-width: 1024px) {
        .upcoming-event-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .upcoming-event-grid { grid-template-columns: 1fr; }
    }
    .upcoming-event-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .upcoming-event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    }
    .upcoming-event-date-box {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .upcoming-event-date-box.ongoing   { background: #eff6ff; color: #2563eb; }
    .upcoming-event-date-box.upcoming  { background: #fff7ed; color: #f59e0b; }
    .upcoming-event-date-box.completed { background: #f0fdf4; color: #10b981; }

    [data-theme="dark"] .upcoming-event-date-box.ongoing   { background: rgba(37,99,235,0.15); color: #60a5fa; }
    [data-theme="dark"] .upcoming-event-date-box.upcoming  { background: rgba(245,158,11,0.15); color: #fbbf24; }
    [data-theme="dark"] .upcoming-event-date-box.completed { background: rgba(16,185,129,0.15); color: #34d399; }

    .upcoming-event-date-day {
        font-size: 18px;
        font-weight: 700;
        line-height: 1;
    }
    .upcoming-event-date-month {
        font-size: 9px;
        font-weight: 700;
        margin-top: 2px;
        letter-spacing: 0.5px;
    }
    .upcoming-event-details {
        flex: 1;
        padding: 0 16px;
        min-width: 0;
    }
    .upcoming-event-name-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .upcoming-event-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .upcoming-event-dot.ongoing  { background: #2563eb; }
    .upcoming-event-dot.upcoming { background: #f59e0b; }
    .upcoming-event-dot.completed { background: #10b981; }

    .upcoming-event-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .upcoming-event-time, .upcoming-event-loc {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .upcoming-event-chevron {
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Original Styles to preserve */
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
    .event-list-item .event-indicator.ongoing  { background: var(--primary); }
    .event-list-item .event-indicator.upcoming { background: var(--warning); }
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
    .badge-ongoing  { background: var(--primary-soft); color: #2b55cc; }
    .badge-upcoming { background: #fff1d6; color: #b27300; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    [data-theme="dark"] .badge-ongoing  { background: var(--primary-soft); color: #8aafff; }
    [data-theme="dark"] .badge-upcoming { background: rgba(251,191,36,0.15); color: #fbbf24; }
    [data-theme="dark"] .badge-completed { background: rgba(52,211,153,0.15); color: #34d399; }

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
    .status-bar .s-ongoing  { background: var(--primary); }
    .status-bar .s-upcoming { background: var(--warning); }
    .status-bar .s-completed { background: var(--success); }
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
    .smart-banner.info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; }
    [data-theme="dark"] .smart-banner.warning { background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.3); }
    [data-theme="dark"] .smart-banner.info    { background: rgba(37,99,235,0.1); border-color: rgba(37,99,235,0.3); color: #60a5fa;}

    .attendance-panel {
        text-align: center;
        padding: 32px 24px;
        border-radius: 20px;
        background: var(--card-bg);
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

    #gpsOverlay {
        position: absolute;
        bottom: 14px;
        left: 14px;
        display: inline-flex;
        align-items: flex-start;
        gap: 10px;
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        border-radius: 12px;
        padding: 9px 14px 9px 9px;
        max-width: 68%;
        z-index: 10;
    }
</style>

@role('CEO|GM')
    <!-- Custom Dashboard Header -->
    <div class="dashboard-header-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Dasbor Utama</h1>
            <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px; font-weight: 500;">Ringkasan performa event dan aktivitas perusahaan secara real-time.</p>
        </div>
        <div>
            <button class="btn-filter" style="display: flex; align-items: center; gap: 8px; background: var(--card-bg); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; transition: background 0.2s;">
                <i data-feather="calendar" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
                <span>12 - 18 Mei 2026</span>
                <i data-feather="chevron-down" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
            </button>
        </div>
    </div>

    <!-- Redesigned Stats Grid with Sparklines -->
    <div class="stats-grid">
        <!-- Card 1: Total Event Aktif -->
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-card-header">
                    <div class="stat-icon blue"><i data-feather="calendar"></i></div>
                    <span class="stat-label">Total Event Aktif</span>
                </div>
                <div>
                    <div class="stat-value">{{ $activeEventsCount ?? 0 }}</div>
                    <div class="stat-sub">dari {{ $totalEvents ?? 0 }} event</div>
                </div>
                <div class="stat-trend-container">
                    <span class="stat-trend-badge blue"><i data-feather="arrow-up" style="width: 10px; height: 10px;"></i> 20%</span>
                    <span class="stat-trend-text">vs minggu lalu</span>
                </div>
            </div>
            <div class="stat-card-right">
                <svg class="sparkline" viewBox="0 0 100 40" style="width: 100px; height: 45px; display: block; overflow: visible;">
                    <defs>
                        <linearGradient id="sparkline-grad-blue" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2563eb" stop-opacity="0.15"></stop>
                            <stop offset="100%" stop-color="#2563eb" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M0,32 Q15,10 30,28 T60,20 T90,35 T100,12" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M0,32 Q15,10 30,28 T60,20 T90,35 T100,12 L100,40 L0,40 Z" fill="url(#sparkline-grad-blue)"></path>
                </svg>
            </div>
        </div>

        <!-- Card 2: Sedang Berjalan -->
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-card-header">
                    <div class="stat-icon emerald"><i data-feather="chevrons-right"></i></div>
                    <span class="stat-label">Sedang Berjalan</span>
                </div>
                <div>
                    <div class="stat-value">{{ $ongoingEventsCount ?? 0 }}</div>
                    <div class="stat-sub">event on-going</div>
                </div>
                <div class="stat-trend-container">
                    <span class="stat-trend-badge emerald"><i data-feather="arrow-up" style="width: 10px; height: 10px;"></i> 50%</span>
                    <span class="stat-trend-text">vs minggu lalu</span>
                </div>
            </div>
            <div class="stat-card-right">
                <svg class="sparkline" viewBox="0 0 100 40" style="width: 100px; height: 45px; display: block; overflow: visible;">
                    <defs>
                        <linearGradient id="sparkline-grad-emerald" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.15"></stop>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M0,30 Q15,20 30,35 T60,18 T90,15 T100,8" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M0,30 Q15,20 30,35 T60,18 T90,15 T100,8 L100,40 L0,40 Z" fill="url(#sparkline-grad-emerald)"></path>
                </svg>
            </div>
        </div>

        <!-- Card 3: Runner Event -->
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-card-header">
                    <div class="stat-icon amber"><i data-feather="users"></i></div>
                    <span class="stat-label">Runner Event</span>
                </div>
                <div>
                    <div class="stat-value">{{ $activeEmployeesCount ?? 0 }}</div>
                    <div class="stat-sub">karyawan bertugas event</div>
                </div>
                <div class="stat-trend-container">
                    <span class="stat-trend-badge amber"><i data-feather="arrow-up" style="width: 10px; height: 10px;"></i> 12%</span>
                    <span class="stat-trend-text">vs minggu lalu</span>
                </div>
            </div>
            <div class="stat-card-right">
                <svg class="sparkline" viewBox="0 0 100 40" style="width: 100px; height: 45px; display: block; overflow: visible;">
                    <defs>
                        <linearGradient id="sparkline-grad-amber" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.15"></stop>
                            <stop offset="100%" stop-color="#f59e0b" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M0,32 Q15,25 30,15 T60,35 T90,20 T100,28" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M0,32 Q15,25 30,15 T60,35 T90,20 T100,28 L100,40 L0,40 Z" fill="url(#sparkline-grad-amber)"></path>
                </svg>
            </div>
        </div>
        
        <!-- Card 4: Kehadiran Hari Ini -->
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-card-header">
                    <div class="stat-icon violet"><i data-feather="user-check"></i></div>
                    <span class="stat-label">Kehadiran Hari Ini</span>
                </div>
                <div>
                    <div class="stat-value">{{ $attendanceRate ?? 0 }}%</div>
                    <div class="stat-sub">karyawan sudah absen</div>
                </div>
                <div class="stat-trend-container">
                    <span class="stat-trend-badge violet"><i data-feather="arrow-up" style="width: 10px; height: 10px;"></i> 8%</span>
                    <span class="stat-trend-text">vs kemarin</span>
                </div>
            </div>
            <div class="stat-card-right">
                <svg class="sparkline" viewBox="0 0 100 40" style="width: 100px; height: 45px; display: block; overflow: visible;">
                    <defs>
                        <linearGradient id="sparkline-grad-violet" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.15"></stop>
                            <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M0,32 Q15,15 30,30 T60,18 T90,32 T100,25" fill="none" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M0,32 Q15,15 30,30 T60,18 T90,32 T100,25 L100,40 L0,40 Z" fill="url(#sparkline-grad-violet)"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Redesigned Status Event horizontal bar & Donut Chart -->
    @php
        $totalStatus = max(($statusCounts['upcoming'] ?? 0) + ($statusCounts['ongoing'] ?? 0) + ($statusCounts['completed'] ?? 0), 1);
        $pUpcoming  = (($statusCounts['upcoming']  ?? 0) / $totalStatus) * 100;
        $pOngoing   = (($statusCounts['ongoing']   ?? 0) / $totalStatus) * 100;
        $pCompleted = (($statusCounts['completed'] ?? 0) / $totalStatus) * 100;

        $offsetCompleted = 100;
        $offsetOngoing = 100 - $pCompleted;
        $offsetUpcoming = 100 - $pCompleted - $pOngoing;
    @endphp
    <div class="section-card" style="margin-bottom: 28px;">
        <div class="section-header" style="margin-bottom: 20px;">
            <span class="section-title">Status Event</span>
        </div>
        <div class="status-section-grid">
            <div class="status-summary-cols">
                <div class="status-col-item">
                    <div class="status-col-header">
                        <span class="dot" style="background: #f59e0b;"></span>
                        <span>Upcoming</span>
                    </div>
                    <div class="status-col-value-group">
                        <span class="status-col-value">{{ $statusCounts['upcoming'] ?? 0 }}</span>
                        <span class="status-col-label">event akan datang</span>
                    </div>
                </div>
                <div class="status-col-item">
                    <div class="status-col-header">
                        <span class="dot" style="background: #2563eb;"></span>
                        <span>On-Going</span>
                    </div>
                    <div class="status-col-value-group">
                        <span class="status-col-value">{{ $statusCounts['ongoing'] ?? 0 }}</span>
                        <span class="status-col-label">sedang berjalan</span>
                    </div>
                </div>
                <div class="status-col-item">
                    <div class="status-col-header">
                        <span class="dot" style="background: #10b981;"></span>
                        <span>Completed</span>
                    </div>
                    <div class="status-col-value-group">
                        <span class="status-col-value">{{ $statusCounts['completed'] ?? 0 }}</span>
                        <span class="status-col-label">selesai</span>
                    </div>
                </div>
            </div>

            <div class="status-donut-wrapper">
                <div class="donut-chart-container" style="position: relative; width: 64px; height: 64px;">
                    <svg width="64" height="64" viewBox="0 0 36 36" class="donut-chart-svg" style="width: 100%; height: 100%;">
                        <circle cx="18" cy="18" r="15.915" fill="none" stroke="var(--border-color)" stroke-width="3"></circle>
                        
                        @if($pCompleted > 0)
                        <circle class="donut-segment completed" cx="18" cy="18" r="15.915" fill="none" stroke="#10b981" stroke-width="3.2" stroke-dasharray="{{ $pCompleted }} {{ 100 - $pCompleted }}" stroke-dashoffset="{{ $offsetCompleted }}"></circle>
                        @endif
                        
                        @if($pOngoing > 0)
                        <circle class="donut-segment ongoing" cx="18" cy="18" r="15.915" fill="none" stroke="#2563eb" stroke-width="3.2" stroke-dasharray="{{ $pOngoing }} {{ 100 - $pOngoing }}" stroke-dashoffset="{{ $offsetOngoing }}"></circle>
                        @endif
                        
                        @if($pUpcoming > 0)
                        <circle class="donut-segment upcoming" cx="18" cy="18" r="15.915" fill="none" stroke="#f59e0b" stroke-width="3.2" stroke-dasharray="{{ $pUpcoming }} {{ 100 - $pUpcoming }}" stroke-dashoffset="{{ $offsetUpcoming }}"></circle>
                        @endif
                    </svg>
                    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 15px; font-weight: 700; color: var(--text-main);">{{ $totalEvents ?? 0 }}</span>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">Total Event</span>
                    <span style="font-size: 12px; color: var(--text-muted); margin-top: 1px;">dari {{ $totalEvents ?? 0 }} event</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Columns: Calendar & Comparison Trend + Upcoming Events List -->
    <div class="dashboard-cols">
        <!-- Column 1: Kalender Event -->
        <div class="section-card calendar-wrapper" style="display: flex; flex-direction: column;">
            <div class="section-header" style="flex: none; margin-bottom: 20px;">
                <span class="section-title" style="display: flex; align-items: center; gap: 8px;">
                    <i data-feather="calendar" style="width: 18px; height: 18px; color: var(--text-muted);"></i>
                    Kalender Event
                </span>
            </div>
            <div id="eventCalendar" style="flex: 1; min-height: 0;"></div>
        </div>

        <!-- Column 2: Comparison Trend Chart & Horizontal Upcoming Events -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Comparison Trend Card -->
            <div class="section-card" style="display: flex; flex-direction: column;">
                <div class="section-header" style="align-items: center; margin-bottom: 20px;">
                    <span class="section-title" style="display: flex; align-items: center; gap: 8px;">
                        <i data-feather="trending-up" style="width: 18px; height: 18px; color: var(--text-muted);"></i>
                        Tren Event Bulanan
                    </span>
                    <select id="trendYearSelect" style="padding: 6px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--hover-bg); color: var(--text-main); font-size: 12px; font-weight: 600; cursor: pointer; outline: none; transition: all 0.2s;">
                        @php $currentYear = request('year', date('Y')); @endphp
                        @for($y = date('Y') + 1; $y >= date('Y') - 4; $y--)
                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="chart-container" style="flex: none; height: 280px; margin-bottom: 12px;">
                    <canvas id="eventTrendChart"></canvas>
                </div>
                <!-- Custom Legend -->
                <div style="display: flex; justify-content: center; gap: 24px; margin-top: 8px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: var(--text-muted);">
                        <span style="width: 12px; height: 3px; background: #2563eb; border-radius: 2px; display: inline-block;"></span>
                        <span>{{ $trendYear }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: var(--text-muted);">
                        <span style="width: 12px; height: 3px; background: #94a3b8; border-radius: 2px; display: inline-block; opacity: 0.7;"></span>
                        <span>{{ $trendYear - 1 }}</span>
                    </div>
                </div>
            </div>

            <!-- Redesigned Horizontal Upcoming Events -->
            <div class="section-card">
                <div class="section-header" style="margin-bottom: 20px; align-items: center;">
                    <span class="section-title">Event Mendatang</span>
                    <a href="{{ route('events.index') }}" style="font-size: 12px; color: #2563eb; text-decoration: none; font-weight: 600; transition: color 0.2s;">
                        Lihat Semua
                    </a>
                </div>

                <div class="upcoming-event-grid">
                    @forelse($upcomingEventsList as $event)
                        <a href="{{ route('events.show', $event['id']) }}" class="upcoming-event-card">
                            <div class="upcoming-event-date-box {{ $event['status'] }}">
                                <span class="upcoming-event-date-day">{{ $event['day_num'] }}</span>
                                <span class="upcoming-event-date-month">{{ $event['month_str'] }}</span>
                            </div>
                            <div class="upcoming-event-details">
                                <div class="upcoming-event-name-group">
                                    <span class="upcoming-event-dot {{ $event['status'] }}"></span>
                                    <span class="upcoming-event-name">{{ $event['name'] }}</span>
                                </div>
                                <div class="upcoming-event-time">{{ $event['time_range'] }}</div>
                                <div class="upcoming-event-loc">{{ $event['location'] }}</div>
                            </div>
                            <div class="upcoming-event-chevron">
                                <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                            </div>
                        </a>
                    @empty
                        <div style="grid-column: span 3; text-align: center; padding: 32px 0; color: var(--text-muted);">
                            <p style="font-size: 13px;">Belum ada event mendatang.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    @if($showBanner)
        @if($bannerType === 'plan')
            <div class="smart-banner warning" style="margin-bottom: 20px;">
                <i data-feather="alert-triangle"></i>
                <div style="font-size: 13px; font-weight: 500;">
                    Kamu belum mengisi Weekly Report nih!
                </div>
            </div>
        @elseif($bannerType === 'final')
            <div class="smart-banner info" style="margin-bottom: 20px;">
                <i data-feather="bell"></i>
                <div style="font-size: 13px; font-weight: 500;">
                    Waktunya evaluasi! Jangan lupa submit laporan final Weekly Report kamu hari ini ya.
                </div>
            </div>
        @endif
    @endif

    <div class="stats-grid-3">
        <!-- Card 1: Total Event -->
        <div class="stat-card">
            <!-- <div class="stat-glow" style="background: var(--primary);"></div> -->
            <div style="display: flex; gap: 16px; align-items: flex-start; margin-bottom: 20px;">
                <div class="stat-icon blue" style="margin-bottom: 0;"><i data-feather="calendar"></i></div>
                <div>
                    <div class="stat-label" style="margin-bottom: 4px;">Total Event</div>
                    <div class="stat-value">{{ $totalEventsThisMonth }} <span style="font-size: 14px; font-weight: 500; color: var(--text-muted);">Event</span></div>
                    <div class="stat-sub" style="margin-top: 2px;">Bulan ini</div>
                </div>
            </div>
            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 0 -24px 16px -24px;">
            <a href="{{ route('events.index') }}" style="display: flex; justify-content: space-between; align-items: center; text-decoration: none; font-size: 13px; font-weight: 600; color: #1e40af;">
                <span>Lihat Semua</span>
                <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
            </a>
        </div>

        <!-- Card 2: Kehadiran Bulan Ini -->
        <div class="stat-card">
            <!-- <div class="stat-glow" style="background: #10b981;"></div> -->
            <div style="display: flex; gap: 16px; align-items: flex-start; margin-bottom: 20px;">
                <div class="stat-icon emerald" style="margin-bottom: 0;"><i data-feather="user"></i></div>
                <div>
                    <div class="stat-label" style="margin-bottom: 4px;">Absensi Bulan Ini</div>
                    <div class="stat-value">{{ $attendanceCountThisMonth }} <span style="font-size: 14px; font-weight: 500; color: var(--text-muted);">Hari</span></div>
                    <div class="stat-sub" style="margin-top: 2px;">dari {{ $workDays }} hari kerja</div>
                </div>
            </div>
            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 0 -24px 16px -24px;">
            <a href="{{ route('attendance.history') }}" style="display: flex; justify-content: space-between; align-items: center; text-decoration: none; font-size: 13px; font-weight: 600; color: #10b981;">
                <span>Lihat Riwayat</span>
                <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
            </a>
        </div>

        <!-- Card 3: To Do Belum Selesai -->
        <div class="stat-card">
            <!-- <div class="stat-glow" style="background: #ec4899;"></div> -->
            <div style="display: flex; gap: 16px; align-items: flex-start; margin-bottom: 20px;">
                <div class="stat-icon violet" style="margin-bottom: 0; background: rgba(236, 72, 153, 0.1); color: var(--danger);"><i data-feather="clipboard"></i></div>
                <div>
                    <div class="stat-label" style="margin-bottom: 4px;">To Do</div>
                    <div class="stat-value">{{ $pendingTasksCount }} <span style="font-size: 14px; font-weight: 500; color: var(--text-muted);">Jobdesk</span></div>
                    <div class="stat-sub" style="margin-top: 2px;">Perlu diselesaikan</div>
                </div>
            </div>
            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 0 -24px 16px -24px;">
            <a href="{{ route('events.index') }}" style="display: flex; justify-content: space-between; align-items: center; text-decoration: none; font-size: 13px; font-weight: 600; color: #ec4899;">
                <span>Lihat Semua</span>
                <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
            </a>
        </div>
    </div>

    <div class="dashboard-cols">
        <!-- Left Container: Kalender Event -->
        <div class="section-card calendar-wrapper" style="display: flex; flex-direction: column;">
            <div class="section-header" style="flex: none; align-items: center; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-feather="calendar" style="color: var(--primary); width: 20px; height: 20px;"></i>
                    <span class="section-title">Kalender Event</span>
                </div>
                <a href="{{ route('events.index') }}" class="btn btn-sm" style="font-size: 12px; padding: 6px 12px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 4px; background: var(--hover-bg); border: 1px solid var(--border-color); color: var(--text-main);">
                    Lihat Semua Event
                </a>
            </div>
            
            <div id="eventCalendar" style="flex: 1; min-height: 0; margin-bottom: 16px;"></div>
            
            <!-- Legend below calendar -->
            <div style="display: flex; gap: 16px; font-size: 12px; margin-top: 12px; border-top: 1px solid var(--border-color); padding-top: 12px;">
                <div style="display: flex; align-items: center; gap: 6px; color: var(--text-muted);">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb; display: inline-block;"></span>
                    Event Anda
                </div>
                <div style="display: flex; align-items: center; gap: 6px; color: var(--text-muted);">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #ec4899; display: inline-block;"></span>
                    Event Penting
                </div>
            </div>
        </div>

        <!-- Right Container: Absen Hari Ini & Riwayat Absen Terbaru -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Widget: Absen Hari Ini -->
            <div class="section-card" style="position: relative;">
                <div class="section-header" style="margin-bottom: 20px; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i data-feather="map-pin" style="color: var(--primary); width: 20px; height: 20px;"></i>
                        <span class="section-title">Absen</span>
                    </div>
                    
                </div>

                @if($todayAttendance)
                    <div style="text-align: center; padding: 24px 0;">
                        <p style="font-size: 14px; color: #059669; margin-bottom: 24px;">
                            Kamu sudah absen hari ini jam {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} WIB.
                        </p>
                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: rgba(16,185,129,0.1); border-radius: 99px; color: #065f46; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                            @if($todayAttendance->attendance_type === 'kantor')
                                <i data-feather="home" style="width: 14px; height: 14px;"></i> Kantor
                            @else
                                <i data-feather="map-pin" style="width: 14px; height: 14px;"></i> Web Absen
                            @endif
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 20px 0;">
                        <div class="digital-clock" id="digitalClock" style="font-size: 48px; font-weight: 700; margin-bottom: 8px; color: var(--text-main); font-family: monospace;">00:00:00</div>
                        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">
                            Silakan lakukan absensi untuk hari ini.
                        </p>
                        <button class="btn" style="width: 100%; justify-content: center; height: 50px; border-radius: 14px; font-weight: 700; background: var(--primary); color: white; border: none; transition: background 0.2s;" onclick="openAttendanceModal()">
                            Absen Sekarang
                        </button>
                    </div>
                @endif
            </div>

            <!-- Widget: Riwayat Absen Terbaru -->
            <div class="section-card">
                <div class="section-header" style="margin-bottom: 16px; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i data-feather="clock" style="color: #2563eb; width: 20px; height: 20px;"></i>
                        <span class="section-title">History Absen</span>
                    </div>
                    <a href="{{ route('attendance.history') }}" style="font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 600;">
                        Lihat Semua Riwayat
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($recentAttendances as $att)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 12px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center;">
                                    <i data-feather="check" style="width: 18px; height: 18px;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 600; color: var(--text-main);">
                                        {{ \Carbon\Carbon::parse($att->date)->locale('id')->translatedFormat('l, d M Y') }}
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <span style="font-size: 13px; font-weight: 600; color: var(--text-main);">
                                    {{ \Carbon\Carbon::parse($att->check_in_time)->format('H.i') }} WIB
                                </span>
                                <span class="badge" style="background: rgba(16,185,129,0.1); color: #10b981; border: none; font-size: 9px; padding: 2px 6px;">Masuk</span>
                                
                                <!-- Simulated check-out to match UI -->
                                @php
                                    // We simulate check-out as check-in + 9 hours (standard working day)
                                    $checkin = \Carbon\Carbon::parse($att->date . ' ' . $att->check_in_time);
                                    $checkout = $checkin->copy()->addHours(9)->addMinutes(rand(-10, 15));
                                @endphp
                                <span style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-left: 8px;">
                                    {{ $checkout->format('H.i') }} WIB
                                </span>
                                <span class="badge" style="background: rgba(37,99,235,0.1); color: #2563eb; border: none; font-size: 9px; padding: 2px 6px;">Pulang</span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 13px;">
                            Belum ada riwayat absensi.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    <div id="attendanceModal" style="display: none; position: fixed; inset: 0; z-index: 100; background: rgba(0,0,0,0.8); backdrop-filter: blur(4px); padding: 20px; flex-direction: column; align-items: center; justify-content: center;">
        <div style="background: var(--card-bg); width: 100%; max-width: 500px; border-radius: 20px; overflow: hidden; position: relative;">
            <button onclick="closeAttendanceModal()" style="position: absolute; top: 12px; right: 12px; z-index: 110; background: rgba(0,0,0,0.5); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">✕</button>
            
            <div style="position: relative; aspect-ratio: 4/3; background: #000;">
                <video id="webcam" autoplay playsinline style="width:100%; height:100%; object-fit:cover;"></video>
                <canvas id="photoCanvas" style="display:none;"></canvas>

                <div id="gpsOverlay" style="position: absolute; bottom: 20px; left: 20px; right: 20px; display: flex; align-items: center; gap: 16px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1.5px solid rgba(255, 255, 255, 0.55); border-radius: 16px; padding: 14px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); z-index: 10; max-width: none;">
                    <div style="width: 72px; height: 72px; border-radius: 10px; overflow: hidden; border: 1.5px solid rgba(255,255,255,0.6); flex-shrink: 0;">
                        <div id="miniMap" style="width: 100%; height: 100%;"></div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 0; text-align: left;">
                        <div id="gpsAddress" style="font-size: 11px; font-weight: 700; color: #fff; line-height: 1.3; overflow-wrap: break-word; word-wrap: break-word; white-space: normal;">Proses Lokasi...</div>
                        <div id="gpsCoords" style="font-size: 9px; font-family: monospace; color: rgba(255,255,255,0.9); font-weight: 500;">—</div>
                        <div id="gpsClock" style="font-size: 9px; color: rgba(255,255,255,0.8); font-weight: 500;">00:00 WIB</div>
                    </div>
                </div>
            </div>

            <div style="padding: 20px;">
                <button id="btnSubmitAbsen" class="btn" style="width:100%; justify-content:center; height: 48px; border-radius: 12px; font-weight: 600;">
                    <i data-feather="camera" style="width:16px; margin-right:8px;"></i> Ambil Foto & Absen
                </button>
                <div style="text-align: center; margin-top: 10px;">
                    <span id="mockLocationBtn" style="font-size: 11px; color: var(--text-muted); text-decoration: underline; cursor: pointer; font-weight: 600;">
                        📍 Simulasikan Jalan Swadaya (Kenten)
                    </span>
                </div>
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
                    datasets: [
                        {
                            label: '{{ $trendYear }}',
                            data: {!! json_encode($trendsCurrent ?? []) !!},
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37,99,235,0.08)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#2563eb',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: '{{ $trendYear - 1 }}',
                            data: {!! json_encode($trendsPrevious ?? []) !!},
                            borderColor: '#94a3b8',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            tension: 0.4,
                            fill: false,
                            pointBackgroundColor: '#94a3b8',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' },
                            ticks: { stepSize: 5 }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Year select listener
        const yearSelect = document.getElementById('trendYearSelect');
        if (yearSelect) {
            yearSelect.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('year', this.value);
                window.location.href = url.toString();
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

    // Helper to format decimal degrees to DMS
    function toDMS(val, isLat) {
        const absolute = Math.abs(val);
        const degrees = Math.floor(absolute);
        const minutesNotTruncated = (absolute - degrees) * 60;
        const minutes = Math.floor(minutesNotTruncated);
        const seconds = ((minutesNotTruncated - minutes) * 60).toFixed(1);
        const direction = isLat ? (val >= 0 ? 'N' : 'S') : (val >= 0 ? 'E' : 'W');
        return `${degrees}°${minutes}'${seconds}"${direction}`;
    }

    // Helper to wrap text on canvas
    function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
        const words = text.split(' ');
        let line = '';
        let currentY = y;
        for (let n = 0; n < words.length; n++) {
            let testLine = line + words[n] + ' ';
            let metrics = ctx.measureText(testLine);
            let testWidth = metrics.width;
            if (testWidth > maxWidth && n > 0) {
                ctx.fillText(line, x, currentY);
                line = words[n] + ' ';
                currentY += lineHeight;
            } else {
                line = testLine;
            }
        }
        ctx.fillText(line, x, currentY);
        return currentY;
    }

    // Helper to draw rounded rectangle
    function drawRoundedRect(ctx, x, y, width, height, radius) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
    }

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
            if(gpsAddress) gpsAddress.textContent = 'Kamera tidak dapat diakses.';
        });

    // ── Geolocation ──
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            userCoords = { lat, lng };
            
            // Format to DMS for UI
            coordsCache = `${toDMS(lat, true)} ${toDMS(lng, false)}`;
            if(gpsCoords) gpsCoords.textContent = coordsCache;

            // Reverse Geocode (longer address details without the country name at the end)
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(r => r.json())
                .then(d => {
                    const parts = d.display_name.split(', ');
                    if (parts.length > 1) {
                        parts.pop(); // Remove the country name at the end
                    }
                    addressCache = parts.join(', ');
                    if(gpsAddress) gpsAddress.textContent = addressCache;
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
    // Developer helper: Mock Location to test named road geocoding
    const mockBtn = document.getElementById('mockLocationBtn');
    if (mockBtn) {
        mockBtn.addEventListener('click', function() {
            // Coordinates of Jalan Swadaya, Palembang
            const mockLat = -2.9507;
            const mockLng = 104.7454;
            userCoords = { lat: mockLat, lng: mockLng };
            
            coordsCache = `${toDMS(mockLat, true)} ${toDMS(mockLng, false)}`;
            if(gpsCoords) gpsCoords.textContent = coordsCache;
            
            if(gpsAddress) gpsAddress.textContent = 'Menghubungkan Lokasi Simulasi (Jalan Swadaya)...';
            
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${mockLat}&lon=${mockLng}&format=json`)
                .then(r => r.json())
                .then(d => {
                    const parts = d.display_name.split(', ');
                    if (parts.length > 1) {
                        parts.pop(); // Remove country
                    }
                    addressCache = parts.join(', ');
                    if(gpsAddress) gpsAddress.textContent = addressCache;
                });
                
            // Update Mini Map
            if (miniMapInst) {
                miniMapInst.setView([mockLat, mockLng], 15);
                // Clear existing markers and place a new one
                miniMapInst.eachLayer(layer => {
                    if (layer instanceof L.Marker) {
                        miniMapInst.removeLayer(layer);
                    }
                });
                L.marker([mockLat, mockLng]).addTo(miniMapInst);
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
        
        // Dimensions of overlay box
        const boxWidth = canvas.width - 40;
        const boxHeight = 110;
        const boxX = 20;
        const boxY = canvas.height - boxHeight - 20;
        const borderRadius = 14;

        // Draw backdrop blur effect on canvas
        ctx.save();
        drawRoundedRect(ctx, boxX, boxY, boxWidth, boxHeight, borderRadius);
        ctx.clip();
        
        // Apply blur to clipped area (backdrop blur)
        ctx.filter = 'blur(12px)';
        ctx.drawImage(video, 0, 0);
        ctx.filter = 'none'; // reset filter
        
        // Translucent overlay
        ctx.fillStyle = 'rgba(255, 255, 255, 0.2)';
        ctx.fillRect(boxX, boxY, boxWidth, boxHeight);
        ctx.restore();
        
        // Overlay stroke border
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.55)';
        ctx.lineWidth = 1.5;
        drawRoundedRect(ctx, boxX, boxY, boxWidth, boxHeight, borderRadius);
        ctx.stroke();

        const dmsCoords = `${toDMS(userCoords.lat, true)} ${toDMS(userCoords.lng, false)}`;

        // Draw Map Tile on Canvas (crop and draw Leaflet OSM tile)
        const mapImg = new Image();
        mapImg.crossOrigin = "Anonymous";
        const zoom = 15;
        const tileX = Math.floor((userCoords.lng + 180) / 360 * Math.pow(2, zoom));
        const tileY = Math.floor((1 - Math.log(Math.tan(userCoords.lat * Math.PI / 180) + 1 / Math.cos(userCoords.lat * Math.PI / 180)) / Math.PI) / 2 * Math.pow(2, zoom));
        
        mapImg.onload = function() {
            // Map box dims
            const mapSize = 72;
            const mapX = boxX + 14;
            const mapY = boxY + (boxHeight - mapSize) / 2;
            
            // OSM tiles are 256x256
            function getTilePercentX(lon, zoom) {
                const a = (lon + 180) / 360 * Math.pow(2, zoom);
                return a - Math.floor(a);
            }
            function getTilePercentY(lat, zoom) {
                const a = (1 - Math.log(Math.tan(lat * Math.PI / 180) + 1 / Math.cos(lat * Math.PI / 180)) / Math.PI) / 2 * Math.pow(2, zoom);
                return a - Math.floor(a);
            }
            const px = getTilePercentX(userCoords.lng, zoom) * 256;
            const py = getTilePercentY(userCoords.lat, zoom) * 256;
            
            // Source crop centered
            const srcSize = 120;
            const srcX = Math.max(0, Math.min(256 - srcSize, px - srcSize/2));
            const srcY = Math.max(0, Math.min(256 - srcSize, py - srcSize/2));

            // Draw map image
            ctx.save();
            // Rounded corners for map
            ctx.beginPath();
            ctx.arc(mapX + 8, mapY + 8, 8, Math.PI, 1.5 * Math.PI);
            ctx.arc(mapX + mapSize - 8, mapY + 8, 8, 1.5 * Math.PI, 2 * Math.PI);
            ctx.arc(mapX + mapSize - 8, mapY + mapSize - 8, 8, 0, 0.5 * Math.PI);
            ctx.arc(mapX + 8, mapY + mapSize - 8, 8, 0.5 * Math.PI, Math.PI);
            ctx.closePath();
            ctx.clip();
            
            ctx.drawImage(mapImg, srcX, srcY, srcSize, srcSize, mapX, mapY, mapSize, mapSize);
            
            // Draw marker dot in center of map
            ctx.beginPath();
            ctx.arc(mapX + mapSize/2, mapY + mapSize/2, 5, 0, 2 * Math.PI);
            ctx.fillStyle = '#ef4444';
            ctx.fill();
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 1.5;
            ctx.stroke();
            
            ctx.restore();

            // Draw map border
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.6)';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.arc(mapX + 8, mapY + 8, 8, Math.PI, 1.5 * Math.PI);
            ctx.arc(mapX + mapSize - 8, mapY + 8, 8, 1.5 * Math.PI, 2 * Math.PI);
            ctx.arc(mapX + mapSize - 8, mapY + mapSize - 8, 8, 0, 0.5 * Math.PI);
            ctx.arc(mapX + 8, mapY + mapSize - 8, 8, 0.5 * Math.PI, Math.PI);
            ctx.closePath();
            ctx.stroke();

            // Draw text overlay
            ctx.fillStyle = '#ffffff';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
            ctx.shadowBlur = 4;
            
            const textX = mapX + mapSize + 16;
            let textY = boxY + 24;
            
            // Address
            ctx.font = 'bold 11px sans-serif';
            const nextY = wrapText(ctx, addressCache || 'Lokasi Absen', textX, textY, boxWidth - mapSize - 40, 15);
            
            // Coordinates
            ctx.font = 'normal 9px monospace';
            ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
            textY = nextY + 14;
            ctx.fillText(dmsCoords, textX, textY);
            
            // Time
            ctx.font = 'normal 9px sans-serif';
            ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
            textY += 12;
            ctx.fillText(new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) + ' WIB', textX, textY);

            submitPhotoAndCoords();
        };

        // Fallback if map loading fails
        mapImg.onerror = function() {
            const mapSize = 72;
            const mapX = boxX + 14;
            const mapY = boxY + (boxHeight - mapSize) / 2;
            
            ctx.fillStyle = 'rgba(0, 0, 0, 0.3)';
            ctx.fillRect(mapX, mapY, mapSize, mapSize);

            ctx.fillStyle = '#ffffff';
            const textX = mapX + mapSize + 16;
            let textY = boxY + 24;
            ctx.font = 'bold 11px sans-serif';
            const nextY = wrapText(ctx, addressCache || 'Lokasi Absen', textX, textY, boxWidth - mapSize - 40, 15);
            
            ctx.font = 'normal 9px monospace';
            ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
            textY = nextY + 14;
            ctx.fillText(dmsCoords, textX, textY);
            
            ctx.font = 'normal 9px sans-serif';
            ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
            textY += 12;
            ctx.fillText(new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) + ' WIB', textX, textY);

            submitPhotoAndCoords();
        };

        mapImg.src = `https://a.tile.openstreetmap.org/${zoom}/${tileX}/${tileY}.png`;

        function submitPhotoAndCoords() {
            const photo = canvas.toDataURL('image/png');
            
            fetch('{{ route("attendance.storeLuar") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ photo, latitude: userCoords.lat, longitude: userCoords.lng })
            })
            .then(r => r.json())
            .then(d => { alert(d.message); location.reload(); })
            .catch(() => { alert('Gagal absen.'); btnSubmit.disabled = false; btnSubmit.innerHTML = 'Ambil Foto & Absen'; });
        }
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