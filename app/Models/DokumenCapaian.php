<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenCapaian extends Model
{
    protected $table = 'dokumen_capaian';

    protected $fillable = [
        'ro',
        'sub_komponen',
        'bulan',
        'nama_dokumen',
        'file_path',
        'keterangan',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
