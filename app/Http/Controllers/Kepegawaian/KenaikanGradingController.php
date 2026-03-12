<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class KenaikanGradingController extends Controller
{
    // ──────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────
    public function index(Request $request)
    {
        $tahun  = (int) $request->input('tahun', date('Y'));
        $bagian = $request->input('bagian', '');
        $search = $request->input('search', '');
        $isAjax = $request->ajax() || $request->input('ajax');

        // Semua rekomendasi (di-cache per tahun)
        $semuaRekomendasi = $this->getRekomendasi($tahun);

        // Daftar bagian untuk dropdown (dari data aktif, bukan hanya yang eligible)
        $bagianList = Pegawai::where('status', 'AKTIF')
            ->whereNotNull('bagian')
            ->distinct()
            ->orderBy('bagian')
            ->pluck('bagian');

        // Filter
        $filtered = $this->applyFilters($semuaRekomendasi, $bagian, $search);

        // Bangun payload
        $rows      = $this->buildRows($filtered);
        $stats     = $this->buildStats($filtered, $tahun);
        $perBagian = $this->buildPerBagian($filtered);

        // Response AJAX — hanya JSON
        if ($isAjax) {
            return response()->json([
                'rows'      => $rows,
                'stats'     => $stats,
                'perBagian' => $perBagian,
            ]);
        }

        // Response normal — kirim ke view
        return view('kepegawaian.grading.index', [
            'tahun'          => $tahun,
            'bagianList'     => $bagianList,
            'initialRows'    => $rows,
            'initialStats'   => $stats,
            'initialPerBagian' => $perBagian,
        ]);
    }

    // ──────────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────────
    public function show(Pegawai $pegawai)
    {
        $rekomendasi = $this->hitungRekomendasi($pegawai, (int) date('Y'));
        return view('kepegawaian.grading.show', compact('pegawai', 'rekomendasi'));
    }

    // ──────────────────────────────────────────────
    // PRIVATE — ambil & cache rekomendasi
    // ──────────────────────────────────────────────
    private function getRekomendasi(int $tahun)
    {
        return Cache::remember("rekomendasi_grading_{$tahun}", 300, function () use ($tahun) {
            return Pegawai::where('status', 'AKTIF')
                ->whereNotNull('grading')
                ->get()
                ->map(function ($pegawai) use ($tahun) {
                    $rek = $this->hitungRekomendasi($pegawai, $tahun);
                    if ($rek['eligible']) {
                        $pegawai->rekomendasi = $rek;
                        return $pegawai;
                    }
                    return null;
                })
                ->filter()
                ->values();
        });
    }

    // ──────────────────────────────────────────────
    // PRIVATE — filter collection
    // ──────────────────────────────────────────────
    private function applyFilters($collection, string $bagian, string $search)
    {
        return $collection
            ->when($bagian, fn($col) => $col->where('bagian', $bagian))
            ->when($search, fn($col) => $col->filter(
                fn($p) => str_contains(mb_strtolower($p->nama), mb_strtolower($search))
                       || str_contains($p->nip ?? '', $search)
            ))
            ->values();
    }

    // ──────────────────────────────────────────────
    // PRIVATE — build rows array untuk JSON / Alpine
    // ──────────────────────────────────────────────
    private function buildRows($collection): array
    {
        return $collection->map(function (Pegawai $p) {
            $nama = $p->nama ?? '';
            $words = explode(' ', trim($nama));
            $initials = collect($words)
                ->filter(fn($w) => strlen($w) > 0)
                ->map(fn($w) => strtoupper($w[0]))
                ->take(2)
                ->implode('');

            return [
                'id'               => $p->id,
                'nama'             => $nama,
                'nip'              => $p->nip ?? '',
                'jabatan'          => $p->jabatan ?? '',
                'bagian'           => $p->bagian ?? '',
                'masa_kerja_tahun' => $p->masa_kerja_tahun ?? 0,
                'grading_sekarang' => $p->rekomendasi['grading_sekarang'],
                'grading_baru'     => $p->rekomendasi['grading_baru'],
                'alasan'           => $p->rekomendasi['alasan'],
                'initials'         => $initials ?: '??',
                'url_show'         => route('kepegawaian.grading.show', $p->id),
            ];
        })->values()->toArray();
    }

    // ──────────────────────────────────────────────
    // PRIVATE — build stats
    // ──────────────────────────────────────────────
    private function buildStats($collection, int $tahun): array
    {
        $totalAktif = Cache::remember('total_pegawai_aktif', 60, fn() =>
            Pegawai::where('status', 'AKTIF')->count()
        );

        $naik1 = $collection->filter(
            fn($p) => ($p->rekomendasi['grading_baru'] - $p->rekomendasi['grading_sekarang']) === 1
        )->count();

        $avgMasaKerja = $collection->avg('masa_kerja_tahun');
        $maxGrade     = $collection->max(fn($p) => $p->rekomendasi['grading_baru']);

        return [
            'total'        => $collection->count(),
            'totalAktif'   => $totalAktif,
            'naik1'        => $naik1,
            'avgMasaKerja' => $avgMasaKerja ? (int) round($avgMasaKerja) : 0,
            'maxGrade'     => $maxGrade ?? '—',
        ];
    }

    // ──────────────────────────────────────────────
    // PRIVATE — build per-bagian untuk progress bar
    // ──────────────────────────────────────────────
    private function buildPerBagian($collection): array
    {
        $grouped = $collection->groupBy('bagian');
        $max     = $grouped->map->count()->max() ?: 1;

        return $grouped
            ->map(fn($items, $bagian) => [
                'bagian' => $bagian,
                'count'  => $items->count(),
                'pct'    => (int) round($items->count() / $max * 100),
            ])
            ->sortByDesc('count')
            ->values()
            ->toArray();
    }

    // ──────────────────────────────────────────────
    // PRIVATE — hitung rekomendasi per pegawai
    // ──────────────────────────────────────────────
    private function hitungRekomendasi(Pegawai $pegawai, int $tahun): array
    {
        $masaKerja       = $pegawai->masa_kerja_tahun ?? 0;
        $gradingSekarang = $pegawai->grading ?? 0;
        $pendidikan      = $pegawai->pendidikan ?? '';

        $eligible  = false;
        $alasan    = [];
        $gradingBaru = $gradingSekarang;

        // Kriteria 1: masa kerja
        if ($masaKerja >= 4 && $gradingSekarang < 16) {
            $eligible    = true;
            $gradingBaru = min($gradingSekarang + 1, 16);
            $alasan[]    = "Masa kerja sudah {$masaKerja} tahun (min. 4 tahun)";
        }

        // Kriteria 2: pendidikan S2/S3
        if (in_array($pendidikan, ['S2', 'S3']) && $gradingSekarang < 15) {
            $eligible = true;
            $alasan[] = "Memiliki pendidikan {$pendidikan}";
        }

        // Kriteria 3: jabatan eselon
        if ($pegawai->eselon && $gradingSekarang < 15) {
            $eligible = true;
            $alasan[] = "Memiliki jabatan eselon {$pegawai->eselon}";
        }

        return [
            'eligible'         => $eligible,
            'grading_sekarang' => $gradingSekarang,
            'grading_baru'     => $gradingBaru,
            'alasan'           => $alasan,
            'tahun'            => $tahun,
        ];
    }
}
