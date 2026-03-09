<?php

namespace App\Http\Controllers;

use App\Models\{Anggaran, Atk, AsetEndUser, Pegawai, PermintaanAtk, SPP, User};
use Illuminate\Support\Facades\{Auth, Cache, DB};

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $extraData = match (true) {
            in_array($role, ['superadmin', 'admin']) => $this->getAdminData(),
            $role === 'eksekutif'                    => $this->getEksekutifData(),
            $role === 'picpegawai'                   => $this->getPicPegawaiData(),
            $role === 'pickeuangan'                  => $this->getPicKeuanganData(),
            $role === 'picinventaris'                => $this->getPicInventarisData(),
            default                                  => [],
        };

        return view('dashboard', array_merge(
            ['user' => $user, 'role' => $role, 'stats' => $this->getBaseStats()],
            $extraData
        ));
    }

    // ── Helpers ──────────────────────────────────────────────

    private function anggaranStats(): array
    {
        return Cache::remember('dashboard_anggaran_stats', 300, function () {
            $pagu        = (float) Anggaran::sum('pagu_anggaran');
            $realisasi   = (float) SPP::sudahSP2D()->sum('netto');
            $outstanding = (float) SPP::belumSP2D()->sum('netto');
            return [
                'total_pagu'        => $pagu,
                'total_realisasi'   => $realisasi,
                'total_outstanding' => $outstanding,
                'total_sisa'        => $pagu - $realisasi,
                'persentase'        => $pagu > 0 ? round($realisasi / $pagu * 100, 2) : 0,
            ];
        });
    }

    private function anggaranBulanChart(): array
    {
        return Cache::remember('chart_anggaran_bulan', 300, function () {
            $months = [
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
            return collect($months)->map(fn($m) => (float) Anggaran::sum($m))->values()->toArray();
        });
    }

    private function sebaranBagian()
    {
        return Cache::remember(
            'chart_sebaran_bagian',
            300,
            fn() =>
            Pegawai::select('bagian', DB::raw('count(*) as total'))
                ->whereNotNull('bagian')->where('status', 'AKTIF')
                ->groupBy('bagian')->orderByDesc('total')->get()
        );
    }

    private function sebaranGrading()
    {
        return Cache::remember(
            'chart_sebaran_grading',
            300,
            fn() =>
            Pegawai::select('grading', DB::raw('count(*) as total'))
                ->whereNotNull('grading')->where('status', 'AKTIF')
                ->groupBy('grading')->orderBy('grading')->get()
        );
    }

    private function sebaranPendidikan()
    {
        return Cache::remember(
            'chart_sebaran_pendidikan',
            300,
            fn() =>
            Pegawai::select('pendidikan', DB::raw('count(*) as total'))
                ->whereNotNull('pendidikan')->where('status', 'AKTIF')
                ->groupBy('pendidikan')->orderByDesc('total')->get()
        );
    }

    private function jenisKelamin()
    {
        return Cache::remember(
            'chart_jenis_kelamin',
            300,
            fn() =>
            Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))
                ->whereNotNull('jenis_kelamin')->where('status', 'AKTIF')
                ->groupBy('jenis_kelamin')->get()
        );
    }

    private function sebaranEselon()
    {
        return Cache::remember(
            'chart_sebaran_eselon',
            300,
            fn() =>
            Pegawai::select('eselon', DB::raw('count(*) as total'))
                ->whereNotNull('eselon')->where('status', 'AKTIF')
                ->groupBy('eselon')->orderByDesc('total')->get()
        );
    }

    private function rangeUsia()
    {
        return Cache::remember('chart_range_usia', 300, function () {
            $ranges = ['20-30' => 0, '31-40' => 0, '41-50' => 0, '51-60' => 0, '60+' => 0];
            Pegawai::where('status', 'AKTIF')->whereNotNull('usia')
                ->pluck('usia')->each(function ($u) use (&$ranges) {
                    if ($u <= 30)      $ranges['20-30']++;
                    elseif ($u <= 40)  $ranges['31-40']++;
                    elseif ($u <= 50)  $ranges['41-50']++;
                    elseif ($u <= 60)  $ranges['51-60']++;
                    else               $ranges['60+']++;
                });
            return collect($ranges)->map(fn($t, $r) => (object)['range' => $r, 'total' => $t]);
        });
    }

    private function inventarisStats(bool $detail = false): array
    {
        $key = $detail ? 'dashboard_inventaris_stats_detail' : 'dashboard_inventaris_stats';
        return Cache::remember($key, 300, function () use ($detail) {
            $base = [
                'total_aset'         => AsetEndUser::count(),
                'aset_dipinjam'      => AsetEndUser::where('status', 'dipinjam')->count(),
                'total_atk'          => Atk::count(),
                'atk_menipis'        => Atk::where('status', 'menipis')->count(),
                'atk_kosong'         => Atk::where('status', 'kosong')->count(),
                'permintaan_pending' => PermintaanAtk::where('status', 'pending')->count(),
            ];
            if (!$detail) return $base;
            return array_merge($base, [
                'aset_baik'           => AsetEndUser::where('kondisi', 'baik')->count(),
                'aset_rusak'          => AsetEndUser::where('kondisi', 'rusak')->count(),
                'atk_tersedia'        => Atk::where('status', 'tersedia')->count(),
                'permintaan_approved' => PermintaanAtk::where('status', 'approved')->count(),
            ]);
        });
    }

    // ── Stats ─────────────────────────────────────────────────

    private function getBaseStats(): array
    {
        return Cache::remember('dashboard_base_stats', 300, fn() => [
            'total_pegawai'      => Pegawai::count(),
            'pegawai_aktif'      => Pegawai::where('status', 'AKTIF')->count(),
            'total_users'        => User::count(),
            'pegawai_per_bagian' => Pegawai::select('bagian', DB::raw('count(*) as total'))
                ->whereNotNull('bagian')->groupBy('bagian')->orderByDesc('total')->get(),
        ]);
    }

    // ── Role Data ─────────────────────────────────────────────

    private function getAdminData(): array
    {
        return [
            'chartSebaranBagian'     => $this->sebaranBagian(),
            'chartSebaranGrading'    => $this->sebaranGrading(),
            'chartSebaranPendidikan' => $this->sebaranPendidikan(),
            'chartJenisKelamin'      => $this->jenisKelamin(),
            'chartSebaranEselon'     => $this->sebaranEselon(),
            'chartRangeUsia'         => $this->rangeUsia(),
            'anggaranStats'          => $this->anggaranStats(),
            'inventarisStats'        => $this->inventarisStats(),
            'chartAnggaranBulan'     => $this->anggaranBulanChart(),
            'userPerRole'            => Cache::remember(
                'dashboard_user_per_role',
                300,
                fn() =>
                User::selectRaw('role, count(*) as total')->groupBy('role')->get()
            ),
            'chartAnggaranPerRo'     => Cache::remember(
                'chart_anggaran_ro',
                300,
                fn() =>
                Anggaran::select('ro', DB::raw('SUM(pagu_anggaran) as pagu'), DB::raw('SUM(total_penyerapan) as realisasi'))
                    ->whereNotNull('ro')->groupBy('ro')->get()
            ),
        ];
    }

    private function getEksekutifData(): array
    {
        return [
            'chartSebaranBagian'  => $this->sebaranBagian(),
            'chartJenisKelamin'   => $this->jenisKelamin(),
            'chartSebaranGrading' => $this->sebaranGrading(),
            'anggaranStats'       => $this->anggaranStats(),
            'inventarisStats'     => $this->inventarisStats(),
            'chartAnggaranBulan'  => $this->anggaranBulanChart(),
        ];
    }

    private function getPicPegawaiData(): array
    {
        return [
            'chartSebaranBagian'     => $this->sebaranBagian(),
            'chartSebaranGrading'    => $this->sebaranGrading(),
            'chartSebaranPendidikan' => $this->sebaranPendidikan(),
            'chartJenisKelamin'      => $this->jenisKelamin(),
            'chartSebaranEselon'     => $this->sebaranEselon(),
            'chartRangeUsia'         => $this->rangeUsia(),
            'pegawaiMendekatiPensiun' => Cache::remember(
                'pegawai_pensiun',
                300,
                fn() =>
                Pegawai::where('status', 'AKTIF')->whereNotNull('tanggal_pensiun')
                    ->whereBetween('tanggal_pensiun', [now(), now()->addYears(2)])
                    ->orderBy('tanggal_pensiun')->take(10)->get()
            ),
        ];
    }

    private function getPicKeuanganData(): array
    {
        return [
            'anggaranStats'      => $this->anggaranStats(),
            'chartAnggaranBulan' => $this->anggaranBulanChart(),
            'chartSppBulan'      => Cache::remember(
                'chart_spp_bulan',
                300,
                fn() =>
                SPP::select('bulan', DB::raw('SUM(netto) as total'))
                    ->sudahSP2D()->groupBy('bulan')->orderBy('bulan')->get()
            ),
            'chartAnggaranPerRo' => Cache::remember(
                'chart_anggaran_ro',
                300,
                fn() =>
                Anggaran::select('ro', DB::raw('SUM(pagu_anggaran) as pagu'), DB::raw('SUM(total_penyerapan) as realisasi'))
                    ->whereNotNull('ro')->groupBy('ro')->get()
            ),
        ];
    }

    private function getPicInventarisData(): array
    {
        return [
            'inventarisStats' => $this->inventarisStats(detail: true),
            'chartAsetKondisi' => Cache::remember(
                'chart_aset_kondisi',
                300,
                fn() =>
                AsetEndUser::select('kondisi', DB::raw('count(*) as total'))->groupBy('kondisi')->get()
            ),
            'chartAsetStatus'  => Cache::remember(
                'chart_aset_status',
                300,
                fn() =>
                AsetEndUser::select('status', DB::raw('count(*) as total'))->groupBy('status')->get()
            ),
            'chartAtkStatus'   => Cache::remember(
                'chart_atk_status',
                300,
                fn() =>
                Atk::select('status', DB::raw('count(*) as total'))->groupBy('status')->get()
            ),
        ];
    }
}
