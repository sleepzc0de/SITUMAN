<?php
namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\AsetEndUser;
use App\Models\KategoriAset;
use App\Models\Pegawai;
use App\Models\RiwayatAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AsetEndUserExport;
use App\Exports\AsetEndUserTemplateExport;
use App\Imports\AsetEndUserImport;
use Maatwebsite\Excel\Facades\Excel;

class AsetEndUserController extends Controller
{
    private const KONDISI_VALID = ['baik', 'rusak ringan', 'rusak berat', 'hilang'];
    private const STATUS_VALID  = ['tersedia', 'dipinjam', 'diperbaiki', 'tidak aktif'];

    /**
     * Aturan validasi terpusat untuk store & update aset.
     */
    private function asetRules(): array
    {
        return [
            'kategori_id'       => 'required|uuid|exists:kategori_aset,id',
            'nama_aset'         => 'required|string|min:3|max:255',
            'deskripsi'         => 'nullable|string|max:2000',
            'merek'             => 'nullable|string|max:100',
            'tipe'              => 'nullable|string|max:100',
            'nomor_seri'        => 'nullable|string|max:100',
            'tanggal_perolehan' => 'nullable|date|before_or_equal:today',
            'nilai_perolehan'   => 'required|numeric|min:0|max:99999999999',
            'kondisi'           => 'required|in:' . implode(',', self::KONDISI_VALID),
            'catatan'           => 'nullable|string|max:2000',
        ];
    }

    private function asetMessages(): array
    {
        return [
            'kategori_id.required'   => 'Kategori harus dipilih.',
            'kategori_id.exists'     => 'Kategori tidak valid.',
            'nama_aset.required'     => 'Nama aset harus diisi.',
            'nama_aset.min'          => 'Nama aset minimal 3 karakter.',
            'nama_aset.max'          => 'Nama aset maksimal 255 karakter.',
            'deskripsi.max'          => 'Deskripsi maksimal 2000 karakter.',
            'merek.max'              => 'Merek maksimal 100 karakter.',
            'tipe.max'               => 'Tipe/Model maksimal 100 karakter.',
            'nomor_seri.max'         => 'Nomor seri maksimal 100 karakter.',
            'tanggal_perolehan.date' => 'Format tanggal tidak valid.',
            'tanggal_perolehan.before_or_equal' => 'Tanggal perolehan tidak boleh di masa depan.',
            'nilai_perolehan.required' => 'Nilai perolehan harus diisi.',
            'nilai_perolehan.numeric'  => 'Nilai perolehan harus berupa angka.',
            'nilai_perolehan.min'      => 'Nilai perolehan tidak boleh negatif.',
            'nilai_perolehan.max'      => 'Nilai perolehan terlalu besar.',
            'kondisi.required'       => 'Kondisi harus dipilih.',
            'kondisi.in'             => 'Kondisi tidak valid.',
            'catatan.max'            => 'Catatan maksimal 2000 karakter.',
        ];
    }

    public function index(Request $request)
    {
        $request->validate([
            'search'   => 'nullable|string|max:100',
            'kategori' => 'nullable|uuid',
            'status'   => 'nullable|in:tersedia,dipinjam,diperbaiki,tidak aktif',
            'kondisi'  => 'nullable|in:baik,rusak ringan,rusak berat,hilang',
        ]);

        $query = AsetEndUser::with(['kategori', 'pegawai']);

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        if ($request->filled('status') && in_array($request->status, self::STATUS_VALID)) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kondisi') && in_array($request->kondisi, self::KONDISI_VALID)) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('search')) {
            $search = mb_substr($request->search, 0, 100);
            $query->where(function ($q) use ($search) {
                $q->where('nama_aset', 'like', '%' . $search . '%')
                    ->orWhere('kode_aset', 'like', '%' . $search . '%')
                    ->orWhere('nomor_seri', 'like', '%' . $search . '%')
                    ->orWhereHas('pegawai', fn($qq) => $qq->where('nama', 'like', '%' . $search . '%'));
            });
        }

