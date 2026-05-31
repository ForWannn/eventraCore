@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
<style>
    /* Back Link & Title Header */
    .show-header {
        margin-bottom: 28px;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        transition: color 0.15s;
    }
    .back-link:hover {
        color: #2563eb;
    }
    .back-link svg {
        width: 16px;
        height: 16px;
    }
    .show-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
        margin: 12px 0 4px 0;
    }
    .show-subtitle {
        font-size: 13.5px;
        color: var(--text-muted);
        margin: 0;
        font-weight: 500;
    }

    /* Main layout grid */
    .show-profile-container {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 992px) {
        .show-profile-container {
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
    
    /* Right Card: Form (Read-only styled) */
    .right-info-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 32px;
    }
    
    .section-title {
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
        background: var(--hover-bg);
        color: var(--text-muted);
        outline: none;
        cursor: not-allowed;
    }
    
    .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
        background: var(--hover-bg);
        color: var(--text-muted);
        outline: none;
        cursor: not-allowed;
        appearance: none;
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
        cursor: not-allowed;
        text-align: center;
        min-height: 90px;
        transition: all 0.2s;
    }
    
    .role-selection-label svg {
        width: 22px;
        height: 22px;
        stroke-width: 1.5;
    }
    
    .role-selection-label span {
        font-size: 12px;
        font-weight: 600;
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
    
    /* Footer buttons */
    .form-footer-actions {
        margin-top: 32px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
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
</style>

<div class="show-header">
    <a href="{{ route('users.index') }}" class="back-link">
        <i data-feather="arrow-left"></i>
        <span>Kembali ke Daftar Karyawan</span>
    </a>
    <h1 class="show-title">Detail Profil Karyawan</h1>
    <p class="show-subtitle">Menampilkan data lengkap dan informasi jabatan karyawan.</p>
</div>

<div class="show-profile-container">
    
    <!-- Left Container: Profile card overview -->
    <div class="left-profile-card">
        <div class="profile-avatar-container">
            <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="profile-avatar-img">
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
    <div class="right-info-card">
        <h3 class="section-title">Informasi Karyawan</h3>
        
        <div class="form-grid-2">
            <div class="form-group">
                <label for="nik">NIK</label>
                <input type="text" id="nik" class="form-input" value="{{ $user->nik }}" disabled>
            </div>
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" class="form-input" value="{{ $user->name }}" disabled>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="email">Email</label>
            <input type="email" id="email" class="form-input" value="{{ $user->email }}" disabled>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="division_id">Divisi</label>
                <select id="division_id" class="form-select" disabled>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ $user->division_id == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="join_date">Tanggal Bergabung</label>
                <input type="date" id="join_date" class="form-input" 
                       value="{{ $user->join_date ? $user->join_date->format('Y-m-d') : '' }}" disabled>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
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
                        <input type="radio" id="role_{{ $role->id }}" name="role" value="{{ $role->name }}" disabled
                            {{ $user->roles->where('name', '!=', 'PIC Event')->first()?->name === $role->name ? 'checked' : '' }}>
                        <label for="role_{{ $role->id }}" class="role-selection-label">
                            <i data-feather="{{ $icon }}"></i>
                            <span>{{ $role->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action buttons -->
        <div class="form-footer-actions">
            <a href="{{ route('users.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </div>

</div>

@endsection
