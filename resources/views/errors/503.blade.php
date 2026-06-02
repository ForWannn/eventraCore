<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Maintenance - eventraCore</title>
    <link rel="icon" href="{{ asset('assets/images/Logor7web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root {
            --bg-color: #F3F4F6;
            --card-bg: #FFFFFF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --primary: #2563EB;
            --primary-soft: rgba(37, 99, 235, 0.05);
            --primary-text: #2563EB;
            --badge-bg: rgba(37, 99, 235, 0.08);
            --badge-text: #2563EB;
            --divider-color: #E2E8F0;
        }

        [data-theme="dark"] {
            --bg-color: #0F172A;
            --card-bg: #1E293B;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --border-color: #334155;
            --primary: #3B82F6;
            --primary-soft: rgba(59, 130, 246, 0.1);
            --primary-text: #60A5FA;
            --badge-bg: rgba(59, 130, 246, 0.15);
            --badge-text: #60A5FA;
            --divider-color: #334155;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Google Sans Flex', sans-serif !important;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Top Header Brand (Centred above card) */
        .brand-header {
            margin-bottom: 24px;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-align: center;
        }
        .brand-header span {
            color: #2563eb;
        }
        [data-theme="dark"] .brand-header span {
            color: #60a5fa;
        }

        /* Maintenance Main Card */
        .maintenance-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            width: 100%;
            max-width: 960px;
            display: flex;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 24px;
        }

        /* Left Container: Graphic Illustration */
        .left-container {
            width: 42%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        /* Right Container: Content */
        .right-container {
            width: 58%;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-left: 1px solid var(--border-color);
        }

        /* System Maintenance Badge */
        .badge-maintenance {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--badge-bg);
            border: 1px solid rgba(37, 99, 235, 0.1);
            padding: 6px 12px;
            border-radius: 8px;
            color: var(--badge-text);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            width: fit-content;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }
        .badge-maintenance svg {
            width: 12px;
            height: 12px;
        }

        .title {
            font-size: 32px;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text-main);
            margin-bottom: 14px;
            letter-spacing: -0.5px;
        }

        .description {
            font-size: 13.5px;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        /* Estimation Box */
        .estimation-box {
            display: flex;
            align-items: center;
            background: var(--primary-soft);
            border: 1px solid var(--border-color);
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
        }
        .estimation-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        .estimation-left svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
        }
        .estimation-details {
            display: flex;
            flex-direction: column;
        }
        .estimation-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .estimation-value {
            font-size: 15px;
            font-weight: 800;
            color: var(--primary-text);
            margin-top: 2px;
        }
        .estimation-divider {
            width: 1px;
            height: 36px;
            background: var(--divider-color);
            margin: 0 20px;
        }
        .estimation-right {
            font-size: 12px;
            color: var(--text-muted);
            max-width: 140px;
            line-height: 1.4;
            font-weight: 500;
        }

        /* Progress Bar Section */
        .progress-section {
            margin-bottom: 28px;
        }
        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .progress-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .progress-pct {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--primary);
        }
        .progress-track {
            height: 8px;
            background: var(--divider-color);
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
        }
        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 10px;
            width: 65%;
        }

        /* Info Row Grid */
        .info-grid {
            display: flex;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--card-bg);
        }
        .info-col {
            flex: 1;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .info-col:not(:last-child) {
            border-right: 1px solid var(--border-color);
        }
        .info-icon-wrapper {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--primary-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
        }
        .info-icon-wrapper svg {
            width: 16px;
            height: 16px;
        }
        .info-col-details {
            display: flex;
            flex-direction: column;
        }
        .info-col-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-col-value {
            font-size: 11.5px;
            font-weight: 800;
            color: var(--text-main);
            margin-top: 1px;
        }

        /* Centered Refresh Button */
        .action-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .btn-refresh {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: #2563EB;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
        }
        .btn-refresh:hover {
            opacity: 0.9;
        }
        .btn-refresh svg {
            width: 16px;
            height: 16px;
        }

        .refresh-notice {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Outside Footer */
        .footer-support {
            margin-top: 24px;
            font-size: 12.5px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }
        .footer-support a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }
        .footer-support a:hover {
            text-decoration: underline;
        }
        .footer-support svg {
            width: 14px;
            height: 14px;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .maintenance-card {
                flex-direction: column;
                max-width: 500px;
            }
            .left-container {
                width: 100%;
                padding: 24px;
                border-bottom: 1px solid var(--border-color);
            }
            .right-container {
                width: 100%;
                padding: 32px;
                border-left: none;
            }
            .info-grid {
                flex-direction: column;
            }
            .info-col:not(:last-child) {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
        }
    </style>
</head>
<body>

    <!-- Brand Header -->
    <!-- <div class="brand-header">
        eventra<span>Core</span>
    </div> -->

    <!-- Main Maintenance Card -->
    <div class="maintenance-card">
        
        <!-- Left Container: Custom SVG Graphic Illustration -->
        <div class="left-container">
            <svg width="300" height="230" viewBox="0 0 300 230" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Background decorative gears -->
                <g opacity="0.05" stroke="var(--text-main)">
                    <circle cx="60" cy="60" r="14" stroke-width="2"/>
                    <path d="M60 42v4M60 74v4M42 60h4M74 60h4M47 47l3 3M70 70l3 3M47 73l3-3M70 47l3 3" stroke-width="2" stroke-linecap="round"/>
                </g>
                <g opacity="0.05" stroke="var(--text-main)">
                    <circle cx="240" cy="90" r="18" stroke-width="2"/>
                    <path d="M240 68v4M240 108v4M218 90h4M258 90h4M224 74l3 3M253 103l3 3M224 106l3-3M253 74l3 3" stroke-width="2" stroke-linecap="round"/>
                </g>

                <!-- Floating top-right wrench badge -->
                <circle cx="215" cy="60" r="16" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5"/>
                <g stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M210 65l10-10M216 54c.3-.3.7-.6 1.4-.6.7 0 1.4.6 1.4 1.3 0 .7-.7 1-.7 1.4l-2.1 2.1M212 63c.3.3.6.7.6 1.4 0 .7-.7 1.4-1.3 1.4-.7 0-1-.7-1.4-.7l2.1-2.1"/>
                </g>

                <!-- Monitor stand and base -->
                <path d="M125 160h50l-10 20h-30l-10-20z" fill="#cbd5e1" stroke="#94a3b8" stroke-width="1.5"/>
                <rect x="105" y="180" width="90" height="6" rx="3" fill="#94a3b8"/>

                <!-- Monitor body -->
                <rect x="55" y="70" width="190" height="90" rx="10" fill="#ffffff" stroke="#2563eb" stroke-width="3"/>
                <!-- Top bar divider -->
                <line x1="55" y1="83" x2="245" y2="83" stroke="#e2e8f0" stroke-width="1.5"/>
                <!-- Three window dots -->
                <circle cx="65" cy="76" r="3" fill="#ef4444"/>
                <circle cx="73" cy="76" r="3" fill="#f59e0b"/>
                <circle cx="81" cy="76" r="3" fill="#10b981"/>

                <!-- Inside screen sad face -->
                <circle cx="150" cy="112" r="9" fill="#eff6ff" stroke="#94a3b8" stroke-width="1.5"/>
                <circle cx="147" cy="110" r="0.75" fill="#94a3b8"/>
                <circle cx="153" cy="110" r="0.75" fill="#94a3b8"/>
                <path d="M147 116q3-2 6 0" stroke="#94a3b8" stroke-width="1" stroke-linecap="round" fill="none"/>

                <!-- Text inside screen -->
                <text x="150" y="132" text-anchor="middle" fill="#64748b" font-size="8" font-weight="700">Sedang dalam perbaikan</text>
                <text x="150" y="141" text-anchor="middle" fill="#94a3b8" font-size="6">Terima kasih atas pengertiannya.</text>

                <!-- Blue gear (bottom-left of monitor) -->
                <g fill="#2563eb">
                    <circle cx="45" cy="155" r="14"/>
                    <path d="M45 137v4M45 169v4M27 155h4M59 155h4M32 142l3 3M55 165l3 3M32 168l3-3M55 142l3 3" stroke="#2563eb" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="45" cy="155" r="6" fill="var(--card-bg)"/>
                </g>

                <!-- Barricade (bottom-left) -->
                <rect x="24" y="170" width="3" height="20" fill="#64748b"/>
                <rect x="54" y="170" width="3" height="20" fill="#64748b"/>
                <rect x="18" y="173" width="42" height="6" rx="1" fill="#eff6ff" stroke="#3b82f6" stroke-width="1"/>
                <path d="M20 173l5 6M30 173l5 6M40 173l5 6M50 173l5 6" stroke="#3b82f6" stroke-width="1.2"/>
                <rect x="18" y="182" width="42" height="6" rx="1" fill="#eff6ff" stroke="#3b82f6" stroke-width="1"/>
                <path d="M20 182l5 6M30 182l5 6M40 182l5 6M50 182l5 6" stroke="#3b82f6" stroke-width="1.2"/>

                <!-- Traffic Cone (bottom-right) -->
                <rect x="210" y="188" width="26" height="3" rx="1" fill="#2563eb"/>
                <path d="M223 188l1.5-18h5l1.5 18z" fill="#2563eb"/>
                <path d="M223.3 183h6.4l.2 3h-6.8z" fill="#ffffff"/>
                <path d="M224.2 173h4.6l.2 3h-5z" fill="#ffffff"/>

                <!-- Wrench leaning on the right side -->
                <g transform="translate(245, 140) rotate(15)">
                    <rect x="-2" y="0" width="4" height="50" rx="1.5" fill="#94a3b8" stroke="#cbd5e1" stroke-width="0.75"/>
                    <circle cx="0" cy="0" r="7" fill="#94a3b8" stroke="#cbd5e1" stroke-width="0.75"/>
                    <rect x="-2" y="-7" width="4" height="5" fill="var(--card-bg)"/>
                    <circle cx="0" cy="50" r="7" fill="#94a3b8" stroke="#cbd5e1" stroke-width="0.75"/>
                    <rect x="-2" y="52" width="4" height="5" fill="var(--card-bg)"/>
                </g>
            </svg>
        </div>

        <!-- Right Container: Typography, Progress & Info Grid -->
        <div class="right-container">
            <!-- Badge -->
            <div class="badge-maintenance">
                <i data-feather="settings"></i>
                <span>Sistem Sedang Maintenance</span>
            </div>

            <!-- Title -->
            <h1 class="title">Kami sedang melakukan<br>pemeliharaan sistem</h1>
            
            <!-- Description -->
            <p class="description">
                Kami sedang melakukan pembaruan dan peningkatan sistem untuk memberikan layanan yang lebih cepat, stabil, dan aman.
            </p>
        </div>
    </div>

   

    <script>
        // Update user theme dynamically based on local storage key
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
        } else if (!currentTheme) {
            const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
            if (prefersDarkScheme.matches) {
                document.body.setAttribute('data-theme', 'dark');
            }
        }
        
        // Initialize Feather Icons
        feather.replace();

        // Auto reload after 30 seconds
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>
