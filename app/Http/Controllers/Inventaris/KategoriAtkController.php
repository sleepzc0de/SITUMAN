<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\KategoriAtk;
use Illuminate\Http\Request;

class KategoriAtkController extends Controller
{
    public function index()
    {
        $kategoris = KategoriAtk::withCount('atk')
            ->withSum('atk', 'stok_tersedia')
            ->with(['atk' => fn($q) => $q->select('kategori_id', 'status')])
            ->latest()
            ->paginate(15);

        // Summary stats
        $totalKategori  = KategoriAtk::count();
        $totalAtk       = \App\Models\Atk::count();
        $totalMenipis   = \App\Models\Atk::where('status', 'menipis')->count();
        $totalKosong    = \App\Models\Atk::where('status', 'kosong')->count();

        return view('inventaris.kategori-atk.index', compact(
            'kategoris', 'totalKategori', 'totalAtk', 'totalMenipis', 'totalKosong'
        ));
    }

    public function create()
    {
        return view('inventaris.kategori-atk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255|unique:kategori_atk,nama',
            'deskripsi' => 'nullable|string',
        ]);

        KategoriAtk::create($validated);

        return redirect()->route('inventaris.kategori-atk.index')
            ->with('success', 'Kategori ATK berhasil ditambahkan.');
    }

    public function show(KategoriAtk $kategoriAtk)
    {
        // Fix: gunakan paginate terpisah, bukan dalam load()
        $atks = $kategoriAtk->atk()->latest()->paginate(10);

        $stats = [
            'total'    => $kategoriAtk->atk()->count(),
            'tersedia' => $kategoriAtk->atk()->where('status', 'tersedia')->count(),
            'menipis'  => $kategoriAtk->atk()->where('status', 'menipis')->count(),
            'kosong'   => $kategoriAtk->atk()->where('status', 'kosong')->count(),
            'nilai'    => $kategoriAtk->atk()->selectRaw('SUM(stok_tersedia * harga_satuan) as total')->value('total') ?? 0,
        ];

        return view('inventaris.kategori-atk.show', compact('kategoriAtk', 'atks', 'stats'));
    }

    public function edit(KategoriAtk $kategoriAtk)
    {
        $kategoriAtk->loadCount('atk');
        return view('inventaris.kategori-atk.edit', compact('kategoriAtk'));
    }

    public function update(Request $request, KategoriAtk $kategoriAtk)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255|unique:kategori_atk,nama,' . $kategoriAtk->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategoriAtk->update($validated);

        return redirect()->route('inventaris.kategori-atk.index')
            ->with('success', 'Kategori ATK berhasil diperbarui.');
    }

    public function destroy(KategoriAtk $kategoriAtk)
    {
        if ($kategoriAtk->atk()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki ' . $kategoriAtk->atk()->count() . ' ATK terkait.');
        }

        $nama = $kategoriAtk->nama;
        $kategoriAtk->delete();

        return redirect()->route('inventaris.kategori-atk.index')
            ->with('success', "Kategori \"{$nama}\" berhasil dihapus.");
    }
}
