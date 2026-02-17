<?php

namespace App\Http\Controllers;

use App\Services\PasswordHashService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
            'password.min'              => 'Password minimal 8 karakter.',
            'password.mixed_case'       => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers'          => 'Password harus mengandung minimal satu angka.',
            'password.symbols'          => 'Password harus mengandung minimal satu simbol (!, @, #, dll).',
        ]);

        $user = $request->user();

        // =====================================================
        // Verifikasi password lama
        // Mendukung dua kondisi:
        // 1. Sistem baru  → verifikasi dengan salt
        // 2. Sistem lama  → verifikasi bcrypt biasa
        // =====================================================
        if (!$this->verifyCurrentPassword($user, $request->current_password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        // =====================================================
        // Pastikan password baru tidak sama dengan password lama
        // =====================================================
        if ($this->isSamePassword($user, $request->password)) {
            return back()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password lama.',
            ]);
        }

        // =====================================================
        // Update password dengan salt baru
        // =====================================================
        $user->setPassword($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Verifikasi password lama dengan mendukung sistem lama dan baru
     */
    private function verifyCurrentPassword($user, string $plainPassword): bool
    {
        // Sistem baru: punya salt
        if (!empty($user->password_salt)) {
            return PasswordHashService::verify(
                $plainPassword,
                $user->password,
                $user->password_salt
            );
        }

        // Sistem lama: bcrypt biasa (tanpa salt)
        // Jika cocok, upgrade otomatis ke sistem baru
        if (password_verify($plainPassword, $user->password)) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah password baru sama dengan password lama
     */
    private function isSamePassword($user, string $newPassword): bool
    {
        if (!empty($user->password_salt)) {
            return PasswordHashService::verify(
                $newPassword,
                $user->password,
                $user->password_salt
            );
        }

        return password_verify($newPassword, $user->password);
    }
}
