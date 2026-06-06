@extends('layouts.app')

@section('title', 'Dasbor Eksekutif Tahunan')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="header-actions" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 class="page-title" style="margin-bottom: 4px;">Dasbor Evaluasi Tahunan Eksekutif</h1>
        <p style="color: var(--text-muted); font-size: 14px;">Ringkasan performa operasional dan kedisiplinan tim Reel Seven</p>
    </div>
    
    <form method="GET" action="{{ route('executive-dashboard') }}" style="display: flex; gap: 12px; align-items: center; background: var(--card-bg); padding: 8px 16px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <i data-feather="calendar" style="width: 18px; color: var(--text-muted);"></i>
        <select name="year" onchange="this.form.submit()" style="border: none; background: transparent; color: var(--text-main); font-weight: 600; font-size: 15px; cursor: pointer; outline: none;">
            @php $currentYear = date('Y'); @endphp
            @for($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

<div class="kpi-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <!-- KPI 1: Event Completion -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="background: var(--status-blue-soft); color: var(--status-blue); padding: 16px; border-radius: 12px;">
                <i data-feather="check-circle" style="width: 28px; height: 28px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600;">Total Event Selesai</p>
                <h3 style="font-size: 32px; font-weight: 700; margin-top: 2px;">{{ $totalCompletedEvents }}</h3>
            </div>
        </div>
        <div style="margin-top: 16px; display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: {{ $eventGrowth >= 0 ? 'var(--status-emerald)' : 'var(--status-rose)' }};">
            <i data-feather="{{ $eventGrowth >= 0 ? 'trending-up' : 'trending-down' }}" style="width: 16px; height: 16px;"></i> 
            <span>{{ abs($eventGrowth) }}% dari tahun sebelumnya</span>
        </div>
    </div>
    
    <!-- KPI 2: Task Completion -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="background: var(--status-emerald-soft); color: var(--status-emerald); padding: 16px; border-radius: 12px;">
                <i data-feather="check-square" style="width: 28px; height: 28px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600;">Penyelesaian Persiapan Event</p>
                <h3 style="font-size: 32px; font-weight: 700; margin-top: 2px;">{{ $taskCompletionRate }}%</h3>
            </div>
        </div>
        <div style="margin-top: 16px; background: var(--bg-color); border-radius: 6px; height: 8px; overflow: hidden;">
            <div style="width: {{ $taskCompletionRate }}%; background: var(--status-emerald); height: 100%; border-radius: 6px; transition: width 1s ease-in-out;"></div>
        </div>
    </div>
    
    <!-- KPI 3: Crew Discipline -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="background: var(--status-amber-soft); color: var(--status-amber); padding: 16px; border-radius: 12px;">
                <i data-feather="clock" style="width: 28px; height: 28px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600;">Kedisiplinan Kru (Tepat Waktu)</p>
                <h3 style="font-size: 32px; font-weight: 700; margin-top: 2px;">{{ $disciplineRate }}%</h3>
            </div>
        </div>
        <div style="margin-top: 16px; background: var(--bg-color); border-radius: 6px; height: 8px; overflow: hidden;">
            <div style="width: {{ $disciplineRate }}%; background: var(--status-amber); height: 100%; border-radius: 6px; transition: width 1s ease-in-out;"></div>
        </div>
    </div>

    <!-- KPI 4: Weekly Report Compliance -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="background: var(--status-indigo-soft); color: var(--status-indigo); padding: 16px; border-radius: 12px;">
                <i data-feather="file-text" style="width: 28px; height: 28px;"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 13px; font-weight: 600;">Kepatuhan Laporan Mingguan</p>
                <h3 style="font-size: 32px; font-weight: 700; margin-top: 2px;">{{ $reportComplianceRate }}%</h3>
            </div>
        </div>
        <div style="margin-top: 16px; background: var(--bg-color); border-radius: 6px; height: 8px; overflow: hidden;">
            <div style="width: {{ $reportComplianceRate }}%; background: var(--status-indigo); height: 100%; border-radius: 6px; transition: width 1s ease-in-out;"></div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 992px) {
        .top-lists-grid {
            grid-template-columns: 1fr !important;
        }
        .header-actions {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .header-actions form {
            width: 100%;
            justify-content: space-between;
        }
    }
    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        .kpi-grid .card {
            padding: 14px !important;
        }
        /* Ubah orientasi isi card menjadi vertikal agar teks tidak berdesakan */
        .kpi-grid .card > div:first-child {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
        /* Perkecil kotak ikon */
        .kpi-grid .card > div:first-child > div:first-child {
            padding: 8px !important;
            border-radius: 8px !important;
        }
        /* Perkecil ukuran feather icons */
        .kpi-grid .card svg {
            width: 18px !important;
            height: 18px !important;
        }
        /* Perkecil teks deskripsi & nilai angka */
        .kpi-grid p {
            font-size: 11px !important;
            line-height: 1.3 !important;
        }
        .kpi-grid h3 {
            font-size: 22px !important;
            margin-top: 0 !important;
        }
        /* Info pelengkap (progress bar / trend text) */
        .kpi-grid .card > div:nth-child(2) {
            margin-top: 12px !important;
            font-size: 10px !important;
        }
        .kpi-grid .card > div:nth-child(2) svg {
            width: 12px !important;
            height: 12px !important;
        }
    }
</style>

<!-- Chart Section -->
<div class="card" style="margin-bottom: 24px; display: flex; flex-direction: column;">
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Tren Volume Event & Produktivitas Bulanan</h3>
    <!-- Fixed height ensures the chart doesn't stretch to match the right column -->
    <div style="height: 350px; position: relative; width: 100%;">
        <canvas id="executiveChart"></canvas>
    </div>
</div>

<!-- Top 5 Lists Section -->
<div class="top-lists-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
    <!-- Terajin -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px dashed var(--border-color);">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--status-emerald);">Top 5 Kru Disiplin</h3>
            <div style="background: var(--status-emerald-soft); padding: 6px; border-radius: 8px; color: var(--status-emerald);">
                <i data-feather="award" style="width: 18px; height: 18px;"></i>
            </div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($topOnTimeUsers as $index => $stat)
            <div style="display: flex; align-items: center; gap: 12px; padding: 10px; background: var(--bg-color); border-radius: 10px; border: 1px solid var(--border-color);">
                <span style="font-weight: 800; color: var(--status-emerald); width: 20px; text-align: center; font-size: 15px;">#{{ $index + 1 }}</span>
                <img src="{{ $stat->user->photo_url }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--status-emerald-border);">
                <div style="flex: 1;">
                    <div style="font-size: 13px; font-weight: 700;">{{ $stat->user->name }}</div>
                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 600;">{{ $stat->total }}x Kehadiran Tepat Waktu</div>
                </div>
            </div>
            @empty
            <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">
                Belum ada data absensi untuk tahun ini.
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Terlambat -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px dashed var(--border-color);">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--status-rose);">Top 5 Kru Sering Terlambat</h3>
            <div style="background: var(--status-rose-soft); padding: 6px; border-radius: 8px; color: var(--status-rose);">
                <i data-feather="alert-triangle" style="width: 18px; height: 18px;"></i>
            </div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($topLateUsers as $index => $stat)
            <div style="display: flex; align-items: center; gap: 12px; padding: 10px; background: var(--bg-color); border-radius: 10px; border: 1px solid var(--border-color);">
                <span style="font-weight: 800; color: var(--status-rose); width: 20px; text-align: center; font-size: 15px;">#{{ $index + 1 }}</span>
                <img src="{{ $stat->user->photo_url }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--status-rose-border);">
                <div style="flex: 1;">
                    <div style="font-size: 13px; font-weight: 700;">{{ $stat->user->name }}</div>
                    <div style="font-size: 11px; color: var(--status-rose); font-weight: 600;">{{ $stat->total }}x Kehadiran Terlambat</div>
                </div>
            </div>
            @empty
            <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">
                Semua kru hadir tepat waktu, luar biasa!
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        const ctx = document.getElementById('executiveChart').getContext('2d');
        
        // Data payload from backend
        const labels = {!! json_encode($chartData['labels']) !!};
        const eventsData = {!! json_encode($chartData['events_count']) !!};
        const productivityData = {!! json_encode($chartData['productivity']) !!};
        
        // CSS Variable Helper function
        const getStyle = (name) => {
            return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        };

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Volume Event (Total Event)',
                        data: eventsData,
                        backgroundColor: getStyle('--status-blue-soft') || 'rgba(37, 99, 235, 0.2)',
                        borderColor: getStyle('--status-blue') || '#2563EB',
                        borderWidth: 2,
                        borderRadius: 6,
                        yAxisID: 'y',
                        order: 2
                    },
                    {
                        label: 'Produktivitas Persiapan (%)',
                        data: productivityData,
                        type: 'line',
                        backgroundColor: getStyle('--status-emerald') || '#10B981',
                        borderColor: getStyle('--status-emerald') || '#10B981',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: getStyle('--status-emerald') || '#10B981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3, // smooth curves
                        yAxisID: 'y1',
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Google Sans Flex', sans-serif", weight: '600' },
                            color: getStyle('--text-main') || '#0F172A'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { family: "'Google Sans Flex', sans-serif", size: 13, weight: 'bold' },
                        bodyFont: { family: "'Google Sans Flex', sans-serif", size: 13 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true,
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Event',
                            font: { family: "'Google Sans Flex', sans-serif", size: 12, weight: '600' },
                            color: getStyle('--text-muted') || '#64748B'
                        },
                        grid: {
                            color: getStyle('--border-color') || '#E5E7EB',
                            drawBorder: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: getStyle('--text-muted') || '#64748B',
                            stepSize: 1
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Produktivitas (%)',
                            font: { family: "'Google Sans Flex', sans-serif", size: 12, weight: '600' },
                            color: getStyle('--text-muted') || '#64748B'
                        },
                        grid: {
                            drawOnChartArea: false, // Don't draw gridlines over the other y-axis
                        },
                        ticks: {
                            color: getStyle('--text-muted') || '#64748B',
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            color: getStyle('--text-main') || '#0F172A',
                            font: { family: "'Google Sans Flex', sans-serif", weight: '500' }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
