<?php

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
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // =====================================================
        // Rate Limiting: max 5 percobaan per menit per IP+email
        // =====================================================
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // =====================================================
        // Cari user berdasarkan email
        // =====================================================
        $user = User::where('email', $request->email)->first();

        // =====================================================
        // Verifikasi password dengan salt
        // =====================================================
        if (!$user || !$this->verifyPassword($user, $request->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // =====================================================
        // Login berhasil
        // =====================================================
        RateLimiter::clear($throttleKey);

        // Rehash otomatis jika cost factor berubah
        if (PasswordHashService::needsRehash($user->password)) {
            $user->setPassword($request->password);
            $user->save();
        }

        // Login manual karena kita tidak pakai Auth::attempt()
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
    // PRIVATE: Verifikasi password dengan fallback
    // =========================================================

    /**
     * Verifikasi password dengan mempertimbangkan dua kondisi:
     *
     * 1. User BARU  → punya password_salt → verifikasi dengan salt
     * 2. User LAMA  → tidak punya salt   → verifikasi bcrypt biasa,
     *                                       lalu upgrade otomatis ke sistem salt
     */
    private function verifyPassword(User $user, string $plainPassword): bool
    {
        // Kondisi 1: User sudah pakai sistem salt (baru)
        if (!empty($user->password_salt)) {
            return $user->verifyPassword($plainPassword);
        }

        // Kondisi 2: User lama pakai bcrypt biasa (tanpa salt)
        if (password_verify($plainPassword, $user->password)) {
            // ✅ Upgrade otomatis ke sistem salt baru
            $user->setPassword($plainPassword);
            $user->save();

            return true;
        }

        return false;
    }
}
