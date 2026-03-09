<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Atk;
use App\Models\AsetEndUser;
use App\Models\Pegawai;
use App\Models\PermintaanAtk;
use App\Models\SPP;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Data dasar (semua role yang bisa akses dashboard)
        $baseStats = $this->getBaseStats();

        // Data tambahan berdasarkan role
        $extraData = match (true) {
            in_array($role, ['superadmin', 'admin']) => $this->getAdminData(),
            $role === 'eksekutif'                    => $this->getEksekutifData(),
            $role === 'picpegawai'                   => $this->getPicPegawaiData(),
            $role === 'pickeuangan'                  => $this->getPicKeuanganData(),
            $role === 'picinventaris'                => $this->getPicInventarisData(),
            default                                  => $this->getUserData(),
        };

        return view('dashboard', array_merge(
            ['user' => $user, 'role' => $role, 'stats' => $baseStats],
            $extraData
        ));
    }

    // =========================================================
    // BASE STATS (semua role)
    // =========================================================

    private function getBaseStats(): array
    {
        return Cache::remember('dashboard_base_stats', 300, function () {
            return [
                'total_pegawai'       => Pegawai::count(),
                'pegawai_aktif'       => Pegawai::where('status', 'AKTIF')->count(),
                'total_users'         => User::count(),
                'pegawai_per_bagian'  => Pegawai::select('bagian', DB::raw('count(*) as total'))
                    ->whereNotNull('bagian')
                    ->groupBy('bagian')
                    ->orderBy('total', 'desc')
                    ->get(),
            ];
        });
    }

    // =========================================================
    // ADMIN/SUPERADMIN DATA - Semua modul
    // =========================================================

    private function getAdminData(): array
    {
        $chartSebaranBagian = Cache::remember('chart_sebaran_bagian', 300, function () {
            return Pegawai::select('bagian', DB::raw('count(*) as total'))
                ->whereNotNull('bagian')->where('status', 'AKTIF')
                ->groupBy('bagian')->orderBy('total', 'desc')->get();
        });

        $chartSebaranGrading = Cache::remember('chart_sebaran_grading', 300, function () {
            return Pegawai::select('grading', DB::raw('count(*) as total'))
                ->whereNotNull('grading')->where('status', 'AKTIF')
                ->groupBy('grading')->orderBy('grading')->get();
        });

        $chartSebaranPendidikan = Cache::remember('chart_sebaran_pendidikan', 300, function () {
            return Pegawai::select('pendidikan', DB::raw('count(*) as total'))
                ->whereNotNull('pendidikan')->where('status', 'AKTIF')
                ->groupBy('pendidikan')->orderBy('total', 'desc')->get();
        });

        $chartJenisKelamin = Cache::remember('chart_jenis_kelamin', 300, function () {
            return Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))
                ->whereNotNull('jenis_kelamin')->where('status', 'AKTIF')
                ->groupBy('jenis_kelamin')->get();
        });

        $chartSebaranEselon = Cache::remember('chart_sebaran_eselon', 300, function () {
            return Pegawai::select('eselon', DB::raw('count(*) as total'))
                ->whereNotNull('eselon')->where('status', 'AKTIF')
                ->groupBy('eselon')->orderBy('total', 'desc')->get();
        });

        $chartRangeUsia = Cache::remember('chart_range_usia', 300, function () {
            $pegawai = Pegawai::where('status', 'AKTIF')->whereNotNull('usia')->get();
            $ranges = ['20-30' => 0, '31-40' => 0, '41-50' => 0, '51-60' => 0, '60+' => 0];
            foreach ($pegawai as $p) {
                $usia = $p->usia;
                if ($usia <= 30) $ranges['20-30']++;
                elseif ($usia <= 40) $ranges['31-40']++;
                elseif ($usia <= 50) $ranges['41-50']++;
                elseif ($usia <= 60) $ranges['51-60']++;
                else $ranges['60+']++;
            }
            return collect($ranges)->map(fn($total, $range) => (object)['range' => $range, 'total' => $total]);
        });

        // Ringkasan anggaran
        $anggaranStats = Cache::remember('dashboard_anggaran_stats', 300, function () {
            $totalPagu = Anggaran::sum('pagu_anggaran');
            $totalRealisasi = SPP::where('status', 'Tagihan Telah SP2D')->sum('netto');
            $totalOutstanding = SPP::where('status', 'Tagihan Belum SP2D')->sum('netto');

            return [
                'total_pagu'        => $totalPagu,
                'total_realisasi'   => $totalRealisasi,
                'total_outstanding' => $totalOutstanding,
                'persentase'        => $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0,
            ];
        });

        // Statistik inventaris
        $inventarisStats = Cache::remember('dashboard_inventaris_stats', 300, function () {
            return [
                'total_aset'     => AsetEndUser::count(),
                'aset_dipinjam'  => AsetEndUser::where('status', 'dipinjam')->count(),
                'total_atk'      => Atk::count(),
                'atk_menipis'    => Atk::where('status', 'menipis')->count(),
                'atk_kosong'     => Atk::where('status', 'kosong')->count(),
                'permintaan_pending' => PermintaanAtk::where('status', 'pending')->count(),
            ];
        });

        // Chart anggaran per bulan
        $chartAnggaranBulan = Cache::remember('chart_anggaran_bulan', 300, function () {
            $months = ['januari','februari','maret','april','mei','juni',
                       'juli','agustus','september','oktober','november','desember'];
            $data = [];
            foreach ($months as $month) {
                $data[] = (float) Anggaran::sum($month);
            }
            return $data;
        });

        // User per role untuk admin
        $userPerRole = Cache::remember('dashboard_user_per_role', 300, function () {
            return User::selectRaw('role, count(*) as total')
                ->groupBy('role')
                ->get();
        });

        return compact(
            'chartSebaranBagian',
            'chartSebaranGrading',
            'chartSebaranPendidikan',
            'chartJenisKelamin',
            'chartSebaranEselon',
            'chartRangeUsia',
            'anggaranStats',
            'inventarisStats',
            'chartAnggaranBulan',
            'userPerRole'
        );
    }

    // =========================================================
    // EKSEKUTIF DATA - Ringkasan semua modul (read-only)
    // =========================================================

    private function getEksekutifData(): array
    {
        $chartSebaranBagian = Cache::remember('chart_sebaran_bagian', 300, fn() =>
            Pegawai::select('bagian', DB::raw('count(*) as total'))
                ->whereNotNull('bagian')->where('status', 'AKTIF')
                ->groupBy('bagian')->orderBy('total', 'desc')->get()
        );

        $chartJenisKelamin = Cache::remember('chart_jenis_kelamin', 300, fn() =>
            Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))
                ->whereNotNull('jenis_kelamin')->where('status', 'AKTIF')
                ->groupBy('jenis_kelamin')->get()
        );

        $anggaranStats = Cache::remember('dashboard_anggaran_stats', 300, function () {
            $totalPagu = Anggaran::sum('pagu_anggaran');
            $totalRealisasi = SPP::where('status', 'Tagihan Telah SP2D')->sum('netto');
            return [
                'total_pagu'      => $totalPagu,
                'total_realisasi' => $totalRealisasi,
                'persentase'      => $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0,
            ];
        });

        $inventarisStats = Cache::remember('dashboard_inventaris_stats', 300, fn() => [
            'total_aset'    => AsetEndUser::count(),
            'aset_dipinjam' => AsetEndUser::where('status', 'dipinjam')->count(),
            'total_atk'     => Atk::count(),
        ]);

        $chartAnggaranBulan = Cache::remember('chart_anggaran_bulan', 300, function () {
            $months = ['januari','februari','maret','april','mei','juni',
                       'juli','agustus','september','oktober','november','desember'];
            return collect($months)->map(fn($m) => (float) Anggaran::sum($m))->values()->toArray();
        });

        $chartSebaranGrading = Cache::remember('chart_sebaran_grading', 300, fn() =>
            Pegawai::select('grading', DB::raw('count(*) as total'))
                ->whereNotNull('grading')->where('status', 'AKTIF')
                ->groupBy('grading')->orderBy('grading')->get()
        );

        return compact(
            'chartSebaranBagian',
            'chartJenisKelamin',
            'anggaranStats',
            'inventarisStats',
            'chartAnggaranBulan',
            'chartSebaranGrading'
        );
    }

    // =========================================================
    // PIC PEGAWAI DATA
    // =========================================================

    private function getPicPegawaiData(): array
    {
        $chartSebaranBagian = Cache::remember('chart_sebaran_bagian', 300, fn() =>
            Pegawai::select('bagian', DB::raw('count(*) as total'))
                ->whereNotNull('bagian')->where('status', 'AKTIF')
                ->groupBy('bagian')->orderBy('total', 'desc')->get()
        );

        $chartSebaranGrading = Cache::remember('chart_sebaran_grading', 300, fn() =>
            Pegawai::select('grading', DB::raw('count(*) as total'))
                ->whereNotNull('grading')->where('status', 'AKTIF')
                ->groupBy('grading')->orderBy('grading')->get()
        );

        $chartSebaranPendidikan = Cache::remember('chart_sebaran_pendidikan', 300, fn() =>
            Pegawai::select('pendidikan', DB::raw('count(*) as total'))
                ->whereNotNull('pendidikan')->where('status', 'AKTIF')
                ->groupBy('pendidikan')->orderBy('total', 'desc')->get()
        );

        $chartJenisKelamin = Cache::remember('chart_jenis_kelamin', 300, fn() =>
            Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))
                ->whereNotNull('jenis_kelamin')->where('status', 'AKTIF')
                ->groupBy('jenis_kelamin')->get()
        );

        $chartSebaranEselon = Cache::remember('chart_sebaran_eselon', 300, fn() =>
            Pegawai::select('eselon', DB::raw('count(*) as total'))
                ->whereNotNull('eselon')->where('status', 'AKTIF')
                ->groupBy('eselon')->orderBy('total', 'desc')->get()
        );

        $chartRangeUsia = Cache::remember('chart_range_usia', 300, function () {
            $pegawai = Pegawai::where('status', 'AKTIF')->whereNotNull('usia')->get();
            $ranges = ['20-30' => 0, '31-40' => 0, '41-50' => 0, '51-60' => 0, '60+' => 0];
            foreach ($pegawai as $p) {
                $u = $p->usia;
                if ($u <= 30) $ranges['20-30']++;
                elseif ($u <= 40) $ranges['31-40']++;
                elseif ($u <= 50) $ranges['41-50']++;
                elseif ($u <= 60) $ranges['51-60']++;
                else $ranges['60+']++;
            }
            return collect($ranges)->map(fn($t, $r) => (object)['range' => $r, 'total' => $t]);
        });

        // Pegawai akan pensiun dalam 2 tahun
        $pegawaiMendekatiPensiun = Cache::remember('pegawai_pensiun', 300, fn() =>
            Pegawai::where('status', 'AKTIF')
                ->whereNotNull('tanggal_pensiun')
                ->whereBetween('tanggal_pensiun', [now(), now()->addYears(2)])
                ->orderBy('tanggal_pensiun')
                ->take(10)
                ->get()
        );

        return compact(
            'chartSebaranBagian',
            'chartSebaranGrading',
            'chartSebaranPendidikan',
            'chartJenisKelamin',
            'chartSebaranEselon',
            'chartRangeUsia',
            'pegawaiMendekatiPensiun'
        );
    }

    // =========================================================
    // PIC KEUANGAN DATA
    // =========================================================

    private function getPicKeuanganData(): array
    {
        $anggaranStats = Cache::remember('dashboard_anggaran_stats', 300, function () {
            $totalPagu = Anggaran::sum('pagu_anggaran');
            $totalRealisasi = SPP::where('status', 'Tagihan Telah SP2D')->sum('netto');
            $totalOutstanding = SPP::where('status', 'Tagihan Belum SP2D')->sum('netto');
            return [
                'total_pagu'        => $totalPagu,
                'total_realisasi'   => $totalRealisasi,
                'total_outstanding' => $totalOutstanding,
                'total_sisa'        => $totalPagu - $totalRealisasi,
                'persentase'        => $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0,
            ];
        });

        $chartAnggaranBulan = Cache::remember('chart_anggaran_bulan', 300, function () {
            $months = ['januari','februari','maret','april','mei','juni',
                       'juli','agustus','september','oktober','november','desember'];
            return collect($months)->map(fn($m) => (float) Anggaran::sum($m))->values()->toArray();
        });

        // Realisasi SPP per bulan
        $chartSppBulan = Cache::remember('chart_spp_bulan', 300, function () {
            return SPP::select('bulan', DB::raw('SUM(netto) as total'))
                ->where('status', 'Tagihan Telah SP2D')
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();
        });

        // Anggaran per RO
        $chartAnggaranPerRo = Cache::remember('chart_anggaran_ro', 300, fn() =>
            Anggaran::select('ro', DB::raw('SUM(pagu_anggaran) as pagu'), DB::raw('SUM(total_penyerapan) as realisasi'))
                ->whereNotNull('ro')
                ->groupBy('ro')
                ->get()
        );

        return compact('anggaranStats', 'chartAnggaranBulan', 'chartSppBulan', 'chartAnggaranPerRo');
    }

    // =========================================================
    // PIC INVENTARIS DATA
    // =========================================================

    private function getPicInventarisData(): array
    {
        $inventarisStats = Cache::remember('dashboard_inventaris_stats_detail', 300, function () {
            return [
                'total_aset'          => AsetEndUser::count(),
                'aset_dipinjam'       => AsetEndUser::where('status', 'dipinjam')->count(),
                'aset_baik'           => AsetEndUser::where('kondisi', 'baik')->count(),
                'aset_rusak'          => AsetEndUser::where('kondisi', 'rusak')->count(),
                'total_atk'           => Atk::count(),
                'atk_tersedia'        => Atk::where('status', 'tersedia')->count(),
                'atk_menipis'         => Atk::where('status', 'menipis')->count(),
                'atk_kosong'          => Atk::where('status', 'kosong')->count(),
                'permintaan_pending'  => PermintaanAtk::where('status', 'pending')->count(),
                'permintaan_approved' => PermintaanAtk::where('status', 'approved')->count(),
            ];
        });

        $chartAsetKondisi = Cache::remember('chart_aset_kondisi', 300, fn() =>
            AsetEndUser::select('kondisi', DB::raw('count(*) as total'))
                ->groupBy('kondisi')->get()
        );

        $chartAsetStatus = Cache::remember('chart_aset_status', 300, fn() =>
            AsetEndUser::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->get()
        );

        $chartAtkStatus = Cache::remember('chart_atk_status', 300, fn() =>
            Atk::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->get()
        );

        return compact('inventarisStats', 'chartAsetKondisi', 'chartAsetStatus', 'chartAtkStatus');
    }

    // =========================================================
    // USER BIASA DATA
    // =========================================================

    private function getUserData(): array
    {
        // User biasa hanya melihat info umum yang tidak sensitif
        return [];
    }
}
