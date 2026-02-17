<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\KategoriAtk;
use Illuminate\Http\Request;

class KategoriAtkController extends Controller
{
    public function index()
    {
        $kategoris = KategoriAtk::withCount('atk')->latest()->paginate(15);

        return view('inventaris.kategori-atk.index', compact('kategoris'));
    }

    public function create()
    {
        return view('inventaris.kategori-atk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_atk,nama',
            'deskripsi' => 'nullable|string',
        ]);

        KategoriAtk::create($validated);

        return redirect()->route('inventaris.kategori-atk.index')
            ->with('success', 'Kategori ATK berhasil ditambahkan');
    }

    public function show(KategoriAtk $kategoriAtk)
    {
        $kategoriAtk->load(['atk' => function($query) {
            $query->latest()->paginate(10);
        }]);

        return view('inventaris.kategori-atk.show', compact('kategoriAtk'));
    }

    public function edit(KategoriAtk $kategoriAtk)
    {
        return view('inventaris.kategori-atk.edit', compact('kategoriAtk'));
    }

    public function update(Request $request, KategoriAtk $kategoriAtk)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_atk,nama,' . $kategoriAtk->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategoriAtk->update($validated);

        return redirect()->route('inventaris.kategori-atk.index')
            ->with('success', 'Kategori ATK berhasil diperbarui');
    }

    public function destroy(KategoriAtk $kategoriAtk)
    {
        // Check if kategori has related ATK
        if ($kategoriAtk->atk()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki ATK terkait');
        }

        $kategoriAtk->delete();

        return redirect()->route('inventaris.kategori-atk.index')
            ->with('success', 'Kategori ATK berhasil dihapus');
    }
}
