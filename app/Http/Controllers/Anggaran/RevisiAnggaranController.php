<?php
// app/Http/Controllers/Anggaran/RevisiAnggaranController.php

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
        $query = RevisiAnggaran::with(['anggaran', 'user'])->orderBy('tanggal_revisi', 'desc');

        if ($request->filled('jenis_revisi') && $request->jenis_revisi !== 'all') {
            $query->where('jenis_revisi', $request->jenis_revisi);
        }

        if ($request->filled('ro')) {
            $query->whereHas('anggaran', fn($q) => $q->where('ro', $request->ro));
        }

        $revisis  = $query->paginate(20);
        $roList   = Anggaran::select('ro')->distinct()->pluck('ro');
        $jenisRevisi = ['POK', 'DIPA', 'Revisi Anggaran', 'Pergeseran'];

        return view('anggaran.revisi.index', compact('revisis', 'roList', 'jenisRevisi'));
    }

    public function create()
    {
        // Hanya tampilkan level Akun (yang punya pagu aktual)
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

        // Pastikan hanya level Akun yang bisa direvisi
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

            // Update pagu via service (propagate otomatis)
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
        // Revisi tidak boleh diedit setelah dibuat (audit trail)
        return redirect()->route('anggaran.revisi.index')
            ->with('error', 'Revisi anggaran tidak dapat diedit untuk menjaga audit trail.');
    }

    public function update(Request $request, RevisiAnggaran $revisi)
    {
        return redirect()->route('anggaran.revisi.index')
            ->with('error', 'Revisi anggaran tidak dapat diedit.');
    }

    public function destroy(RevisiAnggaran $revisi)
    {
        // Hanya superadmin yang bisa hapus, dan hanya jika revisi terbaru
        $isLatest = RevisiAnggaran::where('anggaran_id', $revisi->anggaran_id)
            ->latest()
            ->first()
            ->id === $revisi->id;

        if (!$isLatest) {
            return back()->with('error', 'Hanya revisi terbaru yang dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            // Rollback ke pagu sebelum revisi ini
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
