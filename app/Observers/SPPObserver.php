<?php
// app/Observers/SPPObserver.php

namespace App\Observers;

use App\Models\Anggaran;
use App\Models\SPP;
use Illuminate\Support\Facades\Log;

class SPPObserver
{
    /**
     * Dipanggil setelah SPP dibuat
     */
    public function created(SPP $spp): void
    {
        $this->syncAnggaran($spp);
    }

    /**
     * Dipanggil setelah SPP diupdate
     */
    public function updated(SPP $spp): void
    {
        // Jika COA berubah, sync kedua COA (lama & baru)
        if ($spp->wasChanged('coa')) {
            $oldCoa = $spp->getOriginal('coa');
            $this->syncAnggaranByCoa($oldCoa);
        }

        $this->syncAnggaran($spp);
    }

    /**
     * Dipanggil setelah SPP dihapus (soft delete)
     */
    public function deleted(SPP $spp): void
    {
        $this->syncAnggaran($spp);
    }

    /**
     * Dipanggil setelah SPP di-restore
     */
    public function restored(SPP $spp): void
    {
        $this->syncAnggaran($spp);
    }

    private function syncAnggaran(SPP $spp): void
    {
        $this->syncAnggaranByCoa($spp->coa);
    }

    private function syncAnggaranByCoa(string $coa): void
    {
        try {
            // Cari anggaran level Akun berdasarkan COA
            $anggaran = Anggaran::whereNotNull('kode_akun')
                ->whereRaw("CONCAT(kegiatan, kro, ro, kode_akun) = ?", [$coa])
                ->first();

            if (!$anggaran) {
                Log::warning("SPPObserver: Anggaran tidak ditemukan untuk COA {$coa}");
                return;
            }

            // Recalculate dari SPP
            $anggaran->recalculateFromSPP();

            // Propagate ke parent (SubKomponen & RO)
            $this->propagateToParents($anggaran);

        } catch (\Exception $e) {
            Log::error("SPPObserver error: " . $e->getMessage(), [
                'coa' => $coa,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function propagateToParents(Anggaran $anggaran): void
    {
        // Update SubKomponen
        $subkomp = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->where('kode_subkomponen', $anggaran->kode_subkomponen)
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp) {
            $totals = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->where('kode_subkomponen', $anggaran->kode_subkomponen)
                ->whereNotNull('kode_akun')
                ->selectRaw('SUM(pagu_anggaran) as pagu, SUM(total_penyerapan) as realisasi, SUM(tagihan_outstanding) as outstanding')
                ->first();

            $subkomp->update([
                'pagu_anggaran'       => $totals->pagu ?? 0,
                'total_penyerapan'    => $totals->realisasi ?? 0,
                'tagihan_outstanding' => $totals->outstanding ?? 0,
                'sisa'                => ($totals->pagu ?? 0) - ($totals->realisasi ?? 0),
            ]);
        }

        // Update RO
        $ro = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->whereNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->first();

        if ($ro) {
            $totals = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->selectRaw('SUM(pagu_anggaran) as pagu, SUM(total_penyerapan) as realisasi, SUM(tagihan_outstanding) as outstanding')
                ->first();

            $ro->update([
                'pagu_anggaran'       => $totals->pagu ?? 0,
                'total_penyerapan'    => $totals->realisasi ?? 0,
                'tagihan_outstanding' => $totals->outstanding ?? 0,
                'sisa'                => ($totals->pagu ?? 0) - ($totals->realisasi ?? 0),
            ]);
        }
    }
}
