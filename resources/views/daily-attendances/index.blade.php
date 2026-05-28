@extends('layouts.app')

@section('title', 'Rekap Presensi Harian')

@section('content')
<style>
    /* ── Stat Cards Grid ── */
    .att-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    @media (max-width: 1200px) {
        .att-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .att-stats-grid { grid-template-columns: 1fr; }
    }

    .att-stat-card {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.25s cubic-bezier(.4,0,.2,1), box-shadow 0.25s cubic-bezier(.4,0,.2,1);
    }
    .att-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px -8px rgba(0,0,0,0.1);
    }
    .att-stat-card .att-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .att-stat-card .att-stat-icon svg {
        width: 20px;
        height: 20px;
    }
    .att-stat-card .att-stat-icon.blue    { background: rgba(37,99,235,0.1);  color: #2563eb; }
    .att-stat-card .att-stat-icon.emerald { background: rgba(16,185,129,0.1); color: #10b981; }
    .att-stat-card .att-stat-icon.rose    { background: rgba(244,63,94,0.1);  color: #f43f5e; }
    .att-stat-card .att-stat-icon.violet  { background: rgba(139,92,246,0.1); color: #8b5cf6; }

    [data-theme="dark"] .att-stat-card .att-stat-icon.blue    { background: rgba(37,99,235,0.2); }
    [data-theme="dark"] .att-stat-card .att-stat-icon.emerald { background: rgba(16,185,129,0.2); }
    [data-theme="dark"] .att-stat-card .att-stat-icon.rose    { background: rgba(244,63,94,0.2); }
    [data-theme="dark"] .att-stat-card .att-stat-icon.violet  { background: rgba(139,92,246,0.2); }

    .att-stat-card .att-stat-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 8px;
    }
    .att-stat-card .att-stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -1px;
        line-height: 1;
    }
    .att-stat-card .att-stat-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 6px;
        font-weight: 400;
    }
    .att-stat-card .att-stat-glow {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.08;
        pointer-events: none;
    }

    /* ── Section Card ── */
    .att-section-card {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 28px;
    }
    .att-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .att-section-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.3px;
    }
    .att-section-subtitle {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* ── Filter & Search Bar ── */
    .att-toolbar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .att-input {
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: var(--hover-bg);
        color: var(--text-main);
        font-size: 13px;
        font-family: 'Google Sans Flex', sans-serif;
        font-weight: 500;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .att-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .att-btn-filter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 12px;
        border: none;
        background: var(--primary);
        color: var(--primary-text);
        font-size: 13px;
        font-weight: 600;
        font-family: 'Google Sans Flex', sans-serif;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.15s;
    }
    .att-btn-filter:hover {
        opacity: 0.85;
        transform: translateY(-1px);
    }
    .att-btn-filter svg {
        width: 14px;
        height: 14px;
    }
    .att-search-box {
        position: relative;
    }
    .att-search-box svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .att-search-box input {
        padding-left: 38px;
        width: 260px;
    }
    @media (max-width: 640px) {
        .att-search-box input { width: 100%; }
    }

    /* ── Progress Bar ── */
    .att-progress-bar {
        display: flex;
        height: 8px;
        border-radius: 999px;
        overflow: hidden;
        background: var(--hover-bg);
        margin-bottom: 24px;
    }
    .att-progress-bar div {
        transition: width 0.6s cubic-bezier(.4,0,.2,1);
        height: 100%;
    }
    .att-progress-present { background: #10b981; }
    .att-progress-late    { background: #f43f5e; }
    .att-progress-absent  { background: var(--border-color); }

    .att-progress-legend {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .att-progress-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--text-muted);
    }
    .att-progress-legend-item .att-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* ── Table ── */
    .att-table-wrapper {
        overflow-x: auto;
    }
    .att-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .att-table thead tr {
        text-align: left;
    }
    .att-table thead th {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .att-table tbody tr {
        background: var(--hover-bg);
        transition: transform 0.2s cubic-bezier(.4,0,.2,1), box-shadow 0.2s;
    }
    .att-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px -4px rgba(0,0,0,0.08);
    }
    .att-table tbody td {
        padding: 14px 16px;
        font-size: 13px;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .att-table tbody td:first-child {
        border-left: 1px solid var(--border-color);
        border-radius: 12px 0 0 12px;
    }
    .att-table tbody td:last-child {
        border-right: 1px solid var(--border-color);
        border-radius: 0 12px 12px 0;
    }

    /* ── Badges ── */
    .att-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .att-badge-ontime {
        background: #dcfce7;
        color: #166534;
    }
    .att-badge-late {
        background: #fee2e2;
        color: #b91c1c;
    }
    .att-badge-absent {
        color: var(--text-muted);
        font-style: italic;
        font-size: 12px;
    }
    .att-badge-kantor {
        background: rgba(37,99,235,0.1);
        color: #2563eb;
    }
    .att-badge-luar {
        background: rgba(16,185,129,0.1);
        color: #10b981;
    }

    [data-theme="dark"] .att-badge-ontime {
        background: rgba(22,163,74,0.2);
        color: #86efac;
    }
    [data-theme="dark"] .att-badge-late {
        background: rgba(185,28,28,0.2);
        color: #fca5a5;
    }
    [data-theme="dark"] .att-badge-kantor {
        background: rgba(37,99,235,0.2);
        color: #93c5fd;
    }
    [data-theme="dark"] .att-badge-luar {
        background: rgba(16,185,129,0.2);
        color: #6ee7b7;
    }

    /* ── User Cell ── */
    .att-user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .att-user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
        flex-shrink: 0;
    }
    .att-user-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
    }
    .att-user-division {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* ── Evidence Button ── */
    .att-btn-proof {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--sidebar-bg);
        color: var(--text-main);
        font-size: 12px;
        font-weight: 500;
        font-family: 'Google Sans Flex', sans-serif;
        cursor: pointer;
        transition: all 0.2s;
    }
    .att-btn-proof:hover {
        background: var(--hover-bg);
        border-color: #8b5cf6;
        color: #8b5cf6;
    }
    .att-btn-proof svg {
        width: 14px;
        height: 14px;
    }
    .att-validated-text {
        font-size: 12px;
        font-weight: 500;
        color: #10b981;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .att-validated-text svg {
        width: 14px;
        height: 14px;
    }

    /* ── Empty State Row ── */
    .att-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }
    .att-empty-state svg {
        width: 48px;
        height: 48px;
        opacity: 0.2;
        margin-bottom: 12px;
    }

    /* ── Modal ── */
    .att-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .att-modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .att-modal-content {
        background: var(--sidebar-bg);
        border-radius: 20px;
        width: 100%;
        max-width: 480px;
        overflow: hidden;
        position: relative;
        transform: translateY(20px) scale(0.97);
        transition: transform 0.35s cubic-bezier(.2,.9,.3,1);
        box-shadow: 0 25px 80px -12px rgba(0,0,0,0.3);
    }
    .att-modal-overlay.active .att-modal-content {
        transform: translateY(0) scale(1);
    }
    .att-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .att-modal-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .att-modal-close {
        background: var(--hover-bg);
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .att-modal-close:hover {
        background: var(--border-color);
        color: var(--text-main);
    }
    .att-modal-close svg {
        width: 18px;
        height: 18px;
    }
    .att-modal-body {
        padding: 24px;
    }
    .att-modal-photo {
        width: 100%;
        max-height: 380px;
        object-fit: contain;
        border-radius: 14px;
        background: #000;
        border: 1px solid var(--border-color);
    }
    .att-modal-info {
        margin-top: 16px;
        background: var(--hover-bg);
        padding: 16px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
    }
    .att-modal-info-label {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.8px;
        margin-bottom: 6px;
    }
    .att-modal-info-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }
    .att-modal-map-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        padding: 8px 16px;
        border-radius: 10px;
        background: rgba(37,99,235,0.1);
        color: #2563eb;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .att-modal-map-link:hover {
        background: rgba(37,99,235,0.2);
    }
    .att-modal-map-link svg {
        width: 14px;
        height: 14px;
    }
    [data-theme="dark"] .att-modal-map-link {
        background: rgba(37,99,235,0.2);
        color: #93c5fd;
    }

    /* ── No-Match Search ── */
    .att-no-match {
        display: none;
    }
    .att-no-match td {
        text-align: center;
        padding: 40px 20px !important;
        color: var(--text-muted);
        font-size: 14px;
        border: none !important;
    }

    @media (max-width: 640px) {
        .att-stat-card .att-stat-value { font-size: 26px; }
        .att-section-card { padding: 20px; }
    }
