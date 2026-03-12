<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KenaikanGradingController extends Controller
{
    private const GRADING_MAX = [
        'eselon_i'            => 27,
        'eselon_ii'           => 22,
        'eselon_iii'          => 18,
        'eselon_iv'           => 16,
        'eselon_v'            => 13,
        'fungsional_utama'    => 17,
        'fungsional_madya'    => 14,
        'fungsional_muda'     => 11,
        'fungsional_pertama'  => 9,
        'fungsional_penyelia' => 11,
        'fungsional_mahir'    => 9,
        'fungsional_terampil' => 8,
        'fungsional_pemula'   => 7,
        'pelaksana'           => 12,
        'default'             => 12,
    ];

    public function index(Request $request)
{
    $tahun  = (int) $request->input('tahun', date('Y'));
    $bagian = (string) ($request->input('bagian') ?? '');  // ← fix null
    $search = (string) ($request->input('search') ?? '');  // ← fix null
    $isAjax = $request->ajax() || $request->input('ajax');

    $semuaRekomendasi = $this->getRekomendasi($tahun);

    $bagianList = Pegawai::where('status', 'AKTIF')
        ->whereNotNull('bagian')
        ->distinct()
        ->orderBy('bagian')
        ->pluck('bagian');

    $filtered  = $this->applyFilters($semuaRekomendasi, $bagian, $search);
    $rows      = $this->buildRows($filtered);
    $stats     = $this->buildStats($filtered, $tahun);
    $perBagian = $this->buildPerBagian($filtered);

    if ($isAjax) {
        return response()->json([
            'rows'      => $rows,
            'stats'     => $stats,
            'perBagian' => $perBagian,
        ]);
    }

    return view('kepegawaian.grading.index', [
        'tahun'            => $tahun,
        'bagianList'       => $bagianList,
        'initialRows'      => $rows,
        'initialStats'     => $stats,
        'initialPerBagian' => $perBagian,
    ]);
}


    public function show(Pegawai $pegawai)
    {
        $rekomendasi = $this->hitungRekomendasi($pegawai, (int) date('Y'));
        return view('kepegawaian.grading.show', compact('pegawai', 'rekomendasi'));
    }

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

    private function applyFilters($collection, string $bagian, string $search)
{
    return $collection
        ->when($bagian !== '', fn($col) => $col->where('bagian', $bagian))  // ← fix empty check
        ->when($search !== '', fn($col) => $col->filter(                    // ← fix empty check
            fn($p) => str_contains(mb_strtolower($p->nama ?? ''), mb_strtolower($search))
                   || str_contains($p->nip ?? '', $search)
        ))
        ->values();
}


    private function buildRows($collection): array
    {
        return $collection->map(function (Pegawai $p) {
            $words    = explode(' ', trim($p->nama ?? ''));
            $initials = collect($words)
                ->filter(fn($w) => strlen($w) > 0)
                ->map(fn($w) => strtoupper($w[0]))
                ->take(2)
                ->implode('');

            return [
                'id'               => $p->id,
                'nama'             => $p->nama ?? '',
                'nip'              => $p->nip ?? '',
                'jabatan'          => $p->jabatan ?? '',
                'bagian'           => $p->bagian ?? '',
                'masa_kerja_tahun' => $p->masa_kerja_tahun ?? 0,
                'grading_sekarang' => $p->rekomendasi['grading_sekarang'],
                'grading_baru'     => $p->rekomendasi['grading_baru'],
                'grading_max'      => $p->rekomendasi['grading_max'],
                'level_jabatan'    => $p->rekomendasi['level_jabatan'],
                'alasan'           => $p->rekomendasi['alasan'],
                'catatan'          => $p->rekomendasi['catatan'],
                'initials'         => $initials ?: '??',
                'url_show'         => route('kepegawaian.grading.show', $p->id),
            ];
        })->values()->toArray();
    }

    private function buildStats($collection, int $tahun): array
    {
        $totalAktif = Cache::remember('total_pegawai_aktif', 60,
            fn() => Pegawai::where('status', 'AKTIF')->count()
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

    private function resolveLevel(Pegawai $pegawai): array
    {
        $eselon       = strtolower(trim($pegawai->eselon ?? ''));
        $jenisJabatan = strtolower(trim($pegawai->jenis_jabatan ?? ''));
        $namaJabatan  = strtolower(trim($pegawai->nama_jabatan ?? $pegawai->jabatan ?? ''));

        // Deteksi Eselon
        if ($eselon) {
            if (preg_match('/^i[ab]?$/i', $eselon) || $eselon === '1') {
                return ['key' => 'eselon_i', 'label' => 'Eselon I'];
            }
            if (preg_match('/^ii[ab]?$/i', $eselon) || $eselon === '2') {
                return ['key' => 'eselon_ii', 'label' => 'Eselon II'];
            }
            if (preg_match('/^iii[ab]?$/i', $eselon) || $eselon === '3') {
                return ['key' => 'eselon_iii', 'label' => 'Eselon III'];
            }
            if (preg_match('/^iv[ab]?$/i', $eselon) || $eselon === '4') {
                return ['key' => 'eselon_iv', 'label' => 'Eselon IV'];
            }
            if (preg_match('/^v[ab]?$/i', $eselon) || $eselon === '5') {
                return ['key' => 'eselon_v', 'label' => 'Eselon V'];
            }
        }

        // Deteksi Fungsional
        if (str_contains($jenisJabatan, 'fungsional') || str_contains($namaJabatan, 'fungsional')) {
            foreach ([
                'utama'    => 'fungsional_utama',
                'madya'    => 'fungsional_madya',
                'muda'     => 'fungsional_muda',
                'pertama'  => 'fungsional_pertama',
                'penyelia' => 'fungsional_penyelia',
                'mahir'    => 'fungsional_mahir',
                'terampil' => 'fungsional_terampil',
                'pemula'   => 'fungsional_pemula',
            ] as $keyword => $levelKey) {
                if (str_contains($namaJabatan, $keyword) || str_contains($jenisJabatan, $keyword)) {
                    return ['key' => $levelKey, 'label' => 'Fungsional ' . ucfirst($keyword)];
                }
            }
            return ['key' => 'fungsional_pertama', 'label' => 'Fungsional'];
        }

        // Pelaksana / default
        if (str_contains($jenisJabatan, 'pelaksana') || str_contains($namaJabatan, 'pelaksana')) {
            return ['key' => 'pelaksana', 'label' => 'Pelaksana'];
        }

        return ['key' => 'default', 'label' => 'Pelaksana'];
    }

    private function hitungRekomendasi(Pegawai $pegawai, int $tahun): array
    {
        $masaKerja       = (int) ($pegawai->masa_kerja_tahun ?? 0);
        $gradingSekarang = (int) ($pegawai->grading ?? 0);
        $pendidikan      = strtoupper(trim($pegawai->pendidikan ?? ''));

        $level      = $this->resolveLevel($pegawai);
        $gradingMax = self::GRADING_MAX[$level['key']] ?? self::GRADING_MAX['default'];

        $eligible    = false;
        $alasan      = [];
        $catatan     = [];
        $gradingBaru = $gradingSekarang;

        // Sudah di batas maksimal
        if ($gradingSekarang >= $gradingMax) {
            return [
                'eligible'         => false,
                'grading_sekarang' => $gradingSekarang,
                'grading_baru'     => $gradingSekarang,
                'grading_max'      => $gradingMax,
                'level_jabatan'    => $level['label'],
                'alasan'           => [],
                'catatan'          => [
                    "Grading sudah mencapai batas maksimal untuk level {$level['label']} (G{$gradingMax})"
                ],
                'tahun' => $tahun,
            ];
        }

        // Kriteria 1: Masa kerja minimal 4 tahun
        if ($masaKerja >= 4) {
            $eligible    = true;
            $gradingBaru = min($gradingSekarang + 1, $gradingMax);
            $alasan[]    = "Masa kerja {$masaKerja} tahun memenuhi syarat kenaikan reguler (min. 4 tahun)";
        } else {
            $sisaTahun = 4 - $masaKerja;
            $catatan[] = "Masa kerja {$masaKerja} tahun, perlu {$sisaTahun} tahun lagi untuk kenaikan reguler";
        }

        // Kriteria 2: Pendidikan S2/S3
        if (in_array($pendidikan, ['S2', 'S3'])) {
            if (!$eligible) {
                $eligible    = true;
                $gradingBaru = min($gradingSekarang + 1, $gradingMax);
            }
            $alasan[] = "Pendidikan {$pendidikan} memberikan pertimbangan akselerasi";
        }

        // Catatan jika sudah di batas setelah kenaikan
        if ($eligible && $gradingBaru >= $gradingMax) {
            $catatan[] = "Grade {$gradingMax} adalah batas maksimal untuk level {$level['label']}";
        }

        return [
            'eligible'         => $eligible,
            'grading_sekarang' => $gradingSekarang,
            'grading_baru'     => $gradingBaru,
            'grading_max'      => $gradingMax,
            'level_jabatan'    => $level['label'],
            'alasan'           => $alasan,
            'catatan'          => $catatan,
            'tahun'            => $tahun,
        ];
    }
}
