<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\KategoriAset;
use Illuminate\Http\Request;

class KategoriAsetController extends Controller
{
    public function index()
    {
        $kategoris = KategoriAset::withCount('aset')->latest()->paginate(15);

        return view('inventaris.kategori-aset.index', compact('kategoris'));
    }

    public function create()
    {
        return view('inventaris.kategori-aset.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_aset,nama',
            'deskripsi' => 'nullable|string',
        ]);

        KategoriAset::create($validated);

        return redirect()->route('inventaris.kategori-aset.index')
            ->with('success', 'Kategori Aset berhasil ditambahkan');
    }

    public function show(KategoriAset $kategoriAset)
    {
        $kategoriAset->load(['aset' => function($query) {
            $query->latest()->paginate(10);
        }]);

        return view('inventaris.kategori-aset.show', compact('kategoriAset'));
    }

    public function edit(KategoriAset $kategoriAset)
    {
        return view('inventaris.kategori-aset.edit', compact('kategoriAset'));
    }

    public function update(Request $request, KategoriAset $kategoriAset)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_aset,nama,' . $kategoriAset->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategoriAset->update($validated);

        return redirect()->route('inventaris.kategori-aset.index')
            ->with('success', 'Kategori Aset berhasil diperbarui');
    }

    public function destroy(KategoriAset $kategoriAset)
    {
        // Check if kategori has related Aset
        if ($kategoriAset->aset()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki aset terkait');
        }

        $kategoriAset->delete();

        return redirect()->route('inventaris.kategori-aset.index')
            ->with('success', 'Kategori Aset berhasil dihapus');
    }
}
