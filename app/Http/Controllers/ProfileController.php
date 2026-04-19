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
            'password.symbols'          => 'Password harus mengandung minimal satu simbol.',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Verifikasi password lama
        if (!$this->verifyCurrentPassword($user, $request->current_password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ])->withInput();
        }

        // Password baru tidak boleh sama dengan lama
        if ($this->isSamePassword($user, $request->password)) {
            return back()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password lama.',
            ])->withInput();
        }

        // Simpan password baru dengan salt baru
        $user->setPassword($request->password);
        $user->save();

        return back()->with('password_success', 'Password berhasil diubah.');
    }

    private function verifyCurrentPassword($user, string $plain): bool
    {
        if (!empty($user->password_salt)) {
            return PasswordHashService::verify($plain, $user->password, $user->password_salt);
        }

        // Fallback bcrypt (sistem lama)
        return password_verify($plain, $user->password);
    }

    private function isSamePassword($user, string $newPassword): bool
    {
        if (!empty($user->password_salt)) {
            return PasswordHashService::verify($newPassword, $user->password, $user->password_salt);
        }

        return password_verify($newPassword, $user->password);
    }
}
