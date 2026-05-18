@extends('layouts.app')

@section('title', 'Riwayat Laporan Mingguan')

@section('content')
<style>
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
    th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { color: var(--text-muted); font-weight: 500; font-size: 13px; }
    .badge { padding: 4px 8px; border-radius: 8px; font-size: 12px; font-weight: 500; }
    .badge-submitted { background: #dcfce7; color: #166534; }
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .avatar-table { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); }
</style>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h3 style="margin-bottom: 4px;">Riwayat Seluruh Laporan Mingguan</h3>
            <p style="font-size: 13px; color: var(--text-muted);">Daftar seluruh laporan kerja mingguan yang telah diserahkan oleh staf.</p>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Minggu</th>
                    <th>Nama Karyawan</th>
                    <th>Divisi</th>
                    <th>Tingkat Penyelesaian</th>
                    <th>Tanggal Penyerahan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                <tr>
                    <td style="font-weight: 600;">
                        {{ \Carbon\Carbon::parse($r->week_start_date)->translatedFormat('d M Y') }}
                    </td>
                    <td>
                        <div class="user-cell">
                            <img src="{{ $r->user->photo_url }}" class="avatar-table" alt="{{ $r->user->name }}">
                            <div>
                                <span style="font-weight: 500; display: block;">{{ $r->user->name }}</span>
                                <span style="font-size: 11px; color: var(--text-muted);">NIK: {{ $r->user->nik ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="color: var(--text-muted);">{{ optional($r->user->division)->name ?? '-' }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="flex: 1; background: var(--hover-bg); height: 6px; width: 80px; border-radius: 999px; overflow: hidden;">
                                <div style="background: #10b981; height: 100%; width: {{ $r->completion_percentage }}%;"></div>
                            </div>
                            <span style="font-weight: 600; font-size: 12px;">{{ $r->completion_percentage }}%</span>
                        </div>
                    </td>
                    <td style="font-size: 13px; color: var(--text-muted);">
                        {{ $r->final_submitted_at ? $r->final_submitted_at->translatedFormat('d/m/Y H:i') : '-' }}
                    </td>
                    <td>
                        <a href="{{ route('weekly.show_user', [$r->user_id, $r->week_start_date->format('Y-m-d')]) }}" 
                           style="color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 600; background: var(--hover-bg); padding: 6px 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                           Lihat Detail →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        Belum ada laporan yang diserahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
