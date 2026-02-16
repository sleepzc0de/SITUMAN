<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atk extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'atk';

    protected $fillable = [
        'kategori_id',
        'kode_atk',
        'nama',
        'deskripsi',
        'satuan',
        'stok_minimum',
        'stok_tersedia',
        'harga_satuan',
        'status'
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriAtk::class, 'kategori_id');
    }

    public function permintaanDetail()
    {
        return $this->hasMany(PermintaanAtkDetail::class, 'atk_id');
    }

    // Auto update status based on stock
    public function updateStatus()
    {
        if ($this->stok_tersedia <= 0) {
            $this->status = 'kosong';
        } elseif ($this->stok_tersedia <= $this->stok_minimum) {
            $this->status = 'menipis';
        } else {
            $this->status = 'tersedia';
        }
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($atk) {
            if (!$atk->kode_atk) {
                $atk->kode_atk = 'ATK-' . strtoupper(uniqid());
            }
        });
    }
}
