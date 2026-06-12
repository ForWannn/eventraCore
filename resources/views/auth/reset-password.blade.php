@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
@php
    $emailVal = $email ?? old('email');
    $hasCode = \Illuminate\Support\Facades\Cache::has('password_reset_code_' . $emailVal);
    $secondsLeft = 0;
    if ($hasCode && session('code_sent_at')) {
        $elapsed = time() - session('code_sent_at');
        $secondsLeft = max(0, 300 - $elapsed);
    }
@endphp
<style>
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

    .code-inputs {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .code-inputs input {
        width: 100%;
        height: 48px;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        border: 1px solid var(--input-border);
        border-radius: 12px;
        background-color: var(--input-bg);
        color: var(--text-main);
        outline: none;
        transition: border-color 0.15s;
    }

    .code-inputs input:focus {
        border-color: var(--primary);
    }

    .code-divider {
        color: var(--text-muted);
        font-weight: 500;
        padding: 0 2px;
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

    .theme-toggle svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2; }
    .sun-icon { display: none; }
    [data-theme="dark"] .sun-icon { display: block; }
    [data-theme="dark"] .moon-icon { display: none; }
</style>

<div class="container">
    <div class="header">
        <h1 class="logo">EventraCore</h1>
        <p class="subtitle">Setel ulang password Anda.</p>
    </div>

    @if (session('status'))
        <div style="background-color: #ecfdf5; color: #059669; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST" id="resetForm">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly style="border-radius: 20px; padding: 12px 16px;">
        </div>

        <div class="form-group">
            <label>Kode reset</label>
            <div class="code-inputs" id="codeInputContainer">
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <span class="code-divider">-</span>
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
            </div>
            <input type="hidden" name="code" id="hiddenCode">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                <span id="countdownTimer" style="font-size: 13px; color: var(--text-muted); font-weight: 500;"></span>
                <button type="button" id="resendBtn" style="background: none; border: none; font-size: 13px; cursor: pointer; color: var(--primary); display: none; padding: 0; font-weight: 600; text-decoration: underline;" onclick="document.getElementById('resendForm').submit();">Kirim ulang kode</button>
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password Baru</label>
            <input type="password" id="password" name="password" required style="border-radius: 20px; padding: 12px 16px;">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required style="border-radius: 20px; padding: 12px 16px;">
        </div>

        <button type="submit" class="btn">Update Password</button>
    </form>

    <form id="resendForm" action="{{ route('password.resend') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
    </form>

    <div class="footer">
        <span>Powered by ReelSeven</span>
        <button class="theme-toggle" id="themeToggle" aria-label="Ganti Tema">
            <svg class="moon-icon" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
            <svg class="sun-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        </button>
    </div>
</div>

<script>
    // Code Inputs logic
    const container = document.getElementById('codeInputContainer');
    const inputs = container.querySelectorAll('input');
    const hiddenCode = document.getElementById('hiddenCode');
    const form = document.getElementById('resetForm');

    inputs.forEach((input, index) => {
        // Auto focus next or previous
        input.addEventListener('input', (e) => {
            if (e.target.value.length > 0 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateHiddenCode();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Handle paste
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/\D/g, '').split('');
            digits.forEach((digit, i) => {
                if (index + i < inputs.length) {
                    inputs[index + i].value = digit;
                }
            });
            if (index + digits.length < inputs.length) {
                inputs[index + digits.length].focus();
            } else {
                inputs[inputs.length - 1].focus();
            }
            updateHiddenCode();
        });
    });

    function updateHiddenCode() {
        let code = '';
        inputs.forEach(input => code += input.value);
        hiddenCode.value = code;
    }

    form.addEventListener('submit', (e) => {
        updateHiddenCode();
        if (hiddenCode.value.length !== 6) {
            e.preventDefault();
            alert('Silakan masukkan keseluruhan 6 digit kode reset.');
        }
    });

    // Countdown Timer logic
    let timeLeft = {{ $secondsLeft }};
    const timerElement = document.getElementById('countdownTimer');
    const resendBtn = document.getElementById('resendBtn');

    function updateTimer() {
        if (timeLeft <= 0) {
            timerElement.innerHTML = 'Kode telah kadaluwarsa.';
            timerElement.style.color = 'var(--error)';
            resendBtn.style.display = 'inline-block';
            clearInterval(timerInterval);
        } else {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.innerHTML = `Kode kadaluwarsa dalam ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            timerElement.style.color = 'var(--text-muted)';
            resendBtn.style.display = 'none';
            timeLeft--;
        }
    }

    updateTimer();
    const timerInterval = setInterval(updateTimer, 1000);
</script>
@endsection
