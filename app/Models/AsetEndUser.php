<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetEndUser extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'aset_end_user';

    protected $fillable = [
        'kategori_id',
        'kode_aset',
        'nama_aset',
        'deskripsi',
        'merek',
        'tipe',
        'nomor_seri',
        'tanggal_perolehan',
        'nilai_perolehan',
        'kondisi',
        'status',
        'pegawai_id',
        'tanggal_peminjaman',
        'catatan'
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'tanggal_peminjaman' => 'date',
        'nilai_perolehan' => 'decimal:2',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriAset::class, 'kategori_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatAset::class, 'aset_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aset) {
            if (!$aset->kode_aset) {
                $aset->kode_aset = 'AST-' . strtoupper(uniqid());
            }
        });
    }
}
