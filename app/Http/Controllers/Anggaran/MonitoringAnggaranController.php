<?php
// app/Http/Controllers/Anggaran/MonitoringAnggaranController.php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\DokumenCapaian;
use App\Models\SPP;
use App\Models\UsulanPenarikan;
use App\Services\AnggaranService;
use Illuminate\Http\Request;

class MonitoringAnggaranController extends Controller
{
    private array $roNames = [
        'Z06' => 'Rencana Kebutuhan BMN dan Pengelolaannya',
        '403' => 'Layanan Pengadaan',
        '405' => 'Kerumahtanggaan',
        '994' => 'Layanan Perkantoran',
    ];

    public function __construct(private AnggaranService $anggaranService) {}

    public function index(Request $request)
    {
        $ro          = $request->get('ro', 'all');
        $subkomponen = $request->get('subkomponen', 'all');
        $bulan       = $request->get('bulan', 'all');

        // ===== Data Anggaran =====
        $query = Anggaran::query();
        if ($ro !== 'all')          $query->where('ro', $ro);
        if ($subkomponen !== 'all') $query->where('kode_subkomponen', $subkomponen);

        $anggarans   = $query->orderBy('ro')->orderBy('kode_subkomponen')->get();
        $groupedData = $anggarans->groupBy('ro');

        // ===== Totals dari level RO saja (hindari double counting) =====
        $roLevelQuery = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun');
        if ($ro !== 'all') $roLevelQuery->where('ro', $ro);

        $roLevelData = $roLevelQuery->get();

        $totalPagu        = $roLevelData->sum('pagu_anggaran');
        $totalRealisasi   = $roLevelData->sum('total_penyerapan');
        $totalSisa        = $roLevelData->sum('sisa');
        $totalOutstanding = $roLevelData->sum('tagihan_outstanding');

        // ===== Data SPP Terintegrasi =====
        $sppQuery = SPP::query();
        if ($ro !== 'all')   $sppQuery->where('ro', $ro);
        if ($bulan !== 'all') $sppQuery->where('bulan', $bulan);

        $recentSPP = (clone $sppQuery)->orderBy('tgl_spp', 'desc')->limit(10)->get();
        $sppStats  = [
            'total'       => (clone $sppQuery)->count(),
            'sudah_sp2d'  => (clone $sppQuery)->where('status', 'Tagihan Telah SP2D')->count(),
            'belum_sp2d'  => (clone $sppQuery)->where('status', 'Tagihan Belum SP2D')->count(),
            'nilai_sp2d'  => (clone $sppQuery)->where('status', 'Tagihan Telah SP2D')->sum('netto'),
        ];

        // ===== Usulan Penarikan Pending =====
        $usulanQuery = UsulanPenarikan::where('status', 'pending');
        if ($ro !== 'all') $usulanQuery->where('ro', $ro);

        $usulanPending = $usulanQuery->with('user')->orderBy('created_at', 'desc')->limit(5)->get();
        $totalUsulanPending = UsulanPenarikan::where('status', 'pending')
            ->when($ro !== 'all', fn($q) => $q->where('ro', $ro))
            ->sum('nilai_usulan');

        // ===== Dokumen Capaian per Bulan =====
        $dokumenQuery = DokumenCapaian::query();
        if ($ro !== 'all')   $dokumenQuery->where('ro', $ro);
        if ($bulan !== 'all') $dokumenQuery->where('bulan', $bulan);

        $dokumenCount = $dokumenQuery->count();

        // ===== Filter Lists =====
        $roList = collect($this->roNames)->map(fn($name, $code) => [
            'code' => $code,
            'name' => $name,
        ])->values();

        $subkomponenList = Anggaran::whereNotNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->when($ro !== 'all', fn($q) => $q->where('ro', $ro))
            ->distinct()
            ->get(['kode_subkomponen', 'program_kegiatan'])
            ->mapWithKeys(fn($item) => [$item->kode_subkomponen => $item->program_kegiatan]);

        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        // ===== Realisasi per Bulan (untuk chart) =====
        $bulanFields = $bulanList;
        $chartLabels = array_map('ucfirst', $bulanFields);
        $chartData   = [];

        foreach ($bulanFields as $field) {
            $val = 0;
            if ($ro !== 'all') {
                $val = Anggaran::where('ro', $ro)
                    ->whereNull('kode_subkomponen')
                    ->whereNull('kode_akun')
                    ->sum($field);
            } else {
                $val = Anggaran::whereNull('kode_subkomponen')
                    ->whereNull('kode_akun')
                    ->sum($field);
            }
            $chartData[] = round($val, 2);
        }

        return view('anggaran.monitoring.index', compact(
            'groupedData', 'roList', 'subkomponenList', 'bulanList',
            'totalPagu', 'totalRealisasi', 'totalSisa', 'totalOutstanding',
            'ro', 'subkomponen', 'bulan',
            'recentSPP', 'sppStats',
            'usulanPending', 'totalUsulanPending',
            'dokumenCount',
            'chartLabels', 'chartData'
        ));
    }

    /**
     * Recalculate semua anggaran (endpoint untuk admin)
     */
    public function recalculate(Request $request)
    {
        try {
            $stats = $this->anggaranService->recalculateAll();

            return redirect()->route('anggaran.monitoring.index')
                ->with('success',
                    "Recalculate selesai: {$stats['akun']} akun, " .
                    "{$stats['subkomp']} subkomponen, {$stats['ro']} RO diperbarui."
                );

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal recalculate: ' . $e->getMessage());
        }
    }
}
