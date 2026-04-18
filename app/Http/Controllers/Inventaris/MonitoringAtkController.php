<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Atk;
use App\Models\KategoriAtk;
use Illuminate\Http\Request;
use App\Exports\AtkExport;
use App\Exports\AtkTemplateExport;
use App\Imports\AtkImport;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringAtkController extends Controller
{
    public function index(Request $request)
    {
        $query = Atk::with('kategori');

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('kode_atk', 'like', '%' . $request->search . '%');
            });
        }

        $atk       = $query->latest()->paginate(15);
        $kategoris = KategoriAtk::orderBy('nama')->get();
        $stats = [
            'total_item'    => Atk::count(),
            'stok_tersedia' => Atk::where('status', 'tersedia')->count(),
            'stok_menipis'  => Atk::where('status', 'menipis')->count(),
            'stok_kosong'   => Atk::where('status', 'kosong')->count(),
            'total_nilai'   => Atk::sum('harga_satuan'),
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
            'kategori_id'   => 'required|exists:kategori_atk,id',
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'satuan'        => 'required|string|max:50',
            'stok_minimum'  => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'harga_satuan'  => 'required|numeric|min:0',
        ]);

        try {
            $atk = Atk::create($validated);
            $atk->updateStatus();

            return redirect()->route('inventaris.monitoring-atk.index')
                ->with('success', 'ATK berhasil ditambahkan.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menambahkan ATK.', ['action' => 'store']);
            return back()->withInput()->with('error', 'Gagal menambahkan ATK. Silakan coba lagi.');
        }
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
            'kategori_id'   => 'required|exists:kategori_atk,id',
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'satuan'        => 'required|string|max:50',
            'stok_minimum'  => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'harga_satuan'  => 'required|numeric|min:0',
        ]);

        try {
            $monitoringAtk->update($validated);
            $monitoringAtk->updateStatus();

            return redirect()->route('inventaris.monitoring-atk.index')
                ->with('success', 'ATK berhasil diperbarui.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal memperbarui ATK.', ['action' => 'update', 'atk_id' => $monitoringAtk->id]);
            return back()->withInput()->with('error', 'Gagal memperbarui ATK. Silakan coba lagi.');
        }
    }

    public function destroy(Atk $monitoringAtk)
    {
        try {
            $monitoringAtk->delete();

            return redirect()->route('inventaris.monitoring-atk.index')
                ->with('success', 'ATK berhasil dihapus.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menghapus ATK.', ['action' => 'destroy', 'atk_id' => $monitoringAtk->id]);
            return back()->with('error', 'Gagal menghapus ATK. Silakan coba lagi.');
        }
    }

    public function updateStok(Request $request, Atk $monitoringAtk)
    {
        $validated = $request->validate([
            'jenis'      => 'required|in:tambah,kurang',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        try {
            if ($validated['jenis'] === 'tambah') {
                $monitoringAtk->stok_tersedia += $validated['jumlah'];
            } else {
                if ($monitoringAtk->stok_tersedia < $validated['jumlah']) {
                    return back()->with('error', 'Stok tidak mencukupi.');
                }
                $monitoringAtk->stok_tersedia -= $validated['jumlah'];
            }

            $monitoringAtk->save();
            $monitoringAtk->updateStatus();

            return back()->with('success', 'Stok berhasil diperbarui.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal memperbarui stok ATK.', ['action' => 'updateStok', 'atk_id' => $monitoringAtk->id]);
            return back()->with('error', 'Gagal memperbarui stok. Silakan coba lagi.');
        }
    }

    public function export()
    {
        try {
            return Excel::download(new AtkExport, 'data-atk-' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal export data ATK.');
            return back()->with('error', 'Gagal melakukan export. Silakan coba lagi.');
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new AtkTemplateExport, 'template-import-atk.xlsx');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal mengunduh template ATK.');
            return back()->with('error', 'Gagal mengunduh template. Silakan coba lagi.');
        }
    }

    public function importForm()
    {
        $kategoris = KategoriAtk::orderBy('nama')->get();
        return view('inventaris.monitoring-atk.import', compact('kategoris'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new AtkImport();
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();
            $errors   = $import->errors();

            if ($failures->count() > 0 || $errors->count() > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }
                foreach ($errors as $error) {
                    $this->handleException($error, 'Error pada baris import ATK.');
                    $errorMessages[] = 'Terdapat baris yang tidak dapat diproses.';
                }

                return back()->with('warning', 'Import selesai dengan beberapa peringatan: ' . implode(' | ', $errorMessages));
            }

            return redirect()->route('inventaris.monitoring-atk.index')
                ->with('success', 'Data ATK berhasil diimport.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal import data ATK.', ['action' => 'import']);
            return back()->with('error', 'Gagal melakukan import. Pastikan format file sesuai template.');
        }
    }
}
