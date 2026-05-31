@extends('layouts.app')

@section('title', 'Edit Profil Karyawan')

@section('content')
<style>
    /* Back Link & Title Header */
    .edit-header {
        margin-bottom: 28px;
    }
    .edit-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 4px 0;
    }
    .edit-subtitle {
        font-size: 13.5px;
        color: var(--text-muted);
        margin: 0;
        font-weight: 500;
    }

    /* Main layout grid */
    .edit-profile-container {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 992px) {
        .edit-profile-container {
            grid-template-columns: 1fr;
        }
    }

    /* Left Card: Profile overview */
    .left-profile-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 36px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .profile-avatar-container {
        position: relative;
        width: 140px;
        height: 140px;
        margin-bottom: 20px;
    }
    
    .profile-avatar-img {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--border-color);
        background: var(--bg-color);
        display: block;
    }
    
    .profile-avatar-upload-btn {
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
    
    .profile-avatar-upload-btn:hover {
        transform: scale(1.1);
        background: #1d4ed8;
    }
    
    .profile-avatar-upload-btn svg {
        width: 16px;
        height: 16px;
    }
    
    .profile-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 4px;
        word-break: break-word;
    }
    
    .profile-division-tag {
        font-size: 13.5px;
        font-weight: 600;
        color: #2563eb;
        margin-bottom: 12px;
    }
    [data-theme="dark"] .profile-division-tag {
        color: #60a5fa;
    }
    
    .profile-role-badge {
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
    [data-theme="dark"] .profile-role-badge {
        background: rgba(96, 165, 250, 0.15);
        color: #60a5fa;
        border-color: rgba(96, 165, 250, 0.2);
    }
    
    .profile-details-list {
        width: 100%;
        border-top: 1px solid var(--border-color);
        padding-top: 24px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        text-align: left;
    }
    
    .profile-detail-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .profile-detail-icon {
        color: var(--text-muted);
        width: 18px;
        height: 18px;
        margin-top: 2px;
        flex-shrink: 0;
    }
    
    .profile-detail-info {
        display: flex;
        flex-direction: column;
    }
    
    .profile-detail-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .profile-detail-value {
        font-size: 13.5px;
        font-weight: 500;
        color: var(--text-main);
        margin-top: 2px;
        word-break: break-all;
    }

    /* Badges */
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
    
    /* Right Card: Form */
    .right-form-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 32px;
    }
    
    .form-section-title {
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
    
    /* Roles Selection Grid */
    .role-selection-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
        margin-top: 8px;
    }
    @media (max-width: 1200px) {
        .role-selection-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 640px) {
        .role-selection-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .role-selection-card {
        position: relative;
    }
    
    .role-selection-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .role-selection-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 16px 10px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-color);
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        min-height: 90px;
    }
    
    .role-selection-label svg {
        width: 22px;
        height: 22px;
        stroke-width: 1.5;
        transition: color 0.2s;
    }
    
    .role-selection-label span {
        font-size: 12px;
        font-weight: 600;
        transition: color 0.2s;
    }
    
    .role-selection-card input[type="radio"]:checked + .role-selection-label {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.04);
        color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }
    
    [data-theme="dark"] .role-selection-card input[type="radio"]:checked + .role-selection-label {
        border-color: #60a5fa;
        background: rgba(96, 165, 250, 0.08);
        color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15);
    }
    
    .role-selection-label:hover {
        border-color: var(--text-muted);
        color: var(--text-main);
    }
    
    /* Password field with eye icon */
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
    
    .password-toggle-btn:hover {
        color: var(--text-main);
    }
    
    .password-toggle-btn svg {
        width: 16px;
        height: 16px;
        stroke-width: 2;
    }

    .error-text {
        color: #ef4444;
        font-size: 12px;
        margin-top: 6px;
        font-weight: 500;
    }
    
    /* Footer buttons */
    .form-footer-actions {
        margin-top: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 24px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-secondary:hover {
        background: var(--hover-bg);
        border-color: var(--text-muted);
    }
    
    .btn-primary {
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
    
    .btn-primary:hover {
        opacity: 0.9;
    }

    /* Modal Overlay */
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
        max-width: 420px;
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
        margin-bottom: 24px;
        line-height: 1.5;
    }
    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    .btn-cancel-modal {
        padding: 8px 18px;
        background: var(--hover-bg);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-cancel-modal:hover {
        background: var(--border-color);
    }
    .btn-confirm-delete {
        padding: 8px 18px;
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
    }
    .btn-confirm-delete:hover {
        opacity: 0.85;
    }
</style>

<div class="edit-header">
    <a href="{{ route('users.index') }}" class="btn-back" style="margin-bottom: 16px;">
        <i data-feather="arrow-left"></i>
        <span>Kembali ke Daftar Karyawan</span>
    </a>
    <h1 class="edit-title">Edit Profil Karyawan</h1>
    <p class="edit-subtitle">Perbarui informasi dan role karyawan.</p>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        {{ session('error') }}
    </div>
@endif
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

<form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="file" id="photoInput" name="photo" accept="image/png,image/jpeg" style="display: none;" onchange="previewPhoto(event)">

    <div class="edit-profile-container">
        
        <!-- Left Container: Profile card overview -->
        <div class="left-profile-card">
            <div class="profile-avatar-container">
                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="profile-avatar-img" id="photoPreview">
                <div class="profile-avatar-upload-btn" onclick="document.getElementById('photoInput').click()" title="Ubah Foto">
                    <i data-feather="camera"></i>
                </div>
            </div>
            
            <h3 class="profile-name">{{ $user->name }}</h3>
            <span class="profile-division-tag">{{ $user->division->name ?? 'Tanpa Divisi' }}</span>
            
            @php
                $primaryRole = $user->roles->where('name', '!=', 'PIC Event')->first()?->name ?? '-';
            @endphp
            <div class="profile-role-badge">{{ $primaryRole }}</div>
            
            <div class="profile-details-list">
                <!-- Email -->
                <div class="profile-detail-item">
                    <i data-feather="mail" class="profile-detail-icon"></i>
                    <div class="profile-detail-info">
                        <span class="profile-detail-label">Email</span>
                        <span class="profile-detail-value">{{ $user->email }}</span>
                    </div>
                </div>
                <!-- ID / NIK -->
                <div class="profile-detail-item">
                    <i data-feather="user" class="profile-detail-icon"></i>
                    <div class="profile-detail-info">
                        <span class="profile-detail-label">ID</span>
                        <span class="profile-detail-value">{{ $user->nik ?? '-' }}</span>
                    </div>
                </div>
                <!-- Divisi -->
                <div class="profile-detail-item">
                    <i data-feather="briefcase" class="profile-detail-icon"></i>
                    <div class="profile-detail-info">
                        <span class="profile-detail-label">Divisi</span>
                        <span class="profile-detail-value">{{ $user->division->name ?? '-' }}</span>
                    </div>
                </div>
                
                <!-- Status -->
                <div class="profile-detail-item">
                    <i data-feather="check-circle" class="profile-detail-icon"></i>
                    <div class="profile-detail-info">
                        <span class="profile-detail-label">Status</span>
                        <span class="badge-status success" style="margin-top: 2px;">
                            <span class="dot"></span>
                            <span>Aktif</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Container: Form Detail -->
        <div class="right-form-card">
            <h3 class="form-section-title">Informasi Karyawan</h3>
            
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="nik">ID Karyawan</label>
                    <input type="text" id="nik" name="nik" class="form-input" value="{{ $user->nik }}" readonly style="cursor: not-allowed;" title="ID Karyawan tidak dapat diubah">
                </div>
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap">
                    @error('name')<span class="error-text">{{ $message }}</span>@enderror
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
                    <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789">
                    @error('phone')<span class="error-text">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="division_id">Divisi</label>
                    <select id="division_id" name="division_id" class="form-select" required>
                        <option value="" disabled>Pilih Divisi</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('division_id')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="join_date">Tanggal Bergabung</label>
                    <input type="date" id="join_date" name="join_date" class="form-input" 
                           value="{{ old('join_date', $user->join_date ? $user->join_date->format('Y-m-d') : '') }}" required>
                    @error('join_date')<span class="error-text">{{ $message }}</span>@enderror
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
                    <select id="gender" name="gender" class="form-select" required>
                        <option value="" disabled>Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('gender', $user->gender) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender', $user->gender) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')<span class="error-text">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="employee_type">Tipe Karyawan</label>
                    <select id="employee_type" name="employee_type" class="form-select" required>
                        <option value="Full Time" {{ old('employee_type', $user->employee_type) === 'Full Time' ? 'selected' : '' }}>Full Time</option>
                        <option value="Part Time" {{ old('employee_type', $user->employee_type) === 'Part Time' ? 'selected' : '' }}>Part Time</option>
                        <option value="Contract" {{ old('employee_type', $user->employee_type) === 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Internship" {{ old('employee_type', $user->employee_type) === 'Internship' ? 'selected' : '' }}>Internship</option>
                        <option value="Freelance" {{ old('employee_type', $user->employee_type) === 'Freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                    @error('employee_type')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <!-- spacing -->
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>Role</label>
                <div class="role-selection-grid">
                    @foreach($roles as $role)
                        @if($role->name === 'PIC Event') @continue @endif
                        @php
                            $icon = 'user';
                            if ($role->name === 'CEO') $icon = 'award';
                            elseif ($role->name === 'GM') $icon = 'users';
                            elseif ($role->name === 'Head') $icon = 'shield';
                            elseif ($role->name === 'Employee') $icon = 'user';
                            elseif ($role->name === 'Intern') $icon = 'book-open';
                            elseif ($role->name === 'Freelance') $icon = 'briefcase';
                        @endphp
                        <div class="role-selection-card">
                            <input type="radio" id="role_{{ $role->id }}" name="role" value="{{ $role->name }}"
                                {{ old('role', $user->roles->where('name', '!=', 'PIC Event')->first()?->name) === $role->name ? 'checked' : '' }}>
                            <label for="role_{{ $role->id }}" class="role-selection-label">
                                <i data-feather="{{ $icon }}"></i>
                                <span>{{ $role->name }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('role')<span class="error-text" style="margin-top: 8px;">{{ $message }}</span>@enderror
            </div>

            <!-- Change Password Section (Optional) -->
            <div style="margin-top: 36px; border-top: 1px dashed var(--border-color); padding-top: 28px;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-main); margin-bottom: 20px;">
                    Ubah Password   
                </h4>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password baru">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password')">
                                <i data-feather="eye"></i>
                            </button>
                        </div>
                        @error('password')<span class="error-text">{{ $message }}</span>@enderror
                    </div>  
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Konfirmasi password baru">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_confirmation')">
                                <i data-feather="eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="form-footer-actions">
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </div>

    </div>
</form>

{{-- Danger Zone --}}
@if(auth()->id() !== $user->id)
<div class="card" style="border: 1px dashed rgba(239, 68, 68, 0.4); background: rgba(239, 68, 68, 0.01); margin-top: 28px; padding: 20px 24px; border-radius: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h4 style="font-size: 14.5px; font-weight: 700; color: #ef4444; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <i data-feather="alert-triangle" style="width: 16px; height: 16px;"></i>
                Hapus Akun Karyawan
            </h4>
            <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">Tindakan ini bersifat permanen dan seluruh data history terkait karyawan ini akan dihapus.</p>
        </div>
        <button type="button" style="background: #ef4444; color: #fff; border: none; padding: 10px 20px; border-radius: 12px; font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity 0.2s;" onclick="openDeleteModal()">Hapus Karyawan</button>
    </div>
</div>
@endif

{{-- Delete Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <h4>Konfirmasi Hapus Karyawan</h4>
        <p>
            Anda akan menghapus data karyawan <strong>{{ $user->name }}</strong> (NIK: {{ $user->nik ?? '-' }}) secara permanen. Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin?
        </p>
        <div class="modal-actions">
            <button class="btn-cancel-modal" onclick="closeDeleteModal()">Batal</button>
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Ya, Hapus Karyawan</button>
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

    function togglePasswordVisibility(inputId) {
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
