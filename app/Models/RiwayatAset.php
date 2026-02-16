<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatAset extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'riwayat_aset';

    protected $fillable = [
        'aset_id',
        'pegawai_id',
        'user_id',
        'jenis_aktivitas',
        'tanggal',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function aset()
    {
        return $this->belongsTo(AsetEndUser::class, 'aset_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
