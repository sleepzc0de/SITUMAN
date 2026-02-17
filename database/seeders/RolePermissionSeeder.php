<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat semua permission
        $permissionsData = Permission::getDefaultPermissions();
        foreach ($permissionsData as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                array_merge($perm, ['id' => (string) Str::uuid()])
            );
        }

        // Buat semua role
        $rolesData = Role::getDefaultRoles();
        foreach ($rolesData as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                array_merge($roleData, ['id' => (string) Str::uuid()])
            );
        }

        // =========================================================
        // Assign permissions ke setiap role
        // =========================================================

        // SUPERADMIN - semua permission
        $superadmin = Role::where('name', 'superadmin')->first();
        $superadmin->permissions()->sync(Permission::all()->pluck('id'));

        // ADMIN - semua permission
        $admin = Role::where('name', 'admin')->first();
        $admin->permissions()->sync(Permission::all()->pluck('id'));

        // EKSEKUTIF - hanya dashboard
        $eksekutif = Role::where('name', 'eksekutif')->first();
        $eksekutif->permissions()->sync(
            Permission::whereIn('name', [
                'dashboard.view',
                'dashboard.executive',
            ])->pluck('id')
        );

        // PIC PEGAWAI - dashboard + kepegawaian
        $picPegawai = Role::where('name', 'picpegawai')->first();
        $picPegawai->permissions()->sync(
            Permission::whereIn('name', [
                'dashboard.view',
                'kepegawaian.view',
                'kepegawaian.create',
                'kepegawaian.edit',
            ])->pluck('id')
        );

        // PIC KEUANGAN - dashboard + anggaran
        $picKeuangan = Role::where('name', 'pickeuangan')->first();
        $picKeuangan->permissions()->sync(
            Permission::whereIn('name', [
                'dashboard.view',
                'anggaran.view',
                'anggaran.create',
                'anggaran.edit',
            ])->pluck('id')
        );

        // PIC INVENTARIS - dashboard + inventaris
        $picInventaris = Role::where('name', 'picinventaris')->first();
        $picInventaris->permissions()->sync(
            Permission::whereIn('name', [
                'dashboard.view',
                'inventaris.view',
                'inventaris.create',
                'inventaris.edit',
            ])->pluck('id')
        );

        // USER BIASA - hanya dashboard
        $user = Role::where('name', 'user')->first();
        $user->permissions()->sync(
            Permission::whereIn('name', [
                'dashboard.view',
            ])->pluck('id')
        );

        $this->command->info('✅ Roles dan Permissions berhasil dibuat!');
    }
}
