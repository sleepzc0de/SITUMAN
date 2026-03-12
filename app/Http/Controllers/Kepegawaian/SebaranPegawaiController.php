<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SebaranPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $bagianList = Cache::remember('bagian_list', 3600, function () {
            return Pegawai::select('bagian')
                ->distinct()
                ->whereNotNull('bagian')
                ->orderBy('bagian')
                ->pluck('bagian');
        });

        $sebaranStats = $this->getStats();

        return view('kepegawaian.sebaran.index', compact('bagianList', 'sebaranStats'));
    }

    /**
     * AJAX endpoint — dipanggil Alpine.js setiap kali filter berubah.
     * Mengembalikan JSON: { data, pagination, stats, meta }
     */
    public function data(Request $request)
    {
        abort_unless($request->ajax() || $request->wantsJson(), 403);

        $query = Pegawai::query();

        if ($request->filled('bagian')) {
            $query->where('bagian', $request->bagian);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip',  'like', "%{$search}%");
            });
        }

        // Clone sebelum paginate agar stats pakai query yg sama
        $statsQuery = clone $query;

        $perPage  = max(1, min(100, (int) $request->input('per_page', 20)));
        $pegawai  = $query->orderBy('nama')->paginate($perPage)->withQueryString();

        // Stats dari hasil filter
        $stats = [
            'per_bagian' => (clone $statsQuery)
                ->select('bagian', DB::raw('count(*) as total'))
                ->whereNotNull('bagian')
                ->groupBy('bagian')
                ->orderByDesc('total')
                ->get(),

            'per_eselon' => (clone $statsQuery)
                ->select('eselon', DB::raw('count(*) as total'))
                ->whereNotNull('eselon')
                ->groupBy('eselon')
                ->get(),

            'per_jenis_kelamin' => (clone $statsQuery)
                ->select('jenis_kelamin', DB::raw('count(*) as total'))
                ->whereNotNull('jenis_kelamin')
                ->groupBy('jenis_kelamin')
                ->get(),
        ];

        // Format rows untuk response
        $rows = $pegawai->getCollection()->map(function ($p, $i) use ($pegawai) {
            return [
                'no'            => $pegawai->firstItem() + $i,
                'id'            => $p->id,
                'nama'          => $p->nama,
                'nama_gelar'    => $p->nama_gelar,
                'nip'           => $p->nip,
                'email'         => $p->email_kemenkeu,
                'bagian'        => $p->bagian,
                'subbagian'     => $p->subbagian,
                'jabatan'       => $p->jabatan,
                'eselon'        => $p->eselon,
                'grading'       => $p->grading,
                'status'        => $p->status,
                'initials'      => strtoupper(substr($p->nama, 0, 2)),
                'show_url'      => route('kepegawaian.sebaran.show', $p),
                'edit_url'      => route('kepegawaian.pegawai.edit', $p),
            ];
        });

        return response()->json([
            'data'  => $rows,
            'meta'  => [
                'total'        => $pegawai->total(),
                'per_page'     => $pegawai->perPage(),
                'current_page' => $pegawai->currentPage(),
                'last_page'    => $pegawai->lastPage(),
                'from'         => $pegawai->firstItem() ?? 0,
                'to'           => $pegawai->lastItem()   ?? 0,
            ],
            'stats' => [
                'per_bagian'        => $stats['per_bagian'],
                'per_eselon_count'  => $stats['per_eselon']->count(),
                'per_jenis_kelamin' => $stats['per_jenis_kelamin'],
                'bagian_count'      => $stats['per_bagian']->count(),
            ],
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function getStats(): array
    {
        return Cache::remember('sebaran_stats_global', 300, function () {
            return [
                'per_bagian' => Pegawai::select('bagian', DB::raw('count(*) as total'))
                    ->whereNotNull('bagian')
                    ->groupBy('bagian')
                    ->orderByDesc('total')
                    ->get(),

                'per_eselon' => Pegawai::select('eselon', DB::raw('count(*) as total'))
                    ->whereNotNull('eselon')
                    ->groupBy('eselon')
                    ->get(),

                'per_jenis_kelamin' => Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))
                    ->whereNotNull('jenis_kelamin')
                    ->groupBy('jenis_kelamin')
                    ->get(),
            ];
        });
    }
}
