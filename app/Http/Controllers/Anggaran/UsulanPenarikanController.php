<?php
// app/Http/Controllers/Anggaran/UsulanPenarikanController.php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\UsulanPenarikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UsulanPenarikanController extends Controller
{
    public function index(Request $request)
    {
        $query = UsulanPenarikan::with(['user', 'anggaran'])->orderBy('created_at', 'desc');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('bulan') && $request->bulan !== 'all') {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('ro') && $request->ro !== 'all') {
            $query->where('ro', $request->ro);
        }

        $usulans = $query->paginate(20);

        // Summary statistik
        $summary = [
            'pending'  => UsulanPenarikan::where('status', 'pending')->sum('nilai_usulan'),
            'approved' => UsulanPenarikan::where('status', 'approved')->sum('nilai_usulan'),
            'rejected' => UsulanPenarikan::where('status', 'rejected')->count(),
        ];

        $roList   = Anggaran::select('ro')->distinct()->pluck('ro');
        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        return view('anggaran.usulan.index', compact('usulans', 'summary', 'roList', 'bulanList'));
    }

    public function create()
    {
        $roList   = Anggaran::select('ro')->distinct()->pluck('ro');
        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        return view('anggaran.usulan.create', compact('roList', 'bulanList'));
    }

    public function getSubkomponen(Request $request)
    {
        try {
            if (!$request->ro) {
                return response()->json(['error' => 'RO harus diisi'], 400);
            }

            // Kembalikan info sisa anggaran subkomponen untuk referensi user
            $subkomponens = Anggaran::where('ro', $request->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan', 'pagu_anggaran', 'sisa', 'total_penyerapan']);

            return response()->json($subkomponens);

        } catch (\Exception $e) {
            Log::error('Usulan getSubkomponen error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ro'            => 'required|string|max:50',
            'sub_komponen'  => 'required|string|max:255',
            'anggaran_id'   => 'nullable|exists:anggaran,id',
            'bulan'         => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'nilai_usulan'  => 'required|numeric|min:1',
            'keterangan'    => 'nullable|string|max:500',
        ]);

        // Validasi nilai usulan tidak melebihi sisa subkomponen
        $subkomp = Anggaran::where('ro', $validated['ro'])
            ->where('kode_subkomponen', $validated['sub_komponen'])
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp && $validated['nilai_usulan'] > $subkomp->sisa) {
            return back()->withInput()->with(
                'error',
                'Nilai usulan (Rp ' . number_format($validated['nilai_usulan'], 0, ',', '.') . ') ' .
                'melebihi sisa anggaran subkomponen (Rp ' . number_format($subkomp->sisa, 0, ',', '.') . ')'
            );
        }

        // Auto-set anggaran_id dari SubKomponen jika tidak diisi
        if (empty($validated['anggaran_id']) && $subkomp) {
            $validated['anggaran_id'] = $subkomp->id;
        }

        $validated['user_id'] = Auth::id();
        $validated['status']  = 'pending';

        UsulanPenarikan::create($validated);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil diajukan');
    }

    public function show(UsulanPenarikan $usulan)
    {
        $usulan->load(['user', 'anggaran']);

        // Ambil info anggaran terkait untuk konteks
        $anggaranSubkomp = null;
        if ($usulan->anggaran_id) {
            $anggaranSubkomp = $usulan->anggaran;
        } else {
            $anggaranSubkomp = Anggaran::where('ro', $usulan->ro)
                ->where('kode_subkomponen', $usulan->sub_komponen)
                ->whereNull('kode_akun')
                ->first();
        }

        return view('anggaran.usulan.show', compact('usulan', 'anggaranSubkomp'));
    }

    public function edit(UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return redirect()->route('anggaran.usulan.index')
                ->with('error', 'Usulan yang sudah diproses tidak dapat diedit');
        }

        $roList   = Anggaran::select('ro')->distinct()->pluck('ro');
        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        return view('anggaran.usulan.edit', compact('usulan', 'roList', 'bulanList'));
    }

    public function update(Request $request, UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return redirect()->route('anggaran.usulan.index')
                ->with('error', 'Usulan yang sudah diproses tidak dapat diedit');
        }

        $validated = $request->validate([
            'ro'           => 'required|string|max:50',
            'sub_komponen' => 'required|string|max:255',
            'bulan'        => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'nilai_usulan' => 'required|numeric|min:1',
            'keterangan'   => 'nullable|string|max:500',
        ]);

        // Re-validasi sisa
        $subkomp = Anggaran::where('ro', $validated['ro'])
            ->where('kode_subkomponen', $validated['sub_komponen'])
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp && $validated['nilai_usulan'] > $subkomp->sisa) {
            return back()->withInput()->with(
                'error',
                'Nilai usulan melebihi sisa anggaran subkomponen (Rp ' .
                number_format($subkomp->sisa, 0, ',', '.') . ')'
            );
        }

        // Update anggaran_id jika RO/subkomponen berubah
        if ($subkomp) {
            $validated['anggaran_id'] = $subkomp->id;
        }

        $usulan->update($validated);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil diupdate');
    }

    public function destroy(UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return redirect()->route('anggaran.usulan.index')
                ->with('error', 'Usulan yang sudah diproses tidak dapat dihapus');
        }

        $usulan->delete();

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil dihapus');
    }

    public function approve(Request $request, UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return back()->with('error', 'Usulan sudah diproses sebelumnya');
        }

        $usulan->update([
            'status'     => 'approved',
            'keterangan' => $request->get('catatan', $usulan->keterangan),
        ]);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil disetujui');
    }

    public function reject(Request $request, UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return back()->with('error', 'Usulan sudah diproses sebelumnya');
        }

        $request->validate([
            'keterangan' => 'required|string|max:500',
        ]);

        $usulan->update([
            'status'     => 'rejected',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil ditolak');
    }
}
