<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\UsulanPenarikan;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UsulanPenarikanController extends Controller
{
    public function index(Request $request)
    {
        $query = UsulanPenarikan::with('user')->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('bulan') && $request->bulan !== 'all') {
            $query->where('bulan', $request->bulan);
        }

        $usulans = $query->paginate(20);

        return view('anggaran.usulan.index', compact('usulans'));
    }

    public function create()
    {
        $roList = Anggaran::select('ro')->distinct()->pluck('ro');
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

        return view('anggaran.usulan.create', compact('roList', 'bulanList'));
    }

    // Tambahkan method baru untuk AJAX
    public function getSubkomponen(Request $request)
    {
        try {
            Log::info('Usulan getSubkomponen called', ['ro' => $request->ro]);

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
            Log::error('Usulan getSubkomponen error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ro' => 'required|string',
            'sub_komponen' => 'required|string',
            'bulan' => 'required|string',
            'nilai_usulan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        UsulanPenarikan::create($validated);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil diajukan');
    }

    public function show(UsulanPenarikan $usulan)
    {
        return view('anggaran.usulan.show', compact('usulan'));
    }

    public function edit(UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return redirect()->route('anggaran.usulan.index')
                ->with('error', 'Usulan yang sudah diproses tidak dapat diedit');
        }

        $roList = Anggaran::select('ro')->distinct()->pluck('ro');
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

        return view('anggaran.usulan.edit', compact('usulan', 'roList', 'bulanList'));
    }

    public function update(Request $request, UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return redirect()->route('anggaran.usulan.index')
                ->with('error', 'Usulan yang sudah diproses tidak dapat diedit');
        }

        $validated = $request->validate([
            'ro' => 'required|string',
            'sub_komponen' => 'required|string',
            'bulan' => 'required|string',
            'nilai_usulan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

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

    public function approve(UsulanPenarikan $usulan)
    {
        $usulan->update(['status' => 'approved']);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil disetujui');
    }

    public function reject(Request $request, UsulanPenarikan $usulan)
    {
        $usulan->update([
            'status' => 'rejected',
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil ditolak');
    }
}
