<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\SPP;
use App\Services\AnggaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SPPController extends Controller
{
    public function __construct(private AnggaranService $anggaranService) {}

    public function index(Request $request)
    {
        $query = SPP::query();

        if ($request->filled('bulan') && $request->bulan !== 'all') {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('ro') && $request->ro !== 'all') {
            $query->where('ro', $request->ro);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_spp', 'like', "%{$search}%")
                    ->orWhere('uraian_spp', 'like', "%{$search}%")
                    ->orWhere('nama_pic', 'like', "%{$search}%");
            });
        }

        $spps = $query->orderBy('tgl_spp', 'desc')->paginate(20)->withQueryString();

        $bulanList = [
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
        ];
        $roList = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');

        // Statistik tetap ikut filter RO & bulan
        $statsQuery = SPP::query();
        if ($request->filled('bulan') && $request->bulan !== 'all') {
            $statsQuery->where('bulan', $request->bulan);
        }
        if ($request->filled('ro') && $request->ro !== 'all') {
            $statsQuery->where('ro', $request->ro);
        }

        $totalBruto     = (clone $statsQuery)->sum('bruto');
        $totalNetto     = (clone $statsQuery)->sum('netto');
        $totalSP2D      = (clone $statsQuery)->where('status', 'Tagihan Telah SP2D')->sum('netto');
        $totalBelumSP2D = (clone $statsQuery)->where('status', 'Tagihan Belum SP2D')->sum('netto');

        // Jika AJAX request, return partial view
        if ($request->ajax()) {
            return response()->json([
                'table' => view('anggaran.spp._table_content', compact('spps'))->render(),
                'stats' => [
                    'totalBruto'     => (float) $totalBruto,
                    'totalNetto'     => (float) $totalNetto,
                    'totalSP2D'      => (float) $totalSP2D,
                    'totalBelumSP2D' => (float) $totalBelumSP2D,
                ],
                'total'      => $spps->total(),
                'hasFilters' => $request->hasAny(['bulan', 'status', 'ro', 'search'])
                    && collect($request->only(['bulan', 'status', 'ro', 'search']))
                    ->filter(fn($v) => $v && $v !== 'all')
                    ->isNotEmpty(),
            ]);
        }

        return view('anggaran.spp.index', compact(
            'spps',
            'bulanList',
            'roList',
            'totalBruto',
            'totalNetto',
            'totalSP2D',
            'totalBelumSP2D'
        ));
    }

    public function create()
    {
        $roList = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');

        $bulanList = [
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
        ];

        $jenisBelanja = ['Kontraktual', 'Non Kontraktual', 'GUP', 'TUP'];
        $lsBendahara  = ['LS', 'Bendahara'];

        return view('anggaran.spp.create', compact('roList', 'bulanList', 'jenisBelanja', 'lsBendahara'));
    }

    public function store(Request $request)
    {
        $validated = $this->getValidationRules($request);

        DB::beginTransaction();
        try {
            // Validasi sisa anggaran sebelum simpan
            $coa = $validated['kode_kegiatan'] . $validated['kro'] . $validated['ro'] . $validated['mak'];
            $this->validateSisaAnggaran($coa, (float) $validated['netto']);

            $spp = SPP::create($validated);

            // Sync anggaran via service
            $this->anggaranService->syncFromSPP($spp->coa);

            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SPP store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan data SPP: ' . $e->getMessage());
        }
    }

    public function show(SPP $spp)
    {
        // Load anggaran terkait untuk info sisa
        $anggaran = Anggaran::whereNotNull('kode_akun')
            ->whereRaw("CONCAT(kegiatan, kro, ro, kode_akun) = ?", [$spp->coa])
            ->first();

        return view('anggaran.spp.show', compact('spp', 'anggaran'));
    }

    public function edit(SPP $spp)
    {
        $roList = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');

        $bulanList = [
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
        ];

        $jenisBelanja = ['Kontraktual', 'Non Kontraktual', 'GUP', 'TUP'];
        $lsBendahara  = ['LS', 'Bendahara'];

        $subkomponenList = Anggaran::where('ro', $spp->ro)
            ->whereNotNull('kode_subkomponen')
            ->whereNull('kode_akun')
            ->distinct()
            ->get(['kode_subkomponen', 'program_kegiatan']);

        $akunList = Anggaran::where('ro', $spp->ro)
            ->where('kode_subkomponen', $spp->sub_komponen)
            ->whereNotNull('kode_akun')
            ->get(['kode_akun', 'kegiatan', 'kro', 'program_kegiatan', 'pagu_anggaran', 'sisa']);

        return view('anggaran.spp.edit', compact(
            'spp',
            'roList',
            'bulanList',
            'jenisBelanja',
            'lsBendahara',
            'subkomponenList',
            'akunList'
        ));
    }

    public function update(Request $request, SPP $spp)
    {
        $validated = $this->getValidationRules($request, $spp->id);

        DB::beginTransaction();
        try {
            $oldCoa = $spp->coa;
            $newCoa = $validated['kode_kegiatan'] . $validated['kro'] . $validated['ro'] . $validated['mak'];

            // Validasi sisa anggaran (exclude SPP ini dari perhitungan outstanding)
            $this->validateSisaAnggaran($newCoa, (float) $validated['netto'], $spp->id);

            $spp->update($validated);

            // Pastikan COA terupdate
            if ($spp->coa !== $newCoa) {
                $spp->update(['coa' => $newCoa]);
            }

            // Sync anggaran: jika COA berubah, sync keduanya
            if ($oldCoa !== $newCoa) {
                $this->anggaranService->syncFromSPP($oldCoa);
            }
            $this->anggaranService->syncFromSPP($newCoa);

            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SPP update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mengupdate data SPP: ' . $e->getMessage());
        }
    }

    public function destroy(SPP $spp)
    {
        DB::beginTransaction();
        try {
            $coa = $spp->coa;
            $spp->delete(); // soft delete

            // Sync anggaran setelah delete
            $this->anggaranService->syncFromSPP($coa);

            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SPP destroy error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data SPP: ' . $e->getMessage());
        }
    }

    public function getSubkomponen(Request $request)
    {
        try {
            if (!$request->ro) {
                return response()->json(['error' => 'RO harus diisi'], 400);
            }

            $subkomponens = Anggaran::where('ro', $request->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan']);

            return response()->json($subkomponens);
        } catch (\Exception $e) {
            Log::error('getSubkomponen error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAkun(Request $request)
    {
        try {
            if (!$request->ro || !$request->subkomponen) {
                return response()->json(['error' => 'RO dan Subkomponen harus diisi'], 400);
            }

            $akuns = Anggaran::where('ro', $request->ro)
                ->where('kode_subkomponen', $request->subkomponen)
                ->whereNotNull('kode_akun')
                ->orderBy('kode_akun')
                ->get(['kode_akun', 'kegiatan', 'kro', 'program_kegiatan', 'pagu_anggaran', 'sisa', 'tagihan_outstanding']);

            // Tambahkan info sisa efektif di response
            $akuns->transform(function ($item) {
                $item->sisa_efektif = $item->sisa - $item->tagihan_outstanding;
                return $item;
            });

            return response()->json($akuns);
        } catch (\Exception $e) {
            Log::error('getAkun error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ==================== PRIVATE METHODS ====================

    private function getValidationRules(Request $request, ?string $sppId = null): array
    {
        return $request->validate([
            'bulan'             => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'no_spp'            => 'required|string|max:100|unique:spp,no_spp' . ($sppId ? ",{$sppId}" : ''),
            'nominatif'         => 'nullable|string|max:255',
            'tgl_spp'           => 'required|date',
            'jenis_kegiatan'    => 'required|string|max:255',
            'jenis_belanja'     => 'required|string|in:Kontraktual,Non Kontraktual,GUP,TUP',
            'nomor_kontrak'     => 'nullable|string|max:255',
            'no_bast'           => 'nullable|string|max:255',
            'id_eperjadin'      => 'nullable|string|max:255',
            'uraian_spp'        => 'required|string',
            'bagian'            => 'required|string|max:255',
            'nama_pic'          => 'required|string|max:255',
            'kode_kegiatan'     => 'required|string|max:50',
            'kro'               => 'required|string|max:50',
            'ro'                => 'required|string|max:50',
            'sub_komponen'      => 'required|string|max:255',
            'mak'               => 'required|string|max:50',
            'nomor_surat_tugas' => 'nullable|string|max:255',
            'tanggal_st'        => 'nullable|date',
            'nomor_undangan'    => 'nullable|string|max:255',
            'bruto'             => 'required|numeric|min:0',
            'ppn'               => 'nullable|numeric|min:0',
            'pph'               => 'nullable|numeric|min:0',
            'netto'             => 'required|numeric|min:0',
            'tanggal_mulai'     => 'nullable|date',
            'tanggal_selesai'   => 'nullable|date|after_or_equal:tanggal_mulai',
            'ls_bendahara'      => 'required|string|in:LS,Bendahara',
            'staff_ppk'         => 'nullable|string|max:255',
            'no_sp2d'           => 'nullable|string|max:255',
            'tgl_selesai_sp2d'  => 'nullable|date',
            'tgl_sp2d'          => 'nullable|date',
            'status'            => 'required|in:Tagihan Telah SP2D,Tagihan Belum SP2D',
            'posisi_uang'       => 'nullable|string|max:255',
        ]);
    }

    private function validateSisaAnggaran(string $coa, float $netto, ?string $excludeSppId = null): void
    {
        $anggaran = Anggaran::whereNotNull('kode_akun')
            ->whereRaw("CONCAT(kegiatan, kro, ro, kode_akun) = ?", [$coa])
            ->first();

        if (!$anggaran) {
            throw new \Exception("COA {$coa} tidak ditemukan dalam data anggaran.");
        }

        $query = SPP::where('coa', $coa)
            ->where('status', 'Tagihan Belum SP2D')
            ->whereNull('deleted_at');

        if ($excludeSppId) {
            $query->where('id', '!=', $excludeSppId);
        }

        $totalOutstanding = $query->sum('netto');
        $sisaEfektif      = $anggaran->sisa - $totalOutstanding;

        if ($netto > $sisaEfektif) {
            $fmt = fn($v) => 'Rp ' . number_format($v, 0, ',', '.');
            throw new \Exception(
                "Nilai SPP ({$fmt($netto)}) melebihi sisa anggaran efektif ({$fmt($sisaEfektif)}). " .
                    "Sisa: {$fmt($anggaran->sisa)}, Outstanding: {$fmt($totalOutstanding)}"
            );
        }
    }
}
