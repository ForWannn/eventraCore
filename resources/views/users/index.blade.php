@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: 1fr; }
    }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 108px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.03);
    }
    .stat-card-content {
        display: flex;
        flex-direction: column;
    }
    .stat-card .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .stat-card .stat-icon.blue    { background: var(--status-blue-soft); color: var(--status-blue); border: 1px solid var(--status-blue-border); }
    .stat-card .stat-icon.emerald { background: var(--status-emerald-soft); color: var(--status-emerald); border: 1px solid var(--status-emerald-border); }
    .stat-card .stat-icon.amber   { background: var(--status-amber-soft); color: var(--status-amber); border: 1px solid var(--status-amber-border); }
    .stat-card .stat-icon.violet  { background: var(--status-purple-soft); color: var(--status-purple); border: 1px solid var(--status-purple-border); }
    .stat-card .stat-icon.slate   { background: var(--status-slate-soft); color: var(--status-slate); border: 1px solid var(--status-slate-border); }
    .stat-card .stat-icon.orange  { background: var(--status-orange-soft); color: var(--status-orange); border: 1px solid var(--status-orange-border); }

    .stat-card .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .stat-card .stat-value {
        font-size: 26px;
        font-weight: 700;
        color: var(--text-main);
        margin-top: 4px;
        line-height: 1.1;
    }
    .stat-card .stat-sub {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
        margin-top: 2px;
    }

    /* Control Bar & Filter styling */
    .control-bar {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
        align-items: center;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        padding: 16px 20px;
        border-radius: 16px;
    }
    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 240px;
    }
    .search-wrapper input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-color);
        color: var(--text-main);
        font-size: 13.5px;
        outline: none;
        transition: all 0.2s;
    }
    .search-wrapper input:focus {
        border-color: #2563eb;
        background: var(--card-bg);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }
    .search-wrapper .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        width: 16px;
        height: 16px;
        pointer-events: none;
    }
    .filter-wrapper {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .filter-select {
        padding: 10px 16px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-color);
        color: var(--text-main);
        font-size: 13px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-select:focus {
        border-color: #2563eb;
        background: var(--card-bg);
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
    
    .badge-role {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
    }

    /* Table & Actions styling */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { color: var(--text-muted); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-create { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 12px; font-size: 13px; font-weight: 600; transition: opacity 0.2s; }
    .btn-create:hover { opacity: 0.9; }
    .user-cell { display: flex; align-items: center; gap: 12px; }
    .avatar-table { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--border-color); flex-shrink: 0; }

    /* Pagination controls */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
        flex-wrap: wrap;
        gap: 16px;
    }
    .pagination-info {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .pagination-buttons {
        display: flex;
        gap: 6px;
    }
    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 8px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pagination-btn:hover:not(:disabled) {
        background: var(--hover-bg);
        border-color: var(--text-muted);
    }
    .pagination-btn.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 640px) {
        /* Card & Padding tweaks */
        .card {
            padding: 16px !important;
            border-radius: 16px !important;
            box-sizing: border-box !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .card > div:first-of-type {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            margin-bottom: 20px !important;
            box-sizing: border-box !important;
            width: 100% !important;
        }
        .card h2 {
            font-size: 15px !important;
        }
        .card p {
            font-size: 11px !important;
        }
        
        .btn-create {
            width: 100% !important;
            justify-content: center !important;
            padding: 10px 16px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
        }
        
        /* Stats Grid & Cards */
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
            margin-bottom: 16px !important;
        }
        .stat-card {
            padding: 12px !important;
            border-radius: 12px !important;
            min-height: auto !important;
            gap: 10px !important;
        }
        .stat-card .stat-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            font-size: 14px !important;
        }
        .stat-card .stat-icon svg,
        .stat-card .stat-icon i {
            width: 16px !important;
            height: 16px !important;
        }
        .stat-card .stat-label {
            font-size: 9px !important;
        }
        .stat-card .stat-value {
            font-size: 20px !important;
            margin-top: 2px !important;
        }
        .stat-card .stat-sub {
            font-size: 9px !important;
            margin-top: 1px !important;
        }

        /* Control Bar & Filter styling */
        .control-bar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            padding: 12px !important;
            border-radius: 12px !important;
            margin-bottom: 16px !important;
        }
        .search-wrapper {
            width: 100% !important;
            min-width: auto !important;
            flex: none !important;
        }
        .search-wrapper input {
            padding: 8px 12px 8px 36px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
        }
        .search-wrapper .search-icon {
            left: 12px !important;
            width: 14px !important;
            height: 14px !important;
        }
        .filter-wrapper {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            width: 100% !important;
            gap: 8px !important;
        }
        .filter-select {
            width: 100% !important;
            padding: 8px 10px !important;
            font-size: 11px !important;
            border-radius: 8px !important;
        }

        /* Table to Stack Card layout */
        .table-container {
            border: none !important;
            overflow: visible !important;
        }
        table, thead, tbody, th, td, tr {
            display: block !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        thead {
            display: none !important;
        }
        tbody#userTableBody {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 12px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        #noResultsRow {
            grid-column: span 2 !important;
        }
        .user-row {
            background: var(--bg-color) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 14px !important;
            padding: 12px !important;
            margin-bottom: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            height: 100% !important;
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            gap: 6px !important;
        }
        .user-row td {
            padding: 0 !important;
            border: none !important;
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            font-size: 11px !important;
            line-height: 1.4 !important;
        }
        /* Row Header: Avatar & Name cell */
        .user-row td:nth-child(2) {
            border-bottom: 1px solid var(--border-color) !important;
            padding-bottom: 8px !important;
            margin-bottom: 6px !important;
            display: block !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .user-row td:nth-child(2) .user-cell {
            flex-direction: column !important;
            align-items: center !important;
            gap: 6px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .user-row td:nth-child(2) .avatar-table {
            width: 44px !important;
            height: 44px !important;
        }
        .user-row td:nth-child(2) span {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: var(--text-main) !important;
            word-break: break-word !important;
            text-align: center !important;
            display: block !important;
            width: 100% !important;
        }
        
        /* Prepend Label NIK */
        .user-row td:nth-child(1) {
            color: var(--text-muted) !important;
            font-size: 10.5px !important;
            margin-bottom: 2px !important;
        }
        /* .user-row td:nth-child(1):before {
            content: "NIK: ";
            font-weight: 600;
        } */
        
        /* Email truncation */
        .user-row td:nth-child(3) {
            color: var(--text-muted) !important;
            font-size: 10px !important;
            margin-bottom: 4px !important;
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            max-width: 100% !important;
        }
        
        /* Role styling */
        .user-row td:nth-child(4) {
            margin-bottom: 4px !important;
        }
        .badge-role {
            font-size: 10px !important;
            padding: 2px 6px !important;
        }
        
        /* Division styling */
        .user-row td:nth-child(5) {
            font-weight: 600 !important;
            color: var(--text-main) !important;
            margin-bottom: 4px !important;
        }
        
        /* Status styling */
        .user-row td:nth-child(6) {
            margin-bottom: 4px !important;
        }
        .badge-status {
            font-size: 10px !important;
            padding: 2px 6px !important;
        }

        /* Action buttons card footer */
        .user-row td:nth-child(7) {
            margin-top: auto !important;
            border-top: 1px solid var(--border-color) !important;
            padding-top: 8px !important;
            width: 100% !important;
        }
        .user-row td:nth-child(7) a {
            width: 100% !important;
            justify-content: center !important;
            padding: 6px 8px !important;
            background: var(--hover-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-size: 11px !important;
        }
        
        /* Pagination Container adjustments */
        .pagination-container {
            flex-direction: column !important;
            align-items: center !important;
            margin-top: 16px !important;
            gap: 12px !important;
        }
        .pagination-info {
            font-size: 11.5px !important;
        }
        .pagination-btn {
            min-width: 30px !important;
            height: 30px !important;
            font-size: 11px !important;
            border-radius: 8px !important;
        }
    }
</style>

<div class="stats-grid">
    <!-- Card 1: Total User -->
    <div class="stat-card">
        <div class="stat-icon blue"><i data-feather="users"></i></div>
        <div class="stat-card-content">
            <span class="stat-label">Total User</span>
            <span class="stat-value">{{ $users->count() }}</span>
            <span class="stat-sub">Orang</span>
        </div>
    </div>

    <!-- Card 2: Direksi & Kepala Divisi -->
    <div class="stat-card">
        <div class="stat-icon slate"><i data-feather="briefcase"></i></div>
        <div class="stat-card-content">
            <span class="stat-label">Direksi & Head</span>
            <span class="stat-value">{{ $users->filter(fn($u) => $u->hasAnyRole(['Director', 'GM', 'Head']))->count() }}</span>
            <span class="stat-sub">Orang</span>
        </div>
    </div>

    <!-- Card 3: Inhouse -->
    <div class="stat-card">
        <div class="stat-icon emerald"><i data-feather="user"></i></div>
        <div class="stat-card-content">
            <span class="stat-label">Inhouse</span>
            <span class="stat-value">{{ $users->filter(fn($u) => $u->hasAnyRole(['Employee', 'PIC Event']))->count() }}</span>
            <span class="stat-sub">Orang</span>
        </div>
    </div>

    <!-- Card 4: Intern -->
    <div class="stat-card">
        <div class="stat-icon orange"><i data-feather="user"></i></div>
        <div class="stat-card-content">
            <span class="stat-label">Intern</span>
            <span class="stat-value">{{ $users->filter(fn($u) => $u->hasRole('Intern'))->count() }}</span>
            <span class="stat-sub">Orang</span>
        </div>
    </div>
</div>

<div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 24px;">
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

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0;">Daftar Pegawai & Akses</h2>
            <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px; font-weight: 500; margin-bottom: 0;">Kelola data pegawai, anak magang, beserta jabatannya.</p>
        </div>
        @can('crud_users')
        <a href="{{ route('users.create') }}" class="btn-create">
            <i data-feather="plus" style="width: 16px; height: 16px;"></i>
            <span>Tambah Karyawan</span>
        </a>
        @endcan
    </div>

    <!-- Control Bar: Search & Filters -->
    <div class="control-bar">
        <div class="search-wrapper">
            <i data-feather="search" class="search-icon"></i>
            <input type="text" id="searchQuery" placeholder="Cari nama, ID atau Email" />
        </div>
        <div class="filter-wrapper">
            <!-- Division / Department Filter -->
            <select id="filterDept" class="filter-select">
                <option value="">Semua Departemen</option>
                <option value="Creative">Creative</option>
                <option value="Operasional">Operasional</option>
                <option value="Finance">Finance</option>
                <option value="Account Executive">Account Executive</option>
                <option value="Leader">Leader</option>
            </select>
            <!-- Role Filter -->
            <select id="filterRole" class="filter-select">
                <option value="">Semua Role</option>
                <option value="Director">Director</option>
                <option value="GM">GM</option>
                <option value="Head">Head</option>
                <option value="Employee">Employee</option>
                <option value="Intern">Intern</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <table>
            <thead>
                <tr style="border-bottom: 1.5px solid var(--border-color);">
                    <th style="width: 12%;">ID</th>
                    <th style="width: 25%;">Nama </th>
                    <th style="width: 23%;">Email</th>
                    <th style="width: 15%;">Role</th>
                    <th style="width: 15%;">Divis</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @foreach($users as $user)
                @php
                    $userRoles = $user->roles->pluck('name')->filter(fn($r) => $r !== 'PIC Event');
                @endphp
                <tr class="user-row" 
                    data-nik="{{ $user->nik ?? '-' }}" 
                    data-name="{{ $user->name }}" 
                    data-email="{{ $user->email }}" 
                    data-division="{{ $user->division->name ?? '' }}" 
                    data-role="{{ $userRoles->implode(',') }}"
                    style="border-bottom: 1px solid var(--border-color);">
                    <td style="color: var(--text-muted); font-weight: 600;">{{ $user->nik ?? '-' }}</td>
                    <td>
                        <div class="user-cell">
                            <img src="{{ $user->photo_url }}" class="avatar-table" alt="{{ $user->name }}">
                            <span style="font-weight: 600; color: var(--text-main);">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color: var(--text-muted); font-weight: 500;">{{ $user->email }}</td>
                    <td>
                        @forelse($userRoles as $roleName)
                            <span class="badge-role">{{ $roleName }}</span>
                        @empty
                            <span style="color: var(--text-muted); font-size: 12px;">-</span>
                        @endforelse
                    </td>
                    <td style="color: var(--text-main); font-weight: 500;">{{ $user->division->name ?? '-' }}</td>
                    <td>
                        <span class="badge-status success">
                            <span class="dot"></span>
                            <span>Aktif</span>
                        </span>
                    </td>
                    <td>
                        @can('crud_users')
                        <a href="{{ route('users.edit', $user->id) }}" style="display: inline-flex; align-items: center; gap: 6px; color: #2563eb; text-decoration: none; font-size: 13.5px; font-weight: 700;">
                            <span>Edit</span>
                        </a>
                        @else
                        <a href="{{ route('users.show', $user->id) }}" style="display: inline-flex; align-items: center; gap: 6px; color: #2563eb; text-decoration: none; font-size: 13.5px; font-weight: 700;">
                            <span>Detail</span>
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforeach
                <tr id="noResultsRow" style="display: none;">
                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px 16px; font-weight: 500;">
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                            <i data-feather="users" style="width: 32px; height: 32px; stroke-width: 1.5; opacity: 0.5;"></i>
                            <span>Tidak ada data karyawan yang cocok dengan pencarian Anda</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination-container">
        <div class="pagination-info" id="paginationInfo">
            Menampilkan 1 - 10 dari {{ $users->count() }} karyawan
        </div>
        <div class="pagination-buttons" id="paginationButtons">
            <!-- Dynamically populated via JS -->
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredRows = [];

    function initTable() {
        const rows = Array.from(document.querySelectorAll('.user-row'));
        const searchInput = document.getElementById('searchQuery');
        const deptFilter = document.getElementById('filterDept');
        const roleFilter = document.getElementById('filterRole');

        function applyFilter() {
            const query = searchInput.value.toLowerCase().trim();
            const dept = deptFilter.value;
            const role = roleFilter.value;

            filteredRows = rows.filter(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const email = row.getAttribute('data-email').toLowerCase();
                const nik = row.getAttribute('data-nik').toLowerCase();
                const rowDept = row.getAttribute('data-division');
                const rowRoles = row.getAttribute('data-role').split(',');

                const matchesSearch = !query || name.includes(query) || email.includes(query) || nik.includes(query);
                const matchesDept = !dept || rowDept === dept;
                const matchesRole = !role || rowRoles.includes(role);

                return matchesSearch && matchesDept && matchesRole;
            });

            currentPage = 1;
            renderTable();
        }

        searchInput.addEventListener('input', applyFilter);
        deptFilter.addEventListener('change', applyFilter);
        roleFilter.addEventListener('change', applyFilter);

        applyFilter();
    }

    function renderTable() {
        const rows = Array.from(document.querySelectorAll('.user-row'));
        
        // Hide all rows
        rows.forEach(r => r.style.display = 'none');

        const totalItems = filteredRows.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIdx = (currentPage - 1) * itemsPerPage;
        const endIdx = Math.min(startIdx + itemsPerPage, totalItems);

        // Show only matching rows on the current page
        for (let i = startIdx; i < endIdx; i++) {
            filteredRows[i].style.display = 'table-row';
        }

        const noResultsRow = document.getElementById('noResultsRow');
        if (totalItems === 0) {
            if (noResultsRow) noResultsRow.style.display = 'table-row';
        } else {
            if (noResultsRow) noResultsRow.style.display = 'none';
        }

        renderPagination(totalItems, totalPages, startIdx + 1, endIdx);
    }

    function renderPagination(totalItems, totalPages, displayStart, displayEnd) {
        const info = document.getElementById('paginationInfo');
        const buttonsContainer = document.getElementById('paginationButtons');

        if (totalItems === 0) {
            info.textContent = 'Tidak ada data karyawan yang cocok';
            buttonsContainer.innerHTML = '';
            buttonsContainer.style.display = 'none';
            if (typeof feather !== 'undefined') feather.replace();
            return;
        } else {
            info.textContent = `Menampilkan ${displayStart} - ${displayEnd} dari ${totalItems} karyawan`;
            buttonsContainer.style.display = 'flex';
        }

        buttonsContainer.innerHTML = '';

        // Prev Button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn';
        prevBtn.innerHTML = '<i data-feather="chevron-left" style="width: 14px; height: 14px;"></i>';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            currentPage--;
            renderTable();
        };
        buttonsContainer.appendChild(prevBtn);

        // Page Number Buttons
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, currentPage + 1);

        if (currentPage === 1) {
            endPage = Math.min(totalPages, 3);
        } else if (currentPage === totalPages) {
            startPage = Math.max(1, totalPages - 2);
        }

        for (let p = startPage; p <= endPage; p++) {
            const pBtn = document.createElement('button');
            pBtn.className = `pagination-btn ${p === currentPage ? 'active' : ''}`;
            pBtn.textContent = p;
            pBtn.onclick = () => {
                currentPage = p;
                renderTable();
            };
            buttonsContainer.appendChild(pBtn);
        }

        // Next Button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn';
        nextBtn.innerHTML = '<i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => {
            currentPage++;
            renderTable();
        };
        buttonsContainer.appendChild(nextBtn);

        if (typeof feather !== 'undefined') feather.replace();
    }

    document.addEventListener('DOMContentLoaded', initTable);
</script>
@endsection
