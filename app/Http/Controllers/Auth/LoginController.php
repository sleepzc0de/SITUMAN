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
    /** Maksimal percobaan login sebelum di-throttle */
    private const MAX_ATTEMPTS  = 5;

    /** Durasi throttle dalam detik */
    private const DECAY_SECONDS = 60;

    public function showLoginForm()
    {
        // Jika sudah login, langsung ke dashboard
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|max:255',
            'captcha'  => 'required|string|max:20',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required'  => 'Kode captcha wajib diisi.',
        ]);

        // Validasi captcha DULU sebelum cek ke DB
        $this->validateCaptcha($request);

        // Throttle key: email lowercase + IP
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Cari user — gunakan kolom email saja, password diverifikasi manual
        $user = User::where('email', $request->email)->first();

        if (!$user || !$this->verifyPassword($user, $request->password)) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            // Pesan error generik — tidak boleh membedakan "email tidak ada" vs "password salah"
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // Login berhasil — bersihkan rate limiter
        RateLimiter::clear($throttleKey);

        // Upgrade hash jika perlu (lama → baru)
        if (PasswordHashService::needsRehash($user->password)) {
            $user->setPassword($request->password);
            $user->save();
        }

        Auth::login($user, $request->boolean('remember'));

        // Regenerate session ID untuk cegah session fixation
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate dan regenerate token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Throttle key unik per email+IP.
     * Lowercase email untuk hindari bypass dengan huruf kapital.
     */
    private function throttleKey(Request $request): string
    {
        return Str::lower(trim($request->email)) . '|' . $request->ip();
    }

    /**
     * Validasi captcha dari session.
     * Session captcha di-consume (hapus) setelah dicek — sekali pakai.
     */
    private function validateCaptcha(Request $request): void
    {
        $sessionPhrase = $request->session()->pull('captcha_phrase'); // pull = get + delete
        $inputPhrase   = strtolower(trim($request->input('captcha', '')));

        if (!$sessionPhrase || strtolower(trim($sessionPhrase)) !== $inputPhrase) {
            throw ValidationException::withMessages([
                'captcha' => 'Kode captcha salah atau sudah kadaluarsa.',
            ]);
        }
    }

    /**
     * Verifikasi password dengan mendukung sistem hash lama (bcrypt) dan baru (salt).
     * Jika cocok via bcrypt lama, otomatis upgrade ke sistem baru.
     */
    private function verifyPassword(User $user, string $plain): bool
    {
        // Sistem baru: ada salt
        if (!empty($user->password_salt)) {
            return $user->verifyPassword($plain);
        }

        // Sistem lama: bcrypt tanpa salt — verifikasi lalu upgrade otomatis
        if (password_verify($plain, $user->password)) {
            $user->setPassword($plain);
            $user->save();
            return true;
        }

        return false;
    }
}
