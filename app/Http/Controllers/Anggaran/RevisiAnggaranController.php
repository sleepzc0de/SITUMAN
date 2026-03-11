<?php
namespace App\Http\Controllers\Anggaran;
use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\RevisiAnggaran;
use App\Services\AnggaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class RevisiAnggaranController extends Controller
{
    public function __construct(private AnggaranService $anggaranService) {}

    public function index(Request $request)
    {
        // Jika AJAX/JSON request → kembalikan data saja
        if ($request->expectsJson() || $request->ajax()) {
            return $this->fetchData($request);
        }

        $roList      = Anggaran::select('ro')->distinct()->pluck('ro');
        $jenisRevisi = ['POK', 'DIPA', 'Revisi Anggaran', 'Pergeseran'];
        $stats = [
            'total'   => RevisiAnggaran::count(),
            'naik'    => RevisiAnggaran::whereColumn('pagu_sesudah', '>', 'pagu_sebelum')->count(),
            'turun'   => RevisiAnggaran::whereColumn('pagu_sesudah', '<', 'pagu_sebelum')->count(),
            'selisih' => RevisiAnggaran::selectRaw('SUM(pagu_sesudah - pagu_sebelum) as net')->value('net') ?? 0,
        ];

        // Data awal (tanpa filter)
        $revisis = RevisiAnggaran::with(['anggaran', 'user'])
            ->orderBy('tanggal_revisi', 'desc')
            ->paginate(20);

        return view('anggaran.revisi.index', compact('revisis', 'roList', 'jenisRevisi', 'stats'));
    }

    /**
     * Endpoint khusus AJAX filter — dipanggil dari blade via fetch()
     */
    private function fetchData(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = RevisiAnggaran::with(['anggaran', 'user'])->orderBy('tanggal_revisi', 'desc');

        if ($request->filled('jenis_revisi') && $request->jenis_revisi !== 'all') {
            $query->where('jenis_revisi', $request->jenis_revisi);
        }
        if ($request->filled('ro')) {
            $query->whereHas('anggaran', fn($q) => $q->where('ro', $request->ro));
        }

        $revisis = $query->paginate(20)->withQueryString();

        // Stats berdasarkan filter yang sama
        $statsQuery = RevisiAnggaran::query();
        if ($request->filled('jenis_revisi') && $request->jenis_revisi !== 'all') {
            $statsQuery->where('jenis_revisi', $request->jenis_revisi);
        }
        if ($request->filled('ro')) {
            $statsQuery->whereHas('anggaran', fn($q) => $q->where('ro', $request->ro));
        }

        $stats = [
            'total'   => $statsQuery->count(),
            'naik'    => (clone $statsQuery)->whereColumn('pagu_sesudah', '>', 'pagu_sebelum')->count(),
            'turun'   => (clone $statsQuery)->whereColumn('pagu_sesudah', '<', 'pagu_sebelum')->count(),
            'selisih' => (clone $statsQuery)->selectRaw('SUM(pagu_sesudah - pagu_sebelum) as net')->value('net') ?? 0,
        ];

        $rows = $revisis->getCollection()->map(function ($revisi, $index) use ($revisis) {
            $selisih = $revisi->pagu_sesudah - $revisi->pagu_sebelum;
            return [
                'id'               => $revisi->id,
                'no'               => ($revisis->currentPage() - 1) * $revisis->perPage() + $index + 1,
                'tanggal'          => formatTanggalIndo($revisi->tanggal_revisi),
                'user'             => $revisi->user->nama ?? '-',
                'jenis_revisi'     => $revisi->jenis_revisi,
                'program_kegiatan' => truncate_text($revisi->anggaran->program_kegiatan ?? '-', 45),
                'ro'               => $revisi->anggaran->ro ?? '',
                'kode_akun'        => $revisi->anggaran->kode_akun ?? '',
                'pagu_sebelum'     => format_rupiah($revisi->pagu_sebelum),
                'pagu_sesudah'     => format_rupiah($revisi->pagu_sesudah),
                'selisih_raw'      => $selisih,
                'selisih'          => ($selisih > 0 ? '+' : '') . format_rupiah($selisih),
                'selisih_class'    => $selisih > 0 ? 'naik' : ($selisih < 0 ? 'turun' : 'netral'),
                'dokumen'          => $revisi->dokumen_pendukung ? route('anggaran.revisi.download-dokumen', $revisi) : null,
                'show_url'         => route('anggaran.revisi.show', $revisi),
                'delete_url'       => route('anggaran.revisi.destroy', $revisi),
            ];
        });

        return response()->json([
            'rows'         => $rows,
            'total'        => $revisis->total(),
            'stats'        => $stats,
            'pagination'   => $revisis->links()->toHtml(),
            'current_page' => $revisis->currentPage(),
            'last_page'    => $revisis->lastPage(),
        ]);
    }

    public function create()
    {
        $anggarans   = Anggaran::whereNotNull('kode_akun')
            ->orderBy('ro')
            ->orderBy('kode_subkomponen')
            ->orderBy('kode_akun')
            ->get();
        $jenisRevisi = ['POK', 'DIPA', 'Revisi Anggaran', 'Pergeseran'];
        return view('anggaran.revisi.create', compact('anggarans', 'jenisRevisi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anggaran_id'       => 'required|exists:anggaran,id',
            'jenis_revisi'      => 'required|string|in:POK,DIPA,Revisi Anggaran,Pergeseran',
            'pagu_sesudah'      => 'required|numeric|min:0',
            'alasan_revisi'     => 'required|string|max:1000',
            'tanggal_revisi'    => 'required|date',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $anggaran = Anggaran::findOrFail($validated['anggaran_id']);

        if (!$anggaran->kode_akun) {
            return back()->with('error', 'Revisi hanya dapat dilakukan pada level Akun.');
        }

        $validated['pagu_sebelum'] = $anggaran->pagu_anggaran;
        $validated['user_id']      = Auth::id();

        if ($request->hasFile('dokumen_pendukung')) {
            $file     = $request->file('dokumen_pendukung');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $validated['dokumen_pendukung'] = $file->storeAs('revisi-anggaran', $filename, 'public');
        }

        DB::beginTransaction();
        try {
            RevisiAnggaran::create($validated);
            $this->anggaranService->updatePaguFromRevisi($anggaran, (float) $validated['pagu_sesudah']);
            DB::commit();
            return redirect()->route('anggaran.revisi.index')
                ->with('success', 'Revisi anggaran berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan revisi: ' . $e->getMessage());
        }
    }

    public function show(RevisiAnggaran $revisi)
    {
        $revisi->load(['anggaran', 'user']);
        return view('anggaran.revisi.show', compact('revisi'));
    }

    public function edit(RevisiAnggaran $revisi)
    {
        return view('anggaran.revisi.edit', compact('revisi'));
    }

    public function update(Request $request, RevisiAnggaran $revisi)
    {
        return redirect()->route('anggaran.revisi.index')
            ->with('error', 'Revisi anggaran tidak dapat diedit.');
    }

    public function destroy(RevisiAnggaran $revisi)
    {
        $isLatest = RevisiAnggaran::where('anggaran_id', $revisi->anggaran_id)
            ->latest()
            ->first()
            ->id === $revisi->id;

        if (!$isLatest) {
            return back()->with('error', 'Hanya revisi terbaru yang dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $anggaran = $revisi->anggaran;
            $this->anggaranService->updatePaguFromRevisi($anggaran, $revisi->pagu_sebelum);
            if ($revisi->dokumen_pendukung) {
                Storage::disk('public')->delete($revisi->dokumen_pendukung);
            }
            $revisi->delete();
            DB::commit();
            return redirect()->route('anggaran.revisi.index')
                ->with('success', 'Revisi berhasil dibatalkan dan pagu dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus revisi: ' . $e->getMessage());
        }
    }

    public function downloadDokumen(RevisiAnggaran $revisi)
    {
        if (!$revisi->dokumen_pendukung || !Storage::disk('public')->exists($revisi->dokumen_pendukung)) {
            return back()->with('error', 'Dokumen tidak ditemukan');
        }
        return Storage::disk('public')->download($revisi->dokumen_pendukung);
    }
}
