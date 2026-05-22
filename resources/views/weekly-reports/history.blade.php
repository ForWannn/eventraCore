@extends('layouts.app')

@section('title', 'History Weekly Report')

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
        
        <form method="GET" action="{{ route('weekly.history') }}" style="display: flex; gap: 12px; align-items: center;">
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Filter Minggu</label>
                <select name="week" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 10px; font-size: 13px; background: var(--bg-color); color: var(--text-main); min-width: 200px;">
                    <option value="">Semua Minggu</option>
                    @foreach($availableWeeks as $week)
                        <option value="{{ $week->format('Y-m-d') }}" {{ $selectedWeek == $week->format('Y-m-d') ? 'selected' : '' }}>
                            Minggu {{ $week->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
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

    @if($targetWeek && count($nonSubmitters) > 0)
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px dashed var(--border-color);">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></div>
            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #ef4444;">Belum Mengumpulkan Weekly Report, {{ \Carbon\Carbon::parse($targetWeek)->format('d M Y') }}</h4>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
            @foreach($nonSubmitters as $user)
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 12px;">
                <img src="{{ $user->photo_url }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div>
                    <div style="font-size: 13px; font-weight: 600; color: var(--text-main);">{{ $user->name }}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">{{ $user->division->name ?? 'Tanpa Divisi' }}</div>
                </div>
                <div style="margin-left: auto; font-size: 10px; font-weight: 700; color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 4px 8px; border-radius: 6px; text-transform: uppercase;">Belum</div>
            </div>
            @endforeach
        </div>
    </div>
    @elseif($targetWeek)
    <div style="margin-top: 32px; padding: 16px; background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.15); border-radius: 12px; display: flex; align-items: center; gap: 10px;">
        <svg style="width: 20px; height: 20px; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span style="font-size: 13px; font-weight: 500; color: #166534;">Luar biasa! Seluruh staf telah mengumpulkan laporan untuk minggu ini.</span>
    </div>
    @endif
</div>
@endsection
