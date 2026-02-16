<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanAtkDetail extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'permintaan_atk_detail';

    protected $fillable = [
        'permintaan_id',
        'atk_id',
        'jumlah',
        'keterangan'
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanAtk::class, 'permintaan_id');
    }

    public function atk()
    {
        return $this->belongsTo(Atk::class, 'atk_id');
    }
}
