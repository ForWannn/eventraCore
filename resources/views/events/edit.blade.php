@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* Menggunakan style yang sama persis dengan create.blade.php */
        .create-event-grid { display: grid; grid-template-columns: 2.1fr 0.9fr; gap: 24px; align-items: start; margin-bottom: 24px; }
        @media (max-width: 1024px) { .create-event-grid { grid-template-columns: 1fr; } }
        .card-left { padding: 28px; border-radius: 20px; background: var(--card-bg); border: 1px solid var(--border-color); }
        .card-right { padding: 24px; border-radius: 20px; background: var(--card-bg); border: 1px solid var(--border-color); position: sticky; top: 24px; }
        .form-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
        .form-header-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #d97706; flex-shrink: 0; }
        .form-header-text h2 { font-size: 20px; font-weight: 700; color: var(--text-main); }
        .form-header-text p { font-size: 13px; color: var(--text-muted); margin-top: 2px; }
        .section-indicator-title { display: flex; align-items: center; gap: 8px; font-size: 15px; font-weight: 700; color: var(--text-main); margin: 24px 0 16px 0; border-left: 3px solid #d97706; padding-left: 10px; }
        
        .alert-info-custom { display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; border-radius: 12px; background: rgba(37, 99, 235, 0.05); border: 1px solid rgba(37, 99, 235, 0.1); color: #1E40AF; font-size: 13px; font-weight: 500; margin-bottom: 24px; line-height: 1.5; }
        [data-theme="dark"] .alert-info-custom { background: rgba(30, 58, 95, 0.2); border-color: rgba(30, 58, 95, 0.4); color: #93C5FD; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 13.5px; font-weight: 600; color: var(--text-main); }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 12px; font-size: 14px; background: var(--bg-color); color: var(--text-main); transition: all 0.2s; }
        .form-control:focus { outline: none; border-color: #d97706; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
        .input-with-icon { position: relative; display: flex; align-items: center; }
        .input-with-icon svg, .input-with-icon i { position: absolute; left: 14px; color: var(--text-muted); pointer-events: none; width: 16px; height: 16px; }
        .input-with-icon .form-control { padding-left: 42px; }

        /* Custom Select & Grid (Sama seperti create) */
        .custom-select-wrapper { position: relative; user-select: none; }
        .custom-select { border: 1px solid var(--border-color); border-radius: 12px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-size: 14px; background: var(--bg-color); color: var(--text-main); height: 41.5px; }
        .custom-select .sel { display: flex; align-items: center; gap: 10px; }
        .pic-opts { position: absolute; top: 100%; left: 0; right: 0; background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 12px; margin-top: 6px; max-height: 260px; overflow-y: auto; z-index: 20; display: none; }
        .pic-opts.open { display: block; }
        .pic-opt { padding: 10px 14px; display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-main); }
        .pic-opt:hover { background: var(--hover-bg); }
        .avatar-sm { width: 26px; height: 26px; border-radius: 50%; object-fit: cover; }

        .position-block { border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; margin-bottom: 20px; background: var(--bg-color); }
        .pos-header { display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: end; margin-bottom: 20px; }
        .btn-remove-pos { background: none; border: 1px solid #FECACA; color: var(--danger); border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px; }
        
        .pos-participants-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .pos-search-wrapper { position: relative; width: 250px; }
        .pos-search-wrapper input { padding: 6px 12px 6px 32px; font-size: 13px; border-radius: 8px; }
        
        .emp-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-bottom: 20px; }
        .emp-lbl { cursor: pointer; display: block; }
        .emp-cb { display: none; }
        .emp-inner { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 14px 8px; border: 1px solid var(--border-color); border-radius: 12px; background: var(--sidebar-bg); min-height: 110px; gap: 6px; position: relative; }
        .emp-cb:checked+.emp-inner { border-color: #d97706; background: rgba(245, 158, 11, 0.04); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.08); }
        .emp-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--border-color); }
        .emp-cb:checked+.emp-inner .emp-avatar { border-color: #d97706; }
        .emp-name { font-size: 12px; font-weight: 600; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }
        .emp-div { font-size: 10px; color: var(--text-muted); width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .emp-close-btn { position: absolute; top: 8px; right: 8px; color: var(--text-muted); display: none; }
        .emp-cb:checked+.emp-inner .emp-close-btn { display: block; width: 12px; height: 12px; }
        .emp-lbl.pic-hidden { display: none; }

        .dates-wrap { margin-top: 16px; border-top: 1px solid var(--border-color); padding-top: 16px; }
        .dates-header { font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 12px; text-transform: uppercase; }
        .dates-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 16px; margin-top: 12px; width: 100%; }
        .date-row { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 12px; flex-wrap: wrap; }
        .date-row-user { display: flex; align-items: center; gap: 10px; min-width: 160px; }
        .date-row-user img { width: 28px; height: 28px; border-radius: 50%; }
        .date-row-inputs { display: flex; align-items: center; gap: 10px; flex: 1; flex-wrap: wrap; }
        .date-input-sm { padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; width: 160px; background: var(--bg-color); color: var(--text-main); }
        .btn-full-event { padding: 7px 14px; background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; }
        
        .toggle-btn input { display: none; }
        .badge-opt { display: inline-block; padding: 6px 10px; font-size: 11px; font-weight: 700; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; }
        .toggle-btn input:checked+.badge-opt.ld { background: #E0E7FF; border-color: #6366F1; color: #4338CA; }
        .toggle-btn input:checked+.badge-opt.uld { background: #FEE2E2; border-color: #EF4444; color: #B91C1C; }

        .btn-add-pos { width: 100%; padding: 12px; border: 2px dashed var(--border-color); border-radius: 14px; background: none; color: var(--text-muted); font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-add-pos:hover { border-color: #94A3B8; color: var(--text-main); }
        .form-actions-row { margin-top: 32px; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .btn-cancel { padding: 10px 24px; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; }
        .btn-submit-premium { padding: 12px 28px; background: #d97706; color: #FFFFFF; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        
        .form-grid-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-grid-3col { display: grid; grid-template-columns: 1.2fr 0.9fr 0.9fr; gap: 20px; }
    </style>

    @php
        $pic = $event->participants->where('pivot.is_pic', true)->first();
        $picId = $pic ? $pic->id : null;

        // Persiapkan data posisi yang sudah ada ke dalam format JSON untuk Javascript
        $existingPositions = $event->positions->map(function($pos) {
            $members = [];
            foreach($pos->members as $m) {
                $members[$m->id] = [
                    'id' => $m->id,
                    'work_dates' => is_string($m->pivot->work_dates) ? json_decode($m->pivot->work_dates, true) : ($m->pivot->work_dates ?? []),
                    'is_loading' => $m->pivot->is_loading,
                    'is_unloading' => $m->pivot->is_unloading,
                ];
            }
            return [
                'id' => $pos->id,
                'name' => $pos->name,
                'members' => $members
            ];
        });

        $eventDatesStr = implode(', ', $event->event_dates ?? []);
        $startTimeStr = $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '';
        $endTimeStr = $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '';
    @endphp

    <div class="create-event-grid">
        <div class="card-left">
            <div class="form-header">
                <div class="form-header-icon">
                    <i data-feather="edit-3"></i>
                </div>
                <div class="form-header-text">
                    <h2>Edit Event</h2>
                    <p>Perbarui informasi event, ubah panitia, atau perbarui jadwal kerjanya.</p>
                </div>
            </div>
            
            <div class="alert-info-custom">
                <i data-feather="info" style="width: 24px; height: 24px; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <strong>Tip Pergantian Kru:</strong> Jika ada orang yang diganti di tengah berjalannya event, <b>jangan hapus centang/uncheck</b> namanya. Cukup hapus sisa harinya pada bagian kalender (Detail Tugas) agar histori kerjanya tetap tersimpan. Lalu centang kru baru, dan atur tanggal mulai kerjanya.
                </div>
            </div>

            @if($errors->any())
                <div style="background:#fee2e2;color:#b91c1c;padding:12px;border-radius:12px;font-size:13px;margin-bottom:20px;border:1px solid #fca5a5;">
                    Harap periksa kembali isian formulir di bawah.
                </div>
            @endif

            <form action="{{ route('events.update', $event->id) }}" method="POST" id="eventForm">
                @csrf
                @method('PUT')

                <div class="section-indicator-title">
                    <span>Informasi Event</span>
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label>Nama Event </label>
                        <div class="input-with-icon">
                            <i data-feather="file-text"></i>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $event->name) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>PIC</label>
                        <div class="custom-select-wrapper" id="picWrap">
                            <input type="hidden" name="pic_id" id="pic_id_input" value="{{ old('pic_id', $picId) }}" required>
                            <div class="custom-select" id="picBtn">
                                <div class="sel" id="picSel">
                                    @if($pic)
                                        <img src="{{ $pic->photo_url }}" class="avatar-sm">
                                        <span>{{ $pic->name }}</span>
                                    @else
                                        <i data-feather="user"></i>
                                        <span style="color: var(--text-muted);">Pilih PIC Event</span>
                                    @endif
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
                    <label>Deskripsi</label>
                    <div class="input-with-icon">
                        <i data-feather="edit-2" style="top: 14px;"></i>
                        <textarea name="description" class="form-control" rows="2" style="padding-left: 42px; resize: none;">{{ old('description', $event->description) }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>Lokasi </label>
                    <div class="input-with-icon">
                        <i data-feather="map-pin"></i>
                        <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}" required>
                    </div>
                </div>

                <div class="form-grid-3col">
                    <div class="form-group">
                        <label>Tanggal Event </label>
                        <div class="input-with-icon">
                            <i data-feather="calendar"></i>
                            <input type="text" id="event_dates" name="event_dates" class="form-control" value="{{ old('event_dates', $eventDatesStr) }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Jam Mulai </label>
                        <div class="input-with-icon">
                            <i data-feather="clock"></i>
                            <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $startTimeStr) }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Jam Selesai </label>
                        <div class="input-with-icon">
                            <i data-feather="clock"></i>
                            <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $endTimeStr) }}" required>
                        </div>
                    </div>
                </div>

                <div class="section-indicator-title" style="margin-top: 32px;">
                    <span>Posisi & Peserta</span>
                </div>

                <div id="posContainer"></div>

                <button type="button" class="btn-add-pos" onclick="addPos()">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i>
                    Tambah Posisi Baru
                </button>

                <div class="form-actions-row">
                    <a href="{{ route('events.show', $event->id) }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit-premium">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        <div class="card-right">
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: var(--text-main);">Informasi Edit</h3>
            <p style="font-size: 13px; color: var(--text-muted); line-height: 1.6;">
                Pastikan Anda memeriksa ulang penugasan anggota. Perubahan form ini akan memengaruhi laporan absensi dan pengeluaran terkait anggota yang bertugas di Laporan Akhir event ini.
            </p>
        </div>
    </div>

    <script>
        const ALL_USERS = {!! json_encode($usersJson) !!};
        const EXISTING_POSITIONS = {!! json_encode($existingPositions) !!};
        let posCount = 0;
        let currentPic = "{{ $picId }}";

        // Logic Custom Dropdown PIC
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
            });
        }

        // Live Search Posisi
        function filterPosEmployees(input) {
            const searchVal = input.value.toLowerCase().trim();
            const block = input.closest('.position-block');
            block.querySelectorAll('.emp-lbl').forEach(lbl => {
                const name = lbl.querySelector('.emp-name').textContent.toLowerCase();
                const div = lbl.querySelector('.emp-div').textContent.toLowerCase();
                const isPicHidden = lbl.classList.contains('pic-hidden');
                
                if (isPicHidden) { lbl.style.display = 'none'; return; }
                lbl.style.display = (name.includes(searchVal) || div.includes(searchVal)) ? 'block' : 'none';
            });
        }

        // Listener Checkbox Anggota
        document.getElementById('posContainer').addEventListener('change', function (e) {
            if (!e.target.classList.contains('emp-cb')) return;
            const block = e.target.closest('.position-block');
            const posIdx = block.dataset.pos;
            const userId = e.target.value;
            const user = ALL_USERS.find(u => String(u.id) === String(userId));
            toggleDateRow(e.target.checked, posIdx, userId, user);
        });

        function toggleDateRow(checked, posIdx, userId, user, existingData = null) {
            const container = document.getElementById(`dates-${posIdx}`);
            const header = document.getElementById(`dates-header-${posIdx}`);

            if (checked) {
                const row = document.createElement('div');
                row.className = 'date-row';
                row.dataset.uid = userId;
                
                const isLoading = existingData?.is_loading ? 'checked' : '';
                const isUnloading = existingData?.is_unloading ? 'checked' : '';
                const datesValue = existingData?.work_dates ? existingData.work_dates.join(', ') : '';

                row.innerHTML = `
                    <div class="date-row-user">
                        <img src="${user.photo}" alt="${user.name}">
                        <span>${user.name}</span>
                    </div>
                    <div class="date-row-inputs">
                        <input type="text" name="positions[${posIdx}][member_dates][${userId}][work_dates]" class="date-input-sm multi-date" placeholder="Multi tanggal" value="${datesValue}">
                        <button type="button" class="btn-full-event" onclick="setFullEvent(${posIdx},${userId})">Full Event</button>
                        <div class="date-row-toggles" style="margin-left: auto; display: flex; gap: 6px;">
                            <label class="toggle-btn" title="Tugas Loading">
                                <input type="checkbox" name="positions[${posIdx}][member_loading][${userId}]" ${isLoading}>
                                <span class="badge-opt ld">LD</span>
                            </label>
                            <label class="toggle-btn" title="Tugas Unloading">
                                <input type="checkbox" name="positions[${posIdx}][member_unloading][${userId}]" ${isUnloading}>
                                <span class="badge-opt uld">ULD</span>
                            </label>
                        </div>
                    </div>
                `;
                container.appendChild(row);
                
                flatpickr(row.querySelector('.multi-date'), {
                    mode: "multiple",
                    dateFormat: "Y-m-d",
                    defaultDate: existingData?.work_dates || []
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

        function buildGrid(idx, existingMembers = {}) {
            return ALL_USERS.map(u => {
                const isChecked = existingMembers[u.id] ? 'checked' : '';
                const isPicHidden = currentPic && String(u.id) === String(currentPic) ? 'pic-hidden' : '';
                return `
                <label class="emp-lbl ${isPicHidden}" data-uid="${u.id}">
                    <input type="checkbox" name="positions[${idx}][members][]" value="${u.id}" class="emp-cb" ${isChecked}>
                    <div class="emp-inner">
                        <img src="${u.photo}" class="emp-avatar" alt="${u.name}">
                        <div class="emp-info">
                            <div class="emp-name">${u.name}</div>
                            <div class="emp-div">${u.division}</div>
                        </div>
                        <i data-feather="x" class="emp-close-btn"></i>
                    </div>
                </label>
            `}).join('');
        }

        function addPos(existingPosData = null) {
            const idx = posCount++;
            const block = document.createElement('div');
            block.className = 'position-block';
            block.dataset.pos = idx;

            const hiddenIdInput = existingPosData ? `<input type="hidden" name="positions[${idx}][id]" value="${existingPosData.id}">` : '';
            const posNameValue = existingPosData ? existingPosData.name : '';

            block.innerHTML = `
                ${hiddenIdInput}
                <div class="pos-header">
                    <div style="flex: 1;">
                        <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Nama Posisi</label>
                        <div class="input-with-icon">
                            <i data-feather="briefcase"></i>
                            <input type="text" name="positions[${idx}][name]" class="form-control" value="${posNameValue}" required placeholder="Contoh: MC, Loading, dll.">
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn-remove-pos" onclick="removePos(this)">
                           <i data-feather="trash-2" style="width: 14px; height: 14px;"></i> Hapus Posisi
                        </button>
                    </div>
                </div>
                <div class="pos-participants-header" style="margin-top: 12px;">
                    <div style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Pilih Peserta</div>
                    <div class="pos-search-wrapper">
                        <i data-feather="search"></i>
                        <input type="text" class="form-control pos-search" placeholder="Cari nama atau divisi" oninput="filterPosEmployees(this)">
                    </div>
                </div>
                <div class="emp-grid" style="margin-top: 12px;">
                    ${buildGrid(idx, existingPosData ? existingPosData.members : {})}
                </div>
                <div class="dates-wrap">
                    <div class="dates-header" id="dates-header-${idx}" style="display: none;">Detail Tugas</div>
                    <div id="dates-${idx}" class="dates-grid"></div>
                </div>
            `;
            
            document.getElementById('posContainer').appendChild(block);
            
            // Render detail dates jika ada member yang tersimpan
            if (existingPosData && existingPosData.members) {
                Object.values(existingPosData.members).forEach(mData => {
                    const userObj = ALL_USERS.find(u => String(u.id) === String(mData.id));
                    if(userObj) toggleDateRow(true, idx, mData.id, userObj, mData);
                });
            }

            feather.replace();
            syncRemoveButtons();
        }

        function removePos(btn) {
            btn.closest('.position-block').remove();
            syncRemoveButtons();
        }

        function syncRemoveButtons() {
            const blocks = document.querySelectorAll('.position-block');
            blocks.forEach(b => {
                const btn = b.querySelector('.btn-remove-pos');
                if (btn) btn.style.display = blocks.length > 1 ? 'flex' : 'none';
            });
        }

        // INIT
        window.addEventListener('DOMContentLoaded', () => {
            flatpickr("#event_dates", { mode: "multiple", dateFormat: "Y-m-d" });
            
            if (EXISTING_POSITIONS.length > 0) {
                EXISTING_POSITIONS.forEach(pos => addPos(pos));
            } else {
                addPos(); 
            }

            feather.replace();
        });
    </script>
@endsection