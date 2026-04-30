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
    private const VALID_RO = ['Z06', '403', '405', '994'];

    // ── Konstanta Batasan Karakter ─────────────────────────────
    private const MAX_KEGIATAN         = 50;
    private const MAX_KRO              = 50;
    private const MAX_RO_LEN           = 50;
    private const MAX_SUBKOMPONEN      = 50;
    private const MAX_KODE_AKUN        = 50;
    private const MAX_PROGRAM_KEGIATAN = 1000;
    private const MAX_PIC              = 100;
    private const MAX_PAGU             = 999999999999;
    private const MAX_SEARCH           = 200;

    // ── Validation messages global ─────────────────────────────
    private function validationMessages(): array
    {
        return [
            'kegiatan.required'         => 'Kode kegiatan wajib diisi.',
            'kegiatan.max'              => 'Kode kegiatan maksimal ' . self::MAX_KEGIATAN . ' karakter.',
            'kro.required'              => 'KRO wajib diisi.',
            'kro.max'                   => 'KRO maksimal ' . self::MAX_KRO . ' karakter.',
            'ro.required'               => 'RO wajib dipilih.',
            'ro.in'                     => 'Nilai RO tidak valid.',
            'ro.max'                    => 'RO maksimal ' . self::MAX_RO_LEN . ' karakter.',
            'kode_subkomponen.required' => 'Kode sub komponen wajib diisi.',
            'kode_subkomponen.max'      => 'Kode sub komponen maksimal ' . self::MAX_SUBKOMPONEN . ' karakter.',
            'kode_subkomponen.alpha_num'=> 'Kode sub komponen hanya boleh huruf dan angka.',
            'kode_akun.required'        => 'Kode akun wajib diisi.',
            'kode_akun.max'             => 'Kode akun maksimal ' . self::MAX_KODE_AKUN . ' karakter.',
            'program_kegiatan.required' => 'Uraian program/kegiatan wajib diisi.',
            'program_kegiatan.max'      => 'Uraian program/kegiatan maksimal ' . self::MAX_PROGRAM_KEGIATAN . ' karakter.',
            'pic.required'              => 'PIC wajib diisi.',
            'pic.max'                   => 'PIC maksimal ' . self::MAX_PIC . ' karakter.',
            'pagu_anggaran.required'    => 'Pagu anggaran wajib diisi.',
            'pagu_anggaran.numeric'     => 'Pagu anggaran harus berupa angka.',
            'pagu_anggaran.min'         => 'Pagu anggaran minimal 0.',
            'pagu_anggaran.max'         => 'Pagu anggaran terlalu besar.',
        ];
    }

    public function index(Request $request)
    {
        $query = Anggaran::query();

        if ($request->filled('ro') && $request->ro !== 'all') {
            // Whitelist RO
            if (in_array($request->ro, self::VALID_RO, true)) {
                $query->where('ro', $request->ro);
            }
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
            // Batasi panjang search untuk cegah abuse
            $search = mb_substr(trim($request->search), 0, self::MAX_SEARCH);
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

    public function create()
    {
        $roList = self::VALID_RO;
        return view('anggaran.data.create', compact('roList'));
    }

    public function store(Request $request)
    {
        $hasAkun    = $request->filled('kode_akun');
        $hasSubkomp = $request->filled('kode_subkomponen');

        $rules = [
            'kegiatan'         => ['required', 'string', 'max:' . self::MAX_KEGIATAN],
            'kro'              => ['required', 'string', 'max:' . self::MAX_KRO],
            'ro'               => ['required', 'string', 'max:' . self::MAX_RO_LEN, Rule::in(self::VALID_RO)],
            'program_kegiatan' => ['required', 'string', 'max:' . self::MAX_PROGRAM_KEGIATAN],
            'pic'              => ['required', 'string', 'max:' . self::MAX_PIC],
        ];

        if ($hasAkun) {
            $rules['kode_subkomponen'] = ['required', 'string', 'max:' . self::MAX_SUBKOMPONEN, 'alpha_num'];
            $rules['kode_akun']        = ['required', 'string', 'max:' . self::MAX_KODE_AKUN, 'alpha_num'];
            $rules['pagu_anggaran']    = ['required', 'numeric', 'min:0', 'max:' . self::MAX_PAGU];
        } elseif ($hasSubkomp) {
            $rules['kode_subkomponen'] = ['required', 'string', 'max:' . self::MAX_SUBKOMPONEN, 'alpha_num'];
            $rules['kode_akun']        = ['nullable'];
            $rules['pagu_anggaran']    = ['nullable', 'numeric', 'min:0', 'max:' . self::MAX_PAGU];
        } else {
            $rules['kode_subkomponen'] = ['nullable'];
            $rules['kode_akun']        = ['nullable'];
            $rules['pagu_anggaran']    = ['nullable', 'numeric', 'min:0', 'max:' . self::MAX_PAGU];
        }

        $validated = $request->validate($rules, $this->validationMessages());

        if (!empty($validated['kode_subkomponen'])) {
            $validated['kode_subkomponen'] = mb_substr(strtoupper(
                preg_replace('/[^A-Z0-9]/i', '', $validated['kode_subkomponen'])
            ), 0, self::MAX_SUBKOMPONEN);
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

    public function show(Anggaran $data)
    {
        $children = null;

        if (!$data->kode_akun) {
            if (!$data->kode_subkomponen) {
                $children = Anggaran::where('kegiatan', $data->kegiatan)
                    ->where('kro', $data->kro)
                    ->where('ro', $data->ro)
                    ->whereNotNull('kode_subkomponen')
                    ->whereNull('kode_akun')
                    ->orderBy('kode_subkomponen')
                    ->get();
            } else {
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

    public function edit(Anggaran $data)
    {
        $roList = self::VALID_RO;
        return view('anggaran.data.edit', compact('data', 'roList'));
    }

    public function update(Request $request, Anggaran $data)
    {
        $rules = [
            'kegiatan'         => ['required', 'string', 'max:' . self::MAX_KEGIATAN],
            'kro'              => ['required', 'string', 'max:' . self::MAX_KRO],
            'ro'               => ['required', 'string', 'max:' . self::MAX_RO_LEN, Rule::in(self::VALID_RO)],
            'program_kegiatan' => ['required', 'string', 'max:' . self::MAX_PROGRAM_KEGIATAN],
            'pic'              => ['required', 'string', 'max:' . self::MAX_PIC],
        ];

        if ($data->kode_akun) {
            $rules['pagu_anggaran'] = ['required', 'numeric', 'min:0', 'max:' . self::MAX_PAGU];
        }

        $validated = $request->validate($rules, $this->validationMessages());

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
                unset($validated['pagu_anggaran']);
            }

            $kegiatan = $validated['kegiatan'];
            $kro      = $validated['kro'];
            $ro       = $validated['ro'];
            $subkomp  = $data->kode_subkomponen;
            $akun     = $data->kode_akun;

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

    public function destroy(Anggaran $data)
    {
        // ... tetap sama seperti aslinya
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

    public function export(Request $request)
    {
        try {
            $ro    = $request->get('ro');
            $level = $request->get('level');

            // Validasi RO whitelist
            if ($ro && $ro !== 'all' && !in_array($ro, self::VALID_RO, true)) {
                $ro = null;
            }
            // Validasi level whitelist
            if ($level && !in_array($level, ['ro', 'subkomponen', 'akun'], true)) {
                $level = null;
            }

            $filename = 'data_anggaran_' . date('Ymd_His') . '.xlsx';
            return Excel::download(new DataAnggaranExport($ro, $level), $filename);
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal export data anggaran.', ['action' => 'export']);
            return back()->with('error', 'Gagal melakukan export. Silakan coba lagi.');
        }
    }

    public function getSubkomponen(Request $request)
    {
        try {
            $ro = trim((string) $request->get('ro', ''));

            // Validasi panjang dan whitelist
            if (empty($ro) || mb_strlen($ro) > self::MAX_RO_LEN || !in_array($ro, self::VALID_RO, true)) {
                return response()->json(['error' => 'RO tidak valid.'], 400);
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

    public function importForm()
    {
        return view('anggaran.data.import');
    }

    public function downloadTemplate()
    {
        // ... tetap sama
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

    public function summary(Request $request)
    {
        // ... tetap sama
        try {
            $tahun = (int) $request->get('tahun', date('Y'));
            // Batasi tahun pada range wajar
            if ($tahun < 2000 || $tahun > 2100) {
                $tahun = (int) date('Y');
            }

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

    // Private helpers (updateParentTotals, updateParentPaguAfterEdit) — TETAP SAMA
    private function updateParentTotals(Anggaran $anggaran): void
    {
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

    private function updateParentPaguAfterEdit(Anggaran $anggaran, float $selisih): void
    {
        if ($selisih == 0) return;

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
