<?php
// database/seeders/AnggaranSeeder.php

namespace Database\Seeders;

use App\Models\Anggaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnggaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Anggaran Seeder...');

        // Data RO (Result Output)
        $roData = [
            'Z06' => 'Rencana Kebutuhan BMN dan Pengelolaannya',
            '403' => 'Layanan Pengadaan',
            '405' => 'Kerumahtanggaan',
            '994' => 'Layanan Perkantoran',
        ];

        // Data Sub Komponen untuk setiap RO
        $subkomponenData = [
            'Z06' => [
                'AA' => 'Perencanaan Kebutuhan BMN',
                'AB' => 'Pengelolaan Aset Tetap',
                'AC' => 'Inventarisasi BMN',
            ],
            '403' => [
                'BA' => 'Pengadaan Barang',
                'BB' => 'Pengadaan Jasa',
                'BC' => 'Lelang Pengadaan',
            ],
            '405' => [
                'CA' => 'Kebersihan dan Keamanan',
                'CB' => 'Pemeliharaan Gedung',
                'CC' => 'Utilitas dan Energi',
            ],
            '994' => [
                'DA' => 'Administrasi Perkantoran',
                'DB' => 'Operasional Kantor',
                'DC' => 'Komunikasi dan Teknologi',
            ],
        ];

        // Data Akun untuk setiap Sub Komponen
        $akunData = [
            // Akun untuk kategori administrasi/operasional
            'admin' => [
                '521211' => 'Belanja Bahan',
                '521213' => 'Belanja Honor Output Kegiatan',
                '522151' => 'Belanja Jasa Profesi',
                '524111' => 'Belanja Perjalanan Dinas Biasa',
                '524113' => 'Belanja Perjalanan Dinas Dalam Kota',
            ],
            // Akun untuk pengadaan
            'pengadaan' => [
                '532111' => 'Belanja Modal Peralatan dan Mesin',
                '521811' => 'Belanja Barang Operasional',
                '522141' => 'Belanja Sewa',
                '523121' => 'Belanja Pemeliharaan Gedung',
            ],
        ];

        $kegiatan = '4753';
        $kro = 'EBA';
        $pic = 'SJ.7';

        // Loop setiap RO
        foreach ($roData as $roCode => $roName) {
            $this->command->info("Processing RO: {$roCode} - {$roName}");

            // 1. Create RO Level (Parent)
            $roLevel = $this->createROLevel($kegiatan, $kro, $roCode, $roName, $pic);
            $this->command->line("  ✓ Created RO Level: {$roCode}");

            $totalPaguRO = 0;

            // 2. Create Sub Komponen untuk RO ini
            if (isset($subkomponenData[$roCode])) {
                foreach ($subkomponenData[$roCode] as $subkompCode => $subkompName) {
                    $this->command->line("  Processing Sub Komponen: {$subkompCode} - {$subkompName}");

                    $subkompLevel = $this->createSubkomponenLevel(
                        $kegiatan,
                        $kro,
                        $roCode,
                        $subkompCode,
                        $subkompName,
                        $pic
                    );
                    $this->command->line("    ✓ Created Sub Komponen: {$subkompCode}");

                    $totalPaguSubkomp = 0;

                    // 3. Create Akun untuk Sub Komponen ini
                    // Tentukan jenis akun berdasarkan RO
                    $jenisAkun = ($roCode == '403') ? 'pengadaan' : 'admin';

                    foreach ($akunData[$jenisAkun] as $akunCode => $akunName) {
                        // Generate pagu random antara 50 juta - 500 juta
                        $pagu = rand(50000000, 500000000);

                        $this->createAkunLevel(
                            $kegiatan,
                            $kro,
                            $roCode,
                            $subkompCode,
                            $akunCode,
                            $akunName,
                            $pic,
                            $pagu
                        );

                        $totalPaguSubkomp += $pagu;
                        $this->command->line("      ✓ Created Akun: {$akunCode} - Rp " . number_format($pagu, 0, ',', '.'));
                    }

                    // Update pagu Sub Komponen
                    $subkompLevel->update([
                        'pagu_anggaran' => $totalPaguSubkomp,
                        'sisa' => $totalPaguSubkomp,
                    ]);

                    $totalPaguRO += $totalPaguSubkomp;
                }
            }

            // Update pagu RO Level
            $roLevel->update([
                'pagu_anggaran' => $totalPaguRO,
                'sisa' => $totalPaguRO,
            ]);

            $this->command->info("  ✓ Completed RO: {$roCode} with total pagu: Rp " . number_format($totalPaguRO, 0, ',', '.'));
        }

        $this->command->info('Anggaran Seeder completed successfully!');
        $this->printSummary();
    }

    /**
     * Create RO Level (Parent tertinggi)
     */
    private function createROLevel($kegiatan, $kro, $ro, $programKegiatan, $pic)
    {
        $baseRef = $kegiatan . $kro . $ro;

        return Anggaran::create([
            'id' => Str::uuid(),
            'kegiatan' => $kegiatan,
            'kro' => $kro,
            'ro' => $ro,
            'kode_subkomponen' => null,
            'kode_akun' => null,
            'program_kegiatan' => $programKegiatan,
            'pic' => $pic,
            'pagu_anggaran' => 0, // Will be updated after children
            'referensi' => $baseRef,
            'referensi2' => $baseRef,
            'ref_output' => $baseRef,
            'len' => strlen($baseRef),
            'sisa' => 0,
            'total_penyerapan' => 0,
            'tagihan_outstanding' => 0,
        ]);
    }

    /**
     * Create Sub Komponen Level
     */
    private function createSubkomponenLevel($kegiatan, $kro, $ro, $subkomponen, $programKegiatan, $pic)
    {
        $baseRef = $kegiatan . $kro . $ro;
        $fullRef = $baseRef . $subkomponen;

        return Anggaran::create([
            'id' => Str::uuid(),
            'kegiatan' => $kegiatan,
            'kro' => $kro,
            'ro' => $ro,
            'kode_subkomponen' => $subkomponen,
            'kode_akun' => null,
            'program_kegiatan' => $programKegiatan,
            'pic' => $pic,
            'pagu_anggaran' => 0, // Will be updated after children
            'referensi' => $fullRef,
            'referensi2' => $fullRef,
            'ref_output' => $baseRef,
            'len' => strlen($fullRef),
            'sisa' => 0,
            'total_penyerapan' => 0,
            'tagihan_outstanding' => 0,
        ]);
    }

    /**
     * Create Akun Level (Detail)
     */
    private function createAkunLevel($kegiatan, $kro, $ro, $subkomponen, $akun, $programKegiatan, $pic, $pagu)
    {
        $baseRef = $kegiatan . $kro . $ro;
        $fullRef = $baseRef . $subkomponen . $akun;

        return Anggaran::create([
            'id' => Str::uuid(),
            'kegiatan' => $kegiatan,
            'kro' => $kro,
            'ro' => $ro,
            'kode_subkomponen' => $subkomponen,
            'kode_akun' => $akun,
            'program_kegiatan' => $programKegiatan,
            'pic' => $pic,
            'pagu_anggaran' => $pagu,
            'referensi' => $fullRef,
            'referensi2' => $baseRef . $subkomponen,
            'ref_output' => $baseRef,
            'len' => strlen($fullRef),
            'sisa' => $pagu,
            'total_penyerapan' => 0,
            'tagihan_outstanding' => 0,
        ]);
    }

    /**
     * Print summary statistics
     */
    private function printSummary()
    {
        $totalRO = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun')->count();
        $totalSubkomp = Anggaran::whereNotNull('kode_subkomponen')->whereNull('kode_akun')->count();
        $totalAkun = Anggaran::whereNotNull('kode_akun')->count();
        $totalPagu = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun')->sum('pagu_anggaran');

        $this->command->info('');
        $this->command->info('=== Summary ===');
        $this->command->info("Total RO Level: {$totalRO}");
        $this->command->info("Total Sub Komponen: {$totalSubkomp}");
        $this->command->info("Total Akun: {$totalAkun}");
        $this->command->info("Total Records: " . ($totalRO + $totalSubkomp + $totalAkun));
        $this->command->info("Total Pagu: Rp " . number_format($totalPagu, 0, ',', '.'));
        $this->command->info('');
    }
}
