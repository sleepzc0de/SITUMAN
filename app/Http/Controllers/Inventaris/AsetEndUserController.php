<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\AsetEndUser;
use App\Models\KategoriAset;
use App\Models\Pegawai;
use App\Models\RiwayatAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsetEndUserController extends Controller
{
    public function index(Request $request)
    {
        $query = AsetEndUser::with(['kategori', 'pegawai']);

        // Filter by category
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_aset', 'like', '%' . $request->search . '%')
                    ->orWhere('kode_aset', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_seri', 'like', '%' . $request->search . '%')
                    ->orWhereHas('pegawai', function ($qq) use ($request) {
                        $qq->where('nama', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $aset = $query->latest()->paginate(15);
        $kategoris = KategoriAset::orderBy('nama')->get();

        // Statistics
        $stats = [
            'total_aset' => AsetEndUser::count(),
            'tersedia' => AsetEndUser::where('status', 'tersedia')->count(),
            'dipinjam' => AsetEndUser::where('status', 'dipinjam')->count(),
            'diperbaiki' => AsetEndUser::where('status', 'diperbaiki')->count(),
            'total_nilai' => AsetEndUser::sum('nilai_perolehan'),
        ];

        return view('inventaris.aset-end-user.index', compact('aset', 'kategoris', 'stats'));
    }

    public function create()
    {
        $kategoris = KategoriAset::orderBy('nama')->get();
        return view('inventaris.aset-end-user.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_aset,id',
            'nama_aset' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'merek' => 'nullable|string|max:100',
            'tipe' => 'nullable|string|max:100',
            'nomor_seri' => 'nullable|string|max:100',
            'tanggal_perolehan' => 'nullable|date',
            'nilai_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required|in:baik,rusak ringan,rusak berat,hilang',
            'catatan' => 'nullable|string',
        ]);

        $aset = AsetEndUser::create($validated);

        return redirect()->route('inventaris.aset-end-user.index')
            ->with('success', 'Aset berhasil ditambahkan');
    }

    public function show(AsetEndUser $asetEndUser)
    {
        $asetEndUser->load(['kategori', 'pegawai', 'riwayat.pegawai', 'riwayat.user']);
        return view('inventaris.aset-end-user.show', compact('asetEndUser'));
    }

    public function edit(AsetEndUser $asetEndUser)
    {
        $kategoris = KategoriAset::orderBy('nama')->get();
        return view('inventaris.aset-end-user.edit', compact('asetEndUser', 'kategoris'));
    }

    public function update(Request $request, AsetEndUser $asetEndUser)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_aset,id',
            'nama_aset' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'merek' => 'nullable|string|max:100',
            'tipe' => 'nullable|string|max:100',
            'nomor_seri' => 'nullable|string|max:100',
            'tanggal_perolehan' => 'nullable|date',
            'nilai_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required|in:baik,rusak ringan,rusak berat,hilang',
            'catatan' => 'nullable|string',
        ]);

        $asetEndUser->update($validated);

        return redirect()->route('inventaris.aset-end-user.index')
            ->with('success', 'Aset berhasil diperbarui');
    }

    public function destroy(AsetEndUser $asetEndUser)
    {
        if ($asetEndUser->status === 'dipinjam') {
            return back()->with('error', 'Aset yang sedang dipinjam tidak dapat dihapus');
        }

        $asetEndUser->delete();
        return redirect()->route('inventaris.aset-end-user.index')
            ->with('success', 'Aset berhasil dihapus');
    }

    public function pinjam(Request $request, AsetEndUser $asetEndUser)
    {
        if ($asetEndUser->status !== 'tersedia') {
            return back()->with('error', 'Aset tidak tersedia untuk dipinjam');
        }

        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal_peminjaman' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $asetEndUser->update([
                'pegawai_id' => $validated['pegawai_id'],
                'tanggal_peminjaman' => $validated['tanggal_peminjaman'],
                'status' => 'dipinjam',
                'catatan' => $validated['catatan'],
            ]);

            RiwayatAset::create([
                'aset_id' => $asetEndUser->id,
                'pegawai_id' => $validated['pegawai_id'],
                'user_id' => auth()->id(),
                'jenis_aktivitas' => 'peminjaman',
                'tanggal' => $validated['tanggal_peminjaman'],
                'keterangan' => $validated['catatan'],
            ]);

            DB::commit();
            return back()->with('success', 'Aset berhasil dipinjamkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function kembalikan(Request $request, AsetEndUser $asetEndUser)
    {
        if ($asetEndUser->status !== 'dipinjam') {
            return back()->with('error', 'Aset tidak dalam status dipinjam');
        }

        $validated = $request->validate([
            'kondisi' => 'required|in:baik,rusak ringan,rusak berat,hilang',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $pegawaiId = $asetEndUser->pegawai_id;

            $asetEndUser->update([
                'pegawai_id' => null,
                'tanggal_peminjaman' => null,
                'status' => 'tersedia',
                'kondisi' => $validated['kondisi'],
                'catatan' => $validated['catatan'],
            ]);

            RiwayatAset::create([
                'aset_id' => $asetEndUser->id,
                'pegawai_id' => $pegawaiId,
                'user_id' => auth()->id(),
                'jenis_aktivitas' => 'pengembalian',
                'tanggal' => now(),
                'keterangan' => $validated['catatan'],
            ]);

            DB::commit();
            return back()->with('success', 'Aset berhasil dikembalikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
