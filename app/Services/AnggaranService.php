<?php

namespace App\Services;

use App\Models\Anggaran;
use App\Models\SPP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnggaranService
{
    /**
     * Recalculate anggaran level Akun dari semua SPP yang terkait.
     * Fix SQL Server: tidak bisa pakai alias di GROUP BY.
     */
    public function recalculateAkun(Anggaran $anggaran): void
    {
        if (!$anggaran->kode_akun) return;

        $coa = $anggaran->kegiatan . $anggaran->kro . $anggaran->ro . $anggaran->kode_akun;

        $bulanMap = [
            1  => 'januari',
            2  => 'februari',
            3  => 'maret',
            4  => 'april',
            5  => 'mei',
            6  => 'juni',
            7  => 'juli',
            8  => 'agustus',
            9  => 'september',
            10 => 'oktober',
            11 => 'november',
            12 => 'desember',
        ];

        // ── Fix SQL Server: gunakan DATEPART, jangan pakai alias di GROUP BY ──
        $realisasiPerBulan = SPP::where('coa', $coa)
            ->where('status', 'Tagihan Telah SP2D')
            ->whereNull('deleted_at')
            ->whereNotNull('tgl_sp2d')
            ->selectRaw('DATEPART(month, tgl_sp2d) as bulan_num, SUM(netto) as total')
            ->groupByRaw('DATEPART(month, tgl_sp2d)')
            ->pluck('total', 'bulan_num');

        $totalOutstanding = SPP::where('coa', $coa)
            ->where('status', 'Tagihan Belum SP2D')
            ->whereNull('deleted_at')
            ->sum('netto');

        $updates         = ['tagihan_outstanding' => $totalOutstanding];
        $totalPenyerapan = 0;

        foreach ($bulanMap as $num => $nama) {
            $nilai           = $realisasiPerBulan[$num] ?? 0;
            $updates[$nama]  = $nilai;
            $totalPenyerapan += $nilai;
        }

        $updates['total_penyerapan'] = $totalPenyerapan;
        $updates['sisa']             = $anggaran->pagu_anggaran - $totalPenyerapan;

        $anggaran->update($updates);
    }

    /**
     * Propagate nilai dari Akun ke SubKomponen ke RO.
     */
    public function propagateKeParent(Anggaran $anggaran): void
    {
        $bulanFields = [
            'januari',
            'februari',
            'maret',
            'april',
            'mei',
            'juni',
            'juli',
            'agustus',
            'september',
            'oktober',
            'november',
            'desember'
        ];

        if ($anggaran->kode_subkomponen) {
            $subkomp = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->where('kode_subkomponen', $anggaran->kode_subkomponen)
                ->whereNull('kode_akun')
                ->first();

            if ($subkomp) {
                $childrenAkun = Anggaran::where('kegiatan', $anggaran->kegiatan)
                    ->where('kro', $anggaran->kro)
                    ->where('ro', $anggaran->ro)
                    ->where('kode_subkomponen', $anggaran->kode_subkomponen)
                    ->whereNotNull('kode_akun')
                    ->get();

                $subkompUpdates = [
                    'pagu_anggaran'       => $childrenAkun->sum('pagu_anggaran'),
                    'total_penyerapan'    => $childrenAkun->sum('total_penyerapan'),
                    'tagihan_outstanding' => $childrenAkun->sum('tagihan_outstanding'),
                    'sisa'                => $childrenAkun->sum('sisa'),
                ];
                foreach ($bulanFields as $bulan) {
                    $subkompUpdates[$bulan] = $childrenAkun->sum($bulan);
                }
                $subkomp->update($subkompUpdates);
            }
        }

        $ro = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->whereNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->first();

        if ($ro) {
            $childrenSubkomp = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->get();

            $roUpdates = [
                'pagu_anggaran'       => $childrenSubkomp->sum('pagu_anggaran'),
                'total_penyerapan'    => $childrenSubkomp->sum('total_penyerapan'),
                'tagihan_outstanding' => $childrenSubkomp->sum('tagihan_outstanding'),
                'sisa'                => $childrenSubkomp->sum('sisa'),
            ];
            foreach ($bulanFields as $bulan) {
                $roUpdates[$bulan] = $childrenSubkomp->sum($bulan);
            }
            $ro->update($roUpdates);
        }
    }

    public function updatePaguFromRevisi(Anggaran $anggaran, float $paguBaru): void
    {
        $selisih = $paguBaru - $anggaran->pagu_anggaran;
        $anggaran->update([
            'pagu_anggaran' => $paguBaru,
            'sisa'          => $anggaran->sisa + $selisih,
        ]);
        $this->propagateKeParent($anggaran);
    }

    public function syncFromSPP(string $coa): void
    {
        try {
            $anggaran = Anggaran::whereNotNull('kode_akun')
                ->whereRaw("kegiatan + kro + ro + kode_akun = ?", [$coa])
                ->first();

            if (!$anggaran) {
                Log::warning("AnggaranService::syncFromSPP - Anggaran tidak ditemukan untuk COA: {$coa}");
                return;
            }

            $this->recalculateAkun($anggaran);
            $this->propagateKeParent($anggaran);
        } catch (\Exception $e) {
            Log::error("AnggaranService::syncFromSPP error: " . $e->getMessage(), [
                'coa'   => $coa,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function recalculateAll(): array
    {
        $stats = ['akun' => 0, 'subkomp' => 0, 'ro' => 0];

        DB::transaction(function () use (&$stats) {
            $bulanFields = [
                'januari',
                'februari',
                'maret',
                'april',
                'mei',
                'juni',
                'juli',
                'agustus',
                'september',
                'oktober',
                'november',
                'desember'
            ];

            // 1. Recalculate level Akun
            $akunList = Anggaran::whereNotNull('kode_akun')->get();
            foreach ($akunList as $akun) {
                $this->recalculateAkun($akun);
                $stats['akun']++;
            }

            // 2. Aggregate SubKomponen
            $subkompList = Anggaran::whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->get();

            foreach ($subkompList as $subkomp) {
                $children = Anggaran::where('kegiatan', $subkomp->kegiatan)
                    ->where('kro', $subkomp->kro)
                    ->where('ro', $subkomp->ro)
                    ->where('kode_subkomponen', $subkomp->kode_subkomponen)
                    ->whereNotNull('kode_akun')
                    ->get();

                $updates = [
                    'pagu_anggaran'       => $children->sum('pagu_anggaran'),
                    'total_penyerapan'    => $children->sum('total_penyerapan'),
                    'tagihan_outstanding' => $children->sum('tagihan_outstanding'),
                    'sisa'                => $children->sum('sisa'),
                ];
                foreach ($bulanFields as $bulan) {
                    $updates[$bulan] = $children->sum($bulan);
                }
                $subkomp->update($updates);
                $stats['subkomp']++;
            }

            // 3. Aggregate RO
            $roList = Anggaran::whereNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->get();

            foreach ($roList as $ro) {
                $children = Anggaran::where('kegiatan', $ro->kegiatan)
                    ->where('kro', $ro->kro)
                    ->where('ro', $ro->ro)
                    ->whereNotNull('kode_subkomponen')
                    ->whereNull('kode_akun')
                    ->get();

                $updates = [
                    'pagu_anggaran'       => $children->sum('pagu_anggaran'),
                    'total_penyerapan'    => $children->sum('total_penyerapan'),
                    'tagihan_outstanding' => $children->sum('tagihan_outstanding'),
                    'sisa'                => $children->sum('sisa'),
                ];
                foreach ($bulanFields as $bulan) {
                    $updates[$bulan] = $children->sum($bulan);
                }
                $ro->update($updates);
                $stats['ro']++;
            }
        });

        return $stats;
    }
}
