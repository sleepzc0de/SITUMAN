<?php
// app/Models/SPP.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SPP extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'spp';

    protected $fillable = [
        'bulan', 'no_spp', 'nominatif', 'tgl_spp', 'jenis_kegiatan',
        'jenis_belanja', 'nomor_kontrak', 'no_bast', 'id_eperjadin',
        'uraian_spp', 'bagian', 'nama_pic', 'kode_kegiatan', 'kro',
        'ro', 'sub_komponen', 'mak', 'nomor_surat_tugas', 'tanggal_st',
        'nomor_undangan', 'bruto', 'ppn', 'pph', 'netto', 'tanggal_mulai',
        'tanggal_selesai', 'ls_bendahara', 'staff_ppk', 'no_sp2d',
        'tgl_selesai_sp2d', 'tgl_sp2d', 'status', 'coa', 'posisi_uang'
    ];

    protected $casts = [
        'tgl_spp' => 'date',
        'tanggal_st' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tgl_selesai_sp2d' => 'date',
        'tgl_sp2d' => 'date',
        'bruto' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'netto' => 'decimal:2',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($spp) {
            // Auto generate COA
            $spp->coa = $spp->kode_kegiatan . $spp->kro . $spp->ro . $spp->mak;
        });
    }

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class, 'coa', 'coa');
    }

    // Scope untuk query optimization
    public function scopeByBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSudahSP2D($query)
    {
        return $query->where('status', 'Tagihan Telah SP2D');
    }

    public function scopeBelumSP2D($query)
    {
        return $query->where('status', 'Tagihan Belum SP2D');
    }
}
