<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleManagementController extends Controller
{
    // ✅ HAPUS __construct() - tidak perlu karena sudah ada middleware di routes/web.php
    // Route sudah dilindungi oleh: middleware('role:superadmin,admin')

    // =========================================================
    // ROLES CRUD
    // =========================================================

    public function rolesIndex()
    {
        // Double check auth (opsional, karena route middleware sudah handle)
        if (!in_array(auth()->user()?->role, ['superadmin', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $roles = Role::withCount('users')
            ->with('permissions')
            ->orderBy('created_at')
            ->get();

        $permissionsGrouped = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('roles.index', compact('roles', 'permissionsGrouped'));
    }

    public function rolesStore(Request $request)
    {
        if (!in_array(auth()->user()?->role, ['superadmin', 'admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'name'          => 'required|string|max:50|unique:roles,name',
            'display_name'  => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Pastikan name hanya huruf kecil, angka, underscore
        $validated['name'] = strtolower(preg_replace('/[^a-z0-9_]/', '', $validated['name']));

        $role = Role::create([
            'name'         => $validated['name'],
            'display_name' => $validated['display_name'],
            'description'  => $validated['description'] ?? null,
            'is_active'    => true,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->display_name}' berhasil ditambahkan!");
    }

    public function rolesUpdate(Request $request, Role $role)
    {
        if (!in_array(auth()->user()?->role, ['superadmin', 'admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'display_name'  => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'is_active'     => 'sometimes|boolean',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'display_name' => $validated['display_name'],
            'description'  => $validated['description'] ?? null,
            'is_active'    => $validated['is_active'] ?? $role->is_active,
        ]);

        // Sync permissions (kosong array jika tidak ada yang dicentang)
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->display_name}' berhasil diperbarui!");
    }

    public function rolesDestroy(Role $role)
    {
        if (!in_array(auth()->user()?->role, ['superadmin', 'admin'])) {
            abort(403);
        }

        if ($role->isProtected()) {
            return redirect()->route('roles.index')
                ->with('error', "Role '{$role->display_name}' tidak dapat dihapus (role sistem).");
        }

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return redirect()->route('roles.index')
                ->with('error', "Role '{$role->display_name}' masih digunakan oleh {$userCount} user. Pindahkan user terlebih dahulu.");
        }

        $name = $role->display_name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role '{$name}' berhasil dihapus.");
    }

    // =========================================================
    // ASSIGN ROLE TO USER
    // =========================================================

    public function assignRole(Request $request, User $user)
    {
        if (!in_array(auth()->user()?->role, ['superadmin', 'admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'role' => 'required|in:superadmin,admin,user,eksekutif,picpegawai,pickeuangan,picinventaris',
        ]);

        // Proteksi: hanya superadmin yang bisa assign/ubah role superadmin
        if (
            ($user->role === 'superadmin' || $validated['role'] === 'superadmin')
            && !auth()->user()->isSuperadmin()
        ) {
            return back()->with('error', 'Hanya Superadmin yang bisa mengatur role Superadmin.');
        }

        $oldRole  = $user->role_label;
        $user->update(['role' => $validated['role']]);
        $newRole  = $user->fresh()->role_label;

        return back()->with('success', "Role '{$user->nama}' berhasil diubah dari {$oldRole} ke {$newRole}.");
    }

    // =========================================================
    // PERMISSIONS (Read Only)
    // =========================================================

    public function permissionsIndex()
    {
        if (!in_array(auth()->user()?->role, ['superadmin', 'admin'])) {
            abort(403);
        }

        $permissions = Permission::orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        $roles = Role::with('permissions')
            ->orderBy('created_at')
            ->get();

        return view('roles.permissions', compact('permissions', 'roles'));
    }
}
