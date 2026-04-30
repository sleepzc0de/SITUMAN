<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\SPP;
use App\Services\AnggaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $search = mb_substr(trim($request->search), 0, self::MAX_SEARCH);
            $query->where(function ($q) use ($search) {
                $q->where('no_spp', 'like', "%{$search}%")
                    ->orWhere('uraian_spp', 'like', "%{$search}%")
                    ->orWhere('nama_pic', 'like', "%{$search}%");
            });
        }

        $spps      = $query->orderBy('tgl_spp', 'desc')->paginate(20)->withQueryString();
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
        $roList    = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');

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

        if ($request->ajax()) {
            return response()->json([
                'table'  => view('anggaran.spp._table_content', compact('spps'))->render(),
                'stats'  => [
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
        $roList       = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');
        $bulanList    = [
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
            $coa = $validated['kode_kegiatan'] . $validated['kro'] . $validated['ro'] . $validated['mak'];
            $this->validateSisaAnggaran($coa, (float) $validated['netto']);

            $spp = SPP::create($validated);
            $this->anggaranService->syncFromSPP($spp->coa);

            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil ditambahkan.');
        } catch (\InvalidArgumentException $e) {
            // Validasi bisnis (sisa anggaran tidak cukup) — pesan aman untuk ditampilkan
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal menambahkan data SPP.', ['action' => 'store']);
            return back()->withInput()->with('error', 'Gagal menambahkan data SPP. Silakan coba lagi.');
        }
    }

    public function show(SPP $spp)
    {
        $anggaran = Anggaran::whereNotNull('kode_akun')
            ->whereRaw("CONCAT(kegiatan, kro, ro, kode_akun) = ?", [$spp->coa])
            ->first();

        return view('anggaran.spp.show', compact('spp', 'anggaran'));
    }

    public function edit(SPP $spp)
    {
        $roList      = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');
        $bulanList   = [
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

            $this->validateSisaAnggaran($newCoa, (float) $validated['netto'], $spp->id);

            $spp->update($validated);

            if ($spp->coa !== $newCoa) {
                $spp->update(['coa' => $newCoa]);
            }

            if ($oldCoa !== $newCoa) {
                $this->anggaranService->syncFromSPP($oldCoa);
            }
            $this->anggaranService->syncFromSPP($newCoa);

            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil diupdate.');
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal mengupdate data SPP.', ['action' => 'update', 'spp_id' => $spp->id]);
            return back()->withInput()->with('error', 'Gagal mengupdate data SPP. Silakan coba lagi.');
        }
    }

    public function destroy(SPP $spp)
    {
        DB::beginTransaction();
        try {
            $coa = $spp->coa;
            $spp->delete();
            $this->anggaranService->syncFromSPP($coa);
            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal menghapus data SPP.', ['action' => 'destroy', 'spp_id' => $spp->id]);
            return back()->with('error', 'Gagal menghapus data SPP. Silakan coba lagi.');
        }
    }

    public function getSubkomponen(Request $request)
    {
        try {
            if (!$request->ro) {
                return response()->json(['error' => 'RO harus diisi.'], 400);
            }

            $subkomponens = Anggaran::where('ro', $request->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan']);

            return response()->json($subkomponens);
        } catch (\Exception $e) {
            return $this->handleExceptionJson($e, 'Gagal mengambil data subkomponen.', 500, [
                'action' => 'getSubkomponen',
            ]);
        }
    }

    public function getAkun(Request $request)
    {
        try {
            if (!$request->ro || !$request->subkomponen) {
                return response()->json(['error' => 'RO dan Subkomponen harus diisi.'], 400);
            }

            $akuns = Anggaran::where('ro', $request->ro)
                ->where('kode_subkomponen', $request->subkomponen)
                ->whereNotNull('kode_akun')
                ->orderBy('kode_akun')
                ->get(['kode_akun', 'kegiatan', 'kro', 'program_kegiatan', 'pagu_anggaran', 'sisa', 'tagihan_outstanding']);

            $akuns->transform(function ($item) {
                $item->sisa_efektif = $item->sisa - $item->tagihan_outstanding;
                return $item;
            });

            return response()->json($akuns);
        } catch (\Exception $e) {
            return $this->handleExceptionJson($e, 'Gagal mengambil data akun.', 500, [
                'action' => 'getAkun',
            ]);
        }
    }

    // ── Private Methods ──────────────────────────────────────────

    private const MAX_NO_SPP            = 100;
    private const MAX_NOMINATIF         = 255;
    private const MAX_JENIS_KEGIATAN    = 255;
    private const MAX_JENIS_BELANJA     = 50;
    private const MAX_NOMOR_KONTRAK     = 255;
    private const MAX_NO_BAST           = 255;
    private const MAX_ID_EPERJADIN      = 255;
    private const MAX_URAIAN_SPP        = 5000;
    private const MAX_BAGIAN            = 255;
    private const MAX_NAMA_PIC          = 255;
    private const MAX_KODE_KEGIATAN     = 50;
    private const MAX_KRO               = 50;
    private const MAX_RO                = 50;
    private const MAX_SUB_KOMPONEN      = 255;
    private const MAX_MAK               = 50;
    private const MAX_NOMOR_SURAT_TUGAS = 255;
    private const MAX_NOMOR_UNDANGAN    = 255;
    private const MAX_LS_BENDAHARA      = 50;
    private const MAX_STAFF_PPK         = 255;
    private const MAX_NO_SP2D           = 255;
    private const MAX_POSISI_UANG       = 255;
    private const MAX_NETTO             = 999999999999;
    private const MAX_SEARCH            = 200;

    private function getValidationRules(Request $request, ?string $sppId = null): array
    {
        return $request->validate([
            'bulan'             => 'required|string|max:20|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'no_spp'            => 'required|string|max:' . self::MAX_NO_SPP . '|unique:spp,no_spp' . ($sppId ? ",{$sppId}" : ''),
            'nominatif'         => 'nullable|string|max:' . self::MAX_NOMINATIF,
            'tgl_spp'           => 'required|date',
            'jenis_kegiatan'    => 'required|string|max:' . self::MAX_JENIS_KEGIATAN,
            'jenis_belanja'     => 'required|string|max:' . self::MAX_JENIS_BELANJA . '|in:Kontraktual,Non Kontraktual,GUP,TUP',
            'nomor_kontrak'     => 'nullable|string|max:' . self::MAX_NOMOR_KONTRAK,
            'no_bast'           => 'nullable|string|max:' . self::MAX_NO_BAST,
            'id_eperjadin'      => 'nullable|string|max:' . self::MAX_ID_EPERJADIN,
            'uraian_spp'        => 'required|string|max:' . self::MAX_URAIAN_SPP,
            'bagian'            => 'required|string|max:' . self::MAX_BAGIAN,
            'nama_pic'          => 'required|string|max:' . self::MAX_NAMA_PIC,
            'kode_kegiatan'     => 'required|string|max:' . self::MAX_KODE_KEGIATAN,
            'kro'               => 'required|string|max:' . self::MAX_KRO,
            'ro'                => 'required|string|max:' . self::MAX_RO,
            'sub_komponen'      => 'required|string|max:' . self::MAX_SUB_KOMPONEN,
            'mak'               => 'required|string|max:' . self::MAX_MAK,
            'nomor_surat_tugas' => 'nullable|string|max:' . self::MAX_NOMOR_SURAT_TUGAS,
            'tanggal_st'        => 'nullable|date',
            'nomor_undangan'    => 'nullable|string|max:' . self::MAX_NOMOR_UNDANGAN,
            'bruto'             => 'required|numeric|min:0|max:' . self::MAX_NETTO,
            'ppn'               => 'nullable|numeric|min:0|max:' . self::MAX_NETTO,
            'pph'               => 'nullable|numeric|min:0|max:' . self::MAX_NETTO,
            'netto'             => 'required|numeric|min:0|max:' . self::MAX_NETTO,
            'tanggal_mulai'     => 'nullable|date',
            'tanggal_selesai'   => 'nullable|date|after_or_equal:tanggal_mulai',
            'ls_bendahara'      => 'required|string|max:' . self::MAX_LS_BENDAHARA . '|in:LS,Bendahara',
            'staff_ppk'         => 'nullable|string|max:' . self::MAX_STAFF_PPK,
            'no_sp2d'           => 'nullable|string|max:' . self::MAX_NO_SP2D,
            'tgl_selesai_sp2d'  => 'nullable|date',
            'tgl_sp2d'          => 'nullable|date',
            'status'            => 'required|max:50|in:Tagihan Telah SP2D,Tagihan Belum SP2D',
            'posisi_uang'       => 'nullable|string|max:' . self::MAX_POSISI_UANG,
        ]);
    }

    /**
     * Validasi sisa anggaran — gunakan InvalidArgumentException
     * agar pesan bisnis bisa ditampilkan ke user (bukan Exception generik).
     */
    private function validateSisaAnggaran(string $coa, float $netto, ?string $excludeSppId = null): void
    {
        // ── Fix SQL Server: gunakan string concatenation yang benar ──
        $anggaran = Anggaran::whereNotNull('kode_akun')
            ->whereRaw("kegiatan + kro + ro + kode_akun = ?", [$coa])
            ->first();

        if (!$anggaran) {
            throw new \InvalidArgumentException(
                "COA tidak ditemukan dalam data anggaran. Pastikan data anggaran sudah diinput."
            );
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
            throw new \InvalidArgumentException(
                "Nilai SPP ({$fmt($netto)}) melebihi sisa anggaran efektif ({$fmt($sisaEfektif)}). " .
                    "Sisa: {$fmt($anggaran->sisa)}, Outstanding: {$fmt($totalOutstanding)}."
            );
        }
    }
}
