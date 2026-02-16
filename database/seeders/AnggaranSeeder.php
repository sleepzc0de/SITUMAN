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

        // Data lengkap dengan struktur hierarki dan pagu presisi
        $dataAnggaran = [
            'Z06' => [
                'name' => 'Rencana Kebutuhan BMN dan Pengelolaannya',
                'subkomponen' => [
                    'AA' => [
                        'name' => 'Perencanaan Kebutuhan BMN',
                        'akun' => [
                            '521211' => ['name' => 'Belanja Bahan', 'pagu' => 125750000.00],
                            '521213' => ['name' => 'Belanja Honor Output Kegiatan', 'pagu' => 245500000.00],
                            '522151' => ['name' => 'Belanja Jasa Profesi', 'pagu' => 89250000.00],
                            '524111' => ['name' => 'Belanja Perjalanan Dinas Biasa', 'pagu' => 156800000.00],
                            '524113' => ['name' => 'Belanja Perjalanan Dinas Dalam Kota', 'pagu' => 45600000.00],
                        ]
                    ],
                    'AB' => [
                        'name' => 'Pengelolaan Aset Tetap',
                        'akun' => [
                            '521211' => ['name' => 'Belanja Bahan', 'pagu' => 178900000.00],
                            '522151' => ['name' => 'Belanja Jasa Profesi', 'pagu' => 234600000.00],
                            '523121' => ['name' => 'Belanja Pemeliharaan Gedung', 'pagu' => 456780000.00],
                            '524111' => ['name' => 'Belanja Perjalanan Dinas Biasa', 'pagu' => 98450000.00],
                        ]
                    ],
                    'AC' => [
                        'name' => 'Inventarisasi BMN',
                        'akun' => [
                            '521211' => ['name' => 'Belanja Bahan', 'pagu' => 87650000.00],
                            '521213' => ['name' => 'Belanja Honor Output Kegiatan', 'pagu' => 198700000.00],
                            '524111' => ['name' => 'Belanja Perjalanan Dinas Biasa', 'pagu' => 134900000.00],
                            '524113' => ['name' => 'Belanja Perjalanan Dinas Dalam Kota', 'pagu' => 67800000.00],
                        ]
                    ],
                ]
            ],
            '403' => [
                'name' => 'Layanan Pengadaan',
                'subkomponen' => [
                    'BA' => [
                        'name' => 'Pengadaan Barang',
                        'akun' => [
                            '532111' => ['name' => 'Belanja Modal Peralatan dan Mesin', 'pagu' => 1250000000.00],
                            '521811' => ['name' => 'Belanja Barang Operasional', 'pagu' => 567890000.00],
                            '522141' => ['name' => 'Belanja Sewa', 'pagu' => 345670000.00],
                            '524111' => ['name' => 'Belanja Perjalanan Dinas Biasa', 'pagu' => 123450000.00],
                        ]
                    ],
                    'BB' => [
                        'name' => 'Pengadaan Jasa',
                        'akun' => [
                            '522151' => ['name' => 'Belanja Jasa Profesi', 'pagu' => 876540000.00],
                            '522141' => ['name' => 'Belanja Sewa', 'pagu' => 456780000.00],
                            '521811' => ['name' => 'Belanja Barang Operasional', 'pagu' => 234560000.00],
                            '524111' => ['name' => 'Belanja Perjalanan Dinas Biasa', 'pagu' => 98760000.00],
                        ]
                    ],
                    'BC' => [
                        'name' => 'Lelang Pengadaan',
                        'akun' => [
                            '521213' => ['name' => 'Belanja Honor Output Kegiatan', 'pagu' => 345670000.00],
                            '522151' => ['name' => 'Belanja Jasa Profesi', 'pagu' => 567890000.00],
                            '524111' => ['name' => 'Belanja Perjalanan Dinas Biasa', 'pagu' => 156780000.00],
                        ]
                    ],
                ]
            ],
            '405' => [
                'name' => 'Kerumahtanggaan',
                'subkomponen' => [
                    'CA' => [
                        'name' => 'Kebersihan dan Keamanan',
                        'akun' => [
                            '521811' => ['name' => 'Belanja Barang Operasional', 'pagu' => 456780000.00],
                            '522141' => ['name' => 'Belanja Sewa', 'pagu' => 234560000.00],
                            '522151' => ['name' => 'Belanja Jasa Profesi', 'pagu' => 678900000.00],
                            '523121' => ['name' => 'Belanja Pemeliharaan Gedung', 'pagu' => 345670000.00],
                        ]
                    ],
                    'CB' => [
                        'name' => 'Pemeliharaan Gedung',
                        'akun' => [
                            '523121' => ['name' => 'Belanja Pemeliharaan Gedung', 'pagu' => 1567890000.00],
                            '521811' => ['name' => 'Belanja Barang Operasional', 'pagu' => 345670000.00],
                            '522141' => ['name' => 'Belanja Sewa', 'pagu' => 234560000.00],
                        ]
                    ],
                    'CC' => [
                        'name' => 'Utilitas dan Energi',
                        'akun' => [
                            '521811' => ['name' => 'Belanja Barang Operasional', 'pagu' => 876540000.00],
                            '523121' => ['name' => 'Belanja Pemeliharaan Gedung', 'pagu' => 456780000.00],
                        ]
                    ],
                ]
            ],
            '994' => [
                'name' => 'Layanan Perkantoran',
                'subkomponen' => [
                    'DA' => [
                        'name' => 'Administrasi Perkantoran',
                        'akun' => [
                            '521211' => ['name' => 'Belanja Bahan', 'pagu' => 234560000.00],
                            '521213' => ['name' => 'Belanja Honor Output Kegiatan', 'pagu' => 456780000.00],
                            '524111' => ['name' => 'Belanja Perjalanan Dinas Biasa', 'pagu' => 178900000.00],
                            '524113' => ['name' => 'Belanja Perjalanan Dinas Dalam Kota', 'pagu' => 89450000.00],
                        ]
                    ],
                    'DB' => [
                        'name' => 'Operasional Kantor',
                        'akun' => [
                            '521211' => ['name' => 'Belanja Bahan', 'pagu' => 345670000.00],
                            '521811' => ['name' => 'Belanja Barang Operasional', 'pagu' => 567890000.00],
                            '522141' => ['name' => 'Belanja Sewa', 'pagu' => 234560000.00],
                            '524111' => ['name' => 'Belanja Perjalanan Dinas Biasa', 'pagu' => 123450000.00],
                        ]
                    ],
                    'DC' => [
                        'name' => 'Komunikasi dan Teknologi',
                        'akun' => [
                            '532111' => ['name' => 'Belanja Modal Peralatan dan Mesin', 'pagu' => 876540000.00],
                            '521811' => ['name' => 'Belanja Barang Operasional', 'pagu' => 456780000.00],
                            '522141' => ['name' => 'Belanja Sewa', 'pagu' => 234560000.00],
                        ]
                    ],
                ]
            ],
        ];

        $kegiatan = '4753';
        $kro = 'EBA';
        $pic = 'SJ.7';

        // Loop setiap RO
        foreach ($dataAnggaran as $roCode => $roData) {
            $this->command->info("Processing RO: {$roCode} - {$roData['name']}");

            // 1. Create RO Level (Parent)
            $roLevel = $this->createROLevel($kegiatan, $kro, $roCode, $roData['name'], $pic);
            $this->command->line("  ✓ Created RO Level: {$roCode}");

            $totalPaguRO = 0;

            // 2. Loop Sub Komponen
            foreach ($roData['subkomponen'] as $subkompCode => $subkompData) {
                $this->command->line("  Processing Sub Komponen: {$subkompCode} - {$subkompData['name']}");

                $subkompLevel = $this->createSubkomponenLevel(
                    $kegiatan,
                    $kro,
                    $roCode,
                    $subkompCode,
                    $subkompData['name'],
                    $pic
                );
                $this->command->line("    ✓ Created Sub Komponen: {$subkompCode}");

                $totalPaguSubkomp = 0;

                // 3. Loop Akun
                foreach ($subkompData['akun'] as $akunCode => $akunData) {
                    $this->createAkunLevel(
                        $kegiatan,
                        $kro,
                        $roCode,
                        $subkompCode,
                        $akunCode,
                        $akunData['name'],
                        $pic,
                        $akunData['pagu']
                    );

                    $totalPaguSubkomp += $akunData['pagu'];
                    $this->command->line("      ✓ Created Akun: {$akunCode} - Rp " . number_format($akunData['pagu'], 2, ',', '.'));
                }

                // Update pagu Sub Komponen (SUM dari akun)
                $subkompLevel->update([
                    'pagu_anggaran' => $totalPaguSubkomp,
                    'sisa' => $totalPaguSubkomp,
                ]);

                $totalPaguRO += $totalPaguSubkomp;
                $this->command->line("    → Total Pagu Sub Komponen {$subkompCode}: Rp " . number_format($totalPaguSubkomp, 2, ',', '.'));
            }

            // Update pagu RO Level (SUM dari sub komponen)
            $roLevel->update([
                'pagu_anggaran' => $totalPaguRO,
                'sisa' => $totalPaguRO,
            ]);

            $this->command->info("  ✓ Completed RO: {$roCode} with total pagu: Rp " . number_format($totalPaguRO, 2, ',', '.'));
            $this->command->info('');
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
            'pagu_anggaran' => $pagu, // Nilai presisi tanpa pembulatan
            'referensi' => $fullRef,
            'referensi2' => $baseRef . $subkomponen,
            'ref_output' => $baseRef,
            'len' => strlen($fullRef),
            'sisa' => $pagu, // Nilai presisi
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
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║                    SUMMARY ANGGARAN                        ║');
        $this->command->info('╠════════════════════════════════════════════════════════════╣');
        $this->command->table(
            ['Kategori', 'Jumlah'],
            [
                ['Total RO Level', $totalRO],
                ['Total Sub Komponen', $totalSubkomp],
                ['Total Akun (Detail)', $totalAkun],
                ['Total Records', $totalRO + $totalSubkomp + $totalAkun],
            ]
        );
        $this->command->info("║ Total Pagu Keseluruhan: Rp " . number_format($totalPagu, 2, ',', '.') . str_repeat(' ', max(0, 26 - strlen(number_format($totalPagu, 2, ',', '.')))) . "║");
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->info('');

        // Detail per RO
        $this->command->info('Detail Pagu per RO:');
        $roList = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun')->get();

        $tableData = [];
        foreach ($roList as $ro) {
            $tableData[] = [
                $ro->ro,
                $ro->program_kegiatan,
                'Rp ' . number_format($ro->pagu_anggaran, 2, ',', '.')
            ];
        }

        $this->command->table(['RO', 'Nama', 'Pagu'], $tableData);
    }
}
