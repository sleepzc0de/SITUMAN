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
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Realisasi Anggaran Seeder...');

        // Ambil semua akun (level detail)
        $akunList = Anggaran::whereNotNull('kode_akun')->get();

        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        $jenisBelanja = ['Kontraktual', 'Non Kontraktual', 'GUP', 'TUP'];
        $lsBendahara = ['LS', 'Bendahara'];

        $sppCounter = 1;

        foreach ($akunList as $akun) {
            // Random: 30% kemungkinan akun ini memiliki realisasi
            if (rand(1, 100) > 30) {
                continue;
            }

            $this->command->line("Creating SPP for Akun: {$akun->kode_akun}");

            // Buat 1-3 SPP untuk akun ini
            $jumlahSPP = rand(1, 3);

            for ($i = 0; $i < $jumlahSPP; $i++) {
                // Random bulan
                $bulan = $bulanList[array_rand($bulanList)];

                // Nilai SPP antara 10-50% dari pagu
                $maxNilai = $akun->pagu_anggaran * 0.5;
                $minNilai = $akun->pagu_anggaran * 0.1;
                $bruto = rand($minNilai, $maxNilai);

                // Hitung pajak
                $ppnPercent = rand(0, 11); // 0% atau 11%
                $pphPercent = rand(0, 2) == 0 ? 0 : 2; // 0% atau 2%

                $ppn = ($bruto * $ppnPercent) / 100;
                $pph = ($bruto * $pphPercent) / 100;
                $netto = $bruto - $ppn - $pph;

                // Random status: 70% sudah SP2D, 30% belum
                $status = rand(1, 100) <= 70 ? 'Tagihan Telah SP2D' : 'Tagihan Belum SP2D';

                // Tanggal SPP dalam 6 bulan terakhir
                $tglSPP = Carbon::now()->subMonths(rand(1, 6))->subDays(rand(1, 28));

                $spp = SPP::create([
                    'id' => Str::uuid(),
                    'bulan' => $bulan,
                    'no_spp' => 'SPP-' . str_pad($sppCounter, 5, '0', STR_PAD_LEFT) . '/2024',
                    'nominatif' => 'Nominatif ' . $sppCounter,
                    'tgl_spp' => $tglSPP,
                    'jenis_kegiatan' => 'Kegiatan ' . $akun->program_kegiatan,
                    'jenis_belanja' => $jenisBelanja[array_rand($jenisBelanja)],
                    'uraian_spp' => 'Uraian SPP untuk ' . $akun->program_kegiatan,
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
                    'ls_bendahara' => $lsBendahara[array_rand($lsBendahara)],
                    'status' => $status,
                    'coa' => $akun->kegiatan . $akun->kro . $akun->ro . $akun->kode_akun,
                ]);

                // Update data anggaran
                $this->updateAnggaranFromSPP($akun, $spp);

                $this->command->line("  ✓ Created SPP: {$spp->no_spp} - Rp " . number_format($netto, 0, ',', '.'));

                $sppCounter++;
            }
        }

        $this->command->info('Realisasi Anggaran Seeder completed successfully!');
        $this->printSummary();
    }

    /**
     * Update anggaran based on SPP
     */
    private function updateAnggaranFromSPP($akun, $spp)
    {
        $bulan = strtolower($spp->bulan);

        // Update realisasi bulan atau outstanding
        if ($spp->status === 'Tagihan Telah SP2D') {
            $akun->$bulan = ($akun->$bulan ?? 0) + $spp->netto;
        } else {
            $akun->tagihan_outstanding = ($akun->tagihan_outstanding ?? 0) + $spp->netto;
        }

        // Hitung total penyerapan
        $akun->total_penyerapan =
            ($akun->januari ?? 0) +
            ($akun->februari ?? 0) +
            ($akun->maret ?? 0) +
            ($akun->april ?? 0) +
            ($akun->mei ?? 0) +
            ($akun->juni ?? 0) +
            ($akun->juli ?? 0) +
            ($akun->agustus ?? 0) +
            ($akun->september ?? 0) +
            ($akun->oktober ?? 0) +
            ($akun->november ?? 0) +
            ($akun->desember ?? 0);

        $akun->sisa = $akun->pagu_anggaran - $akun->total_penyerapan;
        $akun->save();

        // Update parent (Sub Komponen)
        $this->updateParentLevels($akun);
    }

    /**
     * Update parent levels
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
            $subkomp->sisa = $subkomp->pagu_anggaran - $subkomp->total_penyerapan;
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
            $ro->sisa = $ro->pagu_anggaran - $ro->total_penyerapan;
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
        $sudahSP2D = SPP::where('status', 'Tagihan Telah SP2D')->count();
        $belumSP2D = SPP::where('status', 'Tagihan Belum SP2D')->count();

        $this->command->info('');
        $this->command->info('=== Summary ===');
        $this->command->info("Total SPP: {$totalSPP}");
        $this->command->info("Sudah SP2D: {$sudahSP2D}");
        $this->command->info("Belum SP2D: {$belumSP2D}");
        $this->command->info("Total Bruto: Rp " . number_format($totalBruto, 0, ',', '.'));
        $this->command->info("Total Netto: Rp " . number_format($totalNetto, 0, ',', '.'));
        $this->command->info('');
    }
}
