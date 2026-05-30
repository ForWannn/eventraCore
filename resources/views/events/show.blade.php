@extends('layouts.app')

@section('title', 'Detail Event')

@section('content')
    <style>
        /* Modern layouts */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 24px;
        }
        @media (max-width: 1024px) {
            .info-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .info-grid { grid-template-columns: 1fr; }
        }

        .info-box-new {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            padding: 16px;
            border-radius: 14px;
        }

        .info-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(37, 99, 235, 0.06);
            border: 1px solid rgba(37, 99, 235, 0.15);
            color: #2563eb;
            flex-shrink: 0;
        }

        /* Accordion row styling */
        .member-row-header {
            cursor: pointer;
            transition: background 0.15s;
        }
        .member-row-header:hover {
            background: var(--hover-bg);
        }
        .chevron-icon {
            transition: transform 0.2s;
        }
        .member-row-header.expanded .chevron-icon {
            transform: rotate(180deg);
        }

        /* Checklist boxes */
        .checklist-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            min-height: 38px;
            transition: all 0.2s;
        }
        .checklist-box.pre {
            background: rgba(37, 99, 235, 0.03);
            border: 1px solid rgba(37, 99, 235, 0.15);
            color: #2563eb;
        }
        .checklist-box.pre:hover {
            background: rgba(37, 99, 235, 0.07);
        }
        .checklist-box.dday {
            background: rgba(245, 158, 11, 0.03);
            border: 1px solid rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }
        .checklist-box.dday:hover {
            background: rgba(245, 158, 11, 0.07);
        }
        .checklist-box.post {
            background: rgba(139, 92, 246, 0.03);
            border: 1px solid rgba(139, 92, 246, 0.15);
            color: #8b5cf6;
        }
        .checklist-box.post:hover {
            background: rgba(139, 92, 246, 0.07);
        }

        /* Task items */
        .task-item-new {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 6px;
            background: var(--card-bg);
            transition: all 0.2s;
        }
        .task-item-new:hover {
            border-color: #4f46e5;
        }
        .task-item-new.completed {
            background: var(--hover-bg);
            opacity: 0.7;
        }
        .task-checkbox-new {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid var(--border-color);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .task-checkbox-new.checked {
            background: #4f46e5;
            border-color: #4f46e5;
            color: white;
        }

        /* Danger zone box */
        .danger-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border: 1px dashed #fca5a5;
            border-radius: 16px;
            background: rgba(254, 226, 226, 0.15);
            margin-top: 32px;
        }

        /* Webcam Absensi Section */
        .att-section-new {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 28px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .camera-wrapper {
            position: relative;
            width: 100%;
            max-width: 280px;
            aspect-ratio: 3/4;
            border-radius: 16px;
            overflow: hidden;
            background: var(--sidebar-bg);
            border: 1.5px solid var(--border-color);
            margin: 16px 0;
        }
        #videoElement, #photoPreview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 24px;
            background: var(--text-main);
            color: var(--bg-color);
            border: none;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-action:hover { opacity: 0.85; }
        .btn-action:disabled { background: var(--border-color); color: var(--text-muted); cursor: not-allowed; }

        /* Modals styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
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
            max-width: 450px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .modal-box h4 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-main);
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
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            background: var(--border-color);
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
            transition: opacity 0.2s;
        }
        .btn-confirm-delete:hover {
            opacity: 0.9;
        }
    </style>

    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); letter-spacing: -0.5px; margin: 0;">Detail Event</h1>
        </div>
        <a href="{{ route('events.index') }}" style="display: flex; align-items: center; gap: 8px; background: var(--card-bg); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--text-main); text-decoration: none; transition: background 0.2s;">
            <i data-feather="arrow-left" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- 1. Top Card: Event Main Info -->
    <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 24px; margin-bottom: 28px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: rgba(37,99,235,0.06); border: 1.5px solid rgba(37,99,235,0.15); color: #2563eb; flex-shrink: 0;">
                    <i data-feather="calendar" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Nama Event</span>
                    <h2 style="font-size: 20px; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $event->name }}</h2>
                </div>
            </div>
            <div>
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 99px; font-size: 12px; font-weight: 700; text-transform: uppercase;
                    @if($event->status === 'upcoming') background: #eff6ff; border: 1px solid #dbeafe; color: #2563eb;
                    @elseif($event->status === 'ongoing') background: #fffbeb; border: 1px solid #fef3c7; color: #d97706;
                    @else background: #ecfdf5; border: 1px solid #d1fae5; color: #10b981; @endif">
                    {{ $event->status }}
                </span>
            </div>
        </div>

        @if($event->description)
            <div style="margin-top: 16px; padding: 12px 16px; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; font-size: 13px; color: var(--text-muted); font-weight: 500; line-height: 1.5;">
                {{ $event->description }}
            </div>
        @endif

        @php
            $dates = $event->event_dates ?? [];
            $dateValue = '-';
            $dateSub = '';
            if (count($dates) > 0) {
                sort($dates);
                $firstDate = \Carbon\Carbon::parse($dates[0]);
                if (count($dates) === 1) {
                    $dateValue = $firstDate->locale('id')->translatedFormat('d F Y');
                    $dateSub = $firstDate->locale('id')->translatedFormat('l');
                } else {
                    $lastDate = \Carbon\Carbon::parse(end($dates));
                    $dateValue = $firstDate->locale('id')->translatedFormat('d M') . ' - ' . $lastDate->locale('id')->translatedFormat('d M Y');
                    $dateSub = $firstDate->locale('id')->translatedFormat('D') . ' - ' . $lastDate->locale('id')->translatedFormat('D');
                }
            }

            $durationStr = '-';
            $durationSub = '';
            if ($event->start_time && $event->end_time) {
                $start = \Carbon\Carbon::parse($event->start_time);
                $end = \Carbon\Carbon::parse($event->end_time);
                $diff = $start->diffInMinutes($end);
                $hours = floor($diff / 60);
                $minutes = $diff % 60;
                
                $durationStr = $hours . ' Jam';
                $durationSub = $minutes . ' Menit';
            }
        @endphp

        <!-- Grid Event Details -->
        <div class="info-grid">
            <div class="info-box-new">
                <div class="info-icon-wrapper"><i data-feather="calendar" style="width: 16px; height: 16px;"></i></div>
                <div>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Tanggal Event</span>
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $dateValue }}</div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px; font-weight: 500;">{{ $dateSub }}</div>
                </div>
            </div>

            <div class="info-box-new">
                <div class="info-icon-wrapper"><i data-feather="clock" style="width: 16px; height: 16px;"></i></div>
                <div>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Jam Mulai</span>
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-top: 2px;">
                        {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '-' }}
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px; font-weight: 500;">WIB</div>
                </div>
            </div>

            <div class="info-box-new">
                <div class="info-icon-wrapper"><i data-feather="clock" style="width: 16px; height: 16px;"></i></div>
                <div>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Jam Selesai</span>
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-top: 2px;">
                        {{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '-' }}
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px; font-weight: 500;">WIB</div>
                </div>
            </div>

            <div class="info-box-new">
                <div class="info-icon-wrapper"><i data-feather="watch" style="width: 16px; height: 16px;"></i></div>
                <div>
                    <span style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Durasi</span>
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $durationStr }}</div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px; font-weight: 500;">{{ $durationSub }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Geotag Absensi Camera Card (Only when assigned, needs attendance, not checked in, ongoing, and attendance open) -->
    @if($isAssigned && $event->needs_attendance && !$myAttendance && $event->status === 'ongoing' && $attendanceOpen)
        <div class="att-section-new" id="attendanceSection" style="margin-bottom: 28px; border: 1.5px solid var(--border-color); border-radius: 20px;">
            <h4 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Absensi Kehadiran Geotag</h4>
            <p style="font-size: 12px; color: var(--text-muted); max-width: 480px; margin-bottom: 16px;">
                Pastikan Anda telah mengizinkan akses kamera dan lokasi browser, posisikan wajah di area kamera lalu tekan tombol ambil foto.
            </p>
            <div class="camera-wrapper">
                <video id="videoElement" autoplay playsinline></video>
                <img id="photoPreview" alt="Preview Absen" style="display: none;">
                <canvas id="canvasElement" style="display: none;"></canvas>
            </div>
            <div id="locStatus" style="font-size: 13px; color: #b91c1c; margin-bottom: 16px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i data-feather="map-pin" style="width: 16px; height: 16px;"></i>
                <span id="locText">Meminta akses lokasi GPS...</span>
            </div>
            <div id="cameraControls">
                <button class="btn-action" id="btnCapture" onclick="takePhoto()" disabled style="opacity: 0.6; cursor: not-allowed;">
                    <i data-feather="camera" style="width: 16px; height: 16px;"></i> Ambil Foto
                </button>
            </div>
            <div id="submitControls" style="display: none; gap: 12px; justify-content: center;">
                <button class="btn-action btn-outline" onclick="retakePhoto()" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main);">Ulangi</button>
                <button class="btn-action" id="btnSubmitAtt" onclick="submitAttendance()" style="background: #10b981; color: white;">Kirim Absensi</button>
            </div>
        </div>
    @elseif($isAssigned && $event->needs_attendance && $myAttendance)
        <div class="att-section-new" style="margin-bottom: 28px; border: 1px solid var(--border-color); border-radius: 20px;">
            <h4 style="font-size: 15px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Absensi Kehadiran</h4>
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); color: #10b981; padding: 6px 16px; border-radius: 99px; font-size: 13px; font-weight: 600; margin-bottom: 16px;">
                <i data-feather="check" style="width: 16px; height: 16px;"></i>
                Hadir pada {{ $myAttendance->attended_at->locale('id')->format('H:i - d M Y') }}
            </div>
            @if($myAttendance->photo_path)
                <img src="{{ asset($myAttendance->photo_path) }}" alt="Bukti Absen" style="width: 110px; height: 110px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.05); cursor: pointer;" onclick="viewFullImage('{{ asset($myAttendance->photo_path) }}')">
            @endif
        </div>
    @endif

    <!-- 2. PIC Event -->
    @if($pic)
        <div style="margin-bottom: 28px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">PIC Event</h3>
            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 18px; padding: 16px 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <img src="{{ $pic->photo_url }}" style="width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2.5px solid var(--border-color);" alt="{{ $pic->name }}">
                        <div>
                            <div style="font-size: 15px; font-weight: 700; color: var(--text-main);">{{ $pic->name }}</div>
                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                                <span style="font-size: 11px; font-weight: 600; color: var(--text-muted);">{{ $pic->division->name ?? 'Internal' }}</span>
                                <span style="width: 4px; height: 4px; border-radius: 50%; background: var(--border-color);"></span>
                                <span style="display: inline-block; padding: 2px 8px; background: #fef3c7; color: #d97706; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase;">PIC</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @php
        // Construct Crew list
        $crewMembers = collect();
        if ($pic) {
            $crewMembers->push([
                'user' => $pic,
                'role' => 'PIC',
                'pivot' => $pic->pivot
            ]);
        }
        foreach ($event->positions as $position) {
            foreach ($position->members as $member) {
                if ($pic && $member->id == $pic->id) continue; // avoid duplication of PIC
                $crewMembers->push([
                    'user' => $member,
                    'role' => $position->name,
                    'pivot' => $member->pivot
                ]);
            }
        }
        // Unique crew
        $crewMembers = $crewMembers->unique(fn($c) => $c['user']->id);
    @endphp

    <!-- 3. Struktur Tim & Penugasan -->
    <div style="margin-bottom: 28px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0;">Tim</h3>
            <span style="background: rgba(37,99,235,0.08); color: #2563eb; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 99px;">
                Jumlah Crew &nbsp;·&nbsp; {{ $crewMembers->count() }} orang
            </span>
        </div>

        <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 18px; padding: 20px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                        <th style="padding: 12px 16px; font-weight: 600;">Nama Crew</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Peran</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Tanggal Event</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($crewMembers as $crew)
                        @php
                            $member = $crew['user'];
                            $role = $crew['role'];
                            $pivot = $crew['pivot'];
                            $wDates = is_string($pivot->work_dates) ? json_decode($pivot->work_dates, true) : ($pivot->work_dates ?? []);
                        @endphp
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 16px; display: flex; align-items: center; gap: 12px;">
                                <img src="{{ $member->photo_url }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--border-color);">
                                <span style="font-weight: 600; color: var(--text-main);">{{ $member->name }}</span>
                            </td>
                            <td style="padding: 14px 16px; color: var(--text-muted); font-weight: 500;">
                                {{ $role }}
                            </td>
                            <td style="padding: 14px 16px; color: var(--text-main); font-weight: 500;">
                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                    <i data-feather="calendar" style="width: 14px; height: 14px; color: var(--text-muted);"></i>
                                    @if(is_array($wDates) && count($wDates) > 0)
                                        {{ collect($wDates)->map(fn($d) => \Carbon\Carbon::parse($d)->locale('id')->translatedFormat('d M Y'))->implode(', ') }}
                                    @else
                                        Full Event
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 24px; color: var(--text-muted);">Belum ada crew bertugas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Checklist Event (Accordion) -->
    @if($isAssigned || $isLeader)
        <div style="margin-bottom: 28px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0;">Checklist Event</h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Catatan tugas per penanggung jawab.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Completion</span>
                    <span style="font-size: 16px; font-weight: 800; color: #2563eb;" id="progress-percentage">{{ $event->official_tasks_percentage }}%</span>
                    <div style="width: 100px; height: 6px; border-radius: 99px; background: var(--border-color); overflow: hidden;">
                        <div id="progress-fill" style="height: 100%; background: #2563eb; width: {{ $event->official_tasks_percentage }}%; transition: width 0.3s ease;"></div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div style="display: flex; gap: 16px; font-size: 12px; font-weight: 600; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 6px; color: var(--text-muted);">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb; display: inline-block;"></span>
                    Pre Event
                </div>
                <div style="display: flex; align-items: center; gap: 6px; color: var(--text-muted);">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                    Day
                </div>
                <div style="display: flex; align-items: center; gap: 6px; color: var(--text-muted);">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6; display: inline-block;"></span>
                    Post Event
                </div>
            </div>

            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 18px; padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            <th style="padding: 12px 16px; font-weight: 600; width: 35%;">Posisi</th>
                            <th style="padding: 12px 16px; font-weight: 600; width: 21%;">H-</th>
                            <th style="padding: 12px 16px; font-weight: 600; width: 21%;">H</th>
                            <th style="padding: 12px 16px; font-weight: 600; width: 21%;">H+</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($crewMembers as $crew)
                            @php
                                $member = $crew['user'];
                                $role = $crew['role'];

                                // Filter tasks per member
                                $memberPreTasks = $event->tasks->where('category', 'pre')->filter(function($t) use ($member, $pic) {
                                    if ($t->assigned_to !== null) return $t->assigned_to == $member->id;
                                    return $pic && $member->id == $pic->id;
                                });
                                $memberPreCompleted = $memberPreTasks->where('is_completed', true)->count();
                                $memberPreTotal = $memberPreTasks->count();

                                $memberDdayTasks = $event->tasks->where('category', 'dday')->filter(function($t) use ($member, $pic) {
                                    if ($t->assigned_to !== null) return $t->assigned_to == $member->id;
                                    return $pic && $member->id == $pic->id;
                                });
                                $memberDdayCompleted = $memberDdayTasks->where('is_completed', true)->count();
                                $memberDdayTotal = $memberDdayTasks->count();

                                $memberPostTasks = $event->tasks->where('category', 'post')->filter(function($t) use ($member, $pic) {
                                    if ($t->assigned_to !== null) return $t->assigned_to == $member->id;
                                    return $pic && $member->id == $pic->id;
                                });
                                $memberPostCompleted = $memberPostTasks->where('is_completed', true)->count();
                                $memberPostTotal = $memberPostTasks->count();
                            @endphp
                            <tr class="member-row-header" id="member-header-{{ $member->id }}" onclick="toggleMemberRow('{{ $member->id }}')" style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 16px; display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img src="{{ $member->photo_url }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--border-color);">
                                        <div>
                                            <span style="font-weight: 600; color: var(--text-main);">{{ $member->name }}</span>
                                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 2px;">{{ $role }}</div>
                                        </div>
                                    </div>
                                    <i data-feather="chevron-down" class="chevron-icon" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                                </td>
                                <td style="padding: 16px;">
                                    <div class="checklist-box pre">
                                        <span style="display: flex; align-items: center; gap: 6px;">
                                            <i data-feather="check-square" style="width: 14px; height: 14px;"></i>
                                            <span id="count-{{ $member->id }}-pre" data-completed="{{ $memberPreCompleted }}" data-total="{{ $memberPreTotal }}">{{ $memberPreCompleted }} / {{ $memberPreTotal }} tugas</span>
                                        </span>
                                        <i data-feather="chevron-right" style="width: 12px; height: 12px;"></i>
                                    </div>
                                </td>
                                <td style="padding: 16px;">
                                    <div class="checklist-box dday">
                                        <span style="display: flex; align-items: center; gap: 6px;">
                                            <i data-feather="check-square" style="width: 14px; height: 14px;"></i>
                                            <span id="count-{{ $member->id }}-dday" data-completed="{{ $memberDdayCompleted }}" data-total="{{ $memberDdayTotal }}">{{ $memberDdayCompleted }} / {{ $memberDdayTotal }} tugas</span>
                                        </span>
                                        <i data-feather="chevron-right" style="width: 12px; height: 12px;"></i>
                                    </div>
                                </td>
                                <td style="padding: 16px;">
                                    <div class="checklist-box post">
                                        <span style="display: flex; align-items: center; gap: 6px;">
                                            <i data-feather="check-square" style="width: 14px; height: 14px;"></i>
                                            <span id="count-{{ $member->id }}-post" data-completed="{{ $memberPostCompleted }}" data-total="{{ $memberPostTotal }}">{{ $memberPostCompleted }} / {{ $memberPostTotal }} tugas</span>
                                        </span>
                                        <i data-feather="chevron-right" style="width: 12px; height: 12px;"></i>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Accordion Panel row -->                            <tr class="expanded-row-panel" id="expanded-row-{{ $member->id }}" style="display: none; background: var(--bg-color);">
                                <td colspan="4" style="padding: 24px; border-bottom: 1px solid var(--border-color);">
                                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                                        
                                        <!-- Column Pre Event -->
                                        <div>
                                            <h5 style="font-size: 13px; font-weight: 700; color: #2563eb; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Pre Event</h5>
                                            <div id="task-list-{{ $member->id }}-pre" style="display: flex; flex-direction: column; gap: 6px;">
                                                @forelse($memberPreTasks as $task)
                                                    <div class="task-item-new {{ $task->is_completed ? 'completed' : '' }}" id="task-item-{{ $task->id }}">
                                                        <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                            <div class="task-checkbox-new {{ $task->is_completed ? 'checked' : '' }}" onclick="toggleTask('{{ $task->id }}', '{{ $member->id }}', 'pre')" id="checkbox-{{ $task->id }}">
                                                                @if($task->is_completed)
                                                                    <i data-feather="check" style="width: 12px; height: 12px; color: white;"></i>
                                                                @endif
                                                            </div>
                                                            <span class="task-text-span" style="font-size: 13px; font-weight: 500; color: var(--text-main); text-decoration: {{ $task->is_completed ? 'line-through' : 'none' }}; line-height: 1.3;">{{ $task->task_name }}</span>
                                                        </div>
                                                        <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0; margin-left: 8px;">
                                                            @if($task->created_by === auth()->id() || $task->assigned_to === auth()->id() || $isPic || $isLeader)
                                                                <span onclick="deleteTask('{{ $task->id }}', '{{ $member->id }}', 'pre', {{ $task->is_completed ? 'true' : 'false' }})" style="color: #ef4444; cursor: pointer; padding: 2px; display: inline-flex; align-items: center;">
                                                                    <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="empty-state" style="font-size: 12px; color: var(--text-muted); font-weight: 500; padding: 8px 0;">Tidak ada tugas.</div>
                                                @endforelse
                                            </div>
 
                                            <!-- Create Task Input (Role Restricted: CEO/GM/PIC can add for anyone, Crew for self) -->
                                            @if($isLeader || $isPic || auth()->id() == $member->id)
                                                <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                                                    <div style="position: relative;">
                                                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; font-weight: bold; pointer-events: none;">+</span>
                                                        <input type="text" class="quick-add-input" placeholder="Tambah To Do" onkeypress="handleQuickAddInput(event, 'pre', '{{ $member->id }}')" style="width: 100%; padding: 8px 12px 8px 30px; border: 1px dashed var(--border-color); border-radius: 8px; font-size: 12.5px; background: transparent; outline: none; transition: border 0.2s;" />
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
 
                                        <!-- Column Day Event -->
                                        <div>
                                            <h5 style="font-size: 13px; font-weight: 700; color: #f59e0b; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Day</h5>
                                            <div id="task-list-{{ $member->id }}-dday" style="display: flex; flex-direction: column; gap: 6px;">
                                                @forelse($memberDdayTasks as $task)
                                                    <div class="task-item-new {{ $task->is_completed ? 'completed' : '' }}" id="task-item-{{ $task->id }}">
                                                        <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                            <div class="task-checkbox-new {{ $task->is_completed ? 'checked' : '' }}" onclick="toggleTask('{{ $task->id }}', '{{ $member->id }}', 'dday')" id="checkbox-{{ $task->id }}">
                                                                @if($task->is_completed)
                                                                    <i data-feather="check" style="width: 12px; height: 12px; color: white;"></i>
                                                                @endif
                                                            </div>
                                                            <span class="task-text-span" style="font-size: 13px; font-weight: 500; color: var(--text-main); text-decoration: {{ $task->is_completed ? 'line-through' : 'none' }}; line-height: 1.3;">{{ $task->task_name }}</span>
                                                        </div>
                                                        <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0; margin-left: 8px;">
                                                            @if($task->created_by === auth()->id() || $task->assigned_to === auth()->id() || $isPic || $isLeader)
                                                                <span onclick="deleteTask('{{ $task->id }}', '{{ $member->id }}', 'dday', {{ $task->is_completed ? 'true' : 'false' }})" style="color: #ef4444; cursor: pointer; padding: 2px; display: inline-flex; align-items: center;">
                                                                    <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="empty-state" style="font-size: 12px; color: var(--text-muted); font-weight: 500; padding: 8px 0;">Tidak ada tugas.</div>
                                                @endforelse
                                            </div>
 
                                            @if($isLeader || $isPic || auth()->id() == $member->id)
                                                <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                                                    <div style="position: relative;">
                                                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; font-weight: bold; pointer-events: none;">+</span>
                                                        <input type="text" class="quick-add-input" placeholder="Tambah To Do" onkeypress="handleQuickAddInput(event, 'dday', '{{ $member->id }}')" style="width: 100%; padding: 8px 12px 8px 30px; border: 1px dashed var(--border-color); border-radius: 8px; font-size: 12.5px; background: transparent; outline: none; transition: border 0.2s;" />
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
 
                                        <!-- Column Post Event -->
                                        <div>
                                            <h5 style="font-size: 13px; font-weight: 700; color: #8b5cf6; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Post Event</h5>
                                            <div id="task-list-{{ $member->id }}-post" style="display: flex; flex-direction: column; gap: 6px;">
                                                @forelse($memberPostTasks as $task)
                                                    <div class="task-item-new {{ $task->is_completed ? 'completed' : '' }}" id="task-item-{{ $task->id }}">
                                                        <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                            <div class="task-checkbox-new {{ $task->is_completed ? 'checked' : '' }}" onclick="toggleTask('{{ $task->id }}', '{{ $member->id }}', 'post')" id="checkbox-{{ $task->id }}">
                                                                @if($task->is_completed)
                                                                    <i data-feather="check" style="width: 12px; height: 12px; color: white;"></i>
                                                                @endif
                                                            </div>
                                                            <span class="task-text-span" style="font-size: 13px; font-weight: 500; color: var(--text-main); text-decoration: {{ $task->is_completed ? 'line-through' : 'none' }}; line-height: 1.3;">{{ $task->task_name }}</span>
                                                        </div>
                                                        <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0; margin-left: 8px;">
                                                            @if($task->created_by === auth()->id() || $task->assigned_to === auth()->id() || $isPic || $isLeader)
                                                                <span onclick="deleteTask('{{ $task->id }}', '{{ $member->id }}', 'post', {{ $task->is_completed ? 'true' : 'false' }})" style="color: #ef4444; cursor: pointer; padding: 2px; display: inline-flex; align-items: center;">
                                                                    <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="empty-state" style="font-size: 12px; color: var(--text-muted); font-weight: 500; padding: 8px 0;">Tidak ada tugas.</div>
                                                @endforelse
                                            </div>
 
                                            @if($isLeader || $isPic || auth()->id() == $member->id)
                                                <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                                                    <div style="position: relative;">
                                                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; font-weight: bold; pointer-events: none;">+</span>
                                                        <input type="text" class="quick-add-input" placeholder="Tambah To Do" onkeypress="handleQuickAddInput(event, 'post', '{{ $member->id }}')" style="width: 100%; padding: 8px 12px 8px 30px; border: 1px dashed var(--border-color); border-radius: 8px; font-size: 12.5px; background: transparent; outline: none; transition: border 0.2s;" />
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
 
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- 5. Danger Zone: Hapus Event -->
    @if($isLeader)
        <div class="danger-card">
            <div>
                <h4 style="font-size: 15px; font-weight: 700; color: #b91c1c; margin: 0;">Hapus Event</h4>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px; margin-bottom: 0;">
                    Menghapus event akan membatalkan seluruh jadwal penugasan dan riwayat terkait secara permanen.
                </p>
            </div>
            <button type="button" class="btn-danger" onclick="openDeleteModal()" style="background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-weight: 700; padding: 10px 20px; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                Hapus Event
            </button>
        </div>
    @endif

    <!-- Modals -->
    <!-- Confirm Delete Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <h4 style="color:#b91c1c;">Konfirmasi Penghapusan</h4>
            <p>Apakah Anda yakin ingin menghapus event <strong>{{ $event->name }}</strong>? Data yang sudah dihapus tidak dapat dipulihkan kembali.</p>
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

    <!-- Full Image Viewer Modal -->
    <div id="fullImageModal" style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.85); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;" onclick="closeFullImageModal()">
        <div style="position: relative; max-width: 90%; max-height: 90%;" onclick="event.stopPropagation()">
            <button onclick="closeFullImageModal()" style="position: absolute; top: -35px; right: 0; background: none; border: none; color: white; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                <i data-feather="x" style="width: 16px; height: 16px;"></i> Tutup
            </button>
            <img id="modalImg" src="" style="max-width: 100%; max-height: 80vh; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); object-fit: contain;">
        </div>
    </div>

    <script>
        function openDeleteModal() { document.getElementById('deleteModal').classList.add('open'); }
        function closeDeleteModal() { document.getElementById('deleteModal').classList.remove('open'); }

        // Accordion toggle helper
        function toggleMemberRow(memberId) {
            const header = document.getElementById(`member-header-${memberId}`);
            const panel = document.getElementById(`expanded-row-${memberId}`);
            
            if (header.classList.contains('expanded')) {
                header.classList.remove('expanded');
                panel.style.display = 'none';
                sessionStorage.removeItem('expanded_member_row');
            } else {
                // Collapse any active row first to keep it clean (optional, but let's just close others)
                document.querySelectorAll('.member-row-header').forEach(h => {
                    h.classList.remove('expanded');
                });
                document.querySelectorAll('.expanded-row-panel').forEach(p => {
                    p.style.display = 'none';
                });

                header.classList.add('expanded');
                panel.style.display = 'table-row';
                sessionStorage.setItem('expanded_member_row', memberId);
            }
        }
        // Re-expand stored row after DOM load
        document.addEventListener("DOMContentLoaded", function() {
            feather.replace();
            
            const expandedMemberId = sessionStorage.getItem('expanded_member_row');
            if (expandedMemberId) {
                const header = document.getElementById(`member-header-${expandedMemberId}`);
                const panel = document.getElementById(`expanded-row-${expandedMemberId}`);
                if (header && panel) {
                    header.classList.add('expanded');
                    panel.style.display = 'table-row';
                }
            }
        });

        // Helper to update the global progress bar
        function updateGlobalProgressBar(percentage) {
            const percentageSpan = document.getElementById('progress-percentage');
            const progressFill = document.getElementById('progress-fill');
            if (percentageSpan) {
                percentageSpan.innerText = `${percentage}%`;
            }
            if (progressFill) {
                progressFill.style.width = `${percentage}%`;
            }
        }

        // Helper to render task HTML dynamically
        function renderTaskItem(task, isPicOrLeader, currentUserId) {
            const isCompleted = task.is_completed;
            
            const div = document.createElement('div');
            div.className = `task-item-new ${isCompleted ? 'completed' : ''}`;
            div.id = `task-item-${task.id}`;
            
            // Left content wrapper
            const leftContainer = document.createElement('div');
            leftContainer.style.display = 'flex';
            leftContainer.style.alignItems = 'center';
            leftContainer.style.gap = '10px';
            leftContainer.style.flex = '1';
            leftContainer.style.minWidth = '0';
            
            // Checkbox
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = `task-checkbox-new ${isCompleted ? 'checked' : ''}`;
            checkboxDiv.id = `checkbox-${task.id}`;
            checkboxDiv.setAttribute('onclick', `toggleTask('${task.id}', '${task.assigned_to}', '${task.category}')`);
            if (isCompleted) {
                checkboxDiv.innerHTML = `<i data-feather="check" style="width: 12px; height: 12px; color: white;"></i>`;
            }
            leftContainer.appendChild(checkboxDiv);
            
            // Text
            const textSpan = document.createElement('span');
            textSpan.className = 'task-text-span';
            textSpan.style.fontSize = '13px';
            textSpan.style.fontWeight = '500';
            textSpan.style.color = 'var(--text-main)';
            textSpan.style.textDecoration = isCompleted ? 'line-through' : 'none';
            textSpan.style.lineHeight = '1.3';
            textSpan.textContent = task.task_name;
            leftContainer.appendChild(textSpan);
            
            div.appendChild(leftContainer);
            
            // Right actions wrapper
            const actionsDiv = document.createElement('div');
            actionsDiv.style.display = 'flex';
            actionsDiv.style.alignItems = 'center';
            actionsDiv.style.gap = '6px';
            actionsDiv.style.flexShrink = '0';
            actionsDiv.style.marginLeft = '8px';
            
            // Delete button
            if (task.created_by == currentUserId || task.assigned_to == currentUserId || isPicOrLeader) {
                const deleteSpan = document.createElement('span');
                deleteSpan.setAttribute('onclick', `deleteTask('${task.id}', '${task.assigned_to}', '${task.category}', ${isCompleted})`);
                deleteSpan.style.color = '#ef4444';
                deleteSpan.style.cursor = 'pointer';
                deleteSpan.style.padding = '2px';
                deleteSpan.style.display = 'inline-flex';
                deleteSpan.style.alignItems = 'center';
                deleteSpan.innerHTML = `<i data-feather="trash-2" style="width: 13px; height: 13px;"></i>`;
                actionsDiv.appendChild(deleteSpan);
            }
            
            div.appendChild(actionsDiv);
            return div;
        }

        // Quick add for specific user
        async function handleQuickAddInput(event, category, assignedTo) {
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
                        body: JSON.stringify({ task_name: name, category, assigned_to: assignedTo })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        event.target.value = '';
                        
                        // Append new task to DOM
                        const taskList = document.getElementById(`task-list-${assignedTo}-${category}`);
                        if (taskList) {
                            const emptyState = taskList.querySelector('.empty-state');
                            if (emptyState) {
                                emptyState.remove();
                            }
                            
                            const isPicOrLeader = {{ ($isPic || $isLeader) ? 'true' : 'false' }};
                            const currentUserId = {{ auth()->id() }};
                            
                            const taskEl = renderTaskItem(data.task, isPicOrLeader, currentUserId);
                            taskList.appendChild(taskEl);
                            
                            // Initialize feather icons
                            feather.replace();
                        }
                        
                        // Update checklist count bubble
                        const countSpan = document.getElementById(`count-${assignedTo}-${category}`);
                        if (countSpan) {
                            let completed = parseInt(countSpan.getAttribute('data-completed'), 10);
                            let total = parseInt(countSpan.getAttribute('data-total'), 10);
                            
                            total++;
                            
                            countSpan.setAttribute('data-total', total);
                            countSpan.innerText = `${completed} / ${total} tugas`;
                        }
                        
                        // Update global progress bar
                        updateGlobalProgressBar(data.completion_percentage);
                    } else {
                        alert(data.error || "Gagal menyimpan To Do");
                    }
                } catch (err) {
                    console.error("Gagal menambah tugas:", err);
                } finally {
                    event.target.disabled = false;
                    event.target.focus();
                }
            }
        }

        async function toggleTask(taskId, memberId, category) {
            try {
                const response = await fetch(`/event-tasks/${taskId}/toggle`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    }
                });
                const data = await response.json();
                if (data.success) {
                    const checkbox = document.getElementById(`checkbox-${taskId}`);
                    const taskItem = document.getElementById(`task-item-${taskId}`);
                    const taskTextSpan = taskItem.querySelector('.task-text-span');
                    
                    const isCompletedNow = data.is_completed;
                    
                    if (isCompletedNow) {
                        checkbox.classList.add('checked');
                        checkbox.innerHTML = `<i data-feather="check" style="width: 12px; height: 12px; color: white;"></i>`;
                        taskItem.classList.add('completed');
                        taskTextSpan.style.textDecoration = 'line-through';
                    } else {
                        checkbox.classList.remove('checked');
                        checkbox.innerHTML = '';
                        taskItem.classList.remove('completed');
                        taskTextSpan.style.textDecoration = 'none';
                    }
                    
                    feather.replace();
                    
                    // Update checklist count bubble
                    const countSpan = document.getElementById(`count-${memberId}-${category}`);
                    if (countSpan) {
                        let completed = parseInt(countSpan.getAttribute('data-completed'), 10);
                        let total = parseInt(countSpan.getAttribute('data-total'), 10);
                        
                        if (isCompletedNow) {
                            completed++;
                        } else {
                            completed--;
                        }
                        
                        countSpan.setAttribute('data-completed', completed);
                        countSpan.innerText = `${completed} / ${total} tugas`;
                    }
                    
                    // Update global progress bar
                    updateGlobalProgressBar(data.completion_percentage);
                }
            } catch (err) {
                console.error("Gagal toggle tugas:", err);
            }
        }

        async function deleteTask(taskId, memberId, category, wasCompleted) {
            if (!confirm('Hapus tugas ini?')) return;
            try {
                const response = await fetch(`/event-tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                if (data.success) {
                    const taskItem = document.getElementById(`task-item-${taskId}`);
                    if (taskItem) {
                        taskItem.remove();
                    }
                    
                    // Update checklist count bubble
                    const countSpan = document.getElementById(`count-${memberId}-${category}`);
                    if (countSpan) {
                        let completed = parseInt(countSpan.getAttribute('data-completed'), 10);
                        let total = parseInt(countSpan.getAttribute('data-total'), 10);
                        
                        total--;
                        if (wasCompleted) {
                            completed--;
                        }
                        
                        countSpan.setAttribute('data-completed', completed);
                        countSpan.setAttribute('data-total', total);
                        countSpan.innerText = `${completed} / ${total} tugas`;
                    }
                    
                    const taskList = document.getElementById(`task-list-${memberId}-${category}`);
                    if (taskList && taskList.children.length === 0) {
                        taskList.innerHTML = `<div class="empty-state" style="font-size: 12px; color: var(--text-muted); font-weight: 500; padding: 8px 0;">Tidak ada tugas.</div>`;
                    }
                    
                    // Update global progress bar
                    updateGlobalProgressBar(data.completion_percentage);
                }
            } catch (err) {
                console.error("Gagal menghapus tugas:", err);
            }
        }

        function viewFullImage(src) {
            const modal = document.getElementById('fullImageModal');
            const img = document.getElementById('modalImg');
            img.src = src;
            modal.style.display = 'flex';
        }

        function closeFullImageModal() {
            document.getElementById('fullImageModal').style.display = 'none';
        }

        window.addEventListener('click', function (event) {
            const delModal = document.getElementById('deleteModal');
            if (event.target == delModal) closeDeleteModal();
        });
    </script>

    <!-- Webcam Geotag Absensi Script -->
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
                    document.getElementById('cameraWrapper').innerHTML = `<p style="color:#b91c1c; padding:40px 20px; font-weight:600;">Gagal mengakses kamera. Mohon izinkan akses kamera pada peramban Anda.</p>`;
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
                                const parts = data.display_name.split(', ');
                                if (parts.length > 1) parts.pop(); // remove country
                                currentAddress = parts.join(', ');
                            } else {
                                currentAddress = `Lat: ${currentLat.toFixed(5)}, Lon: ${currentLon.toFixed(5)}`;
                            }
                        } catch(e) {
                            currentAddress = `Lat: ${currentLat.toFixed(5)}, Lon: ${currentLon.toFixed(5)}`;
                        }

                        locStatus.style.color = "#10b981";
                        locText.innerText = currentAddress;
                        btnCapture.disabled = false;
                        btnCapture.style.opacity = 1;
                        btnCapture.style.cursor = 'pointer';
                    },
                    (error) => {
                        let msg = "Gagal mengakses lokasi.";
                        if (error.code === error.PERMISSION_DENIED) msg = "Akses lokasi ditolak! Anda WAJIB mengaktifkan GPS untuk absensi.";
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

                // Backdrop glass overlay on photo bottom
                const rectHeight = 130;
                ctx.fillStyle = 'rgba(0, 0, 0, 0.55)';
                ctx.fillRect(0, canvas.height - rectHeight, canvas.width, rectHeight);

                ctx.fillStyle = '#ffffff';
                ctx.textBaseline = 'top';
                
                const paddingX = 20;
                const startY = canvas.height - rectHeight + 14;

                ctx.font = 'bold 15px sans-serif';
                ctx.fillText('{{ config("app.name") }} Geotag Absensi', paddingX, startY);

                ctx.font = 'bold 13px monospace';
                ctx.fillStyle = '#fbbf24';
                const dateTimeStr = formatLocalDate();
                ctx.fillText(dateTimeStr, paddingX, startY + 22);

                ctx.font = '12px sans-serif';
                ctx.fillStyle = '#ffffff';
                const maxWidth = canvas.width - (paddingX * 2);
                const addressLines = wrapText(ctx, currentAddress, paddingX, startY + 42, maxWidth, 16);
                
                let curY = startY + 42;
                for (let i = 0; i < addressLines.length; i++) {
                    ctx.fillText(addressLines[i], paddingX, curY);
                    curY += 16;
                    if(i === 3) break; 
                }

                if (currentLat !== null) {
                    ctx.font = '10px monospace';
                    ctx.fillStyle = '#94a3b8';
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
                        btnSubmit.innerText = "Kirim Absensi";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengirim data. Coba lagi.');
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = "Kirim Absensi";
                });
            }

            window.addEventListener('load', startCamera);
        </script>
    @endif
@endsection