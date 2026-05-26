<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eventraCore - Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --input-bg: #ffffff;
            --input-border: #d1d5db;
            --primary: #000000;
            --primary-text: #ffffff;
            --hover-primary: #374151;
            --divider: #e5e7eb;
            --error: #b91c1c;
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
            transition: background-color 0.15s, color 0.15s;
        }

        .container {
            width: 100%;
            max-width: 360px;
            padding: 0 24px;
        }

        .header {
            margin-bottom: 32px;
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
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--input-border);
            border-radius: 20px;
            background-color: var(--input-bg);
            color: var(--text-main);
            font-size: 14px;
            transition: border-color 0.15s;
            outline: none;
        }

        input:focus {
            border-color: var(--primary);
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
            margin-top: 10px;
        }

        .btn:hover {
            background-color: var(--hover-primary);
        }

        .alert {
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            background-color: #fee2e2;
            color: var(--error);
        }

        .footer {
            margin-top: 32px;
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
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .theme-toggle svg { width: 12px; height: 12px; fill: none; stroke: currentColor; stroke-width: 2; }
        .sun-icon { display: none; }
        [data-theme="dark"] .sun-icon { display: block; }
        [data-theme="dark"] .moon-icon { display: none; }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="logo">EventraCore</h1>
            <p class="subtitle">Setel ulang password Anda.</p>
        </div>

        @if($errors->any())
            <div class="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly>
            </div>

            <div class="form-group">
                <label for="code">Kode Reset (6 Digit)</label>
                <input type="text" id="code" name="code" required autofocus placeholder="123456" maxlength="6" pattern="\d{6}" style="letter-spacing: 0.5em; text-align: center; font-weight: 600; font-size: 18px;">
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn">Update Password</button>
        </form>

        <div class="footer">
            <span>Powered by ReelSeven</span>
            <button class="theme-toggle" id="themeToggle">
                <svg class="moon-icon" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                <svg class="sun-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            </button>
        </div>
    </div>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const updateThemeUI = (theme) => {
            if (theme === 'dark') document.body.setAttribute('data-theme', 'dark');
            else document.body.removeAttribute('data-theme');
        };
        let currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        updateThemeUI(currentTheme);
        themeToggle.addEventListener('click', () => {
            const newTheme = document.body.hasAttribute('data-theme') ? 'light' : 'dark';
            updateThemeUI(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    </script>
</body>

</html>
