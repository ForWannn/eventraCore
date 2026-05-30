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
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Google Sans Flex', sans-serif;
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
    </style>
</head>

<body>
    @auth
     <div class="sidebar">
         <div class="sidebar-header">
             eventraCore
         </div>
         <div class="nav-links">
             <div class="nav-section-label">OPERASIONAL</div>
             <a href="{{ route('dashboard') }}"
                 class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                 <i data-feather="grid"></i> <span>Dashboard</span>
             </a>
             
             @role('CEO')
             <a href="{{ route('users.index') }}"
                 class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                 <i data-feather="users"></i> <span>Manajemen Karyawan</span>
             </a>
             @endrole
             
             <a href="{{ route('events.index') }}"
                 class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                 <i data-feather="calendar"></i> 
                 <span>{{ Auth::user()->hasRole(['CEO', 'GM']) ? 'Daftar Event' : 'My Events' }}</span>
             </a>
 
             <div class="nav-section-label" style="margin-top: 16px;">REPORT & HISTORY</div>
             <a href="{{ route('weekly.index') }}"
                 class="nav-link {{ request()->routeIs('weekly.index') ? 'active' : '' }}">
                 <i data-feather="file-text"></i> <span>Weekly Report</span>
             </a>
 
             @role('CEO|GM')
                <a href="{{ route('weekly.recap') }}"
                    class="nav-link {{ request()->routeIs('weekly.recap') || request()->routeIs('weekly.show_user') ? 'active' : '' }}">
                    <i data-feather="layers"></i> <span>Rekap Weekly Report</span>
                </a>
                <a href="{{ route('attendance.recap') }}"
                    class="nav-link {{ request()->routeIs('attendance.recap') ? 'active' : '' }}">
                    <i data-feather="clipboard"></i> <span>Rekap Absensi Harian</span>
                </a>
                 <a href="{{ route('weekly.history') }}"
                     class="nav-link {{ request()->routeIs('weekly.history') ? 'active' : '' }}">
                     <i data-feather="archive"></i> <span>Riwayat Weekly Report</span>
                 </a>
             @else
                 <a href="{{ route('weekly.history') }}"
                     class="nav-link {{ request()->routeIs('weekly.history') ? 'active' : '' }}">
                     <i data-feather="archive"></i> <span>History Weekly Report</span>
                 </a>
                 <a href="{{ route('attendance.history') }}"
                     class="nav-link {{ request()->routeIs('attendance.history') ? 'active' : '' }}">
                     <i data-feather="clock"></i> <span>Riwayat Absensi</span>
                 </a>
             @endrole
 
             @if((Auth::user()->hasRole('Head') && optional(Auth::user()->division)->name === 'Finance') || Auth::user()->hasRole(['CEO', 'GM']))
                 <a href="#" class="nav-link">
                     <i data-feather="bar-chart-2"></i> <span>Rekapitulasi Event</span>
                 </a>
             @endif
         </div>
         <div class="sidebar-footer">
             <div class="nav-link" style="font-size: 12px;">© 2026 eventraCore</div>
         </div>
     </div>
    @endauth
 
     <div class="main-wrapper">
        @auth
         <div class="top-header">
             <div>
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
                 <span>Halo, {{ Auth::user()->name }}</span>
                 <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                     @csrf
                     <button type="submit" class="btn-logout">Keluar</button>
                 </form>
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
       feather.replace();
     </script>
</body>
</html>