<?php

namespace App\Models;

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
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function canBeDeleted(): bool
    {
        // Superadmin dengan email atau NIP tertentu tidak bisa dihapus
        if ($this->role === 'superadmin') {
            $protectedEmails = [
                'auliyaputraazhari@gmail.com',
                'auliyaputraazhari@kemenkeu.go.id'
            ];
            $protectedNips = ['199609102018011005'];

            return !in_array($this->email, $protectedEmails) &&
                !in_array($this->nip, $protectedNips);
        }
        return true;
    }

    // Tambahkan relasi ini
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
