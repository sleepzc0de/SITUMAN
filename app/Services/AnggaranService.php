<?php
// app/Services/AnggaranService.php

namespace App\Services;

use App\Models\Anggaran;
use App\Models\SPP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnggaranService
{
    /**
     * Recalculate anggaran level Akun dari semua SPP yang terkait
     * Ini adalah sumber kebenaran tunggal (single source of truth)
     */
    public function recalculateAkun(Anggaran $anggaran): void
    {
        if (!$anggaran->kode_akun) return;

        $coa = $anggaran->kegiatan . $anggaran->kro . $anggaran->ro . $anggaran->kode_akun;

        // Mapping bulan ke angka
        $bulanMap = [
            1  => 'januari',  2  => 'februari', 3  => 'maret',
            4  => 'april',    5  => 'mei',       6  => 'juni',
            7  => 'juli',     8  => 'agustus',   9  => 'september',
            10 => 'oktober',  11 => 'november',  12 => 'desember',
        ];

        // Hitung realisasi per bulan dari SPP yang sudah SP2D
        // Gunakan tgl_sp2d untuk menentukan bulan realisasi
        $realisasiPerBulan = SPP::where('coa', $coa)
            ->where('status', 'Tagihan Telah SP2D')
            ->whereNull('deleted_at')
            ->whereNotNull('tgl_sp2d')
            ->selectRaw('MONTH(tgl_sp2d) as bulan_num, SUM(netto) as total')
            ->groupBy('bulan_num')
            ->pluck('total', 'bulan_num');

        // Hitung tagihan outstanding (belum SP2D)
        $totalOutstanding = SPP::where('coa', $coa)
            ->where('status', 'Tagihan Belum SP2D')
            ->whereNull('deleted_at')
            ->sum('netto');

        // Build update array
        $updates = ['tagihan_outstanding' => $totalOutstanding];
        $totalPenyerapan = 0;

        foreach ($bulanMap as $num => $nama) {
            $nilai = $realisasiPerBulan[$num] ?? 0;
            $updates[$nama] = $nilai;
            $totalPenyerapan += $nilai;
        }

        $updates['total_penyerapan'] = $totalPenyerapan;
        $updates['sisa'] = $anggaran->pagu_anggaran - $totalPenyerapan;

        $anggaran->update($updates);
    }

    /**
     * Propagate nilai dari Akun ke SubKomponen ke RO
     * Selalu aggregate dari child, bukan increment/decrement
     */
    public function propagateKeParent(Anggaran $anggaran): void
    {
        $bulanFields = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        // ===== Update SubKomponen =====
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

        // ===== Update RO =====
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

    /**
     * Update pagu anggaran dari revisi — update akun lalu propagate
     */
    public function updatePaguFromRevisi(Anggaran $anggaran, float $paguBaru): void
    {
        $selisih = $paguBaru - $anggaran->pagu_anggaran;

        $anggaran->update([
            'pagu_anggaran' => $paguBaru,
            'sisa'          => $anggaran->sisa + $selisih,
        ]);

        $this->propagateKeParent($anggaran);
    }

    /**
     * Sync anggaran dari SPP berdasarkan COA string
     * Dipanggil setelah create/update/delete SPP
     */
    public function syncFromSPP(string $coa): void
    {
        try {
            $anggaran = Anggaran::whereNotNull('kode_akun')
                ->whereRaw("CONCAT(kegiatan, kro, ro, kode_akun) = ?", [$coa])
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

    /**
     * Recalculate semua anggaran dari scratch (untuk repair/maintenance)
     */
    public function recalculateAll(): array
    {
        $stats = ['akun' => 0, 'subkomp' => 0, 'ro' => 0];

        DB::transaction(function () use (&$stats) {
            // 1. Recalculate semua level Akun dari SPP
            $akunList = Anggaran::whereNotNull('kode_akun')->get();
            foreach ($akunList as $akun) {
                $this->recalculateAkun($akun);
                $stats['akun']++;
            }

            // 2. Aggregate SubKomponen dari Akun
            $subkompList = Anggaran::whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->get();

            $bulanFields = [
                'januari', 'februari', 'maret', 'april', 'mei', 'juni',
                'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
            ];

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

            // 3. Aggregate RO dari SubKomponen
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
