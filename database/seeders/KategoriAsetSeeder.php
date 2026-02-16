<?php

namespace Database\Seeders;

use App\Models\KategoriAset;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriAsetSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Komputer & Laptop',
                'deskripsi' => 'Perangkat komputer, laptop, dan notebook untuk keperluan kerja'
            ],
            [
                'nama' => 'Printer & Scanner',
                'deskripsi' => 'Peralatan printing, scanning, dan fotokopi'
            ],
            [
                'nama' => 'Furniture',
                'deskripsi' => 'Meja kerja, kursi, lemari, dan perabotan kantor lainnya'
            ],
            [
                'nama' => 'Elektronik',
                'deskripsi' => 'Peralatan elektronik seperti proyektor, AC, dispenser, dll'
            ],
            [
                'nama' => 'Kendaraan',
                'deskripsi' => 'Kendaraan dinas dan operasional'
            ],
        ];

        foreach ($kategoris as $kategori) {
            KategoriAset::create($kategori);
        }
    }
}
