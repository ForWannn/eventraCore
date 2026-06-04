@extends('layouts.app')

@section('title', 'Dasbor Admin')

@section('content')
<style>
    /* Stats Grid */
    .admin-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    @media (max-width: 1200px) {
        .admin-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .admin-stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
            margin-bottom: 16px !important;
        }
        .admin-stat-card {
            padding: 12px !important;
            border-radius: 10px !important;
            min-height: auto !important;
            gap: 8px !important;
        }
        .admin-stat-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            font-size: 14px !important;
        }
        .admin-stat-icon svg {
            width: 16px !important;
            height: 16px !important;
        }
        .admin-stat-value {
            font-size: 20px !important;
            margin-top: 2px !important;
        }
        .admin-stat-label {
            font-size: 9.5px !important;
        }
        .admin-panel {
            padding: 14px !important;
            border-radius: 12px !important;
            margin-bottom: 16px !important;
        }
        .panel-header {
            margin-bottom: 12px !important;
        }
        .panel-title {
            font-size: 13.5px !important;
        }
    }

    .admin-stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 110px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    /* .admin-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.03);
    } */
    .admin-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .admin-stat-icon.blue    { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .admin-stat-icon.emerald { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
    .admin-stat-icon.amber   { background: #fff7ed; color: #f59e0b; border: 1px solid #fed7aa; }
    .admin-stat-icon.violet  { background: #faf5ff; color: #8b5cf6; border: 1px solid #f3e8ff; }

    [data-theme="dark"] .admin-stat-icon.blue    { background: rgba(37,99,235,0.15); color: #60a5fa; border-color: rgba(37,99,235,0.2); }
    [data-theme="dark"] .admin-stat-icon.emerald { background: rgba(16,185,129,0.15); color: #34d399; border-color: rgba(16,185,129,0.2); }
    [data-theme="dark"] .admin-stat-icon.amber   { background: rgba(245,158,11,0.15); color: #fbbf24; border-color: rgba(245,158,11,0.2); }
    [data-theme="dark"] .admin-stat-icon.violet  { background: rgba(139,92,246,0.15); color: #a78bfa; border-color: rgba(139,92,246,0.2); }

    .admin-stat-info {
        display: flex;
        flex-direction: column;
    }
    .admin-stat-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .admin-stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-main);
        margin-top: 4px;
        line-height: 1.1;
    }

    /* Middle Layout (3 Columns) */
    .admin-middle-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }
    @media (max-width: 1024px) {
        .admin-middle-grid { grid-template-columns: 1fr; }
    }

    .admin-panel {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        flex-direction: column;
    }
    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .panel-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-title svg {
        width: 18px;
        height: 18px;
        color: var(--text-muted);
    }

    /* System Status Items */
    .system-status-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: var(--hover-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    .status-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
    }
    .status-val-badge {
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-val-badge.success {
        background: rgba(16, 185, 129, 0.08);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.15);
    }
    .status-val-badge.info {
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
        border: 1px solid rgba(37, 99, 235, 0.15);
    }

    /* Pulse Green Indicator */
    .online-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse 1.6s infinite;
    }
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    /* Bottom Layout (2 Columns) */
    .admin-bottom-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    @media (max-width: 768px) {
        .admin-bottom-grid { grid-template-columns: 1fr; }
    }

    /* Table styling */
    .admin-table-wrapper {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }
    th, td {
        padding: 12px 14px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }
    th {
        color: var(--text-muted);
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .avatar-mini {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Activity Feed styling */
    .activity-feed {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .activity-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .activity-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 2px;
    }
    .activity-content {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .activity-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
    }
    .activity-desc {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .activity-time {
        font-size: 10.5px;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    /* System Summary Badges */
    .system-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .system-icon-badge.blue   { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .system-icon-badge.orange { background: #fff7ed; color: #f59e0b; border: 1px solid #fed7aa; }
    .system-icon-badge.green  { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
    .system-icon-badge.violet { background: #faf5ff; color: #8b5cf6; border: 1px solid #f3e8ff; }

    [data-theme="dark"] .system-icon-badge.blue   { background: rgba(37,99,235,0.12); color: #60a5fa; border-color: rgba(37,99,235,0.2); }
    [data-theme="dark"] .system-icon-badge.orange { background: rgba(245,158,11,0.12); color: #fbbf24; border-color: rgba(245,158,11,0.2); }
    [data-theme="dark"] .system-icon-badge.green  { background: rgba(16,185,129,0.12); color: #34d399; border-color: rgba(16,185,129,0.2); }
    [data-theme="dark"] .system-icon-badge.violet { background: rgba(139,92,246,0.12); color: #a78bfa; border-color: rgba(139,92,246,0.2); }

    .system-summary-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }
    .system-summary-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .system-summary-row:first-child {
        padding-top: 0;
    }
</style>

<div style="margin-bottom: 28px;">
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Dasbor Admin</h1>
    <p style="color: var(--text-muted); font-size: 13.5px; margin-top: 4px; font-weight: 500;">Pantau performa sistem, absensi, divisi, dan administrasi karyawan.</p>
</div>

@if(session('success'))
    <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif



<!-- 4 Stats Cards -->
<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-icon blue"><i data-feather="users"></i></div>
        <div class="admin-stat-info">
            <span class="admin-stat-label">Total Karyawan</span>
            <span class="admin-stat-value">{{ $totalEmployees }}</span>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon emerald"><i data-feather="user-check"></i></div>
        <div class="admin-stat-info">
            <span class="admin-stat-label">Karyawan Aktif</span>
            <span class="admin-stat-value">{{ $activeEmployees }}</span>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon violet"><i data-feather="layers"></i></div>
        <div class="admin-stat-info">
            <span class="admin-stat-label">Total Divisi</span>
            <span class="admin-stat-value">{{ $totalDivisions }}</span>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon amber"><i data-feather="calendar"></i></div>
        <div class="admin-stat-info">
            <span class="admin-stat-label">Total Event</span>
            <span class="admin-stat-value">{{ $totalEvents }}</span>
        </div>
    </div>
</div>

<!-- Middle Row (3 Panels) -->
<div class="admin-middle-grid">
    <!-- Panel 1: Grafik Presensi Karyawan -->
    <div class="admin-panel">
        <div class="panel-header">
            <span class="panel-title"><i data-feather="activity"></i> Grafik Presensi Karyawan</span>
        </div>
        <div style="flex: 1; min-height: 240px; position: relative;">
            <canvas id="attendanceTrendChart" style="width:100%; height:240px;"></canvas>
        </div>
    </div>

    <!-- Panel 2: Distribusi Karyawan per Divisi -->
    <!-- <div class="admin-panel">
        <div class="panel-header" style="margin-bottom: 16px;">
            <span class="panel-title"><i data-feather="pie-chart"></i> Distribusi Karyawan per Divisi</span>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex: 1;">
            <div style="position: relative; width: 140px; height: 140px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <canvas id="divisionDoughnutChart" style="width: 100%; height: 100%;"></canvas>
                <div style="position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; text-align: center;">
                    <span style="font-size: 20px; font-weight: 800; color: var(--text-main); line-height: 1.1;">{{ $totalEmployees }}</span>
                    <span style="font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-top: 1px; letter-spacing: 0.5px;">Total</span>
                </div>
            </div>
            
            <div class="division-legend-list" style="display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 0;">
                @foreach($divisionsData as $div)
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12.5px; line-height: 1.2;">
                        <div style="display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $div['color'] }}; display: inline-block; flex-shrink: 0;"></span>
                            <span style="font-weight: 600; color: var(--text-main); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $div['name'] }}</span>
                        </div>
                        <span style="color: var(--text-muted); font-weight: 500; font-size: 11.5px; flex-shrink: 0; margin-left: 8px;">
                            {{ $div['count'] }} <span style="font-size: 10.5px; opacity: 0.8;">({{ $div['percentage'] }}%)</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div> -->

    <!-- Panel 3: Ringkasan Sistem -->
    <div class="admin-panel">
        <div class="panel-header" style="margin-bottom: 12px;">
            <span class="panel-title"><i data-feather="cpu"></i> Ringkasan Sistem</span>
        </div>
        <div style="display: flex; flex-direction: column;">
            <!-- Row 1: Pengguna Aktif -->
            <div class="system-summary-row">
                <div class="system-icon-badge blue"><i data-feather="user-check" style="width: 18px; height: 18px;"></i></div>
                <div style="flex: 1;">
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">User Aktif</div>
                    <div style="font-size: 20px; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $activeSessionsCount }}</div>
                </div>
            </div>

            <!-- Row 2: Aktivitas Hari Ini -->
            <div class="system-summary-row">
                <div class="system-icon-badge orange"><i data-feather="zap" style="width: 18px; height: 18px;"></i></div>
                <div style="flex: 1;">
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">Aktivitas Hari Ini</div>
                    <div style="font-size: 20px; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $totalActivitiesToday }}</div>
                </div>
            </div>

            <!-- Row 3: Penyimpanan Upload -->
            <div class="system-summary-row">
                <div class="system-icon-badge green"><i data-feather="folder" style="width: 18px; height: 18px;"></i></div>
                <div style="flex: 1;">
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">Penyimpanan Upload</div>
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-top: 2px; display: flex; justify-content: space-between; align-items: center;">
                        <span>{{ $usedUploadSizeMB }} MB <span style="font-weight: 500; font-size: 12px; color: var(--text-muted);">/ {{ $uploadQuotaGB }} GB</span></span>
                    </div>
                    <!-- Progress Bar -->
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 6px;">
                        <div style="flex: 1; height: 6px; background: var(--border-color); border-radius: 10px; overflow: hidden;">
                            <div style="width: {{ $storagePercentage }}%; height: 100%; background: #2563eb; border-radius: 10px;"></div>
                        </div>
                        <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); min-width: 28px; text-align: right;">{{ $storagePercentage }}%</span>
                    </div>
                </div>
            </div>

            <!-- Row 4: Ukuran Basis Data -->
            <div class="system-summary-row">
                <div class="system-icon-badge blue"><i data-feather="database" style="width: 18px; height: 18px;"></i></div>
                <div style="flex: 1;">
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">Ukuran Basis Data</div>
                    <div style="font-size: 20px; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $dbSizeMB }} MB</div>
                </div>
            </div>

            <!-- Row 5: Backup Terakhir -->
            <div class="system-summary-row" style="align-items: center;">
                <div class="system-icon-badge blue"><i data-feather="clock" style="width: 18px; height: 18px;"></i></div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">Backup Terakhir</div>
                    <div style="font-size: 13px; font-weight: 700; color: var(--text-main); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $lastBackupFormatted }}</div>
                </div>
                <span class="status-val-badge success" style="margin-left: 8px;">Sukses</span>
            </div>

            <!-- Row 6: Status Sistem -->
            <div class="system-summary-row">
                <div class="system-icon-badge violet"><i data-feather="activity" style="width: 18px; height: 18px;"></i></div>
                <div style="flex: 1;">
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">Status Sistem</div>
                    <span class="status-val-badge success" style="margin-top: 4px;">Normal</span>
                </div>
            </div>
        </div>
    </div>
    <div class="admin-panel">
        <div class="panel-header" style="margin-bottom: 16px;">
            <span class="panel-title"><i data-feather="list"></i> Aktivitas Absensi Terbaru</span>
        </div>
        <div class="activity-feed">
            @forelse($latestActivities as $la)
                <div class="activity-item">
                    <img src="{{ $la['photo_url'] }}" class="activity-avatar" alt="{{ $la['user_name'] }}">
                    <div class="activity-content">
                        <div class="activity-title">{{ $la['user_name'] }}</div>
                        <div class="activity-desc">Melakukan {{ $la['type'] }} ({{ $la['status'] }})</div>
                        <div class="activity-time">{{ $la['date'] }} - Pukul {{ $la['time'] }} WIB</div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: var(--text-muted); padding: 20px; font-size: 13px;">Belum ada aktivitas absensi hari ini.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Bottom Row (2 Columns) -->
<div class="admin-bottom-grid">
    <!-- Panel 4: Karyawan Terbaru -->
    <div class="admin-panel">
        <div class="panel-header" style="margin-bottom: 16px;">
            <span class="panel-title"><i data-feather="users"></i> Karyawan Terbaru</span>
            <a href="{{ route('users.index') }}" style="font-size: 12px; color: #2563eb; text-decoration: none; font-weight: 600;">Lihat Semua</a>
        </div>
        <div class="admin-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>ID</th>
                        <th>Divisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestEmployees as $le)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <img src="{{ $le->photo_url }}" class="avatar-mini" alt="{{ $le->name }}">
                                    <span style="font-weight: 600; color: var(--text-main);">{{ $le->name }}</span>
                                </div>
                            </td>
                            <td style="color: var(--text-muted); font-weight: 500;">{{ $le->nik ?? '-' }}</td>
                            <td style="color: var(--text-main); font-weight: 500;">{{ $le->division->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada karyawan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Panel 5: Aktivitas Terbaru -->
    
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Grafik Presensi Karyawan
    const ctxTrend = document.getElementById('attendanceTrendChart');
    if (ctxTrend) {
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($attendanceTrend, 'label')) !!},
                datasets: [{
                    label: 'Kehadiran Karyawan',
                    data: {!! json_encode(array_column($attendanceTrend, 'count')) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.06)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' },
                        ticks: { stepSize: 2 }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 2. Grafik Jumlah Karyawan per Divisi (Doughnut Chart)
    const ctxDiv = document.getElementById('divisionDoughnutChart');
    if (ctxDiv) {
        const cardBgColor = getComputedStyle(document.documentElement).getPropertyValue('--card-bg').trim() || '#ffffff';
        new Chart(ctxDiv, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_column($divisionsData, 'name')) !!},
                datasets: [{
                    data: {!! json_encode(array_column($divisionsData, 'count')) !!},
                    backgroundColor: {!! json_encode(array_column($divisionsData, 'color')) !!},
                    borderWidth: 3,
                    borderColor: cardBgColor,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' Karyawan';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

@endsection
