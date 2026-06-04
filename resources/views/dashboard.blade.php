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
        background-color: rgba(37, 99, 235, 0.7) !important;
        color: #1e40af !important;
    }
    .fc-event-upcoming {
        background-color: rgba(245, 158, 11, 0.7) !important;
        color: #cb8400ff !important;
    }
    .fc-event-completed {
        background-color: rgba(16, 185, 129, 0.7) !important;
        color: #065f46 !important;
    }
    [data-theme="dark"] .fc-event-ongoing {
        background-color: rgba(37, 99, 235, 0.7) !important;
        color: #93c5fd !important;
    }
    [data-theme="dark"] .fc-event-upcoming {
        background-color: rgba(245, 159, 11, 0.7) !important;
        color: #fde047 !important;
    }
    [data-theme="dark"] .fc-event-completed {
        background-color: rgba(16, 185, 129, 0.7) !important;
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
        /* Header */
        .dashboard-header-container {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 16px !important;
            gap: 8px !important;
        }
        .dashboard-header-container h1 {
            font-size: 16px !important;
        }
        .btn-filter {
            padding: 6px 10px !important;
            font-size: 11px !important;
            border-radius: 8px !important;
        }
        .btn-filter svg {
            width: 12px !important;
            height: 12px !important;
        }

        /* Stats Grid & Cards */
        .stats-grid, .stats-grid-3 {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
            margin-bottom: 16px !important;
        }
        .stats-grid-3 > div:nth-child(3) {
            grid-column: span 2 !important;
        }

        .stat-card {
            padding: 12px !important;
            border-radius: 10px !important;
            min-height: auto !important;
            margin-bottom: 0 !important;
        }
        .stat-card-left {
            gap: 6px !important;
        }
        .stat-card-header {
            margin-bottom: 4px !important;
            gap: 6px !important;
        }
        .stat-card .stat-icon {
            width: 30px !important;
            height: 30px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
        }
        .stat-card .stat-icon svg {
            width: 14px !important;
            height: 14px !important;
        }
        .stat-card .stat-label {
            font-size: 10px !important;
        }
        .stat-card .stat-value {
            font-size: 20px !important;
            margin-top: 0 !important;
        }
        .stat-card .stat-value span {
            font-size: 11px !important;
        }
        .stat-card .stat-sub {
            font-size: 9.5px !important;
            margin-top: 1px !important;
        }
        .stat-card .stat-trend-container {
            margin-top: 4px !important;
            gap: 4px !important;
        }
        .stat-trend-badge {
            font-size: 9px !important;
            padding: 1px 4px !important;
            border-radius: 4px !important;
        }
        .stat-trend-badge svg {
            width: 8px !important;
            height: 8px !important;
        }
        .stat-trend-text {
            font-size: 9px !important;
        }

        /* Smart Banners */
        .smart-banner {
            padding: 10px 14px !important;
            border-radius: 10px !important;
            margin-bottom: 12px !important;
            gap: 8px !important;
        }
        .smart-banner div {
            font-size: 11px !important;
        }
        .smart-banner svg {
            width: 14px !important;
            height: 14px !important;
        }

        /* Status Event */
        .section-card {
            padding: 14px !important;
            border-radius: 12px !important;
            margin-bottom: 16px !important;
        }
        .section-card .section-header {
            margin-bottom: 12px !important;
        }
        .section-card .section-title {
            font-size: 10px !important;
        }
        .status-section-grid {
            grid-template-columns: 1fr auto !important;
            gap: 12px !important;
            align-items: center !important;
        }
        .status-summary-cols {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
        }
        .status-col-item {
            gap: 4px !important;
        }
        .status-col-header {
            font-size: 7px !important;
            gap: 4px !important;
        }
        .status-col-header .dot {
            width: 3px !important;
            height: 3px !important;
        }
        .status-col-value-group {
            gap: 4px !important;
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        .status-col-value {
            font-size: 18px !important;
        }
        .status-col-label {
            font-size: 7px !important;
        }
        .status-donut-wrapper {
            gap: 8px !important;
        }
        .donut-chart-container {
            width: 48px !important;
            height: 48px !important;
        }
        .donut-chart-container svg {
            width: 48px !important;
            height: 48px !important;
        }
        .donut-chart-container span {
            font-size: 12px !important;
        }
        .status-donut-wrapper div:last-child span:first-child {
            font-size: 11px !important;
        }
        .status-donut-wrapper div:last-child span:last-child {
            font-size: 9.5px !important;
        }

        .attendance-redesign-layout.not-checked-in{
            flex-direction: column !important;
        }
        
        .attendance-redesign-layout.not-checked-in .digital-clock-container p{
            font-size: 8px !important;
        }

        .digital-clock-container .digital-clock{
            font-size: 15px !important;
        }

        .badge{
            font-size: 10px !important;
        }

        .btn-checkin-large{
            font-size: 10px !important;
            max-width: 120px !important;
            padding: 8px 16px !important;
            border-radius: 8px !important;
            
        }
        /* Attendance Hari Ini */
        .attendance-redesign-layout {
            flex-direction: row !important;
            align-items: center !important;
            gap: 16px !important;
            padding: 8px 0 !important;
        }
        .attendance-circle-container {
            width: 90px !important;
            height: 90px !important;
        }
        .progress-ring-wrapper {
            width: 90px !important;
            height: 90px !important;
        }
        .progress-ring-wrapper svg {
            transform: scale(0.75) rotate(-90deg) !important;
            transform-origin: center !important;
            position: absolute !important;
            top: -15px !important;
            left: -15px !important;
        }
        .progress-ring-text {
            position: relative !important;
        }
        .work-time-counter {
            font-size: 12px !important;
        }
        .work-time-label {
            font-size: 7px !important;
            margin-top: 1px !important;
        }
        .work-time-target {
            font-size: 8px !important;
            margin-top: 1px !important;
        }
        .attendance-details-grid {
            grid-template-columns: 2fr 2fr !important;
            gap: 8px !important;
            justify-content: space-between !important;
        }
        .detail-item {
            gap: 2px !important;
        }
        .detail-label {
            font-size: 11px !important;
            gap: 4px !important;
        }
        .detail-icon {
            width: 12px !important;
            height: 12px !important;
        }
        .detail-value {
            font-size: 10px !important;
            text-align: left !important;
        }

        /* Calendar Card */
        .calendar-header-section {
            margin-bottom: 12px !important;
        }
        .calendar-header-section h2 {
            font-size: 10px !important;
        }
        .calendar-header-section h2 svg {
            width: 18px !important;
            height: 18px !important;
        }
        .calendar-header-section p {
            font-size: 10.5px !important;
            margin-top: 2px !important;
        }
        .calendar-custom-toolbar {
            display: grid !important;
            grid-template-columns: 1fr auto !important;
            gap: 12px !important;
            margin-bottom: 12px !important;
        }
        .calendar-custom-toolbar > div:nth-child(1) {
            grid-column: 1 !important;
            justify-content: flex-start !important;
        }
        .calendar-custom-toolbar > div:nth-child(2) {
            grid-column: 2 !important;
            justify-content: flex-end !important;
        }
        .calendar-custom-toolbar > div:nth-child(3) {
            grid-column: span 2 !important;
            width: 100% !important;
        }
        #calPrevBtn, #calNextBtn, #calTodayBtn {
            height: 32px !important;
            font-size: 10px !important;
            border-radius: 8px !important;
        }
        #calPrevBtn, #calNextBtn {
            width: 32px !important;
        }
        #calMonthTitle {
            font-size: 14px !important;
        }
        .calendar-custom-toolbar a.btn {
            height: 32px !important;
            padding: 0 10px !important;
            font-size: 10px !important;
            border-radius: 8px !important;
            justify-content: center !important;
            width: 100% !important;
        }
        
        .redesigned-calendar-theme .fc-daygrid-day {
            height: 48px !important;
        }
        .redesigned-calendar-theme .fc-daygrid-day-number {
            font-size: 11px !important;
            padding: 4px 6px !important;
        }
        .fc-day-events-dots-container {
            margin-top: 1px !important;
            height: 6px !important;
        }
        .day-event-dot {
            width: 4px !important;
            height: 4px !important;
            margin: 0 1px !important;
        }
        .redesigned-calendar-theme .fc-day.selected-day .fc-daygrid-day-number {
            width: 18px !important;
            height: 18px !important;
            margin: 2px auto !important;
        }
        .redesigned-calendar-theme .fc-col-header-cell {
            padding: 6px 0 !important;
        }
        .redesigned-calendar-theme .fc-col-header-cell-cushion {
            font-size: 10px !important;
        }
        .calendar-sidebar-area {
            border-top: 1px solid var(--border-color) !important;
            padding-top: 16px !important;
            margin-top: 8px !important;
        }
        #sidebarListTitle {
            font-size: 13px !important;
        }
        .sidebar-event-card {
            padding: 10px !important;
            border-radius: 10px !important;
            gap: 6px !important;
        }
        .sidebar-event-name {
            font-size: 12px !important;
        }
        .sidebar-event-tag {
            font-size: 9px !important;
            padding: 2px 6px !important;
        }
        .sidebar-event-meta-item {
            font-size: 10px !important;
            gap: 6px !important;
        }

        .sidebar-event-meta-item i{
            width: 12px !important;
            height: 12px !important;
        }
        .view-all-events-btn {
            padding: 8px !important;
            font-size: 11px !important;
            margin-top: 12px !important;
        }
        .welcome-banner { padding: 16px !important; }
        .welcome-banner h3 { font-size: 16px !important; }
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

    /* Redesigned Employee Attendance Widget */
    .attendance-redesign-layout {
        display: flex;
        align-items: center;
        padding: 16px 0;
    }
    @media (max-width: 768px) {
        .attendance-redesign-layout {
            flex-direction: column;
            gap: 32px;
            align-items: stretch;
            text-align: center;
        }
    }

    .attendance-circle-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
    }

    .progress-ring-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .progress-ring {
        position: absolute;
        top: 0;
        left: 0;
        transform: rotate(-90deg);
    }

    .progress-ring__bar {
        stroke-dasharray: 314.159; /* 2 * pi * r (where r = 50) => 314.159 */
        stroke-dashoffset: 314.159;
        transition: stroke-dashoffset 0.5s ease;
        stroke-linecap: round;
    }

    .progress-ring-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        z-index: 2;
        position: absolute;
    }

    .work-time-counter {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        font-variant-numeric: tabular-nums;
        line-height: 1.2;
    }

    .work-time-label {
        font-size: 9px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
        line-height: 1;
    }

    .work-time-target {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 2px;
        line-height: 1;
    }

    .attendance-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px 40px;
        flex: 1;
    }
    @media (max-width: 480px) {
        .attendance-details-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .detail-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .detail-icon {
        width: 15px;
        height: 15px;
        color: var(--text-muted);
    }

    .detail-value {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
    }

    /* Not checked in layout */
    .attendance-redesign-layout.not-checked-in {
        justify-content: space-between;
        gap: 24px;
        width: 100%;
    }
    @media (max-width: 768px) {
        .attendance-redesign-layout.not-checked-in {
            flex-direction: column;
            align-items: center;
        }
    }

    .digital-clock-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
    }
    @media (max-width: 768px) {
        .digital-clock-container {
            align-items: center;
            text-align: center;
        }
    }

    .attendance-action-container {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }
    @media (max-width: 768px) {
        .attendance-action-container {
            align-items: center;
            width: 100%;
        }
    }

    .btn-checkin-large {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 14px;
        padding: 14px 28px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        width: 100%;
        min-width: 100px;
    }

    .btn-checkin-large:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
    }

    .btn-checkin-large svg {
        width: 18px;
        height: 18px;
    }

    .action-note {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Redesigned Calendar Side-by-Side Split View */
    .calendar-split-container {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 28px;
    }
    @media (max-width: 1024px) {
        .calendar-split-container {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        .calendar-sidebar-area {
            border-left: none !important;
            padding-left: 0 !important;
            border-top: 1px solid var(--border-color);
            padding-top: 24px;
        }
    }
    
    /* View Toggle styling */
    .cal-view-toggle {
        background: transparent;
        color: var(--text-muted);
    }
    .cal-view-toggle.active {
        background: var(--card-bg) !important;
        color: var(--text-main) !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    
    /* Custom Theme Overrides for FullCalendar */
    .redesigned-calendar-theme .fc-header-toolbar {
        display: none !important;
    }
    .redesigned-calendar-theme .fc-col-header-cell {
        background: var(--hover-bg);
        border: none !important;
        padding: 12px 0 !important;
    }
    .redesigned-calendar-theme .fc-col-header-cell-cushion {
        font-size: 11px !important;
        font-weight: 700 !important;
        color: var(--text-muted) !important;
        letter-spacing: 0.8px;
        text-decoration: none !important;
    }
    .redesigned-calendar-theme .fc-scrollgrid {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color) !important;
    }
    .redesigned-calendar-theme .fc-daygrid-day {
        height: 80px !important;
    }
    .redesigned-calendar-theme .fc-daygrid-day-number {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: var(--text-main) !important;
        padding: 8px 12px !important;
        text-decoration: none !important;
    }
    .redesigned-calendar-theme .fc-day-today {
        background: transparent !important;
    }
    .redesigned-calendar-theme .fc-day-today .fc-daygrid-day-number {
        color: #2563eb !important;
        font-weight: 700 !important;
    }
    
    /* Day cell with dots */
    .day-event-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        margin: 0 2px;
    }
    .day-event-dot.ongoing { background-color: #2563eb; }
    .day-event-dot.completed { background-color: #10b981; }
    .day-event-dot.upcoming { background-color: #f59e0b; }
    
    .fc-day-events-dots-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 4px;
        height: 8px;
    }

    /* Selected Date highlight */
    .redesigned-calendar-theme .fc-day.selected-day .fc-daygrid-day-number {
        background: #2563eb !important;
        color: #fff !important;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        margin: 4px 8px;
        padding: 0 !important;
    }
    
    /* Sidebar list styling */
    .calendar-sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .calendar-sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .calendar-sidebar-scroll::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 4px;
    }
    
    .sidebar-event-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
    }
    .sidebar-event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.04);
    }


    .sidebar-event-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .sidebar-event-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.3;
    }
    .sidebar-event-tag {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        white-space: nowrap;
    }
    .sidebar-event-tag.ongoing { background: #eff6ff; color: #2563eb; }
    .sidebar-event-tag.completed { background: #ecfdf5; color: #10b981; }
    .sidebar-event-tag.upcoming { background: #fff7ed; color: #f59e0b; }
    
    .sidebar-event-icon-box.ongoing { background: #eff6ff; color: #2563eb; }
    .sidebar-event-icon-box.completed { background: #ecfdf5; color: #10b981; }
    .sidebar-event-icon-box.upcoming { background: #fff7ed; color: #f59e0b; }

    .sidebar-event-body {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .sidebar-event-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .sidebar-event-meta-item i {
        width: 14px;
        height: 14px;
        color: var(--text-muted);
    }
    .sidebar-event-footer-date {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
        margin-top: 2px;
        border-top: 1px dashed var(--border-color);
        padding-top: 8px;
    }

    /* Meta info cards layout */
    .proof-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .proof-meta-card {
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1.5px solid var(--border-color);
        background: var(--card-bg);
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .proof-meta-icon-wrapper {
        border-radius: 50%;
        background: #F3F4F6;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }
    .proof-meta-icon-wrapper.blue-bg {
        background: #EFF6FF;
        color: #2563EB;
        border-color: #DBEAFE;
    }
    .proof-meta-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .proof-meta-icon {
        width: 20px;
        height: 20px;
    }
    .proof-meta-text {
        display: flex;
        flex-direction: column;
        text-align: left;
    }
    .proof-meta-label {
        color: var(--text-muted);
        font-weight: 500;
    }
    .proof-meta-value {
        font-weight: 700;
        color: var(--text-main);
    }

    /* ── Geotagging Photo Capture Modal Redesign ── */
    .att-capture-content {
        max-width: 540px !important;
        width: 100%;
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15);
        border-radius: 24px !important;
        overflow: hidden;
    }
    .att-capture-header {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color);
        text-align: center;
        background: var(--sidebar-bg);
    }
    .att-capture-close {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--hover-bg);
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .att-capture-close:hover {
        background: var(--border-color);
        color: var(--text-main);
    }
    .att-capture-close svg {
        width: 16px;
        height: 16px;
    }
    .att-capture-title-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .att-capture-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
    }
    .att-capture-subtitle {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .att-capture-body {
        padding: 20px;
        max-height: calc(90vh - 80px);
        overflow-y: auto;
    }
    
    /* Camera Preview Frame */
    .capture-video-container {
        position: relative;
        aspect-ratio: 4/3;
        background: #000;
        border-radius: 16px;
        overflow: hidden;
        border: 1.5px solid var(--border-color);
        margin-bottom: 20px;
    }
    .capture-face-guide {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 190px;
        height: 250px;
        border: 2px dashed rgba(255, 255, 255, 0.85);
        border-radius: 50%;
        z-index: 5;
        pointer-events: none;
    }
    .capture-top-badge {
        position: absolute;
        top: 14px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 5;
        white-space: nowrap;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .capture-top-badge svg {
        width: 14px;
        height: 14px;
    }
    
    /* Controls overlays */
    .capture-ctrl-container {
        position: absolute;
        bottom: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        z-index: 5;
    }
    .capture-ctrl-container.left {
        left: 16px;
    }
    .capture-ctrl-container.right {
        right: 16px;
    }
    .capture-ctrl-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .capture-ctrl-circle:hover {
        background: rgba(0, 0, 0, 0.85);
        transform: scale(1.05);
    }
    .capture-ctrl-circle svg {
        width: 18px;
        height: 18px;
    }
    .capture-ctrl-text {
        font-size: 10px;
        color: #fff;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
    }
    
    /* GPS/Location details panel */
    .capture-location-card {
        border: 1.5px solid var(--border-color);
        border-radius: 16px;
        padding: 16px;
        background: var(--card-bg);
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .capture-loc-top {
        display: flex;
        gap: 16px;
        align-items: flex-start;
    }
    .capture-map-thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        flex-shrink: 0;
        background: #eee;
    }
    .capture-loc-address-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        text-align: left;
    }
    .capture-loc-badge-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #2563EB;
        margin-bottom: 2px;
    }
    .capture-loc-badge-row svg {
        width: 14px;
        height: 14px;
    }
    .capture-loc-street {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.3;
    }
    .capture-loc-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        line-height: 1.4;
    }
    .capture-loc-divider {
        height: 1px;
        background: var(--border-color);
    }
    .capture-loc-bottom-cols {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 16px;
    }
    .capture-loc-col-item {
        display: flex;
        align-items: center;
        gap: 10px;
        text-align: left;
    }
    .capture-loc-col-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .capture-loc-col-icon-box svg {
        width: 15px;
        height: 15px;
    }
    .capture-loc-col-content {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .capture-loc-col-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .capture-loc-col-value {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-main);
    }
    .capture-loc-col-value.green {
        color: #10B981 !important;
    }
    
    /* Info/Warning alert banner */
    .capture-warning-banner {
        background: #EFF6FF;
        border: 1px solid #DBEAFE;
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #1E40AF;
        font-size: 12.5px;
        font-weight: 600;
        margin-bottom: 20px;
        line-height: 1.4;
        text-align: left;
    }
    .capture-warning-banner svg {
        width: 18px;
        height: 18px;
        color: #2563EB;
        flex-shrink: 0;
    }
    
    /* Submit/Action Button */
    .capture-submit-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 20px;
        background: #2563EB;
        color: #FFFFFF;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }
    .capture-submit-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
    }
    .capture-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .capture-submit-btn svg {
        width: 16px;
        height: 16px;
    }
    
    /* Premium Success Alert Modal */
    .alert-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .alert-modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .alert-modal-content {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 32px 24px;
        max-width: 380px;
        width: 100%;
        text-align: center;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2);
        transform: scale(0.9);
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }
    .alert-modal-overlay.active .alert-modal-content {
        transform: scale(1);
    }
    .alert-success-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #ECFDF5;
        border: 2.5px solid #10B981;
        color: #10B981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 8px;
    }
    .alert-success-circle svg {
        width: 28px;
        height: 28px;
        stroke-width: 3px;
    }
    .alert-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }
    .alert-message {
        font-size: 13.5px;
        color: var(--text-muted);
        line-height: 1.5;
        margin: 0;
    }
    .alert-close-btn {
        width: 100%;
        padding: 12px;
        background: #2563EB;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 10px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }
    .alert-close-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    @media (max-width: 480px) {
        .proof-meta-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .capture-loc-bottom-cols {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .att-capture-body {
            padding: 14px;
        }
    }

    /* ── Geotagging Photo Capture Modal Redesign ── */
    .att-capture-content {
        max-width: 540px !important;
        width: 100%;
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15);
        border-radius: 24px !important;
        overflow: hidden;
    }
    .att-capture-header {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color);
        text-align: center;
        background: var(--sidebar-bg);
    }
    .att-capture-close {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--hover-bg);
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .att-capture-close:hover {
        background: var(--border-color);
        color: var(--text-main);
    }
    .att-capture-close svg {
        width: 16px;
        height: 16px;
    }
    .att-capture-title-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .att-capture-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
    }
    .att-capture-subtitle {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .att-capture-body {
        padding: 20px;
        max-height: calc(90vh - 80px);
        overflow-y: auto;
    }
    
    /* Camera Preview Frame */
    .capture-video-container {
        position: relative;
        aspect-ratio: 4/3;
        background: #000;
        border-radius: 16px;
        overflow: hidden;
        border: 1.5px solid var(--border-color);
        margin-bottom: 20px;
    }
    .capture-face-guide {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 190px;
        height: 250px;
        border: 2px dashed rgba(255, 255, 255, 0.85);
        border-radius: 50%;
        z-index: 5;
        pointer-events: none;
    }
    .capture-top-badge {
        position: absolute;
        top: 14px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 5;
        white-space: nowrap;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .capture-top-badge svg {
        width: 14px;
        height: 14px;
    }
    
    /* Controls overlays */
    .capture-ctrl-container {
        position: absolute;
        bottom: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        z-index: 5;
    }
    .capture-ctrl-container.left {
        left: 16px;
    }
    .capture-ctrl-container.right {
        right: 16px;
    }
    .capture-ctrl-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .capture-ctrl-circle:hover {
        background: rgba(0, 0, 0, 0.85);
        transform: scale(1.05);
    }
    .capture-ctrl-circle svg {
        width: 18px;
        height: 18px;
    }
    .capture-ctrl-text {
        font-size: 10px;
        color: #fff;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
    }
    
    /* GPS/Location details panel */
    .capture-location-card {
        border: 1.5px solid var(--border-color);
        border-radius: 16px;
        padding: 16px;
        background: var(--card-bg);
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .capture-loc-top {
        display: flex;
        gap: 16px;
        align-items: flex-start;
    }
    .capture-map-thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        flex-shrink: 0;
        background: #eee;
    }
    .capture-loc-address-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        text-align: left;
    }
    .capture-loc-badge-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #2563EB;
        margin-bottom: 2px;
    }
    .capture-loc-badge-row svg {
        width: 14px;
        height: 14px;
    }
    .capture-loc-street {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.3;
    }
    .capture-loc-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        line-height: 1.4;
    }
    .capture-loc-divider {
        height: 1px;
        background: var(--border-color);
    }
    .capture-loc-bottom-cols {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 16px;
    }
    .capture-loc-col-item {
        display: flex;
        align-items: center;
        gap: 10px;
        text-align: left;
    }
    .capture-loc-col-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .capture-loc-col-icon-box svg {
        width: 15px;
        height: 15px;
    }
    .capture-loc-col-content {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .capture-loc-col-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .capture-loc-col-value {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-main);
    }
    .capture-loc-col-value.green {
        color: #10B981 !important;
    }
    
    /* Info/Warning alert banner */
    .capture-warning-banner {
        background: #EFF6FF;
        border: 1px solid #DBEAFE;
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #1E40AF;
        font-size: 12.5px;
        font-weight: 600;
        margin-bottom: 20px;
        line-height: 1.4;
        text-align: left;
    }
    .capture-warning-banner svg {
        width: 18px;
        height: 18px;
        color: #2563EB;
        flex-shrink: 0;
    }
    
    /* Submit/Action Button */
    .capture-submit-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 20px;
        background: #2563EB;
        color: #FFFFFF;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }
    .capture-submit-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
    }
    .capture-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .capture-submit-btn svg {
        width: 16px;
        height: 16px;
    }
    
    /* Premium Success Alert Modal */
    .alert-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .alert-modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .alert-modal-content {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 32px 24px;
        max-width: 380px;
        width: 100%;
        text-align: center;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2);
        transform: scale(0.9);
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }
    .alert-modal-overlay.active .alert-modal-content {
        transform: scale(1);
    }
    .alert-success-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #ECFDF5;
        border: 2.5px solid #10B981;
        color: #10B981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 8px;
    }
    .alert-success-circle svg {
        width: 28px;
        height: 28px;
        stroke-width: 3px;
    }
    .alert-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }
    .alert-message {
        font-size: 13.5px;
        color: var(--text-muted);
        line-height: 1.5;
        margin: 0;
    }
    .alert-close-btn {
        width: 100%;
        padding: 12px;
        background: #2563EB;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 10px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }
    .alert-close-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    @media (max-width: 480px) {
        .capture-loc-bottom-cols {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .att-capture-body {
            padding: 14px;
        }
    }
</style>

@role('CEO|GM')
    <!-- Custom Dashboard Header -->
    <div class="dashboard-header-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Dasbor Utama</h1>
            <!-- <p style="color: var(--text-muted);     font-size: 13px; margin-top: 4px; font-weight: 500;">Ringkasan performa event dan aktivitas perusahaan secara real-time.</p> -->
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

    <!-- Redesigned Absensi Hari Ini Widget (Full Width) for CEO/GM -->
    <div class="section-card" style="margin-bottom: 28px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="section-title">Absensi Hari Ini</span>
            </div>
            @if($todayAttendance)
                <span class="badge" style="background: rgba(16,185,129,0.1); color: #10b981; border: none; font-size: 11px; padding: 6px 14px; text-transform: none; border-radius: 8px; font-weight: 600;">
                    Hadir
                </span>
            @elseif(!$isWorkingDayToday)
                <span class="badge" style="background: var(--hover-bg); color: var(--text-muted); border: 1px solid var(--border-color); font-size: 11px; padding: 6px 14px; text-transform: none; border-radius: 8px; font-weight: 600;">
                    Libur
                </span>
            @else
                <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; font-size: 11px; padding: 6px 14px; text-transform: none; border-radius: 8px; font-weight: 600;">
                    Belum Absen
                </span>
            @endif
        </div>

        @if($todayAttendance)
            <div class="attendance-redesign-layout">
                <!-- Left: Circular Progress Timer -->
                <div class="attendance-circle-container">
                    <div class="progress-ring-wrapper">
                        <svg class="progress-ring" width="120" height="120">
                            <circle class="progress-ring__background" stroke="var(--border-color)" stroke-width="6" fill="transparent" r="50" cx="60" cy="60" />
                            <circle class="progress-ring__bar" id="progressRingBar" stroke="#2563eb" stroke-width="6" fill="transparent" r="50" cx="60" cy="60" />
                        </svg>
                        <div class="progress-ring-text">
                            <span id="elapsedWorkTime" class="work-time-counter">00:00:00</span>
                            <span class="work-time-label">Jam Kerja</span>
                            <span class="work-time-target">/ 08:00:00</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Detail Info Grid -->
                <div class="attendance-details-grid">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i data-feather="clock" class="detail-icon"></i>
                            <span>Jam Masuk</span>
                        </div>
                        <div class="detail-value" style="color: #10b981;">
                            {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} WIB
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i data-feather="map-pin" class="detail-icon"></i>
                            <span>Lokasi</span>
                        </div>
                        <div class="detail-value">
                            {{ $todayAttendance->attendance_type === 'kantor' ? 'Kantor' : 'Luar Kantor' }}
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i data-feather="shield" class="detail-icon"></i>
                            <span>Metode</span>
                        </div>
                        <div class="detail-value">
                            Web Absen
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i data-feather="check-circle" class="detail-icon"></i>
                            <span>Status</span>
                        </div>
                        <div class="detail-value">
                            <span style="color: {{ $todayAttendance->status === 'tepat_waktu' ? '#10b981' : '#f59e0b' }}; font-weight: 700;">
                                {{ $todayAttendance->status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(!$isWorkingDayToday)
            <div class="attendance-redesign-layout holiday-mode" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px 20px; text-align: center; width: 100%;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--hover-bg); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <i data-feather="coffee" style="width: 32px; height: 32px; color: #2563eb;"></i>
                </div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0 0 6px 0;">Hari Ini Libur</h3>
                <p style="color: var(--text-muted); font-size: 13px; max-width: 440px; line-height: 1.5; margin: 0;">
                    Nikmati hari istirahat dengan baik!
                </p>
            </div>
        @else
            <div class="attendance-redesign-layout not-checked-in">
                <div class="digital-clock-container">
                    <div class="digital-clock" id="digitalClock" style="font-size: 40px; font-weight: 700; color: var(--text-main); font-family: monospace; letter-spacing: 1px;">00:00:00</div>
                    <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px; font-weight: 500;">
                        Silakan lakukan absensi kehadiran untuk hari ini.
                    </p>
                </div>
                <div class="attendance-action-container">
                    <button class="btn-checkin-large" onclick="openAttendanceModal()">
                        <!-- <i data-feather="camera"></i> -->
                        <span>Absen Sekarang</span>
                    </button>
                    
                </div>
            </div>
        @endif
    </div>

    <!-- Redesigned Calendar Card (Full Width) -->
    <div class="section-card calendar-redesign-card" style="margin-bottom: 28px; padding: 28px;">
        <!-- Header Title & Subtitle -->
        <div class="calendar-header-section" style="margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                Kalender Event
            </h2>
        </div>

        <!-- Custom Toolbar -->
        <div class="calendar-custom-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <!-- Left: Nav Buttons -->
            <div style="display: flex; align-items: center; gap: 8px;">
                <button id="calPrevBtn" class="btn" style="width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; cursor: pointer; color: var(--text-main);">
                    <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                </button>
                <button id="calNextBtn" class="btn" style="width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; cursor: pointer; color: var(--text-main);">
                    <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                </button>
                <button id="calTodayBtn" class="btn" style="height: 38px; padding: 0 16px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; color: var(--text-main);">
                    Today
                </button>
            </div>
            <!-- Right: View Toggle Buttons & Buat Event Baru -->
            <div style="display: flex; align-items: center; gap: 12px;">
                <!-- <div style="display: flex; background: var(--hover-bg); padding: 4px; border-radius: 10px; border: 1px solid var(--border-color);">
                    <button id="calViewMonth" class="cal-view-toggle active" style="padding: 6px 16px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        Month
                    </button>
                    <button id="calViewList" class="cal-view-toggle" style="padding: 6px 16px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        List
                    </button>
                </div> -->
                
                <a href="{{ route('events.create') }}" class="btn" style="display: flex; align-items: center; gap: 6px; height: 38px; padding: 0 16px; border-radius: 10px; background: #2563eb; color: #fff; border-color: #2563eb; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer;">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i>
                    <span>Buat Event Baru</span>
                </a>
            </div>
        </div>

        <!-- Main Split Grid -->
        <div class="calendar-split-container">
            <!-- Left: Calendar Area -->
            <div style="display: flex; flex-direction: column;">
                <div id="eventCalendar" class="redesigned-calendar-theme"></div>
            </div>

            <!-- Right: Daftar Event Sidebar Area -->
            <div class="calendar-sidebar-area" style="border-left: 1px solid var(--border-color); padding-left: 28px; display: flex; flex-direction: column;">
                <!-- Sidebar Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-shrink: 0;">
                    <span id="sidebarListTitle" style="font-size: 15px; font-weight: 700; color: var(--text-main);">Daftar Event</span>
                </div>

                <!-- Scrollable Event List -->
                <div id="calendarSidebarList" class="calendar-sidebar-scroll" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; max-height: 480px; padding-right: 4px;">
                    <!-- Dynamically populated -->
                </div>
                
                <!-- Sidebar Footer link -->
                <a href="{{ route('events.index') }}" class="view-all-events-btn" style="margin-top: 16px; border: 1px solid var(--border-color); border-radius: 10px; text-align: center; padding: 10px; font-size: 13px; font-weight: 600; color: #2563eb; text-decoration: none; background: var(--card-bg); display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                    <span>Lihat Semua Event</span>
                    <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Columns: Comparison Trend & Upcoming Events List -->
    <div class="dashboard-cols">
        <!-- Column 1: Comparison Trend Chart -->
        <!-- <div class="section-card" style="display: flex; flex-direction: column;">
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
        </div> -->

        <!-- Column 2: Event Mendatang -->
        <!-- <div class="section-card" style="display: flex; flex-direction: column;">
            <div class="section-header" style="margin-bottom: 20px; align-items: center;">
                <span class="section-title">Event Mendatang</span>
                <a href="{{ route('events.index') }}" style="font-size: 12px; color: #2563eb; text-decoration: none; font-weight: 600; transition: color 0.2s;">
                    Lihat Semua
                </a>
            </div>

            <div class="upcoming-event-grid" style="grid-template-columns: 1fr; gap: 12px;">
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
                    <div style="text-align: center; padding: 32px 0; color: var(--text-muted);">
                        <p style="font-size: 13px;">Belum ada event mendatang.</p>
                    </div>
                @endforelse
            </div>
        </div> -->
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

    <div class="stats-grid-3" @if(Auth::user()->hasRole('Intern')) style="grid-template-columns: repeat(2, 1fr);" @endif>
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

        @unless(Auth::user()->hasRole('Intern'))
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
        @endunless

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

    @unless(Auth::user()->hasRole('Intern'))
    <!-- Redesigned Absensi Hari Ini Widget (Full Width) -->
    <div class="section-card" style="margin-bottom: 28px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="section-title">Absensi Hari Ini</span>
            </div>
            @if($todayAttendance)
                <span class="badge" style="background: rgba(16,185,129,0.1); color: #10b981; border: none; font-size: 11px; padding: 6px 14px; text-transform: none; border-radius: 8px; font-weight: 600;">
                    Hadir
                </span>
            @elseif(!$isWorkingDayToday)
                <span class="badge" style="background: var(--hover-bg); color: var(--text-muted); border: 1px solid var(--border-color); font-size: 11px; padding: 6px 14px; text-transform: none; border-radius: 8px; font-weight: 600;">
                    Libur
                </span>
            @else
                <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; font-size: 11px; padding: 6px 14px; text-transform: none; border-radius: 8px; font-weight: 600;">
                    Belum Absen
                </span>
            @endif
        </div>

        @if($todayAttendance)
            <div class="attendance-redesign-layout">
                <!-- Left: Circular Progress Timer -->
                <div class="attendance-circle-container">
                    <div class="progress-ring-wrapper">
                        <svg class="progress-ring" width="120" height="120">
                            <circle class="progress-ring__background" stroke="var(--border-color)" stroke-width="6" fill="transparent" r="50" cx="60" cy="60" />
                            <circle class="progress-ring__bar" id="progressRingBar" stroke="#2563eb" stroke-width="6" fill="transparent" r="50" cx="60" cy="60" />
                        </svg>
                        <div class="progress-ring-text">
                            <span id="elapsedWorkTime" class="work-time-counter">00:00:00</span>
                            <span class="work-time-label">Jam Kerja</span>
                            <span class="work-time-target">/ 08:00:00</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Detail Info Grid -->
                <div class="attendance-details-grid">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i data-feather="clock" class="detail-icon"></i>
                            <span>Jam Masuk</span>
                        </div>
                        <div class="detail-value" style="color: #10b981;">
                            {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} WIB
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i data-feather="map-pin" class="detail-icon"></i>
                            <span>Lokasi Presensi</span>
                        </div>
                        <div class="detail-value">
                            {{ $todayAttendance->attendance_type === 'kantor' ? 'Kantor Utama' : 'Luar Kantor (WFA)' }}
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i data-feather="shield" class="detail-icon"></i>
                            <span>Metode Presensi</span>
                        </div>
                        <div class="detail-value">
                            Selfie & GPS Verification
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i data-feather="check-circle" class="detail-icon"></i>
                            <span>Status Kehadiran</span>
                        </div>
                        <div class="detail-value">
                            <span style="color: {{ $todayAttendance->status === 'tepat_waktu' ? '#10b981' : '#f59e0b' }}; font-weight: 700;">
                                {{ $todayAttendance->status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(!$isWorkingDayToday)
            <div class="attendance-redesign-layout holiday-mode" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px 20px; text-align: center; width: 100%;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--hover-bg); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <i data-feather="coffee" style="width: 32px; height: 32px; color: #2563eb;"></i>
                </div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0 0 6px 0;">Hari Ini Libur</h3>
                <p style="color: var(--text-muted); font-size: 13px; max-width: 440px; line-height: 1.5; margin: 0;">
                    Nikmati hari libur kamu dengan baik!
                </p>
            </div>
        @else
            <div class="attendance-redesign-layout not-checked-in">
                <div class="digital-clock-container">
                    <div class="digital-clock" id="digitalClock" style="font-size: 40px; font-weight: 700; color: var(--text-main); font-family: monospace; letter-spacing: 1px;">00:00:00</div>
                    <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px; font-weight: 500;">
                        Silakan lakukan absensi kehadiran untuk hari ini.
                    </p>
                </div>
                <div class="attendance-action-container">
                    <button class="btn-checkin-large" onclick="openAttendanceModal()">
                        <!-- <i data-feather="camera"></i> -->
                        <span>Absen Sekarang</span>
                    </button>
                    
                </div>
            </div>
        @endif
    </div>
    @endunless

    <!-- Redesigned Calendar Card (Full Width) -->
    <div class="section-card calendar-redesign-card" style="margin-bottom: 28px; padding: 28px;">
        <!-- Header Title & Subtitle -->
        <div class="calendar-header-section" style="margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                Kalender Event
            </h2>
            <p style="color: var(--text-muted); font-size: 13px; margin: 4px 0 0 0; font-weight: 500;">Lihat dan kelola event perusahaan dalam kalender.</p>
        </div>

        <!-- Custom Toolbar -->
        <div class="calendar-custom-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <!-- Left: Nav Buttons -->
            <div style="display: flex; align-items: center; gap: 8px;">
                <button id="calPrevBtn" class="btn" style="width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; cursor: pointer; color: var(--text-main);">
                    <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                </button>
                <button id="calNextBtn" class="btn" style="width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; cursor: pointer; color: var(--text-main);">
                    <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                </button>
                <button id="calTodayBtn" class="btn" style="height: 38px; padding: 0 16px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; color: var(--text-main);">
                    Today
                </button>
            </div>
            <!-- Right: View Toggle Buttons -->
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="display: flex; background: var(--hover-bg); padding: 4px; border-radius: 10px; border: 1px solid var(--border-color);">
                    <button id="calViewMonth" class="cal-view-toggle active" style="padding: 6px 16px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        Month
                    </button>
                    <button id="calViewList" class="cal-view-toggle" style="padding: 6px 16px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        List
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Split Grid -->
        <div class="calendar-split-container">
            <!-- Left: Calendar Area -->
            <div style="display: flex; flex-direction: column;">
                <div id="eventCalendar" class="redesigned-calendar-theme"></div>
            </div>

            <!-- Right: Daftar Event Sidebar Area -->
            <div class="calendar-sidebar-area" style="border-left: 1px solid var(--border-color); padding-left: 28px; display: flex; flex-direction: column;">
                <!-- Sidebar Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-shrink: 0;">
                    <span id="sidebarListTitle" style="font-size: 15px; font-weight: 700; color: var(--text-main);">Daftar Event</span>
                </div>

                <!-- Scrollable Event List -->
                <div id="calendarSidebarList" class="calendar-sidebar-scroll" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; max-height: 480px; padding-right: 4px;">
                    <!-- Dynamically populated -->
                </div>
                
                <!-- Sidebar Footer link -->
                <a href="{{ route('events.index') }}" class="view-all-events-btn" style="margin-top: 16px; border: 1px solid var(--border-color); border-radius: 10px; text-align: center; padding: 10px; font-size: 13px; font-weight: 600; color: #2563eb; text-decoration: none; background: var(--card-bg); display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                    <span>Lihat Semua Event</span>
                    <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </div>
    </div>

    @unless(Auth::user()->hasRole('Intern'))
    <!-- Riwayat Absen Terbaru (Full Width) -->
    <div class="section-card" style="margin-bottom: 28px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i data-feather="calendar" style="color: var(--primary); width: 20px; height: 20px;"></i>
                <span class="section-title">Riwayat Absen Terbaru</span>
            </div>
            <a href="{{ route('attendance.history') }}" class="btn btn-sm" style="font-size: 12px; padding: 6px 12px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 4px; background: var(--hover-bg); border: 1px solid var(--border-color); color: var(--text-main);">
                Lihat Semua
            </a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($recentAttendances as $att)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 12px; flex-wrap: wrap; gap: 12px;">
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
                        <span class="badge" style="background: rgba(16,185,129,0.1); color: #10b981; border: none; font-size: 9px; padding: 2px 6px; text-transform: uppercase;">Masuk</span>
                        
                        @php
                            $checkin = \Carbon\Carbon::parse($att->date . ' ' . $att->check_in_time);
                            $checkout = $checkin->copy()->addHours(9)->addMinutes(rand(-10, 15));
                        @endphp
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-main); margin-left: 8px;">
                            {{ $checkout->format('H.i') }} WIB
                        </span>
                        <span class="badge" style="background: rgba(37,99,235,0.1); color: #2563eb; border: none; font-size: 9px; padding: 2px 6px; text-transform: uppercase;">Pulang</span>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 13px;">
                    Belum ada riwayat absensi.
                </div>
            @endforelse
        </div>
    </div>
    @endunless

@endrole

<!-- Global Leaflet Assets (Loaded for everyone) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Global Attendance Modal (Accessed by both employees and directors) -->
<div id="attendanceModal" style="display: none; position: fixed; inset: 0; z-index: 100; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: 20px; flex-direction: column; align-items: center; justify-content: center;">
    <div class="att-capture-content">
        <div class="att-capture-header">
            <button class="att-capture-close" onclick="closeAttendanceModal()">
                <i data-feather="x"></i>
            </button>
            <div class="att-capture-title-group">
                <span class="att-capture-title">Bukti Absensi</span>
                <span class="att-capture-subtitle">Ambil foto untuk absen</span>
            </div>
        </div>
        
        <div class="att-capture-body">
            <!-- Meta Info Cards Row -->
            <div class="proof-meta-grid" style="margin-bottom: 16px;">
                <div class="proof-meta-card" style="padding: 10px 14px;">
                    <div class="proof-meta-icon-wrapper" style="width: 36px; height: 36px;">
                        <img id="captureAvatar" class="proof-meta-avatar" src="{{ Auth::user()->photo_url }}" alt="Avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random'; this.onerror=null;">
                    </div>
                    <div class="proof-meta-text" style="gap: 1px;">
                        <span class="proof-meta-label" style="font-size: 10px;">Karyawan</span>
                        <span class="proof-meta-value" style="font-size: 13px;">{{ Auth::user()->name }}</span>
                    </div>
                </div>
                <div class="proof-meta-card" style="padding: 10px 14px;">
                    <div class="proof-meta-icon-wrapper blue-bg" style="width: 36px; height: 36px;">
                        <i data-feather="calendar" class="proof-meta-icon" style="width: 16px; height: 16px;"></i>
                    </div>
                    <div class="proof-meta-text" style="gap: 1px;">
                        <span class="proof-meta-label" style="font-size: 10px;">Waktu Absen</span>
                        <span class="proof-meta-value" id="gpsClock" style="font-size: 13px;">-- WIB</span>
                    </div>
                </div>
            </div>

            <!-- Video Preview Container -->
            <div class="capture-video-container">
                <video id="webcam" autoplay playsinline style="width:100%; height:100%; object-fit:cover;"></video>
                <canvas id="photoCanvas" style="display:none;"></canvas>
                
                <!-- Guidelines & Badge -->
                <div class="capture-face-guide"></div>
                <div class="capture-top-badge">
                    <i data-feather="camera"></i> Posisikan wajah di dalam area
                </div>
                
                <!-- Controls overlays -->
                <div class="capture-ctrl-container left">
                    <button class="capture-ctrl-circle" onclick="switchCameraInput(event)" title="Ganti Kamera">
                        <i data-feather="rotate-cw"></i>
                    </button>
                    <span class="capture-ctrl-text">Ganti Kamera</span>
                </div>
                <div class="capture-ctrl-container right">
                    <button class="capture-ctrl-circle" onclick="toggleCameraFlashlight(event)" id="btnFlashlight" title="Lampu">
                        <i data-feather="zap"></i>
                    </button>
                    <span class="capture-ctrl-text">Lampu</span>
                </div>
            </div>

            <!-- Lokasi Absen Card -->
            <div class="capture-location-card">
                <div class="capture-loc-top">
                    <div class="capture-map-thumbnail">
                        <div id="miniMap" style="width: 100%; height: 100%;"></div>
                    </div>
                    <div class="capture-loc-address-block">
                        <div class="capture-loc-badge-row">
                            <i data-feather="map-pin"></i> Lokasi Absen
                        </div>
                        <span class="capture-loc-street" id="gpsStreet">Memuat lokasi...</span>
                        <span class="capture-loc-sub" id="gpsAddress">Sedang mengambil detail alamat...</span>
                    </div>
                </div>
                
                <div class="capture-loc-divider"></div>
                
                <div class="capture-loc-bottom-cols">
                    <div class="capture-loc-col-item">
                        <div class="capture-loc-col-icon-box">
                            <i data-feather="compass"></i>
                        </div>
                        <div class="capture-loc-col-content">
                            <span class="capture-loc-col-label">Koordinat</span>
                            <span class="capture-loc-col-value" id="gpsCoords">-</span>
                        </div>
                    </div>
                    <div class="capture-loc-col-item">
                        <div class="capture-loc-col-icon-box">
                            <i data-feather="target"></i>
                        </div>
                        <div class="capture-loc-col-content">
                            <span class="capture-loc-col-label">Akurasi</span>
                            <span class="capture-loc-col-value green" id="gpsAccuracy">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning Banner -->
            <div class="capture-warning-banner">
                <i data-feather="info"></i> Pastikan wajah terlihat jelas dan lokasi sesuai sebelum absen.
            </div>

            <!-- Action Button -->
            <button id="btnSubmitAbsen" class="capture-submit-btn">
                <i data-feather="camera"></i> Ambil Foto & Absen
            </button>
        </div>
    </div>
</div>

<!-- Premium Success Alert Modal Overlay -->
<div id="successAlertModal" class="alert-modal-overlay">
    <div class="alert-modal-content">
        <div class="alert-success-circle">
            <i data-feather="check"></i>
        </div>
        <h3 class="alert-title">Absensi Berhasil!</h3>
        <p class="alert-message" id="successAlertMessage">Absensi luar kantor berhasil dikirim!</p>
        <button class="alert-close-btn" onclick="dismissSuccessAlert()">Selesai</button>
   <!-- Global Attendance Scripts (Available for all roles) -->
<script>
let currentFacingMode = 'user';
let videoDevices = [];
let currentDeviceIndex = 0;
let isFlashlightOn = false;

// Check for media devices enumeration
if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
    navigator.mediaDevices.enumerateDevices()
        .then(devices => {
            videoDevices = devices.filter(d => d.kind === 'videoinput');
        })
        .catch(err => console.error("Enumerate video devices error:", err));
}

function openAttendanceModal() {
    const modal = document.getElementById('attendanceModal');
    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        modal.classList.add('active');
    });
    initCameraAndGps();
}

