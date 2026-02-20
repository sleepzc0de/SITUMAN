<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Exports\PegawaiExport;
use App\Http\Controllers\Controller;
use App\Imports\PegawaiImport;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bagian'))        $query->where('bagian', $request->bagian);
        if ($request->filled('status'))        $query->where('status', $request->status);
        if ($request->filled('jenis_kelamin')) $query->where('jenis_kelamin', $request->jenis_kelamin);
        if ($request->filled('pendidikan'))    $query->where('pendidikan', $request->pendidikan);
        if ($request->filled('eselon'))        $query->where('eselon', $request->eselon);

        $perPage = $request->input('per_page', 20);
        $pegawai = $query->orderBy('nama')->paginate($perPage)->withQueryString();

        // Filter options
        $bagianList      = Cache::remember('bagian_list', 3600, fn() => Pegawai::select('bagian')->distinct()->whereNotNull('bagian')->orderBy('bagian')->pluck('bagian'));
        $eselonList      = Cache::remember('eselon_list', 3600, fn() => Pegawai::select('eselon')->distinct()->whereNotNull('eselon')->orderBy('eselon')->pluck('eselon'));
        $pendidikanList  = Cache::remember('pendidikan_list', 3600, fn() => Pegawai::select('pendidikan')->distinct()->whereNotNull('pendidikan')->orderBy('pendidikan')->pluck('pendidikan'));

        // Analytics summary
        $analytics = $this->getAnalyticsSummary();

        return view('kepegawaian.pegawai.index', compact(
            'pegawai', 'bagianList', 'eselonList', 'pendidikanList', 'analytics'
        ));
    }

    public function create()
    {
        $bagianList     = Cache::remember('bagian_list', 3600, fn() => Pegawai::select('bagian')->distinct()->whereNotNull('bagian')->orderBy('bagian')->pluck('bagian'));
        $subbagianList  = Pegawai::select('subbagian')->distinct()->whereNotNull('subbagian')->orderBy('subbagian')->pluck('subbagian');

        return view('kepegawaian.pegawai.create', compact('bagianList', 'subbagianList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        Pegawai::create($validated);

        $this->clearCache();

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $bagianList    = Cache::remember('bagian_list', 3600, fn() => Pegawai::select('bagian')->distinct()->whereNotNull('bagian')->orderBy('bagian')->pluck('bagian'));
        $subbagianList = Pegawai::select('subbagian')->distinct()->whereNotNull('subbagian')->orderBy('subbagian')->pluck('subbagian');

        return view('kepegawaian.pegawai.edit', compact('pegawai', 'bagianList', 'subbagianList'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate($this->validationRules($pegawai->id));

        $pegawai->update($validated);

        $this->clearCache();

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $nama = $pegawai->nama;
        $pegawai->delete();

        $this->clearCache();

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', "Data pegawai {$nama} berhasil dihapus.");
    }

    // =========================================================
    // EXPORT
    // =========================================================

    public function export(Request $request)
    {
        $filters = $request->only(['bagian', 'status', 'jenis_kelamin']);
        $filename = 'data-pegawai-' . date('Y-m-d') . '.xlsx';

        return Excel::download(new PegawaiExport($filters), $filename);
    }

    // =========================================================
    // IMPORT
    // =========================================================

    public function importForm()
    {
        return view('kepegawaian.pegawai.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new PegawaiImport();
            Excel::import($import, $request->file('file'));

            $this->clearCache();

            $errorCount = count($import->errors());
            $msg = 'Import data pegawai berhasil.';
            if ($errorCount > 0) {
                $msg .= " {$errorCount} baris dilewati karena error.";
            }

            return redirect()->route('kepegawaian.pegawai.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        // Generate template menggunakan export kosong dengan header saja
        return Excel::download(new PegawaiExport(['status' => 'TEMPLATE_KOSONG_TIDAK_ADA']), 'template-import-pegawai.xlsx');
    }

    // =========================================================
    // ANALYTICS (JSON untuk Chart AJAX)
    // =========================================================

    public function analyticsData()
    {
        return response()->json($this->getAnalyticsSummary());
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function getAnalyticsSummary(): array
    {
        return Cache::remember('pegawai_analytics_summary', 300, function () {
            $total   = Pegawai::count();
            $aktif   = Pegawai::where('status', 'AKTIF')->count();

            return [
                'total'              => $total,
                'aktif'              => $aktif,
                'tidak_aktif'        => $total - $aktif,
                'per_bagian'         => Pegawai::select('bagian', DB::raw('count(*) as total'))->whereNotNull('bagian')->groupBy('bagian')->orderBy('total', 'desc')->get(),
                'per_grading'        => Pegawai::select('grading', DB::raw('count(*) as total'))->whereNotNull('grading')->groupBy('grading')->orderBy('grading')->get(),
                'per_pendidikan'     => Pegawai::select('pendidikan', DB::raw('count(*) as total'))->whereNotNull('pendidikan')->groupBy('pendidikan')->orderBy('total', 'desc')->get(),
                'per_jenis_kelamin'  => Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))->whereNotNull('jenis_kelamin')->groupBy('jenis_kelamin')->get(),
                'per_eselon'         => Pegawai::select('eselon', DB::raw('count(*) as total'))->whereNotNull('eselon')->groupBy('eselon')->orderBy('total', 'desc')->get(),
                'per_jenis_pegawai'  => Pegawai::select('jenis_pegawai', DB::raw('count(*) as total'))->whereNotNull('jenis_pegawai')->groupBy('jenis_pegawai')->get(),
                'akan_pensiun_2th'   => Pegawai::where('status', 'AKTIF')->whereNotNull('tanggal_pensiun')->whereBetween('tanggal_pensiun', [now(), now()->addYears(2)])->count(),
                'akan_pensiun_1th'   => Pegawai::where('status', 'AKTIF')->whereNotNull('tanggal_pensiun')->whereBetween('tanggal_pensiun', [now(), now()->addYear()])->count(),
                'range_usia'         => $this->getRangeUsia(),
                'range_masa_kerja'   => $this->getRangeMasaKerja(),
                'avg_grading'        => round(Pegawai::where('status', 'AKTIF')->whereNotNull('grading')->avg('grading'), 1),
                'avg_usia'           => round(Pegawai::where('status', 'AKTIF')->whereNotNull('usia')->avg('usia'), 1),
                'avg_masa_kerja'     => round(Pegawai::where('status', 'AKTIF')->whereNotNull('masa_kerja_tahun')->avg('masa_kerja_tahun'), 1),
            ];
        });
    }

    private function getRangeUsia(): array
    {
        $ranges = ['< 30' => 0, '30-39' => 0, '40-49' => 0, '50-59' => 0, '≥ 60' => 0];
        Pegawai::where('status', 'AKTIF')->whereNotNull('usia')->pluck('usia')->each(function ($usia) use (&$ranges) {
            if ($usia < 30) $ranges['< 30']++;
            elseif ($usia < 40) $ranges['30-39']++;
            elseif ($usia < 50) $ranges['40-49']++;
            elseif ($usia < 60) $ranges['50-59']++;
            else $ranges['≥ 60']++;
        });
        return $ranges;
    }

    private function getRangeMasaKerja(): array
    {
        $ranges = ['< 5 th' => 0, '5-10 th' => 0, '11-20 th' => 0, '21-30 th' => 0, '> 30 th' => 0];
        Pegawai::where('status', 'AKTIF')->whereNotNull('masa_kerja_tahun')->pluck('masa_kerja_tahun')->each(function ($mk) use (&$ranges) {
            if ($mk < 5) $ranges['< 5 th']++;
            elseif ($mk <= 10) $ranges['5-10 th']++;
            elseif ($mk <= 20) $ranges['11-20 th']++;
            elseif ($mk <= 30) $ranges['21-30 th']++;
            else $ranges['> 30 th']++;
        });
        return $ranges;
    }

    private function clearCache(): void
    {
        $keys = [
            'bagian_list', 'eselon_list', 'pendidikan_list',
            'chart_sebaran_bagian', 'chart_sebaran_grading', 'chart_sebaran_pendidikan',
            'chart_jenis_kelamin', 'chart_sebaran_eselon', 'chart_range_usia',
            'sebaran_stats_', 'pegawai_pensiun', 'pegawai_analytics_summary',
            'dashboard_base_stats',
        ];
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    private function validationRules(?string $ignoreId = null): array
    {
        return [
            'nama'            => 'required|string|max:255',
            'nama_gelar'      => 'nullable|string|max:255',
            'nip'             => 'required|string|unique:pegawai,nip' . ($ignoreId ? ",{$ignoreId}" : ''),
            'pangkat'         => 'nullable|string|max:100',
            'pendidikan'      => 'nullable|string|max:50',
            'email_kemenkeu'  => 'nullable|email|max:255',
            'email_pribadi'   => 'nullable|email|max:255',
            'no_hp'           => 'nullable|string|max:20',
            'grading'         => 'nullable|integer|min:1|max:16',
            'jabatan'         => 'nullable|string|max:255',
            'jenis_jabatan'   => 'nullable|string|max:100',
            'nama_jabatan'    => 'nullable|string|max:255',
            'eselon'          => 'nullable|string|max:50',
            'jenis_pegawai'   => 'nullable|string|max:100',
            'status'          => 'nullable|string|max:50',
            'lokasi'          => 'nullable|string|max:255',
            'bagian'          => 'nullable|string|max:255',
            'subbagian'       => 'nullable|string|max:255',
            'jurusan_s1'      => 'nullable|string|max:255',
            'jurusan_s2'      => 'nullable|string|max:255',
            'jurusan_s3'      => 'nullable|string|max:255',
            'tmt_cpns'        => 'nullable|date',
            'masa_kerja_tahun'=> 'nullable|integer|min:0',
            'masa_kerja_bulan'=> 'nullable|integer|min:0|max:11',
            'tanggal_lahir'   => 'nullable|date',
            'bulan_lahir'     => 'nullable|string|max:20',
            'tahun_lahir'     => 'nullable|integer|min:1940|max:' . date('Y'),
            'usia'            => 'nullable|integer|min:0|max:100',
            'tanggal_pensiun' => 'nullable|date',
            'tahun_pensiun'   => 'nullable|integer|min:' . date('Y'),
            'proyeksi_kp_1'   => 'nullable|string|max:255',
            'proyeksi_kp_2'   => 'nullable|string|max:255',
            'keterangan_kp'   => 'nullable|string',
            'jenis_kelamin'   => 'nullable|in:Laki-laki,Perempuan',
        ];
    }
}
