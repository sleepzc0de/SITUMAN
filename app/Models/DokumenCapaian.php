<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenCapaian extends Model
{
    protected $table = 'dokumen_capaian';

    protected $fillable = [
        'ro',
        'sub_komponen',
        'bulan',
        'nama_dokumen',
        'file_path', // Legacy single file
        'files',     // Multiple files
        'keterangan',
        'user_id'
    ];

    // Cast files to array
    protected $casts = [
        'files' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to get all files (legacy + new)
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
