<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use Illuminate\Http\Request;

class MonitoringAnggaranController extends Controller
{
    public function index(Request $request)
    {
        $ro = $request->get('ro', 'all');
        $subkomponen = $request->get('subkomponen', 'all');

        $query = Anggaran::query();

        if ($ro !== 'all') {
            $query->where('ro', $ro);
        }

        if ($subkomponen !== 'all') {
            $query->where('kode_subkomponen', $subkomponen);
        }

        $anggarans = $query->orderBy('ro')->orderBy('kode_subkomponen')->get();

        // Group by RO
        $groupedData = $anggarans->groupBy('ro');

        // Get RO list
        $roList = Anggaran::select('ro', 'program_kegiatan')
            ->whereNotNull('kode_subkomponen')
            ->where('kode_subkomponen', '!=', '')
            ->distinct()
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->ro => $this->getRoName($item->ro)];
            });

        // Get subkomponen list
        $subkomponenList = Anggaran::select('kode_subkomponen', 'program_kegiatan')
            ->whereNotNull('kode_subkomponen')
            ->where('kode_subkomponen', '!=', '')
            ->distinct()
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->kode_subkomponen => $item->program_kegiatan];
            });

        // PERBAIKAN: Calculate totals HANYA dari level RO (parent level)
        // whereNull('kode_subkomponen') = Level RO
        // whereNull('kode_akun') = Bukan level Akun
        $roLevelQuery = Anggaran::query()
            ->whereNull('kode_subkomponen')  // Level RO saja (parent tertinggi)
            ->whereNull('kode_akun');         // Pastikan bukan level Akun

        if ($ro !== 'all') {
            $roLevelQuery->where('ro', $ro);
        }

        $roLevelData = $roLevelQuery->get();

        // Hitung total dari level RO saja
        $totalPagu = $roLevelData->sum('pagu_anggaran');
        $totalRealisasi = $roLevelData->sum('total_penyerapan');
        $totalSisa = $roLevelData->sum('sisa');
        $totalOutstanding = $roLevelData->sum('tagihan_outstanding');

        return view('anggaran.monitoring.index', compact(
            'groupedData',
            'roList',
            'subkomponenList',
            'totalPagu',
            'totalRealisasi',
            'totalSisa',
            'totalOutstanding',
            'ro',
            'subkomponen'
        ));
    }

    private function getRoName($ro)
    {
        $roNames = [
            'Z06' => 'Rencana Kebutuhan BMN dan Pengelolaannya',
            '403' => 'Layanan Pengadaan',
            '405' => 'Kerumahtanggaan',
            '994' => 'Layanan Perkantoran',
        ];

        return $roNames[$ro] ?? $ro;
    }
}
