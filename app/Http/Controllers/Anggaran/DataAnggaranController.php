<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataAnggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Anggaran::query();

        // Filter by RO
        if ($request->has('ro') && $request->ro !== 'all') {
            $query->where('ro', $request->ro);
        }

        // Filter by level (parent/child)
        if ($request->has('level')) {
            if ($request->level === 'ro') {
                $query->whereNull('kode_subkomponen')->whereNull('kode_akun');
            } elseif ($request->level === 'subkomponen') {
                $query->whereNotNull('kode_subkomponen')->whereNull('kode_akun');
            } elseif ($request->level === 'akun') {
                $query->whereNotNull('kode_akun');
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program_kegiatan', 'like', "%{$search}%")
                    ->orWhere('kode_akun', 'like', "%{$search}%")
                    ->orWhere('kode_subkomponen', 'like', "%{$search}%");
            });
        }

        $anggarans = $query->orderBy('ro')
            ->orderBy('kode_subkomponen')
            ->orderBy('kode_akun')
            ->paginate(20);

        // Get RO list for filter
        $roList = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');

        return view('anggaran.data.index', compact('anggarans', 'roList'));
    }

    public function create()
    {
        $roList = ['Z06', '403', '405', '994'];

        // Get existing parent items
        $roParents = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun')->get();
        $subkomponenParents = Anggaran::whereNotNull('kode_subkomponen')->whereNull('kode_akun')->get();

        return view('anggaran.data.create', compact('roList', 'roParents', 'subkomponenParents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kegiatan' => 'required|string|max:50',
            'kro' => 'required|string|max:50',
            'ro' => 'required|string|max:50',
            'kode_subkomponen' => 'nullable|string|max:50',
            'kode_akun' => 'nullable|string|max:50',
            'program_kegiatan' => 'required|string',
            'pic' => 'required|string|max:100',
            'pagu_anggaran' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generate referensi
            $baseRef = $validated['kegiatan'] . $validated['kro'] . $validated['ro'];
            $validated['referensi'] = $baseRef;
            $validated['referensi2'] = $baseRef;
            $validated['ref_output'] = $baseRef;
            $validated['len'] = strlen($baseRef);

            if ($validated['kode_subkomponen']) {
                $validated['referensi'] .= $validated['kode_subkomponen'];
                $validated['referensi2'] = $baseRef . $validated['kode_subkomponen'];
                $validated['len'] = strlen($validated['referensi2']);
            }

            if ($validated['kode_akun']) {
                $validated['referensi'] .= $validated['kode_akun'];
                $validated['len'] = strlen($validated['referensi']);
            }

            // Set initial values
            $validated['sisa'] = $validated['pagu_anggaran'];
            $validated['total_penyerapan'] = 0;
            $validated['tagihan_outstanding'] = 0;

            // Set bulan to 0
            foreach (
                [
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
                ] as $bulan
            ) {
                $validated[$bulan] = 0;
            }

            $anggaran = Anggaran::create($validated);

            // Update parent totals if this is a child item
            if ($validated['kode_akun']) {
                $this->updateParentTotals($anggaran);
            }

            DB::commit();

            return redirect()->route('anggaran.data.index')
                ->with('success', 'Data anggaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data anggaran: ' . $e->getMessage());
        }
    }

    public function show(Anggaran $data)
    {
        // Get children if this is a parent item
        $children = null;

        if (!$data->kode_akun) {
            if (!$data->kode_subkomponen) {
                // RO level - get all subkomponen
                $children = Anggaran::where('kegiatan', $data->kegiatan)
                    ->where('kro', $data->kro)
                    ->where('ro', $data->ro)
                    ->whereNotNull('kode_subkomponen')
                    ->whereNull('kode_akun')
                    ->get();
            } else {
                // Subkomponen level - get all akun
                $children = Anggaran::where('kegiatan', $data->kegiatan)
                    ->where('kro', $data->kro)
                    ->where('ro', $data->ro)
                    ->where('kode_subkomponen', $data->kode_subkomponen)
                    ->whereNotNull('kode_akun')
                    ->get();
            }
        }

        return view('anggaran.data.show', compact('data', 'children'));
    }

    public function edit(Anggaran $data)
    {
        $roList = ['Z06', '403', '405', '994'];

        return view('anggaran.data.edit', compact('data', 'roList'));
    }

    public function update(Request $request, Anggaran $data)
    {
        $validated = $request->validate([
            'kegiatan' => 'required|string|max:50',
            'kro' => 'required|string|max:50',
            'ro' => 'required|string|max:50',
            'kode_subkomponen' => 'nullable|string|max:50',
            'kode_akun' => 'nullable|string|max:50',
            'program_kegiatan' => 'required|string',
            'pic' => 'required|string|max:100',
            'pagu_anggaran' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $oldPagu = $data->pagu_anggaran;
            $newPagu = $validated['pagu_anggaran'];
            $selisihPagu = $newPagu - $oldPagu;

            // Update sisa anggaran
            $validated['sisa'] = $data->sisa + $selisihPagu;

            // Regenerate referensi jika ada perubahan
            $baseRef = $validated['kegiatan'] . $validated['kro'] . $validated['ro'];
            $validated['referensi'] = $baseRef;
            $validated['referensi2'] = $baseRef;
            $validated['ref_output'] = $baseRef;
            $validated['len'] = strlen($baseRef);

            if ($validated['kode_subkomponen']) {
                $validated['referensi'] .= $validated['kode_subkomponen'];
                $validated['referensi2'] = $baseRef . $validated['kode_subkomponen'];
                $validated['len'] = strlen($validated['referensi2']);
            }

            if ($validated['kode_akun']) {
                $validated['referensi'] .= $validated['kode_akun'];
                $validated['len'] = strlen($validated['referensi']);
            }

            $data->update($validated);

            // Update parent totals if pagu changed
            if ($selisihPagu != 0 && $data->kode_akun) {
                $this->updateParentPaguAfterEdit($data, $selisihPagu);
            }

            DB::commit();

            return redirect()->route('anggaran.data.index')
                ->with('success', 'Data anggaran berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data anggaran: ' . $e->getMessage());
        }
    }

    public function destroy(Anggaran $data)
    {
        DB::beginTransaction();
        try {
            // Check if this item has children
            if (!$data->kode_akun) {
                if (!$data->kode_subkomponen) {
                    // RO level - check subkomponen
                    $hasChildren = Anggaran::where('kegiatan', $data->kegiatan)
                        ->where('kro', $data->kro)
                        ->where('ro', $data->ro)
                        ->whereNotNull('kode_subkomponen')
                        ->exists();
                } else {
                    // Subkomponen level - check akun
                    $hasChildren = Anggaran::where('kegiatan', $data->kegiatan)
                        ->where('kro', $data->kro)
                        ->where('ro', $data->ro)
                        ->where('kode_subkomponen', $data->kode_subkomponen)
                        ->whereNotNull('kode_akun')
                        ->exists();
                }

                if ($hasChildren) {
                    return back()->with('error', 'Tidak dapat menghapus item yang masih memiliki child items');
                }
            }

            // Check if has realisasi
            if ($data->total_penyerapan > 0 || $data->tagihan_outstanding > 0) {
                return back()->with('error', 'Tidak dapat menghapus item yang sudah memiliki realisasi');
            }

            $data->delete();

            // Update parent totals if this was a child item
            if ($data->kode_akun) {
                $this->updateParentPaguAfterEdit($data, -$data->pagu_anggaran);
            }

            DB::commit();

            return redirect()->route('anggaran.data.index')
                ->with('success', 'Data anggaran berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data anggaran: ' . $e->getMessage());
        }
    }

    /**
     * Update parent totals when adding new child
     */
    private function updateParentTotals(Anggaran $anggaran)
    {
        // Update subkomponen level
        $subkomp = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->where('kode_subkomponen', $anggaran->kode_subkomponen)
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp) {
            $totalPagu = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->where('kode_subkomponen', $anggaran->kode_subkomponen)
                ->whereNotNull('kode_akun')
                ->sum('pagu_anggaran');

            $subkomp->pagu_anggaran = $totalPagu;
            $subkomp->sisa = $totalPagu;
            $subkomp->save();
        }

        // Update RO level
        $ro = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->whereNull('kode_subkomponen')
            ->first();

        if ($ro) {
            $totalPagu = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->sum('pagu_anggaran');

            $ro->pagu_anggaran = $totalPagu;
            $ro->sisa = $totalPagu;
            $ro->save();
        }
    }

    /**
     * Update parent pagu after edit/delete
     */
    private function updateParentPaguAfterEdit(Anggaran $anggaran, $selisih)
    {
        // Update subkomponen level
        $subkomp = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->where('kode_subkomponen', $anggaran->kode_subkomponen)
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp) {
            $subkomp->pagu_anggaran += $selisih;
            $subkomp->sisa += $selisih;
            $subkomp->save();
        }

        // Update RO level
        $ro = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->whereNull('kode_subkomponen')
            ->first();

        if ($ro) {
            $ro->pagu_anggaran += $selisih;
            $ro->sisa += $selisih;
            $ro->save();
        }
    }

    /**
     * Import from Excel
     */
    public function importForm()
    {
        return view('anggaran.data.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        // TODO: Implement Excel import
        // Menggunakan package seperti maatwebsite/excel

        return back()->with('info', 'Fitur import Excel akan segera tersedia');
    }

    /**
     * Export to Excel
     */
    public function export()
    {
        // TODO: Implement Excel export

        return back()->with('info', 'Fitur export Excel akan segera tersedia');
    }

    /**
     * Get subkomponen list berdasarkan RO (untuk AJAX)
     */
    public function getSubkomponen(Request $request)
    {
        try {
            Log::info('DataAnggaran getSubkomponen called', ['ro' => $request->ro]);

            if (!$request->ro) {
                return response()->json(['error' => 'RO harus diisi'], 400);
            }

            $subkomponens = Anggaran::where('ro', $request->ro)
                ->whereNotNull('kode_subkomponen')
                ->where('kode_subkomponen', '!=', '')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan']);

            Log::info('DataAnggaran getSubkomponen result', [
                'count' => $subkomponens->count(),
                'data' => $subkomponens->toArray()
            ]);

            return response()->json($subkomponens);
        } catch (\Exception $e) {
            Log::error('DataAnggaran getSubkomponen error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
