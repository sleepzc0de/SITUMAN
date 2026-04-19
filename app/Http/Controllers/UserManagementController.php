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

    /**
     * Role yang boleh dibuat/diedit oleh admin (non-superadmin).
     * Superadmin dan Administrator TIDAK termasuk.
     */
    private const ADMIN_MANAGEABLE_ROLES = [
        'eksekutif', 'picpegawai', 'pickeuangan', 'picinventaris', 'user',
    ];

    // =========================================================
    // HELPERS — role list berdasarkan siapa yang login
    // =========================================================

    /**
     * Kembalikan daftar role yang boleh di-assign oleh user yang sedang login,
     * digunakan di form create & edit.
     */
    private function getAllowedRolesForForm(): array
    {
        /** @var User $auth */
        $auth = auth()->user();

        if ($auth->isSuperadmin()) {
            // Superadmin bisa assign semua role KECUALI superadmin itu sendiri
            // (superadmin tidak boleh membuat superadmin baru lewat form biasa)
            return array_filter(
                self::AVAILABLE_ROLES,
                fn($key) => $key !== 'superadmin',
                ARRAY_FILTER_USE_KEY
            );
        }

        // Admin hanya boleh assign role-role di bawahnya
        return array_intersect_key(
            self::AVAILABLE_ROLES,
            array_flip(self::ADMIN_MANAGEABLE_ROLES)
        );
    }

    /**
     * Cek apakah user yang login boleh mengelola (create/edit/delete) $targetUser.
     * Return string pesan error jika tidak boleh, null jika boleh.
     */
    private function checkManagePermission(User $targetUser): ?string
    {
        /** @var User $auth */
        $auth = auth()->user();

        // Superadmin bisa mengelola siapa saja
        if ($auth->isSuperadmin()) {
            return null;
        }

        // Admin tidak boleh menyentuh superadmin atau sesama admin
        if (in_array($targetUser->role, ['superadmin', 'admin'])) {
            return 'Anda tidak memiliki izin untuk mengelola user dengan role ' . self::AVAILABLE_ROLES[$targetUser->role] . '.';
        }

        return null;
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index(Request $request)
    {
        /** @var User $auth */
        $auth  = auth()->user();
        $query = User::query();

        // Admin hanya melihat user yang ada di bawah wewenangnya
        if (!$auth->isSuperadmin()) {
            $query->whereIn('role', self::ADMIN_MANAGEABLE_ROLES);
        }

        if ($request->filled('role')) {
            // Pastikan role filter tidak keluar dari yang boleh dilihat
            $allowedFilter = $auth->isSuperadmin()
                ? array_keys(self::AVAILABLE_ROLES)
                : self::ADMIN_MANAGEABLE_ROLES;

            if (in_array($request->role, $allowedFilter)) {
                $query->where('role', $request->role);
            }
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

        // Role list untuk dropdown filter — sesuai hak akses
        $filterRoles = $auth->isSuperadmin()
            ? self::AVAILABLE_ROLES
            : array_intersect_key(self::AVAILABLE_ROLES, array_flip(self::ADMIN_MANAGEABLE_ROLES));

        $roleCounts = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('users.index', compact('users', 'filterRoles', 'roleCounts'));
    }

    // =========================================================
    // CREATE
    // =========================================================
    public function create()
    {
        $allowedRoles = $this->getAllowedRolesForForm();
        return view('users.create', compact('allowedRoles'));
    }

    // =========================================================
    // STORE
    // =========================================================
    public function store(Request $request)
    {
        /** @var User $auth */
        $auth         = auth()->user();
        $allowedRoles = array_keys($this->getAllowedRolesForForm());

        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'nip'           => 'required|string|unique:users,nip|max:18',
            'email'         => 'required|email|unique:users,email',
            'email_pribadi' => 'nullable|email',
            'no_hp'         => 'nullable|string|max:15',
            'password'      => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'role' => ['required', Rule::in($allowedRoles)],
        ], $this->validationMessages());

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
    // SHOW
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
        // Cek izin sebelum tampilkan form
        if ($error = $this->checkManagePermission($user)) {
            return redirect()->route('users.index')->with('error', $error);
        }

        $allowedRoles = $this->getAllowedRolesForForm();

        // Jika user yang diedit adalah superadmin, role tidak bisa diubah
        $roleIsLocked = $user->role === 'superadmin';

        return view('users.edit', compact('user', 'allowedRoles', 'roleIsLocked'));
    }

    // =========================================================
    // UPDATE
    // =========================================================
    public function update(Request $request, User $user)
    {
        // Cek izin
        if ($error = $this->checkManagePermission($user)) {
            return redirect()->route('users.index')->with('error', $error);
        }

        /** @var User $auth */
        $auth = auth()->user();

        // Jika target adalah superadmin, role tidak boleh diubah sama sekali
        $roleIsLocked   = $user->role === 'superadmin';
        $allowedRoles   = $roleIsLocked
            ? ['superadmin']                          // hanya boleh tetap superadmin
            : array_keys($this->getAllowedRolesForForm());

        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'nip'           => ['required', 'string', 'max:18', Rule::unique('users')->ignore($user->id)],
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'email_pribadi' => 'nullable|email',
            'no_hp'         => 'nullable|string|max:15',
            'password'      => [
                'nullable',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'role' => ['required', Rule::in($allowedRoles)],
        ], $this->validationMessages());

        $updateData = [
            'nama'          => $validated['nama'],
            'nip'           => $validated['nip'],
            'email'         => $validated['email'],
            'email_pribadi' => $validated['email_pribadi'] ?? null,
            'no_hp'         => $validated['no_hp'] ?? null,
            // Jika locked, paksa tetap superadmin
            'role'          => $roleIsLocked ? 'superadmin' : $validated['role'],
        ];

        if ($request->filled('password')) {
            $passwordResult              = PasswordHashService::make($validated['password']);
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
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Cek izin
        if ($error = $this->checkManagePermission($user)) {
            return redirect()->route('users.index')->with('error', $error);
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
            'password.symbols'    => 'Password harus mengandung minimal satu simbol.',
            'role.required'       => 'Role wajib dipilih.',
            'role.in'             => 'Role yang dipilih tidak valid atau di luar wewenang Anda.',
        ];
    }
}
