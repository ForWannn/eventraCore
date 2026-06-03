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
</style>

<div style="margin-bottom: 28px;">
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Persetujuan Izin & Cuti</h1>
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

    <div class="table-wrapper">
        <table class="approval-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Karyawan</th>
                    <th style="width: 20%;">Tanggal Range</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 25%;">Alasan</th>
                    <th style="width: 18%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingRequests as $req)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <img src="{{ $req->user->photo_url }}" class="user-avatar" alt="{{ $req->user->name }}">
                                <div>
                                    <div style="font-weight: 600; color: var(--text-main);">{{ $req->user->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $req->user->division->name ?? '-' }} (ID: {{ $req->user->nik ?? '-' }})</div>
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
                            <div class="btn-action-group">
                                <form action="{{ route('leave-approvals.approve', $req->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn-approve">
                                        Setuju
                                    </button>
                                </form>
                                <form action="{{ route('leave-approvals.reject', $req->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn-reject">
                                         Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Tidak ada pengajuan yang menunggu persetujuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
                @forelse($historyRequests as $req)
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
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada riwayat tindakan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
