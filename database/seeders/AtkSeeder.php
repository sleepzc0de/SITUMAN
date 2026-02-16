<?php

namespace Database\Seeders;

use App\Models\Atk;
use App\Models\KategoriAtk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtkSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriAlatTulis = KategoriAtk::where('nama', 'Alat Tulis')->first();
        $kategoriKertas = KategoriAtk::where('nama', 'Kertas')->first();
        $kategoriPerlengkapan = KategoriAtk::where('nama', 'Perlengkapan Kantor')->first();
        $kategoriKomputer = KategoriAtk::where('nama', 'Peralatan Komputer')->first();
        $kategoriFile = KategoriAtk::where('nama', 'File & Folder')->first();

        $atkData = [
            // Alat Tulis
            [
                'kategori_id' => $kategoriAlatTulis->id,
                'nama' => 'Pulpen Hitam Standar',
                'deskripsi' => 'Pulpen tinta hitam untuk keperluan administrasi',
                'satuan' => 'pcs',
                'stok_minimum' => 50,
                'stok_tersedia' => 150,
                'harga_satuan' => 2500,
            ],
            [
                'kategori_id' => $kategoriAlatTulis->id,
                'nama' => 'Pulpen Biru Standar',
                'deskripsi' => 'Pulpen tinta biru untuk keperluan administrasi',
                'satuan' => 'pcs',
                'stok_minimum' => 50,
                'stok_tersedia' => 120,
                'harga_satuan' => 2500,
            ],
            [
                'kategori_id' => $kategoriAlatTulis->id,
                'nama' => 'Pensil 2B',
                'deskripsi' => 'Pensil standar 2B',
                'satuan' => 'pcs',
                'stok_minimum' => 30,
                'stok_tersedia' => 80,
                'harga_satuan' => 3000,
            ],
            [
                'kategori_id' => $kategoriAlatTulis->id,
                'nama' => 'Spidol Whiteboard',
                'deskripsi' => 'Spidol untuk papan tulis whiteboard',
                'satuan' => 'pcs',
                'stok_minimum' => 20,
                'stok_tersedia' => 45,
                'harga_satuan' => 8000,
            ],
            [
                'kategori_id' => $kategoriAlatTulis->id,
                'nama' => 'Stabilo Highlighter',
                'deskripsi' => 'Stabilo warna untuk marking dokumen',
                'satuan' => 'pcs',
                'stok_minimum' => 15,
                'stok_tersedia' => 35,
                'harga_satuan' => 12000,
            ],

            // Kertas
            [
                'kategori_id' => $kategoriKertas->id,
                'nama' => 'Kertas HVS A4 70 gram',
                'deskripsi' => 'Kertas HVS ukuran A4 70 gram untuk print',
                'satuan' => 'rim',
                'stok_minimum' => 10,
                'stok_tersedia' => 45,
                'harga_satuan' => 45000,
            ],
            [
                'kategori_id' => $kategoriKertas->id,
                'nama' => 'Kertas HVS F4 70 gram',
                'deskripsi' => 'Kertas HVS ukuran F4 70 gram untuk print',
                'satuan' => 'rim',
                'stok_minimum' => 5,
                'stok_tersedia' => 20,
                'harga_satuan' => 50000,
            ],
            [
                'kategori_id' => $kategoriKertas->id,
                'nama' => 'Kertas Buffalo',
                'deskripsi' => 'Kertas buffalo untuk sampul laporan',
                'satuan' => 'pack',
                'stok_minimum' => 5,
                'stok_tersedia' => 15,
                'harga_satuan' => 35000,
            ],

            // Perlengkapan Kantor
            [
                'kategori_id' => $kategoriPerlengkapan->id,
                'nama' => 'Stapler Besar',
                'deskripsi' => 'Stapler ukuran besar untuk dokumen tebal',
                'satuan' => 'pcs',
                'stok_minimum' => 5,
                'stok_tersedia' => 12,
                'harga_satuan' => 45000,
            ],
            [
                'kategori_id' => $kategoriPerlengkapan->id,
                'nama' => 'Isi Stapler No. 10',
                'deskripsi' => 'Isi stapler ukuran standar',
                'satuan' => 'box',
                'stok_minimum' => 10,
                'stok_tersedia' => 25,
                'harga_satuan' => 5000,
            ],
            [
                'kategori_id' => $kategoriPerlengkapan->id,
                'nama' => 'Gunting Besar',
                'deskripsi' => 'Gunting ukuran besar untuk kantor',
                'satuan' => 'pcs',
                'stok_minimum' => 5,
                'stok_tersedia' => 10,
                'harga_satuan' => 25000,
            ],
            [
                'kategori_id' => $kategoriPerlengkapan->id,
                'nama' => 'Cutter Besar',
                'deskripsi' => 'Cutter ukuran besar',
                'satuan' => 'pcs',
                'stok_minimum' => 5,
                'stok_tersedia' => 8,
                'harga_satuan' => 15000,
            ],
            [
                'kategori_id' => $kategoriPerlengkapan->id,
                'nama' => 'Penggaris 30 cm',
                'deskripsi' => 'Penggaris plastik 30 cm',
                'satuan' => 'pcs',
                'stok_minimum' => 10,
                'stok_tersedia' => 20,
                'harga_satuan' => 5000,
            ],

            // Peralatan Komputer
            [
                'kategori_id' => $kategoriKomputer->id,
                'nama' => 'Flashdisk 16GB',
                'deskripsi' => 'USB Flashdisk kapasitas 16GB',
                'satuan' => 'pcs',
                'stok_minimum' => 5,
                'stok_tersedia' => 15,
                'harga_satuan' => 75000,
            ],
            [
                'kategori_id' => $kategoriKomputer->id,
                'nama' => 'Flashdisk 32GB',
                'deskripsi' => 'USB Flashdisk kapasitas 32GB',
                'satuan' => 'pcs',
                'stok_minimum' => 3,
                'stok_tersedia' => 8,
                'harga_satuan' => 125000,
            ],
            [
                'kategori_id' => $kategoriKomputer->id,
                'nama' => 'Mouse USB',
                'deskripsi' => 'Mouse kabel USB standar',
                'satuan' => 'pcs',
                'stok_minimum' => 5,
                'stok_tersedia' => 10,
                'harga_satuan' => 45000,
            ],

            // File & Folder
            [
                'kategori_id' => $kategoriFile->id,
                'nama' => 'Map Plastik',
                'deskripsi' => 'Map plastik untuk dokumen',
                'satuan' => 'pcs',
                'stok_minimum' => 20,
                'stok_tersedia' => 50,
                'harga_satuan' => 2000,
            ],
            [
                'kategori_id' => $kategoriFile->id,
                'nama' => 'Ordner Besar',
                'deskripsi' => 'Ordner ukuran besar untuk arsip',
                'satuan' => 'pcs',
                'stok_minimum' => 10,
                'stok_tersedia' => 25,
                'harga_satuan' => 35000,
            ],
            [
                'kategori_id' => $kategoriFile->id,
                'nama' => 'Box File',
                'deskripsi' => 'Box file untuk penyimpanan dokumen',
                'satuan' => 'pcs',
                'stok_minimum' => 5,
                'stok_tersedia' => 15,
                'harga_satuan' => 25000,
            ],
        ];

        foreach ($atkData as $atk) {
            $newAtk = Atk::create($atk);
            $newAtk->updateStatus();
        }
    }
}
