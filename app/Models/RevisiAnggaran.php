<?php
// app/Models/RevisiAnggaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RevisiAnggaran extends Model
{
    use HasUuids;

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

    // Accessor untuk selisih
    public function getSelisihAttribute()
    {
        return $this->pagu_sesudah - $this->pagu_sebelum;
    }

    // Accessor untuk persentase perubahan
    public function getPersentasePerubahanAttribute()
    {
        if ($this->pagu_sebelum == 0) return 0;
        return (($this->pagu_sesudah - $this->pagu_sebelum) / $this->pagu_sebelum) * 100;
    }
}
