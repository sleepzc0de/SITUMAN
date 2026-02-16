<?php
// app/Models/Anggaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    use HasUuids;

    protected $table = 'anggaran';

    protected $fillable = [
        'kegiatan', 'kro', 'ro', 'kode_subkomponen', 'kode_akun',
        'program_kegiatan', 'pic', 'pagu_anggaran', 'referensi',
        'referensi2', 'ref_output', 'len', 'januari', 'februari',
        'maret', 'april', 'mei', 'juni', 'juli', 'agustus',
        'september', 'oktober', 'november', 'desember',
        'tagihan_outstanding', 'total_penyerapan', 'sisa'
    ];

    protected $casts = [
        'pagu_anggaran' => 'decimal:2',
        'januari' => 'decimal:2',
        'februari' => 'decimal:2',
        'maret' => 'decimal:2',
        'april' => 'decimal:2',
        'mei' => 'decimal:2',
        'juni' => 'decimal:2',
        'juli' => 'decimal:2',
        'agustus' => 'decimal:2',
        'september' => 'decimal:2',
        'oktober' => 'decimal:2',
        'november' => 'decimal:2',
        'desember' => 'decimal:2',
        'tagihan_outstanding' => 'decimal:2',
        'total_penyerapan' => 'decimal:2',
        'sisa' => 'decimal:2',
    ];

    // Relasi
    public function spp()
    {
        return $this->hasMany(SPP::class, 'coa', 'coa');
    }

    public function revisi()
    {
        return $this->hasMany(RevisiAnggaran::class);
    }

    // Accessor untuk COA
    public function getCOAAttribute()
    {
        return $this->kegiatan . $this->kro . $this->ro . ($this->kode_akun ?? '');
    }

    // Accessor untuk persentase penyerapan
    public function getPersentasePenyerapanAttribute()
    {
        if ($this->pagu_anggaran == 0) return 0;
        return ($this->total_penyerapan / $this->pagu_anggaran) * 100;
    }

    // Scope untuk query optimization
    public function scopeByRO($query, $ro)
    {
        return $query->where('ro', $ro);
    }

    public function scopeBySubkomponen($query, $subkomponen)
    {
        return $query->where('kode_subkomponen', $subkomponen);
    }

    public function scopeParentRO($query)
    {
        return $query->whereNull('kode_subkomponen')->whereNull('kode_akun');
    }

    public function scopeSubkomponen($query)
    {
        return $query->whereNotNull('kode_subkomponen')->whereNull('kode_akun');
    }

    public function scopeAkun($query)
    {
        return $query->whereNotNull('kode_akun');
    }
}