        $aset      = $query->latest()->paginate(15)->withQueryString();
        $kategoris = KategoriAset::orderBy('nama')->get();

        $stats = [
            'total_aset'  => AsetEndUser::count(),
            'tersedia'    => AsetEndUser::where('status', 'tersedia')->count(),
            'dipinjam'    => AsetEndUser::where('status', 'dipinjam')->count(),
            'diperbaiki'  => AsetEndUser::where('status', 'diperbaiki')->count(),
            'total_nilai' => AsetEndUser::sum('nilai_perolehan') ?? 0,
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
        $validated = $request->validate($this->asetRules(), $this->asetMessages());
        $validated['status'] = 'tersedia';

        try {
            AsetEndUser::create($validated);
            return redirect()->route('inventaris.aset-end-user.index')
                ->with('success', 'Aset berhasil ditambahkan.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menambahkan aset.', ['action' => 'store']);
            return back()->withInput()->with('error', 'Gagal menambahkan aset. Silakan coba lagi.');
        }
    }

    public function show(AsetEndUser $asetEndUser)
    {
        $asetEndUser->load([
            'kategori',
            'pegawai',
            'riwayat' => fn($q) => $q->latest()->with(['pegawai', 'user']),
        ]);

        return view('inventaris.aset-end-user.show', compact('asetEndUser'));
    }

    public function edit(AsetEndUser $asetEndUser)
    {
        $kategoris = KategoriAset::orderBy('nama')->get();
        return view('inventaris.aset-end-user.edit', compact('asetEndUser', 'kategoris'));
    }

    public function update(Request $request, AsetEndUser $asetEndUser)
    {
        $validated = $request->validate($this->asetRules(), $this->asetMessages());

        try {
            $asetEndUser->update($validated);
            return redirect()->route('inventaris.aset-end-user.index')
                ->with('success', 'Aset berhasil diperbarui.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal memperbarui aset.', ['action' => 'update', 'aset_id' => $asetEndUser->id]);
            return back()->withInput()->with('error', 'Gagal memperbarui aset. Silakan coba lagi.');
        }
    }

    public function destroy(AsetEndUser $asetEndUser)
    {
        if ($asetEndUser->status === 'dipinjam') {
            return back()->with('error', 'Aset yang sedang dipinjam tidak dapat dihapus.');
        }

        try {
            $asetEndUser->delete();
            return redirect()->route('inventaris.aset-end-user.index')
                ->with('success', 'Aset berhasil dihapus.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menghapus aset.', ['action' => 'destroy', 'aset_id' => $asetEndUser->id]);
            return back()->with('error', 'Gagal menghapus aset. Silakan coba lagi.');
        }
    }

    public function pinjam(Request $request, AsetEndUser $asetEndUser)
    {
        if ($asetEndUser->status !== 'tersedia') {
            return back()->with('error', 'Aset tidak tersedia untuk dipinjam.');
        }

        $validated = $request->validate([
            'pegawai_id'         => 'required|uuid|exists:pegawai,id',
            'tanggal_peminjaman' => 'required|date|before_or_equal:today',
            'catatan'            => 'nullable|string|max:1000',
        ], [
            'pegawai_id.required'         => 'Pegawai harus dipilih.',
            'pegawai_id.exists'           => 'Pegawai tidak valid.',
            'tanggal_peminjaman.required' => 'Tanggal peminjaman harus diisi.',
            'tanggal_peminjaman.date'     => 'Format tanggal tidak valid.',
            'tanggal_peminjaman.before_or_equal' => 'Tanggal peminjaman tidak boleh di masa depan.',
            'catatan.max'                 => 'Catatan maksimal 1000 karakter.',
        ]);

        DB::beginTransaction();
        try {
            $asetEndUser->update([
                'pegawai_id'         => $validated['pegawai_id'],
                'tanggal_peminjaman' => $validated['tanggal_peminjaman'],
                'status'             => 'dipinjam',
                'catatan'            => $validated['catatan'] ?? null,
            ]);

            RiwayatAset::create([
                'aset_id'         => $asetEndUser->id,
                'pegawai_id'      => $validated['pegawai_id'],
                'user_id'         => auth()->id(),
                'jenis_aktivitas' => 'peminjaman',
                'tanggal'         => $validated['tanggal_peminjaman'],
                'keterangan'      => isset($validated['catatan'])
                    ? mb_substr($validated['catatan'], 0, 1000)
                    : null,
            ]);

            DB::commit();
            return back()->with('success', 'Aset berhasil dipinjamkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal meminjamkan aset.', ['action' => 'pinjam', 'aset_id' => $asetEndUser->id]);
            return back()->with('error', 'Gagal meminjamkan aset. Silakan coba lagi.');
        }
    }

