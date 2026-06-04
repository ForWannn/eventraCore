@extends('layouts.app')

@section('title', 'Tambah Karyawan Baru')

@section('content')
<style>
    /* Card Stack */
    .create-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 32px;
        margin-bottom: 24px;
    }
    
    .create-card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .create-card-title svg {
        width: 18px;
        height: 18px;
        color: var(--text-muted);
    }

    /* Form Layout */
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-size: 13.5px; font-weight: 600; color: var(--text-main); }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 12px; font-size: 14px; background: var(--bg-color); color: var(--text-main); transition: border-color 0.15s; outline: none; }
    .form-control:focus { border-color: #2563eb; background-color: var(--card-bg); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08); }
    .error-text { color: #b91c1c; font-size: 12px; margin-top: 4px; }
    .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }

    /* Photo Upload Box (Left Side) */
    .photo-upload-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .photo-upload-area {
        position: relative;
        width: 100%;
        aspect-ratio: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px;
        border: 2px dashed var(--border-color);
        border-radius: 16px;
        /* background: var(--hover-bg); */
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }
    .photo-upload-area:hover { border-color: #9ca3af; }
    .photo-avatar-preview {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
        border-radius: 14px;
    }
    .photo-upload-area.has-image {
        border-style: solid;
        border-color: var(--border-color);
    }
    .photo-upload-area.has-image .photo-avatar-preview {
        display: block;
    }
    .photo-upload-area.has-image .photo-upload-content {
        display: none !important;
    }
    #photoInputCreate { display: none; }

    /* Password field with eye icon */
    .input-wrapper { position: relative; display: flex; align-items: center; }
    .input-wrapper .form-control { padding-right: 44px; }
    .toggle-eye {
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
    .toggle-eye:hover { color: var(--text-main); }
    .toggle-eye svg { width: 16px; height: 16px; stroke-width: 2; }

    /* Roles Selection Grid */
    .role-selection-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
        margin-top: 16px;
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
        align-items: flex-start;
        padding: 20px;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        background: var(--bg-color);
        color: var(--text-main);
        cursor: pointer;
        transition: all 0.2s;
        /* min-height: 120px; */
    }
    
    .role-selection-header {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .role-selection-header svg {
        width: 20px;
        height: 20px;
        color: var(--text-muted);
    }
    
    .role-selection-header span {
        font-size: 14px;
        font-weight: 700;
    }
    
    .role-selection-desc {
        font-size: 11.5px;
        color: var(--text-muted);
        line-height: 1.4;
        text-align: left;
    }
    
    .role-selection-card input[type="radio"]:checked + .role-selection-label {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.04);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }

    .role-selection-card input[type="radio"]:checked + .role-selection-label .role-selection-header svg {
        color: #2563eb;
    }

    [data-theme="dark"] .role-selection-card input[type="radio"]:checked + .role-selection-label {
        border-color: #60a5fa;
        background: rgba(96, 165, 250, 0.08);
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15);
    }

    [data-theme="dark"] .role-selection-card input[type="radio"]:checked + .role-selection-label .role-selection-header svg {
        color: #60a5fa;
    }
    
    .role-selection-label:hover {
        border-color: var(--text-muted);
    }

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
    }
    [data-theme="dark"] .info-note { background: #1e3a5f; border-color: #2563eb; color: #93c5fd; }
    .info-note svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

    /* Footer Buttons */
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

    .create-layout-split {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 28px;
        align-items: start;
    }

    .form-grid-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .create-layout-split {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .photo-upload-container {
            align-self: center !important;
            width: 160px !important;
            margin: 0 auto 12px auto !important;
        }
        .photo-upload-area {
            padding: 16px !important;
        }
    }

    @media (max-width: 640px) {
        .create-card {
            padding: 20px !important;
            border-radius: 16px !important;
            margin-bottom: 16px !important;
        }
        .create-card-title {
            font-size: 14px !important;
            margin-bottom: 16px !important;
        }
        .form-grid-2col {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }
        .form-group {
            margin-bottom: 12px !important;
        }
        .form-group label {
            font-size: 12.5px !important;
            margin-bottom: 6px !important;
        }
        .form-control {
            padding: 8px 12px !important;
            font-size: 13px !important;
            border-radius: 10px !important;
        }
        .role-selection-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 10px !important;
            margin-top: 12px !important;
        }
        .role-selection-label {
            padding: 12px 14px !important;
            border-radius: 12px !important;
        }
        .role-selection-header svg {
            width: 16px !important;
            height: 16px !important;
        }
        .role-selection-header span {
            font-size: 12px !important;
        }
        .role-selection-desc {
            font-size: 10.5px !important;
        }
        .btn-primary, .btn-secondary {
            padding: 8px 20px !important;
            font-size: 12.5px !important;
            border-radius: 10px !important;
        }
        .form-actions-footer {
            width: 100% !important;
            margin-bottom: 24px !important;
        }
        .form-actions-footer .btn-primary {
            width: 100% !important;
            justify-content: center !important;
            padding: 10px 20px !important;
        }
        /* Title adjustments */
        h1[style*="font-size: 24px"] {
            font-size: 18px !important;
        }
        p[style*="font-size: 13.5px"] {
            font-size: 11.5px !important;
        }
        #cropperModal > div {
            padding: 16px !important;
            border-radius: 16px !important;
        }
    }
