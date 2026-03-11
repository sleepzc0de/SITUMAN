<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    use HasUuids;

    protected $table = 'anggaran';

    protected $fillable = [
        'kegiatan', 'kro', 'ro', 'tahun', 'kode_subkomponen', 'kode_akun',
        'program_kegiatan', 'pic', 'pagu_anggaran', 'referensi',
        'referensi2', 'ref_output', 'len', 'januari', 'februari',
        'maret', 'april', 'mei', 'juni', 'juli', 'agustus',
        'september', 'oktober', 'november', 'desember',
        'tagihan_outstanding', 'total_penyerapan', 'sisa'
    ];

    protected $casts = [
        'pagu_anggaran'       => 'decimal:2',
        'januari'             => 'decimal:2',
        'februari'            => 'decimal:2',
        'maret'               => 'decimal:2',
        'april'               => 'decimal:2',
        'mei'                 => 'decimal:2',
        'juni'                => 'decimal:2',
        'juli'                => 'decimal:2',
        'agustus'             => 'decimal:2',
        'september'           => 'decimal:2',
        'oktober'             => 'decimal:2',
        'november'            => 'decimal:2',
        'desember'            => 'decimal:2',
        'tagihan_outstanding' => 'decimal:2',
        'total_penyerapan'    => 'decimal:2',
        'sisa'                => 'decimal:2',
    ];

    // ==================== RELASI ====================

    public function spp()
    {
        return $this->hasMany(SPP::class, 'coa', 'coa');
    }

    public function revisi()
    {
        return $this->hasMany(RevisiAnggaran::class);
    }

    public function usulanPenarikan()
    {
        return $this->hasMany(UsulanPenarikan::class);
    }

    public function dokumenCapaian()
    {
        return $this->hasMany(DokumenCapaian::class);
    }

    // ==================== ACCESSORS ====================

    public function getCOAAttribute()
    {
        return $this->kegiatan . $this->kro . $this->ro . ($this->kode_akun ?? '');
    }

    public function getPersentasePenyerapanAttribute()
    {
        if ($this->pagu_anggaran == 0) return 0;
        return round(($this->total_penyerapan / $this->pagu_anggaran) * 100, 2);
    }

    public function getPersentaseSisaAttribute()
    {
        if ($this->pagu_anggaran == 0) return 0;
        return round(($this->sisa / $this->pagu_anggaran) * 100, 2);
    }

    public function getStatusAnggaranAttribute(): string
    {
        $persen = $this->persentase_penyerapan;
        if ($persen >= 90) return 'sangat_baik';
        if ($persen >= 70) return 'baik';
        if ($persen >= 50) return 'cukup';
        return 'rendah';
    }

    // ==================== SCOPES ====================

    public function scopeByRO($query, $ro)
    {
        return $query->where('ro', $ro);
    }

    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
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

    // ==================== HELPER METHODS ====================

    /**
     * Recalculate total_penyerapan dari SPP yang sudah SP2D
     */
    public function recalculateFromSPP(): void
    {
        if (!$this->kode_akun) return;

        $coa = $this->getCOAAttribute();

        $totalSP2D = SPP::where('coa', $coa)
            ->where('status', 'Tagihan Telah SP2D')
            ->whereNull('deleted_at')
            ->sum('netto');

        $totalOutstanding = SPP::where('coa', $coa)
            ->where('status', 'Tagihan Belum SP2D')
            ->whereNull('deleted_at')
            ->sum('netto');

        // Update per bulan
        $bulanMap = [
            '01' => 'januari', '02' => 'februari', '03' => 'maret',
            '04' => 'april', '05' => 'mei', '06' => 'juni',
            '07' => 'juli', '08' => 'agustus', '09' => 'september',
            '10' => 'oktober', '11' => 'november', '12' => 'desember',
        ];

        $realisasiPerBulan = SPP::where('coa', $coa)
            ->where('status', 'Tagihan Telah SP2D')
            ->whereNull('deleted_at')
            ->selectRaw('MONTH(tgl_sp2d) as bulan_num, SUM(netto) as total')
            ->groupBy('bulan_num')
            ->pluck('total', 'bulan_num');

        $updates = [
            'total_penyerapan'    => $totalSP2D,
            'tagihan_outstanding' => $totalOutstanding,
            'sisa'                => $this->pagu_anggaran - $totalSP2D,
        ];

        foreach ($bulanMap as $num => $nama) {
            $updates[$nama] = $realisasiPerBulan[(int)$num] ?? 0;
        }

        $this->update($updates);
    }
}
