<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriAtk extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kategori_atk';

    protected $fillable = ['nama', 'deskripsi'];

    public function atk()
    {
        return $this->hasMany(Atk::class, 'kategori_id');
    }
}