    public function kembalikan(Request $request, AsetEndUser $asetEndUser)
    {
        if ($asetEndUser->status !== 'dipinjam') {
            return back()->with('error', 'Aset tidak dalam status dipinjam.');
        }

        $validated = $request->validate([
            'kondisi' => 'required|in:' . implode(',', self::KONDISI_VALID),
            'catatan' => 'nullable|string|max:1000',
        ], [
            'kondisi.required' => 'Kondisi harus dipilih.',
            'kondisi.in'       => 'Kondisi tidak valid.',
            'catatan.max'      => 'Catatan maksimal 1000 karakter.',
        ]);

        DB::beginTransaction();
        try {
            $pegawaiId = $asetEndUser->pegawai_id;

            $asetEndUser->update([
                'pegawai_id'         => null,
                'tanggal_peminjaman' => null,
                'status'             => 'tersedia',
                'kondisi'            => $validated['kondisi'],
                'catatan'            => $validated['catatan'] ?? null,
            ]);

            RiwayatAset::create([
                'aset_id'         => $asetEndUser->id,
                'pegawai_id'      => $pegawaiId,
                'user_id'         => auth()->id(),
                'jenis_aktivitas' => 'pengembalian',
                'tanggal'         => now()->toDateString(),
                'keterangan'      => isset($validated['catatan'])
                    ? mb_substr($validated['catatan'], 0, 1000)
                    : null,
            ]);

            DB::commit();
            return back()->with('success', 'Aset berhasil dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal mengembalikan aset.', ['action' => 'kembalikan', 'aset_id' => $asetEndUser->id]);
            return back()->with('error', 'Gagal mengembalikan aset. Silakan coba lagi.');
        }
    }

    public function export()
    {
        try {
            return Excel::download(
                new AsetEndUserExport,
                'data-aset-end-user-' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal export data aset.');
            return redirect()->route('inventaris.aset-end-user.index')
                ->with('error', 'Gagal melakukan export. Silakan coba lagi.');
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(
                new AsetEndUserTemplateExport,
                'template-import-aset-end-user.xlsx'
            );
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal mengunduh template aset.');
            return redirect()->route('inventaris.aset-end-user.index')
                ->with('error', 'Gagal mengunduh template. Silakan coba lagi.');
        }
    }

    public function importForm()
    {
        $kategoris = KategoriAset::orderBy('nama')->get();
        return view('inventaris.aset-end-user.import', compact('kategoris'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File harus diupload.',
            'file.mimes'    => 'Format file harus xlsx, xls, atau csv.',
            'file.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        try {
            $import = new AsetEndUserImport();
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();
            $errors   = $import->errors();

            if ($failures->count() > 0 || $errors->count() > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }
                foreach ($errors as $error) {
                    $this->handleException($error, 'Error pada baris import aset.');
                    $errorMessages[] = 'Terdapat baris yang tidak dapat diproses.';
                }
                return back()->with(
                    'warning',
                    'Import selesai dengan beberapa peringatan: ' . implode(' | ', array_slice($errorMessages, 0, 10))
                );
            }

            return redirect()->route('inventaris.aset-end-user.index')
                ->with('success', 'Data Aset berhasil diimport.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal import data aset.', ['action' => 'import']);
            return back()->with('error', 'Gagal melakukan import. Pastikan format file sesuai template.');
        }
    }
}