</style>

{{-- ═══ STAT CARDS ═══ --}}
<div class="att-stats-grid">
    <div class="att-stat-card">
        <div class="att-stat-glow" style="background: #2563eb;"></div>
        <div class="att-stat-icon blue"><i data-feather="users"></i></div>
        <div class="att-stat-label">Total</div>
        <div class="att-stat-value">{{ $totalStaff }}</div>
        <div class="att-stat-sub">Karyawan</div>
    </div>

    <div class="att-stat-card">
        <div class="att-stat-glow" style="background: #10b981;"></div>
        <div class="att-stat-icon emerald"><i data-feather="user-check"></i></div>
        <div class="att-stat-label">Hadir</div>
        <div class="att-stat-value">{{ $presentCount }}</div>
        <div class="att-stat-sub">dari {{ $totalStaff }} karyawan</div>
    </div>

    <div class="att-stat-card">
        <div class="att-stat-glow" style="background: #f43f5e;"></div>
        <div class="att-stat-icon rose"><i data-feather="alert-circle"></i></div>
        <div class="att-stat-label">Terlambat</div>
        <div class="att-stat-value">{{ $lateCount }}</div>
        <div class="att-stat-sub">melewati batas 09:00 WIB</div>
    </div>

    <div class="att-stat-card">
        <div class="att-stat-glow" style="background: #8b5cf6;"></div>
        <div class="att-stat-icon violet"><i data-feather="map-pin"></i></div>
        <div class="att-stat-label">Absen Luar</div>
        <div class="att-stat-value">{{ $remoteCount }}</div>
        <div class="att-stat-sub">via web / geotagging</div>
    </div>
