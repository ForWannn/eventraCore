@extends('layouts.app')

@section('title', 'Edit Data Karyawan')

@section('content')
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 12px; font-size: 14px; background: var(--bg-color); color: var(--text-main); }
    .form-control:focus { outline: none; border-color: #9ca3af; }
    .btn-submit { display: inline-block; padding: 10px 24px; background: var(--primary); color: var(--primary-text); text-decoration: none; border-radius: 12px; border: none; font-size: 14px; font-weight: 500; cursor: pointer; transition: opacity 0.2s; }
    .btn-submit:hover { opacity: 0.85; }
    .error-text { color: #b91c1c; font-size: 12px; margin-top: 4px; }

    /* Profile hero */
    .profile-hero {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 24px;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        margin-bottom: 28px;
    }
    .profile-photo-wrapper {
        position: relative;
        width: 80px;
        height: 80px;
        flex-shrink: 0;
        cursor: pointer;
    }
    .profile-photo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--border-color);
        display: block;
        transition: opacity 0.2s;
    }
    .photo-overlay {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: rgba(0,0,0,0.45);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        gap: 3px;
        text-align: center;
        letter-spacing: 0.3px;
    }
    .photo-overlay svg { width: 18px; height: 18px; }
    .profile-photo-wrapper:hover .photo-overlay { opacity: 1; }
    .profile-photo-wrapper:hover .profile-photo { opacity: 0.6; }
    #photoInput { display: none; }
    .profile-info h4 { font-size: 18px; font-weight: 600; margin-bottom: 4px; }
    .profile-info p { font-size: 13px; color: var(--text-muted); }
    .role-badge {
        display: inline-block;
        padding: 4px 10px;
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        margin-top: 6px;
        margin-right: 4px;
    }

    .role-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; }
    .role-card { position: relative; }
    .role-card input[type="radio"] { display: none; }
    .role-label {
        display: block;
        padding: 8px 16px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        color: var(--text-muted);
    }
    .role-card input[type="radio"]:checked + .role-label {
        border-color: var(--primary);
        color: var(--text-main);
        background: var(--hover-bg);
    }
    .role-label:hover { border-color: #9ca3af; color: var(--text-main); }
    
    /* Alert success */
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }
    .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }

    /* Danger Zone */
    .danger-zone { margin-top: 16px; padding: 18px 24px; border: 1px dashed #fca5a5; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; }
    .danger-zone-text h5 { font-size: 14px; font-weight: 600; color: #b91c1c; margin-bottom: 3px; }
    .danger-zone-text p { font-size: 12px; color: var(--text-muted); }
    .btn-danger { padding: 8px 18px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s; }
    .btn-danger:hover { background: #fecaca; border-color: #f87171; }

    /* Modal */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 100; display: none; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 28px; max-width: 400px; width: 90%; box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
    .modal-box h4 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
    .modal-box p { font-size: 13px; color: var(--text-muted); margin-bottom: 24px; line-height: 1.6; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .btn-cancel-modal { padding: 8px 18px; background: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 10px; font-size: 13px; cursor: pointer; transition: all 0.15s; }
    .btn-cancel-modal:hover { background: var(--border-color); }
    .btn-confirm-delete { padding: 8px 18px; background: #b91c1c; color: #fff; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity 0.15s; }
    .btn-confirm-delete:hover { opacity: 0.85; }
</style>

<div class="card" style="max-width: 820px;">

    {{-- Success flash --}}
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Error flash --}}
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    {{-- Validation error flash --}}
    @if($errors->any())
        <div class="alert-error">Harap periksa kembali isian formulir di bawah.</div>
    @endif

    {{-- Profile Hero --}}
    <div class="profile-hero">
        <div class="profile-photo-wrapper" onclick="document.getElementById('photoInput').click()" title="Ganti foto">
            <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="profile-photo" id="photoPreview">
            <div class="photo-overlay">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                    <circle cx="12" cy="13" r="4"/>
                </svg>
                Ganti Foto
            </div>
        </div>
        <div class="profile-info">
            <h4>{{ $user->name }}</h4>
            <p>NIK: {{ $user->nik ?? '-' }} &nbsp;·&nbsp; {{ $user->division->name ?? 'Tanpa Divisi' }}</p>
            <div>
                @foreach($user->roles as $role)
                    @if($role->name === 'PIC Event') @continue @endif
                    <span class="role-badge">{{ $role->name }}</span>
                @endforeach
            </div>
        </div>
    </div>
 
    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="file" id="photoInput" name="photo" accept="image/png,image/jpeg" onchange="previewPhoto(event)">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="nik">No. Induk Karyawan (NIK)</label>
                <input type="text" id="nik" name="nik" class="form-control"
                    value="{{ old('nik', $user->nik) }}" required placeholder="Contoh: CRE-008">
                @error('nik')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-control"
                    value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Row 2: Email --}}
        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control"
                value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        {{-- Row 3: Divisi & Gaji --}}
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="division_id">Divisi / Departemen</label>
                <select id="division_id" name="division_id" class="form-control" required>
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}"
                            {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
                @error('division_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 24px 0;">

        {{-- Role Selection --}}
        <div class="form-group">
            <label>Role</label>
            <div class="role-grid">
                @foreach($roles as $role)
                    @if($role->name === 'PIC Event') @continue @endif
                    <div class="role-card">
                        <input type="radio" id="role_{{ $role->id }}" name="role"
                            value="{{ $role->name }}"
                            {{ old('role', $user->roles->where('name', '!=', 'PIC Event')->first()?->name) === $role->name ? 'checked' : '' }}>
                        <label for="role_{{ $role->id }}" class="role-label">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
            @error('role')<div class="error-text" style="margin-top: 8px;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-top: 32px; display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('users.index') }}"
               style="color: var(--text-muted); text-decoration: none; font-size: 14px;">← Kembali ke Daftar</a>
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </div>
    </form>

    {{-- Danger Zone --}}
    @if(auth()->id() !== $user->id)
    <div class="danger-zone">
        <div class="danger-zone-text">
            <h5>Hapus Karyawan</h5>
            <p>Tindakan ini permanen dan tidak dapat dibatalkan.</p>
        </div>
        <button type="button" class="btn-danger" onclick="openDeleteModal()">Hapus Karyawan</button>
    </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <h4>Konfirmasi Hapus Karyawan</h4>
        <p>
            Anda akan menghapus data <strong>{{ $user->name }}</strong> (NIK: {{ $user->nik ?? '-' }}) secara permanen.
            Semua data terkait akan ikut terhapus. Lanjutkan?
        </p>
        <div class="modal-actions">
            <button class="btn-cancel-modal" onclick="closeDeleteModal()">Batal</button>
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Ya, Hapus Sekarang</button>
            </form>
        </div>
    </div>
</div>

<script>
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }



    function openDeleteModal() {
        document.getElementById('deleteModal').classList.add('open');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
    }
    // Close modal on overlay click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endsection
