<?php
// app/Models/DokumenCapaian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DokumenCapaian extends Model
{
    use HasUuids;

    protected $table = 'dokumen_capaian';

    protected $fillable = [
        'ro',
        'sub_komponen',
        'bulan',
        'nama_dokumen',
        'file_path',
        'files',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'files' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to get all files
    public function getAllFiles()
    {
        $allFiles = [];

        // Add legacy single file if exists
        if ($this->file_path) {
            $allFiles[] = [
                'path' => $this->file_path,
                'name' => basename($this->file_path),
                'is_legacy' => true
            ];
        }

        // Add multiple files
        if ($this->files && is_array($this->files)) {
            foreach ($this->files as $file) {
                $allFiles[] = [
                    'path' => $file['path'] ?? $file,
                    'name' => $file['name'] ?? basename($file['path'] ?? $file),
                    'is_legacy' => false
                ];
            }
        }

        return $allFiles;
    }
}
