<?php
// app/Imports/DataAnggaranImport.php

namespace App\Imports;

use App\Models\Anggaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class DataAnggaranImport implements ToCollection, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    public array $errors   = [];
    public int   $imported = 0;
    public int   $updated  = 0;

    public function collection(Collection $rows): void
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2; // +2 karena heading row

                // Skip baris kosong
                if (empty($row['ro']) && empty($row['kode_akun'])) continue;

                $kegiatan        = trim($row['kegiatan'] ?? '4753');
                $kro             = trim($row['kro'] ?? 'EBA');
                $ro              = trim($row['ro'] ?? '');
                $kodeSubkomponen = trim($row['kode_subkomponen'] ?? '') ?: null;
                $kodeAkun        = trim($row['kode_akun'] ?? '') ?: null;
                $programKegiatan = trim($row['program_kegiatan'] ?? '');
                $pic             = trim($row['pic'] ?? 'SJ.7');
                $paguAnggaran    = (float) str_replace(['.', ','], ['', '.'], $row['pagu_anggaran'] ?? 0);

                if (empty($ro) || empty($programKegiatan)) {
                    $this->errors[] = "Baris {$rowNum}: RO dan Program/Kegiatan wajib diisi";
                    continue;
                }

                // Hanya level Akun yang punya pagu
                if (!$kodeAkun) {
                    $paguAnggaran = 0;
                }

                // Generate referensi
                $baseRef   = $kegiatan . $kro . $ro;
                $referensi = $baseRef;
                $ref2      = $baseRef;

                if ($kodeSubkomponen) {
                    $referensi .= $kodeSubkomponen;
                    $ref2       = $baseRef . $kodeSubkomponen;
                }
                if ($kodeAkun) {
                    $referensi .= $kodeAkun;
                }

                // Cek apakah sudah ada (upsert)
                $existing = Anggaran::where('kegiatan', $kegiatan)
                    ->where('kro', $kro)
                    ->where('ro', $ro)
                    ->where('kode_subkomponen', $kodeSubkomponen)
                    ->where('kode_akun', $kodeAkun)
                    ->first();

                if ($existing) {
                    $selisih = $paguAnggaran - $existing->pagu_anggaran;
                    $existing->update([
                        'program_kegiatan' => $programKegiatan,
                        'pic'              => $pic,
                        'pagu_anggaran'    => $paguAnggaran,
                        'sisa'             => $existing->sisa + $selisih,
                        'referensi'        => $referensi,
                        'referensi2'       => $ref2,
                        'ref_output'       => $baseRef,
                        'len'              => strlen($ref2),
                    ]);
                    $this->updated++;
                } else {
                    Anggaran::create([
                        'kegiatan'         => $kegiatan,
                        'kro'              => $kro,
                        'ro'               => $ro,
                        'kode_subkomponen' => $kodeSubkomponen,
                        'kode_akun'        => $kodeAkun,
                        'program_kegiatan' => $programKegiatan,
                        'pic'              => $pic,
                        'pagu_anggaran'    => $paguAnggaran,
                        'sisa'             => $paguAnggaran,
                        'total_penyerapan' => 0,
                        'tagihan_outstanding' => 0,
                        'referensi'        => $referensi,
                        'referensi2'       => $ref2,
                        'ref_output'       => $baseRef,
                        'len'              => strlen($ref2),
                        'januari'          => 0, 'februari' => 0, 'maret'    => 0,
                        'april'            => 0, 'mei'      => 0, 'juni'     => 0,
                        'juli'             => 0, 'agustus'  => 0, 'september'=> 0,
                        'oktober'          => 0, 'november' => 0, 'desember' => 0,
                    ]);
                    $this->imported++;
                }
            }

            // Recalculate semua parent setelah import selesai
            $this->recalculateAllParents();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Anggaran error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function recalculateAllParents(): void
    {
        // Update semua SubKomponen dari Akun
        $subkomponens = Anggaran::whereNotNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->get();

        foreach ($subkomponens as $subkomp) {
            $totals = Anggaran::where('kegiatan', $subkomp->kegiatan)
                ->where('kro', $subkomp->kro)
                ->where('ro', $subkomp->ro)
                ->where('kode_subkomponen', $subkomp->kode_subkomponen)
                ->whereNotNull('kode_akun')
                ->selectRaw('SUM(pagu_anggaran) as pagu, SUM(total_penyerapan) as realisasi')
                ->first();

            $subkomp->update([
                'pagu_anggaran'    => $totals->pagu ?? 0,
                'total_penyerapan' => $totals->realisasi ?? 0,
                'sisa'             => ($totals->pagu ?? 0) - ($totals->realisasi ?? 0),
            ]);
        }

        // Update semua RO dari SubKomponen
        $ros = Anggaran::whereNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->get();

        foreach ($ros as $ro) {
            $totals = Anggaran::where('kegiatan', $ro->kegiatan)
                ->where('kro', $ro->kro)
                ->where('ro', $ro->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->selectRaw('SUM(pagu_anggaran) as pagu, SUM(total_penyerapan) as realisasi')
                ->first();

            $ro->update([
                'pagu_anggaran'    => $totals->pagu ?? 0,
                'total_penyerapan' => $totals->realisasi ?? 0,
                'sisa'             => ($totals->pagu ?? 0) - ($totals->realisasi ?? 0),
            ]);
        }
    }
}
