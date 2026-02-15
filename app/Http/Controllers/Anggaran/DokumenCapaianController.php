<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\DokumenCapaian;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DokumenCapaianController extends Controller
{
    public function index(Request $request)
    {
        $query = DokumenCapaian::with('user')->orderBy('created_at', 'desc');

        if ($request->has('bulan') && $request->bulan !== 'all') {
            $query->where('bulan', $request->bulan);
        }

        if ($request->has('ro') && $request->ro !== 'all') {
            $query->where('ro', $request->ro);
        }

        $dokumens = $query->paginate(20);

        $roList = Anggaran::select('ro')->distinct()->pluck('ro');
        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        return view('anggaran.dokumen.index', compact('dokumens', 'roList', 'bulanList'));
    }

    public function create()
    {
        $roList = Anggaran::select('ro')->distinct()->pluck('ro');
        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        return view('anggaran.dokumen.create', compact('roList', 'bulanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ro' => 'required|string|max:50',
            'sub_komponen' => 'required|string|max:255',
            'bulan' => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'nama_dokumen' => 'required|string|max:255',
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $uploadedFiles = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $path = $file->storeAs('dokumen-capaian', $filename, 'public');

                    $uploadedFiles[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'extension' => $file->getClientOriginalExtension(),
                    ];
                }
            }

            $validated['files'] = $uploadedFiles;
            $validated['user_id'] = Auth::id();

            DokumenCapaian::create($validated);

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil diupload (' . count($uploadedFiles) . ' file)');

        } catch (\Exception $e) {
            Log::error('Error storing dokumen: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Gagal mengupload dokumen: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $dokumen = DokumenCapaian::with('user')->findOrFail($id);
            return view('anggaran.dokumen.show', compact('dokumen'));
        } catch (\Exception $e) {
            Log::error('Error showing dokumen: ' . $e->getMessage());
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan');
        }
    }

    public function edit($id)
    {
        try {
            $dokumen = DokumenCapaian::findOrFail($id);

            $roList = Anggaran::select('ro')->distinct()->pluck('ro');
            $bulanList = [
                'januari', 'februari', 'maret', 'april', 'mei', 'juni',
                'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
            ];

            return view('anggaran.dokumen.edit', compact('dokumen', 'roList', 'bulanList'));
        } catch (\Exception $e) {
            Log::error('Error editing dokumen: ' . $e->getMessage());
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $dokumen = DokumenCapaian::findOrFail($id);

            $validated = $request->validate([
                'ro' => 'required|string|max:50',
                'sub_komponen' => 'required|string|max:255',
                'bulan' => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
                'nama_dokumen' => 'required|string|max:255',
                'files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                'keterangan' => 'nullable|string',
                'remove_files' => 'nullable|array', // Files to remove
            ]);

            // Get existing files
            $existingFiles = $dokumen->files ?? [];

            // Remove selected files
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
                $existingFiles = array_values($existingFiles); // Re-index array
            }

            // Upload new files
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $path = $file->storeAs('dokumen-capaian', $filename, 'public');

                    $existingFiles[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'extension' => $file->getClientOriginalExtension(),
                    ];
                }
            }

            $validated['files'] = $existingFiles;
            $dokumen->update($validated);

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil diupdate');

        } catch (\Exception $e) {
            Log::error('Error updating dokumen: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Gagal mengupdate dokumen: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $dokumen = DokumenCapaian::findOrFail($id);

            // Delete all files
            $allFiles = $dokumen->getAllFiles();
            foreach ($allFiles as $file) {
                if (Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }

            // Delete record
            $dokumen->delete();

            Log::info('Dokumen deleted successfully', ['id' => $id]);

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Error deleting dokumen: ' . $e->getMessage());
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $dokumen = DokumenCapaian::findOrFail($id);
            $allFiles = $dokumen->getAllFiles();

            // If only one file, download it directly
            if (count($allFiles) === 1) {
                $file = $allFiles[0];
                $fullPath = storage_path('app/public/' . $file['path']);

                if (!file_exists($fullPath)) {
                    throw new \Exception('File tidak ditemukan');
                }

                return response()->download($fullPath, $file['name']);
            }

            // If multiple files, create ZIP
            $zip = new \ZipArchive();
            $zipFileName = 'dokumen_' . $dokumen->id . '_' . time() . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            // Create temp directory if not exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                foreach ($allFiles as $index => $file) {
                    $fullPath = storage_path('app/public/' . $file['path']);
                    if (file_exists($fullPath)) {
                        $zip->addFile($fullPath, $file['name']);
                    }
                }
                $zip->close();

                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
            }

            throw new \Exception('Gagal membuat file ZIP');

        } catch (\Exception $e) {
            Log::error('Download error', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal mendownload file: ' . $e->getMessage());
        }
    }

    public function downloadSingle($id, $fileIndex)
    {
        try {
            $dokumen = DokumenCapaian::findOrFail($id);
            $allFiles = $dokumen->getAllFiles();

            if (!isset($allFiles[$fileIndex])) {
                throw new \Exception('File tidak ditemukan');
            }

            $file = $allFiles[$fileIndex];
            $fullPath = storage_path('app/public/' . $file['path']);

            if (!file_exists($fullPath)) {
                throw new \Exception('File tidak ditemukan di server');
            }

            return response()->download($fullPath, $file['name']);

        } catch (\Exception $e) {
            Log::error('Download single file error', [
                'id' => $id,
                'fileIndex' => $fileIndex,
                'message' => $e->getMessage()
            ]);

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

            $subkomponens = Anggaran::where('ro', $request->ro)
                ->whereNotNull('kode_subkomponen')
                ->where('kode_subkomponen', '!=', '')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan']);

            return response()->json($subkomponens);

        } catch (\Exception $e) {
            Log::error('getSubkomponen error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
