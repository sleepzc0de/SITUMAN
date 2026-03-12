<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ProyeksiMutasiController extends Controller
{
    private const CACHE_TTL = 300;

    public function index(Request $request)
    {
        $tahun     = (int) $request->input('tahun', date('Y'));
        $bagian    = (string) ($request->input('bagian') ?? '');
        $prioritas = (string) ($request->input('prioritas') ?? '');
        $search    = (string) ($request->input('search') ?? '');

        $bagianList = Cache::remember('pegawai_bagian_list', 600, fn() =>
            Pegawai::where('status', 'AKTIF')
                ->whereNotNull('bagian')
                ->distinct()
                ->orderBy('bagian')
                ->pluck('bagian')
        );

        $proyeksi    = $this->getProyeksi($tahun, $bagian, $prioritas, $search);
        $statsBagian = $proyeksi->groupBy('bagian')->map(fn($g) => $g->count())->sortDesc();

        if ($request->ajax()) {
            return response()->json([
                'html'        => view('kepegawaian.mutasi._table',
                                    compact('proyeksi', 'tahun', 'search', 'bagian', 'prioritas'))->render(),
                'stats'       => view('kepegawaian.mutasi._stats',
                                    compact('proyeksi'))->render(),
                'statsBagian' => view('kepegawaian.mutasi._stats_bagian',
                                    compact('statsBagian', 'proyeksi'))->render(),
                'count'       => $proyeksi->count(),
            ]);
        }

        return view('kepegawaian.mutasi.index', compact(
            'proyeksi', 'tahun', 'bagian', 'prioritas',
            'search', 'bagianList', 'statsBagian'
        ));
    }

    private function getProyeksi(int $tahun, string $bagian, string $prioritas, string $search)
    {
        $cacheKey = "proyeksi_mutasi_{$tahun}_{$bagian}_{$prioritas}_{$search}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tahun, $bagian, $prioritas, $search) {
            $query = Pegawai::where('status', 'AKTIF');

            if ($bagian !== '') {
                $query->where('bagian', $bagian);
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            }

            return $query->get()
                ->map(function ($pegawai) use ($tahun) {
                    $analisis = $this->analisisMutasi($pegawai, $tahun);
                    if ($analisis['perlu_mutasi']) {
                        $pegawai->analisis_mutasi = $analisis;
                        return $pegawai;
                    }
                    return null;
                })
                ->filter()
                ->when($prioritas !== '', function ($col) use ($prioritas) {
                    return $col->filter(function ($p) use ($prioritas) {
                        $skor = $p->analisis_mutasi['prioritas'];
                        return match ($prioritas) {
                            'tinggi' => $skor >= 5,
                            'sedang' => $skor >= 3 && $skor < 5,
                            'rendah' => $skor < 3,
                            default  => true,
                        };
                    });
                })
                ->sortByDesc('analisis_mutasi.prioritas')
                ->values();
        });
    }

    private function analisisMutasi(Pegawai $pegawai, int $tahun): array
    {
        $perluMutasi = false;
        $alasan      = [];
        $prioritas   = 0;
        $lamaJabatan = null;

        // ── Masa jabatan via tmt_jabatan, fallback proyeksi_kp_1 ──
        $tmtSource = $pegawai->tmt_jabatan ?? null;
        if (!$tmtSource && $pegawai->proyeksi_kp_1) {
            try { $tmtSource = Carbon::parse($pegawai->proyeksi_kp_1); } catch (\Exception) {}
        }

        if ($tmtSource) {
            try {
                $tmt         = $tmtSource instanceof Carbon ? $tmtSource : Carbon::parse($tmtSource);
                $lamaJabatan = (int) $tmt->diffInMonths(now());

                if ($lamaJabatan >= 24) {
                    $perluMutasi = true;
                    $prioritas  += 3;
                    $alasan[]    = "Masa jabatan {$lamaJabatan} bulan (≥24 bulan)";
                } elseif ($lamaJabatan >= 18) {
                    $perluMutasi = true;
                    $prioritas  += 2;
                    $alasan[]    = "Mendekati batas masa jabatan ({$lamaJabatan} dari 24 bulan)";
                }
            } catch (\Exception) {}
        }

        // ── Mendekati pensiun ──
        if ($pegawai->tanggal_pensiun) {
            try {
                $bulanSampaiPensiun = (int) now()->diffInMonths(
                    Carbon::parse($pegawai->tanggal_pensiun), false
                );

                if ($bulanSampaiPensiun > 0 && $bulanSampaiPensiun <= 12) {
                    $perluMutasi = true;
                    $prioritas  += 5;
                    $alasan[]    = "Akan pensiun dalam {$bulanSampaiPensiun} bulan";
                } elseif ($bulanSampaiPensiun > 12 && $bulanSampaiPensiun <= 24) {
                    $perluMutasi = true;
                    $prioritas  += 2;
                    $alasan[]    = "Akan pensiun dalam {$bulanSampaiPensiun} bulan (2 tahun ke depan)";
                }
            } catch (\Exception) {}
        }

        // ── Booster pejabat struktural ──
        if ($pegawai->eselon && in_array($pegawai->eselon, ['Eselon III', 'Eselon IV'])) {
            if ($perluMutasi) $prioritas += 1;
            $alasan[] = "Pejabat struktural ({$pegawai->eselon})";
        }

        return [
            'perlu_mutasi'      => $perluMutasi,
            'prioritas'         => $prioritas,
            'alasan'            => $alasan,
            'lama_jabatan'      => $lamaJabatan,
            'rekomendasi_waktu' => $this->rekomendasiWaktuMutasi($pegawai, $tahun),
            'progress_jabatan'  => $lamaJabatan !== null
                ? min(100, (int) round(($lamaJabatan / 24) * 100))
                : null,
        ];
    }

    private function rekomendasiWaktuMutasi(Pegawai $pegawai, int $tahun): string
    {
        $tmtSource = $pegawai->tmt_jabatan ?? null;
        if (!$tmtSource && $pegawai->proyeksi_kp_1) {
            try { $tmtSource = Carbon::parse($pegawai->proyeksi_kp_1); } catch (\Exception) {}
        }

        if ($tmtSource) {
            try {
                $tmt    = $tmtSource instanceof Carbon ? $tmtSource : Carbon::parse($tmtSource);
                $target = $tmt->copy()->addYears(2);
                $result = $target->month <= 6
                    ? Carbon::create($target->year, 4, 1)
                    : Carbon::create($target->year, 10, 1);
                return $result->translatedFormat('F Y');
            } catch (\Exception) {}
        }

        return now()->month <= 3
            ? "April {$tahun}"
            : (now()->month <= 9 ? "Oktober {$tahun}" : "April " . ($tahun + 1));
    }

    public function show(Pegawai $pegawai)
    {
        $analisis = $this->analisisMutasi($pegawai, (int) date('Y'));

        $rekanSebagian = Pegawai::where('status', 'AKTIF')
            ->where('bagian', $pegawai->bagian)
            ->where('id', '!=', $pegawai->id)
            ->select('id', 'nama', 'jabatan', 'tmt_jabatan', 'proyeksi_kp_1', 'nip')
            ->limit(5)
            ->get()
            ->map(function ($r) {
                $r->analisis_mutasi = $this->analisisMutasi($r, (int) date('Y'));
                return $r;
            });

        return view('kepegawaian.mutasi.show', compact('pegawai', 'analisis', 'rekanSebagian'));
    }
}
