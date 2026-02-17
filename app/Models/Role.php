<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasUuids;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Daftar role yang tidak boleh dihapus
     */
    public static array $protectedRoles = [
        'superadmin',
        'admin',
    ];

    /**
     * Definisi semua role yang tersedia
     */
    public static function getDefaultRoles(): array
    {
        return [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Administrator',
                'description' => 'Akses penuh ke seluruh sistem',
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Akses penuh ke seluruh sistem (kecuali pengaturan kritis)',
                'is_active' => true,
            ],
            [
                'name' => 'eksekutif',
                'display_name' => 'Eksekutif',
                'description' => 'Akses dashboard eksekutif dengan ringkasan data',
                'is_active' => true,
            ],
            [
                'name' => 'picpegawai',
                'display_name' => 'PIC Kepegawaian',
                'description' => 'Akses dashboard dan modul kepegawaian',
                'is_active' => true,
            ],
            [
                'name' => 'pickeuangan',
                'display_name' => 'PIC Keuangan/Anggaran',
                'description' => 'Akses dashboard dan modul anggaran',
                'is_active' => true,
            ],
            [
                'name' => 'picinventaris',
                'display_name' => 'PIC Inventaris',
                'description' => 'Akses dashboard dan modul inventaris',
                'is_active' => true,
            ],
            [
                'name' => 'user',
                'display_name' => 'User Biasa',
                'description' => 'Akses dashboard saja',
                'is_active' => true,
            ],
        ];
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')->withTimestamps();
    }

    public function isProtected(): bool
    {
        return in_array($this->name, self::$protectedRoles);
    }

    /**
     * Cek apakah role ini punya akses ke modul tertentu
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Grant permission ke role ini
     */
    public function givePermission(string|array $permissions): void
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->syncWithoutDetaching($permissionIds);
    }

    /**
     * Revoke permission dari role ini
     */
    public function revokePermission(string|array $permissions): void
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->detach($permissionIds);
    }
}
