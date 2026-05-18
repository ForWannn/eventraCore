@extends('layouts.app')

@section('title', 'Buat Event Baru')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            background: var(--bg-color);
            color: var(--text-main);
            transition: border-color 0.15s;
        }

        .form-control:focus {
            outline: none;
            border-color: #9ca3af;
        }

        .btn-submit {
            display: inline-block;
            padding: 10px 24px;
            background: var(--primary);
            color: var(--primary-text);
            text-decoration: none;
            border-radius: 12px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .error-text {
            color: #b91c1c;
            font-size: 12px;
            margin-top: 4px;
        }

        .section-divider {
            border: 0;
            border-top: 1px dashed var(--border-color);
            margin: 28px 0;
        }

        .section-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        /* PIC Dropdown */
        .custom-select-wrapper {
            position: relative;
            user-select: none;
        }

        .custom-select {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--hover-bg);
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-size: 14px;
        }

        .custom-select .sel {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-sm {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }

        .pic-opts {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-top: 4px;
            max-height: 260px;
            overflow-y: auto;
            z-index: 20;
            display: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
        }

        .pic-opts.open {
            display: block;
        }

        .pic-opt {
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-size: 14px;
            border-bottom: 1px solid var(--border-color);
            transition: background .1s;
        }

        .pic-opt:hover {
            background: var(--hover-bg);
        }

        /* Fee prefix input */
        .fee-prefix {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            background: var(--bg-color);
        }

        .fee-prefix span {
            padding: 10px 12px;
            background: var(--hover-bg);
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            border-right: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .fee-prefix input {
            flex: 1;
            padding: 10px 12px;
            border: none;
            background: transparent;
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            min-width: 0;
        }

        /* Position block */
        .position-block {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            background: var(--hover-bg);
        }

        .pos-header {
            display: grid;
            gap: 12px;
            align-items: end;
            margin-bottom: 16px;
        }

        .btn-remove-pos {
            background: none;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
            height: 42px;
        }

        .btn-remove-pos:hover {
            background: #fee2e2;
        }

        /* Employee grid */
        .emp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
        }

        .emp-lbl {
            cursor: pointer;
            display: block;
        }

        .emp-cb {
            display: none;
        }

        .emp-inner {
            border: 2px solid var(--border-color);
            border-radius: 14px;
            padding: 12px 6px;
            text-align: center;
            background: var(--sidebar-bg);
            transition: all .18s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .emp-inner:hover {
            border-color: #9ca3af;
        }

        .emp-cb:checked+.emp-inner {
            border-color: #22c55e;
            background: #f0fdf4;
            box-shadow: 0 3px 8px rgba(34, 197, 94, .15);
        }

        .emp-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 8px;
            border: 2px solid #e5e7eb;
        }

        .emp-cb:checked+.emp-inner .emp-avatar {
            border-color: #22c55e;
        }

        .emp-name {
            font-size: 11px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 2px;
            word-break: break-word;
        }

        .emp-div {
            font-size: 10px;
            color: var(--text-muted);
        }

        .emp-lbl.pic-hidden {
            display: none;
        }

        /* Dates container */
        .dates-wrap {
            margin-top: 16px;
        }

        .dates-header {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
            display: none;
        }

        .date-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .date-row-user {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 140px;
            flex: 0 0 auto;
        }

        .date-row-user img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }

        .date-row-user span {
            font-size: 13px;
            font-weight: 500;
        }

        .date-row-inputs {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 0;
            flex-wrap: wrap;
        }

        .date-input-sm {
            padding: 7px 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13px;
            background: var(--bg-color);
            color: var(--text-main);
            width: 140px;
        }

        .btn-full-event {
            padding: 6px 12px;
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s;
        }

        .btn-full-event:hover {
            background: #bfdbfe;
        }

        /* Loading/Unload Toggle UI */
        .toggle-btn input {
            display: none;
        }

        .badge-opt {
            display: inline-block;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: pointer;
            background: var(--bg-color);
            color: var(--text-muted);
            transition: 0.2s;
            user-select: none;
        }

        .toggle-btn input:checked+.badge-opt.ld {
            background: #e0e7ff;
            border-color: #6366f1;
            color: #3730a3;
        }

        .toggle-btn input:checked+.badge-opt.uld {
            background: #e0e7ff;
            border-color: #6366f1;
            color: #3730a3;
        }

        .btn-add-pos {
            width: 100%;
            padding: 12px;
            border: 2px dashed var(--border-color);
            border-radius: 14px;
            background: none;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-add-pos:hover {
            border-color: #9ca3af;
            color: var(--text-main);
            background: var(--hover-bg);
        }
    </style>

    <div class="card" style="max-width: 880px;">
        <h3 style="margin-bottom: 24px;">Formulir Pembuatan Event</h3>

        @if($errors->any())
            <div style="background:#fee2e2;color:#b91c1c;padding:12px;border-radius:12px;font-size:13px;margin-bottom:20px;">
                Harap periksa kembali isian formulir di bawah.
            </div>
        @endif

        <form action="{{ route('events.store') }}" method="POST" id="eventForm">
            @csrf

            <div class="form-group">
                <label for="name">Judul Event</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi (Opsional)</label>
                <textarea id="description" name="description" class="form-control"
                    rows="2">{{ old('description') }}</textarea>
            </div>

            <hr class="section-divider">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label for="event_dates">Tanggal Event</label>
                    <input type="text" id="event_dates" name="event_dates" class="form-control"
                        value="{{ old('event_dates') }}" required placeholder="Pilih tanggal event">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div class="form-group">
                        <label for="start_time">Jam Mulai</label>
                        <input type="time" id="start_time" name="start_time" class="form-control"
                            value="{{ old('start_time') }}">
                    </div>
                    <div class="form-group">
                        <label for="end_time">Jam Selesai</label>
                        <input type="time" id="end_time" name="end_time" class="form-control" value="{{ old('end_time') }}">
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label for="attendance_start">Absensi Dibuka (Jam)</label>
                    <input type="time" id="attendance_start" name="attendance_start" class="form-control"
                        value="{{ old('attendance_start') }}">
                </div>
                <div class="form-group">
                    <label for="attendance_end">Absensi Ditutup (Jam)</label>
                    <input type="time" id="attendance_end" name="attendance_end" class="form-control"
                        value="{{ old('attendance_end') }}">
                </div>
            </div>

            <hr class="section-divider">

            <div style="display:flex; justify-content: space-between; align-items: center;">
                <div class="section-label" style="margin-bottom:0;">PIC Event</div>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none;">
                    <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Perlu Absen?</span>
                    <input type="checkbox" name="needs_attendance" value="1" checked 
                           style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;">
                </label>
            </div>
            <div style="display:grid;grid-template-columns:1fr @role('CEO') 200px @endrole;gap:16px;align-items:start;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Pilih PIC</label>
                    <div class="custom-select-wrapper" id="picWrap">
                        <input type="hidden" name="pic_id" id="pic_id_input" value="{{ old('pic_id') }}" required>
                        <div class="custom-select" id="picBtn">
                            <div class="sel" id="picSel">PIC</div>
                            <span>▼</span>
                        </div>
                        <div class="pic-opts" id="picOpts">
                            @foreach($users as $u)
                                <div class="pic-opt" data-id="{{ $u->id }}" data-name="{{ $u->name }}"
                                    data-photo="{{ $u->photo_url }}">
                                    <img src="{{ $u->photo_url }}" class="avatar-sm"> <span>{{ $u->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @role('CEO|GM')
                <div class="form-group" style="margin-bottom:0;">
                    <label>Fee PIC</label>
                    <input type="hidden" id="pic_fee" name="pic_fee" value="{{ old('pic_fee', 0) }}" placeholder="0">
                    <div class="fee-prefix">
                        <span>Rp</span>
                        <input type="text" id="pic_fee_d" placeholder="0"
                            value="{{ number_format(old('pic_fee', 0), 0, ',', '.') }}" oninput="fmtFee(this,'pic_fee')"
                            autocomplete="off">
                    </div>
                </div>
                @endrole
            </div>

            <hr class="section-divider">

            {{-- TARIF OPERASIONAL (KHUSUS CEO & GM) --}}
            @role('CEO|GM')
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom: 24px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Fee Loading</label>
                    <div class="fee-prefix">
                        <span>Rp</span>
                        <input type="text" class="form-control" oninput="fmtFee(this, 'loading_fee_hid')" placeholder="0">
                        <input type="hidden" name="loading_fee" id="loading_fee_hid" value="0">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Fee Unloading</label>
                    <div class="fee-prefix">
                        <span>Rp</span>
                        <input type="text" class="form-control" oninput="fmtFee(this, 'unloading_fee_hid')" placeholder="0">
                        <input type="hidden" name="unloading_fee" id="unloading_fee_hid" value="0">
                    </div>
                </div>
            </div>
            <hr class="section-divider">
            @endrole

            <div class="section-label">Partisipan Event</div>
            <p style="font-size:13px;color:var(--text-muted);margin:-8px 0 20px;">
            </p>

            <div id="posContainer"></div>

            <button type="button" class="btn-add-pos" onclick="addPos()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Posisi Baru
            </button>

            <div style="margin-top:32px;display:flex;justify-content:space-between;align-items:center;">
                <a href="{{ route('events.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:14px;">←
                    Batal</a>
                <button type="submit" class="btn-submit">Simpan &amp; Buat Event</button>
            </div>
        </form>
    </div>

    <script>
        const ALL_USERS = {!! json_encode($usersJson) !!};
        const IS_CEO = @role('CEO|GM') true @else false @endrole;
        let posCount = 0;
        let currentPic = null;

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
                if (match) { const cb = lbl.querySelector('.emp-cb'); if (cb && cb.checked) cb.click(); }
            });
        }

        function fmtFee(inp, hidId) {
            const raw = inp.value.replace(/\D/g, '');
            inp.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById(hidId).value = raw;
        }

        function fmtFeePos(d) {
            const raw = d.value.replace(/\D/g, '');
            d.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            d.closest('.pos-header').querySelector('.fee-h').value = raw;
        }

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
                row.className = 'date-row'; row.dataset.uid = userId;
                row.innerHTML = `
                                                                                    <div class="date-row-user">
                                                                                        <img src="${user.photo}" alt="${user.name}">
                                                                                        <span>${user.name}</span>
                                                                                    </div>
                                                                                    <div class="date-row-inputs">
                                                                                        <input type="text" name="positions[${posIdx}][member_dates][${userId}][work_dates]" class="date-input-sm multi-date" placeholder="Multi tanggal">
                                                                                        <button type="button" class="btn-full-event" onclick="setFullEvent(${posIdx},${userId})">Full Event</button>

                                                                                        <div style="margin-left: 8px; display:flex; gap:6px;">
                                                                                            <label class="toggle-btn" title="Tugas Loading">
                                                                                                <input type="checkbox" name="positions[${posIdx}][member_loading][${userId}]">
                                                                                                <span class="badge-opt ld">LD</span>
                                                                                            </label>
                                                                                            <label class="toggle-btn" title="Tugas Unloading">
                                                                                                <input type="checkbox" name="positions[${posIdx}][member_unloading][${userId}]">
                                                                                                <span class="badge-opt uld">ULD</span>
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>`;
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
                                                                                        <div class="emp-name">${u.name}</div>
                                                                                        <div class="emp-div">${u.division}</div>
                                                                                    </div>
                                                                                </label>`).join('');
        }

        function addPos() {
            const idx = posCount++;
            const block = document.createElement('div');
            block.className = 'position-block';
            block.dataset.pos = idx;

            const feeHtml = IS_CEO ? `
                                                                                <div>
                                                                                    <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Fee / Orang (Rp)</label>
                                                                                    <input type="hidden" name="positions[${idx}][fee]" class="fee-h" value="0">
                                                                                    <div class="fee-prefix"><span>Rp</span><input type="text" class="fee-d" placeholder="0" oninput="fmtFeePos(this)" autocomplete="off"></div>
                                                                                </div>` : `<input type="hidden" name="positions[${idx}][fee]" value="0">`;

            block.innerHTML = `
                                                                                <div class="pos-header" style="grid-template-columns: 1fr ${IS_CEO ? '1fr' : ''} auto;">
                                                                                    <div>
                                                                                        <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Nama Posisi</label>
                                                                                        <input type="text" name="positions[${idx}][name]" class="form-control"  required>
                                                                                    </div>
                                                                                    ${feeHtml}
                                                                                    <div><button type="button" class="btn-remove-pos" onclick="removePos(this)">✕ Hapus</button></div>
                                                                                </div>
                                                                                <div class="emp-grid">${buildGrid(idx)}</div>
                                                                                <div class="dates-wrap">
                                                                                    <div class="dates-header" id="dates-header-${idx}">Detail Tugas</div>
                                                                                    <div id="dates-${idx}"></div>
                                                                                </div>`;
            document.getElementById('posContainer').appendChild(block);
            syncRemoveButtons();
        }

        function removePos(btn) { btn.closest('.position-block').remove(); syncRemoveButtons(); }
        function syncRemoveButtons() {
            const blocks = document.querySelectorAll('.position-block');
            blocks.forEach(b => { b.querySelector('.btn-remove-pos').style.display = blocks.length > 1 ? 'block' : 'none'; });
        }

        // Strip dots on submit
        document.getElementById('eventForm').addEventListener('submit', function () {
            document.querySelectorAll('.fee-h').forEach(h => { h.value = h.value.replace(/\./g, ''); });
            const picFee = document.getElementById('pic_fee');
            if (picFee) picFee.value = picFee.value.replace(/\./g, '');
            const ldFee = document.getElementById('loading_fee_hid');
            if (ldFee) ldFee.value = ldFee.value.replace(/\./g, '');
            const uldFee = document.getElementById('unloading_fee_hid');
            if (uldFee) uldFee.value = uldFee.value.replace(/\./g, '');
        });

        // Init 1st position & flatpickr
        window.addEventListener('DOMContentLoaded', () => {
            flatpickr("#event_dates", {
                mode: "multiple",
                dateFormat: "Y-m-d"
            });
            addPos();
        });
    </script>
@endsection