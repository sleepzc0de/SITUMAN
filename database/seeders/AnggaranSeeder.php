<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnggaranSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks untuk SQL Server
        DB::statement('SET NOCOUNT ON');

        // Hapus data dengan cara yang aman
        DB::table('anggaran')->delete();

        // Reset identity jika ada
        DB::statement('DBCC CHECKIDENT (\'anggaran\', RESEED, 0)');

        $data = [
            // ====================================================
            // RO Z06 - Rencana Kebutuhan BMN dan Pengelolaannya
            // ====================================================

            // Level RO (Parent)
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => 'Z06',
                'kode_subkomponen' => null,
                'kode_akun' => null,
                'program_kegiatan' => 'Rencana Kebutuhan BMN dan Pengelolaannya',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 572012000,
                'referensi' => '4753EBAZ06',
                'referensi2' => '4753EBAZ06',
                'ref_output' => '4753EBAZ06',
                'len' => 10,
            ],

            // Level Sub Komponen AA
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => 'Z06',
                'kode_subkomponen' => 'AA',
                'kode_akun' => null,
                'program_kegiatan' => 'Koordinasi pemenuhan kebutuhan gedung kantor dan hunian pegawai kemenkeu',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 65499000,
                'referensi' => '4753EBAZ06AA',
                'referensi2' => '4753EBAZ06AA',
                'ref_output' => '4753EBAZ06',
                'len' => 12,
            ],

            // Level Akun (Detail) - Sub Komponen AA
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => 'Z06',
                'kode_subkomponen' => 'AA',
                'kode_akun' => '521211',
                'program_kegiatan' => 'Belanja bahan',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 4313000,
                'referensi' => '4753EBAZ06AA521211',
                'referensi2' => '4753EBAZ06AA',
                'ref_output' => '4753EBAZ06',
                'len' => 18,
            ],
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => 'Z06',
                'kode_subkomponen' => 'AA',
                'kode_akun' => '524111',
                'program_kegiatan' => 'Belanja perjalanan biasa',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 61186000,
                'referensi' => '4753EBAZ06AA524111',
                'referensi2' => '4753EBAZ06AA',
                'ref_output' => '4753EBAZ06',
                'len' => 18,
            ],

            // Level Sub Komponen AB
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => 'Z06',
                'kode_subkomponen' => 'AB',
                'kode_akun' => null,
                'program_kegiatan' => 'Pengelolaan BMN Lainnya',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 506513000,
                'referensi' => '4753EBAZ06AB',
                'referensi2' => '4753EBAZ06AB',
                'ref_output' => '4753EBAZ06',
                'len' => 12,
            ],
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => 'Z06',
                'kode_subkomponen' => 'AB',
                'kode_akun' => '521211',
                'program_kegiatan' => 'Belanja bahan',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 506513000,
                'referensi' => '4753EBAZ06AB521211',
                'referensi2' => '4753EBAZ06AB',
                'ref_output' => '4753EBAZ06',
                'len' => 18,
            ],

            // ====================================================
            // RO 403 - Layanan Pengadaan
            // ====================================================

            // Level RO (Parent)
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '403',
                'kode_subkomponen' => null,
                'kode_akun' => null,
                'program_kegiatan' => 'Layanan Pengadaan',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 39265000,
                'referensi' => '4753EBA403',
                'referensi2' => '4753EBA403',
                'ref_output' => '4753EBA403',
                'len' => 10,
            ],

            // Level Sub Komponen AA
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '403',
                'kode_subkomponen' => 'AA',
                'kode_akun' => null,
                'program_kegiatan' => 'Pelaksanaan Operasional UKPBJ Kementerian Keuangan',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 39265000,
                'referensi' => '4753EBA403AA',
                'referensi2' => '4753EBA403AA',
                'ref_output' => '4753EBA403',
                'len' => 12,
            ],

            // Level Akun (Detail) - Sub Komponen AA
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '403',
                'kode_subkomponen' => 'AA',
                'kode_akun' => '521211',
                'program_kegiatan' => 'Belanja bahan',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 19681000,
                'referensi' => '4753EBA403AA521211',
                'referensi2' => '4753EBA403AA',
                'ref_output' => '4753EBA403',
                'len' => 18,
            ],
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '403',
                'kode_subkomponen' => 'AA',
                'kode_akun' => '524113',
                'program_kegiatan' => 'Belanja Perjalanan Dinas Dalam Kota',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 19584000,
                'referensi' => '4753EBA403AA524113',
                'referensi2' => '4753EBA403AA',
                'ref_output' => '4753EBA403',
                'len' => 18,
            ],

            // ====================================================
            // RO 405 - Kerumahtanggaan
            // ====================================================

            // Level RO (Parent)
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '405',
                'kode_subkomponen' => null,
                'kode_akun' => null,
                'program_kegiatan' => 'Kerumahtanggaan',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 118793000,
                'referensi' => '4753EBA405',
                'referensi2' => '4753EBA405',
                'ref_output' => '4753EBA405',
                'len' => 10,
            ],

            // Level Sub Komponen CC
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '405',
                'kode_subkomponen' => 'CC',
                'kode_akun' => null,
                'program_kegiatan' => 'Penyelenggaraan Operasional dan Pemeliharaan Perkantoran',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 118793000,
                'referensi' => '4753EBA405CC',
                'referensi2' => '4753EBA405CC',
                'ref_output' => '4753EBA405',
                'len' => 12,
            ],

            // Level Akun (Detail) - Sub Komponen CC
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '405',
                'kode_subkomponen' => 'CC',
                'kode_akun' => '521211',
                'program_kegiatan' => 'Belanja bahan',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 14377000,
                'referensi' => '4753EBA405CC521211',
                'referensi2' => '4753EBA405CC',
                'ref_output' => '4753EBA405',
                'len' => 18,
            ],
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '405',
                'kode_subkomponen' => 'CC',
                'kode_akun' => '522111',
                'program_kegiatan' => 'Belanja Langganan Listrik',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 104416000,
                'referensi' => '4753EBA405CC522111',
                'referensi2' => '4753EBA405CC',
                'ref_output' => '4753EBA405',
                'len' => 18,
            ],

            // ====================================================
            // RO 994 - Layanan Perkantoran
            // ====================================================

            // Level RO (Parent)
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '994',
                'kode_subkomponen' => null,
                'kode_akun' => null,
                'program_kegiatan' => 'Layanan Perkantoran',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 27332937000,
                'referensi' => '4753EBA994',
                'referensi2' => '4753EBA994',
                'ref_output' => '4753EBA994',
                'len' => 10,
            ],

            // Level Sub Komponen GG
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '994',
                'kode_subkomponen' => 'GG',
                'kode_akun' => null,
                'program_kegiatan' => 'Operasional dan Pemeliharaan Kantor',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 27332937000,
                'referensi' => '4753EBA994GG',
                'referensi2' => '4753EBA994GG',
                'ref_output' => '4753EBA994',
                'len' => 12,
            ],

            // Level Akun (Detail) - Sub Komponen GG
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '994',
                'kode_subkomponen' => 'GG',
                'kode_akun' => '521115',
                'program_kegiatan' => 'Honor Operasional satuan kerja',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 201000000,
                'referensi' => '4753EBA994GG521115',
                'referensi2' => '4753EBA994GG',
                'ref_output' => '4753EBA994',
                'len' => 18,
            ],
            [
                'kegiatan' => '4753',
                'kro' => 'EBA',
                'ro' => '994',
                'kode_subkomponen' => 'GG',
                'kode_akun' => '523113',
                'program_kegiatan' => 'Belanja Asuransi Gedung dan Bangunan',
                'pic' => 'SJ.7',
                'pagu_anggaran' => 27131937000,
                'referensi' => '4753EBA994GG523113',
                'referensi2' => '4753EBA994GG',
                'ref_output' => '4753EBA994',
                'len' => 18,
            ],
        ];

        // Insert data dengan default values
        foreach ($data as $item) {
            DB::table('anggaran')->insert(array_merge($item, [
                'januari' => 0,
                'februari' => 0,
                'maret' => 0,
                'april' => 0,
                'mei' => 0,
                'juni' => 0,
                'juli' => 0,
                'agustus' => 0,
                'september' => 0,
                'oktober' => 0,
                'november' => 0,
                'desember' => 0,
                'tagihan_outstanding' => 0,
                'total_penyerapan' => 0,
                'sisa' => $item['pagu_anggaran'],
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ Anggaran seeder completed successfully!');
        $this->command->info('📊 Total records inserted: ' . count($data));
    }
}
