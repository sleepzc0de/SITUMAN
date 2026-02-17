<?php

namespace App\Models;

use App\Services\PasswordHashService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'nama',
        'nip',
        'email',
        'email_pribadi',
        'no_hp',
        'password',
        'password_salt', // ✅ wajib ada
        'role',
    ];

    protected $hidden = [
        'password',
        'password_salt', // ✅ sembunyikan dari JSON/array
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // ✅ TIDAK ada 'password' => 'hashed'
            // karena kita handle hash manual
        ];
    }

    // =========================================================
    // PASSWORD SALT METHODS
    // =========================================================

    /**
     * Set password baru dengan generate salt otomatis
     * Simpan ke properti model (belum ke DB sampai ->save() dipanggil)
     */
    public function setPassword(string $plainPassword): void
    {
        $result = PasswordHashService::make($plainPassword);

        $this->password      = $result['hash'];
        $this->password_salt = $result['salt'];
    }

    /**
     * Verifikasi password plain terhadap hash + salt yang tersimpan di DB
     */
    public function verifyPassword(string $plainPassword): bool
    {
        if (empty($this->password) || empty($this->password_salt)) {
            return false;
        }

        return PasswordHashService::verify(
            $plainPassword,
            $this->password,
            $this->password_salt
        );
    }

    /**
     * Cek apakah password perlu di-rehash
     */
    public function passwordNeedsRehash(): bool
    {
        return PasswordHashService::needsRehash($this->password);
    }

    // =========================================================
    // ROLE HELPERS
    // =========================================================

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return in_array($this->role, explode('|', $roles));
    }

    public function canAccessModule(string $module): bool
    {
        $accessMap = [
            'superadmin'    => ['dashboard', 'kepegawaian', 'anggaran', 'inventaris', 'users', 'roles'],
            'admin'         => ['dashboard', 'kepegawaian', 'anggaran', 'inventaris', 'users', 'roles'],
            'eksekutif'     => ['dashboard'],
            'picpegawai'    => ['dashboard', 'kepegawaian'],
            'pickeuangan'   => ['dashboard', 'anggaran'],
            'picinventaris' => ['dashboard', 'inventaris'],
            'user'          => ['dashboard'],
        ];

        return in_array($module, $accessMap[$this->role] ?? ['dashboard']);
    }

    public function isAdminLevel(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'superadmin'    => 'Super Administrator',
            'admin'         => 'Administrator',
            'eksekutif'     => 'Eksekutif',
            'picpegawai'    => 'PIC Kepegawaian',
            'pickeuangan'   => 'PIC Keuangan',
            'picinventaris' => 'PIC Inventaris',
            'user'          => 'User Biasa',
            default         => 'Belum Ada Role',
        };
    }

    public function getRoleColorAttribute(): string
    {
        return match ($this->role) {
            'superadmin'    => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'admin'         => 'bg-navy-100 text-navy-800 dark:bg-navy-900/30 dark:text-navy-400',
            'eksekutif'     => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'picpegawai'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'pickeuangan'   => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'picinventaris' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            'user'          => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            default         => 'bg-gray-100 text-gray-500',
        };
    }

    public function canBeDeleted(): bool
    {
        if ($this->role === 'superadmin') {
            $protectedEmails = [
                'auliyaputraazhari@gmail.com',
                'auliyaputraazhari@kemenkeu.go.id',
            ];
            $protectedNips = ['199609102018011005'];

            return !in_array($this->email, $protectedEmails)
                && !in_array($this->nip, $protectedNips);
        }
        return true;
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')->withTimestamps();
    }

    public function usulanPenarikan()
    {
        return $this->hasMany(UsulanPenarikan::class);
    }

    public function dokumenCapaian()
    {
        return $this->hasMany(DokumenCapaian::class);
    }

    public function revisiAnggaran()
    {
        return $this->hasMany(RevisiAnggaran::class);
    }
}
