<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Exports\PegawaiExport;
use App\Http\Controllers\Controller;
use App\Imports\PegawaiImport;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class PegawaiController extends Controller
{
    // ── Whitelist nilai yang boleh ada di dropdown ─────────────
    private const VALID_JENIS_JABATAN = ['Struktural', 'Fungsional', 'Pelaksana'];
    private const VALID_ESELON        = ['Eselon I', 'Eselon II', 'Eselon III', 'Eselon IV', 'Non Eselon'];
    private const VALID_JENIS_PEGAWAI = ['PNS', 'PPPK', 'Honorer'];
    private const VALID_STATUS        = ['AKTIF', 'CLTN', 'PENSIUN', 'NON AKTIF'];
    private const VALID_PENDIDIKAN    = ['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
    private const VALID_JENIS_KELAMIN = ['Laki-laki', 'Perempuan'];

    // ── INDEX ──────────────────────────────────────────────────

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

        $perPage = min(100, max(1, (int) $request->input('per_page', 20)));
        $pegawai = $query->orderBy('nama')->paginate($perPage)->withQueryString();

        $bagianList     = Cache::remember('bagian_list',     3600, fn() => Pegawai::select('bagian')->distinct()->whereNotNull('bagian')->orderBy('bagian')->pluck('bagian'));
        $eselonList     = Cache::remember('eselon_list',     3600, fn() => Pegawai::select('eselon')->distinct()->whereNotNull('eselon')->orderBy('eselon')->pluck('eselon'));
        $pendidikanList = Cache::remember('pendidikan_list', 3600, fn() => Pegawai::select('pendidikan')->distinct()->whereNotNull('pendidikan')->orderBy('pendidikan')->pluck('pendidikan'));

        $analytics = $this->getAnalyticsSummary();

        if ($request->ajax()) {
            return response()->json([
                'html'      => view('kepegawaian.pegawai._table', compact('pegawai'))->render(),
                'paginator' => view('kepegawaian.pegawai._paginator', compact('pegawai'))->render(),
                'info'      => [
                    'from'       => $pegawai->firstItem() ?? 0,
                    'to'         => $pegawai->lastItem()   ?? 0,
                    'total'      => $pegawai->total(),
                    'has_filter' => $request->anyFilled(['search','bagian','status','jenis_kelamin','pendidikan','eselon']),
                ],
            ]);
        }

        return view('kepegawaian.pegawai.index', compact(
            'pegawai', 'bagianList', 'eselonList', 'pendidikanList', 'analytics'
        ));
    }

    // ── CREATE ─────────────────────────────────────────────────

    public function create()
    {
        $bagianList    = Cache::remember('bagian_list', 3600, fn() => Pegawai::select('bagian')->distinct()->whereNotNull('bagian')->orderBy('bagian')->pluck('bagian'));
        $subbagianList = Pegawai::select('subbagian')->distinct()->whereNotNull('subbagian')->orderBy('subbagian')->pluck('subbagian');

        return view('kepegawaian.pegawai.create', compact('bagianList', 'subbagianList'));
    }

    // ── STORE ──────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());
        Pegawai::create($validated);
        $this->clearCache();

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    // ── SHOW ───────────────────────────────────────────────────

    public function show(Pegawai $pegawai)
    {
        return view('kepegawaian.pegawai.show', compact('pegawai'));
    }

    // ── EDIT ───────────────────────────────────────────────────

    public function edit(Pegawai $pegawai)
    {
        $bagianList    = Cache::remember('bagian_list', 3600, fn() => Pegawai::select('bagian')->distinct()->whereNotNull('bagian')->orderBy('bagian')->pluck('bagian'));
        $subbagianList = Pegawai::select('subbagian')->distinct()->whereNotNull('subbagian')->orderBy('subbagian')->pluck('subbagian');

        return view('kepegawaian.pegawai.edit', compact('pegawai', 'bagianList', 'subbagianList'));
    }

    // ── UPDATE ─────────────────────────────────────────────────

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate($this->validationRules($pegawai->id));
        $pegawai->update($validated);
        $this->clearCache();

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    // ── DESTROY ────────────────────────────────────────────────

    public function destroy(Pegawai $pegawai)
    {
        $nama = $pegawai->nama;
        $pegawai->delete();
        $this->clearCache();

        return redirect()->route('kepegawaian.pegawai.index')
            ->with('success', "Data pegawai {$nama} berhasil dihapus.");
    }

    // ── EXPORT ─────────────────────────────────────────────────

    public function export(Request $request)
    {
        // Hanya izinkan filter yang dikenal — jangan pass semua input ke export
        $filters  = $request->only(['bagian', 'status', 'jenis_kelamin']);
        $filename = 'data-pegawai-' . date('Y-m-d') . '.xlsx';

        // Excel::download() mengembalikan BinaryFileResponse — bukan redirect
        return Excel::download(new PegawaiExport($filters), $filename);
    }

    // ── IMPORT FORM ────────────────────────────────────────────

    public function importForm()
    {
        return view('kepegawaian.pegawai.import');
    }

    // ── IMPORT ─────────────────────────────────────────────────

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            $import = new PegawaiImport();
            Excel::import($import, $request->file('file'));
            $this->clearCache();

            $errorCount = count($import->errors());
            $msg        = 'Import data pegawai berhasil.';
            if ($errorCount > 0) {
                $msg .= " {$errorCount} baris dilewati karena data tidak valid.";
            }

            return redirect()->route('kepegawaian.pegawai.index')
                ->with('success', $msg);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errors = [];
            foreach ($e->failures() as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return back()->with('error',
                'Validasi file gagal. Pastikan format file sesuai template.');

        } catch (\Exception $e) {
            // JANGAN tampilkan $e->getMessage() ke user — bisa mengekspos info internal
            $this->handleException($e, 'Import pegawai gagal.', ['action' => 'import']);
            return back()->with('error',
                'Import gagal. Pastikan format file sesuai template dan coba lagi.');
        }
    }

    // ── DOWNLOAD TEMPLATE ──────────────────────────────────────

    public function downloadTemplate()
    {
        return Excel::download(
            new PegawaiExport(['status' => 'TEMPLATE_KOSONG_TIDAK_ADA']),
            'template-import-pegawai.xlsx'
        );
    }

    // ── ANALYTICS ──────────────────────────────────────────────

    public function analyticsData()
    {
        return response()->json($this->getAnalyticsSummary());
    }

    // ── PRIVATE HELPERS ────────────────────────────────────────

    private function getAnalyticsSummary(): array
    {
        return Cache::remember('pegawai_analytics_summary', 300, function () {
            $total = Pegawai::count();
            $aktif = Pegawai::where('status', 'AKTIF')->count();

            return [
                'total'             => $total,
                'aktif'             => $aktif,
                'tidak_aktif'       => $total - $aktif,
                'per_bagian'        => Pegawai::select('bagian', DB::raw('count(*) as total'))->whereNotNull('bagian')->groupBy('bagian')->orderBy('total', 'desc')->get(),
                'per_grading'       => Pegawai::select('grading', DB::raw('count(*) as total'))->whereNotNull('grading')->groupBy('grading')->orderBy('grading')->get(),
                'per_pendidikan'    => Pegawai::select('pendidikan', DB::raw('count(*) as total'))->whereNotNull('pendidikan')->groupBy('pendidikan')->orderBy('total', 'desc')->get(),
                'per_jenis_kelamin' => Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))->whereNotNull('jenis_kelamin')->groupBy('jenis_kelamin')->get(),
                'per_eselon'        => Pegawai::select('eselon', DB::raw('count(*) as total'))->whereNotNull('eselon')->groupBy('eselon')->orderBy('total', 'desc')->get(),
                'per_jenis_pegawai' => Pegawai::select('jenis_pegawai', DB::raw('count(*) as total'))->whereNotNull('jenis_pegawai')->groupBy('jenis_pegawai')->get(),
                'akan_pensiun_2th'  => Pegawai::where('status', 'AKTIF')->whereNotNull('tanggal_pensiun')->whereBetween('tanggal_pensiun', [now(), now()->addYears(2)])->count(),
                'akan_pensiun_1th'  => Pegawai::where('status', 'AKTIF')->whereNotNull('tanggal_pensiun')->whereBetween('tanggal_pensiun', [now(), now()->addYear()])->count(),
                'range_usia'        => $this->getRangeUsia(),
                'range_masa_kerja'  => $this->getRangeMasaKerja(),
                'avg_grading'       => round(Pegawai::where('status', 'AKTIF')->whereNotNull('grading')->avg('grading') ?? 0, 1),
                'avg_usia'          => round(Pegawai::where('status', 'AKTIF')->whereNotNull('usia')->avg('usia') ?? 0, 1),
                'avg_masa_kerja'    => round(Pegawai::where('status', 'AKTIF')->whereNotNull('masa_kerja_tahun')->avg('masa_kerja_tahun') ?? 0, 1),
            ];
        });
    }

    private function getRangeUsia(): array
    {
        $ranges = ['< 30' => 0, '30-39' => 0, '40-49' => 0, '50-59' => 0, '≥ 60' => 0];
        Pegawai::where('status', 'AKTIF')->whereNotNull('usia')->pluck('usia')->each(function ($usia) use (&$ranges) {
            if      ($usia < 30) $ranges['< 30']++;
            elseif  ($usia < 40) $ranges['30-39']++;
            elseif  ($usia < 50) $ranges['40-49']++;
            elseif  ($usia < 60) $ranges['50-59']++;
            else                 $ranges['≥ 60']++;
        });
        return $ranges;
    }

    private function getRangeMasaKerja(): array
    {
        $ranges = ['< 5 th' => 0, '5-10 th' => 0, '11-20 th' => 0, '21-30 th' => 0, '> 30 th' => 0];
        Pegawai::where('status', 'AKTIF')->whereNotNull('masa_kerja_tahun')->pluck('masa_kerja_tahun')->each(function ($mk) use (&$ranges) {
            if      ($mk < 5)   $ranges['< 5 th']++;
            elseif  ($mk <= 10) $ranges['5-10 th']++;
            elseif  ($mk <= 20) $ranges['11-20 th']++;
            elseif  ($mk <= 30) $ranges['21-30 th']++;
            else                $ranges['> 30 th']++;
        });
        return $ranges;
    }

    private function clearCache(): void
    {
        $keys = [
            'bagian_list', 'eselon_list', 'pendidikan_list',
            'pegawai_analytics_summary', 'sebaran_stats_global',
            'pegawai_bagian_list', 'dashboard_base_stats',
        ];
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        // Flush cache grading dan mutasi yang mungkin stale
        // (menggunakan prefix flush agar tidak cache poisoning)
        foreach (range(date('Y') - 2, date('Y') + 1) as $y) {
            Cache::forget("rekomendasi_grading_{$y}");
        }
    }

    /**
     * Validation rules dengan whitelist server-side untuk semua dropdown.
     * Ini adalah pertahanan utama terhadap user yang memanipulasi dropdown menjadi text.
     */
    private function validationRules(?string $ignoreId = null): array
    {
        return [
            'nama'             => ['required', 'string', 'max:255'],
            'nama_gelar'       => ['nullable', 'string', 'max:255'],
            // unique:table,column,except_id,id_column — format yang benar
            'nip'              => ['required', 'string', 'max:50',
                                   Rule::unique('pegawai', 'nip')->ignore($ignoreId)],
            'pangkat'          => ['nullable', 'string', 'max:100'],
            // Dropdown — gunakan Rule::in untuk whitelist server-side
            'pendidikan'       => ['nullable', Rule::in(self::VALID_PENDIDIKAN)],
            'email_kemenkeu'   => ['nullable', 'email', 'max:255'],
            'email_pribadi'    => ['nullable', 'email', 'max:255'],
            'no_hp'            => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\+\-\(\)]*$/'],
            'grading'          => ['nullable', 'integer', 'min:1', 'max:27'], // maks grade Eselon I
            'jabatan'          => ['nullable', 'string', 'max:255'],
            // Dropdown
            'jenis_jabatan'    => ['nullable', Rule::in(self::VALID_JENIS_JABATAN)],
            'nama_jabatan'     => ['nullable', 'string', 'max:255'],
            // Dropdown
            'eselon'           => ['nullable', Rule::in(self::VALID_ESELON)],
            // Dropdown
            'jenis_pegawai'    => ['nullable', Rule::in(self::VALID_JENIS_PEGAWAI)],
            // Dropdown
            'status'           => ['nullable', Rule::in(self::VALID_STATUS)],
            'lokasi'           => ['nullable', 'string', 'max:255'],
            // bagian & subbagian adalah free-text (bisa unit baru), tapi dibatasi panjangnya
            'bagian'           => ['nullable', 'string', 'max:255'],
            'subbagian'        => ['nullable', 'string', 'max:255'],
            'jurusan_s1'       => ['nullable', 'string', 'max:255'],
            'jurusan_s2'       => ['nullable', 'string', 'max:255'],
            'jurusan_s3'       => ['nullable', 'string', 'max:255'],
            'tmt_cpns'         => ['nullable', 'date'],
            'masa_kerja_tahun' => ['nullable', 'integer', 'min:0', 'max:50'],
            'masa_kerja_bulan' => ['nullable', 'integer', 'min:0', 'max:11'],
            'tanggal_lahir'    => ['nullable', 'date', 'before:today'],
            'bulan_lahir'      => ['nullable', 'string', 'max:20'],
            'tahun_lahir'      => ['nullable', 'integer', 'min:1940', 'max:' . date('Y')],
            'usia'             => ['nullable', 'integer', 'min:15', 'max:100'],
            'tanggal_pensiun'  => ['nullable', 'date'],
            'tahun_pensiun'    => ['nullable', 'integer', 'min:' . date('Y'), 'max:' . (date('Y') + 50)],
            'proyeksi_kp_1'    => ['nullable', 'string', 'max:255'],
            'proyeksi_kp_2'    => ['nullable', 'string', 'max:255'],
            'keterangan_kp'    => ['nullable', 'string', 'max:2000'],
            // Dropdown
            'jenis_kelamin'    => ['nullable', Rule::in(self::VALID_JENIS_KELAMIN)],
            'tmt_jabatan'      => ['nullable', 'date'],
        ];
    }
}
