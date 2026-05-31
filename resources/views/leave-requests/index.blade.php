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

    /* Form Fields */
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

    /* Table Badges */
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
</style>

<div style="margin-bottom: 28px;">
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Pengajuan Izin & Cuti</h1>
    <p style="color: var(--text-muted); font-size: 13.5px; margin-top: 4px; font-weight: 500;">Ajukan perizinan sakit, cuti tahunan, atau urusan keluarga Anda.</p>
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
            <i data-feather="send"></i>
            <span>Form Pengajuan</span>
        </h3>
        
        <form action="{{ route('leave-requests.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="type">Jenis Pengajuan</label>
                <select id="type" name="type" class="form-select" required>
                    <option value="izin">Izin (Sakit / Keperluan Penting)</option>
                    <option value="cuti">Cuti Tahunan</option>
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
                <textarea id="reason" name="reason" class="form-textarea" required placeholder="Tulis alasan pengajuan secara jelas..."></textarea>
                @error('reason')<span style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn-submit">
                <i data-feather="check"></i> Kirim Pengajuan
            </button>
        </form>
    </div>

    <!-- Right Column: History -->
    <div class="leave-card">
        <h3 class="leave-card-title">
            <i data-feather="clock"></i>
            <span>Riwayat Pengajuan</span>
        </h3>
        
        <div class="table-wrapper">
            <table class="leave-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Tanggal Range</th>
                        <th style="width: 15%;">Jenis</th>
                        <th style="width: 30%;">Alasan</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Disetujui Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
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
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada riwayat pengajuan izin/cuti.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
