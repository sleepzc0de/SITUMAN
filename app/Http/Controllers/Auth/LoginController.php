<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordHashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'captcha'  => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required'  => 'Kode captcha wajib diisi.',
        ]);

        // ✅ Validasi Captcha
        $this->validateCaptcha($request);

        // Rate Limiting
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Verifikasi password
        if (!$user || !$this->verifyPassword($user, $request->password)) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // Login berhasil
        RateLimiter::clear($throttleKey);

        if (PasswordHashService::needsRehash($user->password)) {
            $user->setPassword($request->password);
            $user->save();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // =========================================================
    // PRIVATE: Validasi Captcha
    // =========================================================
    private function validateCaptcha(Request $request): void
    {
        $sessionPhrase = $request->session()->get('captcha_phrase');
        $inputPhrase   = strtolower(trim($request->input('captcha')));


        $request->session()->forget('captcha_phrase');

        if (!$sessionPhrase || strtolower($sessionPhrase) !== $inputPhrase) {
            throw ValidationException::withMessages([
                'captcha' => 'Kode captcha salah atau sudah kadaluarsa. Silakan refresh captcha.',
            ]);
        }
    }

    // =========================================================
    // PRIVATE: Verifikasi password
    // =========================================================
    private function verifyPassword(User $user, string $plainPassword): bool
    {
        if (!empty($user->password_salt)) {
            return $user->verifyPassword($plainPassword);
        }

        if (password_verify($plainPassword, $user->password)) {
            $user->setPassword($plainPassword);
            $user->save();
            return true;
        }

        return false;
    }
}
