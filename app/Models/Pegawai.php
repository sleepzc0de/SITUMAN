<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pegawai';

    protected $fillable = [
        'nama',
        'nama_gelar',
        'nip',
        'pangkat',
        'pendidikan',
        'email_kemenkeu',
        'email_pribadi',
        'no_hp',
        'grading',
        'jabatan',
        'jenis_jabatan',
        'nama_jabatan',
        'eselon',
        'jenis_pegawai',
        'status',
        'lokasi',
        'bagian',
        'subbagian',
        'jurusan_s1',
        'jurusan_s2',
        'jurusan_s3',
        'tmt_cpns',
        'masa_kerja_tahun',
        'masa_kerja_bulan',
        'tanggal_lahir',
        'bulan_lahir',
        'tahun_lahir',
        'usia',
        'tanggal_pensiun',
        'tahun_pensiun',
        'proyeksi_kp_1',
        'proyeksi_kp_2',
        'keterangan_kp',
        'jenis_kelamin'
    ];

    protected $casts = [
        'tmt_cpns' => 'date',
        'tanggal_lahir' => 'date',
        'tanggal_pensiun' => 'date',
    ];
}
