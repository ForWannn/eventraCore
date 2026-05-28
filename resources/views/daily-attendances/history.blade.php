    @extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Catatan Kehadiran Anda</h3>
        <div style="font-size: 13px; color: var(--text-muted);">Menampilkan catatan absensi harian</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="text-align: left; border-bottom: 1.5px solid var(--border-color);">
                    <th style="padding: 12px 16px; font-weight: 600; color: var(--text-muted);">Tanggal</th>
                    <th style="padding: 12px 16px; font-weight: 600; color: var(--text-muted);">Jam Masuk</th>
                    <th style="padding: 12px 16px; font-weight: 600; color: var(--text-muted);">Status</th>
                    <th style="padding: 12px 16px; font-weight: 600; color: var(--text-muted);">Metode</th>
                    <th style="padding: 12px 16px; font-weight: 600; color: var(--text-muted);">Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 16px; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}
                        </td>
                        <td style="padding: 16px;">
                            {{ \Carbon\Carbon::parse($att->check_in_time)->format('H:i') }}
                        </td>
                        <td style="padding: 16px;">
                            @if($att->status === 'tepat_waktu')
                                <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600;">Tepat Waktu</span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600;">Terlambat</span>
                            @endif
                        </td>
                        <td style="padding: 16px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                            @if($att->attendance_type === 'kantor')
                                <i data-feather="home" style="width: 14px;"></i> Gedung (Hikvision)
                            @else
                                <i data-feather="map-pin" style="width: 14px;"></i> Luar (Web)
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($att->photo_path)
                                <a href="{{ asset('storage/' . $att->photo_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $att->photo_path) }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);">
                                </a>
                            @else
                                <span style="color: var(--text-muted); font-size: 12px;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            Belum ada catatan absensi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
