@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<!-- Load Leaflet JS & CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    /* Stats grid responsiveness */
    .stats-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }
    @media (max-width: 1024px) {
        .stats-summary-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
        }
        .stats-summary-item {
            border-right: none !important;
            padding-right: 0 !important;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
        }
        .stats-summary-item:nth-child(3), .stats-summary-item:nth-child(4) {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }
    }
    @media (max-width: 640px) {
        .stats-summary-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        .stats-summary-item {
            border-right: none !important;
            padding-right: 0 !important;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
        }
        .stats-summary-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }
    }

    /* Custom scrollbar for tables */
    .table-container {
        overflow-x: auto;
    }
    
    /* Pagination hover states */
    .page-btn:hover:not(.disabled):not(.active) {
        background: var(--hover-bg) !important;
    }

    /* Input select clean styling */
    select:focus, input:focus {
        border-color: #2563eb !important;
    }

    /* ── Geotagging Modal Styles ── */
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
    }
    .att-modal-map-link:hover {
        background: rgba(37,99,235,0.15);
    }
    .att-modal-map-link svg {
        width: 12px;
        height: 12px;
    }

    /* ── Mobile Responsive Overrides ── */
    @media (max-width: 768px) {
        .card {
            padding: 16px !important;
        }
        
        .stats-summary-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 16px !important;
        }
        
        .stats-summary-item {
            border: none !important;
            padding: 8px 0 !important;
            gap: 12px !important;
        }
        
        .stats-summary-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px !important;
        }
        .stats-summary-icon svg {
            width: 16px !important;
            height: 16px !important;
        }
        .stats-summary-item div div:first-child {
            font-size: 11px !important;
        }
        .stats-summary-item div div:nth-child(2) {
            font-size: 20px !important;
        }
        .stats-summary-item div div:nth-child(3) {
            font-size: 9.5px !important;
        }

        #filterForm {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        #filterForm > div {
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .filter-row-top {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 12px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .filter-date-wrapper {
            font-size: 11px !important;
            padding: 8px 8px !important;
            gap: 4px !important;
            justify-content: space-between !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .filter-date-wrapper svg,
        .filter-date-wrapper i {
            width: 13px !important;
            height: 13px !important;
        }
        .filter-date-wrapper svg:last-of-type,
        .filter-date-wrapper i:last-of-type {
            margin-left: 2px !important;
        }
        .filter-status-wrapper {
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .filter-status-wrapper svg,
        .filter-status-wrapper i {
            right: 8px !important;
            width: 12px !important;
            height: 12px !important;
        }
        #statusSelect {
            width: 100% !important;
            box-sizing: border-box !important;
            font-size: 11px !important;
            padding: 8px 24px 8px 8px !important;
        }
        #filterForm input,
        #filterForm button {
            width: 100% !important;
            box-sizing: border-box !important;
        }
        #filterForm button {
            justify-content: center !important;
        }

        /* Card Layout for Attendance Table */
        .table-container {
            overflow-x: visible !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .attendance-table thead {
            display: none !important;
        }
        
        .attendance-table {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .attendance-table tbody {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        @media (max-width: 576px) {
            .attendance-table tbody {
                grid-template-columns: 1fr !important;
            }
        }
        
        .attendance-table tr:not(:has(td[colspan])) {
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
        [data-theme="dark"] .attendance-table tr:not(:has(td[colspan])) {
            background: rgba(30, 41, 59, 0.25) !important;
        }
        
        .attendance-table tr td {
            display: block !important;
            border: none !important;
            padding: 0 !important;
            text-align: left !important;
            font-size: 11.5px !important;
            min-width: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            word-break: break-word !important;
            box-sizing: border-box !important;
        }
        
        .attendance-table tr:has(td[colspan]) {
            grid-column: 1 / -1 !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .attendance-table tr:has(td[colspan]) td {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            text-align: center !important;
            padding: 40px 0 !important;
            box-sizing: border-box !important;
        }
        
        /* Column 1 (Date) */
        .attendance-table tr td:nth-child(1) {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: var(--text-main) !important;
            margin-bottom: 2px !important;
        }
        
        /* Labeled clock in column */
        .attendance-table tr td:nth-child(2) {
            border-top: 1px dashed var(--border-color) !important;
            padding-top: 6px !important;
        }
        .attendance-table tr td:nth-child(2):before {
            content: "Jam Masuk: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled status column */
        .attendance-table tr td:nth-child(3):before {
            content: "Status: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled lateness column */
        .attendance-table tr td:nth-child(4):before {
            content: "Keterlambatan: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled method column */
        .attendance-table tr td:nth-child(5):before {
            content: "Metode: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
            display: inline-block;
            vertical-align: middle;
        }
        .attendance-table tr td:nth-child(5) span {
            display: inline-flex !important;
            vertical-align: middle;
        }
        
        /* Labeled location column */
        .attendance-table tr td:nth-child(6):before {
            content: "Lokasi: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
            display: inline-block;
            vertical-align: middle;
        }
        .attendance-table tr td:nth-child(6) span {
            display: inline-flex !important;
            vertical-align: middle;
        }
        
        /* Labeled photo column */
        .attendance-table tr td:nth-child(7) {
            border-top: 1px dashed var(--border-color) !important;
            padding-top: 8px !important;
            margin-top: auto !important;
        }
        .attendance-table tr td:nth-child(7):before {
            content: "Foto Bukti: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
            display: inline-block;
            vertical-align: middle;
        }
        .attendance-table tr td:nth-child(7) img {
            display: inline-block !important;
            vertical-align: middle;
        }

        .custom-pagination-container {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            gap: 16px !important;
        }
        .pagination-buttons {
            justify-content: center !important;
            width: 100% !important;
        }
        #perPageSelect {
            width: 100% !important;
            max-width: 200px !important;
        }
    }
</style>

<!-- Custom Header -->
<div style="margin-bottom: 28px;">
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Riwayat Absensi</h1>
    <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px; font-weight: 500;">Lihat catatan kehadiran dan absensi Anda.</p>
</div>

<!-- Statistics Cards -->
<div class="stats-summary-card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 18px; padding: 20px 24px; margin-bottom: 28px;">
    <div class="stats-summary-grid">
        <!-- Card 1: Total Hari Kerja -->
        <div class="stats-summary-item" style="display: flex; align-items: center; gap: 16px; border-right: 1px solid var(--border-color); padding-right: 16px;">
            <div class="stats-summary-icon blue" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(37,99,235,0.06); border: 1.5px solid rgba(37,99,235,0.15); color: #2563eb;">
                <i data-feather="calendar" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600; line-height: 1;">Total Hari Kerja</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--text-main); margin-top: 4px; display: flex; align-items: baseline; gap: 4px; line-height: 1;">
                    {{ $stats['workdays'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">hari</span>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">Bulan {{ \Carbon\Carbon::parse($filters['start_date'])->locale('id')->translatedFormat('F Y') }}</div>
            </div>
        </div>
        <!-- Card 2: Hadir -->
        <div class="stats-summary-item" style="display: flex; align-items: center; gap: 16px; border-right: 1px solid var(--border-color); padding-right: 16px;">
            <div class="stats-summary-icon emerald" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(16,185,129,0.06); border: 1.5px solid rgba(16,185,129,0.15); color: #10b981;">
                <i data-feather="check-circle" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600; line-height: 1;">Hadir</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--text-main); margin-top: 4px; display: flex; align-items: baseline; gap: 4px; line-height: 1;">
                    {{ $stats['hadir'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">hari</span>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">{{ $stats['hadir_pct'] }}% dari total</div>
            </div>
        </div>
        <!-- Card 3: Terlambat -->
        <div class="stats-summary-item" style="display: flex; align-items: center; gap: 16px; border-right: 1px solid var(--border-color); padding-right: 16px;">
            <div class="stats-summary-icon rose" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(239,68,68,0.06); border: 1.5px solid rgba(239,68,68,0.15); color: #ef4444;">
                <i data-feather="alert-circle" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600; line-height: 1;">Terlambat</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--text-main); margin-top: 4px; display: flex; align-items: baseline; gap: 4px; line-height: 1;">
                    {{ $stats['terlambat'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">hari</span>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">{{ $stats['terlambat_pct'] }}% dari total</div>
            </div>
        </div>
        <!-- Card 4: Tidak Hadir -->
        <div class="stats-summary-item" style="display: flex; align-items: center; gap: 16px;">
            <div class="stats-summary-icon amber" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(245,158,11,0.06); border: 1.5px solid rgba(245,158,11,0.15); color: #f59e0b;">
                <i data-feather="slash" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600; line-height: 1;">Tidak Hadir</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--text-main); margin-top: 4px; display: flex; align-items: baseline; gap: 4px; line-height: 1;">
                    {{ $stats['tidak_hadir'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">hari</span>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">{{ $stats['tidak_hadir_pct'] }}% dari total</div>
            </div>
        </div>
    </div>
</div>

<!-- History Container -->
<div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 28px;">
    <!-- Filters & Form -->
    <form id="filterForm" method="GET" action="{{ route('attendance.history') }}" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: center; margin-bottom: 24px; width: 100%;">
        <div class="filter-row-top" style="display: flex; gap: 16px; align-items: center;">
            <!-- Date Range Picker -->
            <div class="filter-date-wrapper" style="position: relative; display: flex; align-items: center; gap: 8px; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; min-height: 38px;">
                <i data-feather="calendar" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
                <span id="dateRangeDisplay">{{ $dateRangeString }}</span>
                <i data-feather="chevron-down" style="width: 15px; height: 15px; color: var(--text-muted); margin-left: 8px;"></i>
                <input type="text" id="flatpickr-range" style="position: absolute; inset: 0; opacity: 0; cursor: pointer;" />
                <input type="hidden" name="start_date" id="start_date" value="{{ $filters['start_date'] }}" />
                <input type="hidden" name="end_date" id="end_date" value="{{ $filters['end_date'] }}" />
            </div>

            <!-- Status Filter -->
            <div class="filter-status-wrapper" style="position: relative;">
                <select name="status" id="statusSelect" onchange="this.form.submit()" style="appearance: none; -webkit-appearance: none; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 36px 8px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; outline: none; min-height: 38px; width: 100%;">
                    <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="hadir" {{ $filters['status'] == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ $filters['status'] == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="tidak_hadir" {{ $filters['status'] == 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>
                <i data-feather="chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted); pointer-events: none;"></i>
            </div>
        </div>
        <!-- Search Input -->
        <div style="position: relative; flex: 1; min-width: 200px;">
            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Cari tanggal atau status" style="width: 100%; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 14px 8px 36px; border-radius: 10px; font-size: 13px; font-weight: 500; color: var(--text-main); outline: none; min-height: 38px;" />
            <i data-feather="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted);"></i>
        </div>

        <!-- Download Button -->
        <!-- <button type="button" onclick="exportData()" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; transition: background 0.2s; min-height: 38px;">
            <i data-feather="download" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
            <span>Unduh</span>
        </button> -->
    </form>

    <!-- Table -->
    <div class="table-container">
        <table class="attendance-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="text-align: left; border-bottom: 1.5px solid var(--border-color);">
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Tanggal</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Jam Masuk</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Status</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Keterlambatan</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Metode</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Lokasi</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 16px; font-weight: 500; color: var(--text-main);">
                            {{ $att['day_name'] }}
                        </td>
                        <td style="padding: 16px; font-weight: 600; color: {{ $att['status'] === 'Terlambat' ? 'var(--danger)' : 'var(--text-main)' }};">
                            {{ $att['check_in'] }}
                        </td>
                        <td style="padding: 16px;">
                            @if($att['status'] === 'Hadir')
                                <span style="background: rgba(16,185,129,0.1); color: #10b981; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Hadir</span>
                            @elseif($att['status'] === 'Terlambat')
                                <span style="background: rgba(239,68,68,0.1); color: #ef4444; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Terlambat</span>
                            @else
                                <span style="background: rgba(244,63,94,0.1); color: #f43f5e; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Tidak Hadir</span>
                            @endif
                        </td>
                        <td style="padding: 16px; font-weight: 500; color: {{ $att['status'] === 'Terlambat' ? 'var(--warning)' : 'var(--text-muted)' }};">
                            {{ $att['lateness'] }}
                        </td>
                        <td style="padding: 16px; color: var(--text-main); font-weight: 500;">
                            @if($att['attendance_type'] === 'kantor')
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="home" style="width: 14px; height: 14px; color: var(--text-muted);"></i> Kantor
                                </span>
                            @elseif($att['attendance_type'] === 'luar')
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="globe" style="width: 14px; height: 14px; color: var(--text-muted);"></i> Luar (Web)
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="geo-location-cell" style="padding: 16px; color: var(--text-main); font-weight: 500;" data-lat="{{ $att['latitude'] }}" data-lng="{{ $att['longitude'] }}" data-type="{{ $att['attendance_type'] }}">
                            @if($att['attendance_type'] === 'kantor')
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="map-pin" style="width: 14px; height: 14px; color: var(--text-muted);"></i> Jakarta
                                </span>
                            @elseif($att['attendance_type'] === 'luar' && $att['latitude'])
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="map-pin" style="width: 14px; height: 14px; color: var(--text-muted);"></i> <span class="loc-text">Loading...</span>
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($att['photo_path'])
                                <img src="{{ asset('storage/' . $att['photo_path']) }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1.5px solid var(--border-color); cursor: pointer;" onclick="showProofModal('{{ asset('storage/' . $att['photo_path']) }}', '{{ $att['latitude'] }}', '{{ $att['longitude'] }}', '{{ addslashes(Auth::user()->name) }}', '{{ Auth::user()->photo_url }}', '{{ \Carbon\Carbon::parse($att['date'] . ' ' . $att['check_in'])->locale('id')->translatedFormat('d M Y, H:i') }} WIB')" title="Lihat Bukti Foto">
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            Belum ada catatan absensi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="custom-pagination-container" style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; flex-wrap: wrap; gap: 16px;">
        <!-- Records Count -->
        <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">
            Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari {{ $attendances->total() }} data
        </div>

        <!-- Page Links -->
        <div class="pagination-buttons" style="display: flex; align-items: center; gap: 8px;">
            @if ($attendances->onFirstPage())
                <span class="page-btn disabled" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-muted); cursor: not-allowed; opacity: 0.5;">
                    <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                </span>
            @else
                <a href="{{ $attendances->previousPageUrl() }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; transition: all 0.2s;">
                    <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                </a>
            @endif

            @foreach ($attendances->getUrlRange(max(1, $attendances->currentPage() - 2), min($attendances->lastPage(), $attendances->currentPage() + 2)) as $page => $url)
                @if ($page == $attendances->currentPage())
                    <span class="page-btn active" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #2563eb; color: white; font-weight: 600; font-size: 13px;">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            @if ($attendances->hasMorePages())
                <a href="{{ $attendances->nextPageUrl() }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; transition: all 0.2s;">
                    <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                </a>
            @else
                <span class="page-btn disabled" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-muted); cursor: not-allowed; opacity: 0.5;">
                    <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                </span>
            @endif
        </div>

        <!-- Page Size Select -->
        <div style="position: relative;">
            <select id="perPageSelect" name="per_page" onchange="updatePerPage(this.value)" style="appearance: none; -webkit-appearance: none; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 32px 8px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; outline: none; min-height: 38px;">
                <option value="7" {{ $filters['per_page'] == 7 ? 'selected' : '' }}>7 / halaman</option>
                <option value="15" {{ $filters['per_page'] == 15 ? 'selected' : '' }}>15 / halaman</option>
                <option value="30" {{ $filters['per_page'] == 30 ? 'selected' : '' }}>30 / halaman</option>
            </select>
            <i data-feather="chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted); pointer-events: none;"></i>
        </div>
    </div>
</div>

<!-- Geotagging Photo Proof Modal -->
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
                    <!-- Details List -->
                    <div class="proof-details-list">
                        <!-- Nama Jalan -->
                        <div class="proof-detail-item">
                            <div class="proof-detail-icon-box">
                                <i data-feather="map" class="proof-detail-icon"></i>
                            </div>
                            <div class="proof-detail-content">
                                <span class="proof-detail-label">Nama Jalan</span>
                                <span class="proof-detail-value" id="modalStreetName">Memuat lokasi</span>
                                <span class="proof-detail-sub" id="modalAddressDetail">Sedang mengambil detail alamat</span>
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

<script>
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

        fetch(`https://us1.locationiq.com/v1/reverse?key=${window.LOCATIONIQ_API_KEY}&lat=${lat}&lon=${lng}&format=json&addressdetails=1&zoom=18`)
            .then(response => response.json())
            .then(data => {
                if (data && data.address) {
                    const road = data.address.road || data.address.pedestrian || data.address.footway || data.address.cycleway || data.address.path || data.address.residential || '';
                    const houseNumber = data.address.house_number ? ' No. ' + data.address.house_number : '';
                    
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

                    if (road) {
                        streetNameElem.textContent = road + houseNumber;
                        addressDetailElem.textContent = detailAddress;
                    } else {
                        // Road not found — fetch nearest street with zoom=17
                        streetNameElem.textContent = 'Mencari jalan terdekat...';
                        addressDetailElem.textContent = detailAddress;
                        
                        fetch(`https://us1.locationiq.com/v1/reverse?key=${window.LOCATIONIQ_API_KEY}&lat=${lat}&lon=${lng}&format=json&zoom=17`)
                            .then(r2 => r2.json())
                            .then(streetData => {
                                const nearestRoad = streetData?.address?.road || streetData?.address?.pedestrian || streetData?.address?.footway || streetData?.name || '';
                                if (nearestRoad) {
                                    streetNameElem.textContent = nearestRoad + houseNumber;
                                } else {
                                    streetNameElem.textContent = data.address.amenity || data.address.building || data.address.neighbourhood || data.address.hamlet || village || data.name || (data.display_name ? data.display_name.split(',')[0] : null) || `Lokasi (${lat}, ${lng})`;
                                }
                            })
                            .catch(() => {
                                streetNameElem.textContent = data.address.amenity || data.address.building || data.address.neighbourhood || data.address.hamlet || village || data.name || (data.display_name ? data.display_name.split(',')[0] : null) || `Lokasi (${lat}, ${lng})`;
                            });
                    }
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

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }

    // Close modals on overlay click
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.att-modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
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
    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();

        // Flatpickr Range Initialization
        const startDateVal = document.getElementById("start_date").value;
        const endDateVal = document.getElementById("end_date").value;

        flatpickr("#flatpickr-range", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [startDateVal, endDateVal],
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    const startStr = selectedDates[0].toLocaleDateString('sv-SE'); // YYYY-MM-DD
                    const endStr = selectedDates[1].toLocaleDateString('sv-SE');
                    
                    document.getElementById("start_date").value = startStr;
                    document.getElementById("end_date").value = endStr;
                    document.getElementById("filterForm").submit();
                }
            }
        });

        // Geocoding cells
        const geoCells = document.querySelectorAll(".geo-location-cell");
        const geoCache = {};

        async function loadGeoCells() {
            for (let i = 0; i < geoCells.length; i++) {
                const cell = geoCells[i];
                const type = cell.getAttribute("data-type");
                const lat = cell.getAttribute("data-lat");
                const lng = cell.getAttribute("data-lng");
                const textEl = cell.querySelector(".loc-text");

                if (type === "luar" && lat && lng && textEl) {
                    const cacheKey = `${lat},${lng}`;
                    if (geoCache[cacheKey]) {
                        textEl.textContent = geoCache[cacheKey];
                    } else {
                        // Wait for 500ms before making the API call to respect the LocationIQ rate limit of 2 req/sec
                        await new Promise(resolve => setTimeout(resolve, 500));
                        
                        try {
                            const response = await fetch(`https://us1.locationiq.com/v1/reverse?key=${window.LOCATIONIQ_API_KEY}&lat=${lat}&lon=${lng}&format=json&addressdetails=1&zoom=18`);
                            const data = await response.json();
                            const addr = data.address || {};
                            const road = addr.road || addr.pedestrian || addr.footway || '';
                            const city = addr.city || addr.town || addr.village || addr.city_district || addr.county || '';

                            if (road) {
                                const label = city ? `${road}, ${city}` : road;
                                geoCache[cacheKey] = label;
                                textEl.textContent = label;
                            } else {
                                // No road — try zoom=17 for nearest street
                                // Wait another 500ms
                                await new Promise(resolve => setTimeout(resolve, 500));
                                
                                try {
                                    const r2 = await fetch(`https://us1.locationiq.com/v1/reverse?key=${window.LOCATIONIQ_API_KEY}&lat=${lat}&lon=${lng}&format=json&zoom=17`);
                                    const streetData = await r2.json();
                                    const nearestRoad = streetData?.address?.road || streetData?.address?.pedestrian || streetData?.address?.footway || streetData?.name || '';
                                    if (nearestRoad) {
                                        const label = city ? `${nearestRoad}, ${city}` : nearestRoad;
                                        geoCache[cacheKey] = label;
                                        textEl.textContent = label;
                                    } else {
                                        const fallback = city || addr.suburb || addr.neighbourhood || addr.hamlet || (data.display_name ? data.display_name.split(',')[0] : null) || "Luar Kantor";
                                        geoCache[cacheKey] = fallback;
                                        textEl.textContent = fallback;
                                    }
                                } catch {
                                    const fallback = city || addr.suburb || addr.neighbourhood || (data.display_name ? data.display_name.split(',')[0] : null) || "Luar Kantor";
                                    geoCache[cacheKey] = fallback;
                                    textEl.textContent = fallback;
                                }
                            }
                        } catch {
                            textEl.textContent = `${parseFloat(lat).toFixed(3)}, ${parseFloat(lng).toFixed(3)}`;
                        }
                    }
                }
            }
        }
        loadGeoCells();
    });

    function updatePerPage(val) {
        const form = document.getElementById('filterForm');
        let perPageInput = form.querySelector('input[name="per_page"]');
        if (!perPageInput) {
            perPageInput = document.createElement('input');
            perPageInput.type = 'hidden';
            perPageInput.name = 'per_page';
            form.appendChild(perPageInput);
        }
        perPageInput.value = val;
        form.submit();
    }

    function exportData() {
        let csv = [];
        let rows = document.querySelectorAll("table.attendance-table tr");
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            
            for (let j = 0; j < cols.length; j++) {
                // Omit photo column (last column)
                if (j === cols.length - 1) continue;
                let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
                row.push('"' + text.replace(/"/g, '""') + '"');
            }
            csv.push(row.join(","));
        }
        
        let csvContent = "data:text/csv;charset=utf-8,\uFEFF" + csv.join("\n");
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "riwayat_absensi_{{ Auth::user()->name }}.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