</div>

{{-- ═══ ATTENDANCE PROGRESS BAR ═══ --}}
@php
    $ontimeCount = $presentCount - $lateCount;
    $absentCount = $totalStaff - $presentCount;
    $pOntime  = $totalStaff > 0 ? ($ontimeCount / $totalStaff) * 100 : 0;
    $pLate    = $totalStaff > 0 ? ($lateCount / $totalStaff) * 100 : 0;
    $pAbsent  = $totalStaff > 0 ? ($absentCount / $totalStaff) * 100 : 0;
@endphp

{{-- ═══ MAIN TABLE SECTION ═══ --}}
<div class="att-section-card">
    <div class="att-section-header">
        <div>
            <div class="att-section-title">Pemantauan Kedisiplinan</div>
            <div class="att-section-subtitle">Rekapitulasi kehadiran seluruh karyawan — {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</div>
        </div>

        <div class="att-toolbar">
            <div class="att-search-box">
                <i data-feather="search"></i>
                <input type="text" id="attSearchInput" class="att-input" placeholder="Cari nama karyawan..." oninput="filterTable()">
            </div>
            <form action="{{ route('attendance.recap') }}" method="GET" style="display: flex; gap: 8px; align-items: center; margin: 0;">
                <input type="date" name="date" value="{{ $date }}" class="att-input">
                <button type="submit" class="att-btn-filter">
                    <i data-feather="filter"></i> Filter
                </button>
            </form>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="att-progress-bar">
        <div class="att-progress-present" style="width: {{ $pOntime }}%;"></div>
        <div class="att-progress-late" style="width: {{ $pLate }}%;"></div>
        <div class="att-progress-absent" style="width: {{ $pAbsent }}%;"></div>
    </div>
    <div class="att-progress-legend">
        <div class="att-progress-legend-item">
            <div class="att-dot" style="background: #10b981;"></div>
            Tepat Waktu ({{ $ontimeCount }})
        </div>
        <div class="att-progress-legend-item">
            <div class="att-dot" style="background: #f43f5e;"></div>
            Terlambat ({{ $lateCount }})
        </div>
        <div class="att-progress-legend-item">
            <div class="att-dot" style="background: var(--border-color);"></div>
            Belum Hadir ({{ $absentCount }})
        </div>
    </div>

    {{-- Table --}}
    <div class="att-table-wrapper">
        <table class="att-table" id="attTable">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Jam Masuk</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Validasi / Bukti</th>
                </tr>
            </thead>
            <tbody id="attTableBody">
                @forelse($users as $user)
                    @php
                        $attendance = $user->dailyAttendances->first();
                    @endphp
                    <tr class="att-row" data-name="{{ strtolower($user->name) }}">
                        {{-- Karyawan --}}
                        <td>
                            <div class="att-user-cell">
                                <img src="{{ $user->photo_url }}" class="att-user-avatar" alt="{{ $user->name }}">
                                <div>
                                    <div class="att-user-name">{{ $user->name }}</div>
                                    <div class="att-user-division">{{ $user->division->name ?? 'Tanpa Divisi' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Jam Masuk --}}
                        <td>
                            @if($attendance)
                                <span style="font-weight: 600; color: var(--text-main);">
                                    {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                                </span>
                            @else
                                <span class="att-badge-absent">Belum Hadir</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($attendance)
                                @if($attendance->status === 'tepat_waktu')
                                    <span class="att-badge att-badge-ontime">
                                        <i data-feather="check-circle" style="width:12px;height:12px;"></i> Tepat Waktu
                                    </span>
                                @else
                                    <span class="att-badge att-badge-late">
                                        <i data-feather="clock" style="width:12px;height:12px;"></i> Terlambat
                                    </span>
                                @endif
                            @else
                                <span class="att-badge-absent">—</span>
                            @endif
                        </td>

                        {{-- Metode --}}
                        <td>
                            @if($attendance)
                                @if($attendance->attendance_type === 'kantor')
                                    <span class="att-badge att-badge-kantor">
                                        <i data-feather="monitor" style="width:12px;height:12px;"></i> Kantor
                                    </span>
                                @else
                                    <span class="att-badge att-badge-luar">
                                        <i data-feather="map-pin" style="width:12px;height:12px;"></i> Luar Kantor
                                    </span>
                                @endif
                            @else
                                <span class="att-badge-absent">—</span>
                            @endif
                        </td>

                        {{-- Validasi / Bukti --}}
                        <td>
                            @if($attendance)
                                @if($attendance->attendance_type === 'kantor')
                                    <span class="att-validated-text">
                                        <i data-feather="shield" style="width:14px;height:14px;"></i> Tervalidasi Mesin
                                    </span>
                                @else
                                    <button class="att-btn-proof" onclick="showProofModal('{{ asset('storage/' . $attendance->photo_path) }}', '{{ $attendance->latitude }}', '{{ $attendance->longitude }}', '{{ $user->name }}')">
                                        <i data-feather="camera"></i> Lihat Bukti
                                    </button>
                                @endif
                            @else
                                <span class="att-badge-absent">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="att-empty-state">
                            <i data-feather="inbox"></i>
                            <p>Tidak ada data karyawan.</p>
                        </td>
                    </tr>
                @endforelse

                {{-- No match row (hidden by default) --}}
                <tr class="att-no-match" id="attNoMatch">
                    <td colspan="5">
                        <i data-feather="search" style="width:24px;height:24px;opacity:0.3;margin-bottom:8px;display:block;margin-left:auto;margin-right:auto;"></i>
                        Tidak ditemukan karyawan dengan nama tersebut.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ PROOF MODAL ═══ --}}
