<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Atk;
use App\Models\KategoriAtk;
use Illuminate\Http\Request;

class MonitoringAtkController extends Controller
{
    public function index(Request $request)
    {
        $query = Atk::with('kategori');

        // Filter by category
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('kode_atk', 'like', '%' . $request->search . '%');
            });
        }

        $atk = $query->latest()->paginate(15);
        $kategoris = KategoriAtk::orderBy('nama')->get();

        // Statistics
        $stats = [
            'total_item' => Atk::count(),
            'stok_tersedia' => Atk::where('status', 'tersedia')->count(),
            'stok_menipis' => Atk::where('status', 'menipis')->count(),
            'stok_kosong' => Atk::where('status', 'kosong')->count(),
            'total_nilai' => Atk::sum('harga_satuan'),
        ];

        return view('inventaris.monitoring-atk.index', compact('atk', 'kategoris', 'stats'));
    }

    public function create()
    {
        $kategoris = KategoriAtk::orderBy('nama')->get();
        return view('inventaris.monitoring-atk.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_atk,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'stok_minimum' => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $atk = Atk::create($validated);
        $atk->updateStatus();

        return redirect()->route('inventaris.monitoring-atk.index')
            ->with('success', 'ATK berhasil ditambahkan');
    }

    public function show(Atk $monitoringAtk)
    {
        $monitoringAtk->load('kategori', 'permintaanDetail.permintaan.pegawai');
        return view('inventaris.monitoring-atk.show', compact('monitoringAtk'));
    }

    public function edit(Atk $monitoringAtk)
    {
        $kategoris = KategoriAtk::orderBy('nama')->get();
        return view('inventaris.monitoring-atk.edit', compact('monitoringAtk', 'kategoris'));
    }

    public function update(Request $request, Atk $monitoringAtk)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_atk,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'stok_minimum' => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $monitoringAtk->update($validated);
        $monitoringAtk->updateStatus();

        return redirect()->route('inventaris.monitoring-atk.index')
            ->with('success', 'ATK berhasil diperbarui');
    }

    public function destroy(Atk $monitoringAtk)
    {
        $monitoringAtk->delete();
        return redirect()->route('inventaris.monitoring-atk.index')
            ->with('success', 'ATK berhasil dihapus');
    }

    public function updateStok(Request $request, Atk $monitoringAtk)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:tambah,kurang',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        if ($validated['jenis'] === 'tambah') {
            $monitoringAtk->stok_tersedia += $validated['jumlah'];
        } else {
            if ($monitoringAtk->stok_tersedia < $validated['jumlah']) {
                return back()->with('error', 'Stok tidak mencukupi');
            }
            $monitoringAtk->stok_tersedia -= $validated['jumlah'];
        }

        $monitoringAtk->save();
        $monitoringAtk->updateStatus();

        return back()->with('success', 'Stok berhasil diperbarui');
    }
}
