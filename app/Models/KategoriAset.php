<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriAset extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kategori_aset';

    protected $fillable = ['nama', 'deskripsi'];

    public function aset()
    {
        return $this->hasMany(AsetEndUser::class, 'kategori_id');
    }
}
