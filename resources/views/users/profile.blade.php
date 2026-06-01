@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<style>
    /* Header Section */
    .profile-header {
        margin-bottom: 28px;
    }
    .profile-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 4px 0;
    }
    .profile-subtitle {
        font-size: 13.5px;
        color: var(--text-muted);
        margin: 0;
        font-weight: 500;
    }

    /* Main Grid Layout */
    .profile-layout-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 992px) {
        .profile-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Left Card: Overview */
    .left-overview-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 36px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .avatar-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin-bottom: 20px;
    }
    
    .avatar-img {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--border-color);
        background: var(--bg-color);
        display: block;
    }
    
    .avatar-upload-trigger {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #2563eb;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid var(--card-bg);
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .avatar-upload-trigger:hover {
        transform: scale(1.1);
        background: #1d4ed8;
    }
    .avatar-upload-trigger svg {
        width: 16px;
        height: 16px;
    }
    
    .overview-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 4px;
        word-break: break-word;
    }
    
    .overview-division {
        font-size: 13.5px;
        font-weight: 600;
        color: #2563eb;
        margin-bottom: 12px;
    }
    [data-theme="dark"] .overview-division {
        color: #60a5fa;
    }
    
    .overview-role-badge {
        display: inline-block;
        padding: 4px 12px;
        background: rgba(37, 99, 235, 0.08);
        border: 1px solid rgba(37, 99, 235, 0.2);
        color: #2563eb;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 28px;
    }
    [data-theme="dark"] .overview-role-badge {
        background: rgba(96, 165, 250, 0.15);
        color: #60a5fa;
        border-color: rgba(96, 165, 250, 0.2);
    }
    
    .overview-details {
        width: 100%;
        border-top: 1px solid var(--border-color);
        padding-top: 24px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        text-align: left;
    }
    
    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .detail-icon {
        color: var(--text-muted);
        width: 18px;
        height: 18px;
        margin-top: 2px;
        flex-shrink: 0;
    }
    
    .detail-info {
        display: flex;
        flex-direction: column;
    }
    
    .detail-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .detail-value {
        font-size: 13.5px;
        font-weight: 500;
        color: var(--text-main);
        margin-top: 2px;
        word-break: break-all;
    }

    /* Status badge */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 11.5px;
        font-weight: 600;
        width: fit-content;
    }
    .badge-status.success {
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #10b981;
    }
    .badge-status.success .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
    }

    /* Right Section: Form cards */
    .right-form-cards {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .form-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 32px;
    }
    
    .form-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 24px;
    }
    
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 576px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group label {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 8px;
    }
    
    .form-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
        background: var(--bg-color);
        color: var(--text-main);
        outline: none;
        transition: all 0.2s;
    }
    .form-input:focus {
        border-color: #2563eb;
        background: var(--card-bg);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }
    .form-input:disabled, .form-input[readonly] {
        background: var(--hover-bg);
        color: var(--text-muted);
        cursor: not-allowed;
        border-color: var(--border-color);
    }
    
    .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
        background: var(--bg-color);
        color: var(--text-main);
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 16px;
    }
    .form-select:focus {
        border-color: #2563eb;
        background-color: var(--card-bg);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }
    .form-select:disabled {
        background: var(--hover-bg);
        color: var(--text-muted);
        cursor: not-allowed;
        background-image: none;
    }
    
    .textarea-input {
        min-height: 80px;
        resize: vertical;
        font-family: inherit;
    }

    .error-text {
        color: #ef4444;
        font-size: 12.5px;
        margin-top: 6px;
        font-weight: 500;
    }

    /* Ubah Password Card Layout */
    .password-card-layout {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }
    @media (max-width: 576px) {
        .password-card-layout {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    .password-card-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .password-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(139, 92, 246, 0.08);
        border: 1px solid rgba(139, 92, 246, 0.2);
        color: #8b5cf6;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .password-text-info {
        display: flex;
        flex-direction: column;
    }
    .password-text-info h4 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }
    .password-text-info p {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 4px 0 0 0;
    }
    
    .btn-action-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 13px;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-action-outline:hover {
        background: var(--hover-bg);
        border-color: var(--text-muted);
    }

    .btn-submit-blue {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 24px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-submit-blue:hover {
        opacity: 0.9;
    }

    /* Modal Styling */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 999;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.open {
        display: flex;
    }
    .modal-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 28px;
        max-width: 440px;
        width: 90%;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .modal-box h4 {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
    }
    .modal-box p {
        font-size: 13.5px;
        color: var(--text-muted);
        margin-bottom: 20px;
        line-height: 1.5;
    }
    
    .password-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-input-wrapper .form-input {
        padding-right: 42px;
    }
    .password-toggle-btn {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    .password-toggle-btn svg {
        width: 16px;
        height: 16px;
    }
</style>

<div class="profile-header">
    <h1 class="profile-title">Profil Saya</h1>
    <p class="profile-subtitle">Kelola informasi profil dan preferensi akun Anda.</p>
</div>

{{-- Success flash --}}
@if(session('success'))
    <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif

{{-- Validation error flash --}}
@if($errors->any())
    <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        Harap periksa kembali isian formulir di bawah.
        <ul style="margin-top: 8px; margin-left: 16px; font-size: 12.5px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="profile-layout-grid">
    <!-- Left Container: Profile Overview -->
    <div class="left-overview-card">
        <form id="avatarForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Hidden inputs so validation doesn't complain about missing required fields -->
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">
            
            <input type="file" id="photoInput" name="photo" accept="image/png,image/jpeg" style="display: none;" onchange="previewPhoto(event)">
            <input type="hidden" name="cropped_photo" id="croppedPhotoInput">
            <div class="avatar-wrapper">
                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="avatar-img" id="photoPreview">
                <div class="avatar-upload-trigger" onclick="document.getElementById('photoInput').click()" title="Ubah Foto">
                    <i data-feather="camera"></i>
                </div>
            </div>
        </form>

        <h3 class="overview-name">{{ $user->name }}</h3>
        <span class="overview-division">{{ $user->division->name ?? 'Tanpa Divisi' }}</span>
        
        @php
            $primaryRole = $user->roles->where('name', '!=', 'PIC Event')->first()?->name ?? 'Crew';
        @endphp
        <div class="overview-role-badge">{{ $primaryRole }}</div>
        
        <div class="overview-details">
            <!-- Email -->
            <div class="detail-item">
                <i data-feather="mail" class="detail-icon"></i>
                <div class="detail-info">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
            </div>
            <!-- ID / NIK -->
            <div class="detail-item">
                <i data-feather="user" class="detail-icon"></i>
                <div class="detail-info">
                    <span class="detail-label">NIK</span>
                    <span class="detail-value">{{ $user->nik ?? '-' }}</span>
                </div>
            </div>
            <!-- Divisi -->
            <div class="detail-item">
                <i data-feather="briefcase" class="detail-icon"></i>
                <div class="detail-info">
                    <span class="detail-label">Divisi</span>
                    <span class="detail-value">{{ $user->division->name ?? '-' }}</span>
                </div>
            </div>
            <!-- Bergabung Sejak -->
            <div class="detail-item">
                <i data-feather="calendar" class="detail-icon"></i>
                <div class="detail-info">
                    <span class="detail-label">Bergabung Sejak</span>
                    <span class="detail-value">
                        @if($user->join_date)
                            {{ $user->join_date->translatedFormat('d F Y') }}
                        @else
                            {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '01 Januari 2024' }}
                        @endif
                    </span>
                </div>
            </div>
            <!-- Status -->
            <div class="detail-item">
                <i data-feather="check-circle" class="detail-icon"></i>
                <div class="detail-info">
                    <span class="detail-label">Status</span>
                    <span class="badge-status success" style="margin-top: 2px;">
                        <span class="dot"></span>
                        <span>Aktif</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Container: Form Detail -->
    <div class="right-form-cards">
        
        <!-- card 1: Informasi Personal -->
        <form action="{{ route('profile.update') }}" method="POST" class="form-card">
            @csrf
            @method('PUT')
            
            <h3 class="form-title">Informasi Personal</h3>
            
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap">
                    @error('name')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="nik_disp">No Karyawan</label>
                    <input type="text" id="nik_disp" class="form-input" value="{{ $user->nik ?? '-' }}" disabled>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required placeholder="nama@eventracore.com">
                    @error('email')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="phone">No Telepon</label>
                    <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="+62 812-3456-7890">
                    @error('phone')<span class="error-text">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="birth_date">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" class="form-input" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                    @error('birth_date')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="gender">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="form-select">
                        <option value="" disabled {{ is_null($user->gender) ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('gender', $user->gender) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender', $user->gender) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')<span class="error-text">{{ $message }}</span>@enderror
                </div>
            </div>



            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-submit-blue">Simpan Perubahan</button>
            </div>
        </form>

        <!-- card 2: Informasi Pekerjaan -->
        <div class="form-card">
            <h3 class="form-title">Informasi Pekerjaan</h3>
            
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="job_division">Divisi</label>
                    <select id="job_division" class="form-select" disabled>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $user->division_id == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="job_role">Role</label>
                    <input type="text" id="job_role" class="form-input" value="{{ $primaryRole }}" disabled>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="job_manager">Atasan Langsung</label>
                    <select id="job_manager" class="form-select" disabled>
                        <option value="">-</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ $user->direct_manager_id == $mgr->id ? 'selected' : '' }}>
                                {{ $mgr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="job_emp_type">Tipe Karyawan</label>
                    <select id="job_emp_type" class="form-select" disabled>
                        <option value="Full Time" {{ ($user->employee_type ?? 'Full Time') === 'Full Time' ? 'selected' : '' }}>Full Time</option>
                        <option value="Part Time" {{ ($user->employee_type ?? '') === 'Part Time' ? 'selected' : '' }}>Part Time</option>
                        <option value="Intern" {{ ($user->employee_type ?? '') === 'Intern' ? 'selected' : '' }}>Intern</option>
                        <option value="Contract" {{ ($user->employee_type ?? '') === 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Freelance" {{ ($user->employee_type ?? '') === 'Freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-2" style="margin-bottom: 0;">
                <div class="form-group">
                    <label for="job_join_date">Tanggal Bergabung</label>
                    @php
                        $joinDateStr = $user->join_date ? $user->join_date->format('Y-m-d') : ($user->created_at ? $user->created_at->format('Y-m-d') : '2024-01-01');
                    @endphp
                    <input type="date" id="job_join_date" class="form-input" value="{{ $joinDateStr }}" disabled>
                </div>
                <div class="form-group">
                    <label for="job_status">Status</label>
                    <select id="job_status" class="form-select" disabled>
                        <option value="Active" selected>Aktif</option>
                        <option value="Inactive">Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- card 3: Ubah Password -->
        <div class="form-card" style="background: rgba(139, 92, 246, 0.01); border-color: rgba(139, 92, 246, 0.15);">
            <div class="password-card-layout">
                <div class="password-card-left">
                    <div class="password-icon-wrapper">
                        <i data-feather="lock"></i>
                    </div>
                    <div class="password-text-info">
                        <h4>Ubah Password</h4>
                        <p>Pastikan password baru Anda kuat dan aman.</p>
                    </div>
                </div>
                <button type="button" class="btn-action-outline" onclick="openPasswordModal()">Ubah Password</button>
            </div>
        </div>

    </div>
</div>

{{-- Ubah Password Modal --}}
<div class="modal-overlay" id="passwordModal">
    <div class="modal-box">
        <h4>Ubah Password</h4>
        <p>Silakan masukkan password baru Anda di bawah ini untuk memperbarui keamanan akun Anda.</p>
        
        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="new_password">Password Baru</label>
                <div class="password-input-wrapper">
                    <input type="password" id="new_password" name="password" class="form-input" required placeholder="Masukkan password baru">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordModalVisibility('new_password')">
                        <i data-feather="eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="new_password_confirmation">Konfirmasi Password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="new_password_confirmation" name="password_confirmation" class="form-input" required placeholder="Konfirmasi password baru">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordModalVisibility('new_password_confirmation')">
                        <i data-feather="eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" onclick="closePasswordModal()">Batal</button>
                <button type="submit" class="btn-submit-blue" style="border-radius: 10px; padding: 8px 18px; font-size: 13px;">Simpan Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cropping Foto -->
<div id="cropperModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 20px; width: 90%; max-width: 500px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--text-main);">Atur & Potong Foto</h4>
            <button type="button" onclick="closeCropperModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i data-feather="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
        <div style="width: 100%; aspect-ratio: 1; max-height: 300px; background: #000; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
            <img id="cropperImage" src="" style="max-width: 100%; max-height: 100%;">
        </div>
        <!-- Cropper Controls -->
        <div style="display: flex; justify-content: center; gap: 12px; margin-top: 8px; flex-wrap: wrap;">
            <button type="button" class="btn-secondary" onclick="cropper.zoom(0.1)" style="padding: 6px 12px; display: flex; align-items: center; gap: 4px; font-size: 12px;">
                <i data-feather="zoom-in" style="width: 14px; height: 14px;"></i> Perbesar
            </button>
            <button type="button" class="btn-secondary" onclick="cropper.zoom(-0.1)" style="padding: 6px 12px; display: flex; align-items: center; gap: 4px; font-size: 12px;">
                <i data-feather="zoom-out" style="width: 14px; height: 14px;"></i> Perkecil
            </button>
            <button type="button" class="btn-secondary" onclick="cropper.rotate(-90)" style="padding: 6px 12px; display: flex; align-items: center; gap: 4px; font-size: 12px;">
                <i data-feather="rotate-ccw" style="width: 14px; height: 14px;"></i> Putar
            </button>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px; border-top: 1px solid var(--border-color); padding-top: 16px;">
            <button type="button" class="btn-secondary" onclick="closeCropperModal()">Batal</button>
            <button type="button" class="btn-primary" onclick="saveCroppedPhoto()" style="background: #2563eb; color: #fff;">Potong & Simpan</button>
        </div>
    </div>
</div>

<script>
    let cropper;

    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cropperModal').style.display = 'flex';
            const cropperImage = document.getElementById('cropperImage');
            cropperImage.src = e.target.result;

            if (cropper) {
                cropper.destroy();
            }

            setTimeout(() => {
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.9,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false
                });
                
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }, 100);
        };
        reader.readAsDataURL(file);
    }

    function closeCropperModal() {
        document.getElementById('cropperModal').style.display = 'none';
        document.getElementById('photoInput').value = '';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    function saveCroppedPhoto() {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400
        });

        const dataUrl = canvas.toDataURL('image/jpeg');

        const preview = document.getElementById('photoPreview');
        preview.src = dataUrl;

        document.getElementById('croppedPhotoInput').value = dataUrl;

        document.getElementById('photoInput').value = '';

        document.getElementById('cropperModal').style.display = 'none';
        
        cropper.destroy();
        cropper = null;

        // Auto submit the avatar form
        document.getElementById('avatarForm').submit();
    }

    function togglePasswordModalVisibility(inputId) {
        const input = document.getElementById(inputId);
        const btn = input.nextElementSibling;
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<i data-feather="eye-off" style="width: 16px; height: 16px;"></i>';
        } else {
            input.type = 'password';
            btn.innerHTML = '<i data-feather="eye" style="width: 16px; height: 16px;"></i>';
        }
        if (typeof feather !== 'undefined') feather.replace();
    }

    function openPasswordModal() {
        document.getElementById('passwordModal').classList.add('open');
    }
    
    function closePasswordModal() {
        document.getElementById('passwordModal').classList.remove('open');
    }

    // Close modal on overlay click
    document.getElementById('passwordModal').addEventListener('click', function(e) {
        if (e.target === this) closePasswordModal();
    });
</script>
@endsection
