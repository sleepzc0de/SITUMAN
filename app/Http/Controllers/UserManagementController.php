<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PasswordHashService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public const AVAILABLE_ROLES = [
        'superadmin'    => 'Super Administrator',
        'admin'         => 'Administrator',
        'eksekutif'     => 'Eksekutif',
        'picpegawai'    => 'PIC Kepegawaian',
        'pickeuangan'   => 'PIC Keuangan',
        'picinventaris' => 'PIC Inventaris',
        'user'          => 'User Biasa',
    ];

    // =========================================================
    // INDEX
    // =========================================================

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $availableRoles = self::AVAILABLE_ROLES;

        $roleCounts = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('users.index', compact('users', 'availableRoles', 'roleCounts'));
    }

    // =========================================================
    // CREATE
    // =========================================================

    public function create()
    {
        $availableRoles = self::AVAILABLE_ROLES;
        return view('users.create', compact('availableRoles'));
    }

    // =========================================================
    // STORE
    // =========================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'nip'           => 'required|string|unique:users,nip|max:18',
            'email'         => 'required|email|unique:users,email',
            'email_pribadi' => 'nullable|email',
            'no_hp'         => 'nullable|string|max:15',
            'password'      => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => ['required', Rule::in(array_keys(self::AVAILABLE_ROLES))],
        ], $this->validationMessages());

        // Generate salt & hash password
        $passwordResult = PasswordHashService::make($validated['password']);

        $user = User::create([
            'nama'          => $validated['nama'],
            'nip'           => $validated['nip'],
            'email'         => $validated['email'],
            'email_pribadi' => $validated['email_pribadi'] ?? null,
            'no_hp'         => $validated['no_hp'] ?? null,
            'password'      => $passwordResult['hash'],
            'password_salt' => $passwordResult['salt'],
            'role'          => $validated['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->nama}' berhasil ditambahkan.");
    }

    // =========================================================
    // SHOW (opsional, redirect ke edit)
    // =========================================================

    public function show(User $user)
    {
        return redirect()->route('users.edit', $user);
    }

    // =========================================================
    // EDIT
    // =========================================================

    public function edit(User $user)
    {
        $availableRoles = self::AVAILABLE_ROLES;
        return view('users.edit', compact('user', 'availableRoles'));
    }

    // =========================================================
    // UPDATE
    // =========================================================

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'nip'           => ['required', 'string', 'max:18', Rule::unique('users')->ignore($user->id)],
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'email_pribadi' => 'nullable|email',
            'no_hp'         => 'nullable|string|max:15',
            'password'      => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => ['required', Rule::in(array_keys(self::AVAILABLE_ROLES))],
        ], $this->validationMessages());

        // Proteksi: non-superadmin tidak bisa ubah/downgrade akun superadmin
        if ($user->role === 'superadmin' && !auth()->user()->isSuperadmin()) {
            return back()->with('error', 'Hanya Superadmin yang bisa mengubah akun Superadmin.');
        }

        // Proteksi: non-superadmin tidak bisa assign role superadmin
        if ($validated['role'] === 'superadmin' && !auth()->user()->isSuperadmin()) {
            return back()->with('error', 'Hanya Superadmin yang bisa menetapkan role Superadmin.');
        }

        $updateData = [
            'nama'          => $validated['nama'],
            'nip'           => $validated['nip'],
            'email'         => $validated['email'],
            'email_pribadi' => $validated['email_pribadi'] ?? null,
            'no_hp'         => $validated['no_hp'] ?? null,
            'role'          => $validated['role'],
        ];

        // Update password + salt baru hanya jika field password diisi
        if ($request->filled('password')) {
            $passwordResult          = PasswordHashService::make($validated['password']);
            $updateData['password']      = $passwordResult['hash'];
            $updateData['password_salt'] = $passwordResult['salt'];
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->nama}' berhasil diperbarui.");
    }

    // =========================================================
    // DESTROY
    // =========================================================

    public function destroy(User $user)
    {
        // Tidak bisa hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if (!$user->canBeDeleted()) {
            return redirect()->route('users.index')
                ->with('error', 'User ini tidak dapat dihapus (Protected Account).');
        }

        $nama = $user->nama;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User '{$nama}' berhasil dihapus.");
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function validationMessages(): array
    {
        return [
            'nama.required'       => 'Nama wajib diisi.',
            'nip.required'        => 'NIP wajib diisi.',
            'nip.unique'          => 'NIP sudah digunakan oleh user lain.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah digunakan oleh user lain.',
            'password.required'   => 'Password wajib diisi.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'password.min'        => 'Password minimal 8 karakter.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers'    => 'Password harus mengandung minimal satu angka.',
            'password.symbols'    => 'Password harus mengandung minimal satu simbol (!, @, #, dll).',
            'role.required'       => 'Role wajib dipilih.',
            'role.in'             => 'Role yang dipilih tidak valid.',
        ];
    }
}
