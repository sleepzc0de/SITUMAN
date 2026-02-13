<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsulanPenarikan extends Model
{
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
}
