@extends('layouts.app')

@section('title', 'Buat Event')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* Layout grid */
        .create-event-grid {
            display: grid;
            grid-template-columns: 2.1fr 0.9fr;
            gap: 24px;
            align-items: start;
            margin-bottom: 24px;
        }
        @media (max-width: 1024px) {
            .create-event-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card overrides */
        .card-left {
            padding: 28px;
            border-radius: 20px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
        }
        .card-right {
            padding: 24px;
            border-radius: 20px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            position: sticky;
            top: 24px;
        }

        /* Header style */
        .form-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        .form-header-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--primary-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563EB;
            flex-shrink: 0;
        }
        .form-header-icon svg {
            width: 24px;
            height: 24px;
        }
        .form-header-text h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
        }
        .form-header-text p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Section label with blue indicator */
        .section-indicator-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            margin: 24px 0 16px 0;
            border-left: 3px solid #2563EB;
            padding-left: 10px;
        }

        /* Info box alert */
        .alert-info-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            background: rgba(37, 99, 235, 0.05);
            border: 1px solid rgba(37, 99, 235, 0.1);
            color: #1E40AF;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }
        [data-theme="dark"] .alert-info-custom {
            background: rgba(30, 58, 95, 0.2);
            border-color: rgba(30, 58, 95, 0.4);
            color: #93C5FD;
        }
        .alert-info-custom svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        /* Form group and controls */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-main);
        }
        .form-group label span.required {
            color: var(--danger);
            margin-left: 2px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            background: var(--bg-color);
            color: var(--text-main);
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Input with icon wrapper */
        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-with-icon svg, .input-with-icon i {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            pointer-events: none;
            width: 16px;
            height: 16px;
        }
        .input-with-icon .form-control {
            padding-left: 42px;
        }

        /* Custom Select Dropdown for PIC */
        .custom-select-wrapper {
            position: relative;
            user-select: none;
        }
        .custom-select {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-size: 14px;
            background: var(--bg-color);
            color: var(--text-main);
            transition: all 0.2s;
            height: 41.5px;
        }
        .custom-select:hover {
            border-color: #94A3B8;
        }
        .custom-select .sel {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .custom-select .sel svg {
            width: 16px;
            height: 16px;
            color: var(--text-muted);
        }
        .pic-opts {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-top: 6px;
            max-height: 260px;
            overflow-y: auto;
            z-index: 20;
            display: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        .pic-opts.open {
            display: block;
        }
        .pic-opt {
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-size: 14px;
            border-bottom: 1px solid var(--border-color);
            transition: background 0.15s;
            color: var(--text-main);
        }
        .pic-opt:last-child {
            border-bottom: none;
        }
        .pic-opt:hover {
            background: var(--hover-bg);
        }
        .avatar-sm {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Position blocks */
        .position-block {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            background: var(--bg-color);
        }
        [data-theme="dark"] .position-block {
            background: rgba(30, 41, 59, 0.2);
        }
        .pos-header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            align-items: end;
            margin-bottom: 20px;
        }
        .btn-remove-pos {
            background: none;
            border: 1px solid #FECACA;
            color: var(--danger);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
            height: 42px;
        }
        .btn-remove-pos:hover {
            background: #FEE2E2;
        }
        [data-theme="dark"] .btn-remove-pos:hover {
            background: rgba(239, 68, 68, 0.15);
        }

        /* Position block participant selection */
        .pos-participants-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .pos-search-wrapper {
            position: relative;
            width: 250px;
        }
        .pos-search-wrapper input {
            padding: 6px 12px 6px 32px;
            font-size: 13px;
            border-radius: 8px;
        }
        .pos-search-wrapper svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            color: var(--text-muted);
        }

        /* Employee cards grid */
        .emp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .emp-lbl {
            cursor: pointer;
            display: block;
        }
        .emp-cb {
            display: none;
        }
        .emp-inner {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--sidebar-bg);
            transition: all 0.2s;
            position: relative;
            text-align: left;
            height: 60px;
        }
        .emp-inner:hover {
            border-color: #94A3B8;
        }
        .emp-cb:checked+.emp-inner {
            border-color: #2563EB;
            background: rgba(37, 99, 235, 0.04);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.06);
        }
        .emp-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 1.5px solid var(--border-color);
        }
        .emp-cb:checked+.emp-inner .emp-avatar {
            border-color: #2563EB;
        }
        .emp-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .emp-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .emp-div {
            font-size: 10.5px;
            color: var(--text-muted);
            margin-top: 1px;
        }
        .emp-close-btn {
            position: absolute;
            top: 4px;
            right: 6px;
            color: var(--text-muted);
            display: none;
        }
        .emp-cb:checked+.emp-inner .emp-close-btn {
            display: block;
            width: 12px;
            height: 12px;
        }
        .emp-lbl.pic-hidden {
            display: none;
        }

        /* Detail tugas (dates & roles detail) */
        .dates-wrap {
            margin-top: 16px;
            border-top: 1px solid var(--border-color);
            padding-top: 16px;
        }
        .dates-header {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .date-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        .date-row-user {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 160px;
        }
        .date-row-user img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }
        .date-row-user span {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
        }
        .date-row-inputs {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            flex-wrap: wrap;
        }
        .date-input-sm {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13px;
            background: var(--bg-color);
            color: var(--text-main);
            width: 160px;
        }
        .btn-full-event {
            padding: 7px 14px;
            background: var(--primary-soft);
            color: #2563EB;
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-full-event:hover {
            background: rgba(37, 99, 235, 0.12);
        }

        /* Toggle buttons LD/ULD */
        .toggle-btn input {
            display: none;
        }
        .badge-opt {
            display: inline-block;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: pointer;
            background: var(--bg-color);
            color: var(--text-muted);
            transition: all 0.15s;
            user-select: none;
        }
        .toggle-btn input:checked+.badge-opt.ld {
            background: #E0E7FF;
            border-color: #6366F1;
            color: #4338CA;
        }
        .toggle-btn input:checked+.badge-opt.uld {
            background: #FEE2E2;
            border-color: #EF4444;
            color: #B91C1C;
        }

        /* Button Tambah Posisi */
        .btn-add-pos {
            width: 100%;
            padding: 12px;
            border: 2px dashed var(--border-color);
            border-radius: 14px;
            background: none;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-add-pos:hover {
            border-color: #94A3B8;
            color: var(--text-main);
            background: var(--hover-bg);
        }

        /* Sidebar styling */
        .sidebar-title-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .sidebar-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
        }
        .sidebar-badge {
            background-color: var(--primary-soft);
            color: #2563EB;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .employee-schedules-list {
            max-height: 520px;
            overflow-y: auto;
            margin-bottom: 16px;
            padding-right: 4px;
        }
        .employee-schedules-list::-webkit-scrollbar {
            width: 6px;
        }
        .employee-schedules-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .employee-schedules-list::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        /* Scheduled employee row item */
        .employee-schedule-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid var(--border-color);
            gap: 12px;
        }
        .employee-schedule-item:last-child {
            border-bottom: none;
        }
        .emp-sched-left {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            min-width: 0;
        }
        .emp-sched-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 1.5px solid var(--border-color);
        }
        .emp-sched-details {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .emp-sched-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .emp-sched-div {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
            margin-bottom: 6px;
        }
        .emp-sched-event-box {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: var(--bg-color);
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        .emp-sched-event-name {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .emp-sched-event-name svg {
            width: 12px;
            height: 12px;
            color: var(--primary);
            flex-shrink: 0;
        }
        .emp-sched-event-time {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .emp-sched-event-time svg {
            width: 12px;
            height: 12px;
            color: var(--text-muted);
            flex-shrink: 0;
        }
        .emp-sched-no-event {
            font-size: 11.5px;
            color: var(--text-muted);
            font-style: italic;
        }

        /* Event count badge colors */
        .event-count-badge {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }
        .badge-blue { background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; }
        .badge-purple { background: #FAF5FF; border: 1px solid #E9D5FF; color: #6B21A8; }
        .badge-red { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
        .badge-green { background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; }
        .badge-orange { background: #FFF7ED; border: 1px solid #FFEDD5; color: #9A3412; }
        .badge-gray { background: #F8FAFC; border: 1px solid #E2E8F0; color: #475569; }

        /* Show All Employees button */
        .btn-show-all {
            width: 100%;
            padding: 10px;
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            color: #2563EB;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-show-all:hover {
            background: var(--hover-bg);
        }
        .btn-show-all svg {
            width: 14px;
            height: 14px;
        }

        /* Buttons on Left Container Footer */
        .btn-cancel {
            display: inline-flex;
            align-items: center;
            padding: 10px 24px;
            background: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-cancel:hover {
            background: var(--hover-bg);
        }
        .btn-submit-premium {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: #2563EB;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2);
        }
        .btn-submit-premium:hover {
            background: #1D4ED8;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }
        .btn-submit-premium svg {
            width: 16px;
            height: 16px;
        }
    </style>

    <div class="create-event-grid">
        <!-- Container Kiri: Form Informasi Event & Participant -->
        <div class="card-left">
            <div class="form-header">
                <div class="form-header-icon">
                    <i data-feather="calendar"></i>
                </div>
                <div class="form-header-text">
                    <h2>Buat Event Baru</h2>
                    <p>Lengkapi informasi event dan pilih peserta yang akan terlibat.</p>
                </div>
            </div>

            <div class="alert-info-custom">
                <i data-feather="info"></i>
                <span>Pastikan karyawan belum memiliki jadwal event yang bertabrakan.</span>
            </div>

            @if($errors->any())
                <div style="background:#fee2e2;color:#b91c1c;padding:12px;border-radius:12px;font-size:13px;margin-bottom:20px;border:1px solid #fca5a5;">
                    Harap periksa kembali isian formulir di bawah.
                </div>
            @endif

            <form action="{{ route('events.store') }}" method="POST" id="eventForm">
                @csrf

                <div class="section-indicator-title">
                    <span>Informasi Event</span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div class="form-group">
                        <label for="name">Judul Event <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i data-feather="file-text"></i>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Masukkan judul event">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>PIC <span class="required">*</span></label>
                        <div class="custom-select-wrapper" id="picWrap">
                            <input type="hidden" name="pic_id" id="pic_id_input" value="{{ old('pic_id') }}" required>
                            <div class="custom-select" id="picBtn">
                                <div class="sel" id="picSel">
                                    <i data-feather="user"></i>
                                    <span style="color: var(--text-muted);">Pilih PIC Event</span>
                                </div>
                                <i data-feather="chevron-down" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                            </div>
                            <div class="pic-opts" id="picOpts">
                                @foreach($users as $u)
                                    <div class="pic-opt" data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-photo="{{ $u->photo_url }}">
                                        <img src="{{ $u->photo_url }}" class="avatar-sm">
                                        <span>{{ $u->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <div class="input-with-icon">
                        <i data-feather="edit-2" style="top: 14px;"></i>
                        <textarea id="description" name="description" class="form-control" rows="2" style="padding-left: 42px; resize: none;" placeholder="Tulis deskripsi event (opsional)">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div class="form-group">
                        <label for="location">Lokasi <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i data-feather="map-pin"></i>
                            <input type="text" id="location" name="location" class="form-control" value="{{ old('location') }}" required placeholder="Contoh: Ruang Meeting Utama, Aula Lantai 3">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="first_pos_name">Runner Event (Nama Posisi) <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i data-feather="briefcase"></i>
                            <input type="text" id="first_pos_name" name="positions[0][name]" class="form-control" value="{{ old('positions.0.name', 'Runner Event') }}" required placeholder="Cari atau pilih posisi">
                        </div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1.2fr 0.9fr 0.9fr;gap:20px;">
                    <div class="form-group">
                        <label for="event_dates">Tanggal Event <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i data-feather="calendar"></i>
                            <input type="text" id="event_dates" name="event_dates" class="form-control" value="{{ old('event_dates') }}" required placeholder="Pilih tanggal">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="start_time">Jam Mulai <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i data-feather="clock"></i>
                            <input type="time" id="start_time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="end_time">Jam Selesai <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i data-feather="clock"></i>
                            <input type="time" id="end_time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                        </div>
                    </div>
                </div>

                <div id="posContainer"></div>

                <button type="button" class="btn-add-pos" onclick="addPos()">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i>
                    Tambah Posisi Baru
                </button>

                <div style="margin-top:32px;display:flex;justify-content:space-between;align-items:center;">
                    <a href="{{ route('events.index') }}" class="btn-cancel">
                        Batal
                    </a>
                    <button type="submit" class="btn-submit-premium">
                        <i data-feather="save"></i> Simpan Event
                    </button>
                </div>
            </form>
        </div>

        <!-- Container Kanan: Karyawan dengan Event Terjadwal -->
        <div class="card-right">
            <div class="sidebar-title-container">
                <span class="sidebar-title">Karyawan dengan Event Terjadwal</span>
                <span class="sidebar-badge" id="sidebarCountBadge">0 karyawan</span>
            </div>

            <div class="sidebar-search-wrapper">
                <input type="text" id="sidebarSearch" class="form-control" placeholder="Cari karyawan atau event..." style="padding-right: 36px; height: 40px; border-radius: 10px;">
                <i data-feather="search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted); pointer-events: none;"></i>
            </div>

            <div class="sidebar-filter-wrapper" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Filter</span>
                <select id="sidebarFilter" class="form-control" style="width: auto; padding: 6px 28px 6px 12px; font-size: 13px; border-radius: 8px; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;16&quot; height=&quot;16&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;%2364748B&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><polyline points=&quot;6 9 12 15 18 9&quot;></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 12px; height: 32px;">
                    <option value="all">Semua Event</option>
                    <option value="has_events">Punya Event</option>
                    <option value="no_events">Tidak Ada Event</option>
                </select>
            </div>

            <div class="employee-schedules-list" id="sidebarList">
                <!-- Dynamically populated by JS -->
            </div>

            <button type="button" id="toggleShowAllEmployees" class="btn-show-all">
                Lihat Semua Karyawan <i data-feather="chevron-right"></i>
            </button>
        </div>
    </div>

    <script>
        const ALL_USERS = {!! json_encode($usersJson) !!};
        const USER_SCHEDULES = {!! $usersSchedules->toJson() !!};
        const IS_CEO = @role('CEO|GM') true @else false @endrole;
        let posCount = 0;
        let currentPic = null;
        let showAllEmployees = false;

        // Custom Dropdown PIC
        document.getElementById('picBtn').addEventListener('click', e => {
            e.stopPropagation();
            document.getElementById('picOpts').classList.toggle('open');
        });
        document.addEventListener('click', () => document.getElementById('picOpts').classList.remove('open'));

        document.querySelectorAll('.pic-opt').forEach(opt => {
            opt.addEventListener('click', function () {
                const id = this.dataset.id;
                document.getElementById('pic_id_input').value = id;
                document.getElementById('picSel').innerHTML = `<img src="${this.dataset.photo}" class="avatar-sm"> <span>${this.dataset.name}</span>`;
                currentPic = id;
                syncPic(id);
            });
        });

        function syncPic(picId) {
            document.querySelectorAll('.emp-lbl').forEach(lbl => {
                const match = String(lbl.dataset.uid) === String(picId);
                lbl.classList.toggle('pic-hidden', match);
                if (match) {
                    const cb = lbl.querySelector('.emp-cb');
                    if (cb && cb.checked) cb.click();
                }
            });
        }

        // Search participant grid inside a position block
        function filterPosEmployees(input) {
            const searchVal = input.value.toLowerCase().trim();
            const block = input.closest('.position-block');
            const labels = block.querySelectorAll('.emp-lbl');
            labels.forEach(lbl => {
                const name = lbl.querySelector('.emp-name').textContent.toLowerCase();
                const div = lbl.querySelector('.emp-div').textContent.toLowerCase();
                const isPicHidden = lbl.classList.contains('pic-hidden');
                
                if (isPicHidden) {
                    lbl.style.display = 'none';
                    return;
                }
                
                if (name.includes(searchVal) || div.includes(searchVal)) {
                    lbl.style.display = 'block';
                } else {
                    lbl.style.display = 'none';
                }
            });
        }

        // Track employee checkbox changes to show Detail Tugas
        document.getElementById('posContainer').addEventListener('change', function (e) {
            if (!e.target.classList.contains('emp-cb')) return;
            const block = e.target.closest('.position-block');
            const posIdx = block.dataset.pos;
            const userId = e.target.value;
            const user = ALL_USERS.find(u => String(u.id) === String(userId));
            toggleDateRow(e.target.checked, posIdx, userId, user);
        });

        function toggleDateRow(checked, posIdx, userId, user) {
            const container = document.getElementById(`dates-${posIdx}`);
            const header = document.getElementById(`dates-header-${posIdx}`);

            if (checked) {
                const row = document.createElement('div');
                row.className = 'date-row';
                row.dataset.uid = userId;
                row.innerHTML = `
                    <div class="date-row-user">
                        <img src="${user.photo}" alt="${user.name}">
                        <span>${user.name}</span>
                    </div>
                    <div class="date-row-inputs">
                        <input type="text" name="positions[${posIdx}][member_dates][${userId}][work_dates]" class="date-input-sm multi-date" placeholder="Multi tanggal">
                        <button type="button" class="btn-full-event" onclick="setFullEvent(${posIdx},${userId})">Full Event</button>
                        <div style="margin-left: auto; display:flex; gap:6px;">
                            <label class="toggle-btn" title="Tugas Loading">
                                <input type="checkbox" name="positions[${posIdx}][member_loading][${userId}]">
                                <span class="badge-opt ld">LD</span>
                            </label>
                            <label class="toggle-btn" title="Tugas Unloading">
                                <input type="checkbox" name="positions[${posIdx}][member_unloading][${userId}]">
                                <span class="badge-opt uld">ULD</span>
                            </label>
                        </div>
                    </div>
                `;
                container.appendChild(row);
                flatpickr(row.querySelector('.multi-date'), {
                    mode: "multiple",
                    dateFormat: "Y-m-d",
                });
            } else {
                const row = container.querySelector(`[data-uid="${userId}"]`);
                if (row) row.remove();
            }
            header.style.display = container.children.length ? 'block' : 'none';
        }

        function setFullEvent(posIdx, userId) {
            const evDates = document.getElementById('event_dates').value;
            const input = document.querySelector(`input[name="positions[${posIdx}][member_dates][${userId}][work_dates]"]`);
            if (input && input._flatpickr && evDates) {
                input._flatpickr.setDate(evDates.split(', '));
            }
        }

        function buildGrid(idx) {
            return ALL_USERS.map(u => `
                <label class="emp-lbl ${currentPic && String(u.id) === String(currentPic) ? 'pic-hidden' : ''}" data-uid="${u.id}">
                    <input type="checkbox" name="positions[${idx}][members][]" value="${u.id}" class="emp-cb">
                    <div class="emp-inner">
                        <img src="${u.photo}" class="emp-avatar" alt="${u.name}">
                        <div class="emp-info">
                            <div class="emp-name">${u.name}</div>
                            <div class="emp-div">${u.division}</div>
                        </div>
                        <i data-feather="x" class="emp-close-btn"></i>
                    </div>
                </label>
            `).join('');
        }

        function addPos() {
            const idx = posCount++;
            const block = document.createElement('div');
            block.className = 'position-block';
            block.dataset.pos = idx;

            let posHeaderHtml = '';
            if (idx === 0) {
                posHeaderHtml = `
                    <div class="pos-participants-header">
                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">Pilih Peserta (PIC)</div>
                        <div class="pos-search-wrapper">
                            <i data-feather="search"></i>
                            <input type="text" class="form-control pos-search" placeholder="Cari nama karyawan atau posisi..." oninput="filterPosEmployees(this)">
                        </div>
                    </div>
                `;
            } else {
                posHeaderHtml = `
                    <div class="pos-header">
                        <div>
                            <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Nama Posisi</label>
                            <div class="input-with-icon">
                                <i data-feather="briefcase"></i>
                                <input type="text" name="positions[${idx}][name]" class="form-control" required placeholder="Contoh: MC, Loading, dll.">
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn-remove-pos" onclick="removePos(this)">
                                <i data-feather="trash-2"></i> Hapus Posisi
                            </button>
                        </div>
                    </div>
                    <div class="pos-participants-header" style="margin-top: 12px;">
                        <div style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Pilih Peserta</div>
                        <div class="pos-search-wrapper">
                            <i data-feather="search"></i>
                            <input type="text" class="form-control pos-search" placeholder="Cari nama karyawan..." oninput="filterPosEmployees(this)">
                        </div>
                    </div>
                `;
            }

            block.innerHTML = `
                ${posHeaderHtml}
                <div class="emp-grid" style="margin-top: 12px;">${buildGrid(idx)}</div>
                <div class="dates-wrap">
                    <div class="dates-header" id="dates-header-${idx}" style="display: none;">Detail Tugas</div>
                    <div id="dates-${idx}"></div>
                </div>
            `;
            
            document.getElementById('posContainer').appendChild(block);
            feather.replace();
            syncRemoveButtons();
        }

        function removePos(btn) {
            btn.closest('.position-block').remove();
            syncRemoveButtons();
        }

        function syncRemoveButtons() {
            const blocks = document.querySelectorAll('.position-block');
            blocks.forEach((b, idx) => {
                const removeBtn = b.querySelector('.btn-remove-pos');
                if (removeBtn) {
                    removeBtn.style.display = blocks.length > 1 ? 'flex' : 'none';
                }
            });
        }

        // Render Scheduled Employees Sidebar
        function renderSidebar() {
            const listContainer = document.getElementById('sidebarList');
            const searchVal = document.getElementById('sidebarSearch').value.toLowerCase().trim();
            const filterVal = document.getElementById('sidebarFilter').value;
            
            listContainer.innerHTML = '';
            
            let filtered = USER_SCHEDULES.filter(item => {
                const matchesSearch = item.name.toLowerCase().includes(searchVal) ||
                                      item.division.toLowerCase().includes(searchVal) ||
                                      item.all_events.some(ev => ev.name.toLowerCase().includes(searchVal));
                
                let matchesFilter = true;
                if (filterVal === 'has_events') {
                    matchesFilter = item.active_events_count > 0;
                } else if (filterVal === 'no_events') {
                    matchesFilter = item.active_events_count === 0;
                }
                
                return matchesSearch && matchesFilter;
            });

            if (!showAllEmployees && !searchVal && filterVal === 'all') {
                filtered = filtered.filter(item => item.active_events_count > 0);
            }

            const activeEmployeeCount = filtered.filter(item => item.active_events_count > 0).length;
            document.getElementById('sidebarCountBadge').textContent = `${activeEmployeeCount} karyawan`;

            if (filtered.length === 0) {
                listContainer.innerHTML = `
                    <div style="padding: 24px; text-align: center; color: var(--text-muted); font-style: italic; font-size: 13px;">
                        Tidak ada karyawan ditemukan
                    </div>
                `;
                return;
            }

            filtered.forEach(item => {
                let badgeColorClass = 'badge-gray';
                if (item.active_events_count === 1) {
                    badgeColorClass = 'badge-purple';
                } else if (item.active_events_count === 2) {
                    badgeColorClass = 'badge-blue';
                } else if (item.active_events_count === 3) {
                    badgeColorClass = 'badge-green';
                } else if (item.active_events_count > 3) {
                    badgeColorClass = 'badge-red';
                }
                
                let nextEventHtml = '';
                if (item.next_event) {
                    nextEventHtml = `
                        <div class="emp-sched-event-box">
                            <div class="emp-sched-event-name">
                                <i data-feather="calendar" style="width: 12px; height: 12px; color: var(--primary);"></i>
                                <span>${item.next_event.name}</span>
                            </div>
                            <div class="emp-sched-event-time">
                                <i data-feather="clock" style="width: 12px; height: 12px; color: var(--text-muted);"></i>
                                <span>${item.next_event.date}, ${item.next_event.time}</span>
                            </div>
                        </div>
                    `;
                } else {
                    nextEventHtml = `<div class="emp-sched-no-event">Tidak ada event terjadwal</div>`;
                }

                const rowHtml = `
                    <div class="employee-schedule-item" data-uid="${item.id}">
                        <div class="emp-sched-left">
                            <img src="${item.photo}" class="emp-sched-avatar" alt="${item.name}">
                            <div class="emp-sched-details">
                                <div class="emp-sched-name">${item.name}</div>
                                <div class="emp-sched-div">${item.division}</div>
                                ${nextEventHtml}
                            </div>
                        </div>
                        <div>
                            <span class="event-count-badge ${badgeColorClass}">
                                ${item.active_events_count} Event
                            </span>
                        </div>
                    </div>
                `;
                listContainer.insertAdjacentHTML('beforeend', rowHtml);
            });

            feather.replace();
        }

        document.getElementById('sidebarSearch').addEventListener('input', renderSidebar);
        document.getElementById('sidebarFilter').addEventListener('change', renderSidebar);
        
        document.getElementById('toggleShowAllEmployees').addEventListener('click', function() {
            showAllEmployees = !showAllEmployees;
            this.innerHTML = showAllEmployees ? 
                `Tampilkan Karyawan Terjadwal <i data-feather="chevron-right"></i>` : 
                `Lihat Semua Karyawan <i data-feather="chevron-right"></i>`;
            renderSidebar();
        });

        // Initialize view
        window.addEventListener('DOMContentLoaded', () => {
            flatpickr("#event_dates", {
                mode: "multiple",
                dateFormat: "Y-m-d"
            });
            addPos();
            renderSidebar();
            feather.replace();
        });
    </script>
@endsection