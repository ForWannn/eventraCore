@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    /* Stats grid responsiveness */
    .stats-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }
    @media (max-width: 1024px) {
        .stats-summary-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
        }
        .stats-summary-item {
            border-right: none !important;
            padding-right: 0 !important;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
        }
        .stats-summary-item:nth-child(3), .stats-summary-item:nth-child(4) {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }
    }
    @media (max-width: 640px) {
        .stats-summary-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        .stats-summary-item {
            border-right: none !important;
            padding-right: 0 !important;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
        }
        .stats-summary-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }
    }

    /* Custom scrollbar for tables */
    .table-container {
        overflow-x: auto;
    }
    
    /* Pagination hover states */
    .page-btn:hover:not(.disabled):not(.active) {
        background: var(--hover-bg) !important;
    }

    /* Input select clean styling */
    select:focus, input:focus {
        border-color: #2563eb !important;
    }
</style>

<!-- Custom Header -->
<div style="margin-bottom: 28px;">
    <!-- <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Riwayat Absensi</h1> -->
    <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px; font-weight: 500;">Lihat catatan kehadiran dan absensi Anda.</p>
</div>

<!-- Statistics Cards -->
<div class="stats-summary-card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 18px; padding: 20px 24px; margin-bottom: 28px;">
    <div class="stats-summary-grid">
        <!-- Card 1: Total Hari Kerja -->
        <div class="stats-summary-item" style="display: flex; align-items: center; gap: 16px; border-right: 1px solid var(--border-color); padding-right: 16px;">
            <div class="stats-summary-icon blue" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(37,99,235,0.06); border: 1.5px solid rgba(37,99,235,0.15); color: #2563eb;">
                <i data-feather="calendar" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600; line-height: 1;">Total Hari Kerja</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--text-main); margin-top: 4px; display: flex; align-items: baseline; gap: 4px; line-height: 1;">
                    {{ $stats['workdays'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">hari</span>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">Bulan {{ \Carbon\Carbon::parse($filters['start_date'])->locale('id')->translatedFormat('F Y') }}</div>
            </div>
        </div>
        <!-- Card 2: Hadir -->
        <div class="stats-summary-item" style="display: flex; align-items: center; gap: 16px; border-right: 1px solid var(--border-color); padding-right: 16px;">
            <div class="stats-summary-icon emerald" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(16,185,129,0.06); border: 1.5px solid rgba(16,185,129,0.15); color: #10b981;">
                <i data-feather="check-circle" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600; line-height: 1;">Hadir</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--text-main); margin-top: 4px; display: flex; align-items: baseline; gap: 4px; line-height: 1;">
                    {{ $stats['hadir'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">hari</span>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">{{ $stats['hadir_pct'] }}% dari total</div>
            </div>
        </div>
        <!-- Card 3: Terlambat -->
        <div class="stats-summary-item" style="display: flex; align-items: center; gap: 16px; border-right: 1px solid var(--border-color); padding-right: 16px;">
            <div class="stats-summary-icon rose" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(239,68,68,0.06); border: 1.5px solid rgba(239,68,68,0.15); color: #ef4444;">
                <i data-feather="alert-circle" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600; line-height: 1;">Terlambat</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--text-main); margin-top: 4px; display: flex; align-items: baseline; gap: 4px; line-height: 1;">
                    {{ $stats['terlambat'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">hari</span>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">{{ $stats['terlambat_pct'] }}% dari total</div>
            </div>
        </div>
        <!-- Card 4: Tidak Hadir -->
        <div class="stats-summary-item" style="display: flex; align-items: center; gap: 16px;">
            <div class="stats-summary-icon amber" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(245,158,11,0.06); border: 1.5px solid rgba(245,158,11,0.15); color: #f59e0b;">
                <i data-feather="slash" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600; line-height: 1;">Tidak Hadir</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--text-main); margin-top: 4px; display: flex; align-items: baseline; gap: 4px; line-height: 1;">
                    {{ $stats['tidak_hadir'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">hari</span>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">{{ $stats['tidak_hadir_pct'] }}% dari total</div>
            </div>
        </div>
    </div>
</div>

<!-- History Container -->
<div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 28px;">
    <!-- Filters & Form -->
    <form id="filterForm" method="GET" action="{{ route('attendance.history') }}" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: center; margin-bottom: 24px; width: 100%;">
        <!-- Date Range Picker -->
        <div class="filter-date-wrapper" style="position: relative; display: flex; align-items: center; gap: 8px; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; min-height: 38px;">
            <i data-feather="calendar" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
            <span id="dateRangeDisplay">{{ $dateRangeString }}</span>
            <i data-feather="chevron-down" style="width: 15px; height: 15px; color: var(--text-muted); margin-left: 8px;"></i>
            <input type="text" id="flatpickr-range" style="position: absolute; inset: 0; opacity: 0; cursor: pointer;" />
            <input type="hidden" name="start_date" id="start_date" value="{{ $filters['start_date'] }}" />
            <input type="hidden" name="end_date" id="end_date" value="{{ $filters['end_date'] }}" />
        </div>

        <!-- Status Filter -->
        <div style="position: relative;">
            <select name="status" id="statusSelect" onchange="this.form.submit()" style="appearance: none; -webkit-appearance: none; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 36px 8px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; outline: none; min-height: 38px;">
                <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="hadir" {{ $filters['status'] == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="terlambat" {{ $filters['status'] == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="tidak_hadir" {{ $filters['status'] == 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
            </select>
            <i data-feather="chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted); pointer-events: none;"></i>
        </div>

        <!-- Search Input -->
        <div style="position: relative; flex: 1; min-width: 200px;">
            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Cari tanggal atau status..." style="width: 100%; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 14px 8px 36px; border-radius: 10px; font-size: 13px; font-weight: 500; color: var(--text-main); outline: none; min-height: 38px;" />
            <i data-feather="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted);"></i>
        </div>

        <!-- Download Button -->
        <button type="button" onclick="exportData()" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; transition: background 0.2s; min-height: 38px;">
            <i data-feather="download" style="width: 15px; height: 15px; color: var(--text-muted);"></i>
            <span>Unduh</span>
        </button>
    </form>

    <!-- Table -->
    <div class="table-container">
        <table class="attendance-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="text-align: left; border-bottom: 1.5px solid var(--border-color);">
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Tanggal</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Jam Masuk</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Status</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Keterlambatan</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Metode</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Lokasi</th>
                    <th style="padding: 14px 16px; font-weight: 600; color: var(--text-muted);">Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 16px; font-weight: 500; color: var(--text-main);">
                            {{ $att['day_name'] }}
                        </td>
                        <td style="padding: 16px; font-weight: 600; color: {{ $att['status'] === 'Terlambat' ? 'var(--danger)' : 'var(--text-main)' }};">
                            {{ $att['check_in'] }}
                        </td>
                        <td style="padding: 16px;">
                            @if($att['status'] === 'Hadir')
                                <span style="background: rgba(16,185,129,0.1); color: #10b981; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Hadir</span>
                            @elseif($att['status'] === 'Terlambat')
                                <span style="background: rgba(239,68,68,0.1); color: #ef4444; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Terlambat</span>
                            @else
                                <span style="background: rgba(244,63,94,0.1); color: #f43f5e; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Tidak Hadir</span>
                            @endif
                        </td>
                        <td style="padding: 16px; font-weight: 500; color: {{ $att['status'] === 'Terlambat' ? 'var(--warning)' : 'var(--text-muted)' }};">
                            {{ $att['lateness'] }}
                        </td>
                        <td style="padding: 16px; color: var(--text-main); font-weight: 500;">
                            @if($att['attendance_type'] === 'kantor')
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="home" style="width: 14px; height: 14px; color: var(--text-muted);"></i> Kantor
                                </span>
                            @elseif($att['attendance_type'] === 'luar')
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="globe" style="width: 14px; height: 14px; color: var(--text-muted);"></i> Luar (Web)
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="geo-location-cell" style="padding: 16px; color: var(--text-main); font-weight: 500;" data-lat="{{ $att['latitude'] }}" data-lng="{{ $att['longitude'] }}" data-type="{{ $att['attendance_type'] }}">
                            @if($att['attendance_type'] === 'kantor')
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="map-pin" style="width: 14px; height: 14px; color: var(--text-muted);"></i> Jakarta
                                </span>
                            @elseif($att['attendance_type'] === 'luar' && $att['latitude'])
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="map-pin" style="width: 14px; height: 14px; color: var(--text-muted);"></i> <span class="loc-text">Loading...</span>
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($att['photo_path'])
                                <img src="{{ asset('storage/' . $att['photo_path']) }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1.5px solid var(--border-color); cursor: pointer;" onclick="viewFullImage('{{ asset('storage/' . $att['photo_path']) }}')">
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            Belum ada catatan absensi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="custom-pagination-container" style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; flex-wrap: wrap; gap: 16px;">
        <!-- Records Count -->
        <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">
            Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari {{ $attendances->total() }} data
        </div>

        <!-- Page Links -->
        <div class="pagination-buttons" style="display: flex; align-items: center; gap: 8px;">
            @if ($attendances->onFirstPage())
                <span class="page-btn disabled" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-muted); cursor: not-allowed; opacity: 0.5;">
                    <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                </span>
            @else
                <a href="{{ $attendances->previousPageUrl() }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; transition: all 0.2s;">
                    <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                </a>
            @endif

            @foreach ($attendances->getUrlRange(max(1, $attendances->currentPage() - 2), min($attendances->lastPage(), $attendances->currentPage() + 2)) as $page => $url)
                @if ($page == $attendances->currentPage())
                    <span class="page-btn active" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #2563eb; color: white; font-weight: 600; font-size: 13px;">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            @if ($attendances->hasMorePages())
                <a href="{{ $attendances->nextPageUrl() }}" class="page-btn" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; transition: all 0.2s;">
                    <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                </a>
            @else
                <span class="page-btn disabled" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-muted); cursor: not-allowed; opacity: 0.5;">
                    <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                </span>
            @endif
        </div>

        <!-- Page Size Select -->
        <div style="position: relative;">
            <select id="perPageSelect" name="per_page" onchange="updatePerPage(this.value)" style="appearance: none; -webkit-appearance: none; background: var(--input-bg); border: 1px solid var(--border-color); padding: 8px 32px 8px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer; outline: none; min-height: 38px;">
                <option value="7" {{ $filters['per_page'] == 7 ? 'selected' : '' }}>7 / halaman</option>
                <option value="15" {{ $filters['per_page'] == 15 ? 'selected' : '' }}>15 / halaman</option>
                <option value="30" {{ $filters['per_page'] == 30 ? 'selected' : '' }}>30 / halaman</option>
            </select>
            <i data-feather="chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted); pointer-events: none;"></i>
        </div>
    </div>
</div>

<!-- Bottom Guidelines Alert -->
<div style="display: flex; gap: 12px; background: rgba(37,99,235,0.06); border: 1.5px solid rgba(37,99,235,0.15); border-radius: 12px; padding: 16px; margin-top: 24px; align-items: flex-start;">
    <i data-feather="info" style="width: 18px; height: 18px; color: #2563eb; flex-shrink: 0; margin-top: 2px;"></i>
    <div>
        <h4 style="font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Keterangan Keterlambatan</h4>
        <p style="font-size: 12px; color: var(--text-muted); font-weight: 500; line-height: 1.4;">
            Keterlambatan dihitung dari waktu masuk setelah jadwal masuk kantor (08:00 WIB).
        </p>
    </div>
</div>

<!-- Full Image Modal -->
<div id="fullImageModal" style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.85); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;" onclick="closeFullImageModal()">
    <div style="position: relative; max-width: 90%; max-height: 90%;" onclick="event.stopPropagation()">
        <button onclick="closeFullImageModal()" style="position: absolute; top: -35px; right: 0; background: none; border: none; color: white; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;">
            <i data-feather="x" style="width: 16px; height: 16px;"></i> Tutup
        </button>
        <img id="modalImg" src="" style="max-width: 100%; max-height: 80vh; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); object-fit: contain;">
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();

        // Flatpickr Range Initialization
        const startDateVal = document.getElementById("start_date").value;
        const endDateVal = document.getElementById("end_date").value;

        flatpickr("#flatpickr-range", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [startDateVal, endDateVal],
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    const startStr = selectedDates[0].toLocaleDateString('sv-SE'); // YYYY-MM-DD
                    const endStr = selectedDates[1].toLocaleDateString('sv-SE');
                    
                    document.getElementById("start_date").value = startStr;
                    document.getElementById("end_date").value = endStr;
                    document.getElementById("filterForm").submit();
                }
            }
        });

        // Geocoding cells
        const geoCells = document.querySelectorAll(".geo-location-cell");
        const geoCache = {};

        geoCells.forEach(cell => {
            const type = cell.getAttribute("data-type");
            const lat = cell.getAttribute("data-lat");
            const lng = cell.getAttribute("data-lng");
            const textEl = cell.querySelector(".loc-text");

            if (type === "luar" && lat && lng && textEl) {
                const cacheKey = `${lat},${lng}`;
                if (geoCache[cacheKey]) {
                    textEl.textContent = geoCache[cacheKey];
                } else {
                    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                        .then(r => r.json())
                        .then(data => {
                            const addr = data.address || {};
                            const city = addr.city || addr.town || addr.village || addr.city_district || addr.county || "Luar Kantor";
                            geoCache[cacheKey] = city;
                            textEl.textContent = city;
                        })
                        .catch(() => {
                            textEl.textContent = `${parseFloat(lat).toFixed(3)}, ${parseFloat(lng).toFixed(3)}`;
                        });
                }
            }
        });
    });

    function updatePerPage(val) {
        const form = document.getElementById('filterForm');
        let perPageInput = form.querySelector('input[name="per_page"]');
        if (!perPageInput) {
            perPageInput = document.createElement('input');
            perPageInput.type = 'hidden';
            perPageInput.name = 'per_page';
            form.appendChild(perPageInput);
        }
        perPageInput.value = val;
        form.submit();
    }

    function viewFullImage(src) {
        const modal = document.getElementById('fullImageModal');
        const img = document.getElementById('modalImg');
        img.src = src;
        modal.style.display = 'flex';
        // replace feather icons inside modal if any
        setTimeout(() => feather.replace(), 50);
    }

    function closeFullImageModal() {
        document.getElementById('fullImageModal').style.display = 'none';
    }

    function exportData() {
        let csv = [];
        let rows = document.querySelectorAll("table.attendance-table tr");
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            
            for (let j = 0; j < cols.length; j++) {
                // Omit photo column (last column)
                if (j === cols.length - 1) continue;
                let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
                row.push('"' + text.replace(/"/g, '""') + '"');
            }
            csv.push(row.join(","));
        }
        
        let csvContent = "data:text/csv;charset=utf-8,\uFEFF" + csv.join("\n");
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "riwayat_absensi_{{ Auth::user()->name }}.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
