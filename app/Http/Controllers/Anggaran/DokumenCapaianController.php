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
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'keterangan' => 'nullable|string',
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $path = $file->storeAs('dokumen-capaian', $filename, 'public');
                $validated['file_path'] = $path;
            }

            $validated['user_id'] = Auth::id();

            DokumenCapaian::create($validated);

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil diupload');

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
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                'keterangan' => 'nullable|string',
            ]);

            // Handle file upload if new file provided
            if ($request->hasFile('file')) {
                // Delete old file
                if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
                    Storage::disk('public')->delete($dokumen->file_path);
                }

                $file = $request->file('file');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $path = $file->storeAs('dokumen-capaian', $filename, 'public');
                $validated['file_path'] = $path;
            }

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

            // Delete file from storage
            if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
                Log::info('File deleted from storage: ' . $dokumen->file_path);
            }

            // Delete record from database
            $dokumen->delete();

            Log::info('Dokumen deleted successfully', ['id' => $id]);

            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Error deleting dokumen: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $dokumen = DokumenCapaian::findOrFail($id);

            Log::info('Download attempt', [
                'dokumen_id' => $id,
                'file_path' => $dokumen->file_path
            ]);

            // Validate file_path exists
            if (!$dokumen->file_path) {
                throw new \Exception('File path kosong');
            }

            // Get full path
            $fullPath = storage_path('app/public/' . $dokumen->file_path);

            Log::info('Full file path', ['path' => $fullPath]);

            // Check if file exists
            if (!file_exists($fullPath)) {
                throw new \Exception('File tidak ditemukan di: ' . $fullPath);
            }

            // Get file extension
            $extension = pathinfo($dokumen->file_path, PATHINFO_EXTENSION);

            // Create download filename
            $downloadName = $dokumen->nama_dokumen . '.' . $extension;

            Log::info('Download starting', [
                'download_name' => $downloadName,
                'file_size' => filesize($fullPath)
            ]);

            // Return download response
            return response()->download($fullPath, $downloadName, [
                'Content-Type' => mime_content_type($fullPath),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Dokumen not found', ['id' => $id]);
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Dokumen tidak ditemukan');

        } catch (\Exception $e) {
            Log::error('Download error', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal mendownload file: ' . $e->getMessage());
        }
    }

    public function getSubkomponen(Request $request)
    {
        try {
            Log::info('getSubkomponen called', ['ro' => $request->ro]);

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

            Log::info('getSubkomponen result', [
                'count' => $subkomponens->count()
            ]);

            return response()->json($subkomponens);

        } catch (\Exception $e) {
            Log::error('getSubkomponen error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
