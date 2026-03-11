<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\DokumenCapaian;
use App\Models\SPP;
use App\Models\UsulanPenarikan;
use App\Services\AnggaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitoringAnggaranController extends Controller
{
    private array $roNames = [
        'Z06' => 'Rencana Kebutuhan BMN dan Pengelolaannya',
        '403' => 'Layanan Pengadaan',
        '405' => 'Kerumahtanggaan',
        '994' => 'Layanan Perkantoran',
    ];

    private array $bulanFields = [
        'januari', 'februari', 'maret', 'april', 'mei', 'juni',
        'juli', 'agustus', 'september', 'oktober', 'november', 'desember',
    ];

    public function __construct(private AnggaranService $anggaranService) {}

    // ═══════════════════════════════════════════════════════
    // HALAMAN UTAMA
    // ═══════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $ro          = $request->get('ro', 'all');
        $subkomponen = $request->get('subkomponen', 'all');
        $bulan       = $request->get('bulan', 'all');

        // ── Anggaran ──────────────────────────────────────────
        $query = Anggaran::query();
        if ($ro !== 'all')          $query->where('ro', $ro);
        if ($subkomponen !== 'all') $query->where('kode_subkomponen', $subkomponen);

        $anggarans   = $query->orderBy('ro')->orderBy('kode_subkomponen')->get();
        $groupedData = $anggarans->groupBy('ro');

        // ── Totals (dari level RO saja, hindari double counting) ─
        $roLevelQuery = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun');
        if ($ro !== 'all') $roLevelQuery->where('ro', $ro);
        $roLevelData = $roLevelQuery->get();

        $totalPagu        = $roLevelData->sum('pagu_anggaran');
        $totalRealisasi   = $roLevelData->sum('total_penyerapan');
        $totalSisa        = $roLevelData->sum('sisa');
        $totalOutstanding = $roLevelData->sum('tagihan_outstanding');

        // ── SPP ───────────────────────────────────────────────
        $sppQuery = SPP::query();
        if ($ro !== 'all')    $sppQuery->where('ro', $ro);
        if ($bulan !== 'all') $sppQuery->where('bulan', $bulan);

        $recentSPP = (clone $sppQuery)->orderBy('tgl_spp', 'desc')->limit(10)->get();
        $sppStats  = [
            'total'      => (clone $sppQuery)->count(),
            'sudah_sp2d' => (clone $sppQuery)->where('status', 'Tagihan Telah SP2D')->count(),
            'belum_sp2d' => (clone $sppQuery)->where('status', 'Tagihan Belum SP2D')->count(),
            'nilai_sp2d' => (float) (clone $sppQuery)->where('status', 'Tagihan Telah SP2D')->sum('netto'),
        ];

        // ── Usulan Penarikan ──────────────────────────────────
        $usulanQuery = UsulanPenarikan::where('status', 'pending');
        if ($ro !== 'all') $usulanQuery->where('ro', $ro);

        $usulanPending      = $usulanQuery->with('user')->orderBy('created_at', 'desc')->limit(5)->get();
        $totalUsulanPending = (float) UsulanPenarikan::where('status', 'pending')
            ->when($ro !== 'all', fn($q) => $q->where('ro', $ro))
            ->sum('nilai_usulan');

        // ── Dokumen Capaian ───────────────────────────────────
        $dokumenQuery = DokumenCapaian::query();
        if ($ro !== 'all')    $dokumenQuery->where('ro', $ro);
        if ($bulan !== 'all') $dokumenQuery->where('bulan', $bulan);
        $dokumenCount = $dokumenQuery->count();

        // ── Filter Lists ──────────────────────────────────────
        $roList = collect($this->roNames)->map(fn($name, $code) => [
            'code' => $code,
            'name' => $name,
        ])->values();

        $subkomponenList = Anggaran::whereNotNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->when($ro !== 'all', fn($q) => $q->where('ro', $ro))
            ->distinct()
            ->get(['kode_subkomponen', 'program_kegiatan'])
            ->mapWithKeys(fn($item) => [
                $item->kode_subkomponen => $item->program_kegiatan,
            ]);

        $bulanList = $this->bulanFields;

        // ── Chart Data ────────────────────────────────────────
        $chartLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $chartData   = [];
        foreach ($this->bulanFields as $field) {
            $q = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun');
            if ($ro !== 'all') $q->where('ro', $ro);
            $chartData[] = round((float) $q->sum($field), 2);
        }

        return view('anggaran.monitoring.index', compact(
            'groupedData', 'roList', 'subkomponenList', 'bulanList',
            'totalPagu', 'totalRealisasi', 'totalSisa', 'totalOutstanding',
            'ro', 'subkomponen', 'bulan',
            'recentSPP', 'sppStats',
            'usulanPending', 'totalUsulanPending',
            'dokumenCount',
            'chartLabels', 'chartData',
        ));
    }

    // ═══════════════════════════════════════════════════════
    // AJAX — DATA UNTUK FILTER TANPA RELOAD
    // ═══════════════════════════════════════════════════════
    public function data(Request $request): JsonResponse
    {
        $ro          = $request->get('ro', 'all');
        $subkomponen = $request->get('subkomponen', 'all');
        $bulan       = $request->get('bulan', 'all');

        // ── Anggaran ──────────────────────────────────────────
        $query = Anggaran::query();
        if ($ro !== 'all')          $query->where('ro', $ro);
        if ($subkomponen !== 'all') $query->where('kode_subkomponen', $subkomponen);

        $anggarans   = $query->orderBy('ro')->orderBy('kode_subkomponen')->get();
        $groupedData = $anggarans->groupBy('ro');

        // ── Totals ────────────────────────────────────────────
        $roLevelQuery = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun');
        if ($ro !== 'all') $roLevelQuery->where('ro', $ro);
        $roLevelData = $roLevelQuery->get();

        // ── SPP ───────────────────────────────────────────────
        $sppQuery = SPP::query();
        if ($ro !== 'all')    $sppQuery->where('ro', $ro);
        if ($bulan !== 'all') $sppQuery->where('bulan', $bulan);

        $recentSPP = (clone $sppQuery)->orderBy('tgl_spp', 'desc')->limit(10)->get();

        // ── Usulan ────────────────────────────────────────────
        $usulanQuery = UsulanPenarikan::where('status', 'pending');
        if ($ro !== 'all') $usulanQuery->where('ro', $ro);
        $usulanPending = $usulanQuery->with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        // ── Dokumen ───────────────────────────────────────────
        $dokumenQuery = DokumenCapaian::query();
        if ($ro !== 'all')    $dokumenQuery->where('ro', $ro);
        if ($bulan !== 'all') $dokumenQuery->where('bulan', $bulan);

        // ── Subkomponen list (update dinamis saat RO berubah) ─
        $subkomponenList = Anggaran::whereNotNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->when($ro !== 'all', fn($q) => $q->where('ro', $ro))
            ->distinct()
            ->get(['kode_subkomponen', 'program_kegiatan'])
            ->mapWithKeys(fn($item) => [
                $item->kode_subkomponen => $item->program_kegiatan,
            ]);

        // ── Chart Data ────────────────────────────────────────
        $chartData = [];
        foreach ($this->bulanFields as $field) {
            $q = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun');
            if ($ro !== 'all') $q->where('ro', $ro);
            $chartData[] = round((float) $q->sum($field), 2);
        }

        // ── Serialize groupedData ─────────────────────────────
        $serializedGrouped = $groupedData->map(fn($roData) =>
            $roData->map(fn($item) => [
                'ro'                  => $item->ro,
                'kode_subkomponen'    => $item->kode_subkomponen,
                'kode_akun'           => $item->kode_akun,
                'program_kegiatan'    => $item->program_kegiatan,
                'pagu_anggaran'       => (float) $item->pagu_anggaran,
                'tagihan_outstanding' => (float) $item->tagihan_outstanding,
                'total_penyerapan'    => (float) $item->total_penyerapan,
                'sisa'                => (float) $item->sisa,
                'januari'             => (float) $item->januari,
                'februari'            => (float) $item->februari,
                'maret'               => (float) $item->maret,
                'april'               => (float) $item->april,
                'mei'                 => (float) $item->mei,
                'juni'                => (float) $item->juni,
                'juli'                => (float) $item->juli,
                'agustus'             => (float) $item->agustus,
                'september'           => (float) $item->september,
                'oktober'             => (float) $item->oktober,
                'november'            => (float) $item->november,
                'desember'            => (float) $item->desember,
            ])->values()
        );

        return response()->json([
            'totals' => [
                'pagu'        => (float) $roLevelData->sum('pagu_anggaran'),
                'realisasi'   => (float) $roLevelData->sum('total_penyerapan'),
                'sisa'        => (float) $roLevelData->sum('sisa'),
                'outstanding' => (float) $roLevelData->sum('tagihan_outstanding'),
            ],
            'sppStats' => [
                'total'      => (clone $sppQuery)->count(),
                'sudah_sp2d' => (clone $sppQuery)->where('status', 'Tagihan Telah SP2D')->count(),
                'belum_sp2d' => (clone $sppQuery)->where('status', 'Tagihan Belum SP2D')->count(),
                'nilai_sp2d' => (float) (clone $sppQuery)->where('status', 'Tagihan Telah SP2D')->sum('netto'),
            ],
            'recentSPP' => $recentSPP->map(fn($s) => [
                'id'         => $s->id,
                'no_spp'     => $s->no_spp,
                'tgl_spp'    => $s->tgl_spp?->format('d/m/Y'),
                'uraian_spp' => $s->uraian_spp,
                'ro'         => $s->ro,
                'netto'      => (float) $s->netto,
                'status'     => $s->status,
            ]),
            'usulanPending' => $usulanPending->map(fn($u) => [
                'ro'           => $u->ro,
                'bulan'        => $u->bulan,
                'nilai_usulan' => (float) $u->nilai_usulan,
                'user'         => ['nama' => $u->user->nama ?? '-'],
            ]),
            'totalUsulanPending' => (float) UsulanPenarikan::where('status', 'pending')
                ->when($ro !== 'all', fn($q) => $q->where('ro', $ro))
                ->sum('nilai_usulan'),
            'dokumenCount'    => $dokumenQuery->count(),
            'chartData'       => $chartData,
            'subkomponenList' => $subkomponenList,
            'groupedData'     => $serializedGrouped,
            'roNames'         => $this->roNames,
        ]);
    }

    // ═══════════════════════════════════════════════════════
    // EXPORT EXCEL
    // ═══════════════════════════════════════════════════════
    public function export(Request $request)
    {
        $ro          = $request->get('ro', 'all');
        $subkomponen = $request->get('subkomponen', 'all');

        $query = Anggaran::query();
        if ($ro !== 'all')          $query->where('ro', $ro);
        if ($subkomponen !== 'all') $query->where('kode_subkomponen', $subkomponen);

        $data     = $query->orderBy('ro')->orderBy('kode_subkomponen')->orderBy('kode_akun')->get();
        $filename = 'monitoring-anggaran-' . now()->format('Ymd-His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MonitoringAnggaranExport($data, $ro),
            $filename
        );
    }

    // ═══════════════════════════════════════════════════════
    // RECALCULATE (Admin only)
    // ═══════════════════════════════════════════════════════
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
