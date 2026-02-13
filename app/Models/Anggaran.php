<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
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

    public function spp()
    {
        return $this->hasMany(SPP::class, 'coa', 'coa');
    }

    public function revisi()
    {
        return $this->hasMany(RevisiAnggaran::class);
    }

    public function getCOAAttribute()
    {
        return $this->kegiatan . $this->kro . $this->ro . $this->kode_akun;
    }

    public function getPersentasePenyerapanAttribute()
    {
        if ($this->pagu_anggaran == 0) return 0;
        return ($this->total_penyerapan / $this->pagu_anggaran) * 100;
    }
}
