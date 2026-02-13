<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SebaranPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::query();

        // Filter
        if ($request->filled('bagian')) {
            $query->where('bagian', $request->bagian);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        $pegawai = $query->paginate(20);

        // Data untuk filter
        $bagianList = Cache::remember('bagian_list', 3600, function() {
            return Pegawai::select('bagian')
                ->distinct()
                ->whereNotNull('bagian')
                ->pluck('bagian');
        });

        // Statistik sebaran
        $sebaranStats = Cache::remember('sebaran_stats_' . md5($request->fullUrl()), 300, function() use ($query) {
            return [
                'per_bagian' => Pegawai::select('bagian', \DB::raw('count(*) as total'))
                    ->groupBy('bagian')
                    ->orderBy('total', 'desc')
                    ->get(),
                'per_eselon' => Pegawai::select('eselon', \DB::raw('count(*) as total'))
                    ->whereNotNull('eselon')
                    ->groupBy('eselon')
                    ->get(),
                'per_jenis_kelamin' => Pegawai::select('jenis_kelamin', \DB::raw('count(*) as total'))
                    ->groupBy('jenis_kelamin')
                    ->get(),
            ];
        });

        return view('kepegawaian.sebaran.index', compact('pegawai', 'bagianList', 'sebaranStats'));
    }

    public function show(Pegawai $pegawai)
    {
        return view('kepegawaian.sebaran.show', compact('pegawai'));
    }

    public function export(Request $request)
    {
        // Export to Excel/PDF logic
        // You can use Laravel Excel or similar package
    }
}
