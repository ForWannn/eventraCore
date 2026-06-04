@extends('layouts.app')

@section('title', 'Persetujuan Izin & Cuti')

@section('content')
<style>
    .approval-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 32px;
        margin-bottom: 28px;
    }
    .approval-title-section {
        display: flex;
        flex-direction: column;
        margin-bottom: 24px;
    }
    .approval-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .approval-title svg {
        width: 20px;
        height: 20px;
        color: var(--text-muted);
    }
    .approval-subtitle {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    /* Badges */
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

    /* Table styling */
    .table-wrapper {
        overflow-x: auto;
    }
    .approval-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }
    .approval-table th, .approval-table td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .approval-table th {
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11.5px;
        letter-spacing: 0.5px;
    }
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Action buttons */
    .btn-action-group {
        display: flex;
        gap: 8px;
    }
    .btn-approve {
        padding: 6px 14px;
        background: #10b981;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-reject {
        padding: 6px 14px;
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-approve:hover, .btn-reject:hover {
        opacity: 0.9;
    }

    /* Tabs Navigation */
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
        h1{
            font-size: 16px !important;
        }
        .approval-card {
            padding: 16px !important;
            border-radius: 16px !important;
            margin-bottom: 20px !important;
        }
        .approval-title {
            font-size: 12px !important;
        }
        .approval-subtitle {
            font-size: 10px !important;
            line-height: 1.4 !important;
        }
        
        .table-wrapper {
            overflow-x: visible !important;
        }
        
        /* Hide standard table header */
        .approval-table thead {
            display: none !important;
        }
        
        /* Force table elements to stack */
        .approval-table, 
        .approval-table tbody, 
        .approval-table tr, 
        .approval-table td {
            display: block !important;
            width: 100% !important;
        }
        
        /* Render normal rows as compact card boxes, exclude empty rows */
        .approval-table tr:not(:has(td[colspan])) {
            /* background: var(--hover-bg) !important; */
            border: 1px solid var(--border-color) !important;
            border-radius: 14px !important;
            padding: 14px !important;
            margin-bottom: 14px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
        }
        [data-theme="dark"] .approval-table tr:not(:has(td[colspan])) {
            background: rgba(30, 41, 59, 0.25) !important;
        }
        
        .approval-table tr td {
            border: none !important;
            padding: 0 !important;
            text-align: left !important;
            font-size: 10px !important;
        }
        
        /* Column 1 (Employee) */
        .approval-table tr td:nth-child(1) {
            margin-bottom: 4px !important;
        }
        
        /* Labeled date column */
        .approval-table tr td:nth-child(2) {
            border-top: 1px dashed var(--border-color) !important;
            padding-top: 8px !important;
            font-size: 10px !important;
        }
        .approval-table tr td:nth-child(2):before {
            content: "Tanggal: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 10px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled type column */
        .approval-table tr td:nth-child(3) {
            font-size: 10px !important;
        }
        .approval-table tr td:nth-child(3):before {
            content: "Jenis: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 10px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled reason column */
        .approval-table tr td:nth-child(4) {
            font-size: 10px !important;
            line-height: 1.4 !important;
        }
        .approval-table tr td:nth-child(4):before {
            content: "Alasan: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 10px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Action buttons column */
        .approval-table tr td:nth-child(5) .btn-action-group {
            display: flex !important;
            width: 100% !important;
            gap: 8px !important;
            margin-top: 6px !important;
            border-top: 1px dashed var(--border-color) !important;
            padding-top: 10px !important;
        }
        .approval-table tr td:nth-child(5) .btn-action-group form {
            flex: 1 !important;
        }
        .approval-table tr td:nth-child(5) .btn-action-group button {
            width: 100% !important;
            padding: 8px 12px !important;
            justify-content: center !important;
            border-radius: 8px !important;
            font-size: 12.5px !important;
        }
        
        /* Labeled status column (History table) */
        .approval-table tr td:nth-child(5):not(:has(.btn-action-group)) {
            font-size: 10px !important;
        }
        .approval-table tr td:nth-child(5):not(:has(.btn-action-group)):before {
            content: "Status: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 10px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        
        /* Labeled approved by column (History table) */
        .approval-table tr td:nth-child(6) {
            font-size: 10px !important;
        }
        .approval-table tr td:nth-child(6):before {
            content: "Oleh: ";
            font-weight: 700;
            color: var(--text-muted);
            font-size: 10px;
            text-transform: uppercase;
            margin-right: 4px;
        }
        .badge-type{
            font-size: 10px !important;
        }
        .filter_leaves label{
            font-size: 10px !important;
        }
        .filter_leaves input{
            font-size: 10px !important;
        }
        .btn-approve,
        .btn-reject{
            font-size: 10px !important;
            height: 30px !important;
        }
        .tab-button, .tab-badge,
        .badge-status.approved{
            font-size: 10px !important;
        }
    }
</style>

<div style="margin-bottom: 28px;">
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Persetujuan Izin / Cuti</h1>
</div>

@if(session('success'))
    <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        {{ session('error') }}
    </div>
@endif

<!-- Pending Requests Panel -->
<div class="approval-card">
    <div class="approval-title-section">
        <h3 class="approval-title">
            <!-- <i data-feather="alert-circle"></i> -->
            <span>Menunggu Persetujuan</span>
        </h3>
        <p class="approval-subtitle">Daftar pengajuan izin dan cuti karyawan yang memerlukan tindakan Anda.</p>
    </div>

<!-- History Panel -->
<div class="approval-card">
    <div class="approval-title-section">
        <h3 class="approval-title">
            <!-- <i data-feather="check-square"></i> -->
            <span>Riwayat Tindakan</span>
        </h3>
        <p class="approval-subtitle">Log riwayat persetujuan atau penolakan pengajuan izin dan cuti.</p>
    </div>

    <!-- Date Range Filter Form -->
    <form action="{{ route('leave-approvals.index') }}" method="GET" style="margin-bottom: 24px;">
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 140px;" class="filter_leaves">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; display: block;">Mulai Tanggal</label>
                <input type="date" name="filter_start_date" class="form-input" style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid var(--border-color); border-radius: 10px; background: var(--bg-color); color: var(--text-main); outline: none;" value="{{ request('filter_start_date') }}">
            </div>
            <div style="flex: 1; min-width: 140px;" class="filter_leaves">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; display: block;">Sampai Tanggal</label>
                <input type="date" name="filter_end_date" class="form-input" style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid var(--border-color); border-radius: 10px; background: var(--bg-color); color: var(--text-main); outline: none;" value="{{ request('filter_end_date') }}">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-approve" style="height: 40px; padding: 0 16px; margin: 0; background: #2563eb; border-radius: 10px; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    Filter
                </button>
                <a href="{{ route('leave-approvals.index') }}" class="btn-reject" style="height: 40px; padding: 0 16px; margin: 0; background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); border-radius: 10px; font-size: 13.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; justify-content: center;">
                    Reset
                </a>
            </div>
        </div>
    </form>

    @php
        $izinHistory = $historyRequests->where('type', 'izin');
        $cutiHistory = $historyRequests->where('type', 'cuti');
    @endphp

    <!-- Tabs Navigation -->
    <div class="navigation-tabs">
        <button type="button" class="tab-button active" onclick="switchTab(event, 'tab-history-izin')">
            Izin <span class="tab-badge">{{ $izinHistory->count() }}</span>
        </button>
        <button type="button" class="tab-button" onclick="switchTab(event, 'tab-history-cuti')">
            Cuti <span class="tab-badge">{{ $cutiHistory->count() }}</span>
        </button>
    </div>

    <!-- Tab Content: Izin History -->
    <div id="tab-history-izin" class="tab-content active">
        <div class="table-wrapper">
            <table class="approval-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Karyawan</th>
                        <th style="width: 20%;">Tanggal Range</th>
                        <th style="width: 12%;">Jenis</th>
                        <th style="width: 23%;">Alasan</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Disetujui Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinHistory as $req)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <img src="{{ $req->user->photo_url }}" class="user-avatar" alt="{{ $req->user->name }}">
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-main);">{{ $req->user->name }}</div>
                                        <div style="font-size: 11px; color: var(--text-muted);">{{ $req->user->division->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight: 600; color: var(--text-main);">
                                @if($req->start_date->format('Y-m-d') === $req->end_date->format('Y-m-d'))
                                    {{ $req->start_date->translatedFormat('d M Y') }}
                                @else
                                    {{ $req->start_date->translatedFormat('d M') }} - {{ $req->end_date->translatedFormat('d M Y') }}
                                @endif
                            </td>
                            <td>
                                <span class="badge-type">{{ $req->type }}</span>
                            </td>
                            <td style="color: var(--text-main); font-weight: 500;">
                                {{ $req->reason }}
                            </td>
                            <td>
                                @if($req->status === 'approved')
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
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada riwayat tindakan izin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab Content: Cuti History -->
    <div id="tab-history-cuti" class="tab-content">
        <div class="table-wrapper">
            <table class="approval-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Karyawan</th>
                        <th style="width: 20%;">Tanggal Range</th>
                        <th style="width: 12%;">Jenis</th>
                        <th style="width: 23%;">Alasan</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Disetujui Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cutiHistory as $req)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <img src="{{ $req->user->photo_url }}" class="user-avatar" alt="{{ $req->user->name }}">
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-main);">{{ $req->user->name }}</div>
                                        <div style="font-size: 11px; color: var(--text-muted);">{{ $req->user->division->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight: 600; color: var(--text-main);">
                                @if($req->start_date->format('Y-m-d') === $req->end_date->format('Y-m-d'))
                                    {{ $req->start_date->translatedFormat('d M Y') }}
                                @else
                                    {{ $req->start_date->translatedFormat('d M') }} - {{ $req->end_date->translatedFormat('d M Y') }}
                                @endif
                            </td>
                            <td>
                                <span class="badge-type">{{ $req->type }}</span>
                            </td>
                            <td style="color: var(--text-main); font-weight: 500;">
                                {{ $req->reason }}
                            </td>
                            <td>
                                @if($req->status === 'approved')
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
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada riwayat tindakan cuti.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
</script>

@endsection
