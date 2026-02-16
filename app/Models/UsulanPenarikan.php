<?php
// app/Models/UsulanPenarikan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UsulanPenarikan extends Model
{
    use HasUuids;

    protected $table = 'usulan_penarikan';

    protected $fillable = [
        'ro', 'sub_komponen', 'bulan', 'nilai_usulan',
        'keterangan', 'status', 'user_id'
    ];

    protected $casts = [
        'nilai_usulan' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk query optimization
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }
}