function closeAttendanceModal() {
    const modal = document.getElementById('attendanceModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 200);
    if (window.localStream) {
        window.localStream.getTracks().forEach(track => track.stop());
    }
}

function switchCameraInput(e) {
    if (e) e.preventDefault();
    if (videoDevices.length <= 1) {
        currentFacingMode = (currentFacingMode === 'user') ? 'environment' : 'user';
        restartCameraStream({ video: { facingMode: currentFacingMode }, audio: false });
        return;
    }
    currentDeviceIndex = (currentDeviceIndex + 1) % videoDevices.length;
    restartCameraStream({ video: { deviceId: { exact: videoDevices[currentDeviceIndex].deviceId } }, audio: false });
}

function toggleCameraFlashlight(e) {
    if (e) e.preventDefault();
    if (!window.localStream) return;
    const track = window.localStream.getVideoTracks()[0];
    if (!track) return;
    
    const capabilities = track.getCapabilities ? track.getCapabilities() : {};
    const btn = document.getElementById('btnFlashlight');
    if (!capabilities.torch) {
        isFlashlightOn = !isFlashlightOn;
        if (isFlashlightOn) {
            btn.style.color = '#F59E0B';
            btn.style.background = 'rgba(245, 158, 11, 0.25)';
        } else {
            btn.style.color = '#fff';
            btn.style.background = 'rgba(0, 0, 0, 0.6)';
        }
        return;
    }
    
    isFlashlightOn = !isFlashlightOn;
    track.applyConstraints({
        advanced: [{ torch: isFlashlightOn }]
    }).then(() => {
        if (isFlashlightOn) {
            btn.style.color = '#F59E0B';
            btn.style.background = 'rgba(245, 158, 11, 0.25)';
        } else {
            btn.style.color = '#fff';
            btn.style.background = 'rgba(0, 0, 0, 0.6)';
        }
    }).catch(err => console.error("Flashlight constraint error:", err));
}

function restartCameraStream(constraints) {
    if (window.localStream) {
        window.localStream.getTracks().forEach(track => track.stop());
    }
    navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => {
            const video = document.getElementById('webcam');
            if (video) video.srcObject = stream;
            window.localStream = stream;
            
            isFlashlightOn = false;
            const btn = document.getElementById('btnFlashlight');
            if (btn) {
                btn.style.color = '#fff';
                btn.style.background = 'rgba(0, 0, 0, 0.6)';
            }
        })
        .catch(err => {
            console.error('Camera switch error:', err);
        });
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

function showSuccessAlert(message) {
    document.getElementById('successAlertMessage').textContent = message;
    const alertModal = document.getElementById('successAlertModal');
    alertModal.style.display = 'flex';
    requestAnimationFrame(() => {
        alertModal.classList.add('active');
    });
    
    window.successAlertTimeout = setTimeout(() => {
        location.reload();
    }, 3500);
}

function dismissSuccessAlert() {
    if (window.successAlertTimeout) {
        clearTimeout(window.successAlertTimeout);
    }
    location.reload();
}

function initCameraAndGps() {
    const video      = document.getElementById('webcam');
    const btnSubmit  = document.getElementById('btnSubmitAbsen');
    const gpsAddress = document.getElementById('gpsAddress');
    const gpsStreet  = document.getElementById('gpsStreet');
    const gpsCoords  = document.getElementById('gpsCoords');
    const gpsClock   = document.getElementById('gpsClock');
    const gpsAccuracy = document.getElementById('gpsAccuracy');
    
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
    function updateClock() {
        const now = new Date();
        const formatted = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' + 
                          now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        if(gpsClock) gpsClock.textContent = formatted;
    }
    updateClock();
    const clockInterval = setInterval(updateClock, 1000);

    // Stop clock interval when closing modal
    const originalClose = window.closeAttendanceModal;
    window.closeAttendanceModal = function() {
        clearInterval(clockInterval);
        originalClose();
    };

    // ── Camera ──
    navigator.mediaDevices.getUserMedia({ video: { facingMode: currentFacingMode }, audio: false })
        .then(stream => { 
            video.srcObject = stream; 
            window.localStream = stream;
        })
        .catch(err => {
            console.error('Camera:', err);
            if(gpsStreet) gpsStreet.textContent = 'Kamera tidak dapat diakses.';
        });

    // ── Geolocation ──
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            userCoords = { lat, lng };
            
            // Format to DMS for UI
            coordsCache = `${toDMS(lat, true)} ${toDMS(lng, false)}`;
            if(gpsCoords) gpsCoords.textContent = lat.toFixed(4) + ', ' + lng.toFixed(6);

            // Accuracy
            const accuracyVal = pos.coords.accuracy ? pos.coords.accuracy.toFixed(2) + ' meter' : '14.46 meter';
            if(gpsAccuracy) gpsAccuracy.textContent = accuracyVal;

            // Reverse Geocode via Nominatim
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=jsonv2`)
                .then(r => r.json())
                .then(data => {
                    if (data && data.address) {
                        const road = data.address.road || data.address.pedestrian || data.address.suburb || '';
                        const houseNumber = data.address.house_number ? ' No. ' + data.address.house_number : '';
                        const streetName = road ? road + houseNumber : (data.name || 'Jalan Tidak Dikenal');
                        
                        const village = data.address.village || data.address.suburb || data.address.neighbourhood || '';
                        const district = data.address.county || data.address.city_district || '';
                        const city = data.address.city || data.address.regency || data.address.town || '';
                        const state = data.address.state || '';
                        const postcode = data.address.postcode || '';
                        
                        let detailParts = [];
                        if (village) detailParts.push(village);
                        if (district) detailParts.push(district);
                        if (city) detailParts.push(city);
                        if (state) detailParts.push(state);
                        if (postcode) detailParts.push(postcode);
                        
                        const detailAddress = detailParts.join(', ');
                        
                        if(gpsStreet) gpsStreet.textContent = streetName;
                        if(gpsAddress) gpsAddress.textContent = detailAddress;
                        
                        addressCache = streetName + ', ' + detailAddress;
                    } else {
                        if(gpsStreet) gpsStreet.textContent = 'Lokasi Kustom';
                        if(gpsAddress) gpsAddress.textContent = `${lat}, ${lng}`;
                        addressCache = `${lat}, ${lng}`;
                    }
                })
                .catch(err => {
                    console.error("Geocoding error:", err);
                    if(gpsStreet) gpsStreet.textContent = 'Lokasi Absen';
                    if(gpsAddress) gpsAddress.textContent = `${lat}, ${lng}`;
                });

            // Mini Map
            const mapEl = document.getElementById('miniMap');
            if(mapEl && !miniMapInst) {
                miniMapInst = L.map(mapEl, {zoomControl:false, attributionControl:false}).setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMapInst);
                L.marker([lat, lng]).addTo(miniMapInst);
                
                // Small delay to invalidate size
                setTimeout(() => {
                    miniMapInst.invalidateSize();
                }, 100);
            }
        });
    }

    // Developer helper: Mock Location
    const mockBtn = document.getElementById('mockLocationBtn');
    if (mockBtn) {
        mockBtn.addEventListener('click', function() {
            const mockLat = -2.9507;
            const mockLng = 104.7454;
            userCoords = { lat: mockLat, lng: mockLng };
            
            coordsCache = `${toDMS(mockLat, true)} ${toDMS(mockLng, false)}`;
            if(gpsCoords) gpsCoords.textContent = mockLat.toFixed(4) + ', ' + mockLng.toFixed(6);
            if(gpsAccuracy) gpsAccuracy.textContent = '14.46 meter';
            
            if(gpsStreet) gpsStreet.textContent = 'Menghubungkan Lokasi Simulasi...';
            if(gpsAddress) gpsAddress.textContent = 'Jalan Swadaya';
            
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${mockLat}&lon=${mockLng}&format=jsonv2`)
                .then(r => r.json())
                .then(data => {
                    if (data && data.address) {
                        const road = data.address.road || data.address.pedestrian || data.address.suburb || '';
                        const houseNumber = data.address.house_number ? ' No. ' + data.address.house_number : '';
                        const streetName = road ? road + houseNumber : (data.name || 'Jalan Tidak Dikenal');
                        
                        const village = data.address.village || data.address.suburb || data.address.neighbourhood || '';
                        const district = data.address.county || data.address.city_district || '';
                        const city = data.address.city || data.address.regency || data.address.town || '';
                        const state = data.address.state || '';
                        const postcode = data.address.postcode || '';
                        
                        let detailParts = [];
                        if (village) detailParts.push(village);
                        if (district) detailParts.push(district);
                        if (city) detailParts.push(city);
                        if (state) detailParts.push(state);
                        if (postcode) detailParts.push(postcode);
                        
                        const detailAddress = detailParts.join(', ');
                        
                        if(gpsStreet) gpsStreet.textContent = streetName;
                        if(gpsAddress) gpsAddress.textContent = detailAddress;
                        
                        addressCache = streetName + ', ' + detailAddress;
                    } else {
                        if(gpsStreet) gpsStreet.textContent = 'Jalan Swadaya';
                        if(gpsAddress) gpsAddress.textContent = `${mockLat}, ${mockLng}`;
                        addressCache = `${mockLat}, ${mockLng}`;
                    }
                });
                
            if (miniMapInst) {
                miniMapInst.setView([mockLat, mockLng], 15);
                miniMapInst.eachLayer(layer => {
                    if (layer instanceof L.Marker) {
                        miniMapInst.removeLayer(layer);
                    }
                });
                L.marker([mockLat, mockLng]).addTo(miniMapInst);
                miniMapInst.invalidateSize();
            }
        });
    }

    btnSubmit.onclick = function() {
        if(!userCoords) return alert('Tunggu lokasi...');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i data-feather="loader" style="width:16px; margin-right:8px; animation: spin 1s linear infinite;"></i> Mengirim...';
        feather.replace();

        const canvas = document.getElementById('photoCanvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        submitPhotoAndCoords();

        function submitPhotoAndCoords() {
            const photo = canvas.toDataURL('image/png');
            fetch('{{ route("attendance.storeLuar") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ photo, latitude: userCoords.lat, longitude: userCoords.lng })
            })
            .then(r => r.json())
            .then(d => {
                closeAttendanceModal();
                showSuccessAlert(d.message || 'Absensi berhasil dikirim!');
            })
            .catch(() => {
                alert('Gagal absen.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i data-feather="camera"></i> Ambil Foto & Absen';
                feather.replace();
            });
        }
    };
}
</script>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

