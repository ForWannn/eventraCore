<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eventraCore - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #ffffff;
            Login Page --text-main: #111827;
            --text-muted: #6b7280;
            --input-bg: #ffffff;
            --input-border: #d1d5db;
            --primary: #000000;
            --primary-text: #ffffff;
            --hover-primary: #374151;
            --divider: #e5e7eb;
        }

        [data-theme="dark"] {
            --bg-color: #111827;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --input-bg: #1f2937;
            --input-border: #374151;
            --primary: #ffffff;
            --primary-text: #000000;
            --hover-primary: #e5e7eb;
            --divider: #374151;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            /* Transisi super cepat untuk kesan ringan */
            transition: background-color 0.15s, color 0.15s;
        }

        .container {
            width: 100%;
            max-width: 360px;
            padding: 0 24px;
        }

        .header {
            margin-bottom: 40px;
        }

        .logo {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--input-border);
            border-radius: 20px;
            background-color: var(--input-bg);
            color: var(--text-main);
            font-size: 14px;
            transition: border-color 0.15s;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 32px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-wrapper input {
            cursor: pointer;
        }

        a.link {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.15s;
        }

        a.link:hover {
            color: var(--text-main);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: var(--primary-text);
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.15s;
            margin-bottom: 12px;
        }

        .btn:hover {
            background-color: var(--hover-primary);
        }

        .footer {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid var(--divider);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--text-muted);
        }

        .theme-toggle {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 0;
            transition: color 0.15s;
        }

        .theme-toggle:hover {
            color: var(--text-main);
        }

        .theme-toggle svg {
            width: 14px;
            height: 14px;
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
    <div class="container">
        <div class="header">
            <h1 class="logo">EventraCore</h1>
            <p class="subtitle">Event Management System</p>
        </div>

        <form action="" method="POST">
            @csrf

            @if (session('status'))
                <div
                    style="background-color: #ecfdf5; color: #059669; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div
                    style="background-color: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="form-group">
                <label for="username">Email</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <div class="form-options">
                <label class="checkbox-wrapper">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Remember Me</span>
                </label>
                <a href="{{ route('password.request') }}" class="link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn">Masuk</button>
        </form>

        <div class="footer">
            <span>Powered by ReelSeven</span>

            <button class="theme-toggle" id="themeToggle" aria-label="Ganti Tema">
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
                <span id="themeText">Tema Gelap</span>
            </button>
        </div>
    </div>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeText = document.getElementById('themeText');

        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

        const updateThemeUI = (theme) => {
            if (theme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                themeText.textContent = '';
            } else {
                document.body.removeAttribute('data-theme');
                themeText.textContent = '';
            }
        };

        let currentTheme = localStorage.getItem('theme');
        if (!currentTheme) {
            currentTheme = prefersDarkScheme.matches ? 'dark' : 'light';
        }
        updateThemeUI(currentTheme);

        themeToggle.addEventListener('click', () => {
            const isDark = document.body.hasAttribute('data-theme');
            const newTheme = isDark ? 'light' : 'dark';

            updateThemeUI(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    </script>
</body>

</html>