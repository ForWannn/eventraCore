@extends('layouts.app')

@section('title', 'Rekap Presensi Harian')

@section('content')
<div class="section-card">
    <div class="section-header" style="flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 class="section-title" style="font-size: 20px;">Pemantauan Kedisiplinan</h2>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Pusat kendali operasional Reelseven Organizer</p>
        </div>
        
        <form action="{{ route('attendance.recap') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="date" name="date" value="{{ $date }}" class="form-control" style="width: auto; margin-bottom: 0;">
            <button type="submit" class="btn btn-primary" style="margin-bottom: 0; padding: 10px 20px;">
                <i data-feather="filter" style="width: 14px; height: 14px; margin-right: 6px;"></i> Filter
            </button>
        </form>
    </div>

    <div style="margin-top: 24px; overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0 8px;">
            <thead>
                <tr style="text-align: left; color: var(--text-muted); font-size: 13px; font-weight: 600;">
                    <th style="padding: 12px 16px;">Karyawan</th>
                    <th style="padding: 12px 16px;">Jam Hadir</th>
                    <th style="padding: 12px 16px;">Metode</th>
                    <th style="padding: 12px 16px;">Status</th>
                    <th style="padding: 12px 16px;">Validasi / Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                <tr style="background: var(--hover-bg); transition: transform 0.2s;">
                    <td style="padding: 16px; border-radius: 12px 0 0 12px; border: 1px solid var(--border-color); border-right: none;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="{{ $attendance->user->photo_url }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--text-main);">{{ $attendance->user->name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $attendance->user->employee_id ?? 'No ID' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                        <div style="font-size: 14px; font-weight: 500; color: var(--text-main);">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}</div>
                    </td>
                    <td style="padding: 16px; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                        @if($attendance->attendance_type === 'kantor')
                            <span style="background: rgba(37,99,235,0.1); color: #2563eb; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                🏢 Kantor (Biometrik)
                            </span>
                        @else
                            <span style="background: rgba(16,185,129,0.1); color: #10b981; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                📍 Luar Kantor (Web)
                            </span>
                        @endif
                    </td>
                    <td style="padding: 16px; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                        @if($attendance->status === 'tepat_waktu')
                            <span style="color: #10b981; font-size: 12px; font-weight: 600;">Tepat Waktu</span>
                        @else
                            <span style="color: #ef4444; font-size: 12px; font-weight: 600;">Terlambat</span>
                        @endif
                    </td>
                    <td style="padding: 16px; border-radius: 0 12px 12px 0; border: 1px solid var(--border-color); border-left: none;">
                        @if($attendance->attendance_type === 'kantor')
                            <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Validasi Mesin</span>
                        @else
                            <button onclick="showProofModal('{{ asset('storage/' . $attendance->photo_path) }}', '{{ $attendance->latitude }}', '{{ $attendance->longitude }}', '{{ $attendance->user->name }}')" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); padding: 6px 12px; border-radius: 8px; font-size: 12px; cursor: pointer; color: var(--text-main); display: flex; align-items: center; gap: 6px;">
                                <i data-feather="eye" style="width: 14px; height: 14px;"></i> Lihat Bukti
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 14px;">
                        Belum ada data presensi untuk tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Simple Modal for Proof -->
<div id="proofModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--sidebar-bg); border-radius: 20px; width: 100%; max-width: 500px; overflow: hidden; position: relative;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 600; color: var(--text-main);" id="modalName">Bukti Absensi</h3>
            <button onclick="closeModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted);">
                <i data-feather="x"></i>
            </button>
        </div>
        <div style="padding: 20px; text-align: center;">
            <img id="modalImage" src="" style="width: 100%; max-height: 400px; object-fit: contain; border-radius: 12px; background: #000;">
            <div style="margin-top: 16px; text-align: left; background: var(--hover-bg); padding: 12px; border-radius: 10px; border: 1px solid var(--border-color);">
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Koordinat Lokasi</div>
                <div id="modalCoords" style="font-size: 13px; font-weight: 500; color: var(--text-main); font-family: monospace;"></div>
                <a id="modalMapLink" href="" target="_blank" style="display: inline-block; margin-top: 8px; color: var(--primary); font-size: 12px; text-decoration: none; font-weight: 600;">
                    <i data-feather="map-pin" style="width: 12px; height: 12px; margin-right: 4px;"></i> Buka di Google Maps
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function showProofModal(imgUrl, lat, lng, name) {
        document.getElementById('modalImage').src = imgUrl;
        document.getElementById('modalCoords').textContent = lat + ', ' + lng;
        document.getElementById('modalName').textContent = 'Bukti: ' + name;
        document.getElementById('modalMapLink').href = `https://www.google.com/maps?q=${lat},${lng}`;
        document.getElementById('proofModal').style.display = 'flex';
        feather.replace();
    }

    function closeModal() {
        document.getElementById('proofModal').style.display = 'none';
    }
</script>
@endsection
