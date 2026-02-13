<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'total_pegawai' => Pegawai::count(),
                'pegawai_aktif' => Pegawai::where('status', 'AKTIF')->count(),
                'total_users' => User::count(),
                'pegawai_per_bagian' => Pegawai::select('bagian', DB::raw('count(*) as total'))
                    ->whereNotNull('bagian')
                    ->groupBy('bagian')
                    ->orderBy('total', 'desc')
                    ->get(),
            ];
        });

        // Data untuk chart sebaran per bagian
        $chartSebaranBagian = Cache::remember('chart_sebaran_bagian', 300, function () {
            return Pegawai::select('bagian', DB::raw('count(*) as total'))
                ->whereNotNull('bagian')
                ->where('status', 'AKTIF')
                ->groupBy('bagian')
                ->orderBy('total', 'desc')
                ->get();
        });

        // Data untuk chart sebaran per grading
        $chartSebaranGrading = Cache::remember('chart_sebaran_grading', 300, function () {
            return Pegawai::select('grading', DB::raw('count(*) as total'))
                ->whereNotNull('grading')
                ->where('status', 'AKTIF')
                ->groupBy('grading')
                ->orderBy('grading')
                ->get();
        });

        // Data untuk chart sebaran per pendidikan
        $chartSebaranPendidikan = Cache::remember('chart_sebaran_pendidikan', 300, function () {
            return Pegawai::select('pendidikan', DB::raw('count(*) as total'))
                ->whereNotNull('pendidikan')
                ->where('status', 'AKTIF')
                ->groupBy('pendidikan')
                ->orderBy('total', 'desc')
                ->get();
        });

        // Data untuk chart sebaran per jenis kelamin
        $chartJenisKelamin = Cache::remember('chart_jenis_kelamin', 300, function () {
            return Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))
                ->whereNotNull('jenis_kelamin')
                ->where('status', 'AKTIF')
                ->groupBy('jenis_kelamin')
                ->get();
        });

        // Data untuk chart sebaran per eselon
        $chartSebaranEselon = Cache::remember('chart_sebaran_eselon', 300, function () {
            return Pegawai::select('eselon', DB::raw('count(*) as total'))
                ->whereNotNull('eselon')
                ->where('status', 'AKTIF')
                ->groupBy('eselon')
                ->orderBy('total', 'desc')
                ->get();
        });

        // Data untuk chart range usia
        $chartRangeUsia = Cache::remember('chart_range_usia', 300, function () {
            $pegawai = Pegawai::where('status', 'AKTIF')
                ->whereNotNull('usia')
                ->get();

            $ranges = [
                '20-30' => 0,
                '31-40' => 0,
                '41-50' => 0,
                '51-60' => 0,
                '60+' => 0,
            ];

            foreach ($pegawai as $p) {
                $usia = $p->usia;
                if ($usia >= 20 && $usia <= 30) $ranges['20-30']++;
                elseif ($usia >= 31 && $usia <= 40) $ranges['31-40']++;
                elseif ($usia >= 41 && $usia <= 50) $ranges['41-50']++;
                elseif ($usia >= 51 && $usia <= 60) $ranges['51-60']++;
                elseif ($usia > 60) $ranges['60+']++;
            }

            return collect($ranges)->map(fn($total, $range) => (object)['range' => $range, 'total' => $total]);
        });

        return view('dashboard', compact(
            'stats',
            'chartSebaranBagian',
            'chartSebaranGrading',
            'chartSebaranPendidikan',
            'chartJenisKelamin',
            'chartSebaranEselon',
            'chartRangeUsia'
        ));
    }
}
