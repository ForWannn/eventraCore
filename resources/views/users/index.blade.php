@extends('layouts.app')

@section('title', 'Manajemen Karyawan')

@section('content')
<style>
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
    th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { color: var(--text-muted); font-weight: 500; font-size: 13px; }
    .btn-create { display: inline-block; padding: 10px 16px; background: var(--primary); color: var(--primary-text); text-decoration: none; border-radius: 12px; font-size: 13px; font-weight: 500; transition: opacity 0.2s; }
    .btn-create:hover { opacity: 0.9; }
    .badge { padding: 4px 8px; background: var(--hover-bg); border: 1px solid var(--border-color); border-radius: 8px; font-size: 12px; margin-right: 4px; }
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .avatar-table { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); flex-shrink: 0; }
</style>

<div class="card">
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h3 style="margin-bottom: 4px;">Daftar Pegawai & Akses</h3>
            <p style="font-size: 13px; color: var(--text-muted);">Kelola data pegawai, anak magang, beserta jabatannya.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-create">+ Tambah Karyawan</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Hak Akses (Role)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td style="color: var(--text-muted);">{{ $user->nik ?? '-' }}</td>
                    <td>
                        <div class="user-cell">
                            <img src="{{ $user->photo_url }}" class="avatar-table" alt="{{ $user->name }}">
                            <span style="font-weight: 500;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color: var(--text-muted);">{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            @if($role->name === 'PIC Event') @continue @endif
                            <span class="badge">{{ $role->name }}</span>
                        @endforeach
                        @if($user->roles->isEmpty())
                            <span style="color: var(--text-muted); font-size: 12px;">(Tidak ada role)</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" style="color: var(--text-muted); text-decoration: none; font-size: 13px; font-weight: 500;">Lihat / Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
