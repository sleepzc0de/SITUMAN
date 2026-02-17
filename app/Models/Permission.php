<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasUuids;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'display_name',
        'module',
        'description',
    ];

    /**
     * Daftar semua permission yang tersedia di sistem
     */
    public static function getDefaultPermissions(): array
    {
        return [
            // Dashboard
            ['name' => 'dashboard.view',         'display_name' => 'Lihat Dashboard Umum',     'module' => 'dashboard'],
            ['name' => 'dashboard.executive',     'display_name' => 'Lihat Dashboard Eksekutif','module' => 'dashboard'],

            // Kepegawaian
            ['name' => 'kepegawaian.view',        'display_name' => 'Lihat Kepegawaian',        'module' => 'kepegawaian'],
            ['name' => 'kepegawaian.create',      'display_name' => 'Tambah Data Kepegawaian',  'module' => 'kepegawaian'],
            ['name' => 'kepegawaian.edit',        'display_name' => 'Edit Data Kepegawaian',    'module' => 'kepegawaian'],
            ['name' => 'kepegawaian.delete',      'display_name' => 'Hapus Data Kepegawaian',   'module' => 'kepegawaian'],

            // Anggaran
            ['name' => 'anggaran.view',           'display_name' => 'Lihat Anggaran',           'module' => 'anggaran'],
            ['name' => 'anggaran.create',         'display_name' => 'Tambah Data Anggaran',     'module' => 'anggaran'],
            ['name' => 'anggaran.edit',           'display_name' => 'Edit Data Anggaran',       'module' => 'anggaran'],
            ['name' => 'anggaran.delete',         'display_name' => 'Hapus Data Anggaran',      'module' => 'anggaran'],
            ['name' => 'anggaran.approve',        'display_name' => 'Approve Anggaran',         'module' => 'anggaran'],

            // Inventaris
            ['name' => 'inventaris.view',         'display_name' => 'Lihat Inventaris',         'module' => 'inventaris'],
            ['name' => 'inventaris.create',       'display_name' => 'Tambah Data Inventaris',   'module' => 'inventaris'],
            ['name' => 'inventaris.edit',         'display_name' => 'Edit Data Inventaris',     'module' => 'inventaris'],
            ['name' => 'inventaris.delete',       'display_name' => 'Hapus Data Inventaris',    'module' => 'inventaris'],
            ['name' => 'inventaris.approve',      'display_name' => 'Approve Inventaris',       'module' => 'inventaris'],

            // User Management
            ['name' => 'users.view',              'display_name' => 'Lihat User',               'module' => 'users'],
            ['name' => 'users.create',            'display_name' => 'Tambah User',              'module' => 'users'],
            ['name' => 'users.edit',              'display_name' => 'Edit User',                'module' => 'users'],
            ['name' => 'users.delete',            'display_name' => 'Hapus User',               'module' => 'users'],

            // Role Management
            ['name' => 'roles.view',              'display_name' => 'Lihat Role',               'module' => 'roles'],
            ['name' => 'roles.create',            'display_name' => 'Tambah Role',              'module' => 'roles'],
            ['name' => 'roles.edit',              'display_name' => 'Edit Role',                'module' => 'roles'],
            ['name' => 'roles.delete',            'display_name' => 'Hapus Role',               'module' => 'roles'],
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
