@extends('layouts.app')

@section('title', 'Detail Event')

@section('content')
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 28px;
        }

        .info-box {
            padding: 14px 16px;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            background: var(--hover-bg);
        }

        .info-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 500;
        }

        /* PIC hero */
        .pic-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: var(--hover-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .pic-profile {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .pic-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--border-color);
        }

        .pic-name {
            font-size: 15px;
            font-weight: 600;
        }

        .pic-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .badge-pic {
            display: inline-block;
            padding: 4px 10px;
            background: #fef08a;
            color: #854d0e;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .fee-pill {
            padding: 6px 14px;
            background: #dcfce7;
            color: #166534;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Position blocks */
        .position-section {
            margin-bottom: 20px;
        }

        .position-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px 14px 0 0;
            border-bottom: none;
        }

        .position-name {
            font-size: 14px;
            font-weight: 700;
        }

        .position-fee {
            font-size: 13px;
            font-weight: 600;
            color: #166534;
            background: #dcfce7;
            padding: 4px 12px;
            border-radius: 8px;
        }

        .position-members {
            border: 1px solid var(--border-color);
            border-radius: 0 0 14px 14px;
            padding: 16px;
            background: var(--hover-bg);
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }

        .member-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 16px 12px;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            background: var(--sidebar-bg);
        }

        .member-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid var(--border-color);
        }

        .member-name {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 2px;
        }

        .member-div {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .work-dates {
            font-size: 10px;
            font-weight: 600;
            padding: 4px 8px;
            background: var(--hover-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-main);
            display: inline-block;
        }

        /* Danger Zone & Modal */
        .danger-zone {
            margin-top: 40px;
            padding: 24px;
            border: 1px dashed #fca5a5;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(254, 226, 226, 0.2);
        }

        .danger-zone-text h5 {
            font-size: 15px;
            font-weight: 700;
            color: #b91c1c;
            margin-bottom: 4px;
        }

        .danger-zone-text p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .btn-danger {
            padding: 10px 20px;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-danger:hover {
            background: #fecaca;
            border-color: #f87171;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-box {
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 32px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-box h4 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .modal-box p {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-secondary {
            padding: 10px 20px;
            background: var(--hover-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-confirm-delete {
            padding: 10px 20px;
            background: #b91c1c;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        /* Absensi WebRTC */
        .att-section {
            background: var(--hover-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 32px 24px;
            margin-bottom: 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .att-section h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .att-section p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .camera-wrapper {
            position: relative;
            width: 100%;
            max-width: 280px;
            aspect-ratio: 3/4;
            border-radius: 16px;
            overflow: hidden;
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        #videoElement {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transform: scaleX(-1);
        }

        #photoPreview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            transform: scaleX(-1);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 28px;
            background: var(--text-main);
            color: var(--bg-color);
            border: none;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-action:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }

        .btn-action:disabled {
            background: var(--border-color);
            color: var(--text-muted);
            cursor: not-allowed;
            transform: none;
        }

        .btn-outline {
            background: var(--sidebar-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-outline:hover {
            background: var(--border-color);
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .attendance-table th {
            color: var(--text-muted);
            font-weight: 600;
        }

        .att-photo-sm {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .att-photo-sm:hover {
            transform: scale(1.5);
            position: relative;
            z-index: 10;
        }

        /* Task Management Styles - Optimized Typography & Spacing */
        .task-card {
            margin-top: 24px;
            padding: 20px;
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .task-progress-container {
            margin-bottom: 20px;
            background: var(--hover-bg);
            padding: 14px 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .task-progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .task-progress-bar {
            height: 8px;
            background: var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }

        .task-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            transition: width 0.6s ease;
        }

        .task-section-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        @media (max-width: 1024px) {
            .task-section-grid { grid-template-columns: 1fr; }
        }

        .task-column {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .task-column-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 2px 0;
        }

        .task-column-header h5 {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .task-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .task-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .task-item:hover { 
            border-color: #4f46e5; 
        }
        
        .task-item.completed { 
            background: var(--hover-bg); 
            opacity: 0.7; 
        }
        
        .task-item.completed .task-text { 
            text-decoration: line-through; 
            color: var(--text-muted); 
        }

        .task-checkbox {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid var(--border-color);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .task-checkbox.checked { 
            background: #4f46e5; 
            border-color: #4f46e5; 
            color: #fff; 
        }

        .task-text {
            font-size: 12.5px;
            font-weight: 500;
            flex: 1;
            line-height: 1.4;
        }

        .task-type-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .task-official { background: #e0f2fe; color: #0369a1; }
        .task-personal { background: #f3f4f6; color: #374151; }

        .task-assignee {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }

        .quick-add-container { 
            position: relative; 
            margin-top: 4px; 
        }
        
        .quick-add-input {
            width: 100%;
            padding: 8px 12px 8px 32px;
            border: 1px dashed var(--border-color);
            border-radius: 8px;
            font-size: 12.5px;
            background: transparent;
            transition: all 0.2s;
        }
        
        .quick-add-input:focus {
            border-style: solid;
            border-color: #4f46e5;
            background: #fff;
            outline: none;
        }
        
        .quick-add-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            font-weight: bold;
        }

        .task-delete-btn { 
            opacity: 0; 
            color: #ef4444; 
            cursor: pointer; 
            padding: 2px; 
            display: flex;
            align-items: center;
        }
        
        .task-item:hover .task-delete-btn { 
            opacity: 1; 
        }

        .btn-add-official {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 8px;
            background: rgba(79, 70, 229, 0.05);
            color: #4f46e5;
            border: 1px dashed #4f46e5;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 4px;
        }
        .btn-add-official:hover { 
            background: rgba(79, 70, 229, 0.1); 
        }
    </style>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
            <div>
                <h3 style="margin-bottom: 6px;">{{ $event->name }}</h3>
                @if($event->description)
                    <p style="color: var(--text-muted); font-size: 13px;">{{ $event->description }}</p>
                @endif
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="badge-{{ $event->status }}" style="padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700; text-transform: uppercase;
                                                  @if($event->status === 'upcoming') background:#dbeafe;color:#1e3a8a;
                                                  @elseif($event->status === 'ongoing') background:#fef08a;color:#854d0e;
                                                  @else background:#dcfce7;color:#166534; @endif">
                    {{ $event->status }}
                </span>
                <a href="{{ route('events.index') }}"
                    style="color:var(--text-muted);text-decoration:none;font-size:13px; font-weight: 500;">← Kembali</a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="info-box" style="grid-column: 1 / -1;">
                <div class="info-label">Tanggal Event</div>
                <div class="info-value">
                    @php
                        $dates = $event->event_dates ?? [];
                        if (count($dates) > 0) {
                            sort($dates);
                            $displayDates = collect($dates)->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M Y'))->implode(', ');
                            echo $displayDates;
                        } else {
                            echo '-';
                        }
                    @endphp
                </div>
            </div>
            @if($event->start_time && $event->end_time)
                <div class="info-box">
                    <div class="info-label">Jam Mulai</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Jam Selesai</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</div>
                </div>
            @endif
            <div class="info-box">
                <div class="info-label">Absensi Dibuka</div>
                <div class="info-value">
                    {{ $event->attendance_start ? \Carbon\Carbon::parse($event->attendance_start)->format('H:i') : 'Otomatis' }}
                </div>
            </div>
            <div class="info-box">
                <div class="info-label">Absensi Ditutup</div>
                <div class="info-value">
                    {{ $event->attendance_end ? \Carbon\Carbon::parse($event->attendance_end)->format('H:i') : 'Selesai Event' }}
                </div>
            </div>
        </div>

        @if($isLeader)
            <div class="detail-grid" style="margin-top: 16px;">
                <div class="info-box" style="background: #fffbeb; border-color: #fde68a;">
                    <div class="info-label" style="color: #92400e;">Fee Loading (Global)</div>
                    <div class="info-value">Rp {{ number_format($event->loading_fee, 0, ',', '.') }}</div>
                </div>
                <div class="info-box" style="background: #eef2ff; border-color: #c7d2fe;">
                    <div class="info-label" style="color: #3730a3;">Fee Unloading (Global)</div>
                    <div class="info-value">Rp {{ number_format($event->unloading_fee, 0, ',', '.') }}</div>
                </div>
            </div>
        @endif

        <hr style="border:0;border-top:1px dashed var(--border-color);margin:32px 0;">

        @if($isAssigned && $event->needs_attendance)
            <div class="att-section" id="attendanceSection">
                <h4>Absensi Kehadiran</h4>

                @if($myAttendance)
                    <p style="margin-bottom: 16px;">Sistem telah mencatat kehadiran Anda.</p>
                    <div
                        style="display: inline-flex; align-items: center; gap: 8px; background: #dcfce7; color: #166534; padding: 8px 16px; border-radius: 99px; font-size: 13px; font-weight: 600; margin-bottom: 20px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Hadir pada {{ $myAttendance->attended_at->format('H:i - d M Y') }}
                    </div>
                    @if($myAttendance->photo_path)
                        <img src="{{ asset($myAttendance->photo_path) }}" alt="Bukti Absen"
                            style="width: 140px; height: 140px; object-fit: cover; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    @endif
                @else
                    @if($event->status === 'ongoing')
                        @if($attendanceOpen)
                            <p>Pastikan Anda telah mengizinkan akses kamera dan lokasi, posisikan wajah di area kamera lalu tekan tombol ambil foto.</p>
                            <div class="camera-wrapper" id="cameraWrapper">
                                <video id="videoElement" autoplay playsinline></video>
                                <img id="photoPreview" alt="Preview Absen">
                                <canvas id="canvasElement" style="display: none;"></canvas>
                            </div>
                            <div id="locStatus" style="font-size: 13px; color: #b91c1c; margin-bottom: 16px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                <span id="locText">Meminta akses lokasi GPS...</span>
                            </div>
                            <div id="cameraControls">
                                <button class="btn-action" id="btnCapture" onclick="takePhoto()" disabled style="opacity: 0.6; cursor: not-allowed;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="13" r="4" />
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                    </svg>
                                    Ambil Foto
                                </button>
                            </div>
                            <div id="submitControls" style="display: none; gap: 12px; justify-content: center;">
                                <button class="btn-action btn-outline" onclick="retakePhoto()">Ulangi</button>
                                <button class="btn-action" id="btnSubmitAtt" onclick="submitAttendance()">Kirim Data</button>
                            </div>
                        @else
                            <p style="color: #b91c1c; font-weight: 500;">Masa absensi saat ini ditutup. Sistem absensi hari ini hanya dibuka
                                pada jam {{ \Carbon\Carbon::parse($event->attendance_start)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($event->attendance_end)->format('H:i') }}.
                            </p>
                        @endif
                    @elseif($event->status === 'upcoming')
                        <p>Kamera absensi akan otomatis aktif saat status event berubah menjadi <strong>Ongoing</strong>.</p>
                    @else
                        <p style="color: #b91c1c;">Event telah selesai. Periode absensi telah ditutup.</p>
                    @endif
                @endif
            </div>
            <hr style="border:0;border-top:1px dashed var(--border-color);margin:32px 0;">
        @endif

        @php $pic = $event->participants->where('pivot.is_pic', true)->first(); @endphp
        @if($pic)
            <div
                style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:16px;">
                PIC Event</div>
            <div class="pic-section">
                <div class="pic-profile">
                    <img src="{{ $pic->photo_url }}" class="pic-avatar" alt="{{ $pic->name }}">
                    <div>
                        <div class="pic-name">{{ $pic->name }}</div>
                        <div class="pic-label">{{ $pic->division->name ?? 'Internal' }} &nbsp;·&nbsp; <span
                                class="badge-pic">PIC</span></div>
                    </div>
                </div>
                @if($isLeader)
                    <div class="fee-pill">Fee: Rp {{ number_format($event->pic_fee, 0, ',', '.') }}</div>
                @endif
            </div>
        @endif

        <div
            style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-top:40px;margin-bottom:20px;">
            Struktur Tim & Penugasan
        </div>

        @forelse($event->positions as $position)
            <div class="position-section">
                <div class="position-header">
                    <div class="position-name">{{ $position->name }} <span
                            style="font-weight:400; color:var(--text-muted); margin-left:8px;">({{ $position->members->count() }}
                            orang)</span></div>

                    @if($isLeader)
                        <div class="position-fee">Fee: Rp {{ number_format($position->fee, 0, ',', '.') }} / orang</div>
                    @endif
                </div>
                <div class="position-members">
                    @forelse($position->members as $member)
                        <div class="member-card">
                            <img src="{{ $member->photo_url }}" class="member-avatar" alt="{{ $member->name }}">
                            <div class="member-name">{{ $member->name }}</div>

                            <div style="display: flex; gap: 4px; margin-top: 4px; margin-bottom: 8px;">
                                @if($member->pivot->is_loading)
                                    <span
                                        style="font-size: 10px; background: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 4px; font-weight: 700;"
                                        title="Bertugas Loading">Load</span>
                                @endif
                                @if($member->pivot->is_unloading)
                                    <span
                                        style="font-size: 10px; background: #e0e7ff; color: #3730a3; padding: 2px 6px; border-radius: 4px; font-weight: 700;"
                                        title="Bertugas Unloading">Unload</span>
                                @endif
                            </div>

                            @php
                                $wDates = is_string($member->pivot->work_dates) ? json_decode($member->pivot->work_dates, true) : ($member->pivot->work_dates ?? []);
                            @endphp
                            @if(is_array($wDates) && count($wDates) > 0)
                                <div class="work-dates">
                                    {{ collect($wDates)->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->implode(', ') }}
                                </div>
                            @else
                                <div class="work-dates" style="opacity: 0.5;">Full Event</div>
                            @endif
                        </div>
                    @empty
                        <div style="grid-column: 1/-1; text-align: center; color: var(--text-muted); font-size: 13px;">Belum ada
                            anggota yang ditugaskan.</div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="info-box" style="text-align:center; padding: 40px;">
                <p style="color:var(--text-muted);">Belum ada posisi tim yang dibuat untuk event ini.</p>
            </div>
        @endforelse

        @if($isLeader || $isPic)
            <div style="margin-top: 40px; margin-bottom: 20px;">
                <div
                    style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:16px;">
                    Rekapitulasi Kehadiran
                </div>
                <div style="border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden;">
                    <table class="attendance-table">
                        <thead style="background: var(--hover-bg);">
                            <tr>
                                <th>Karyawan</th>
                                <th>Waktu Hadir</th>
                                <th>Metode</th>
                                <th>Bukti / Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($event->attendances as $att)
                                <tr>
                                    <td style="font-weight: 500;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <img src="{{ $att->user->photo_url }}"
                                                style="width: 28px; height: 28px; border-radius: 50%; object-fit:cover;">
                                            {{ $att->user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $att->attended_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        @if($att->method === 'camera')
                                            <span
                                                style="background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                Kamera</span>
                                        @else
                                            <span
                                                style="background: #f3f4f6; color: #4b5563; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">Manual
                                                oleh PIC</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($att->method === 'camera' && $att->photo_path)
                                            <a href="{{ asset($att->photo_path) }}" target="_blank">
                                                <img src="{{ asset($att->photo_path) }}" class="att-photo-sm" alt="Bukti Absen">
                                            </a>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 12px;">{{ $att->notes ?? '-' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada
                                        data absensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- TASK MANAGEMENT SECTION --}}
        @if($isAssigned || $isLeader)
        <div class="task-card">
            <div class="task-progress-container">
                <div class="task-progress-header">
                    <div>
                        <h4 style="margin:0; font-size: 16px;">Checklist Event</h4>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 20px; font-weight: 800; color: #4f46e5;" id="progress-percentage">{{ $event->official_tasks_percentage }}%</span>
                    </div>
                </div>
                <div class="task-progress-bar">
                    <div class="task-progress-fill" id="progress-fill" style="width: {{ $event->official_tasks_percentage }}%"></div>
                </div>
            </div>

            <div class="task-section-grid">
                @php
                    $categories = [
                        'pre' => ['label' => 'Pre Event', 'icon' => '📝', 'bg' => 'transparent'],
                        'dday' => ['label' => 'Day', 'icon' => '⚡', 'bg' => 'transparent'],
                        'post' => ['label' => 'Post Event', 'icon' => '🏁', 'bg' => 'transparent']
                    ];
                @endphp

                @foreach($categories as $catKey => $catInfo)
                <div class="task-column">
                    <div class="task-column-header">
                        <span style="font-size: 14px;">{{ $catInfo['icon'] }}</span>
                        <h5 style="margin:0; font-size: 14px; font-weight: 700;">{{ $catInfo['label'] }}</h5>
                    </div>

                    <div class="task-list" id="task-list-{{ $catKey }}">
                        {{-- Official Tasks first --}}
                        @foreach($event->tasks->where('category', $catKey)->where('type', 'official')->sortBy('is_completed') as $task)
                            @include('events.partials.task_item', ['task' => $task])
                        @endforeach

                        {{-- Personal Tasks (only for the current user) --}}
                        @php
                            $personalTasks = $event->tasks->where('category', $catKey)->where('type', 'personal')->sortBy('is_completed');
                            if (!$isLeader && !$isPic) {
                                $personalTasks = $personalTasks->where('assigned_to', auth()->id());
                            }
                        @endphp

                        @foreach($personalTasks as $task)
                            @include('events.partials.task_item', ['task' => $task])
                        @endforeach
                    </div>

                    <div class="quick-add-container">
                        <span class="quick-add-icon">+</span>
                        <input type="text" 
                               class="quick-add-input" 
                               placeholder="Tambah To Do" 
                               onkeypress="handleQuickAdd(event, '{{ $catKey }}', 'personal')">
                    </div>
                    
                    @if($isPic || $isLeader)
                    <div>
                        <button onclick="openOfficialTaskModal('{{ $catKey }}')" class="btn-add-official">
                            <i data-feather="plus" style="width: 12px; height: 12px;"></i> Buat To Do Resmi
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($isLeader)
            <div class="danger-zone">
                <div class="danger-zone-text">
                    <h5>Hapus Seluruh Data Event</h5>
                    <p>Menghapus event akan membatalkan seluruh jadwal penugasan dan riwayat absensi terkait.</p>
                </div>
                <button type="button" class="btn-danger" onclick="openDeleteModal()">Hapus Event</button>
            </div>
        @endif
    </div>

    <div class="modal-overlay" id="officialTaskModal">
        <div class="modal-box" style="max-width: 450px;">
            <h4 id="official-modal-title">Tambah To Do</h4>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">To Do ini merupakan target utama dan akan mempengaruhi progres indikator event.</p>
            
            <form id="officialTaskForm">
                <input type="hidden" id="official-category" name="category">
                <input type="hidden" name="type" value="official">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:8px;">NAMA TO DO</label>
                    <input type="text" name="task_name" required 
                           style="width:100%; padding:12px; border:1px solid var(--border-color); border-radius:12px; font-size:14px;"
                           placeholder="Contoh: Dokumentasi Hari Event">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:8px;">TUGASKAN KE (OPSIONAL)</label>
                    <select name="assigned_to" style="width:100%; padding:12px; border:1px solid var(--border-color); border-radius:12px; font-size:14px; background:var(--bg-color);">
                        <option value="">-- Umum / Seluruh Tim --</option>
                        @foreach($assignedUsers as $participant)
                            <option value="{{ $participant->id }}">{{ $participant->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeOfficialTaskModal()">Batal</button>
                    <button type="submit" class="btn-primary" style="padding: 10px 24px; border-radius: 12px; border:none; background:#4f46e5; color:#fff; font-weight:600; cursor:pointer;">Simpan To Do</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <h4>Konfirmasi Penghapusan</h4>
            <p>Apakah Anda yakin ingin menghapus event <strong>{{ $event->name }}</strong>? Data yang sudah dihapus tidak
                dapat dipulihkan kembali.</p>
            <div class="modal-actions">
                <button class="btn-secondary" onclick="closeDeleteModal()">Batal</button>
                <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-confirm-delete">Hapus Event</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() { document.getElementById('deleteModal').classList.add('open'); }
        function closeDeleteModal() { document.getElementById('deleteModal').classList.remove('open'); }
        
        function openOfficialTaskModal(category) {
            const labels = { 'pre': 'Pre-Event', 'dday': 'D-Day', 'post': 'Post-Event' };
            document.getElementById('official-modal-title').innerText = 'Tambah To Do - ' + labels[category];
            document.getElementById('official-category').value = category;
            document.getElementById('officialTaskModal').classList.add('open');
        }

        function closeOfficialTaskModal() {
            document.getElementById('officialTaskModal').classList.remove('open');
            document.getElementById('officialTaskForm').reset();
        }

        async function handleQuickAdd(event, category, type) {
            if (event.key === 'Enter' && event.target.value.trim() !== '') {
                const name = event.target.value.trim();
                event.target.disabled = true;
                
                try {
                    const response = await fetch(`{{ route('events.tasks.store', $event->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ task_name: name, category, type })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        event.target.value = '';
                        appendTaskToUI(data.task, category);
                        updateProgressBar(data.completion_percentage);
                    }
                } catch (err) {
                    console.error("Gagal menambah tugas:", err);
                } finally {
                    event.target.disabled = false;
                    event.target.focus();
                }
            }
        }

        document.getElementById('officialTaskForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const body = Object.fromEntries(formData.entries());
            
            if (!body.assigned_to) {
                body.assigned_to = null;
            }
            
            try {
                const response = await fetch(`{{ route('events.tasks.store', $event->id) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(body)
                });
                
                const data = await response.json();
                if (data.success) {
                    appendTaskToUI(data.task, body.category);
                    updateProgressBar(data.completion_percentage);
                    closeOfficialTaskModal();
                } else {
                    alert(data.error || "Gagal menyimpan To Do");
                }
            } catch (err) {
                console.error("Gagal menambah To Do resmi:", err);
            }
        };

        async function toggleTask(taskId) {
            const checkbox = document.getElementById(`checkbox-${taskId}`);
            const item = document.getElementById(`task-item-${taskId}`);
            
            try {
                const response = await fetch(`/event-tasks/${taskId}/toggle`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                
                if (data.success) {
                    checkbox.classList.toggle('checked');
                    item.classList.toggle('completed');
                    checkbox.innerHTML = data.is_completed ? `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>` : '';
                    updateProgressBar(data.completion_percentage);
                }
            } catch (err) {
                console.error("Gagal toggle tugas:", err);
            }
        }

        async function deleteTask(taskId) {
            if (!confirm('Hapus tugas ini?')) return;
            
            try {
                const response = await fetch(`/event-tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById(`task-item-${taskId}`).remove();
                    updateProgressBar(data.completion_percentage);
                }
            } catch (err) {
                console.error("Gagal menghapus tugas:", err);
            }
        }

        function appendTaskToUI(task, category) {
            const list = document.getElementById(`task-list-${category}`);
            const div = document.createElement('div');
            div.id = `task-item-${task.id}`;
            div.className = `task-item ${task.is_completed ? 'completed' : ''}`;
            
            const assigneeHtml = task.assignee ? `<img src="${task.assignee.photo_url}" class="task-assignee" title="Tugas: ${task.assignee.name}">` : '';
            const typeLabel = task.type === 'official' ? 'To Do' : 'Personal';
            
            div.innerHTML = `
                <div class="task-checkbox ${task.is_completed ? 'checked' : ''}" onclick="toggleTask('${task.id}')" id="checkbox-${task.id}">
                    ${task.is_completed ? `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>` : ''}
                </div>
                <div class="task-text">${task.task_name}</div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="task-type-badge task-${task.type}">${typeLabel}</span>
                    ${assigneeHtml}
                    <span class="task-delete-btn" onclick="deleteTask('${task.id}')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </span>
                </div>
            `;
            list.appendChild(div);
        }

        function updateProgressBar(percentage) {
            const fill = document.getElementById('progress-fill');
            const text = document.getElementById('progress-percentage');
            if (fill) fill.style.width = percentage + '%';
            if (text) text.innerText = percentage + '%';
        }

        window.addEventListener('click', function (event) {
            const delModal = document.getElementById('deleteModal');
            const offModal = document.getElementById('officialTaskModal');
            if (event.target == delModal) closeDeleteModal();
            if (event.target == offModal) closeOfficialTaskModal();
        });
    </script>

    @if($isAssigned && $event->needs_attendance && !$myAttendance && $event->status === 'ongoing' && $attendanceOpen)
        <script>
            const video = document.getElementById('videoElement');
            const canvas = document.getElementById('canvasElement');
            const photoPreview = document.getElementById('photoPreview');
            const btnCapture = document.getElementById('btnCapture');
            const cameraControls = document.getElementById('cameraControls');
            const submitControls = document.getElementById('submitControls');
            const locStatus = document.getElementById('locStatus');
            const locText = document.getElementById('locText');
            
            let stream = null;
            let photoBlob = null;
            let currentLat = null;
            let currentLon = null;
            let currentAddress = "Lokasi tidak diketahui";

            async function startCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                    video.srcObject = stream;
                    getLocation();
                } catch (err) {
                    console.error("Akses kamera ditolak / error:", err);
                    document.getElementById('cameraWrapper').innerHTML = `<p style="color:#b91c1c; padding:40px 20px;">Gagal mengakses kamera. Pastikan memberikan izin pada browser.</p>`;
                    btnCapture.disabled = true;
                    locStatus.style.display = 'none';
                }
            }

            function getLocation() {
                if (!navigator.geolocation) {
                    locText.innerText = "Browser Anda tidak mendukung geolokasi.";
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        currentLat = position.coords.latitude;
                        currentLon = position.coords.longitude;
                        locText.innerText = `Menerjemahkan koordinat (${currentLat.toFixed(4)}, ${currentLon.toFixed(4)})...`;
                        
                        try {
                            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${currentLat}&lon=${currentLon}&zoom=18&addressdetails=1`);
                            const data = await res.json();
                            if(data && data.display_name) {
                                currentAddress = data.display_name;
                            } else {
                                currentAddress = `Lat: ${currentLat.toFixed(5)}, Lon: ${currentLon.toFixed(5)}`;
                            }
                        } catch(e) {
                            currentAddress = `Lat: ${currentLat.toFixed(5)}, Lon: ${currentLon.toFixed(5)}`;
                        }

                        locStatus.style.color = "#166534";
                        locText.innerText = currentAddress;
                        btnCapture.disabled = false;
                        btnCapture.style.opacity = 1;
                        btnCapture.style.cursor = 'pointer';
                    },
                    (error) => {
                        let msg = "Gagal mengakses lokasi.";
                        if (error.code === error.PERMISSION_DENIED) msg = "Akses lokasi ditolak! Anda WAJIB menyalakan GPS untuk absen.";
                        locText.innerText = msg;
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }

            function formatLocalDate() {
                const now = new Date();
                const d = now.getDate().toString().padStart(2, '0');
                const mNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
                const m = mNames[now.getMonth()];
                const y = now.getFullYear();
                const h = now.getHours().toString().padStart(2, '0');
                const min = now.getMinutes().toString().padStart(2, '0');
                return `${d} ${m} ${y} - ${h}:${min}`;
            }

            function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
                const words = text.split(' ');
                let line = '';
                let lines = [];
                for(let n = 0; n < words.length; n++) {
                    let testLine = line + words[n] + ' ';
                    let metrics = ctx.measureText(testLine);
                    let testWidth = metrics.width;
                    if (testWidth > maxWidth && n > 0) {
                        lines.push(line);
                        line = words[n] + ' ';
                    } else {
                        line = testLine;
                    }
                }
                lines.push(line);
                return lines;
            }

            function takePhoto() {
                const vW = video.videoWidth;
                const vH = video.videoHeight;
                canvas.width = vW;
                canvas.height = vH;

                const ctx = canvas.getContext('2d');
                
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);

                const rectHeight = 150;
                ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                ctx.fillRect(0, canvas.height - rectHeight, canvas.width, rectHeight);

                ctx.fillStyle = '#ffffff';
                ctx.textBaseline = 'top';
                
                const paddingX = 16;
                const startY = canvas.height - rectHeight + 12;

                ctx.font = 'bold 16px sans-serif';
                ctx.fillText('{{ config("app.name") }} Geotag Absensi', paddingX, startY);

                ctx.font = 'bold 14px monospace';
                ctx.fillStyle = '#fef08a';
                const dateTimeStr = formatLocalDate();
                ctx.fillText(dateTimeStr, paddingX, startY + 24);

                ctx.font = '13px sans-serif';
                ctx.fillStyle = '#ffffff';
                const maxWidth = canvas.width - (paddingX * 2);
                const addressLines = wrapText(ctx, currentAddress, paddingX, startY + 46, maxWidth, 18);
                
                let curY = startY + 46;
                for (let i = 0; i < addressLines.length; i++) {
                    ctx.fillText(addressLines[i], paddingX, curY);
                    curY += 18;
                    if(i === 3) break; 
                }

                if (currentLat !== null) {
                    ctx.font = '11px monospace';
                    ctx.fillStyle = '#d1d5db';
                    ctx.fillText(`${currentLat.toFixed(5)}, ${currentLon.toFixed(5)}`, paddingX, curY + 2);
                }

                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                photoPreview.src = dataUrl;

                video.style.display = 'none';
                photoPreview.style.display = 'block';
                cameraControls.style.display = 'none';
                submitControls.style.display = 'flex';

                fetch(dataUrl).then(res => res.blob()).then(blob => photoBlob = blob);
            }

            function retakePhoto() {
                photoPreview.style.display = 'none';
                video.style.display = 'block';
                cameraControls.style.display = 'block';
                submitControls.style.display = 'none';
                photoBlob = null;
            }

            function submitAttendance() {
                if (!photoBlob) return;
                const btnSubmit = document.getElementById('btnSubmitAtt');
                btnSubmit.disabled = true;
                btnSubmit.innerText = "Mengirim...";

                const formData = new FormData();
                formData.append('photo', photoBlob, 'absen.jpg');
                formData.append('_token', '{{ csrf_token() }}');

                fetch(`{{ route('attendances.store', $event->id) }}`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (stream) stream.getTracks().forEach(track => track.stop());
                            window.location.reload();
                        } else {
                            alert(data.error || "Terjadi kesalahan.");
                            btnSubmit.disabled = false;
                            btnSubmit.innerText = "Kirim Data";
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mengirim data. Coba lagi.');
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = "Kirim Data";
                    });
            }

            window.addEventListener('load', startCamera);
        </script>
    @endif
@endsection