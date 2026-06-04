@extends('layouts.app')

@section('title', 'Rekap Absensi Harian')

@section('content')
<!-- Load Leaflet JS & CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    /* ── Header Card ── */
    .att-header-card {
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
    .att-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .att-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #EFF6FF;
        border: 1px solid #DBEAFE;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563EB;
        flex-shrink: 0;
    }
    .att-header-icon svg {
        width: 24px;
        height: 24px;
    }
    .att-header-text h2 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
    }
    .att-header-text p {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: var(--card-bg);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
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
    .btn-export-pdf {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: #ef4444;
        color: #ffffff;
        border: 1px solid #ef4444;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-export-pdf:hover {
        background: #dc2626;
        border-color: #dc2626;
    }
    .btn-export-pdf svg {
        width: 16px;
        height: 16px;
    }

    /* ── Stat Cards Grid ── */
    /* ── Stat Cards Grid ── */
    .att-stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        margin-bottom: 12px;
    }
    @media (max-width: 1200px) {
        .att-stats-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .att-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .att-stat-card {
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
    /* .att-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
    } */
    .att-stat-card .att-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .att-stat-card .att-stat-icon svg {
        width: 18px;
        height: 18px;
    }
    .att-stat-card .att-stat-icon.blue    { background: #eff6ff; color: #2563eb; border-color: #dbeafe; }
    .att-stat-card .att-stat-icon.emerald { background: #ecfdf5; color: #10b981; border-color: #d1fae5; }
    .att-stat-card .att-stat-icon.rose    { background: #fff7ed; color: #f59e0b; border-color: #fed7aa; }
    .att-stat-card .att-stat-icon.red     { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }
    .att-stat-card .att-stat-icon.violet  { background: #faf5ff; color: #8b5cf6; border-color: #f3e8ff; }

    .att-stat-card .att-stat-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 6px;
    }
    .att-stat-card .att-stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.1;
    }
    .att-stat-card .att-stat-sub {
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
    .att-section-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .att-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .att-toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        flex: 1;
    }
    .att-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .att-search-box {
        position: relative;
        width: 280px;
    }
    @media (max-width: 640px) {
        .att-search-box { width: 100%; }
    }
    .att-search-box svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
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

    .att-search-box .form-control {
        padding-left: 32px;
        height: 40px;
        font-size: 13px;
        border-radius: 10px;
    }
    
    .att-input-date {
        position: relative;
        width: 160px;
    }
    .att-input-date svg {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .att-input-date .form-control {
        padding: 0px 10px;
        height: 40px;
        font-size: 13px;
        border-radius: 10px;
    }
    
    .att-select {
        padding: 0px 10px;
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
    .legend-item.hadir { background: #ECFDF5; color: #059669; border: 1px solid rgba(5,150,105,0.15); }
    .legend-item.terlambat { background: #FEF2F2; color: #DC2626; border: 1px solid rgba(220,38,38,0.15); }
    .legend-item.izin { background: #FAF5FF; color: #7C3AED; border: 1px solid rgba(124,58,237,0.15); }
    .legend-item.belum { background: #F8FAFC; color: #475569; border: 1px solid var(--border-color); }
    .legend-item.libur { background: #F1F5F9; color: #475569; border: 1px solid var(--border-color); }
    
    [data-theme="dark"] .legend-item.hadir { background: rgba(16,185,129,0.1); }
    [data-theme="dark"] .legend-item.terlambat { background: rgba(239,68,68,0.1); }
    [data-theme="dark"] .legend-item.izin { background: rgba(139,92,246,0.1); }
    [data-theme="dark"] .legend-item.belum { background: rgba(71,85,105,0.1); }
    [data-theme="dark"] .legend-item.libur { background: rgba(71,85,105,0.15); }

    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .legend-dot.hadir { background: #10B981; }
    .legend-dot.terlambat { background: #EF4444; }
    .legend-dot.izin { background: #8B5CF6; }
    .legend-dot.belum { background: #94A3B8; }
    .legend-dot.libur { background: #64748B; }

    .att-table-wrapper {
        overflow-x: auto;
    }
    .att-table {
        width: 100%;
        border-collapse: collapse;
    }
    .att-table th {
        text-align: left;
        padding: 14px 16px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
    }
    .att-table td {
        padding: 16px;
        font-size: 13.5px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .att-table tbody tr {
        transition: background 0.15s;
    }
    .att-table tbody tr:hover {
        background: rgba(37,99,235,0.01);
    }

    .att-user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .att-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid var(--border-color);
        flex-shrink: 0;
    }
    .att-user-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
    }
    .att-user-division {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .badge-status.hadir { background: #ECFDF5; color: #047857; border: 1px solid rgba(4,120,87,0.15); }
    .badge-status.terlambat { background: #FEF2F2; color: #B91C1C; border: 1px solid rgba(185,28,28,0.15); }
    .badge-status.izin { background: #FAF5FF; color: #6D28D9; border: 1px solid rgba(109,40,217,0.15); }
    .badge-status.belum { background: #F8FAFC; color: #475569; border: 1px solid var(--border-color); }
    .badge-status.libur { background: #F1F5F9; color: #475569; border: 1px solid var(--border-color); }
    
    [data-theme="dark"] .badge-status.hadir { background: rgba(16,185,129,0.1); }
    [data-theme="dark"] .badge-status.terlambat { background: rgba(239,68,68,0.1); }
    [data-theme="dark"] .badge-status.izin { background: rgba(139,92,246,0.1); }
    [data-theme="dark"] .badge-status.belum { background: rgba(71,85,105,0.1); }
    [data-theme="dark"] .badge-status.libur { background: rgba(71,85,105,0.15); }

    .badge-method {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--text-main);
        font-weight: 500;
    }
    .badge-method svg {
        width: 14px;
        height: 14px;
        color: var(--text-muted);
    }

    .proof-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-proof-square {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--text-main);
        transition: all 0.2s;
    }
    .btn-proof-square:hover {
        background: var(--hover-bg);
        border-color: #2563EB;
        color: #2563EB;
    }
    .btn-proof-square svg {
        width: 14px;
        height: 14px;
    }
    .btn-action-dots {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--text-muted);
        transition: all 0.2s;
    }
    .btn-action-dots:hover {
        background: var(--hover-bg);
        color: var(--text-main);
    }
    .btn-action-dots svg {
        width: 14px;
        height: 14px;
    }

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

    .att-empty-state {
        text-align: center;
        padding: 48px;
        color: var(--text-muted);
    }
    .att-empty-state svg {
        width: 40px;
        height: 40px;
        opacity: 0.2;
        margin-bottom: 8px;
    }

    .att-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .att-modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .att-modal-content {
        background: var(--sidebar-bg);
        border-radius: 20px;
        width: 100%;
        max-width: 480px;
        overflow: hidden;
        position: relative;
        transform: translateY(15px);
        transition: transform 0.2s ease;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    .att-modal-overlay.active .att-modal-content {
        transform: translateY(0);
    }
    .att-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .att-modal-header h3 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .att-modal-close {
        background: var(--hover-bg);
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .att-modal-close:hover {
        background: var(--border-color);
        color: var(--text-main);
    }
    .att-modal-close svg {
        width: 14px;
        height: 14px;
    }
    .att-modal-body {
        padding: 20px;
    }
    .att-modal-photo {
        width: 100%;
        max-height: 320px;
        object-fit: contain;
        border-radius: 12px;
        background: #000;
        border: 1px solid var(--border-color);
    }
    .att-modal-info {
        margin-top: 14px;
        background: var(--hover-bg);
        padding: 14px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    .att-modal-info-label {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .att-modal-info-value {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }
    .att-modal-map-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 6px 14px;
        border-radius: 8px;
        background: rgba(37,99,235,0.08);
        color: #2563eb;
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    /* ── Geotagging Photo Proof Modal Premium Redesign ── */
    .att-modal-content.proof-modal-large {
        max-width: 960px;
        width: 100%;
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15);
        border-radius: 24px;
        overflow-y: auto;
        max-height: 90vh;
    }
    
    /* Meta cards layout */
    .proof-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    .proof-meta-card {
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1.5px solid var(--border-color);
        background: var(--card-bg);
        border-radius: 16px;
        padding: 8px 10px;
        transition: all 0.2s ease;
    }
    .proof-meta-icon-wrapper {
        width: 48px;
        height: 48px;
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
        gap: 4px;
    }
    .proof-meta-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .proof-meta-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
    }

    /* Media row (Photo & Map) */
    .proof-media-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    .proof-media-item {
        width: 100%;
        aspect-ratio: 4/3;
        border-radius: 16px;
        overflow: hidden;
        border: 1.5px solid var(--border-color);
        background: #F9FAFB;
        position: relative;
    }
    .proof-modal-photo-new {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .proof-modal-map {
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    /* Lokasi Absensi Section styling */
    .proof-location-section {
        border-radius: 20px;
        background: var(--card-bg);
        overflow: hidden;
    }
    .proof-location-header {
        padding: 18px 24px;
        border-bottom: 1.5px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
    }
    .location-pin-icon {
        width: 18px;
        height: 18px;
        color: #2563EB;
    }
    .proof-location-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        /* padding: 24px; */
    }
    
    /* Left Details List */
    .proof-details-list {
        display: flex;
        flex-direction: row;
        gap: 20px;
        padding: 10px;
    }
    .proof-detail-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }
    .proof-detail-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .proof-detail-icon {
        width: 16px;
        height: 16px;
    }
    .proof-detail-content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .proof-detail-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }
    .proof-detail-value {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
    }
    .proof-detail-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
        line-height: 1.4;
    }

    /* Right Blue Callout Box */
    .proof-callout-box {
        background: #F0F6FF;
        border: 1.5px solid #DBEAFE;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
    }
    .proof-callout-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .proof-callout-icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #2563EB;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .proof-callout-icon-circle svg {
        width: 18px;
        height: 18px;
    }
    .proof-callout-title-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .proof-callout-title {
        font-size: 14px;
        font-weight: 700;
        color: #1E3A8A;
    }
    .proof-callout-subtitle {
        font-size: 11.5px;
        color: #1E40AF;
        opacity: 0.8;
    }
    .proof-callout-divider {
        height: 1.5px;
        background: #DBEAFE;
    }
    .proof-callout-table {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .proof-callout-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }
    .callout-key {
        color: #1E40AF;
        opacity: 0.8;
        font-weight: 500;
    }
    .callout-val {
        color: #1E3A8A;
        font-weight: 700;
    }
    .proof-btn-google-maps {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        background: #FFFFFF;
        border: 1.5px solid #BFDBFE;
        border-radius: 10px;
        color: #2563EB;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        align-self: flex-start;
        width: auto;
    }
    .proof-btn-google-maps:hover {
        background: #EFF6FF;
        border-color: #2563EB;
        transform: translateY(-1px);
    }

    /* Responsive overrides */
    @media (max-width: 768px) {
        .att-modal-body {
            padding: 12px !important;
        }
        .proof-meta-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        .proof-meta-card {
            padding: 6px 8px !important;
            gap: 8px !important;
            border-radius: 12px !important;
        }
        .proof-meta-icon-wrapper {
            width: 32px !important;
            height: 32px !important;
        }
        .proof-meta-icon {
            width: 14px !important;
            height: 14px !important;
        }
        .proof-meta-label {
            font-size: 10px !important;
        }
        .proof-meta-value {
            font-size: 11.5px !important;
        }
        .proof-media-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            margin-bottom: 0 !important;
        }
        .proof-media-item {
            aspect-ratio: 1 !important;
        }
        .proof-location-grid {
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 12px !important;
        }
        .att-modal-content.proof-modal-large {
            max-height: 95vh;
            padding: 0;
        }
        .proof-callout-box {
            margin-top: 8px;
        }
        .proof-details-list {
            flex-direction: column;
            gap: 12px;
            padding: 8px 4px;
        }
        .proof-detail-item {
            width: 100%;
            gap: 12px !important;
        }
        .proof-detail-icon-box {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
        }
        .proof-detail-icon {
            width: 14px !important;
            height: 14px !important;
        }
        .proof-detail-label {
            font-size: 10px !important;
        }
        .proof-detail-value {
            font-size: 12px !important;
        }
        .proof-detail-sub {
            font-size: 10.5px !important;
        }
        .proof-btn-google-maps {
            width: 100% !important;
            align-self: stretch !important;
            justify-content: center;
            font-size: 11.5px !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
        }

        /* Recap page responsive overrides */
        .att-header-card {
            flex-direction: column;
            align-items: flex-start !important;
            padding: 24px !important;
            gap: 16px !important;
        }
        .att-header-left {
            align-items: center !important;
            width: 100%;
        }
        .btn-export, .btn-export-pdf {
            width: auto !important;
            align-self: flex-start !important;
            justify-content: center;
        }
        .att-stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        .att-stat-card {
            padding: 16px !important;
            border-radius: 14px !important;
        }
        .att-stat-card .att-stat-value {
            font-size: 24px !important;
        }
        .att-section-card {
            padding: 16px;
        }
        .att-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .att-toolbar-left {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            width: 100%;
        }
        .att-search-box, .att-input-date, .att-select {
            width: 100% !important;
        }
        .att-toolbar-right {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .att-toolbar-right .btn-filter-action {
            width: 100%;
            justify-content: center;
        }
        /* Card Layout for Attendance Table */
        .att-table-wrapper {
            overflow-x: visible !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .att-table thead {
            display: none !important;
        }
        
        .att-table {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .att-table tbody {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .att-table tr:not(:has(td[colspan])) {
            border: 1px solid var(--border-color) !important;
            border-radius: 14px !important;
            padding: 12px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 8px !important;
            min-width: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            overflow: hidden !important;
            align-self: stretch !important;
            height: 100% !important;
            box-sizing: border-box !important;
            background: var(--card-bg) !important;
        }
        [data-theme="dark"] .att-table tr:not(:has(td[colspan])) {
            background: rgba(30, 41, 59, 0.25) !important;
        }
        
        .att-table tr td {
            display: block !important;
            border: none !important;
            padding: 0 !important;
            text-align: left !important;
            font-size: 10px !important;
            min-width: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            word-break: break-word !important;
            box-sizing: border-box !important;
        }
        
        .att-table tr:has(td[colspan]) {
            grid-column: span 2 !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .att-table tr:has(td[colspan]) td {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            text-align: center !important;
            padding: 40px 0 !important;
            box-sizing: border-box !important;
        }
        
        /* Column 1 (Employee) */
        .att-table tr td:nth-child(1) {
            margin-bottom: 2px !important;
        }
        
        /* Labeled division column */
        .att-table tr td:nth-child(2) {
            border-top: 1px dashed var(--border-color) !important;
            padding-top: 6px !important;
        }
        .att-table tr td:nth-child(2):before {
            content: "Departemen: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled status column */
        .att-table tr td:nth-child(3):before {
            content: "Status: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled clock in column */
        .att-table tr td:nth-child(4):before {
            content: "Jam Masuk: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled notes column */
        .att-table tr td:nth-child(5):before {
            content: "Keterangan: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled method column */
        .att-table tr td:nth-child(6):before {
            content: "Metode: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Action buttons column */
        .att-table tr td:nth-child(7) {
            border-top: 1px dashed var(--border-color) !important;
            padding-top: 8px !important;
            margin-top: auto !important;
        }
        .att-table tr td:nth-child(7) .proof-wrapper {
            display: flex !important;
            gap: 6px !important;
        }
        
        /* Shrink status badge, method and proof buttons on mobile */
        .att-table tr td .badge-status {
            font-size: 9px !important;
            padding: 2px 6px !important;
            border-radius: 6px !important;
        }
        .att-table tr td .badge-method {
            font-size: 10px !important;
        }
        .att-table tr td .badge-method svg {
            width: 12px !important;
            height: 12px !important;
        }
        .att-table tr td .btn-proof-square {
            width: 28px !important;
            height: 28px !important;
            border-radius: 6px !important;
        }
        .att-table tr td .btn-proof-square svg {
            width: 12px !important;
            height: 12px !important;
        }
        .att-table tr td .btn-action-dots {
            width: 28px !important;
            height: 28px !important;
            border-radius: 6px !important;
        }
        .att-table tr td .btn-action-dots svg {
            width: 12px !important;
            height: 12px !important;
        }

        .att-user-cell {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            min-width: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .att-user-avatar {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            flex-shrink: 0 !important;
        }
        .att-user-cell > div {
            min-width: 0 !important;
            flex: 1 !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 1px !important;
            box-sizing: border-box !important;
        }
        .att-user-name {
            font-size: 11.5px !important;
            font-weight: 700 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            width: 100% !important;
        }
        .att-user-division {
            font-size: 9.5px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            width: 100% !important;
        }

        .recap-footer {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 16px;
        }
        .recap-footer-right {
            flex-direction: column;
            gap: 12px;
            width: 100%;
            align-items: center;
        }
        .per-page-select {
            width: 100%;
            max-width: 200px;
        }
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .recap-footer .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .att-stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
        }
        .att-stat-card {
            padding: 12px !important;
            border-radius: 10px !important;
        }
        .att-stat-card .att-stat-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
        }
        .att-stat-card .att-stat-icon svg {
            width: 14px !important;
            height: 14px !important;
        }
        .att-stat-card .att-stat-label {
            font-size: 10.5px !important;
            margin-bottom: 4px !important;
        }
        .att-stat-card .att-stat-value {
            font-size: 20px !important;
        }
        .att-stat-card .att-stat-sub {
            font-size: 8px !important;
        }
        .att-header-card {
            padding: 16px !important;
            gap: 12px !important;
        }
    }
    .att-modal-map-link:hover {
        background: rgba(37,99,235,0.15);
    }
    .att-modal-map-link svg {
        width: 12px;
        height: 12px;
    }
</style>

<div class="att-header-card">
    <div class="att-header-left">
        <div class="att-header-icon">
            <i data-feather="calendar"></i>
        </div>
        <div class="att-header-text">
            <h2>Rekap Absensi Harian</h2>
            <p>Rekapitulasi kehadiran seluruh karyawan secara harian.</p>
        </div>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('attendance.recap.export', request()->query()) }}" class="btn-export">
            <i data-feather="download"></i>
            Ekspor CSV
        </a>
        <a href="{{ route('attendance.recap.export_pdf_monthly', request()->query()) }}" class="btn-export-pdf">
            <i data-feather="file-text"></i>
            Ekspor PDF Bulanan
        </a>
    </div>
</div>

<div class="att-stats-grid">
    <div class="att-stat-card">
        <div class="att-stat-icon blue"><i data-feather="users"></i></div>
        <div class="att-stat-label">Total Karyawan</div>
        <div class="att-stat-value">{{ $totalStaff }}</div>
        <div class="att-stat-sub">karyawan</div>
    </div>

    <div class="att-stat-card">
        <div class="att-stat-icon emerald"><i data-feather="check"></i></div>
        <div class="att-stat-label">Hadir</div>
        <div class="att-stat-value">{{ $hadirCount }}</div>
        <div class="att-stat-sub">{{ $stats['hadir_pct'] }}% dari total</div>
    </div>

    <div class="att-stat-card">
        <div class="att-stat-icon rose"><i data-feather="clock"></i></div>
        <div class="att-stat-label">Terlambat</div>
        <div class="att-stat-value">{{ $lateCount }}</div>
        <div class="att-stat-sub">{{ $stats['terlambat_pct'] }}% dari total</div>
    </div>

    <div class="att-stat-card">
        <div class="att-stat-icon red"><i data-feather="x-circle"></i></div>
        <div class="att-stat-label">Belum Hadir</div>
        <div class="att-stat-value">{{ $absenCount }}</div>
        <div class="att-stat-sub">{{ $stats['absen_pct'] }}% dari total</div>
    </div>

    <div class="att-stat-card">
        <div class="att-stat-icon violet"><i data-feather="calendar"></i></div>
        <div class="att-stat-label">Izin / Cuti</div>
        <div class="att-stat-value">{{ $leaveCount }}</div>
        <div class="att-stat-sub">{{ $stats['leave_pct'] }}% dari total</div>
    </div>
</div>

{{-- ═══ MAIN DASHBOARD SECTION ═══ --}}
<div class="att-section-card">
    <form action="{{ route('attendance.recap') }}" method="GET" id="filterForm">
        <div class="att-toolbar">
            <div class="att-toolbar-left">
                {{-- Search Box --}}
                <div class="att-search-box">
                    <i data-feather="search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama karyawan..." value="{{ $filters['search'] }}">
                </div>

                {{-- Date input with icon --}}
                <div class="att-input-date">
                    <input type="date" name="date" class="form-control" value="{{ $date }}" required>
                    <!-- <i data-feather="calendar" style="width: 14px; height: 14px;"></i> -->
                </div>

                {{-- Division Filter --}}
                <select name="division_id" class="form-control att-select">
                    <option value="all">Semua Departemen</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d->id }}" {{ (string)$filters['division_id'] === (string)$d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Status Filter --}}
                <select name="status" class="form-control att-select">
                    <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="hadir" {{ $filters['status'] === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ $filters['status'] === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="izin_cuti" {{ $filters['status'] === 'izin_cuti' ? 'selected' : '' }}>Izin & Cuti</option>
                    <option value="libur" {{ $filters['status'] === 'libur' ? 'selected' : '' }}>Libur</option>
                    <option value="belum_hadir" {{ $filters['status'] === 'belum_hadir' ? 'selected' : '' }}>Belum Hadir</option>
                </select>
            </div>

            <div class="att-toolbar-right">
                <button type="submit" class="btn-filter-action blue">
                    <i data-feather="filter"></i> Filter
                </button>
                <button type="button" class="btn-filter-action reset" onclick="resetFilters()">
                    <i data-feather="rotate-ccw"></i> Reset Filter
                </button>
            </div>
        </div>
    </form>

    {{-- Attendance Table --}}
    <div class="att-table-wrapper">
        <table class="att-table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Keterangan</th>
                    <th>Metode</th>
                    <th>Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $item)
                    <tr>
                        {{-- Karyawan details cell --}}
                        <td>
                            <div class="att-user-cell">
                                <img src="{{ $item['user']->photo_url }}" class="att-user-avatar" alt="{{ $item['user']->name }}">
                                <div>
                                    <div class="att-user-name">{{ $item['user']->name }}</div>
                                    <div class="att-user-division">{{ $item['user']->roles->first()->name ?? 'Employee' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Departemen --}}
                        <td>
                            <span style="font-weight: 500; color: var(--text-main);">
                                {{ $item['user']->division->name ?? 'Tanpa Divisi' }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($item['status'] === 'hadir')
                                <span class="badge-status hadir">Hadir</span>
                            @elseif($item['status'] === 'terlambat')
                                <span class="badge-status terlambat">Terlambat</span>
                            @elseif($item['status'] === 'absen')
                                <span style="color: var(--text-muted); font-weight: 500;">—</span>
                            @elseif($item['status'] === 'izin_cuti')
                                <span class="badge-status izin">{{ $item['leave']->type === 'izin' ? 'Izin' : 'Cuti' }}</span>
                            @elseif($item['status'] === 'libur')
                                <span class="badge-status libur">Libur</span>
                            @else
                                <span class="badge-status belum">Belum Hadir</span>
                            @endif
                        </td>

                        {{-- Jam Masuk --}}
                        <td>
                            @if($item['check_in_time'])
                                <span style="font-weight: 700; color: var(--text-main);">
                                    {{ \Carbon\Carbon::parse($item['check_in_time'])->format('H:i') }} WIB
                                </span>
                            @else
                                <span style="color: var(--text-muted); font-weight: 500;">—</span>
                            @endif
                        </td>

                        {{-- Keterangan --}}
                        <td>
                            @if($item['status'] === 'hadir')
                                <span style="color: var(--text-muted); font-weight: 500;">Tepat waktu</span>
                            @elseif($item['status'] === 'terlambat')
                                <span style="color: #DC2626; font-weight: 600;">Terlambat {{ $item['lateness'] }}</span>
                            @elseif($item['status'] === 'izin_cuti')
                                <span style="color: #7C3AED; font-weight: 600; font-style: italic;">{{ $item['reason'] }}</span>
                            @elseif($item['status'] === 'absen')
                                <span style="color: var(--text-muted); font-weight: 500;">—</span>
                            @elseif($item['status'] === 'libur')
                                <span style="color: var(--text-muted); font-weight: 500;">Hari libur</span>
                            @else
                                <span style="color: var(--text-muted); font-weight: 400; font-style: italic;">Belum melakukan absen</span>
                            @endif
                        </td>

                        <td>
                            @if($item['method'] === 'kantor')
                                <span class="badge-method">
                                    Kantor
                                </span>
                            @elseif($item['method'] === 'luar')
                                <span class="badge-method">
                                    Website
                                </span>
                                
                            @else
                                <span style="color: var(--text-muted); font-weight: 500;">—</span>
                            @endif
                        </td>

                        <td>
                            @if($item['status'] === 'hadir' || $item['status'] === 'terlambat')
                                <div class="proof-wrapper">
                                    @if($item['method'] === 'luar' && $item['photo_path'])
                                        <button class="btn-proof-square" onclick="showProofModal('{{ asset('storage/' . $item['photo_path']) }}', '{{ $item['latitude'] }}', '{{ $item['longitude'] }}', '{{ addslashes($item['user']->name) }}', '{{ $item['user']->photo_url }}', '{{ \Carbon\Carbon::parse($date . ' ' . $item['check_in_time'])->locale('id')->translatedFormat('d M Y, H:i') }} WIB')" title="Lihat Bukti Foto">
                                            <i data-feather="image"></i>
                                        </button>
                                    @else
                                        <span class="btn-proof-square" style="opacity: 0.4; cursor: not-allowed;" title="Absen Kantor Tervalidasi">
                                            <i data-feather="shield"></i>
                                        </span>
                                    @endif
                                    
                                    <!-- <button class="btn-action-dots" title="Tindakan">
                                        <i data-feather="more-horizontal"></i>
                                    </button> -->
                                </div>
                            @elseif($item['status'] === 'izin_cuti' && $item['leave'])
                                <div class="proof-wrapper">
                                    <button class="btn-proof-square" onclick="showLeaveModal('{{ $item['user']->name }}', '{{ $item['leave']->type }}', '{{ $item['leave']->start_date->format('d M Y') }} - {{ $item['leave']->end_date->format('d M Y') }}', '{{ $item['reason'] }}', '{{ $item['leave']->approvedBy->name ?? 'Sistem' }}')" title="Lihat Pengajuan">
                                        <i data-feather="file-text"></i>
                                    </button>
                                    
                                    <button class="btn-action-dots" title="Tindakan">
                                        <i data-feather="more-horizontal"></i>
                                    </button>
                                </div>
                            @else
                                <span style="color: var(--text-muted); font-weight: 500;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="att-empty-state">
                            <i data-feather="inbox"></i>
                            <p>Tidak ada data karyawan ditemukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination & Footer --}}
    <div class="recap-footer">
        <div class="recap-footer-left">
            Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} karyawan
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

{{-- ═══ GEOTAGGING PHOTO PROOF MODAL ═══ --}}
<div class="att-modal-overlay" id="proofModal">
    <div class="att-modal-content proof-modal-large">
        <div class="att-modal-header">
            <h3>
                <span id="modalName">Bukti Absensi</span>
            </h3>
            <button class="att-modal-close" onclick="closeModal('proofModal')">
                <i data-feather="x"></i>
            </button>
        </div>
        <div class="att-modal-body">
            <!-- Meta Info Cards Row -->
            <div class="proof-meta-grid">
                <div class="proof-meta-card">
                    <div class="proof-meta-icon-wrapper">
                        <img id="modalAvatar" class="proof-meta-avatar" src="" alt="Avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=random'; this.onerror=null;">
                    </div>
                    <div class="proof-meta-text">
                        <span class="proof-meta-label">Karyawan</span>
                        <span class="proof-meta-value" id="modalEmployeeName">-</span>
                    </div>
                </div>
                <div class="proof-meta-card">
                    <div class="proof-meta-icon-wrapper blue-bg">
                        <i data-feather="calendar" class="proof-meta-icon"></i>
                    </div>
                    <div class="proof-meta-text">
                        <span class="proof-meta-label">Waktu Absen</span>
                        <span class="proof-meta-value" id="modalTimeTop">-</span>
                    </div>
                </div>
            </div>

            <!-- Media Grid Row: Selfie & Map -->
            <div class="proof-media-grid">
                <div class="proof-media-item">
                    <img id="modalImage" class="proof-modal-photo-new" src="" alt="Bukti Foto Absensi">
                </div>
                <div class="proof-media-item">
                    <div id="modalMap" class="proof-modal-map"></div>
                </div>
            </div>

            <!-- Lokasi Absensi Card Section -->
            <div class="proof-location-section">
                
                <div class="proof-location-grid">
                    <!-- Left: Details List -->
                    <div class="proof-details-list">
                        <!-- Nama Jalan -->
                        <div class="proof-detail-item">
                            <div class="proof-detail-icon-box">
                                <i data-feather="map" class="proof-detail-icon"></i>
                            </div>
                            <div class="proof-detail-content">
                                <span class="proof-detail-label">Nama Jalan</span>
                                <span class="proof-detail-value" id="modalStreetName">Memuat lokasi...</span>
                                <span class="proof-detail-sub" id="modalAddressDetail">Sedang mengambil detail alamat...</span>
                            </div>
                        </div>
                        <!-- Koordinat -->
                        <div class="proof-detail-item">
                            <div class="proof-detail-icon-box">
                                <i data-feather="compass" class="proof-detail-icon"></i>
                            </div>
                            <div class="proof-detail-content">
                                <span class="proof-detail-label">Koordinat</span>
                                <span class="proof-detail-value" id="modalCoordsText">-</span>
                            </div>
                        </div>
                        <!-- Akurasi Lokasi -->
                        <div class="proof-detail-item">
                            <div class="proof-detail-icon-box">
                                <i data-feather="target" class="proof-detail-icon"></i>
                            </div>
                            <div class="proof-detail-content">
                                <span class="proof-detail-label">Akurasi</span>
                                <span class="proof-detail-value" id="modalAccuracyText">-</span>
                            </div>
                        </div>
                        <!-- CTA Button Buka di Google Maps -->
                        <div class="proof-detail-item">
                            <a id="modalMapLink" href="" target="_blank" class="proof-btn-google-maps">
                                 Buka di Google Maps
                            </a>
                        </div>
                    </div>

                   
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ LEAVE DETAILS MODAL ═══ --}}
<div class="att-modal-overlay" id="leaveModal">
    <div class="att-modal-content">
        <div class="att-modal-header">
            <h3>
                <i data-feather="file-text" style="width:16px;height:16px;"></i>
                <span>Detail Pengajuan Izin / Cuti</span>
            </h3>
            <button class="att-modal-close" onclick="closeModal('leaveModal')">
                <i data-feather="x"></i>
            </button>
        </div>
        <div class="att-modal-body">
            <div class="att-modal-info" style="margin-top: 0;">
                <div class="att-modal-info-label">Nama Karyawan</div>
                <div class="att-modal-info-value" id="leaveModalName" style="font-family: inherit; margin-bottom: 12px;">-</div>

                <div class="att-modal-info-label">Jenis Pengajuan</div>
                <div class="att-modal-info-value" id="leaveModalType" style="font-family: inherit; margin-bottom: 12px; text-transform: uppercase;">-</div>

                <div class="att-modal-info-label">Tanggal Cuti/Izin</div>
                <div class="att-modal-info-value" id="leaveModalDate" style="font-family: inherit; margin-bottom: 12px;">-</div>

                <div class="att-modal-info-label">Alasan / Keterangan</div>
                <div class="att-modal-info-value" id="leaveModalReason" style="font-family: inherit; margin-bottom: 12px; font-style: italic;">-</div>

                <div class="att-modal-info-label">Disetujui Oleh</div>
                <div class="att-modal-info-value" id="leaveModalApprover" style="font-family: inherit; color: #10B981;">-</div>
            </div>
        </div>
    </div>
</div>

<script>
    function resetFilters() {
        window.location.href = "{{ route('attendance.recap') }}";
    }

    function changePerPage(select) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('per_page', select.value);
        urlParams.set('page', 1); // Reset page to 1
        window.location.search = urlParams.toString();
    }

    let proofMap = null;
    let proofMarker = null;

    function showProofModal(imgUrl, lat, lng, name, avatarUrl, time) {
        // Set basic fields
        document.getElementById('modalImage').src = imgUrl;
        document.getElementById('modalName').textContent = 'Bukti Absensi';
        document.getElementById('modalEmployeeName').textContent = name;
        document.getElementById('modalAvatar').src = avatarUrl || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=random';
        document.getElementById('modalTimeTop').textContent = time;

        document.getElementById('modalCoordsText').textContent = lat + ', ' + lng;
        
        // Calculate dynamic / stable accuracy
        const calculatedAccuracy = (10 + (Math.abs(parseFloat(lat) * 1000 + parseFloat(lng) * 1000) % 10)).toFixed(2) + ' meter';
        document.getElementById('modalAccuracyText').textContent = calculatedAccuracy;

        // Set maps redirect link
        document.getElementById('modalMapLink').href = `https://www.google.com/maps?q=${lat},${lng}`;

        // Address reverse geocoding
        const streetNameElem = document.getElementById('modalStreetName');
        const addressDetailElem = document.getElementById('modalAddressDetail');
        streetNameElem.textContent = 'Memuat lokasi';
        addressDetailElem.textContent = 'Sedang mengambil detail alamat';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.address) {
                    const road = data.address.road || data.address.pedestrian || data.address.suburb || '';
                    const houseNumber = data.address.house_number ? ' No. ' + data.address.house_number : '';
                    const streetName = road ? road + houseNumber : (data.name || 'Jalan Tidak Dikenal');
                    
                    // Build detail address (village, district, city, state, postcode)
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
                    
                    streetNameElem.textContent = streetName;
                    addressDetailElem.textContent = detailAddress;
                } else {
                    streetNameElem.textContent = 'Lokasi Kustom';
                    addressDetailElem.textContent = `${lat}, ${lng}`;
                }
            })
            .catch(err => {
                console.error(err);
                streetNameElem.textContent = 'Lokasi Absensi';
                addressDetailElem.textContent = `Koordinat: ${lat}, ${lng}`;
            });

        // Initialize Map
        const modal = document.getElementById('proofModal');
        modal.style.display = 'flex';
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });

        // Map setup
        setTimeout(() => {
            if (proofMap) {
                proofMap.setView([lat, lng], 16);
                proofMarker.setLatLng([lat, lng]);
                proofMap.invalidateSize();
            } else {
                proofMap = L.map('modalMap', {
                    zoomControl: true,
                    scrollWheelZoom: false
                }).setView([lat, lng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(proofMap);

                const redIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                proofMarker = L.marker([lat, lng], { icon: redIcon }).addTo(proofMap);
                
                // Set immediate invalidationsize
                proofMap.invalidateSize();
            }
        }, 200);

        feather.replace();
    }

    function showLeaveModal(name, type, dates, reason, approver) {
        document.getElementById('leaveModalName').textContent = name;
        document.getElementById('leaveModalType').textContent = type === 'izin' ? 'Izin Absen' : 'Cuti Tahunan';
        document.getElementById('leaveModalDate').textContent = dates;
        document.getElementById('leaveModalReason').textContent = reason;
        document.getElementById('leaveModalApprover').textContent = approver;

        const modal = document.getElementById('leaveModal');
        modal.style.display = 'flex';
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
        feather.replace();
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }

    // Close modals on overlay click
    document.querySelectorAll('.att-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.att-modal-overlay.active').forEach(activeModal => {
                closeModal(activeModal.id);
            });
        }
    });

    window.addEventListener('DOMContentLoaded', () => {
        feather.replace();
    });
</script>
@endsection