<div class="att-modal-overlay" id="proofModal">
    <div class="att-modal-content">
        <div class="att-modal-header">
            <h3>
                <i data-feather="image" style="width:18px;height:18px;"></i>
                <span id="modalName">Bukti Absensi</span>
            </h3>
            <button class="att-modal-close" onclick="closeModal()">
                <i data-feather="x"></i>
            </button>
        </div>
        <div class="att-modal-body">
            <img id="modalImage" class="att-modal-photo" src="" alt="Bukti Foto Absensi">
            <div class="att-modal-info">
                <div class="att-modal-info-label">Koordinat Lokasi</div>
                <div class="att-modal-info-value" id="modalCoords"></div>
                <a id="modalMapLink" href="" target="_blank" class="att-modal-map-link">
                    <i data-feather="navigation"></i> Buka di Google Maps
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // ── Search Filter ──
    function filterTable() {
        const query = document.getElementById('attSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.att-row');
        const noMatch = document.getElementById('attNoMatch');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            if (name.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        noMatch.style.display = visibleCount === 0 && query.length > 0 ? '' : 'none';
    }

    // ── Proof Modal ──
    function showProofModal(imgUrl, lat, lng, name) {
        document.getElementById('modalImage').src = imgUrl;
        document.getElementById('modalCoords').textContent = lat + ', ' + lng;
        document.getElementById('modalName').textContent = 'Bukti: ' + name;
        document.getElementById('modalMapLink').href = `https://www.google.com/maps?q=${lat},${lng}`;

        const modal = document.getElementById('proofModal');
        modal.style.display = 'flex';
        // Trigger reflow for animation
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
        feather.replace();
    }

    function closeModal() {
        const modal = document.getElementById('proofModal');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    // Close modal on overlay click
    document.getElementById('proofModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endsection
