<?php
// app/Http/Controllers/Anggaran/DokumenCapaianController.php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\DokumenCapaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // Jika request dari Alpine (AJAX), kembalikan JSON
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

        $roList   = Anggaran::select('ro')->distinct()->pluck('ro');
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

        return view('anggaran.dokumen.index', compact('dokumens', 'roList', 'bulanList'));
    }

    public function create()
    {
        $roList   = Anggaran::select('ro')->distinct()->pluck('ro');
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
                return back()->withInput()->with('error', 'Minimal satu file harus diupload');
            }

            // Auto-set anggaran_id dari SubKomponen jika tidak diisi
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
                ->with('success', 'Dokumen capaian output berhasil diupload (' . count($uploadedFiles) . ' file)');
        } catch (\Exception $e) {
            Log::error('DokumenCapaian store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mengupload dokumen: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $dokumen = DokumenCapaian::with(['user', 'anggaran'])->findOrFail($id);

            // Ambil info anggaran subkomponen untuk ditampilkan
            $anggaranSubkomp = $dokumen->anggaran ?? Anggaran::where('ro', $dokumen->ro)
                ->where('kode_subkomponen', $dokumen->sub_komponen)
                ->whereNull('kode_akun')
                ->first();

            return view('anggaran.dokumen.show', compact('dokumen', 'anggaranSubkomp'));
        } catch (\Exception $e) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan');
        }
    }

    public function edit($id)
    {
        try {
            $dokumen  = DokumenCapaian::findOrFail($id);
            $roList   = Anggaran::select('ro')->distinct()->pluck('ro');
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

            return view('anggaran.dokumen.edit', compact('dokumen', 'roList', 'bulanList'));
        } catch (\Exception $e) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan');
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

            // Hapus file yang dipilih
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

            // Upload file baru
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

            // Update anggaran_id jika RO/subkomponen berubah
            $subkomp = Anggaran::where('ro', $validated['ro'])
                ->where('kode_subkomponen', $validated['sub_komponen'])
                ->whereNull('kode_akun')
                ->first();

            $validated['anggaran_id'] = $subkomp?->id;
            $validated['files']       = $existingFiles;

            $dokumen->update($validated);

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil diupdate');
        } catch (\Exception $e) {
            Log::error('DokumenCapaian update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mengupdate dokumen: ' . $e->getMessage());
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
                ->with('success', 'Dokumen capaian output berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('DokumenCapaian destroy error: ' . $e->getMessage());
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $dokumen  = DokumenCapaian::findOrFail($id);
            $allFiles = $dokumen->getAllFiles();

            if (empty($allFiles)) {
                throw new \Exception('Tidak ada file untuk didownload');
            }

            if (count($allFiles) === 1) {
                $file     = $allFiles[0];
                $fullPath = storage_path('app/public/' . $file['path']);

                if (!file_exists($fullPath)) {
                    throw new \Exception('File tidak ditemukan');
                }

                return response()->download($fullPath, $file['name']);
            }

            // Multiple files: buat ZIP
            $tempDir     = storage_path('app/temp');
            if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);

            $zipFileName = 'dokumen_' . $dokumen->id . '_' . time() . '.zip';
            $zipPath     = $tempDir . '/' . $zipFileName;

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Gagal membuat file ZIP');
            }

            foreach ($allFiles as $file) {
                $fullPath = storage_path('app/public/' . $file['path']);
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, $file['name']);
                }
            }

            $zip->close();

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('DokumenCapaian download error: ' . $e->getMessage());
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal mendownload file: ' . $e->getMessage());
        }
    }

    public function downloadSingle($id, $fileIndex)
    {
        try {
            $dokumen  = DokumenCapaian::findOrFail($id);
            $allFiles = $dokumen->getAllFiles();

            if (!isset($allFiles[$fileIndex])) {
                throw new \Exception('File tidak ditemukan');
            }

            $file     = $allFiles[$fileIndex];
            $fullPath = storage_path('app/public/' . $file['path']);

            if (!file_exists($fullPath)) {
                throw new \Exception('File tidak ditemukan di server');
            }

            return response()->download($fullPath, $file['name']);
        } catch (\Exception $e) {
            Log::error('DokumenCapaian downloadSingle error: ' . $e->getMessage());
            return redirect()->route('anggaran.dokumen.show', $id)
                ->with('error', 'Gagal mendownload file: ' . $e->getMessage());
        }
    }

    public function getSubkomponen(Request $request)
    {
        try {
            if (!$request->ro) {
                return response()->json(['error' => 'RO harus diisi'], 400);
            }

            // Sertakan info sisa anggaran untuk referensi
            $subkomponens = Anggaran::where('ro', $request->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan', 'pagu_anggaran', 'total_penyerapan', 'sisa']);

            return response()->json($subkomponens);
        } catch (\Exception $e) {
            Log::error('DokumenCapaian getSubkomponen error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
