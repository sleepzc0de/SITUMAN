<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\RevisiAnggaran;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RevisiAnggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = RevisiAnggaran::with(['anggaran', 'user'])->orderBy('tanggal_revisi', 'desc');

        if ($request->has('jenis_revisi') && $request->jenis_revisi !== 'all') {
            $query->where('jenis_revisi', $request->jenis_revisi);
        }

        $revisis = $query->paginate(20);

        return view('anggaran.revisi.index', compact('revisis'));
    }

    public function create()
    {
        $anggarans = Anggaran::whereNotNull('kode_akun')
            ->orderBy('ro')
            ->orderBy('kode_subkomponen')
            ->get();

        $jenisRevisi = ['POK', 'DIPA', 'Revisi Anggaran', 'Pergeseran'];

        return view('anggaran.revisi.create', compact('anggarans', 'jenisRevisi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anggaran_id' => 'required|exists:anggaran,id',
            'jenis_revisi' => 'required|string',
            'pagu_sesudah' => 'required|numeric|min:0',
            'alasan_revisi' => 'required|string',
            'tanggal_revisi' => 'required|date',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $anggaran = Anggaran::findOrFail($validated['anggaran_id']);
        $validated['pagu_sebelum'] = $anggaran->pagu_anggaran;
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('dokumen_pendukung')) {
            $file = $request->file('dokumen_pendukung');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('revisi-anggaran', $filename, 'public');
            $validated['dokumen_pendukung'] = $path;
        }

        DB::beginTransaction();
        try {
            RevisiAnggaran::create($validated);

            // Update pagu anggaran
            $selisih = $validated['pagu_sesudah'] - $validated['pagu_sebelum'];
            $anggaran->pagu_anggaran = $validated['pagu_sesudah'];
            $anggaran->sisa = $anggaran->sisa + $selisih;
            $anggaran->save();

            // Update parent rows
            $this->updateParentPagu($anggaran, $selisih);

            DB::commit();

            return redirect()->route('anggaran.revisi.index')
                ->with('success', 'Revisi anggaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan revisi anggaran: ' . $e->getMessage());
        }
    }

    public function show(RevisiAnggaran $revisi)
    {
        return view('anggaran.revisi.show', compact('revisi'));
    }

    public function downloadDokumen(RevisiAnggaran $revisi)
    {
        if (!$revisi->dokumen_pendukung || !Storage::disk('public')->exists($revisi->dokumen_pendukung)) {
            return redirect()->route('anggaran.revisi.index')
                ->with('error', 'Dokumen tidak ditemukan');
        }

        return Storage::disk('public')->download($revisi->dokumen_pendukung);
    }

    private function updateParentPagu(Anggaran $anggaran, $selisih)
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
}
