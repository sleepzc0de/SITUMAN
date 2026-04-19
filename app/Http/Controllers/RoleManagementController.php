<?php
namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleManagementController extends Controller
{
    /**
     * Role yang HANYA boleh dikelola oleh superadmin.
     * Admin tidak boleh menyentuh role ini sama sekali.
     */
    private const SUPERADMIN_ONLY_ROLES = ['superadmin', 'admin'];

    // =========================================================
    // HELPER — pastikan hanya superadmin yg bisa akses
    // =========================================================
    private function requireSuperadmin(): void
    {
        if (!auth()->user()?->isSuperadmin()) {
            abort(403, 'Hanya Super Administrator yang dapat mengakses fitur ini.');
        }
    }

    /**
     * Cek apakah admin (non-superadmin) boleh mengedit role tertentu.
     * Admin hanya boleh edit role custom (bukan sistem).
     */
    private function checkRoleEditPermission(Role $role): void
    {
        if (auth()->user()?->isSuperadmin()) {
            return; // superadmin bebas
        }

        // Admin tidak boleh menyentuh role sistem (superadmin & admin)
        if ($role->isProtected()) {
            abort(403, 'Administrator tidak dapat mengubah role sistem yang dilindungi.');
        }

        // Admin tidak boleh menyentuh role custom yang namanya ada di list superadmin-only
        if (in_array($role->name, self::SUPERADMIN_ONLY_ROLES)) {
            abort(403, 'Akses ditolak.');
        }
    }

    // =========================================================
    // ROLES INDEX
    // =========================================================
    public function rolesIndex()
    {
        /** @var User $auth */
        $auth = auth()->user();

        if (!$auth->isAdminLevel()) {
            abort(403);
        }

        // Superadmin lihat semua role; admin hanya lihat role non-sistem
        $rolesQuery = Role::withCount('users')->with('permissions')->orderBy('created_at');

        if (!$auth->isSuperadmin()) {
            $rolesQuery->whereNotIn('name', self::SUPERADMIN_ONLY_ROLES);
        }

        $roles              = $rolesQuery->get();
        $permissionsGrouped = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        // Untuk permission matrix, superadmin lihat semua role
        $allRoles = $auth->isSuperadmin()
            ? Role::withCount('users')->with('permissions')->orderBy('created_at')->get()
            : $roles;

        return view('roles.index', compact('roles', 'permissionsGrouped', 'allRoles', 'auth'));
    }

    // =========================================================
    // ROLES STORE — hanya superadmin
    // =========================================================
    public function rolesStore(Request $request)
    {
        // Hanya superadmin yang bisa membuat role baru
        $this->requireSuperadmin();

        $validated = $request->validate([
            'name'          => [
                'required', 'string', 'max:50',
                'unique:roles,name',
                'regex:/^[a-z0-9_]+$/',
                // Tidak boleh pakai nama yang sudah direservasi
                function ($attribute, $value, $fail) {
                    $reserved = ['superadmin', 'admin', 'root', 'system', 'guest'];
                    if (in_array(strtolower($value), $reserved)) {
                        $fail('Nama role tersebut sudah direservasi oleh sistem.');
                    }
                },
            ],
            'display_name'  => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'         => strtolower($validated['name']),
            'display_name' => $validated['display_name'],
            'description'  => $validated['description'] ?? null,
            'is_active'    => true,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->display_name}' berhasil ditambahkan.");
    }

    // =========================================================
    // ROLES UPDATE
    // =========================================================
    public function rolesUpdate(Request $request, Role $role)
    {
        /** @var User $auth */
        $auth = auth()->user();

        if (!$auth->isAdminLevel()) {
            abort(403);
        }

        $this->checkRoleEditPermission($role);

        $validated = $request->validate([
            'display_name'  => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'is_active'     => 'sometimes|boolean',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Admin tidak boleh assign permission 'users.*' atau 'roles.*'
        if (!$auth->isSuperadmin() && !empty($validated['permissions'])) {
            $restrictedPermissions = Permission::whereIn('id', $validated['permissions'])
                ->whereIn('module', ['users', 'roles'])
                ->exists();

            if ($restrictedPermissions) {
                return back()->with('error', 'Administrator tidak dapat assign permission modul Users atau Roles.');
            }
        }

        $role->update([
            'display_name' => $validated['display_name'],
            'description'  => $validated['description'] ?? null,
            'is_active'    => $validated['is_active'] ?? $role->is_active,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->display_name}' berhasil diperbarui.");
    }

    // =========================================================
    // ROLES DESTROY — hanya superadmin
    // =========================================================
    public function rolesDestroy(Role $role)
    {
        // Hanya superadmin yang bisa menghapus role
        $this->requireSuperadmin();

        if ($role->isProtected()) {
            return redirect()->route('roles.index')
                ->with('error', "Role '{$role->display_name}' tidak dapat dihapus (role sistem).");
        }

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return redirect()->route('roles.index')
                ->with('error', "Role '{$role->display_name}' masih digunakan oleh {$userCount} user.");
        }

        $nama = $role->display_name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role '{$nama}' berhasil dihapus.");
    }

    // =========================================================
    // ASSIGN ROLE TO USER — hanya superadmin
    // =========================================================
    public function assignRole(Request $request, User $user)
    {
        // Assign role hanya boleh dilakukan superadmin
        // (admin melakukan ini lewat UserManagementController)
        $this->requireSuperadmin();

        $validated = $request->validate([
            'role' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $validRoles = array_keys(UserManagementController::AVAILABLE_ROLES);
                    if (!in_array($value, $validRoles)) {
                        $fail('Role tidak valid.');
                    }
                },
            ],
        ]);

        // Tidak bisa ubah diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $oldRole = $user->role_label;
        $user->update(['role' => $validated['role']]);
        $newRole = $user->fresh()->role_label;

        return back()->with('success', "Role '{$user->nama}' berhasil diubah dari {$oldRole} ke {$newRole}.");
    }

    // =========================================================
    // PERMISSIONS INDEX
    // =========================================================
    public function permissionsIndex()
    {
        /** @var User $auth */
        $auth = auth()->user();

        if (!$auth->isAdminLevel()) {
            abort(403);
        }

        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        // Admin hanya lihat role non-sistem di matrix
        $rolesQuery = Role::with('permissions')->withCount('users')->orderBy('created_at');
        if (!$auth->isSuperadmin()) {
            $rolesQuery->whereNotIn('name', self::SUPERADMIN_ONLY_ROLES);
        }
        $roles = $rolesQuery->get();

        return view('roles.permissions', compact('permissions', 'roles', 'auth'));
    }
}
