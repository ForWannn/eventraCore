@extends('layouts.app')

@section('title', 'Rekapitulasi Laporan Mingguan')

@section('content')
<style>
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
    th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { color: var(--text-muted); font-weight: 500; font-size: 13px; }
    .badge { padding: 4px 8px; border-radius: 8px; font-size: 12px; font-weight: 500; }
    .badge-draft { background: var(--hover-bg); color: var(--text-muted); }
    .badge-submitted { background: #dcfce7; color: #166534; }
    .badge-late { background: #fee2e2; color: #b91c1c; margin-left: 4px; }
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .avatar-table { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); }
</style>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h3 style="margin-bottom: 4px;">Monitoring Log Kerja Staf</h3>
            <p style="font-size: 13px; color: var(--text-muted);">Pantau perencanaan tujuan dan hasil akhir kerja mingguan karyawan.</p>
        </div>
        
        <form method="GET" action="{{ route('weekly.recap') }}" style="display: flex; gap: 10px; align-items: center;">
            <label style="font-size: 13px; color: var(--text-muted);">Pilih Minggu (Senin):</label>
            <input type="date" name="week" value="{{ $weekStart }}" onchange="this.form.submit()" 
                   style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; background: var(--bg-color); color: var(--text-main); outline: none;">
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama Karyawan / Magang</th>
                    <th>Divisi</th>
                    <th>Status Perencanaan</th>
                    <th>Status Laporan Akhir</th>
                    <th>Tingkat Penyelesaian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                @php $userReport = $u->weeklyReports->first(); @endphp
                <tr>
                    <td>
                        <div class="user-cell">
                            <img src="{{ $u->photo_url }}" class="avatar-table" alt="{{ $u->name }}">
                            <div>
                                <span style="font-weight: 500; display: block;">{{ $u->name }}</span>
                                <span style="font-size: 11px; color: var(--text-muted);">NIK: {{ $u->nik ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="color: var(--text-muted);">{{ optional($u->division)->name ?? '-' }}</td>
                    <td>
                        @if($userReport && $userReport->plan_submitted_at)
                            <span class="badge badge-submitted">Terkirim</span>
                            @if($userReport->is_late_plan)
                                <span class="badge badge-late">Terlambat</span>
                            @endif
                        @else
                            <span class="badge badge-draft">Belum Setor</span>
                        @endif
                    </td>
                    <td>
                        @if($userReport && $userReport->status === 'submitted')
                            <span class="badge badge-submitted">Selesai diserahkan</span>
                        @else
                            <span class="badge badge-draft">Proses / Draft</span>
                        @endif
                    </td>
                    <td>
                        @if($userReport && $userReport->status === 'submitted')
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="flex: 1; background: var(--hover-bg); height: 6px; width: 80px; border-radius: 999px; overflow: hidden;">
                                    <div style="background: #10b981; height: 100%; width: {{ $userReport->completion_percentage }}%;"></div>
                                </div>
                                <span style="font-weight: 600; font-size: 12px;">{{ $userReport->completion_percentage }}%</span>
                            </div>
                        @else
                            <span style="color: var(--text-muted); font-size: 13px;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($userReport)
                            <a href="{{ route('weekly.show_user', [$u->id, $weekStart]) }}" 
                               style="color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 600; background: var(--hover-bg); padding: 6px 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                               Review Laporan →
                            </a>
                        @else
                            <span style="color: var(--text-muted); font-size: 12px; italic">Belum ada data</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection