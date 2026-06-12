<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\ResetPasswordCode;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $email = $request->email;
        $code = (string) random_int(100000, 999999);

        // Store code in cache for 5 minutes
        Cache::put('password_reset_code_' . $email, $code, now()->addMinutes(5));
        session(['code_sent_at' => now()->timestamp]);

        // Send email
        Mail::to($email)->send(new ResetPasswordCode($code));

        return redirect()->route('password.reset')
            ->with(['email' => $email, 'status' => 'Kode reset telah dikirim ke email Anda.']);
    }

    /**
     * Display the password reset view.
     */
    public function showResetForm(Request $request)
    {
        $email = session('email') ?? $request->email;
        
        if ($email) {
            session(['password_reset_email' => $email]);
        } else {
            $email = session('password_reset_email');
        }
        
        return view('auth.reset-password')->with(['email' => $email]);
    }

    /**
     * Handle an incoming password reset request.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
            'password' => 'required|confirmed|min:8',
        ]);

        $cachedCode = Cache::get('password_reset_code_' . $request->email);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return back()->withErrors(['code' => 'Kode reset tidak valid atau sudah kadaluwarsa.'])->withInput();
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Clear cache and session
        Cache::forget('password_reset_code_' . $request->email);
        session()->forget(['password_reset_email', 'code_sent_at']);

        return redirect()->route('login')->with('status', 'Password Anda telah berhasil diperbarui.');
    }

    /**
     * Resend the password reset code.
     */
    public function resendCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $email = $request->email;
        $code = (string) random_int(100000, 999999);

        // Store code in cache for 5 minutes
        Cache::put('password_reset_code_' . $email, $code, now()->addMinutes(5));
        session(['code_sent_at' => now()->timestamp]);

        // Send email
        Mail::to($email)->send(new ResetPasswordCode($code));

        return redirect()->route('password.reset')
            ->with(['email' => $email, 'status' => 'Kode reset baru telah dikirim ke email Anda.']);
    }
}