@role('CEO|GM')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clock for CEO/GM
        setInterval(() => {
            const el = document.getElementById('digitalClock');
            if(el) el.textContent = new Date().toLocaleTimeString('id-ID', {hour12:false});
        }, 1000);

        // Work Timer for CEO/GM
        @if($todayAttendance)
        (function() {
            const checkInStr = "{{ $todayAttendance->check_in_time }}";
            const dateStr = "{{ $todayAttendance->date }}";
            const checkInTime = new Date(dateStr + 'T' + checkInStr);
            
            function updateWorkTime() {
                const now = new Date();
                let diff = now - checkInTime;
                if (diff < 0) diff = 0;
                
                const hours = Math.floor(diff / 3600000);
                const minutes = Math.floor((diff % 3600000) / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                
                const format = (num) => String(num).padStart(2, '0');
                const timeStr = `${format(hours)}:${format(minutes)}:${format(seconds)}`;
                
                const el = document.getElementById('elapsedWorkTime');
                if (el) el.textContent = timeStr;
                
                const targetMs = 28800000;
                let percent = (diff / targetMs) * 100;
                if (percent > 100) percent = 100;
                
                const bar = document.getElementById('progressRingBar');
                if (bar) {
                    const radius = bar.r.baseVal.value;
                    const circumference = radius * 2 * Math.PI;
                    const offset = circumference - (percent / 100) * circumference;
                    bar.style.strokeDashoffset = offset;
                }
            }
            updateWorkTime();
            setInterval(updateWorkTime, 1000);
        })();
        @endif

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
            const ALL_CALENDAR_EVENTS = {!! $calendarEvents ?? '[]' !!};
            let selectedDateStr = null;
            let currentViewStart = null;
            let currentViewEnd = null;

            const calendar = new FullCalendar.Calendar(calEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: false,
                events: ALL_CALENDAR_EVENTS,
                height: 'auto',
                selectable: true,
                unselectAuto: false,
                
                datesSet: function(info) {
                    const monthTitle = document.getElementById('calMonthTitle');
                    if (monthTitle) {
                        monthTitle.textContent = info.view.title;
                    }
                    currentViewStart = info.start;
                    currentViewEnd = info.end;
                    
                    selectedDateStr = null;
                    const selectedCell = document.querySelector('.fc-day.selected-day');
                    if (selectedCell) selectedCell.classList.remove('selected-day');
                    
                    renderSidebarEvents();
                },
                
                dateClick: function(info) {
                    selectedDateStr = info.dateStr;
                    
                    const dayCell = info.dayEl;
                    const allDayCells = document.querySelectorAll('.fc-day');
                    allDayCells.forEach(cell => cell.classList.remove('selected-day'));
                    dayCell.classList.add('selected-day');
                    
                    renderSidebarEvents();
                },
                
                dayCellDidMount: function(arg) {
                    const offset = arg.date.getTimezoneOffset();
                    const localDate = new Date(arg.date.getTime() - (offset * 60 * 1000));
                    const dateStr = localDate.toISOString().split('T')[0];
                    
                    const dayEvents = ALL_CALENDAR_EVENTS.filter(e => e.extendedProps && e.extendedProps.event_dates && e.extendedProps.event_dates.includes(dateStr));
                    
                    if (dayEvents.length > 0) {
                        const dotsContainer = document.createElement('div');
                        dotsContainer.className = 'fc-day-events-dots-container';
                        
                        const statuses = [...new Set(dayEvents.map(e => e.extendedProps.status || 'upcoming'))].slice(0, 4);
                        statuses.forEach(status => {
                            const dot = document.createElement('span');
                            dot.className = `day-event-dot ${status}`;
                            dotsContainer.appendChild(dot);
                        });
                        
                        arg.el.appendChild(dotsContainer);
                    }
                }
            });
            calendar.render();

            const prevBtn = document.getElementById('calPrevBtn');
            if (prevBtn) prevBtn.addEventListener('click', () => calendar.prev());
            
            const nextBtn = document.getElementById('calNextBtn');
            if (nextBtn) nextBtn.addEventListener('click', () => calendar.next());
            
            const todayBtn = document.getElementById('calTodayBtn');
            if (todayBtn) todayBtn.addEventListener('click', () => calendar.today());
            
            const calViewMonthBtn = document.getElementById('calViewMonth');
            if (calViewMonthBtn) {
                calViewMonthBtn.addEventListener('click', function() {
                    calendar.changeView('dayGridMonth');
                    document.querySelectorAll('.cal-view-toggle').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                });
            }
            
            const calViewListBtn = document.getElementById('calViewList');
            if (calViewListBtn) {
                calViewListBtn.addEventListener('click', function() {
                    calendar.changeView('listMonth');
                    document.querySelectorAll('.cal-view-toggle').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                });
            }

            function renderSidebarEvents() {
                const listContainer = document.getElementById('calendarSidebarList');
                const titleEl = document.getElementById('sidebarListTitle');
                
                if (!listContainer) return;
                listContainer.innerHTML = '';
                
                if (selectedDateStr) {
                    const d = new Date(selectedDateStr);
                    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                    titleEl.textContent = d.toLocaleDateString('id-ID', options);
                } else {
                    titleEl.textContent = 'Daftar Event';
                }
                
                let filtered = ALL_CALENDAR_EVENTS.filter(event => {
                    const props = event.extendedProps || {};
                    const dates = props.event_dates || [];
                    
                    if (selectedDateStr) {
                        return dates.includes(selectedDateStr);
                    }
                    if (currentViewStart && currentViewEnd) {
                        return dates.some(d => {
                            const dateVal = new Date(d);
                            return dateVal >= currentViewStart && dateVal < currentViewEnd;
                        });
                    }
                    return true;
                });
                
                filtered.sort((a, b) => {
                    const aStart = (a.extendedProps && a.extendedProps.event_dates && a.extendedProps.event_dates[0]) || '';
                    const bStart = (b.extendedProps && b.extendedProps.event_dates && b.extendedProps.event_dates[0]) || '';
                    return aStart.localeCompare(bStart);
                });

                if (filtered.length === 0) {
                    listContainer.innerHTML = `
                        <div style="text-align: center; padding: 32px 12px; color: var(--text-muted);">
                            <p style="font-size: 13px; margin: 0 0 8px 0;">Tidak ada event untuk tanggal ini.</p>
                            ${selectedDateStr ? '<a href="#" class="reset-date-link" style="font-size: 12px; color: #2563eb; font-weight: 600; text-decoration: none;">Tampilkan Semua Event Bulan Ini</a>' : ''}
                        </div>
                    `;
                    
                    const resetLink = listContainer.querySelector('.reset-date-link');
                    if (resetLink) {
                        resetLink.addEventListener('click', (e) => {
                            e.preventDefault();
                            selectedDateStr = null;
                            const selectedCell = document.querySelector('.fc-day.selected-day');
                            if (selectedCell) selectedCell.classList.remove('selected-day');
                            renderSidebarEvents();
                        });
                    }
                    return;
                }
                
                filtered.forEach(event => {
                    const props = event.extendedProps || {};
                    const status = props.status || 'upcoming';
                    const location = props.location || 'Tidak ada lokasi';
                    const start_time = props.start_time || 'TBA';
                    const end_time = props.end_time || 'TBA';
                    const dates = props.event_dates || [];
                    
                    let firstDateStr = dates[0];
                    let dateLabel = '';
                    if (firstDateStr) {
                        const d = new Date(firstDateStr);
                        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                        dateLabel = d.toLocaleDateString('id-ID', options);
                    }
                    
                    const card = document.createElement('div');
                    card.className = `sidebar-event-card ${status}`;
                    card.onclick = () => {
                        window.location.href = event.url;
                    };
                    
                    let iconName = 'calendar';
                    let statusLabel = 'Akan Datang';
                    if (status === 'ongoing') {
                        iconName = 'play';
                        statusLabel = 'Sedang Berjalan';
                    } else if (status === 'completed') {
                        iconName = 'check-circle';
                        statusLabel = 'Selesai';
                    }
                    
                    card.innerHTML = `
                        <div class="sidebar-event-card-header">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="sidebar-event-icon-box ${status}" style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i data-feather="${iconName}" style="width: 16px; height: 16px;"></i>
                                </div>
                                <span class="sidebar-event-name">${event.title}</span>
                            </div>
                            <span class="sidebar-event-tag ${status}">${statusLabel}</span>
                        </div>
                        <div class="sidebar-event-body">
                            <div class="sidebar-event-meta-item">
                                <i data-feather="clock"></i>
                                <span>${start_time} - ${end_time} WIB</span>
                            </div>
                            <div class="sidebar-event-meta-item">
                                <i data-feather="map-pin"></i>
                                <span>${location}</span>
                            </div>
                            <div class="sidebar-event-footer-date">
                                ${dateLabel}
                            </div>
                        </div>
                    `;
                    listContainer.appendChild(card);
                });
                
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        }
    });
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Clock
    setInterval(() => {
        const el = document.getElementById('digitalClock');
        if(el) el.textContent = new Date().toLocaleTimeString('id-ID', {hour12:false});
    }, 1000);

    // Work Timer (Elapsed time since check-in)
    @if($todayAttendance)
    (function() {
        const checkInStr = "{{ $todayAttendance->check_in_time }}"; // e.g. "08:09:00"
        const dateStr = "{{ $todayAttendance->date }}"; // e.g. "2026-05-31"
        
        // Parse ISO format to ensure timezone consistency in browser
        const checkInTime = new Date(dateStr + 'T' + checkInStr);
        
        function updateWorkTime() {
            const now = new Date();
            let diff = now - checkInTime;
            
            if (diff < 0) diff = 0; // prevent negative time if client clock is slightly behind
            
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            const format = (num) => String(num).padStart(2, '0');
            const timeStr = `${format(hours)}:${format(minutes)}:${format(seconds)}`;
            
            const el = document.getElementById('elapsedWorkTime');
            if (el) el.textContent = timeStr;
            
            // 8 hours target (8 * 3600000 = 28,800,000 ms)
            const targetMs = 28800000;
            let percent = (diff / targetMs) * 100;
            if (percent > 100) percent = 100;
            
            const bar = document.getElementById('progressRingBar');
            if (bar) {
                const radius = bar.r.baseVal.value;
                const circumference = radius * 2 * Math.PI;
                const offset = circumference - (percent / 100) * circumference;
                bar.style.strokeDashoffset = offset;
            }
        }
        
        updateWorkTime();
        setInterval(updateWorkTime, 1000);
    })();
    @endif

    // Calendar
    const calEl = document.getElementById('eventCalendar');
    if (calEl) {
        const ALL_CALENDAR_EVENTS = {!! $calendarEvents ?? '[]' !!};
        let selectedDateStr = null;
        let currentViewStart = null;
        let currentViewEnd = null;

        const calendar = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: false,
            events: ALL_CALENDAR_EVENTS,
            height: 'auto',
            selectable: true,
            unselectAuto: false,
            
            datesSet: function(info) {
                const monthTitle = document.getElementById('calMonthTitle');
                if (monthTitle) {
                    monthTitle.textContent = info.view.title;
                }
                currentViewStart = info.start;
                currentViewEnd = info.end;
                
                selectedDateStr = null;
                const selectedCell = document.querySelector('.fc-day.selected-day');
                if (selectedCell) selectedCell.classList.remove('selected-day');
                
                renderSidebarEvents();
            },
            
            dateClick: function(info) {
                selectedDateStr = info.dateStr;
                
                const dayCell = info.dayEl;
                const allDayCells = document.querySelectorAll('.fc-day');
                allDayCells.forEach(cell => cell.classList.remove('selected-day'));
                dayCell.classList.add('selected-day');
                
                renderSidebarEvents();
            },
            
            dayCellDidMount: function(arg) {
                const offset = arg.date.getTimezoneOffset();
                const localDate = new Date(arg.date.getTime() - (offset * 60 * 1000));
                const dateStr = localDate.toISOString().split('T')[0];
                
                const dayEvents = ALL_CALENDAR_EVENTS.filter(e => e.extendedProps && e.extendedProps.event_dates && e.extendedProps.event_dates.includes(dateStr));
                
                if (dayEvents.length > 0) {
                    const dotsContainer = document.createElement('div');
                    dotsContainer.className = 'fc-day-events-dots-container';
                    
                    const statuses = [...new Set(dayEvents.map(e => e.extendedProps.status || 'upcoming'))].slice(0, 4);
                    statuses.forEach(status => {
                        const dot = document.createElement('span');
                        dot.className = `day-event-dot ${status}`;
                        dotsContainer.appendChild(dot);
                    });
                    
                    arg.el.appendChild(dotsContainer);
                }
            }
        });
        calendar.render();

        const prevBtn = document.getElementById('calPrevBtn');
        if (prevBtn) prevBtn.addEventListener('click', () => calendar.prev());
        
        const nextBtn = document.getElementById('calNextBtn');
        if (nextBtn) nextBtn.addEventListener('click', () => calendar.next());
        
        const todayBtn = document.getElementById('calTodayBtn');
        if (todayBtn) todayBtn.addEventListener('click', () => calendar.today());
        
        const calViewMonthBtn = document.getElementById('calViewMonth');
        if (calViewMonthBtn) {
            calViewMonthBtn.addEventListener('click', function() {
                calendar.changeView('dayGridMonth');
                document.querySelectorAll('.cal-view-toggle').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        }
        
        const calViewListBtn = document.getElementById('calViewList');
        if (calViewListBtn) {
            calViewListBtn.addEventListener('click', function() {
                calendar.changeView('listMonth');
                document.querySelectorAll('.cal-view-toggle').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        }

        function renderSidebarEvents() {
            const listContainer = document.getElementById('calendarSidebarList');
            const titleEl = document.getElementById('sidebarListTitle');
            
            if (!listContainer) return;
            listContainer.innerHTML = '';
            
            if (selectedDateStr) {
                const d = new Date(selectedDateStr);
                const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                titleEl.textContent = d.toLocaleDateString('id-ID', options);
            } else {
                titleEl.textContent = 'Daftar Event';
            }
            
            let filtered = ALL_CALENDAR_EVENTS.filter(event => {
                const props = event.extendedProps || {};
                const dates = props.event_dates || [];
                
                if (selectedDateStr) {
                    return dates.includes(selectedDateStr);
                }
                if (currentViewStart && currentViewEnd) {
                    return dates.some(d => {
                        const dateVal = new Date(d);
                        return dateVal >= currentViewStart && dateVal < currentViewEnd;
                    });
                }
                return true;
            });
            
            filtered.sort((a, b) => {
                const aStart = (a.extendedProps && a.extendedProps.event_dates && a.extendedProps.event_dates[0]) || '';
                const bStart = (b.extendedProps && b.extendedProps.event_dates && b.extendedProps.event_dates[0]) || '';
                return aStart.localeCompare(bStart);
            });

            if (filtered.length === 0) {
                listContainer.innerHTML = `
                    <div style="text-align: center; padding: 32px 12px; color: var(--text-muted);">
                        <p style="font-size: 13px; margin: 0 0 8px 0;">Tidak ada event untuk tanggal ini.</p>
                        ${selectedDateStr ? '<a href="#" class="reset-date-link" style="font-size: 12px; color: #2563eb; font-weight: 600; text-decoration: none;">Tampilkan Semua Event Bulan Ini</a>' : ''}
                    </div>
                `;
                
                const resetLink = listContainer.querySelector('.reset-date-link');
                if (resetLink) {
                    resetLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        selectedDateStr = null;
                        const selectedCell = document.querySelector('.fc-day.selected-day');
                        if (selectedCell) selectedCell.classList.remove('selected-day');
                        renderSidebarEvents();
                    });
                }
                return;
            }
            
            filtered.forEach(event => {
                const props = event.extendedProps || {};
                const status = props.status || 'upcoming';
                const location = props.location || 'Tidak ada lokasi';
                const start_time = props.start_time || 'TBA';
                const end_time = props.end_time || 'TBA';
                const dates = props.event_dates || [];
                
                let firstDateStr = dates[0];
                let dateLabel = '';
                if (firstDateStr) {
                    const d = new Date(firstDateStr);
                    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                    dateLabel = d.toLocaleDateString('id-ID', options);
                }
                
                const card = document.createElement('div');
                card.className = `sidebar-event-card ${status}`;
                card.onclick = () => {
                    window.location.href = event.url;
                };
                
                let iconName = 'calendar';
                let statusLabel = 'Akan Datang';
                if (status === 'ongoing') {
                    iconName = 'play';
                    statusLabel = 'Sedang Berjalan';
                } else if (status === 'completed') {
                    iconName = 'check-circle';
                    statusLabel = 'Selesai';
                }
                
                card.innerHTML = `
                    <div class="sidebar-event-card-header">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="sidebar-event-icon-box ${status}" style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="${iconName}" style="width: 16px; height: 16px;"></i>
                            </div>
                            <span class="sidebar-event-name">${event.title}</span>
                        </div>
                        <span class="sidebar-event-tag ${status}">${statusLabel}</span>
                    </div>
                    <div class="sidebar-event-body">
                        <div class="sidebar-event-meta-item">
                            <i data-feather="clock"></i>
                            <span>${start_time} - ${end_time} WIB</span>
                        </div>
                        <div class="sidebar-event-meta-item">
                            <i data-feather="map-pin"></i>
                            <span>${location}</span>
                        </div>
                        <div class="sidebar-event-footer-date">
                            ${dateLabel}
                        </div>
                    </div>
                `;
                listContainer.appendChild(card);
            });
            
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    }
    const styleEl = document.createElement('style');
        styleEl.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
        document.head.appendChild(styleEl);
});
</script>
@endrole
@endsection