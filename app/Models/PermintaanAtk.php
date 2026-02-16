<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanAtk extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'permintaan_atk';

    protected $fillable = [
        'nomor_permintaan',
        'user_id',
        'pegawai_id',
        'tanggal_permintaan',
        'status',
        'keterangan',
        'alasan_penolakan',
        'disetujui_oleh',
        'tanggal_disetujui'
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'tanggal_disetujui' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function details()
    {
        return $this->hasMany(PermintaanAtkDetail::class, 'permintaan_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permintaan) {
            if (!$permintaan->nomor_permintaan) {
                $date = now()->format('Ymd');
                $count = static::whereDate('created_at', now())->count() + 1;
                $permintaan->nomor_permintaan = 'PATK-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
