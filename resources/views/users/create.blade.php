@extends('layouts.app')

@section('title', 'Tambah Karyawan Baru')

@section('content')
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 12px; font-size: 14px; background: var(--bg-color); color: var(--text-main); transition: border-color 0.15s; }
    .form-control:focus { outline: none; border-color: #9ca3af; }
    .btn-submit { display: inline-block; padding: 10px 24px; background: var(--primary); color: var(--primary-text); text-decoration: none; border-radius: 12px; border: none; font-size: 14px; font-weight: 500; cursor: pointer; transition: opacity 0.2s; }
    .btn-submit:hover { opacity: 0.85; }
    .error-text { color: #b91c1c; font-size: 12px; margin-top: 4px; }
    .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }

    /* Role radio cards — sama persis dengan edit.blade */
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

    /* Password toggle */
    .input-wrapper { position: relative; }
    .input-wrapper .form-control { padding-right: 44px; }
    .toggle-eye {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--text-muted);
        background: none;
        border: none;
        padding: 0;
        display: flex;
        align-items: center;
    }
    .toggle-eye svg { width: 18px; height: 18px; }

    /* Photo upload */
    .photo-upload-area {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 16px 20px;
        border: 1.5px dashed var(--border-color);
        border-radius: 16px;
        background: var(--hover-bg);
        margin-bottom: 24px;
        cursor: pointer;
        transition: border-color 0.15s;
    }
    .photo-upload-area:hover { border-color: #9ca3af; }
    .photo-preview-wrap { position: relative; }
    .photo-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
        display: block;
    }
    .photo-upload-text h5 { font-size: 14px; font-weight: 600; margin-bottom: 3px; }
    .photo-upload-text p { font-size: 12px; color: var(--text-muted); }
    #photoInputCreate { display: none; }

    /* Info box */
    .info-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        font-size: 12px;
        color: #1e40af;
        margin-bottom: 20px;
    }
    [data-theme="dark"] .info-note { background: #1e3a5f; border-color: #2563eb; color: #93c5fd; }
    .info-note svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
</style>

<div class="card" style="max-width: 820px;">

    @if($errors->any())
        <div class="alert-error">Harap periksa kembali isian formulir di bawah.</div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Photo Upload --}}
        <div class="photo-upload-area" onclick="document.getElementById('photoInputCreate').click()">
            <div class="photo-preview-wrap">
                <img src="https://ui-avatars.com/api/?name=Karyawan+Baru&background=e5e7eb&color=6b7280"
                     id="createPhotoPreview" class="photo-avatar" alt="Preview Foto">
            </div>
            <div class="photo-upload-text">
                <h5>Foto Profil</h5>
                <p>Klik untuk upload foto · PNG atau JPG · Maks. 2MB</p>
            </div>
        </div>
        <input type="file" id="photoInputCreate" name="photo" accept="image/png,image/jpeg"
               onchange="previewCreatePhoto(event)">

        {{-- Row 1: NIK & Nama --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="nik">No Karyawan</label>
                <input type="text" id="nik" name="nik" class="form-control"
                       value="{{ old('nik') }}" required >
                @error('nik')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name') }}" required >
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Row 2: Email & Password --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email') }}" required >
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-control"
                           value="password123" required>
                    <button type="button" class="toggle-eye" onclick="togglePassword()" title="Tampilkan/sembunyikan">
                        <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="division_id">Divisi</label>
                <select id="division_id" name="division_id" class="form-control" required>
                    <option value="" disabled selected>Pilih Divisi</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
                @error('division_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="join_date">Tanggal Bergabung</label>
                <input type="date" id="join_date" name="join_date" class="form-control"
                       value="{{ old('join_date', date('Y-m-d')) }}" required>
                @error('join_date')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 24px 0;">

        <div class="form-group">
            <label>Role</label>
            <div class="info-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Role <strong>CEO</strong> dan <strong>GM</strong> memiliki akses penuh ke seluruh manajemen sistem.</span>
            </div>

            <div class="role-grid">
                @foreach($roles as $role)
                    @if($role->name === 'PIC Event') @continue @endif
                    <div class="role-card">
                        <input type="radio" id="role_{{ $role->id }}" name="role"
                               value="{{ $role->name }}"
                               {{ old('role', 'Employee') === $role->name ? 'checked' : '' }}>
                        <label for="role_{{ $role->id }}" class="role-label">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
            @error('role')<div class="error-text" style="margin-top: 8px;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-top: 32px; display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('users.index') }}"
               style="color: var(--text-muted); text-decoration: none; font-size: 14px;">← Kembali ke Daftar</a>
            <button type="submit" class="btn-submit">Simpan Data Karyawan</button>
        </div>
    </form>
</div>

<script>
    function previewCreatePhoto(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => document.getElementById('createPhotoPreview').src = e.target.result;
        reader.readAsDataURL(file);
    }

    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
        } else {
            input.type = 'password';
            icon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        }
    }

</script>
@endsection
