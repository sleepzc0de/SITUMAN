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
            'nama'      => 'required|string|min:3|max:100|unique:kategori_aset,nama',
            'deskripsi' => 'nullable|string|max:2000',
        ], [
            'nama.required' => 'Nama kategori harus diisi.',
            'nama.min'      => 'Nama kategori minimal 3 karakter.',
            'nama.max'      => 'Nama kategori maksimal 100 karakter.',
            'nama.unique'   => 'Nama kategori sudah digunakan.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
        ]);

        try {
            KategoriAset::create($validated);
            return redirect()->route('inventaris.kategori-aset.index')
                ->with('success', 'Kategori Aset berhasil ditambahkan');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menambahkan kategori aset.');
            return back()->withInput()->with('error', 'Gagal menambahkan kategori. Silakan coba lagi.');
        }
    }

    public function show(KategoriAset $kategoriAset)
    {
        $aset = $kategoriAset->aset()
            ->with('pegawai')
            ->latest()
            ->paginate(15);

        return view('inventaris.kategori-aset.show', compact('kategoriAset', 'aset'));
    }

    public function edit(KategoriAset $kategoriAset)
    {
        return view('inventaris.kategori-aset.edit', compact('kategoriAset'));
    }

    public function update(Request $request, KategoriAset $kategoriAset)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|min:3|max:100|unique:kategori_aset,nama,' . $kategoriAset->id,
            'deskripsi' => 'nullable|string|max:2000',
        ], [
            'nama.required' => 'Nama kategori harus diisi.',
            'nama.min'      => 'Nama kategori minimal 3 karakter.',
            'nama.max'      => 'Nama kategori maksimal 100 karakter.',
            'nama.unique'   => 'Nama kategori sudah digunakan.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
        ]);

        try {
            $kategoriAset->update($validated);
            return redirect()->route('inventaris.kategori-aset.index')
                ->with('success', 'Kategori Aset berhasil diperbarui');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal memperbarui kategori aset.');
            return back()->withInput()->with('error', 'Gagal memperbarui kategori. Silakan coba lagi.');
        }
    }

    public function destroy(KategoriAset $kategoriAset)
    {
        if ($kategoriAset->aset()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki aset terkait.');
        }

        try {
            $kategoriAset->delete();
            return redirect()->route('inventaris.kategori-aset.index')
                ->with('success', 'Kategori Aset berhasil dihapus');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menghapus kategori aset.');
            return back()->with('error', 'Gagal menghapus kategori. Silakan coba lagi.');
        }
    }
}
