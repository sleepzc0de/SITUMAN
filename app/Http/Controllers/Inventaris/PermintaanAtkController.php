<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Atk;
use App\Models\Pegawai;
use App\Models\PermintaanAtk;
use App\Models\PermintaanAtkDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanAtkController extends Controller
{
    public function index(Request $request)
    {
        $query = PermintaanAtk::with(['user', 'pegawai', 'details.atk']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_permintaan', 'like', '%' . $request->search . '%')
                    ->orWhereHas('pegawai', function ($qq) use ($request) {
                        $qq->where('nama', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Filter by date
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_sampai);
        }

        $permintaan = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total' => PermintaanAtk::count(),
            'pending' => PermintaanAtk::where('status', 'pending')->count(),
            'disetujui' => PermintaanAtk::where('status', 'disetujui')->count(),
            'ditolak' => PermintaanAtk::where('status', 'ditolak')->count(),
            'selesai' => PermintaanAtk::where('status', 'selesai')->count(),
        ];

        return view('inventaris.permintaan-atk.index', compact('permintaan', 'stats'));
    }

    public function create()
    {
        $pegawai = Pegawai::orderBy('nama')->get();
        $atk = Atk::where('status', '!=', 'kosong')
            ->orderBy('nama')
            ->get();

        return view('inventaris.permintaan-atk.create', compact('pegawai', 'atk'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal_permintaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'atk_id' => 'required|array|min:1',
            'atk_id.*' => 'required|exists:atk,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'keterangan_item' => 'nullable|array',
            'keterangan_item.*' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $permintaan = PermintaanAtk::create([
                'user_id' => auth()->id(),
                'pegawai_id' => $validated['pegawai_id'],
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'keterangan' => $validated['keterangan'],
                'status' => 'pending',
            ]);

            foreach ($validated['atk_id'] as $index => $atkId) {
                PermintaanAtkDetail::create([
                    'permintaan_id' => $permintaan->id,
                    'atk_id' => $atkId,
                    'jumlah' => $validated['jumlah'][$index],
                    'keterangan' => $validated['keterangan_item'][$index] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventaris.permintaan-atk.index')
                ->with('success', 'Permintaan ATK berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(PermintaanAtk $permintaanAtk)
    {
        $permintaanAtk->load(['user', 'pegawai', 'penyetuju', 'details.atk.kategori']);
        return view('inventaris.permintaan-atk.show', compact('permintaanAtk'));
    }

    public function edit(PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan dengan status pending yang dapat diedit');
        }

        $pegawai = Pegawai::orderBy('nama')->get();
        $atk = Atk::where('status', '!=', 'kosong')->orderBy('nama')->get();
        $permintaanAtk->load('details.atk');

        return view('inventaris.permintaan-atk.edit', compact('permintaanAtk', 'pegawai', 'atk'));
    }

    public function update(Request $request, PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan dengan status pending yang dapat diedit');
        }

        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal_permintaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'atk_id' => 'required|array|min:1',
            'atk_id.*' => 'required|exists:atk,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'keterangan_item' => 'nullable|array',
            'keterangan_item.*' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $permintaanAtk->update([
                'pegawai_id' => $validated['pegawai_id'],
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'keterangan' => $validated['keterangan'],
            ]);

            // Delete old details
            $permintaanAtk->details()->delete();

            // Create new details
            foreach ($validated['atk_id'] as $index => $atkId) {
                PermintaanAtkDetail::create([
                    'permintaan_id' => $permintaanAtk->id,
                    'atk_id' => $atkId,
                    'jumlah' => $validated['jumlah'][$index],
                    'keterangan' => $validated['keterangan_item'][$index] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventaris.permintaan-atk.index')
                ->with('success', 'Permintaan ATK berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(PermintaanAtk $permintaanAtk)
    {
        if (!in_array($permintaanAtk->status, ['pending', 'ditolak'])) {
            return back()->with('error', 'Permintaan yang sudah disetujui atau selesai tidak dapat dihapus');
        }

        $permintaanAtk->delete();
        return redirect()->route('inventaris.permintaan-atk.index')
            ->with('success', 'Permintaan ATK berhasil dihapus');
    }

    public function approve(Request $request, PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses');
        }

        DB::beginTransaction();
        try {
            // Check stock availability
            foreach ($permintaanAtk->details as $detail) {
                if ($detail->atk->stok_tersedia < $detail->jumlah) {
                    return back()->with('error',
                        'Stok ' . $detail->atk->nama . ' tidak mencukupi. Tersedia: ' .
                        $detail->atk->stok_tersedia . ' ' . $detail->atk->satuan
                    );
                }
            }

            // Approve and reduce stock
            $permintaanAtk->update([
                'status' => 'disetujui',
                'disetujui_oleh' => auth()->id(),
                'tanggal_disetujui' => now(),
            ]);

            foreach ($permintaanAtk->details as $detail) {
                $atk = $detail->atk;
                $atk->stok_tersedia -= $detail->jumlah;
                $atk->save();
                $atk->updateStatus();
            }

            DB::commit();
            return back()->with('success', 'Permintaan ATK berhasil disetujui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $permintaanAtk->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $validated['alasan_penolakan'],
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

        return back()->with('success', 'Permintaan ATK berhasil ditolak');
    }

    public function complete(PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'disetujui') {
            return back()->with('error', 'Hanya permintaan yang disetujui yang dapat diselesaikan');
        }

        $permintaanAtk->update(['status' => 'selesai']);
        return back()->with('success', 'Permintaan ATK ditandai sebagai selesai');
    }
}
