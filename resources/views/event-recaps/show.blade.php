@extends('layouts.app')

@section('title', 'Detail Rekapitulasi Event')

@section('content')
<style>
    /* ── Main Container ── */
    .recap-detail-grid {
        display: grid;
        grid-template-columns: 7fr 3fr;
        gap: 24px;
        align-items: start;
    }
    .recap-left-column {
        min-width: 0;
        width: 100%;
    }
    .recap-right-column {
        min-width: 0;
        width: 100%;
    }
    @media (max-width: 1024px) {
        .recap-detail-grid { grid-template-columns: 1fr; }
    }

    /* ── Event Info Header Card ── */
    .event-info-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
    }
    .event-info-left {
        display: flex;
        gap: 20px;
        align-items: center;
        flex: 1;
        min-width: 300px;
    }
    .event-poster {
        width: 90px;
        height: 120px;
        border-radius: 12px;
        background: #1e293b;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .event-details-text h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.5px;
    }
    .event-details-text .organized-by {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 4px;
    }
    .event-meta-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 12px;
    }
    .event-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-muted);
    }
    .event-meta-item svg {
        width: 14px;
        height: 14px;
    }
    .event-info-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
        min-width: 180px;
    }
    @media (max-width: 640px) {
        .event-info-right { align-items: flex-start; }
    }
    .event-status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.15);
        color: #10B981;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pic-badge-card {
        text-align: right;
    }
    @media (max-width: 640px) {
        .pic-badge-card { text-align: left; }
    }
    .pic-badge-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .pic-badge-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
        margin-top: 2px;
    }
    .pic-badge-division {
        font-size: 11.5px;
        color: var(--text-muted);
    }

    /* ── Summary Financial Cards ── */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 992px) {
        .summary-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .summary-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .summary-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .summary-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .summary-card-icon.blue { background: rgba(37,99,235,0.06); color: #2563eb; }
    .summary-card-icon.rose { background: rgba(244,63,94,0.06); color: #f43f5e; }
    .summary-card-icon.emerald { background: rgba(16,185,129,0.06); color: #10b981; }
    .summary-card-icon.amber { background: rgba(245,158,11,0.06); color: #f59e0b; }
    
    .summary-card-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .summary-card-label {
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .summary-card-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin-top: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .summary-card-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* ── Tabs Navigation ── */
    .navigation-tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 24px;
        gap: 8px;
        overflow-x: auto;
    }
    .tab-button {
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-muted);
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        text-decoration: none;
    }
    .tab-button:hover {
        color: var(--text-main);
    }
    .tab-button.active {
        color: #2563EB;
        border-bottom-color: #2563EB;
    }

    /* ── Tables & Filters ── */
    .recap-section {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
        min-width: 0;
        box-sizing: border-box;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
    }
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #2563EB;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-primary{
        text-decoration: none !important;
    }
    .btn-primary:hover {
        background: #1d4ed8;
    }
    .btn-primary svg {
        width: 16px;
        height: 16px;
    }

    .filter-toolbar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .search-input-wrapper {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .search-input-wrapper svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .search-control {
        width: 100%;
        padding: 10px 10px 10px 38px;
        height: 40px;
        border: 1px solid var(--border-color);
        background: var(--input-bg);
        color: var(--text-main);
        border-radius: 10px;
        font-size: 13px;
        outline: none;
    }
    .select-control {
        height: 40px;
        padding: 0 12px;
        border: 1px solid var(--border-color);
        background: var(--input-bg);
        color: var(--text-main);
        border-radius: 10px;
        font-size: 13px;
        outline: none;
        min-width: 140px;
    }

    /* ── Table Styling ── */
    .items-table-wrapper {
        overflow-x: auto;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }
    .items-table th {
        text-align: left;
        padding: 12px 14px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
    }
    .items-table td {
        padding: 14px;
        font-size: 13px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .items-table tbody tr:hover {
        background: rgba(37,99,235,0.01);
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
    }
    .category-badge.konsumsi { background: rgba(37,99,235,0.06); color: #2563eb; border: 1px solid rgba(37,99,235,0.15); }
    .category-badge.transportasi { background: rgba(16,185,129,0.06); color: #10b981; border: 1px solid rgba(16,185,129,0.15); }
    .category-badge.perlengkapan { background: rgba(139,92,246,0.06); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.15); }
    .category-badge.dekorasi { background: rgba(236,72,153,0.06); color: #ec4899; border: 1px solid rgba(236,72,153,0.15); }
    .category-badge.sewa { background: rgba(245,158,11,0.06); color: #f59e0b; border: 1px solid rgba(245,158,11,0.15); }
    .category-badge.operasional { background: rgba(100,116,139,0.06); color: #64748b; border: 1px solid rgba(100,116,139,0.15); }

    .receipt-thumbnail {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .receipt-thumbnail:hover {
        transform: scale(1.05);
    }
    
    .btn-icon {
        background: none;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        padding: 6px;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-icon:hover {
        background: var(--hover-bg);
        color: var(--text-main);
    }
    .btn-icon.delete:hover {
        color: #ef4444;
        border-color: #fca5a5;
        background: #fef2f2;
    }
    [data-theme="dark"] .btn-icon.delete:hover {
        background: rgba(220,38,38,0.1);
    }
    .btn-icon svg {
        width: 14px;
        height: 14px;
    }

    /* ── Sidebar Right Panel ── */
    .recap-panel-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .panel-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 16px;
    }

    /* Donut Chart Visual */
    .donut-chart-container {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        margin: 20px 0;
    }
    .donut-chart-svg {
        transform: rotate(-90deg);
    }
    .donut-chart-center {
        position: absolute;
        text-align: center;
    }
    .donut-chart-pct {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
    }
    .donut-chart-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
    }
    .breakdown-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
        border-top: 1px solid var(--border-color);
        padding-top: 16px;
    }
    .breakdown-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }
    .breakdown-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .breakdown-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .breakdown-dot.blue { background: #2563EB; }
    .breakdown-dot.emerald { background: #10B981; }
    .breakdown-dot.slate { background: var(--border-color); }
    .breakdown-val {
        font-weight: 700;
        color: var(--text-main);
    }

    /* Progress Penyelesaian */
    .progress-bar-wrapper {
        background: var(--hover-bg);
        height: 8px;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 12px;
        margin-bottom: 16px;
    }
    .progress-bar-fill {
        background: #2563EB;
        height: 100%;
        transition: width 0.3s ease;
    }
    .progress-metrics {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .metric-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        color: var(--text-muted);
    }
    .metric-row svg {
        width: 14px;
        height: 14px;
        color: #2563eb;
    }

    /* Catatan System */
    .notice-card {
        background: rgba(37,99,235,0.04);
        border: 1px solid rgba(37,99,235,0.1);
        border-radius: 12px;
        padding: 14px;
        font-size: 12.5px;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 16px;
    }
    .notice-card.warning {
        background: rgba(245,158,11,0.04);
        border-color: rgba(245,158,11,0.15);
    }

    /* CTA Buttons Box */
    .action-button-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .btn-action-block {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 700;
        text-align: center;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        transition: all 0.2s;
    }
    .btn-action-block.blue {
        background: #2563EB;
        color: #fff;
    }
    .btn-action-block.blue:hover {
        background: #1d4ed8;
    }
    .btn-action-block.white {
        background: var(--card-bg);
        border: 1px solid #2563EB;
        color: #2563EB;
    }
    .btn-action-block.white:hover {
        background: var(--hover-bg);
    }

    /* ── Modals ── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 16px;
    }
    .modal-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        width: 100%;
        max-width: 500px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
    }
    .modal-close-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    .modal-close-btn svg {
        width: 20px;
        height: 20px;
    }
    .modal-body {
        padding: 24px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
    }
    .form-group .form-control {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 13.5px;
    }
    .form-group textarea.form-control {
        height: 80px;
        padding: 10px 12px;
        resize: none;
    }
    
    /* File Upload Area */
    .upload-drag-area {
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s;
        margin-bottom: 16px;
    }
    .upload-drag-area:hover {
        border-color: #2563EB;
        background: rgba(37,99,235,0.01);
    }
    .upload-drag-area svg {
        width: 32px;
        height: 32px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .upload-drag-area p {
        font-size: 12.5px;
        color: var(--text-muted);
    }
    .upload-drag-area span {
        font-size: 11px;
        color: var(--text-muted);
        display: block;
        margin-top: 4px;
    }

    /* High-res Image Preview Modal */
    .preview-modal-box {
        max-width: 800px;
    }
    .preview-modal-layout {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 20px;
    }
    @media (max-width: 640px) {
        .preview-modal-layout { grid-template-columns: 1fr; }
    }
    .preview-image-panel {
        background: #0f172a;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 250px;
        overflow: hidden;
    }
    .preview-image-panel img {
        max-width: 100%;
        max-height: 400px;
        object-fit: contain;
    }
    .preview-details-panel {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .preview-detail-row {
        margin-bottom: 12px;
        font-size: 13.5px;
    }
    .preview-detail-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 2px;
    }

    @media (max-width: 768px) {
        .recap-detail-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        
        .event-info-card {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 16px !important;
            gap: 16px !important;
        }
        .event-info-left {
            min-width: 0 !important;
        }
        .event-details-text h2 {
            font-size: 18px !important;
        }
        .event-info-right {
            align-items: flex-start !important;
            min-width: 0 !important;
        }
        .pic-badge-card {
            text-align: left !important;
        }

        .summary-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        .summary-card {
            padding: 12px !important;
            border-radius: 14px !important;
            gap: 10px !important;
        }
        .summary-card-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
        }
        .summary-card-icon svg {
            width: 14px !important;
            height: 14px !important;
        }
        .summary-card-label {
            font-size: 9px !important;
        }
        .summary-card-value {
            font-size: 13.5px !important;
            margin-top: 2px !important;
        }
        .summary-card-sub {
            font-size: 9px !important;
        }
        
        .navigation-tabs {
            gap: 4px !important;
            margin-bottom: 16px !important;
        }
        .tab-button {
            padding: 10px 14px !important;
            font-size: 12.5px !important;
        }

        .recap-section {
            padding: 16px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow-x: hidden !important;
        }
        
        .section-header {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .section-header .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .filter-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .search-input-wrapper {
            width: 100% !important;
        }
        .select-control {
            width: 100% !important;
        }

        /* Card Layout for Expenditures Table */
        .items-table-wrapper {
            overflow-x: visible !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .items-table thead {
            display: none !important;
        }
        
        .items-table {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .items-table tbody {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        .items-table tr:not(:has(td[colspan])) {
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
        [data-theme="dark"] .items-table tr:not(:has(td[colspan])) {
            background: rgba(30, 41, 59, 0.25) !important;
        }
        
        .items-table tr td {
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
        
        .items-table tr:has(td[colspan]) {
            grid-column: 1 / -1 !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .items-table tr:has(td[colspan]) td {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            text-align: center !important;
            padding: 40px 0 !important;
            box-sizing: border-box !important;
        }
        
        /* Column 1 (No) */
        .items-table tr td:nth-child(1):before {
            content: "No: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled date column */
        .items-table tr td:nth-child(2) {
            border-top: 1px dashed var(--border-color) !important;
            padding-top: 6px !important;
        }
        .items-table tr td:nth-child(2):before {
            content: "Tanggal: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled category column */
        .items-table tr td:nth-child(3):before {
            content: "Kategori: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
            display: inline-block;
            vertical-align: middle;
        }
        .items-table tr td:nth-child(3) .category-badge {
            display: inline-flex !important;
            vertical-align: middle;
        }
        
        /* Labeled vendor column */
        .items-table tr td:nth-child(4):before {
            content: "Vendor: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled nominal column */
        .items-table tr td:nth-child(5):before {
            content: "Nominal: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled receipt column */
        .items-table tr td:nth-child(6) {
            padding-bottom: 6px !important;
        }
        .items-table tr td:nth-child(6):before {
            content: "Bukti: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 9px;
            text-transform: uppercase;
            margin-right: 4px;
            display: inline-block;
            vertical-align: middle;
        }
        .items-table tr td:nth-child(6) .receipt-thumbnail {
            display: inline-block !important;
            vertical-align: middle;
        }
        
        /* Action buttons column */
        .items-table tr td:nth-child(7) {
            border-top: 1px dashed var(--border-color) !important;
            padding-top: 8px !important;
            margin-top: auto !important;
        }
        .items-table tr td:nth-child(7) form {
            display: block !important;
            width: 100% !important;
        }
        .items-table tr td:nth-child(7) .btn-icon.delete {
            display: flex !important;
            width: 100% !important;
            justify-content: center;
            box-sizing: border-box !important;
        }

        /* Table Card Layout for Tab Summary */
        .breakdown-table tr td:nth-child(1):before {
            content: "Kategori: ";
        }
        .breakdown-table tr td:nth-child(2):before {
            content: "Transaksi: ";
        }
        .breakdown-table tr td:nth-child(3):before {
            content: "Total Belanja: ";
        }

        /* Sidebar Column Right Adjustments */
        .recap-left-column {
            width: 100% !important;
            min-width: 0 !important;
        }
        .recap-right-column {
            width: 100% !important;
            min-width: 0 !important;
        }
        
        /* Modals mobile bounds */
        .modal-box {
            max-width: 100% !important;
            width: 100% !important;
            border-radius: 16px !important;
        }
        .modal-body {
            padding: 16px !important;
        }
        .preview-modal-box {
            max-width: 95vw !important;
        }
        .preview-modal-layout {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        .preview-image-panel img {
            max-height: 250px !important;
        }
        .btn-action-block {
            padding: 10px !important;
            font-size: 13px !important;
        }
    }

    /* Premium Upload Modal Redesign Styles */
    .upload-modal-box {
        max-width: 520px !important;
        width: 100% !important;
        background: var(--card-bg);
        border-radius: 20px !important;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15) !important;
    }
    .upload-drag-area-new {
        border: 2px dashed rgba(37, 99, 235, 0.15);
        background: rgba(37, 99, 235, 0.02);
        border-radius: 16px;
        padding: 32px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 180px;
        box-sizing: border-box;
    }
    .upload-drag-area-new:hover {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.05);
    }
    .upload-icon-circle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        margin-left: auto;
        margin-right: auto;
    }
    .upload-alert-banner {
        background: rgba(37, 99, 235, 0.04);
        border: 1.5px solid rgba(37, 99, 235, 0.12);
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .upload-alert-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #2563eb;
    }
    .upload-modal-input {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
        color: var(--text-main);
        outline: none;
        background: var(--input-bg);
        box-sizing: border-box;
        transition: border-color 0.2s;
    }
    .upload-modal-input:focus {
        border-color: #2563eb !important;
    }
    .upload-modal-textarea {
        width: 100%;
        height: 100px;
        padding: 12px 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
        color: var(--text-main);
        outline: none;
        background: var(--input-bg);
        resize: none;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }
    .upload-modal-textarea:focus {
        border-color: #2563eb !important;
    }
    .btn-remove-preview {
        background: rgba(239, 68, 68, 0.9);
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-remove-preview:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    
    /* Premium Success/Warning/Danger Alert Modal */
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
        background: var(--card-bg);
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
        margin-bottom: 8px;
    }
    .alert-success-circle svg {
        width: 28px;
        height: 28px;
        stroke-width: 3px;
    }
    .alert-warning-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #FEF3C7;
        border: 2.5px solid #D97706;
        color: #D97706;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }
    .alert-warning-circle svg {
        width: 28px;
        height: 28px;
        stroke-width: 3px;
    }
    .alert-danger-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #FEE2E2;
        border: 2.5px solid #DC2626;
        color: #DC2626;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }
    .alert-danger-circle svg {
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
    }
</style>

{{-- Premium Custom Alert/Confirm Modal --}}
<div id="customConfirmModal" class="alert-modal-overlay">
    <div class="alert-modal-content">
        <div id="confirm-icon-circle" class="alert-success-circle">
            <i id="confirm-icon" data-feather="help-circle"></i>
        </div>
        <h3 class="alert-title" id="confirm-title">Konfirmasi</h3>
        <p class="alert-message" id="confirm-message">Apakah Anda yakin?</p>
        <div style="display: flex; gap: 12px; width: 100%; margin-top: 10px;">
            <button type="button" id="btn-confirm-cancel" class="alert-close-btn" style="background: var(--hover-bg); border: 1px solid var(--border-color); color: var(--text-main); margin-top: 0; box-shadow: none;">Batal</button>
            <button type="button" id="btn-confirm-action" class="alert-close-btn" style="margin-top: 0;">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

{{-- ═══ NOTIFICATION ALERTS ═══ --}}
@if(session('success'))
    <div class="notice-card" style="background: rgba(16,185,129,0.05); border-color: rgba(16,185,129,0.2); color: #047857; margin-bottom: 20px; padding: 14px 18px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
        <i data-feather="check-circle" style="width: 16px; height: 16px;"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="notice-card" style="background: rgba(239,68,68,0.05); border-color: rgba(239,68,68,0.2); color: #b91c1c; margin-bottom: 20px; padding: 14px 18px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
        <i data-feather="alert-circle" style="width: 16px; height: 16px;"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- ═══ BACK BUTTON ═══ --}}
<div style="margin-bottom: 16px;">
    <a href="{{ route('event-recaps.index') }}" class="btn-back">
        <i data-feather="arrow-left"></i> Kembali ke Daftar Rekap
    </a>
</div>

{{-- ═══ 1. EVENT INFO HEADER CARD ═══ --}}
<div class="event-info-card">
    <div class="event-info-left">
        
        <div class="event-details-text">
            <h2>{{ $event->name }}</h2>
            <div class="event-meta-list">
                <div class="event-meta-item">
                    <i data-feather="calendar"></i>
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
                </div>
                <div class="event-meta-item">
                    <i data-feather="map-pin"></i>
                    {{ $event->location ?? 'Belum ditentukan' }}
                </div>
            </div>
        </div>
    </div>
    
    <div class="event-info-right">
        @if($picDetails)
        <div class="pic-badge-card">
            <div class="pic-badge-label">PIC Event</div>
            <div class="pic-badge-name">{{ $picDetails->name }}</div>
            <!-- <div class="pic-badge-division">{{ optional($picDetails->division)->name ?? '-' }}</div> -->
        </div>
        @endif
    </div>
</div>

@php
    $spentPercentage = $recap->initial_nominal > 0 ? min(100, round(($totalSpent / $recap->initial_nominal) * 100, 1)) : 0;
    $remainingPercentage = 100 - $spentPercentage;

    $statusLabel = 'Draft';
    $statusDesc = 'PIC sedang menyusun rekap nota';
    $statusColor = 'blue';

    if ($recap->status === 'dalam_rekap') {
        $statusLabel = 'Dalam Rekap';
        $statusDesc = 'Nota sedang diupload oleh PIC';
        $statusColor = 'blue';
    } elseif ($recap->status === 'menunggu_finance') {
        $statusLabel = 'Menunggu Finance';
        $statusDesc = 'Menunggu verifikasi laporan keuangan';
        $statusColor = 'amber';
    } elseif ($recap->status === 'direvisi') {
        $statusLabel = 'Direvisi';
        $statusDesc = 'Rekap dikembalikan untuk direvisi';
        $statusColor = 'rose';
    } elseif ($recap->status === 'selesai') {
        $statusLabel = 'Selesai';
        $statusDesc = 'Rekapitulasi disetujui & ditutup';
        $statusColor = 'emerald';
    }
@endphp
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-card-icon blue"><i data-feather="dollar-sign"></i></div>
        <div class="summary-card-info" style="flex: 1;">
            <div class="summary-card-label">Total Anggaran</div>
            <div class="summary-card-value">Rp {{ number_format($recap->initial_nominal, 0, ',', '.') }}</div>
        </div>
        @if($isFinance && $recap->status !== 'selesai')
            <button class="btn-icon" onclick="openBudgetModal()" title="Edit Anggaran" style="border-color: #2563eb; color: #2563eb;">
                <i data-feather="edit-2"></i>
            </button>
        @endif
    </div>

    <div class="summary-card">
        <div class="summary-card-icon rose"><i data-feather="shopping-bag"></i></div>
        <div class="summary-card-info">
            <div class="summary-card-label">Total Pengeluaran</div>
            <div class="summary-card-value">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
            <!-- <div class="summary-card-sub">{{ $spentPercentage }}% dari anggaran</div> -->
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-card-icon emerald"><i data-feather="credit-card"></i></div>
        <div class="summary-card-info">
            <div class="summary-card-label">Sisa Anggaran</div>
            <div class="summary-card-value" style="color: {{ $remainingBudget < 0 ? '#ef4444' : 'inherit' }}">
                {{ $remainingBudget < 0 ? '-' : '' }}Rp {{ number_format(abs($remainingBudget), 0, ',', '.') }}
            </div>
            <!-- <div class="summary-card-sub">{{ $remainingPercentage }}% dari anggaran</div> -->
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-card-icon {{ $statusColor }}"><i data-feather="check-square"></i></div>
        <div class="summary-card-info">
            <div class="summary-card-label">Status Rekap</div>
            <div class="summary-card-value" style="font-size: 15px;">{{ $statusLabel }}</div>
            <div class="summary-card-sub">{{ $statusDesc }}</div>
        </div>
    </div>
</div>

<div class="navigation-tabs">
    <a href="?tab=recap" class="tab-button {{ $activeTab === 'recap' ? 'active' : '' }}">Rekapitulasi Pengeluaran</a>
    <a href="?tab=summary" class="tab-button {{ $activeTab === 'summary' ? 'active' : '' }}">Ringkasan</a>
    <a href="?tab=history" class="tab-button {{ $activeTab === 'history' ? 'active' : '' }}">Riwayat Rekap</a>
    <a href="?tab=export" class="tab-button {{ $activeTab === 'export' ? 'active' : '' }}">Dokumen Export</a>
</div>

<div class="recap-detail-grid">
    
    <div class="recap-left-column">
        
        @if($activeTab === 'recap')
        <div class="recap-section">
            <div class="section-header">
                <div class="section-title">Daftar Pengeluaran</div>
                @if($isPic && in_array($recap->status, ['draft', 'dalam_rekap', 'direvisi']))
                    <button class="btn-primary" onclick="openUploadModal()">
                        <i data-feather="plus"></i> Tambah Pengeluaran
                    </button>
                @endif
            </div>

            <form action="{{ url()->current() }}" method="GET">
                <input type="hidden" name="tab" value="recap">
                <div class="filter-toolbar">
                    <div class="search-input-wrapper">
                        <i data-feather="search"></i>
                        <input type="text" name="search_item" class="search-control" placeholder="Cari Keterangan" value="{{ $searchQuery }}">
                    </div>
                    
                    <select name="category" class="select-control" onchange="this.form.submit()">
                        <option value="all" {{ $categoryFilter === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="Konsumsi" {{ $categoryFilter === 'Konsumsi' ? 'selected' : '' }}>Konsumsi</option>
                        <option value="Transportasi" {{ $categoryFilter === 'Transportasi' ? 'selected' : '' }}>Transportasi</option>
                        <option value="Perlengkapan" {{ $categoryFilter === 'Perlengkapan' ? 'selected' : '' }}>Perlengkapan</option>
                        <option value="Dekorasi" {{ $categoryFilter === 'Dekorasi' ? 'selected' : '' }}>Dekorasi</option>
                        <option value="Sewa" {{ $categoryFilter === 'Sewa' ? 'selected' : '' }}>Sewa</option>
                        <option value="Operasional" {{ $categoryFilter === 'Operasional' ? 'selected' : '' }}>Operasional</option>
                    </select>

                    <select name="sort" class="select-control" onchange="this.form.submit()">
                        <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="nominal_desc" {{ $sortBy === 'nominal_desc' ? 'selected' : '' }}>Nominal Terbesar</option>
                        <option value="nominal_asc" {{ $sortBy === 'nominal_asc' ? 'selected' : '' }}>Nominal Terkecil</option>
                    </select>
                </div>
            </form>

            <div class="items-table-wrapper">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Vendor</th>
                            <th>Nominal</th>
                            <th>Bukti</th>
                            @if($isPic && in_array($recap->status, ['draft', 'dalam_rekap', 'direvisi']))
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->date->translatedFormat('d M Y') }}</td>
                            <td>
                                <span class="category-badge {{ strtolower($item->category) }}">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">{{ $item->vendor }}</div>
                                <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">{{ $item->description ?? '-' }}</div>
                            </td>
                            <td style="font-weight: 700; color: var(--text-main);">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td>
                                <img src="{{ asset($item->receipt_path) }}" class="receipt-thumbnail" 
                                     alt="Nota {{ $item->vendor }}" 
                                     onclick="openPreviewModal('{{ asset($item->receipt_path) }}', '{{ number_format($item->nominal, 0, ',', '.') }}', '{{ $item->category }}', '{{ $item->vendor }}', '{{ $item->date->translatedFormat('d F Y') }}', '{{ optional($item->uploader)->name ?? '-' }}')">
                            </td>
                            @if($isPic && in_array($recap->status, ['draft', 'dalam_rekap', 'direvisi']))
                            <td>
                                <form action="{{ route('event-recaps.items.destroy', [$event->id, $item->id]) }}" method="POST" onsubmit="event.preventDefault(); const form = this; showCustomConfirm({
                                    title: 'Hapus Bukti Pengeluaran?',
                                    message: 'Apakah Anda yakin ingin menghapus bukti pengeluaran ini?',
                                    type: 'danger',
                                    confirmText: 'Ya, Hapus',
                                    confirmBg: '#DC2626',
                                    onConfirm: () => form.submit()
                                });">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Hapus Nota">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                <i data-feather="inbox" style="width: 32px; height: 32px; opacity: 0.2; margin-bottom: 8px; display: block; margin-left: auto; margin-right: auto;"></i>
                                <span>Belum ada nota pengeluaran yang diunggah.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- TAB: RINGKASAN ANALYSIS --}}
        @if($activeTab === 'summary')
        <div class="recap-section">
            <h3 class="section-title" style="margin-bottom: 16px;">Analisa Penggunaan Anggaran</h3>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 24px;">
                <div style="border: 1px solid var(--border-color); padding: 18px; border-radius: 12px;">
                    <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Persentase Anggaran Terpakai</div>
                    <div style="font-size: 28px; font-weight: 800; color: #2563EB; margin-top: 6px;">{{ $spentPercentage }}%</div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Tingkat penyerapan alokasi dana awal.</div>
                </div>
                <div style="border: 1px solid var(--border-color); padding: 18px; border-radius: 12px;">
                    <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Sisa Anggaran Efektif</div>
                    <div style="font-size: 28px; font-weight: 800; color: {{ $remainingBudget < 0 ? '#ef4444' : '#10b981' }}; margin-top: 6px;">
                        Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Selisih anggaran yang belum dibelanjakan.</div>
                </div>
            </div>

            <h3 class="section-title" style="margin-bottom: 16px;">Breakdown Berdasarkan Kategori</h3>
            <div class="items-table-wrapper">
                <table class="items-table breakdown-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Nominal Belanja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $catBreakdown = [
                                'Konsumsi' => ['count' => 0, 'sum' => 0],
                                'Transportasi' => ['count' => 0, 'sum' => 0],
                                'Perlengkapan' => ['count' => 0, 'sum' => 0],
                                'Dekorasi' => ['count' => 0, 'sum' => 0],
                                'Sewa' => ['count' => 0, 'sum' => 0],
                                'Operasional' => ['count' => 0, 'sum' => 0],
                            ];
                            foreach($items as $i) {
                                if (isset($catBreakdown[$i->category])) {
                                    $catBreakdown[$i->category]['count']++;
                                    $catBreakdown[$i->category]['sum'] += (float)$i->nominal;
                                }
                            }
                        @endphp
                        @foreach($catBreakdown as $catName => $data)
                        @if($data['count'] > 0)
                        <tr>
                            <td>
                                <span class="category-badge {{ strtolower($catName) }}">{{ $catName }}</span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-main);">{{ $data['count'] }} nota</td>
                            <td style="font-weight: 700; color: var(--text-main);">Rp {{ number_format($data['sum'], 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- TAB: RIWAYAT REKAP --}}
        @if($activeTab === 'history')
        <div class="recap-section">
            <h3 class="section-title" style="margin-bottom: 20px;">Aktivitas & Kronologi Laporan</h3>
            
            <div style="position: relative; padding-left: 28px; border-left: 2px solid var(--border-color); margin-left: 10px; display: flex; flex-direction: column; gap: 24px;">
                @if($recap->completed_at)
                <div>
                    <span style="position: absolute; left: -7px; width: 12px; height: 12px; border-radius: 50%; background: #10b981; border: 2px solid var(--card-bg);"></span>
                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">{{ $recap->completed_at->translatedFormat('d M Y, H:i') }} WIB</div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px; margin-top: 4px;">Rekapitulasi Disetujui</div>
                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">Tim Finance memverifikasi laporan belanja dan menandai status rekapitulasi Selesai. Efisiensi Kecepatan: <strong>{{ $recap->speed_percentage }}%</strong>.</div>
                </div>
                @endif

                @if($recap->status === 'menunggu_finance')
                <div>
                    <span style="position: absolute; left: -7px; width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; border: 2px solid var(--card-bg);"></span>
                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">{{ $recap->updated_at->translatedFormat('d M Y, H:i') }} WIB</div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px; margin-top: 4px;">Menunggu Verifikasi Finance</div>
                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">PIC Event menyelesaikan input nota belanja dan mengirimkan laporan rekapitulasi ke Finance. Halaman terkunci bagi PIC.</div>
                </div>
                @endif

                @if($items->count() > 0)
                <div>
                    <span style="position: absolute; left: -7px; width: 12px; height: 12px; border-radius: 50%; background: #2563EB; border: 2px solid var(--card-bg);"></span>
                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">{{ $items->first()->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px; margin-top: 4px;">Input Nota Aktif</div>
                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">PIC Event mengupload sebanyak <strong>{{ $items->count() }} nota belanja</strong> ke dalam sistem.</div>
                </div>
                @endif

                @if($recap->initial_nominal > 0)
                <div>
                    <span style="position: absolute; left: -7px; width: 12px; height: 12px; border-radius: 50%; background: var(--text-muted); border: 2px solid var(--card-bg);"></span>
                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">{{ $recap->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px; margin-top: 4px;">Anggaran Dialokasikan</div>
                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">Anggaran awal sebesar <strong>Rp {{ number_format($recap->initial_nominal, 0, ',', '.') }}</strong> dialokasikan oleh tim Finance.</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($activeTab === 'export')
        <div class="recap-section">
            <div class="section-header">
                <div class="section-title">Preview Format Excel</div>
                @if($isFinance || $isLeader)
                <a href="{{ route('event-recaps.export', $event->id) }}" class="btn-primary">
                    <i data-feather="download"></i> Download File Excel
                </a>
                @endif
            </div>

            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="padding: 32px; background: #fff; color: #000; font-family: 'Arial', sans-serif; min-width: 800px;">
                    <div style="text-align: center; font-size: 18px; font-weight: 800; letter-spacing: 0.5px;">EVENTRA CORE</div>
                    <div style="text-align: center; font-size: 10px; color: #64748b; margin-top: 4px; border-bottom: 2px solid #000; padding-bottom: 12px;">
                        Jl. Raya Kenangan No. 7, Jakarta Selatan | Telp: (021) 1234567 | Email: finance@eventracore.com
                    </div>
                    
                    <h4 style="text-align: center; font-size: 13px; text-transform: uppercase; font-weight: 700; margin: 20px 0 10px 0;">LAPORAN REKAPITULASI KEUANGAN EVENT</h4>
                    
                    <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 20px;">
                        <tr>
                            <td style="width: 15%; padding: 4px 0; font-weight: bold;">Nama Event</td>
                            <td style="width: 35%; padding: 4px 0;">: {{ $event->name }}</td>
                            <td style="width: 15%; padding: 4px 0; font-weight: bold;">Anggaran Awal</td>
                            <td style="width: 35%; padding: 4px 0;">: Rp {{ number_format($recap->initial_nominal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0; font-weight: bold;">Pelaksana</td>
                            <td style="padding: 4px 0;">: {{ $picDetails ? $picDetails->name : '-' }}</td>
                            <td style="padding: 4px 0; font-weight: bold;">Total Belanja</td>
                            <td style="padding: 4px 0;">: Rp {{ number_format($totalSpent, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0; font-weight: bold;">Tanggal Event</td>
                            <td style="padding: 4px 0;">
                                : @if(!empty($dates))
                                    {{ \Carbon\Carbon::parse($dates[0])->translatedFormat('d M Y') }}
                                    @if(count($dates) > 1) - {{ \Carbon\Carbon::parse(end($dates))->translatedFormat('d M Y') }}@endif
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 4px 0; font-weight: bold;">Sisa Anggaran</td>
                            <td style="padding: 4px 0; font-weight: bold; color: {{ $remainingBudget < 0 ? '#b91c1c' : 'inherit' }}">
                                : {{ $remainingBudget < 0 ? '-' : '' }}Rp {{ number_format(abs($remainingBudget), 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>

                    <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 20px;">
                        <thead>
                            <tr style="background: #f1f5f9;">
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">No</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Tanggal</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Kategori</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Vendor</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Keterangan</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $iIndex => $item)
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $iIndex + 1 }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $item->date->translatedFormat('d/m/Y') }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $item->category }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $item->vendor }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $item->description ?? '-' }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold;">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr style="background: #f8fafc; font-weight: bold;">
                                <td colspan="5" style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Total Pengeluaran:</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Rp {{ number_format($totalSpent, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Right Side Panel (30%) --}}
    <div class="recap-right-column">
        
        {{-- CARD A: RINGKASAN REKAPITULASI (DONUT CHART) --}}
        <div class="recap-panel-card">
            <h3 class="panel-title">Ringkasan Rekapitulasi</h3>
            
            <div class="donut-chart-container">
                <svg width="120" height="120" class="donut-chart-svg">
                    <!-- Circle Background -->
                    <circle cx="60" cy="60" r="40" fill="transparent" stroke="var(--border-color)" stroke-width="12" />
                    <!-- Circle Spent (Blue) -->
                    @php
                        // Circumference = 2 * pi * r = 2 * 3.14159 * 40 = 251.3
                        $circumference = 251.3;
                        $offset = $circumference - ($circumference * min(100, $spentPercentage) / 100);
                    @endphp
                    <circle cx="60" cy="60" r="40" fill="transparent" stroke="#2563EB" stroke-width="12"
                            stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
                            stroke-linecap="round" />
                </svg>
                <div class="donut-chart-center">
                    <div class="donut-chart-pct">{{ $spentPercentage }}%</div>
                    <div class="donut-chart-label">Terpakai</div>
                </div>
            </div>

            <div class="breakdown-list">
                <div class="breakdown-item">
                    <div class="breakdown-label"> Total Pengeluaran</div>
                    <div class="breakdown-val">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-label"> Sisa Anggaran</div>
                    <div class="breakdown-val" style="color: {{ $remainingBudget < 0 ? '#ef4444' : 'inherit' }}">
                        {{ $remainingBudget < 0 ? '-' : '' }}Rp {{ number_format(abs($remainingBudget), 0, ',', '.') }}
                    </div>
                </div>
                <div class="breakdown-item">
                    <div class="breakdown-label"> Total Anggaran</div>
                    <div class="breakdown-val">Rp {{ number_format($recap->initial_nominal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- CARD B: PROGRESS PENYELESAIAN REKAP --}}
        <div class="recap-panel-card">
            <h3 class="panel-title" style="margin-bottom: 4px;">Progress</h3>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 14px;">
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">Skor Penyelesaian</span>
                <strong style="font-size: 20px; font-weight: 800; color: #2563EB;">{{ $completionScore }}%</strong>
            </div>

            <div class="progress-bar-wrapper">
                <div class="progress-bar-fill" style="width: {{ $completionScore }}%;"></div>
            </div>

            <div class="progress-metrics">
                <div class="metric-row">
                    <i data-feather="check-circle"></i>
                    <span>Kelengkapan Nota ({{ $items->count() }} nota)</span>
                </div>
                <div class="metric-row">
                    <i data-feather="clock"></i>
                    @php
                        $daysLateMsg = 'Menunggu penyelesaian';
                        if ($recap->completed_at) {
                            $daysLateMsg = 'Selesai diinput';
                        } else {
                            if (!empty($dates)) {
                                $eventEnd = \Carbon\Carbon::parse(end($dates))->endOfDay();
                                if ($event->end_time) {
                                    $eventEnd = \Carbon\Carbon::parse(end($dates))->setTimeFromTimeString((string) $event->end_time);
                                }
                                if (now() > $eventEnd) {
                                    $days = $eventEnd->diffInDays(now());
                                    $daysLateMsg = $days . ' hari setelah event selesai';
                                } else {
                                    $daysLateMsg = 'Event masih berjalan';
                                }
                            }
                        }
                    @endphp
                    <span>Ketepatan Waktu ({{ $daysLateMsg }})</span>
                </div>
                <div class="metric-row">
                    <i data-feather="shield"></i>
                    <span>Status Validasi ({{ $statusLabel }})</span>
                </div>
            </div>
        </div>

        {{-- CARD D: AKSI REKAP (CONDITIONAL ACCORDING TO ROLE & STATUS) --}}
        <div class="recap-panel-card">
            <h3 class="panel-title">Aksi Rekap</h3>
            
            <div class="action-button-box">
                @if($isPic)
                    @if(in_array($recap->status, ['draft', 'dalam_rekap', 'direvisi']))
                        <form action="{{ route('event-recaps.submit', $event->id) }}" method="POST" onsubmit="event.preventDefault(); const form = this; showCustomConfirm({
                            title: 'Selesaikan Rekapitulasi?',
                            message: 'Apakah Anda yakin ingin menyelesaikan rekapitulasi belanja event ini dan mengirimkannya ke Finance?',
                            type: 'warning',
                            confirmText: 'Ya, Selesaikan',
                            confirmBg: '#2563EB',
                            onConfirm: () => form.submit()
                        });">
                            @csrf
                            <button type="submit" class="btn-action-block blue">
                                 Selesai Rekap
                            </button>
                        </form>
                        <div style="font-size: 11px; color: var(--text-muted); line-height: 1.4; text-align: center; margin-top: 8px;">
                            Mengirim seluruh nota rekapitulasi belanja ke tim Finance untuk diverifikasi.
                        </div>
                    @else
                        <button type="button" class="btn-action-block blue" disabled style="opacity: 0.5; cursor: not-allowed;">
                             Selesai
                        </button>
                        <div style="font-size: 11px; color: var(--text-muted); line-height: 1.4; text-align: center; margin-top: 8px;">
                            Rekapitulasi telah dikirim. Halaman terkunci dari pengeditan atau pengunggahan baru.
                        </div>
                    @endif
                @endif

                @if($isFinance)
                    @if($recap->status === 'menunggu_finance')
                        <form action="{{ route('event-recaps.approve', $event->id) }}" method="POST" style="margin-bottom: 8px;">
                            @csrf
                            <button type="submit" class="btn-action-block blue">
                                 Setuju 
                            </button>
                        </form>
                    @endif
                    
                    @if(in_array($recap->status, ['menunggu_finance', 'selesai']))
                        <form action="{{ route('event-recaps.reopen', $event->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action-block white">
                                 Buka Rekap Tambahan
                            </button>
                        </form>
                    @else
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center;">
                            Menunggu PIC event mengirimkan berkas rekap belanja untuk diverifikasi.
                        </div>
                    @endif
                @endif

                
            </div>
        </div>

        
    </div>
</div>

{{-- ═══ MODAL: EDIT ANGGARAN (FINANCE ONLY) ── --}}
@if($isFinance)
<div class="modal-overlay" id="budgetModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Alokasi Anggaran Awal Event</div>
            <button class="modal-close-btn" onclick="closeBudgetModal()"><i data-feather="x"></i></button>
        </div>
        <form action="{{ route('event-recaps.budget', $event->id) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="initial_nominal">Nominal Anggaran Awal (Rp)</label>
                    <input type="number" id="initial_nominal" name="initial_nominal" class="form-control" value="{{ $recap->initial_nominal }}" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                    <button type="button" class="btn-primary" style="background: var(--hover-bg); border: 1px solid var(--border-color); color: var(--text-main);" onclick="closeBudgetModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Anggaran</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ═══ MODAL: TAMBAH PENGELUARAN (PIC ONLY) ── --}}
@if($isPic)
<div class="modal-overlay" id="uploadModal">
    <div class="modal-box upload-modal-box">
        <!-- Header -->
        <div style="padding: 24px 24px 16px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0;">Unggah Foto/Berkas Nota Fisik</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">Upload foto atau berkas nota fisik Anda.</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeUploadModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px;"><i data-feather="x" style="width: 20px; height: 20px;"></i></button>
        </div>
        
        <form action="{{ route('event-recaps.items.store', $event->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; overflow: hidden; margin: 0;">
            @csrf
            <!-- Scrollable Body -->
            <div class="modal-body" style="padding: 24px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 20px;">
                <!-- Drag and drop zone -->
                <div class="form-group" style="margin: 0;">
                    <div class="upload-drag-area-new" id="dragArea" onclick="document.getElementById('receipt').click()">
                        <div id="upload-placeholder">
                            <div class="upload-icon-circle">
                                <i data-feather="upload-cloud" style="width: 24px; height: 24px; color: #2563EB;"></i>
                            </div>
                            <p style="font-weight: 700; color: var(--text-main); font-size: 14px; margin: 0 0 6px 0;">Klik untuk memilih file nota</p>
                            <span style="font-size: 11.5px; color: var(--text-muted);">Mendukung JPG, PNG, JPEG (Maks. 5MB)</span>
                        </div>
                        <div id="upload-preview-container" style="display: none; width: 100%; height: 100%; justify-content: center; align-items: center; flex-direction: column; position: relative;">
                            <img id="upload-preview-img" src="" style="max-width: 100%; max-height: 160px; border-radius: 8px; object-fit: contain; border: 1px solid var(--border-color);">
                            <div id="upload-file-name" style="margin-top: 8px; font-weight: 700; font-size: 12px; color: #2563EB; word-break: break-all;"></div>
                            <button type="button" class="btn-remove-preview" onclick="removeUploadPreview(event)" style="position: absolute; top: -8px; right: -8px; background: rgba(239, 68, 68, 0.9); border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: white; cursor: pointer; transition: all 0.2s;">
                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                            </button>
                        </div>
                    </div>
                    <input type="file" id="receipt" name="receipt" style="display: none;" accept="image/*" onchange="previewUploadFileName(this)" required>
                </div>

                <!-- Info banner -->
                <div class="upload-alert-banner">
                    <div class="upload-alert-icon">
                        <i data-feather="info" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 12.5px; font-weight: 700; color: var(--text-main); line-height: 1.4;">Pastikan foto jelas dan seluruh informasi nota terbaca.</div>
                        <div style="font-size: 11.5px; color: var(--text-muted); opacity: 0.8; margin-top: 2px;">File maksimal 5MB dengan format JPG, PNG, atau JPEG.</div>
                    </div>
                </div>

                <!-- Form inputs -->
                <div class="form-group" style="margin: 0;">
                    <label for="date" style="display: block; font-size: 13.5px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Tanggal Transaksi</label>
                    <input type="date" id="date" name="date" class="upload-modal-input" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group" style="position: relative; margin: 0;">
                    <label for="category" style="display: block; font-size: 13.5px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Kategori Pengeluaran</label>
                    <select id="category" name="category" class="upload-modal-input" style="appearance: none; -webkit-appearance: none; padding-right: 36px;" required>
                        <option value="Konsumsi">Konsumsi</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Perlengkapan">Perlengkapan</option>
                        <option value="Dekorasi">Dekorasi</option>
                        <option value="Sewa">Sewa</option>
                        <option value="Operasional">Operasional</option>
                    </select>
                    <i data-feather="chevron-down" style="position: absolute; right: 14px; bottom: 14px; width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
                </div>

                <!-- Vendor and Nominal Belanja row -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group" style="margin: 0;">
                        <label for="vendor" style="display: block; font-size: 13.5px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Vendor</label>
                        <input type="text" id="vendor" name="vendor" class="upload-modal-input" placeholder="Contoh: Tempat makan / toko susu" required>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label for="nominal" style="display: block; font-size: 13.5px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Nominal Belanja (Rp)</label>
                        <input type="number" id="nominal" name="nominal" class="upload-modal-input" placeholder="100000" required>
                    </div>
                </div>

                <div class="form-group" style="position: relative; margin: 0;">
                    <label for="description" style="display: block; font-size: 13.5px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Keterangan (Opsional)</label>
                    <textarea id="description" name="description" class="upload-modal-textarea" placeholder="Deskripsikan barang atau layanan yang dibeli..." maxlength="200" oninput="updateCharCount(this)"></textarea>
                    <span id="char-count" style="position: absolute; right: 12px; bottom: 10px; font-size: 11px; color: var(--text-muted);">0/200</span>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div style="padding: 16px 24px 24px 24px; display: flex; flex-direction: column; align-items: center; border-top: 1px solid var(--border-color); background: var(--card-bg);">
                <button type="submit" style="width: 100%; height: 46px; background: #1D4ED8; color: #FFFFFF; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#1E40AF'" onmouseout="this.style.background='#1D4ED8'">Simpan Nota</button>
                <button type="button" onclick="closeUploadModal()" style="margin-top: 16px; background: none; border: none; color: #2563EB; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none;">Batal</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ═══ MODAL: PREVIEW NOTA PREVIEW ── --}}
<div class="modal-overlay" id="previewModal">
    <div class="modal-box preview-modal-box">
        <div class="modal-header">
            <div class="modal-title">Detail Bukti Nota Pembelian</div>
            <button class="modal-close-btn" onclick="closePreviewModal()"><i data-feather="x"></i></button>
        </div>
        <div class="modal-body">
            <div class="preview-modal-layout">
                <div class="preview-image-panel">
                    <img id="preview-image-src" src="" alt="Nota Fullscreen">
                </div>
                <div class="preview-details-panel">
                    <div>
                        <div class="preview-detail-row">
                            <div class="preview-detail-label">Vendor</div>
                            <div class="preview-detail-value" id="preview-vendor-val">-</div>
                        </div>
                        <div class="preview-detail-row">
                            <div class="preview-detail-label">Nominal</div>
                            <div class="preview-detail-value" style="color: #2563EB; font-size: 18px;" id="preview-nominal-val">-</div>
                        </div>
                        <div class="preview-detail-row">
                            <div class="preview-detail-label">Kategori</div>
                            <div class="preview-detail-value" id="preview-category-val">-</div>
                        </div>
                        <div class="preview-detail-row">
                            <div class="preview-detail-label">Tanggal</div>
                            <div class="preview-detail-value" id="preview-date-val">-</div>
                        </div>
                        <div class="preview-detail-row">
                            <div class="preview-detail-label">Diupload Oleh</div>
                            <div class="preview-detail-value" id="preview-uploader-val">-</div>
                        </div>
                    </div>
                    <button type="button" class="btn-primary" style="width: 100%; text-align: center; justify-content: center; margin-top: 24px;" onclick="closePreviewModal()">Tutup Tampilan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Budget Modal
    function openBudgetModal() {
        const modal = document.getElementById('budgetModal');
        if (modal) modal.style.display = 'flex';
    }
    function closeBudgetModal() {
        const modal = document.getElementById('budgetModal');
        if (modal) modal.style.display = 'none';
    }

    // Upload Modal
    function openUploadModal() {
        const modal = document.getElementById('uploadModal');
        if (modal) modal.style.display = 'flex';
    }
    function closeUploadModal() {
        const modal = document.getElementById('uploadModal');
        if (modal) {
            modal.style.display = 'none';
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                removeUploadPreview();
                const charCount = document.getElementById('char-count');
                if (charCount) charCount.innerText = '0/200';
            }
        }
    }
    function previewUploadFileName(input) {
        const placeholder = document.getElementById('upload-placeholder');
        const container = document.getElementById('upload-preview-container');
        const img = document.getElementById('upload-preview-img');
        const fileName = document.getElementById('upload-file-name');
        
        if (placeholder && container && img && fileName && input.files && input.files[0]) {
            const file = input.files[0];
            fileName.innerText = file.name;
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                img.style.display = 'none';
            }
            
            placeholder.style.display = 'none';
            container.style.display = 'flex';
        }
    }

    function removeUploadPreview(event) {
        if (event) event.stopPropagation(); // Prevent trigger click on dragArea
        const input = document.getElementById('receipt');
        const placeholder = document.getElementById('upload-placeholder');
        const container = document.getElementById('upload-preview-container');
        const img = document.getElementById('upload-preview-img');
        const fileName = document.getElementById('upload-file-name');
        
        if (input) input.value = '';
        if (img) img.src = '';
        if (fileName) fileName.innerText = '';
        
        if (container) container.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
    }

    function updateCharCount(textarea) {
        const counter = document.getElementById('char-count');
        if (counter) {
            counter.innerText = textarea.value.length + '/200';
        }
    }

    // Preview Nota Modal
    function openPreviewModal(imgSrc, nominal, category, vendor, date, uploader) {
        document.getElementById('preview-image-src').src = imgSrc;
        document.getElementById('preview-vendor-val').innerText = vendor;
        document.getElementById('preview-nominal-val').innerText = 'Rp ' + nominal;
        document.getElementById('preview-category-val').innerText = category;
        document.getElementById('preview-date-val').innerText = date;
        document.getElementById('preview-uploader-val').innerText = uploader;
        
        const modal = document.getElementById('previewModal');
        if (modal) modal.style.display = 'flex';
    }
    function closePreviewModal() {
        const modal = document.getElementById('previewModal');
        if (modal) modal.style.display = 'none';
    }

    // Custom Confirm & Alert Modals
    function showCustomConfirm({ title, message, type, confirmText, confirmBg, onConfirm }) {
        document.getElementById('confirm-title').innerText = title;
        document.getElementById('confirm-message').innerText = message;
        
        const circle = document.getElementById('confirm-icon-circle');
        const icon = document.getElementById('confirm-icon');
        
        if (type === 'danger') {
            circle.className = 'alert-danger-circle';
            icon.setAttribute('data-feather', 'trash-2');
        } else if (type === 'warning') {
            circle.className = 'alert-warning-circle';
            icon.setAttribute('data-feather', 'alert-triangle');
        } else {
            circle.className = 'alert-success-circle';
            icon.setAttribute('data-feather', 'help-circle');
        }
        feather.replace();
        
        const actionBtn = document.getElementById('btn-confirm-action');
        actionBtn.innerText = confirmText || 'Ya, Lanjutkan';
        actionBtn.style.background = confirmBg || '#2563EB';
        if (type === 'danger') {
            actionBtn.style.boxShadow = '0 4px 12px rgba(220, 38, 38, 0.15)';
        } else {
            actionBtn.style.boxShadow = '0 4px 12px rgba(37, 99, 235, 0.15)';
        }
        actionBtn.style.width = ''; // Reset alert full-width overrides
        
        const cancelBtn = document.getElementById('btn-confirm-cancel');
        cancelBtn.style.display = 'block';
        
        const modal = document.getElementById('customConfirmModal');
        if (modal) {
            modal.style.display = 'flex';
            requestAnimationFrame(() => {
                modal.classList.add('active');
            });
        }
        
        actionBtn.onclick = function() {
            closeCustomConfirm();
            if (onConfirm) onConfirm();
        };
        
        cancelBtn.onclick = function() {
            closeCustomConfirm();
        };
    }

    function showCustomAlert({ title, message, type, buttonText, onConfirm }) {
        document.getElementById('confirm-title').innerText = title;
        document.getElementById('confirm-message').innerText = message;
        
        const circle = document.getElementById('confirm-icon-circle');
        const icon = document.getElementById('confirm-icon');
        
        if (type === 'danger') {
            circle.className = 'alert-danger-circle';
            icon.setAttribute('data-feather', 'alert-circle');
        } else if (type === 'success') {
            circle.className = 'alert-success-circle';
            icon.setAttribute('data-feather', 'check');
        } else {
            circle.className = 'alert-warning-circle';
            icon.setAttribute('data-feather', 'info');
        }
        feather.replace();
        
        const actionBtn = document.getElementById('btn-confirm-action');
        actionBtn.innerText = buttonText || 'Selesai';
        actionBtn.style.background = '#2563EB';
        actionBtn.style.boxShadow = '0 4px 12px rgba(37, 99, 235, 0.15)';
        actionBtn.style.width = '100%';
        
        const cancelBtn = document.getElementById('btn-confirm-cancel');
        cancelBtn.style.display = 'none'; // Hide cancel button for alerts
        
        const modal = document.getElementById('customConfirmModal');
        if (modal) {
            modal.style.display = 'flex';
            requestAnimationFrame(() => {
                modal.classList.add('active');
            });
        }
        
        actionBtn.onclick = function() {
            closeCustomConfirm();
            cancelBtn.style.display = 'block'; // Restore cancel button display
            if (onConfirm) onConfirm();
        };
    }

    function closeCustomConfirm() {
        const modal = document.getElementById('customConfirmModal');
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        feather.replace();
    });
</script>
@endsection
