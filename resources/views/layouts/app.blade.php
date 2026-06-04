<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - eventraCore</title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="icon" href="{{ asset('assets/images/Logor7web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <style>
        :root {
            --bg-color: #F8FAFC;
            --sidebar-bg: #FFFFFF;
            --card-bg: #FFFFFF;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border-color: #E5E7EB;
            --primary: #8AAFFF;
            --primary-soft: #DBEAFE;
            --primary-text: #0F172A;
            --btn-primary-text: #FFFFFF;
            --hover-bg: #DBEAFE;
            --success: #10B981;
            --warning: #FFC96E;
            --danger: #EF4444;
            --input-bg: #FFFFFF;
            --input-border: #E5E7EB;
            --hover-primary: #A8C5FF;
            --divider: #E5E7EB;

            /* Status Colors */
            --status-blue: #2563EB;
            --status-blue-soft: rgba(37, 99, 235, 0.08);
            --status-blue-border: rgba(37, 99, 235, 0.15);

            --status-emerald: #10B981;
            --status-emerald-soft: rgba(16, 185, 129, 0.08);
            --status-emerald-border: rgba(16, 185, 129, 0.15);

            --status-indigo: #6366F1;
            --status-indigo-soft: rgba(99, 102, 241, 0.08);
            --status-indigo-border: rgba(99, 102, 241, 0.15);

            --status-teal: #14B8A6;
            --status-teal-soft: rgba(20, 184, 166, 0.08);
            --status-teal-border: rgba(20, 184, 166, 0.15);

            --status-cyan: #06B6D4;
            --status-cyan-soft: rgba(6, 182, 212, 0.08);
            --status-cyan-border: rgba(6, 182, 212, 0.15);

            --status-amber: #F59E0B;
            --status-amber-soft: rgba(245, 158, 11, 0.08);
            --status-amber-border: rgba(245, 158, 11, 0.15);

            --status-rose: #F43F5E;
            --status-rose-soft: rgba(244, 63, 94, 0.08);
            --status-rose-border: rgba(244, 63, 94, 0.15);

            --status-purple: #8B5CF6;
            --status-purple-soft: rgba(139, 92, 246, 0.08);
            --status-purple-border: rgba(139, 92, 246, 0.15);

            --status-slate: #475569;
            --status-slate-soft: rgba(71, 85, 105, 0.08);
            --status-slate-border: rgba(71, 85, 105, 0.15);

            --status-orange: #FB923C;
            --status-orange-soft: rgba(251, 146, 60, 0.08);
            --status-orange-border: rgba(251, 146, 60, 0.15);
        }

        [data-theme="dark"] {
            --bg-color: #121923ff;
            --sidebar-bg: #121923ff;
            --card-bg: #121822ff;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --border-color: #334155;
            --primary: #8AAFFF;
            --primary-hover: #A8C5FF;
            --primary-soft: #1E3A5F;
            --primary-text: #0F172A;
            --btn-secondary-text: #0F172A;
            --hover-bg: #1E3A5F;
            --success: #34D399;
            --warning: #FBBF24;
            --danger: #F87171;
            --input-bg: #1E293B;
            --input-border: #334155;
            --hover-primary: #A8C5FF;
            --divider: #334155;

            /* Status Colors (Dark Mode) */
            --status-blue: #60A5FA;
            --status-blue-soft: rgba(96, 165, 250, 0.15);
            --status-blue-border: rgba(96, 165, 250, 0.2);

            --status-emerald: #34D399;
            --status-emerald-soft: rgba(52, 211, 153, 0.15);
            --status-emerald-border: rgba(52, 211, 153, 0.2);

            --status-indigo: #818CF8;
            --status-indigo-soft: rgba(129, 140, 248, 0.15);
            --status-indigo-border: rgba(129, 140, 248, 0.2);

            --status-teal: #2DD4BF;
            --status-teal-soft: rgba(45, 212, 191, 0.15);
            --status-teal-border: rgba(45, 212, 191, 0.2);

            --status-cyan: #22D3EE;
            --status-cyan-soft: rgba(34, 211, 238, 0.15);
            --status-cyan-border: rgba(34, 211, 238, 0.2);

            --status-amber: #FBBF24;
            --status-amber-soft: rgba(251, 191, 36, 0.15);
            --status-amber-border: rgba(251, 191, 36, 0.2);

            --status-rose: #F87171;
            --status-rose-soft: rgba(248, 113, 113, 0.15);
            --status-rose-border: rgba(248, 113, 113, 0.2);

            --status-purple: #A78BFA;
            --status-purple-soft: rgba(167, 139, 250, 0.15);
            --status-purple-border: rgba(167, 139, 250, 0.2);

            --status-slate: #94A3B8;
            --status-slate-soft: rgba(148, 163, 184, 0.15);
            --status-slate-border: rgba(148, 163, 184, 0.2);

            --status-orange: #FDBA74;
            --status-orange-soft: rgba(253, 186, 116, 0.15);
            --status-orange-border: rgba(253, 186, 116, 0.2);
        }

        *, input, select, textarea, button {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Google Sans Flex', sans-serif !important;
        }

        body {
            font-family: 'Google Sans Flex', sans-serif !important;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
            transition: background-color 0.15s, color 0.15s;
        }

        @guest
        body {
            justify-content: center;
            align-items: center;
            overflow: auto;
        }
        .main-wrapper {
            justify-content: center;
            align-items: center;
            width: 100%;
            min-height: 100vh;
        }
        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 24px 0;
            overflow: auto;
        }
        @endguest

        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: all 0.15s;
        }

        .sidebar-header {
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid var(--border-color);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .nav-links {
            padding: 24px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            overflow-y: auto;
        }

        .nav-links::-webkit-scrollbar {
            width: 4px;
        }
        .nav-links::-webkit-scrollbar-track {
            background: transparent;
        }
        .nav-links::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }
        .nav-links::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            text-decoration: none;
            color: var(--text-muted);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--hover-bg);
            color: var(--text-main);
        }

        .nav-link svg {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .nav-section-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            padding: 0 12px;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            opacity: 0.7;
        }

        .sidebar-footer {
            padding: 24px 12px;
            border-top: 1px solid var(--border-color);
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .top-header {
            height: 64px;
            background-color: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-logout {
            background: none;
            border: 1px solid var(--border-color);
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            color: var(--text-main);
            transition: all 0.15s;
        }

        .btn-logout:hover {
            background: var(--hover-bg);
        }

        .content {
            padding: 32px;
            flex: 1;
            overflow-y: auto;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 24px;
            letter-spacing: -0.5px;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .theme-toggle-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle-btn svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sun-icon {
            display: none;
        }

        [data-theme="dark"] .sun-icon {
            display: block;
        }

        [data-theme="dark"] .moon-icon {
            display: none;
        }

        /* User Mini Card on Sidebar */
        .user-mini-card-wrapper {
            position: relative;
            margin: 8px 12px 4px 12px;
        }
        .user-mini-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: rgba(37, 99, 235, 0.05);
            border: 1px solid rgba(37, 99, 235, 0.1);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        [data-theme="dark"] .user-mini-card {
            background: rgba(30, 58, 95, 0.3);
            border-color: rgba(30, 58, 95, 0.5);
        }
        .user-mini-card:hover {
            background: rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.2);
        }
        .user-mini-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 1.5px solid var(--border-color);
        }
        .user-mini-info {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
            text-align: left;
        }
        .user-mini-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-mini-role {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }
        .user-mini-chevron {
            width: 14px;
            height: 14px;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .sidebar-logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            margin: 4px 12px 12px 12px;
            color: var(--danger);
            background: transparent;
            border: none;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 600;
            width: calc(100% - 24px);
            cursor: pointer;
            text-align: left;
            transition: background 0.15s;
        }
        .sidebar-logout-btn:hover {
            background: rgba(239, 68, 68, 0.05);
        }
        .sidebar-logout-btn svg {
            width: 16px;
            height: 16px;
            stroke-width: 2;
        }

        /* Global Back Button Style (CTA Style) */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: var(--hover-bg);
            border-color: var(--text-muted);
            color: var(--text-main);
        }
        .btn-back svg, .btn-back i {
            width: 16px;
            height: 16px;
            color: var(--text-muted);
            transition: color 0.2s;
        }
        .btn-back:hover svg, .btn-back:hover i {
            color: var(--text-main);
        }

        /* Mobile Responsive Sidebar Drawer Styles */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 998;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            transition: opacity 0.25s ease;
        }
        .sidebar-overlay.active {
            display: block;
        }

        .mobile-toggle-btn {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            cursor: pointer;
            padding: 8px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .mobile-toggle-btn:hover {
            background: var(--hover-bg);
        }
        .mobile-toggle-btn svg {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .mobile-toggle-btn {
                display: inline-flex;
            }
            .top-header {
                padding: 0 16px;
            }
            .content {
                padding: 16px;
            }

            /* Compact Cards on Mobile */
            .card, 
            .stat-card, 
            .admin-stat-card, 
            .admin-panel, 
            .section-card,
            .upcoming-event-card,
            .attendance-panel {
                padding: 16px !important;
                border-radius: 12px !important;
                margin-bottom: 16px !important;
            }

            /* Horizontal Rule margin offset inside cards */
            .stat-card hr {
                margin: 0 -16px 12px -16px !important;
            }

            /* Stats values and labels tuning */
            .stat-card {
                min-height: auto !important;
            }
            .stat-card .stat-value,
            .admin-stat-value {
                font-size: 24px !important;
            }
            .stat-card .stat-label,
            .admin-stat-label {
                font-size: 12px !important;
            }
            .stat-card .stat-icon,
            .admin-stat-icon {
                width: 36px !important;
                height: 36px !important;
                border-radius: 10px !important;
                font-size: 16px !important;
            }
            .stat-card .stat-icon svg,
            .admin-stat-icon svg {
                width: 18px !important;
                height: 18px !important;
            }

            /* Smart banners / Alert cards */
            .smart-banner {
                padding: 12px 16px !important;
                border-radius: 12px !important;
                margin-bottom: 16px !important;
            }
        }
    </style>
</head>

<body>
    @auth
     <div class="sidebar">
         <div class="sidebar-header">
             eventraCore
         </div>
           <div class="nav-links">
               <!-- SECTION 1: OPERASIONAL -->
               <div class="nav-section-label">OPERASIONAL</div>
               @can('view_dashboard')
               <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                   <i data-feather="grid"></i> <span>Dashboard</span>
               </a>
               @endcan
               
               <a href="{{ route('events.index') }}"
                   class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                   <i data-feather="calendar"></i> 
                   <span>{{ Auth::user()->can('crud_events') ? 'Daftar Event' : 'Event' }}</span>
               </a>

               <!-- SECTION 2: PENGATURAN -->
               @if(Auth::user()->can('manage_calendar') || Auth::user()->can('crud_users'))
               <div class="nav-section-label" style="margin-top: 16px;">PENGATURAN</div>
               @can('manage_calendar')
               <a href="{{ route('settings.calendar') }}"
                   class="nav-link {{ request()->routeIs('settings.calendar') ? 'active' : '' }}">
                   <i data-feather="calendar"></i> <span>Kalender</span>
               </a>
               @endcan
               
               @can('crud_users')
               <a href="{{ route('users.index') }}"
                   class="nav-link {{ (request()->routeIs('users.*') && !request()->routeIs('users.permissions')) ? 'active' : '' }}">
                   <i data-feather="users"></i> <span>Manajemen User</span>
               </a>
               @endcan
               @endif

               <!-- SECTION 3: REPORT -->
               @if((Auth::user()->can('weekly_report') && !Auth::user()->hasAnyRole(['Admin', 'Superadmin'])) || Auth::user()->can('leave_request') || Auth::user()->can('leave_approvals'))
               <div class="nav-section-label" style="margin-top: 16px;">REPORT</div>
               @can('weekly_report')
                 @unless(Auth::user()->hasAnyRole(['Admin', 'Superadmin']))
                 <a href="{{ route('weekly.index') }}"
                     class="nav-link {{ request()->routeIs('weekly.index') ? 'active' : '' }}">
                     <i data-feather="file-text"></i> <span>Weekly Report</span>
                 </a>
                 @endunless
               @endcan

               @can('leave_request')
               <a href="{{ route('leave-requests.index') }}"
                   class="nav-link {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
                   <i data-feather="send"></i> <span>Pengajuan Izin/Cuti</span>
               </a>
               @endcan

               @can('leave_approvals')
               <a href="{{ route('leave-approvals.index') }}"
                   class="nav-link {{ request()->routeIs('leave-approvals.*') ? 'active' : '' }}">
                   <i data-feather="check-square"></i> <span>Persetujuan Izin/Cuti</span>
               </a>
               @endcan
               @endif

               <!-- SECTION 4: REKAPITULASI -->
               @if(Auth::user()->can('rekap_weekly') || Auth::user()->can('rekap_absen') || (Auth::user()->hasRole('Head') && optional(Auth::user()->division)->name === 'Finance') || Auth::user()->hasRole(['CEO', 'GM']) || Auth::user()->can('rekap_event') || Auth::user()->events()->wherePivot('is_pic', true)->exists())
               <div class="nav-section-label" style="margin-top: 16px;">REKAPITULASI</div>
               @can('rekap_weekly')
               <a href="{{ route('weekly.recap') }}"
                   class="nav-link {{ request()->routeIs('weekly.recap') || request()->routeIs('weekly.show_user') ? 'active' : '' }}">
                   <i data-feather="layers"></i> <span>Rekap Weekly Report</span>
               </a>
               @endcan

               @can('rekap_absen')
               <a href="{{ route('attendance.recap') }}"
                   class="nav-link {{ request()->routeIs('attendance.recap') ? 'active' : '' }}">
                   <i data-feather="clipboard"></i> 
                   <span>{{ Auth::user()->hasAnyRole(['Admin', 'Superadmin']) ? 'Rekap Absensi Karyawan' : 'Rekap Absensi Harian' }}</span>
               </a>
               @endcan

               @if(optional(Auth::user()->division)->name === 'Finance' || Auth::user()->hasRole(['CEO', 'GM']) || Auth::user()->events()->wherePivot('is_pic', true)->exists() || Auth::user()->can('rekap_event'))
               <a href="{{ route('event-recaps.index') }}"
                   class="nav-link {{ request()->routeIs('event-recaps.index') ? 'active' : '' }}">
                   <i data-feather="bar-chart-2"></i> <span>Rekap Event</span>
               </a>
               <a href="{{ route('event-recaps.history') }}"
                   class="nav-link {{ request()->routeIs('event-recaps.history') ? 'active' : '' }}">
                   <i data-feather="archive"></i> <span>Riwayat Rekap</span>
               </a>
               @endif
               @endif

               <!-- SECTION 5: HISTORY -->
               @if(Auth::user()->can('weekly_history') || Auth::user()->can('attendance_history'))
               <div class="nav-section-label" style="margin-top: 16px;">HISTORY</div>
               @can('weekly_history')
               <a href="{{ route('weekly.history') }}"
                   class="nav-link {{ request()->routeIs('weekly.history') ? 'active' : '' }}">
                   <i data-feather="archive"></i> <span>History Weekly Report</span>
               </a>
               @endcan

               @can('attendance_history')
               @unless(Auth::user()->hasRole('Intern'))
               <a href="{{ route('attendance.history') }}"
                   class="nav-link {{ request()->routeIs('attendance.history') ? 'active' : '' }}">
                   <i data-feather="clock"></i> <span>History Absen</span>
               </a>
               @endunless
               @endcan
               @endif

               <!-- SECTION 6: SUPERADMIN -->
               @if(Auth::user()->hasRole('Superadmin'))
               <div class="nav-section-label" style="margin-top: 16px;">SUPERADMIN</div>
               <a href="{{ route('users.permissions') }}"
                   class="nav-link {{ request()->routeIs('users.permissions') ? 'active' : '' }}">
                   <i data-feather="shield"></i> <span>Hak Akses User</span>
               </a>
               @endif
           </div>
             <!-- Sidebar User Mini Card & Logout -->
             <div class="user-mini-card-wrapper">
                 @if(Auth::user()->hasAnyRole(['Admin', 'Superadmin']))
                 <div class="user-mini-card" style="cursor: default;">
                     <img src="{{ Auth::user()->photo_url }}" class="user-mini-avatar" alt="{{ Auth::user()->name }}">
                     <div class="user-mini-info">
                         <span class="user-mini-name">{{ Auth::user()->name }}</span>
                         @php
                             $userRole = Auth::user()->roles->where('name', '!=', 'PIC Event')->first()?->name ?? 'Crew';
                         @endphp
                         <span class="user-mini-role">{{ $userRole }}</span>
                     </div>
                 </div>
                 @else
                 <a href="{{ route('profile') }}" class="user-mini-card">
                     <img src="{{ Auth::user()->photo_url }}" class="user-mini-avatar" alt="{{ Auth::user()->name }}">
                     <div class="user-mini-info">
                         <span class="user-mini-name">{{ Auth::user()->name }}</span>
                         @php
                             $userRole = Auth::user()->roles->where('name', '!=', 'PIC Event')->first()?->name ?? 'Crew';
                         @endphp
                         <span class="user-mini-role">{{ $userRole }}</span>
                     </div>
                     <i data-feather="chevron-right" class="user-mini-chevron"></i>
                 </a>
                 @endif
             </div>

             <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                 @csrf
                 <button type="submit" class="sidebar-logout-btn">
                     <i data-feather="log-out"></i>
                     <span>Keluar</span>
                 </button>
             </form>
          </div>
          <div class="sidebar-overlay" id="sidebarOverlay"></div>
         @endauth
     
         <div class="main-wrapper">
            @auth
             <div class="top-header">
                 <div>
                      <button class="mobile-toggle-btn" id="mobileSidebarToggle" aria-label="Buka Menu">
                          <i data-feather="menu"></i>
                      </button>
                 </div>
                 <div class="user-info">
                     <button class="theme-toggle-btn" id="themeToggle">
                         <svg class="moon-icon" viewBox="0 0 24 24">
                             <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                         </svg>
                         <svg class="sun-icon" viewBox="0 0 24 24">
                             <circle cx="12" cy="12" r="5"></circle>
                             <line x1="12" y1="1" x2="12" y2="3"></line>
                             <line x1="12" y1="21" x2="12" y2="23"></line>
                             <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                             <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                             <line x1="1" y1="12" x2="3" y2="12"></line>
                             <line x1="21" y1="12" x2="23" y2="12"></line>
                             <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                             <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                         </svg>
                     </button>
                     <span style="font-weight: 600; color: var(--text-main);">Halo, {{ Auth::user()->name }}</span>
                 </div>
             </div>
            @endauth
 
         <div class="content">
            @auth
             <!-- <h1 class="page-title">@yield('title', 'Dasbor')</h1> -->
            @endauth
             @yield('content')
         </div>
     </div>
 
     <script>
         const themeToggle = document.getElementById('themeToggle');
 
         const updateThemeUI = (theme) => {
             if (theme === 'dark') {
                 document.body.setAttribute('data-theme', 'dark');
             } else {
                 document.body.removeAttribute('data-theme');
             }
         };
 
         let currentTheme = localStorage.getItem('theme');
         if (!currentTheme) {
             const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
             currentTheme = prefersDarkScheme.matches ? 'dark' : 'light';
         }
         updateThemeUI(currentTheme);
 
         if (themeToggle) {
             themeToggle.addEventListener('click', () => {
                 const isDark = document.body.hasAttribute('data-theme');
                 const newTheme = isDark ? 'light' : 'dark';
 
                 updateThemeUI(newTheme);
                 localStorage.setItem('theme', newTheme);
             });
         }
     </script>
      <script>
         // Sidebar Mobile Toggle
         const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
         const sidebar = document.querySelector('.sidebar');
         const sidebarOverlay = document.getElementById('sidebarOverlay');

         if (mobileSidebarToggle && sidebar && sidebarOverlay) {
             mobileSidebarToggle.addEventListener('click', () => {
                 sidebar.classList.add('open');
                 sidebarOverlay.classList.add('active');
             });

             sidebarOverlay.addEventListener('click', () => {
                 sidebar.classList.remove('open');
                 sidebarOverlay.classList.remove('active');
             });

             const navLinks = document.querySelectorAll('.nav-link');
             navLinks.forEach(link => {
                 link.addEventListener('click', () => {
                     sidebar.classList.remove('open');
                     sidebarOverlay.classList.remove('active');
                 });
             });
         }

         feather.replace();
      </script>
</body>
</html>