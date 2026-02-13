<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah');
    }
}