</style>

<div style="margin-bottom: 28px;">
    <a href="{{ route('users.index') }}" class="btn-back" style="margin-bottom: 16px;">
        <i data-feather="arrow-left"></i>
        <span>Kembali ke Manajemen User</span>
    </a>
    <h1 style="font-size: 24px; font-weight: 700; color: var(--text-main); margin: 0 0 4px 0;">Tambah Karyawan</h1>
    <p style="font-size: 13.5px; color: var(--text-muted); margin: 0; font-weight: 500;">Isi data karyawan baru untuk menambahkan ke sistem.</p>
</div>

@if($errors->any())
    <div class="alert-error" style="max-width: 1000px;">Harap periksa kembali isian formulir di bawah.</div>
@endif

<form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" style="max-width: 1000px;">
    @csrf

    <!-- Card Atas: Informasi Personal & Pekerjaan -->
    <div class="create-card">
        
        <div class="create-layout-split">
            <!-- Left Side: Profile Photo Upload -->
            <div class="photo-upload-container">
                <label style="display: block; margin-bottom: 8px; font-size: 13.5px; font-weight: 600; color: var(--text-main);">Foto Profil</label>
                <div class="photo-upload-area" id="photoUploadArea" onclick="document.getElementById('photoInputCreate').click()">
                    <!-- Image Preview (1:1 aspect ratio) -->
                    <img src="" id="createPhotoPreview" class="photo-avatar-preview" alt="Preview Foto">
                    
                    <!-- Default Upload Content -->
                    <div class="photo-upload-content" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <i data-feather="upload-cloud" style="width: 28px; height: 28px; color: #2563eb; margin-bottom: 8px;"></i>
                        <h5 style="font-size: 13px; font-weight: 600; color: var(--text-main); margin: 0 0 4px 0;">Klik untuk upload foto</h5>
                        <p style="font-size: 11px; color: var(--text-muted); margin: 0;">PNG atau JPG · Maks. 2MB</p>
                    </div>
                </div>
                <input type="hidden" name="cropped_photo" id="croppedPhotoInput">
                <input type="file" id="photoInputCreate" name="photo" accept="image/png,image/jpeg" style="display: none;" onchange="previewCreatePhoto(event)">
            </div>

            <!-- Right Side: Input Grid -->
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="nik">ID Karyawan <span style="color: #ef4444;">*</span></label>
                        <input type="text" id="nik" name="nik" class="form-control"
                               value="{{ old('nik') }}" placeholder="Contoh: LDR-001" required>
                        @error('nik')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                        @error('name')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="email">Email <span style="color: #ef4444;">*</span></label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="{{ old('email') }}" placeholder="nama@eventracore.com" required>
                        @error('email')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="phone">No Telepon</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                               value="{{ old('phone') }}" placeholder="08123456789">
                        @error('phone')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="password">Password <span style="color: #ef4444;">*</span></label>
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
                    <div class="form-group">
                        <label for="division_id">Divisi <span style="color: #ef4444;">*</span></label>
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
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="join_date">Tanggal Bergabung <span style="color: #ef4444;">*</span></label>
                        <input type="date" id="join_date" name="join_date" class="form-control"
                               value="{{ old('join_date', date('Y-m-d')) }}" required>
                        @error('join_date')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="birth_date">Tanggal Lahir</label>
                        <input type="date" id="birth_date" name="birth_date" class="form-control"
                               value="{{ old('birth_date') }}">
                        @error('birth_date')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="employee_type">Tipe Karyawan <span style="color: #ef4444;">*</span></label>
                        <select id="employee_type" name="employee_type" class="form-control" required>
                            <option value="Full Time" {{ old('employee_type', 'Full Time') === 'Full Time' ? 'selected' : '' }}>Full Time</option>
                            <option value="Part Time" {{ old('employee_type') === 'Part Time' ? 'selected' : '' }}>Part Time</option>
                            <option value="Contract" {{ old('employee_type') === 'Contract' ? 'selected' : '' }}>Contract</option>
                            <option value="Internship" {{ old('employee_type') === 'Internship' ? 'selected' : '' }}>Internship</option>
                            <option value="Freelance" {{ old('employee_type') === 'Freelance' ? 'selected' : '' }}>Freelance</option>
                        </select>
                        @error('employee_type')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="gender">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Bawah: Role / Akses -->
    <div class="create-card">
        <div class="create-card-title">
            <!-- <i data-feather="key"></i> -->
            <span>Role</span>
        </div>

        <!-- <div class="info-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>Pilih role untuk menentukan akses karyawan dalam sistem.</span>
        </div> -->

        <div class="role-selection-grid">
            @foreach($roles as $role)
                @if($role->name === 'PIC Event') @continue @endif
                @php
                    $icon = 'user';
                    if ($role->name === 'CEO') {
                        $icon = 'award';
                    } elseif ($role->name === 'GM') {
                        $icon = 'users';
                    } elseif ($role->name === 'Head') {
                        $icon = 'shield';
                    } elseif ($role->name === 'Employee') {
                        $icon = 'user';
                    } elseif ($role->name === 'Intern') {
                        $icon = 'book-open';
                    } elseif ($role->name === 'Freelance') {
                        $icon = 'briefcase';
                    } elseif ($role->name === 'Admin') {
                        $icon = 'settings';
                    }
                @endphp
                <div class="role-selection-card">
                    <input type="radio" id="role_{{ $role->id }}" name="role" value="{{ $role->name }}"
                        {{ old('role', 'Employee') === $role->name ? 'checked' : '' }}>
                    <label for="role_{{ $role->id }}" class="role-selection-label">
                        <div class="role-selection-header">
                            <i data-feather="{{ $icon }}"></i>
                            <span>{{ $role->name }}</span>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>
        @error('role')<div class="error-text" style="margin-top: 12px;">{{ $message }}</div>@enderror
    </div>

    <!-- Form Actions Footer -->
    <div class="form-actions-footer" style="display: flex; justify-content: space-between; align-items: center; max-width: 1000px; margin-top: 12px; margin-bottom: 40px;">
        <button type="submit" class="btn-primary">Simpan Data Karyawan</button>
    </div>
</form>

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

    function previewCreatePhoto(event) {
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
        document.getElementById('photoInputCreate').value = '';
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

        const preview = document.getElementById('createPhotoPreview');
        preview.src = dataUrl;
        document.getElementById('photoUploadArea').classList.add('has-image');

        document.getElementById('croppedPhotoInput').value = dataUrl;

        // Clear original file to prevent heavy transmission & validation bypass
        document.getElementById('photoInputCreate').value = '';

        document.getElementById('cropperModal').style.display = 'none';
        
        cropper.destroy();
        cropper = null;
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
