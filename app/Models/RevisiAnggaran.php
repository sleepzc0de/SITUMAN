<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevisiAnggaran extends Model
{
    protected $table = 'revisi_anggaran';

    protected $fillable = [
        'anggaran_id', 'jenis_revisi', 'pagu_sebelum', 'pagu_sesudah',
        'alasan_revisi', 'tanggal_revisi', 'dokumen_pendukung', 'user_id'
    ];

    protected $casts = [
        'pagu_sebelum' => 'decimal:2',
        'pagu_sesudah' => 'decimal:2',
        'tanggal_revisi' => 'date',
    ];

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
