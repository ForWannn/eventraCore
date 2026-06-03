@extends('layouts.app')

@section('title', 'Hak Akses Pengguna')

@section('content')
<style>
    .permissions-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 32px;
    }
    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .settings-title-section {
        display: flex;
        flex-direction: column;
    }
    .settings-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }
    .settings-subtitle {
        font-size: 13.5px;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    /* Control Bar: Search input */
    .control-bar {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
        align-items: center;
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        padding: 12px 16px;
        border-radius: 16px;
    }
    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 240px;
    }
    .search-wrapper input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 13.5px;
        outline: none;
        transition: all 0.2s;
    }
    .search-wrapper input:focus {
        border-color: #2563eb;
        background: var(--card-bg);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }
    .search-wrapper .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        width: 16px;
        height: 16px;
        pointer-events: none;
    }

    /* Table styling */
    .table-wrapper {
        margin-top: 20px;
        overflow: auto;
        max-height: calc(100vh - 280px);
        border: 1px solid var(--border-color);
        border-radius: 12px;
    }
    .permissions-table {
        width: 100%;
        border-collapse: collapse;
    }
    .permissions-table th, .permissions-table td {
        padding: 12px 10px;
        text-align: center;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }
    .permissions-table th {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        background: var(--bg-color);
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .permissions-table th:first-child, .permissions-table td:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        z-index: 8;
        background: var(--card-bg);
        border-right: 1.5px solid var(--border-color);
    }
    .permissions-table th:first-child {
        z-index: 12;
        background: var(--bg-color);
    }
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar-table {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid var(--border-color);
        flex-shrink: 0;
    }
    .user-details {
        display: flex;
        flex-direction: column;
    }
    .user-name {
        font-weight: 700;
        color: var(--text-main);
        font-size: 13.5px;
    }
    .user-email {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* Switch styling */
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 22px;
    }
    [data-theme="dark"] .slider {
        background-color: #475569;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #2563eb;
    }
    input:checked + .slider:before {
        transform: translateX(18px);
    }
    input:disabled + .slider {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-status.active-user {
        background: rgba(37, 99, 235, 0.08);
        border: 1px solid rgba(37, 99, 235, 0.2);
        color: #2563eb;
    }
    .badge-status.superadmin-user {
        background: rgba(139, 92, 246, 0.08);
        border: 1px solid rgba(139, 92, 246, 0.2);
        color: #8b5cf6;
    }

    /* Save button section */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 28px;
    }
    .btn-save {
        padding: 10px 24px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover {
        opacity: 0.9;
    }
</style>

<div style="margin-bottom: 20px;">
    <a href="{{ route('dashboard') }}" class="btn-back">
        <i data-feather="arrow-left"></i>
        <span>Kembali ke Dashboard</span>
    </a>
</div>

<div class="permissions-card">
    <div class="settings-header">
        <div class="settings-title-section">
            <h1 class="settings-title">Hak Akses Pengguna</h1>
            <p class="settings-subtitle">Atur modul dan fitur yang dapat diakses oleh masing-masing pengguna secara langsung.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13.5px; margin-bottom: 20px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search/Filter Bar -->
    <div class="control-bar">
        <div class="search-wrapper">
            <i data-feather="search" class="search-icon"></i>
            <input type="text" id="searchQuery" placeholder="Cari nama atau email..." />
        </div>
    </div>

    <form action="{{ route('users.permissions.update') }}" method="POST">
        @csrf
        
        <div class="table-wrapper">
            <table class="permissions-table">
                <thead>
                    <tr>
                        <th style="width: 220px;">Pengguna</th>
                        <th title="Mengakses dasbor utama sistem.">Dashboard</th>
                        <th title="Mengakses dan mengisi weekly report.">Weekly Report</th>
                        <th title="Mengajukan izin atau cuti.">Pengajuan Cuti</th>
                        <th title="Melihat riwayat absensi pribadi.">Riwayat Absen</th>
                        <th title="Melihat, menambah, mengubah, dan menghapus data karyawan.">CRUD Karyawan</th>
                        <th title="Menambah, mengubah, dan menghapus event serta penugasan tugas.">CRUD Event</th>
                        <th title="Mengatur hari kerja dan libur operasional pada kalender.">Kelola Kalender</th>
                        <th title="Melihat dan mengekspor rekapitulasi absensi harian seluruh karyawan.">Rekap Absen</th>
                        <th title="Melihat dan mengekspor rekapitulasi laporan mingguan seluruh karyawan.">Rekap Weekly</th>
                        <th title="Melihat arsip riwayat laporan mingguan karyawan.">Riwayat Weekly</th>
                        <th title="Meninjau, menyetujui, atau menolak permohonan izin/cuti karyawan.">Persetujuan Izin/Cuti</th>
                        <th title="Melihat, menginput anggaran, mengupload nota, dan mengekspor rekapitulasi event.">Rekap Event</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $availablePermissions = ['view_dashboard', 'weekly_report', 'leave_request', 'attendance_history', 'crud_users', 'crud_events', 'manage_calendar', 'rekap_absen', 'rekap_weekly', 'weekly_history', 'leave_approvals', 'rekap_event'];
                    @endphp
                    @foreach($users as $user)
                        @php
                            $isSelf = $user->id === Auth::id();
                            $isSuperadmin = $user->hasRole('Superadmin');
                        @endphp
                        <tr class="user-row" 
                            data-name="{{ strtolower($user->name) }}" 
                            data-email="{{ strtolower($user->email) }}">
                            <td>
                                <div class="user-cell">
                                    <img src="{{ $user->photo_url }}" class="avatar-table" alt="{{ $user->name }}">
                                    <div class="user-details">
                                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <span class="user-name">{{ $user->name }}</span>
                                            @if($isSelf)
                                                <span class="badge-status active-user">Anda</span>
                                            @endif
                                            @if($isSuperadmin)
                                                <span class="badge-status superadmin-user" title="Akses penuh bypass gate">Superadmin</span>
                                            @endif
                                        </div>
                                        <span class="user-email">{{ $user->email }} · {{ $user->roles->where('name', '!=', 'PIC Event')->first()?->name ?? 'Crew' }}</span>
                                    </div>
                                </div>
                            </td>
                            @foreach($availablePermissions as $perm)
                                <td style="text-align: center;">
                                    <label class="switch" title="{{ $isSelf ? 'Anda tidak dapat mengubah hak akses sendiri' : ($isSuperadmin ? 'Superadmin memiliki akses penuh' : '') }}">
                                        @if($isSelf)
                                            <!-- Checkbox is disabled but checked to represent full access and prevent self-revocation -->
                                            <input type="checkbox" disabled checked>
                                            <span class="slider"></span>
                                        @elseif($isSuperadmin)
                                            <!-- Disabled and checked since Superadmin has all permissions -->
                                            <input type="checkbox" disabled checked>
                                            <span class="slider"></span>
                                        @else
                                            <input type="checkbox" name="permissions[{{ $user->id }}][{{ $perm }}]" value="1" {{ $user->hasDirectPermission($perm) ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        @endif
                                    </label>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="13" style="text-align: center; color: var(--text-muted); padding: 32px 16px; font-weight: 500;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                <i data-feather="users" style="width: 32px; height: 32px; stroke-width: 1.5; opacity: 0.5;"></i>
                                <span>Tidak ada data pengguna yang cocok dengan pencarian Anda</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <!-- <i data-feather="save" style="width: 16px; height: 16px;"></i> -->
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchQuery');
        const rows = document.querySelectorAll('.user-row');
        const noResultsRow = document.getElementById('noResultsRow');

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().trim();
            let matches = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const email = row.getAttribute('data-email');
                if (!query || name.includes(query) || email.includes(query)) {
                    row.style.display = '';
                    matches++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (matches === 0) {
                noResultsRow.style.display = '';
            } else {
                noResultsRow.style.display = 'none';
            }
        });
    });
</script>
@endsection
