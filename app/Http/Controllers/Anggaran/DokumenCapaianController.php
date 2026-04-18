<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\DokumenCapaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenCapaianController extends Controller
{
    public function index(Request $request)
    {
        $query = DokumenCapaian::with(['user', 'anggaran'])->orderBy('created_at', 'desc');

        if ($request->filled('bulan') && $request->bulan !== 'all') {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('ro') && $request->ro !== 'all') {
            $query->where('ro', $request->ro);
        }

        $dokumens = $query->paginate(20);

        if ($request->boolean('json')) {
            $rows = $dokumens->getCollection()->map(fn($d) => [
                'id'               => $d->id,
                'nama_dokumen'     => $d->nama_dokumen,
                'ro'               => $d->ro,
                'sub_komponen'     => $d->sub_komponen,
                'bulan'            => $d->bulan,
                'keterangan'       => $d->keterangan,
                'file_count'       => count($d->getAllFiles()),
                'user_nama'        => $d->user?->nama ?? '—',
                'created_at_short' => format_tanggal_short($d->created_at),
            ]);

            return response()->json([
                'rows'         => $rows,
                'current_page' => $dokumens->currentPage(),
                'last_page'    => $dokumens->lastPage(),
                'per_page'     => $dokumens->perPage(),
                'total'        => $dokumens->total(),
                'total_file'   => $dokumens->getCollection()->sum(fn($d) => count($d->getAllFiles())),
                'ro_count'     => $dokumens->getCollection()->pluck('ro')->unique()->count(),
            ]);
        }

        $roList    = Anggaran::select('ro')->distinct()->pluck('ro');
        $bulanList = ['januari','februari','maret','april','mei','juni',
                      'juli','agustus','september','oktober','november','desember'];

        return view('anggaran.dokumen.index', compact('dokumens', 'roList', 'bulanList'));
    }

    public function create()
    {
        $roList    = Anggaran::select('ro')->distinct()->pluck('ro');
        $bulanList = ['januari','februari','maret','april','mei','juni',
                      'juli','agustus','september','oktober','november','desember'];

        return view('anggaran.dokumen.create', compact('roList', 'bulanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ro'           => 'required|string|max:50',
            'sub_komponen' => 'required|string|max:255',
            'anggaran_id'  => 'nullable|exists:anggaran,id',
            'bulan'        => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'nama_dokumen' => 'required|string|max:255',
            'files.*'      => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'keterangan'   => 'nullable|string|max:1000',
        ]);

        try {
            $uploadedFiles = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . uniqid() . '_' .
                        preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $path = $file->storeAs('dokumen-capaian', $filename, 'public');

                    $uploadedFiles[] = [
                        'path'      => $path,
                        'name'      => $file->getClientOriginalName(),
                        'size'      => $file->getSize(),
                        'extension' => $file->getClientOriginalExtension(),
                    ];
                }
            }

            if (empty($uploadedFiles)) {
                return back()->withInput()->with('error', 'Minimal satu file harus diupload.');
            }

            if (empty($validated['anggaran_id'])) {
                $subkomp = Anggaran::where('ro', $validated['ro'])
                    ->where('kode_subkomponen', $validated['sub_komponen'])
                    ->whereNull('kode_akun')
                    ->first();

                if ($subkomp) {
                    $validated['anggaran_id'] = $subkomp->id;
                }
            }

            $validated['files']   = $uploadedFiles;
            $validated['user_id'] = Auth::id();

            DokumenCapaian::create($validated);

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil diupload (' . count($uploadedFiles) . ' file).');
        } catch (\Exception $e) {
            // Hapus file yang sudah terupload jika terjadi error
            foreach ($uploadedFiles ?? [] as $uploaded) {
                Storage::disk('public')->delete($uploaded['path'] ?? '');
            }
            $this->handleException($e, 'Gagal mengupload dokumen capaian.', ['action' => 'store']);
            return back()->withInput()->with('error', 'Gagal mengupload dokumen. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        try {
            $dokumen = DokumenCapaian::with(['user', 'anggaran'])->findOrFail($id);

            $anggaranSubkomp = $dokumen->anggaran ?? Anggaran::where('ro', $dokumen->ro)
                ->where('kode_subkomponen', $dokumen->sub_komponen)
                ->whereNull('kode_akun')
                ->first();

            return view('anggaran.dokumen.show', compact('dokumen', 'anggaranSubkomp'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal memuat dokumen.', ['action' => 'show', 'id' => $id]);
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal memuat dokumen. Silakan coba lagi.');
        }
    }

    public function edit($id)
    {
        try {
            $dokumen   = DokumenCapaian::findOrFail($id);
            $roList    = Anggaran::select('ro')->distinct()->pluck('ro');
            $bulanList = ['januari','februari','maret','april','mei','juni',
                          'juli','agustus','september','oktober','november','desember'];

            return view('anggaran.dokumen.edit', compact('dokumen', 'roList', 'bulanList'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal memuat form edit dokumen.', ['action' => 'edit', 'id' => $id]);
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal memuat halaman. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $dokumen   = DokumenCapaian::findOrFail($id);
            $validated = $request->validate([
                'ro'           => 'required|string|max:50',
                'sub_komponen' => 'required|string|max:255',
                'bulan'        => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
                'nama_dokumen' => 'required|string|max:255',
                'files.*'      => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                'keterangan'   => 'nullable|string|max:1000',
                'remove_files' => 'nullable|array',
            ]);

            $existingFiles = $dokumen->files ?? [];

            if ($request->has('remove_files') && is_array($request->remove_files)) {
                foreach ($request->remove_files as $fileIndex) {
                    if (isset($existingFiles[$fileIndex])) {
                        $filePath = $existingFiles[$fileIndex]['path'] ?? $existingFiles[$fileIndex];
                        if (Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                        unset($existingFiles[$fileIndex]);
                    }
                }
                $existingFiles = array_values($existingFiles);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . uniqid() . '_' .
                        preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $path = $file->storeAs('dokumen-capaian', $filename, 'public');

                    $existingFiles[] = [
                        'path'      => $path,
                        'name'      => $file->getClientOriginalName(),
                        'size'      => $file->getSize(),
                        'extension' => $file->getClientOriginalExtension(),
                    ];
                }
            }

            $subkomp = Anggaran::where('ro', $validated['ro'])
                ->where('kode_subkomponen', $validated['sub_komponen'])
                ->whereNull('kode_akun')
                ->first();

            $validated['anggaran_id'] = $subkomp?->id;
            $validated['files']       = $existingFiles;

            $dokumen->update($validated);

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil diupdate.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal mengupdate dokumen capaian.', ['action' => 'update', 'id' => $id]);
            return back()->withInput()->with('error', 'Gagal mengupdate dokumen. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $dokumen = DokumenCapaian::findOrFail($id);

            foreach ($dokumen->getAllFiles() as $file) {
                if (Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }

            $dokumen->delete();

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menghapus dokumen capaian.', ['action' => 'destroy', 'id' => $id]);
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal menghapus dokumen. Silakan coba lagi.');
        }
    }

    public function download($id)
    {
        try {
            $dokumen  = DokumenCapaian::findOrFail($id);
            $allFiles = $dokumen->getAllFiles();

            if (empty($allFiles)) {
                return redirect()->route('anggaran.dokumen.index')
                    ->with('error', 'Tidak ada file untuk didownload.');
            }

            if (count($allFiles) === 1) {
                $file     = $allFiles[0];
                $fullPath = storage_path('app/public/' . $file['path']);

                if (!file_exists($fullPath)) {
                    return redirect()->route('anggaran.dokumen.index')
                        ->with('error', 'File tidak ditemukan.');
                }

                return response()->download($fullPath, $file['name']);
            }

            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $zipFileName = 'dokumen_' . $dokumen->id . '_' . time() . '.zip';
            $zipPath     = $tempDir . '/' . $zipFileName;

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                return redirect()->route('anggaran.dokumen.index')
                    ->with('error', 'Gagal membuat file ZIP. Silakan coba lagi.');
            }

            foreach ($allFiles as $file) {
                $fullPath = storage_path('app/public/' . $file['path']);
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, $file['name']);
                }
            }

            $zip->close();

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal mendownload dokumen.', ['action' => 'download', 'id' => $id]);
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal mendownload file. Silakan coba lagi.');
        }
    }

    public function downloadSingle($id, $fileIndex)
    {
        try {
            $dokumen  = DokumenCapaian::findOrFail($id);
            $allFiles = $dokumen->getAllFiles();

            if (!isset($allFiles[$fileIndex])) {
                return redirect()->route('anggaran.dokumen.show', $id)
                    ->with('error', 'File tidak ditemukan.');
            }

            $file     = $allFiles[$fileIndex];
            $fullPath = storage_path('app/public/' . $file['path']);

            if (!file_exists($fullPath)) {
                return redirect()->route('anggaran.dokumen.show', $id)
                    ->with('error', 'File tidak ditemukan di server.');
            }

            return response()->download($fullPath, $file['name']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal mendownload file.', ['action' => 'downloadSingle', 'id' => $id]);
            return redirect()->route('anggaran.dokumen.show', $id)
                ->with('error', 'Gagal mendownload file. Silakan coba lagi.');
        }
    }

    public function getSubkomponen(Request $request)
    {
        try {
            if (!$request->ro) {
                return response()->json(['error' => 'RO harus diisi.'], 400);
            }

            $subkomponens = Anggaran::where('ro', $request->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan', 'pagu_anggaran', 'total_penyerapan', 'sisa']);

            return response()->json($subkomponens);
        } catch (\Exception $e) {
            return $this->handleExceptionJson($e, 'Gagal mengambil data subkomponen.', 500, [
                'action' => 'getSubkomponen',
            ]);
        }
    }
}
