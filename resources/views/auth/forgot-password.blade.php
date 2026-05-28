@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<style>
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
        line-height: 1.5;
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

    input[type="email"] {
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

    input[type="email"]:focus {
        border-color: var(--primary);
    }

    .btn {
        width: 100%;
        padding: 14px;
        background-color: var(--primary);
        color: var(--input-bg);
        border: none;
        border-radius: 16px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.15s;
        margin-bottom: 12px;
    }

    .btn:hover {
        background-color: var(--hover-primary);
    }

    .back-link {
        display: block;
        text-align: center;
        font-size: 13px;
        color: var(--text-muted);
        text-decoration: none;
        margin-top: 16px;
        transition: color 0.15s;
    }

    .back-link:hover {
        color: var(--text-main);
    }

    .alert {
        padding: 12px;
        border-radius: 12px;
        font-size: 13px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #ecfdf5;
        color: var(--success);
    }

    .alert-error {
        background-color: #fee2e2;
        color: var(--error);
    }

    .footer {
        margin-top: 40px;
        padding-top: 24px;
        border-top: 1px solid var(--divider);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
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
    }

    .sun-icon { display: none; }
    [data-theme="dark"] .sun-icon { display: block; }
    [data-theme="dark"] .moon-icon { display: none; }
</style>

<div class="container">
    <div class="header">
        <h1 class="logo">EventraCore</h1>
        <p class="subtitle">Masukkan email Anda untuk menerima tautan reset password.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus placeholder="email" style="border-radius: 20px; padding: 12px 16px;">
        </div>

        <button type="submit" class="btn">Kirim Link Reset</button>
    </form>

    <a href="{{ route('login') }}" class="back-link">Kembali ke Login</a>

    <div class="footer">
        <span>Powered by ReelSeven</span>
        <button class="theme-toggle" id="themeToggle" aria-label="Ganti Tema">
            <svg class="moon-icon" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
            <svg class="sun-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        </button>
    </div>
</div>
@endsection
