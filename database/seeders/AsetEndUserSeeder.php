<?php

namespace Database\Seeders;

use App\Models\AsetEndUser;
use App\Models\KategoriAset;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AsetEndUserSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriKomputer = KategoriAset::where('nama', 'Komputer & Laptop')->first();
        $kategoriPrinter = KategoriAset::where('nama', 'Printer & Scanner')->first();
        $kategoriFurniture = KategoriAset::where('nama', 'Furniture')->first();
        $kategoriElektronik = KategoriAset::where('nama', 'Elektronik')->first();

        $asetData = [
            // Komputer & Laptop
            [
                'kategori_id' => $kategoriKomputer->id,
                'nama_aset' => 'Laptop Dell Latitude 5420',
                'deskripsi' => 'Laptop untuk keperluan kerja administratif',
                'merek' => 'Dell',
                'tipe' => 'Latitude 5420',
                'nomor_seri' => 'DL5420-001',
                'tanggal_perolehan' => '2024-01-15',
                'nilai_perolehan' => 12500000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriKomputer->id,
                'nama_aset' => 'Laptop Dell Latitude 5420',
                'deskripsi' => 'Laptop untuk keperluan kerja administratif',
                'merek' => 'Dell',
                'tipe' => 'Latitude 5420',
                'nomor_seri' => 'DL5420-002',
                'tanggal_perolehan' => '2024-01-15',
                'nilai_perolehan' => 12500000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriKomputer->id,
                'nama_aset' => 'Laptop HP ProBook 440 G9',
                'deskripsi' => 'Laptop untuk keperluan kerja',
                'merek' => 'HP',
                'tipe' => 'ProBook 440 G9',
                'nomor_seri' => 'HP440-001',
                'tanggal_perolehan' => '2024-02-01',
                'nilai_perolehan' => 11000000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriKomputer->id,
                'nama_aset' => 'PC Desktop Lenovo ThinkCentre',
                'deskripsi' => 'PC Desktop untuk workstation',
                'merek' => 'Lenovo',
                'tipe' => 'ThinkCentre M720',
                'nomor_seri' => 'LN-M720-001',
                'tanggal_perolehan' => '2023-11-10',
                'nilai_perolehan' => 8500000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriKomputer->id,
                'nama_aset' => 'Monitor LG 24 inch',
                'deskripsi' => 'Monitor tambahan untuk dual screen',
                'merek' => 'LG',
                'tipe' => '24MK430H',
                'nomor_seri' => 'LG24-001',
                'tanggal_perolehan' => '2024-01-20',
                'nilai_perolehan' => 1800000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],

            // Printer & Scanner
            [
                'kategori_id' => $kategoriPrinter->id,
                'nama_aset' => 'Printer HP LaserJet Pro M404dn',
                'deskripsi' => 'Printer laser untuk dokumen',
                'merek' => 'HP',
                'tipe' => 'LaserJet Pro M404dn',
                'nomor_seri' => 'HPL-M404-001',
                'tanggal_perolehan' => '2023-12-15',
                'nilai_perolehan' => 4500000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriPrinter->id,
                'nama_aset' => 'Printer Canon Pixma G3010',
                'deskripsi' => 'Printer inkjet multifungsi',
                'merek' => 'Canon',
                'tipe' => 'Pixma G3010',
                'nomor_seri' => 'CN-G3010-001',
                'tanggal_perolehan' => '2024-01-05',
                'nilai_perolehan' => 2800000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriPrinter->id,
                'nama_aset' => 'Scanner Canon DR-C225',
                'deskripsi' => 'Scanner dokumen otomatis',
                'merek' => 'Canon',
                'tipe' => 'DR-C225',
                'nomor_seri' => 'CN-DRC225-001',
                'tanggal_perolehan' => '2023-10-20',
                'nilai_perolehan' => 6500000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],

            // Furniture
            [
                'kategori_id' => $kategoriFurniture->id,
                'nama_aset' => 'Meja Kerja Kayu',
                'deskripsi' => 'Meja kerja standar ukuran 120x60 cm',
                'merek' => 'Olympic',
                'tipe' => 'Standard Desk',
                'nomor_seri' => 'OLY-SDK-001',
                'tanggal_perolehan' => '2023-08-15',
                'nilai_perolehan' => 1500000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriFurniture->id,
                'nama_aset' => 'Kursi Kantor Ergonomis',
                'deskripsi' => 'Kursi kantor dengan sandaran punggung ergonomis',
                'merek' => 'Chitose',
                'tipe' => 'Ergo Plus',
                'nomor_seri' => 'CHT-ERGO-001',
                'tanggal_perolehan' => '2023-08-15',
                'nilai_perolehan' => 1200000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriFurniture->id,
                'nama_aset' => 'Lemari Arsip Besi 4 Pintu',
                'deskripsi' => 'Lemari arsip besi untuk penyimpanan dokumen',
                'merek' => 'Brother',
                'tipe' => 'FC-4D',
                'nomor_seri' => 'BRO-FC4D-001',
                'tanggal_perolehan' => '2023-09-01',
                'nilai_perolehan' => 2500000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],

            // Elektronik
            [
                'kategori_id' => $kategoriElektronik->id,
                'nama_aset' => 'Proyektor Epson EB-X05',
                'deskripsi' => 'Proyektor untuk presentasi',
                'merek' => 'Epson',
                'tipe' => 'EB-X05',
                'nomor_seri' => 'EPS-EBX05-001',
                'tanggal_perolehan' => '2024-01-10',
                'nilai_perolehan' => 5500000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $kategoriElektronik->id,
                'nama_aset' => 'AC Split 1 PK Daikin',
                'deskripsi' => 'AC split untuk ruangan kerja',
                'merek' => 'Daikin',
                'tipe' => 'FTV25BXV14',
                'nomor_seri' => 'DKN-FTV25-001',
                'tanggal_perolehan' => '2023-07-20',
                'nilai_perolehan' => 4200000,
                'kondisi' => 'baik',
                'status' => 'tersedia',
            ],
        ];

        foreach ($asetData as $aset) {
            AsetEndUser::create($aset);
        }
    }
}
