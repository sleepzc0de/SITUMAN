<?php
// database/seeders/RealisasiAnggaranSeeder.php

namespace Database\Seeders;

use App\Models\Anggaran;
use App\Models\SPP;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RealisasiAnggaranSeeder extends Seeder
{
    /**
     * Data SPP dengan nilai presisi
     */
    private $sppData = [
        // Format: [akun_ro_subkomp, bulan, bruto, ppn_percent, pph_percent, status]
        ['4753EBAZ06AA521211', 'januari', 45750000.50, 11, 2, 'Tagihan Telah SP2D'],
        ['4753EBAZ06AA521213', 'januari', 89500000.75, 11, 0, 'Tagihan Telah SP2D'],
        ['4753EBAZ06AA522151', 'februari', 34250000.25, 0, 2, 'Tagihan Telah SP2D'],
        ['4753EBAZ06AB521211', 'februari', 78900000.00, 11, 2, 'Tagihan Belum SP2D'],
        ['4753EBAZ06AB523121', 'maret', 156780000.50, 11, 0, 'Tagihan Telah SP2D'],
        ['4753EBA403BA532111', 'januari', 450000000.00, 11, 2, 'Tagihan Telah SP2D'],
        ['4753EBA403BA521811', 'februari', 234567000.75, 11, 0, 'Tagihan Telah SP2D'],
        ['4753EBA403BB522151', 'maret', 345670000.50, 0, 2, 'Tagihan Belum SP2D'],
        ['4753EBA405CA521811', 'april', 189450000.25, 11, 2, 'Tagihan Telah SP2D'],
        ['4753EBA405CB523121', 'mei', 567890000.00, 11, 0, 'Tagihan Telah SP2D'],
        ['4753EBA994DA521211', 'juni', 98765000.50, 0, 2, 'Tagihan Telah SP2D'],
        ['4753EBA994DB521811', 'juli', 234567000.25, 11, 2, 'Tagihan Belum SP2D'],
    ];

    public function run(): void
    {
        $this->command->info('Starting Realisasi Anggaran Seeder (Presisi)...');

        $sppCounter = 1;
        $bulanMapping = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];

        foreach ($this->sppData as $data) {
            [$coa, $bulan, $bruto, $ppnPercent, $pphPercent, $status] = $data;

            // Cari anggaran berdasarkan COA
            $akun = Anggaran::whereNotNull('kode_akun')
                ->where('kegiatan', substr($coa, 0, 4))
                ->where('kro', substr($coa, 4, 3))
                ->where('ro', substr($coa, 7, 3))
                ->where('kode_subkomponen', substr($coa, 10, 2))
                ->where('kode_akun', substr($coa, 12, 6))
                ->first();

            if (!$akun) {
                $this->command->warn("Akun not found for COA: {$coa}");
                continue;
            }

            // Hitung pajak dengan presisi
            $ppn = ($bruto * $ppnPercent) / 100;
            $pph = ($bruto * $pphPercent) / 100;
            $netto = $bruto - $ppn - $pph;

            // Tanggal SPP
            $bulanNum = $bulanMapping[$bulan];
            $tglSPP = Carbon::create(2024, $bulanNum, rand(5, 25));

            $spp = SPP::create([
                'id' => Str::uuid(),
                'bulan' => $bulan,
                'no_spp' => 'SPP-' . str_pad($sppCounter, 5, '0', STR_PAD_LEFT) . '/2024',
                'nominatif' => 'Nominatif ' . $akun->program_kegiatan,
                'tgl_spp' => $tglSPP,
                'jenis_kegiatan' => $akun->program_kegiatan,
                'jenis_belanja' => 'Kontraktual',
                'uraian_spp' => 'Pembayaran untuk ' . $akun->program_kegiatan . ' bulan ' . ucfirst($bulan),
                'bagian' => 'Bagian ' . $akun->pic,
                'nama_pic' => 'PIC ' . $akun->pic,
                'kode_kegiatan' => $akun->kegiatan,
                'kro' => $akun->kro,
                'ro' => $akun->ro,
                'sub_komponen' => $akun->kode_subkomponen,
                'mak' => $akun->kode_akun,
                'bruto' => $bruto,
                'ppn' => $ppn,
                'pph' => $pph,
                'netto' => $netto,
                'ls_bendahara' => 'LS',
                'status' => $status,
                'coa' => $coa,
            ]);

            // Update anggaran dengan nilai presisi
            $this->updateAnggaranFromSPP($akun, $spp);

            $this->command->line("✓ SPP-{$sppCounter}: {$akun->program_kegiatan} - Rp " . number_format($netto, 2, ',', '.'));
            $sppCounter++;
        }

        $this->command->info('Realisasi Anggaran Seeder completed!');
        $this->printSummary();
    }

    /**
     * Update anggaran based on SPP dengan presisi
     */
    private function updateAnggaranFromSPP($akun, $spp)
    {
        $bulan = strtolower($spp->bulan);

        // Update realisasi bulan atau outstanding dengan nilai presisi
        if ($spp->status === 'Tagihan Telah SP2D') {
            $akun->$bulan = bcadd($akun->$bulan ?? 0, $spp->netto, 2);
        } else {
            $akun->tagihan_outstanding = bcadd($akun->tagihan_outstanding ?? 0, $spp->netto, 2);
        }

        // Hitung total penyerapan dengan presisi
        $total = 0;
        foreach (['januari', 'februari', 'maret', 'april', 'mei', 'juni',
                  'juli', 'agustus', 'september', 'oktober', 'november', 'desember'] as $b) {
            $total = bcadd($total, $akun->$b ?? 0, 2);
        }

        $akun->total_penyerapan = $total;
        $akun->sisa = bcsub($akun->pagu_anggaran, $total, 2);
        $akun->save();

        // Update parent levels
        $this->updateParentLevels($akun);
    }

    /**
     * Update parent levels dengan presisi
     */
    private function updateParentLevels($akun)
    {
        // Update Sub Komponen
        $subkomp = Anggaran::where('kegiatan', $akun->kegiatan)
            ->where('kro', $akun->kro)
            ->where('ro', $akun->ro)
            ->where('kode_subkomponen', $akun->kode_subkomponen)
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp) {
            $children = Anggaran::where('kegiatan', $akun->kegiatan)
                ->where('kro', $akun->kro)
                ->where('ro', $akun->ro)
                ->where('kode_subkomponen', $akun->kode_subkomponen)
                ->whereNotNull('kode_akun')
                ->get();

            foreach (['januari', 'februari', 'maret', 'april', 'mei', 'juni',
                      'juli', 'agustus', 'september', 'oktober', 'november', 'desember'] as $bulan) {
                $subkomp->$bulan = $children->sum($bulan);
            }

            $subkomp->tagihan_outstanding = $children->sum('tagihan_outstanding');
            $subkomp->total_penyerapan = $children->sum('total_penyerapan');
            $subkomp->sisa = bcsub($subkomp->pagu_anggaran, $subkomp->total_penyerapan, 2);
            $subkomp->save();
        }

        // Update RO
        $ro = Anggaran::where('kegiatan', $akun->kegiatan)
            ->where('kro', $akun->kro)
            ->where('ro', $akun->ro)
            ->whereNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->first();

        if ($ro) {
            $children = Anggaran::where('kegiatan', $akun->kegiatan)
                ->where('kro', $akun->kro)
                ->where('ro', $akun->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->get();

            foreach (['januari', 'februari', 'maret', 'april', 'mei', 'juni',
                      'juli', 'agustus', 'september', 'oktober', 'november', 'desember'] as $bulan) {
                $ro->$bulan = $children->sum($bulan);
            }

            $ro->tagihan_outstanding = $children->sum('tagihan_outstanding');
            $ro->total_penyerapan = $children->sum('total_penyerapan');
            $ro->sisa = bcsub($ro->pagu_anggaran, $ro->total_penyerapan, 2);
            $ro->save();
        }
    }

    /**
     * Print summary
     */
    private function printSummary()
    {
        $totalSPP = SPP::count();
        $totalBruto = SPP::sum('bruto');
        $totalNetto = SPP::sum('netto');
        $totalPPN = SPP::sum('ppn');
        $totalPPh = SPP::sum('pph');
        $sudahSP2D = SPP::where('status', 'Tagihan Telah SP2D')->sum('netto');
        $belumSP2D = SPP::where('status', 'Tagihan Belum SP2D')->sum('netto');

        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║                   SUMMARY REALISASI                        ║');
        $this->command->info('╠════════════════════════════════════════════════════════════╣');
        $this->command->table(
            ['Kategori', 'Nilai'],
            [
                ['Total SPP', $totalSPP],
                ['Total Bruto', 'Rp ' . number_format($totalBruto, 2, ',', '.')],
                ['Total PPN', 'Rp ' . number_format($totalPPN, 2, ',', '.')],
                ['Total PPh', 'Rp ' . number_format($totalPPh, 2, ',', '.')],
                ['Total Netto', 'Rp ' . number_format($totalNetto, 2, ',', '.')],
                ['Sudah SP2D', 'Rp ' . number_format($sudahSP2D, 2, ',', '.')],
                ['Belum SP2D', 'Rp ' . number_format($belumSP2D, 2, ',', '.')],
            ]
        );
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->info('');
    }
}
