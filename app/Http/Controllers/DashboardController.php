<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'total_pegawai' => Pegawai::count(),
                'pegawai_aktif' => Pegawai::where('status', 'AKTIF')->count(),
                'total_users' => User::count(),
                'pegawai_per_bagian' => Pegawai::select('bagian', \DB::raw('count(*) as total'))
                    ->groupBy('bagian')
                    ->get(),
            ];
        });

        return view('dashboard', compact('stats'));
    }
}
