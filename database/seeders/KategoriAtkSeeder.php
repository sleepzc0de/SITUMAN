<?php

namespace Database\Seeders;

use App\Models\KategoriAtk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriAtkSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Alat Tulis',
                'deskripsi' => 'Berbagai macam alat tulis untuk kebutuhan kantor seperti pulpen, pensil, spidol, dll.'
            ],
            [
                'nama' => 'Kertas',
                'deskripsi' => 'Berbagai jenis kertas seperti HVS, buffalo, foto, dll.'
            ],
            [
                'nama' => 'Perlengkapan Kantor',
                'deskripsi' => 'Perlengkapan kantor seperti stapler, gunting, cutter, dll.'
            ],
            [
                'nama' => 'Peralatan Komputer',
                'deskripsi' => 'Aksesoris dan perlengkapan komputer seperti flashdisk, mouse, keyboard, dll.'
            ],
            [
                'nama' => 'File & Folder',
                'deskripsi' => 'Map, ordner, binder, dan perlengkapan pengarsipan lainnya'
            ],
        ];

        foreach ($kategoris as $kategori) {
            KategoriAtk::create($kategori);
        }
    }
}
