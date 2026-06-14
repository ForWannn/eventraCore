@extends('layouts.app')

@section('title', 'Pengajuan Izin & Cuti')

@section('content')
<style>
    .leave-layout {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 992px) {
        .leave-layout { grid-template-columns: 1fr; }
    }

    .leave-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 28px;
    }
    .leave-card-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .leave-card-title svg {
        width: 20px;
        height: 20px;
        color: var(--text-muted);
    }
    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 18px;
    }
    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 8px;
    }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 13.5px;
        background: var(--bg-color);
        color: var(--text-main);
        outline: none;
        transition: border-color 0.15s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #2563eb;
    }
    .form-textarea {
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-submit:hover {
        opacity: 0.9;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .badge-status.pending { background: #fef3c7; color: #d97706; }
    .badge-status.approved { background: #dcfce7; color: #166534; }
    .badge-status.rejected { background: #fee2e2; color: #b91c1c; }

    [data-theme="dark"] .badge-status.pending { background: rgba(217,119,6,0.15); color: #fbbf24; }
    [data-theme="dark"] .badge-status.approved { background: rgba(22,163,74,0.15); color: #86efac; }
    [data-theme="dark"] .badge-status.rejected { background: rgba(185,28,28,0.15); color: #fca5a5; }

    .badge-type {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        text-transform: uppercase;
    }

    .table-wrapper {
        overflow-x: auto;
    }
    .leave-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }
    .leave-table th, .leave-table td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }
    .leave-table th {
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11.5px;
        letter-spacing: 0.5px;
    }

    .navigation-tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 20px;
        gap: 8px;
        overflow-x: auto;
    }
    .tab-button {
        padding: 10px 16px;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-muted);
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .tab-button:hover {
        color: var(--text-main);
    }
    .tab-button.active {
        color: #2563EB;
        border-bottom-color: #2563EB;
    }
    .tab-badge {
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 6px;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
    }
    .tab-button.active .tab-badge {
        background: rgba(37, 99, 235, 0.1);
        border-color: rgba(37, 99, 235, 0.2);
        color: #2563EB;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    @media (max-width: 768px) {
        .table-wrapper {
            overflow-x: visible !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .leave-table thead {
            display: none !important;
        }
        .leave-table {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .leave-table tr,
        .leave-table td {
            display: block !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .leave-table tbody {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .leave-table tr:not(:has(td[colspan])) {
            border: 1px solid var(--border-color) !important;
            border-radius: 14px !important;
            padding: 14px !important;
            margin-bottom: 0px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
            background: var(--bg-color);
            min-width: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            overflow: hidden !important;
            align-self: start;
            box-sizing: border-box !important;
        }
        .leave-table tr td {
            border: none !important;
            padding: 0 !important;
            text-align: left !important;
            font-size: 12px !important;
            min-width: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            word-break: break-word !important;
            box-sizing: border-box !important;
        }
        
        .leave-table tr:has(td[colspan]) {
            grid-column: span 2 !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .leave-table tr:has(td[colspan]) td {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            text-align: center !important;
            padding: 40px 0 !important;
            box-sizing: border-box !important;
        }
        
        .leave-table tr td:nth-child(1) {
            font-weight: 700 !important;
            font-size: 13px !important;
            color: var(--text-main) !important;
            border-bottom: 1px solid var(--border-color) !important;
            padding-bottom: 8px !important;
            margin-bottom: 4px !important;
        }
        
        /* Column 2 (Reason) */
        .leave-table tr td:nth-child(2):before {
            content: "Alasan: ";
            font-weight: 700;
            color: var(--text-muted);
            margin-right: 4px;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        /* Column 3 (Status) */
        .leave-table tr td:nth-child(3):before {
            content: "Status: ";
            font-weight: 700;
            color: var(--text-muted);
            margin-right: 4px;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        /* Column 4 (Approved By) */
        .leave-table tr td:nth-child(4):before {
            content: "Oleh: ";
            font-weight: 700;
            color: var(--text-muted);
            margin-right: 4px;
            text-transform: uppercase;
            font-size: 10px;
        }

        .tab-button, .tab-badge,
        .badge-status {
            font-size: 10px !important;
        }
    }

    @media (max-width: 640px) {
        .leave-card {
            padding: 16px !important;
            border-radius: 16px !important;
        }
        h1[style*="font-size: 24px"] {
            font-size: 20px !important;
        }
        .leave-card-title {
            font-size: 15px !important;
            margin-bottom: 16px !important;
        }
        .form-group {
            margin-bottom: 14px !important;
        }
        .form-input, .form-select, .form-textarea {
            font-size: 13px !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
        }
        .btn-submit {
            font-size: 13px !important;
            padding: 10px !important;
            border-radius: 8px !important;
        }
        .filter-actions-wrapper {
            width: 100% !important;
        }
        .filter-actions-wrapper .btn-submit {
            flex: 1 !important;
            width: 100% !important;
            justify-content: center !important;
        }
        .leave-table tbody {
            gap: 12px !important;
        }
        .leave-table tr {
            padding: 12px !important;
        }
    }
    .upload-drag-area {
        border: 2px dashed rgba(37, 99, 235, 0.15);
        background: rgba(37, 99, 235, 0.02);
        border-radius: 16px;
        padding: 24px 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 150px;
        box-sizing: border-box;
        margin-top: 8px;
    }
    .upload-drag-area:hover {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.05);
    }
    .upload-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .preview-container {
        display: none;
        width: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .preview-img {
        max-width: 100%;
        max-height: 120px;
        border-radius: 8px;
        object-fit: contain;
        border: 1px solid var(--border-color);
    }
    .preview-doc {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #2563eb;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        width: 64px;
        height: 64px;
        border-radius: 12px;
        text-transform: uppercase;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }
    .btn-remove-preview {
        position: absolute;
        top: -8px;
        right: -8px;
        background: rgba(239, 68, 68, 0.9);
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-remove-preview:hover {
        background: rgb(239, 68, 68);
    }
    .cuti-info-box {
        background: rgba(37, 99, 235, 0.04);
        border: 1.5px solid rgba(37, 99, 235, 0.12);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cuti-info-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #2563eb;
    }
    .cuti-info-content {
        flex: 1;
    }
    .cuti-info-title {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-main);
    }
    .cuti-info-value {
        font-size: 13.5px;
        color: #2563eb;
        font-weight: 800;
        margin-top: 2px;
    }
</style>

<div style="margin-bottom: 28px;">
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Pengajuan Izin & Cuti</h1>
</div>

@if(session('success'))
    <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif

<div class="leave-layout">
    <!-- Left Column: Form -->
    <div class="leave-card">
        <h3 class="leave-card-title">
            <span>Form Pengajuan</span>
        </h3>
        <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="type">Jenis Pengajuan</label>
                <select id="type" name="type" class="form-select" required>
                    <option value="izin">Izin</option>
                    @unless(Auth::user()->hasRole('Intern'))
                        <option value="cuti">Cuti</option>
                    @endunless
                </select>
                @error('type')<span style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="start_date">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" class="form-input" required value="{{ date('Y-m-d') }}">
                @error('start_date')<span style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="end_date">Tanggal Selesai</label>
                <input type="date" id="end_date" name="end_date" class="form-input" required value="{{ date('Y-m-d') }}">
                @error('end_date')<span style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="reason">Alasan / Keterangan</label>
                <textarea id="reason" name="reason" class="form-textarea" required placeholder="Alasan pengajuan"></textarea>
                @error('reason')<span style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</span>@enderror
            </div>
            <div class="cuti-info-box" id="cuti-info-group" style="display: none;">
                <div class="cuti-info-icon">
                    <i data-feather="calendar" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="cuti-info-content">
                    <div class="cuti-info-title">Sisa Cuti Tahun{{ date('Y') }}</div>
                    <div class="cuti-info-value" id="cuti-remaining-val">{{ $remainingCuti }} Hari</div>
                </div>
            </div>
            <div class="form-group" id="proof-group">
                <label for="proof" id="proof-label">Bukti</label>
                <div class="upload-drag-area" onclick="document.getElementById('proof').click()">
                    <div id="upload-placeholder">
                        <div class="upload-icon-circle" style="margin-left: auto; margin-right: auto;">
                            <i data-feather="upload-cloud" style="width: 20px; height: 20px; color: #2563eb;"></i>
                        </div>
                        <p style="font-weight: 700; color: var(--text-main); font-size: 13px; margin: 0 0 4px 0;">Klik untuk unggah dokumen bukti</p>
                        <span style="font-size: 11px; color: var(--text-muted);">Mendukung Gambar, PDF, DOC, DOCX (Maks. 5MB)</span>
                    </div>
                    <div id="preview-container" class="preview-container">
                        <div id="preview-content"></div>
                        <div id="preview-filename" style="margin-top: 8px; font-weight: 700; font-size: 11.5px; color: #2563eb; word-break: break-all;"></div>
                        <button type="button" class="btn-remove-preview" onclick="clearUpload(event)">
                            <i data-feather="x" style="width: 12px; height: 12px;"></i>
                        </button>
                    </div>
                </div>
                <input type="file" id="proof" name="proof" style="display: none;" accept="image/*,.pdf,.doc,.docx" onchange="previewUpload(this)">
                @error('proof')<span style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn-submit">
                 Kirim
            </button>
        </form>
    </div>
    <!-- Right Column: History -->
    <div class="leave-card">
        <h3 class="leave-card-title">
            <span>Riwayat Pengajuan</span>
        </h3>
        <!-- Date Range Filter Form -->
        <form action="{{ route('leave-requests.index') }}" method="GET" style="margin-bottom: 24px;">
            <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; display: block;">Mulai Tanggal</label>
                    <input type="date" name="filter_start_date" class="form-input" style="height: 40px; padding: 0 12px;" value="{{ request('filter_start_date') }}">
                </div>
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; display: block;">Sampai Tanggal</label>
                    <input type="date" name="filter_end_date" class="form-input" style="height: 40px; padding: 0 12px;" value="{{ request('filter_end_date') }}">
                </div>
                <div class="filter-actions-wrapper" style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-submit" style="width: auto; height: 40px; padding: 0 16px; margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                        Filter
                    </button>
                    <a href="{{ route('leave-requests.index') }}" class="btn-submit" style="width: auto; height: 40px; padding: 0 16px; margin: 0; background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; justify-content: center;">
                        Reset
                    </a>
                </div>
            </div>
        </form>
        @php
            $izinRequests = $requests->where('type', 'izin');
            $cutiRequests = $requests->where('type', 'cuti');
        @endphp
        <!-- Tabs Navigation -->
        <div class="navigation-tabs">
            <button type="button" class="tab-button active" onclick="switchTab(event, 'tab-izin')">
                Izin <span class="tab-badge">{{ $izinRequests->count() }}</span>
            </button>
            @unless(Auth::user()->hasRole('Intern'))
            <button type="button" class="tab-button" onclick="switchTab(event, 'tab-cuti')">
                Cuti <span class="tab-badge">{{ $cutiRequests->count() }}</span>
            </button>
            @endunless
        </div>
        <!-- Tab Content: Izin -->
        <div id="tab-izin" class="tab-content active">
            <div class="table-wrapper">
                <table class="leave-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Tanggal Range</th>
                            <th style="width: 40%;">Alasan</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;">Disetujui Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($izinRequests as $req)
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main);">
                                    @if($req->start_date->format('Y-m-d') === $req->end_date->format('Y-m-d'))
                                        {{ $req->start_date->translatedFormat('d M Y') }}
                                    @else
                                        {{ $req->start_date->translatedFormat('d M') }} - {{ $req->end_date->translatedFormat('d M Y') }}
                                    @endif
                                </td>
                                <td style="color: var(--text-main); font-weight: 500;">
                                    {{ $req->reason }}
                                    @if($req->proof_path)
                                        <div style="margin-top: 6px;">
                                            <a href="{{ asset($req->proof_path) }}" target="_blank" style="font-size: 11.5px; color: #2563eb; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; font-weight: 600;">
                                                <i data-feather="file-text" style="width: 12px; height: 12px;"></i> Lihat Bukti
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge-status pending">
                                            <i data-feather="clock" style="width:12px; height:12px;"></i> Pending
                                        </span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge-status approved">
                                            <i data-feather="check-circle" style="width:12px; height:12px;"></i> Disetujui
                                        </span>
                                    @else
                                        <span class="badge-status rejected">
                                            <i data-feather="x-circle" style="width:12px; height:12px;"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td style="color: var(--text-muted); font-weight: 600;">
                                    {{ $req->approvedBy->name ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada riwayat pengajuan izin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Tab Content: Cuti -->
        <div id="tab-cuti" class="tab-content">
            <div class="table-wrapper">
                <table class="leave-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Tanggal Range</th>
                            <th style="width: 40%;">Alasan</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;">Disetujui Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cutiRequests as $req)
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main);">
                                    @if($req->start_date->format('Y-m-d') === $req->end_date->format('Y-m-d'))
                                        {{ $req->start_date->translatedFormat('d M Y') }}
                                    @else
                                        {{ $req->start_date->translatedFormat('d M') }} - {{ $req->end_date->translatedFormat('d M Y') }}
                                    @endif
                                </td>
                                <td style="color: var(--text-main); font-weight: 500;">
                                    {{ $req->reason }}
                                    @if($req->proof_path)
                                        <div style="margin-top: 6px;">
                                            <a href="{{ asset($req->proof_path) }}" target="_blank" style="font-size: 11.5px; color: #2563eb; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; font-weight: 600;">
                                                <i data-feather="file-text" style="width: 12px; height: 12px;"></i> Lihat Bukti
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge-status pending">
                                            <i data-feather="clock" style="width:12px; height:12px;"></i> Pending
                                        </span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge-status approved">
                                            <i data-feather="check-circle" style="width:12px; height:12px;"></i> Disetujui
                                        </span>
                                        <div style="margin-top: 6px;">
                                            <a href="{{ route('leave-requests.download-pdf', $req->id) }}" style="font-size: 11.5px; color: #10b981; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; font-weight: 600;">
                                                <i data-feather="download" style="width: 12px; height: 12px;"></i> Unduh Surat
                                            </a>
                                        </div>
                                    @else
                                        <span class="badge-status rejected">
                                            <i data-feather="x-circle" style="width:12px; height:12px;"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td style="color: var(--text-muted); font-weight: 600;">
                                    @if($req->status === 'approved')
                                        <div style="font-size: 11px; display: flex; flex-direction: column; gap: 2px;">
                                            <span>GM: {{ $req->approvedByGm->name ?? '-' }}</span>
                                            <span>CEO: {{ $req->approvedByCeo->name ?? '-' }}</span>
                                        </div>
                                    @elseif($req->status === 'pending')
                                        <div style="font-size: 11px; display: flex; flex-direction: column; gap: 4px;">
                                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                                GM: {!! $req->approved_by_gm_id ? '<span style="color:#10b981; font-weight:700;">Setuju</span>' : '<span style="color:#d97706; font-weight:500;">Menunggu</span>' !!}
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                                CEO: {!! $req->approved_by_ceo_id ? '<span style="color:#10b981; font-weight:700;">Setuju</span>' : '<span style="color:#d97706; font-weight:500;">Menunggu</span>' !!}
                                            </span>
                                        </div>
                                    @elseif($req->status === 'rejected')
                                        <span>{{ $req->approvedBy->name ?? '-' }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada riwayat pengajuan cuti.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function switchTab(evt, tabId) {
        document.querySelectorAll('.tab-content').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById(tabId).style.display = 'block';
        evt.currentTarget.classList.add('active');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const proofGroup = document.getElementById('proof-group');
        const cutiInfoGroup = document.getElementById('cuti-info-group');
        const proofInput = document.getElementById('proof');
        if (typeSelect) {
            function updateFormLayout() {
                if (typeSelect.value === 'izin') {
                    if (proofGroup) proofGroup.style.display = 'block';
                    if (cutiInfoGroup) cutiInfoGroup.style.display = 'none';
                    if (proofInput) proofInput.required = true;
                } else {
                    if (proofGroup) proofGroup.style.display = 'none';
                    if (cutiInfoGroup) cutiInfoGroup.style.display = 'flex';
                    if (proofInput) {
                        proofInput.required = false;
                        // Clear upload if they switched from izin to cuti
                        const clearBtn = document.querySelector('.btn-remove-preview');
                        if (clearBtn && proofInput.value) {
                            clearBtn.click();
                        }
                    }
                }
            }
            typeSelect.addEventListener('change', updateFormLayout);
            updateFormLayout();
        }
    });
    function previewUpload(input) {
        const file = input.files[0];
        const placeholder = document.getElementById('upload-placeholder');
        const container = document.getElementById('preview-container');
        const content = document.getElementById('preview-content');
        const filename = document.getElementById('preview-filename');
        if (file) {
            placeholder.style.display = 'none';
            container.style.display = 'flex';
            filename.textContent = file.name;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    content.innerHTML = `<img src="${e.target.result}" class="preview-img">`;
                };
                reader.readAsDataURL(file);
            } else {
                const extension = file.name.split('.').pop().toUpperCase();
                content.innerHTML = `<div class="preview-doc">${extension}</div>`;
            }
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    }
    function clearUpload(event) {
        event.stopPropagation();
        const input = document.getElementById('proof');
        const placeholder = document.getElementById('upload-placeholder');
        const container = document.getElementById('preview-container');
        const content = document.getElementById('preview-content');
        const filename = document.getElementById('preview-filename');
        if (input) input.value = '';
        if (content) content.innerHTML = '';
        if (filename) filename.textContent = '';
        if (container) container.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
    }
</script>
@endsection
