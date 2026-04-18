<?php

namespace App\Http\Controllers\Anggaran;

use App\Exports\DataAnggaranExport;
use App\Http\Controllers\Controller;
use App\Imports\DataAnggaranImport;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class DataAnggaranController extends Controller
{
    /**
     * Whitelist RO yang valid — sumber kebenaran tunggal di server.
     * Tidak pernah diambil dari user input secara langsung.
     */
    private const VALID_RO = ['Z06', '403', '405', '994'];

    // ══════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = Anggaran::query();

        if ($request->filled('ro') && $request->ro !== 'all') {
            $query->where('ro', $request->ro);
        }

        if ($request->filled('level')) {
            match ($request->level) {
                'ro'          => $query->whereNull('kode_subkomponen')->whereNull('kode_akun'),
                'subkomponen' => $query->whereNotNull('kode_subkomponen')->whereNull('kode_akun'),
                'akun'        => $query->whereNotNull('kode_akun'),
                default       => null,
            };
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program_kegiatan', 'like', "%{$search}%")
                  ->orWhere('kode_akun', 'like', "%{$search}%")
                  ->orWhere('kode_subkomponen', 'like', "%{$search}%");
            });
        }

        $anggarans = $query
            ->orderBy('ro')
            ->orderBy('kode_subkomponen')
            ->orderBy('kode_akun')
            ->paginate(20)
            ->withQueryString();

        $roList = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');

        return view('anggaran.data.index', compact('anggarans', 'roList'));
    }

    // ══════════════════════════════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════════════════════════════

    public function create()
    {
        $roList = self::VALID_RO;

        return view('anggaran.data.create', compact('roList'));
    }

    // ══════════════════════════════════════════════════════════════
    // STORE
    // ══════════════════════════════════════════════════════════════

    public function store(Request $request)
    {
        // Deteksi level dari field yang dikirim
        $hasAkun      = $request->filled('kode_akun');
        $hasSubkomp   = $request->filled('kode_subkomponen');

        // Bangun rules berdasarkan level
        $rules = [
            'kegiatan'         => ['required', 'string', 'max:50'],
            'kro'              => ['required', 'string', 'max:50'],
            'ro'               => ['required', 'string', Rule::in(self::VALID_RO)],
            'program_kegiatan' => ['required', 'string', 'max:1000'],
            'pic'              => ['required', 'string', 'max:100'],
        ];

        if ($hasAkun) {
            // Level Akun: subkomponen wajib ada, akun wajib ada, pagu wajib
            $rules['kode_subkomponen'] = ['required', 'string', 'max:50', 'alpha_num'];
            $rules['kode_akun']        = ['required', 'string', 'max:50'];
            $rules['pagu_anggaran']    = ['required', 'numeric', 'min:0', 'max:999999999999'];
        } elseif ($hasSubkomp) {
            // Level SubKomponen: hanya subkomponen, tidak ada akun
            $rules['kode_subkomponen'] = ['required', 'string', 'max:50', 'alpha_num'];
            $rules['kode_akun']        = ['nullable'];
            $rules['pagu_anggaran']    = ['nullable', 'numeric', 'min:0'];
        } else {
            // Level RO: tidak ada subkomponen maupun akun
            $rules['kode_subkomponen'] = ['nullable'];
            $rules['kode_akun']        = ['nullable'];
            $rules['pagu_anggaran']    = ['nullable', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);

        // Bersihkan & normalisasi kode (uppercase, strip non-alphanumeric)
        if (!empty($validated['kode_subkomponen'])) {
            $validated['kode_subkomponen'] = strtoupper(
                preg_replace('/[^A-Z0-9]/i', '', $validated['kode_subkomponen'])
            );
        }

        DB::beginTransaction();
        try {
            $kegiatan = $validated['kegiatan'];
            $kro      = $validated['kro'];
            $ro       = $validated['ro'];
            $subkomp  = $validated['kode_subkomponen'] ?? null;
            $akun     = $validated['kode_akun'] ?? null;

            $baseRef = $kegiatan . $kro . $ro;

            $validated['referensi']  = $baseRef;
            $validated['referensi2'] = $baseRef;
            $validated['ref_output'] = $baseRef;
            $validated['len']        = strlen($baseRef);

            if (!empty($subkomp)) {
                $validated['referensi']  = $baseRef . $subkomp;
                $validated['referensi2'] = $baseRef . $subkomp;
                $validated['len']        = strlen($baseRef . $subkomp);
            }

            if (!empty($akun)) {
                $validated['referensi'] = ($baseRef . ($subkomp ?? '')) . $akun;
                $validated['len']       = strlen($validated['referensi']);
            }

            // Pagu untuk RO dan SubKomponen selalu 0 — dihitung otomatis
            if (empty($akun)) {
                $validated['pagu_anggaran'] = 0;
            }

            $validated['sisa']                = $validated['pagu_anggaran'] ?? 0;
            $validated['total_penyerapan']    = 0;
            $validated['tagihan_outstanding'] = 0;

            foreach (['januari','februari','maret','april','mei','juni',
                      'juli','agustus','september','oktober','november','desember'] as $bulan) {
                $validated[$bulan] = 0;
            }

            $anggaran = Anggaran::create($validated);

            if (!empty($akun)) {
                $this->updateParentTotals($anggaran);
            }

            DB::commit();

            return redirect()->route('anggaran.data.index')
                ->with('success', 'Data anggaran berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal menambahkan data anggaran.', ['action' => 'store']);
            return back()->withInput()
                ->with('error', 'Gagal menambahkan data anggaran. Silakan coba lagi.');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // SHOW
    // ══════════════════════════════════════════════════════════════

    public function show(Anggaran $data)
    {
        $children = null;

        if (!$data->kode_akun) {
            if (!$data->kode_subkomponen) {
                // Level RO → tampilkan SubKomponen di bawahnya
                $children = Anggaran::where('kegiatan', $data->kegiatan)
                    ->where('kro', $data->kro)
                    ->where('ro', $data->ro)
                    ->whereNotNull('kode_subkomponen')
                    ->whereNull('kode_akun')
                    ->orderBy('kode_subkomponen')
                    ->get();
            } else {
                // Level SubKomponen → tampilkan Akun di bawahnya
                $children = Anggaran::where('kegiatan', $data->kegiatan)
                    ->where('kro', $data->kro)
                    ->where('ro', $data->ro)
                    ->where('kode_subkomponen', $data->kode_subkomponen)
                    ->whereNotNull('kode_akun')
                    ->orderBy('kode_akun')
                    ->get();
            }
        }

        return view('anggaran.data.show', compact('data', 'children'));
    }

    // ══════════════════════════════════════════════════════════════
    // EDIT
    // ══════════════════════════════════════════════════════════════

    public function edit(Anggaran $data)
    {
        $roList = self::VALID_RO;
        return view('anggaran.data.edit', compact('data', 'roList'));
    }

    // ══════════════════════════════════════════════════════════════
    // UPDATE
    // ══════════════════════════════════════════════════════════════

    public function update(Request $request, Anggaran $data)
    {
        $rules = [
            'kegiatan'         => ['required', 'string', 'max:50'],
            'kro'              => ['required', 'string', 'max:50'],
            'ro'               => ['required', 'string', Rule::in(self::VALID_RO)],
            'program_kegiatan' => ['required', 'string', 'max:1000'],
            'pic'              => ['required', 'string', 'max:100'],
        ];

        if ($data->kode_akun) {
            $rules['pagu_anggaran'] = ['required', 'numeric', 'min:0', 'max:999999999999'];
        }

        $validated = $request->validate($rules);

        // ── SECURITY: kode_subkomponen & kode_akun TIDAK PERNAH diubah dari request ──
        // Selalu ambil dari model yang sudah ada di DB
        unset($validated['kode_subkomponen'], $validated['kode_akun']);

        DB::beginTransaction();
        try {
            $selisihPagu = 0;

            if ($data->kode_akun) {
                $oldPagu     = (float) $data->pagu_anggaran;
                $newPagu     = (float) $validated['pagu_anggaran'];
                $selisihPagu = $newPagu - $oldPagu;
                $validated['sisa'] = (float) $data->sisa + $selisihPagu;
            } else {
                // Jangan izinkan pagu diubah untuk level RO/SubKomponen via form
                unset($validated['pagu_anggaran']);
            }

            // Rebuild referensi menggunakan kode yang sudah tersimpan di DB
            $kegiatan = $validated['kegiatan'];
            $kro      = $validated['kro'];
            $ro       = $validated['ro'];
            $subkomp  = $data->kode_subkomponen; // dari DB, bukan request
            $akun     = $data->kode_akun;         // dari DB, bukan request

            $baseRef = $kegiatan . $kro . $ro;

            $validated['referensi']  = $baseRef;
            $validated['referensi2'] = $baseRef;
            $validated['ref_output'] = $baseRef;
            $validated['len']        = strlen($baseRef);

            if (!empty($subkomp)) {
                $validated['referensi']  = $baseRef . $subkomp;
                $validated['referensi2'] = $baseRef . $subkomp;
                $validated['len']        = strlen($baseRef . $subkomp);
            }

            if (!empty($akun)) {
                $validated['referensi'] = ($baseRef . ($subkomp ?? '')) . $akun;
                $validated['len']       = strlen($validated['referensi']);
            }

            $data->update($validated);

            if ($selisihPagu != 0 && $data->kode_akun) {
                $this->updateParentPaguAfterEdit($data, $selisihPagu);
            }

            DB::commit();

            return redirect()->route('anggaran.data.index')
                ->with('success', 'Data anggaran berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal mengupdate data anggaran.', [
                'action'  => 'update',
                'data_id' => $data->id,
            ]);
            return back()->withInput()
                ->with('error', 'Gagal mengupdate data anggaran. Silakan coba lagi.');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════════════════════════════

    public function destroy(Anggaran $data)
    {
        DB::beginTransaction();
        try {
            if (!$data->kode_akun) {
                if (!$data->kode_subkomponen) {
                    $hasChildren = Anggaran::where('kegiatan', $data->kegiatan)
                        ->where('kro', $data->kro)
                        ->where('ro', $data->ro)
                        ->whereNotNull('kode_subkomponen')
                        ->exists();
                } else {
                    $hasChildren = Anggaran::where('kegiatan', $data->kegiatan)
                        ->where('kro', $data->kro)
                        ->where('ro', $data->ro)
                        ->where('kode_subkomponen', $data->kode_subkomponen)
                        ->whereNotNull('kode_akun')
                        ->exists();
                }

                if ($hasChildren) {
                    return back()->with('error',
                        'Tidak dapat menghapus item yang masih memiliki sub-item.');
                }
            }

            if ($data->total_penyerapan > 0 || $data->tagihan_outstanding > 0) {
                return back()->with('error',
                    'Tidak dapat menghapus item yang sudah memiliki realisasi.');
            }

            // Simpan nilai sebelum dihapus untuk propagate ke parent
            $paguSnapshot = (float) $data->pagu_anggaran;
            $isAkun       = (bool) $data->kode_akun;

            $data->delete();

            if ($isAkun && $paguSnapshot > 0) {
                $this->updateParentPaguAfterEdit($data, -$paguSnapshot);
            }

            DB::commit();

            return redirect()->route('anggaran.data.index')
                ->with('success', 'Data anggaran berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal menghapus data anggaran.', [
                'action'  => 'destroy',
                'data_id' => $data->id,
            ]);
            return back()->with('error', 'Gagal menghapus data anggaran. Silakan coba lagi.');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // IMPORT
    // ══════════════════════════════════════════════════════════════

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            $import = new DataAnggaranImport();
            Excel::import($import, $request->file('file'));

            $message = "Import berhasil! {$import->imported} data baru ditambahkan, "
                     . "{$import->updated} data diperbarui.";

            if (!empty($import->errors)) {
                $errorList = implode('<br>', array_map('e', $import->errors));
                return back()->with('warning', $message . "<br><strong>Peringatan:</strong><br>{$errorList}");
            }

            return back()->with('success', $message);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errors = [];
            foreach ($e->failures() as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return back()->with('error',
                'Validasi gagal:<br>' . implode('<br>', array_map('e', $errors)));

        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal import data anggaran.', ['action' => 'import']);
            return back()->with('error',
                'Gagal melakukan import. Pastikan format file sesuai template.');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // EXPORT — mengembalikan file download, BUKAN redirect
    // ══════════════════════════════════════════════════════════════

    public function export(Request $request)
    {
        try {
            $ro       = $request->get('ro');
            $level    = $request->get('level');
            $filename = 'data_anggaran_' . date('Ymd_His') . '.xlsx';

            // Excel::download() mengembalikan BinaryFileResponse langsung.
            // Jangan dibungkus redirect() — itulah yang menyebabkan halaman refresh.
            return Excel::download(new DataAnggaranExport($ro, $level), $filename);

        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal export data anggaran.', ['action' => 'export']);
            return back()->with('error', 'Gagal melakukan export. Silakan coba lagi.');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // AJAX — GET SUBKOMPONEN
    // ══════════════════════════════════════════════════════════════

    public function getSubkomponen(Request $request)
    {
        try {
            $ro = trim((string) $request->get('ro', ''));

            if (empty($ro)) {
                return response()->json(['error' => 'RO harus diisi.'], 400);
            }

            $subkomponens = Anggaran::where('ro', $ro)
                ->whereNotNull('kode_subkomponen')
                ->where('kode_subkomponen', '!=', '')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan']);

            return response()->json($subkomponens);

        } catch (\Exception $e) {
            return $this->handleExceptionJson(
                $e, 'Gagal mengambil data subkomponen.', 500,
                ['action' => 'getSubkomponen', 'ro' => $request->get('ro')]
            );
        }
    }

    // ══════════════════════════════════════════════════════════════
    // IMPORT FORM
    // ══════════════════════════════════════════════════════════════

    public function importForm()
    {
        return view('anggaran.data.import');
    }

    // ══════════════════════════════════════════════════════════════
    // DOWNLOAD TEMPLATE
    // ══════════════════════════════════════════════════════════════

    public function downloadTemplate()
    {
        try {
            $export = new class implements
                \Maatwebsite\Excel\Concerns\FromArray,
                \Maatwebsite\Excel\Concerns\WithHeadings,
                \Maatwebsite\Excel\Concerns\WithStyles,
                \Maatwebsite\Excel\Concerns\WithColumnWidths
            {
                public function array(): array
                {
                    return [
                        ['4753', 'EBA', '403', 'AA', '521211', 'Belanja Keperluan Perkantoran', 'SJ.7', 5000000],
                        ['4753', 'EBA', '403', 'AA', '',       'Sub Komponen AA - Uraian',      'SJ.7', 0],
                        ['4753', 'EBA', '403', '',   '',       'RO 403 - Uraian RO',            'SJ.7', 0],
                    ];
                }

                public function headings(): array
                {
                    return ['kegiatan', 'kro', 'ro', 'kode_subkomponen', 'kode_akun',
                            'program_kegiatan', 'pic', 'pagu_anggaran'];
                }

                public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                {
                    return [1 => ['font' => ['bold' => true]]];
                }

                public function columnWidths(): array
                {
                    return ['A' => 12, 'B' => 8, 'C' => 8, 'D' => 18,
                            'E' => 12, 'F' => 50, 'G' => 12, 'H' => 18];
                }
            };

            return Excel::download($export, 'template_anggaran.xlsx');

        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal mengunduh template anggaran.');
            return back()->with('error', 'Gagal mengunduh template. Silakan coba lagi.');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // SUMMARY
    // ══════════════════════════════════════════════════════════════

    public function summary(Request $request)
    {
        try {
            $tahun = $request->get('tahun', date('Y'));

            $summaryRO = Anggaran::whereNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->orderBy('ro')
                ->get()
                ->map(fn($item) => [
                    'ro'           => $item->ro,
                    'nama'         => get_ro_name($item->ro),
                    'pagu'         => $item->pagu_anggaran,
                    'realisasi'    => $item->total_penyerapan,
                    'outstanding'  => $item->tagihan_outstanding,
                    'sisa'         => $item->sisa,
                    'persen_serap' => $item->persentase_penyerapan,
                    'status'       => $item->status_anggaran,
                ]);

            $totals = Anggaran::whereNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->selectRaw('SUM(pagu_anggaran) as total_pagu,
                             SUM(total_penyerapan) as total_realisasi,
                             SUM(tagihan_outstanding) as total_outstanding,
                             SUM(sisa) as total_sisa')
                ->first();

            $bulanFields       = ['januari','februari','maret','april','mei','juni',
                                  'juli','agustus','september','oktober','november','desember'];
            $selectFields      = implode(', ', array_map(fn($b) => "SUM({$b}) as {$b}", $bulanFields));
            $realisasiPerBulan = Anggaran::whereNotNull('kode_akun')
                ->selectRaw($selectFields)
                ->first();

            return view('anggaran.data.summary',
                compact('summaryRO', 'totals', 'realisasiPerBulan', 'tahun'));

        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal memuat halaman summary anggaran.');
            return back()->with('error', 'Gagal memuat data summary. Silakan coba lagi.');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════

    /**
     * Recalculate pagu & realisasi SubKomponen dan RO parent dari akun yang baru dibuat.
     */
    private function updateParentTotals(Anggaran $anggaran): void
    {
        // ── SubKomponen parent ────────────────────────────────────
        $subkomp = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->where('kode_subkomponen', $anggaran->kode_subkomponen)
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp) {
            $siblingsQ = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->where('kode_subkomponen', $anggaran->kode_subkomponen)
                ->whereNotNull('kode_akun');

            $totalPagu      = (float) $siblingsQ->sum('pagu_anggaran');
            $totalRealisasi = (float) $siblingsQ->sum('total_penyerapan');

            $subkomp->update([
                'pagu_anggaran'    => $totalPagu,
                'total_penyerapan' => $totalRealisasi,
                'sisa'             => $totalPagu - $totalRealisasi,
            ]);
        }

        // ── RO parent ─────────────────────────────────────────────
        $ro = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->whereNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->first();

        if ($ro) {
            $siblingsQ = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun');

            $totalPagu      = (float) $siblingsQ->sum('pagu_anggaran');
            $totalRealisasi = (float) $siblingsQ->sum('total_penyerapan');

            $ro->update([
                'pagu_anggaran'    => $totalPagu,
                'total_penyerapan' => $totalRealisasi,
                'sisa'             => $totalPagu - $totalRealisasi,
            ]);
        }
    }

    /**
     * Tambah/kurangi selisih pagu ke SubKomponen dan RO parent.
     * Digunakan saat edit pagu akun atau saat akun dihapus.
     */
    private function updateParentPaguAfterEdit(Anggaran $anggaran, float $selisih): void
    {
        if ($selisih == 0) {
            return;
        }

        $subkomp = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->where('kode_subkomponen', $anggaran->kode_subkomponen)
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp) {
            $subkomp->increment('pagu_anggaran', $selisih);
            $subkomp->increment('sisa', $selisih);
        }

        $ro = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->whereNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->first();

        if ($ro) {
            $ro->increment('pagu_anggaran', $selisih);
            $ro->increment('sisa', $selisih);
        }
    }
}
