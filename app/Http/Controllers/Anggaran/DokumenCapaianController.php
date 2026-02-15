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
        $roList = Anggaran::select('ro')->distinct()->pluck('ro');
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
            'ro' => 'required|string|max:50',
            'sub_komponen' => 'required|string|max:255',
            'bulan' => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'nama_dokumen' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('dokumen-capaian', $filename, 'public');

            $validated['file_path'] = $path;
        }

        $validated['user_id'] = Auth::id();

        DokumenCapaian::create($validated);

        return redirect()->route('anggaran.dokumen.index')
            ->with('success', 'Dokumen capaian output berhasil diupload');
    }

    public function show(DokumenCapaian $dokumen)
    {
        return view('anggaran.dokumen.show', compact('dokumen'));
    }

    // ✅ PERBAIKAN: Method download yang benar
    public function download(DokumenCapaian $dokumen)
    {
        try {
            // Cek apakah file_path ada
            if (!$dokumen->file_path) {
                Log::error('File path kosong untuk dokumen ID: ' . $dokumen->id);
                return redirect()->route('anggaran.dokumen.index')
                    ->with('error', 'File path tidak ditemukan');
            }

            // Cek apakah file exists di storage
            if (!Storage::disk('public')->exists($dokumen->file_path)) {
                Log::error('File tidak ditemukan di storage: ' . $dokumen->file_path);
                return redirect()->route('anggaran.dokumen.index')
                    ->with('error', 'File tidak ditemukan di server');
            }

            // Get file path lengkap
            $filePath = Storage::disk('public')->path($dokumen->file_path);

            // Get extension
            $extension = pathinfo($dokumen->file_path, PATHINFO_EXTENSION);

            // Buat nama file yang akan didownload (gunakan nama dokumen + extension)
            $downloadName = $dokumen->nama_dokumen . '.' . $extension;

            // Download file
            return response()->download($filePath, $downloadName);

        } catch (\Exception $e) {
            Log::error('Error downloading dokumen: ' . $e->getMessage(), [
                'dokumen_id' => $dokumen->id,
                'file_path' => $dokumen->file_path ?? 'null'
            ]);

            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'Gagal mendownload file: ' . $e->getMessage());
        }
    }

    // ✅ PERBAIKAN: Method destroy yang benar dengan redirect
    public function destroy(DokumenCapaian $dokumen)
    {
        try {
            // Hapus file dari storage jika ada
            if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
                Log::info('File deleted: ' . $dokumen->file_path);
            }

            // Hapus record dari database
            $dokumen->delete();

            Log::info('Dokumen capaian deleted successfully', ['id' => $dokumen->id]);

            // ✅ PENTING: Redirect ke halaman pertama untuk refresh data
            return redirect()->route('anggaran.dokumen.index')
                ->with('success', 'Dokumen capaian output berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Error deleting dokumen: ' . $e->getMessage(), [
                'dokumen_id' => $dokumen->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function edit(DokumenCapaian $dokumen)
    {
        $roList = Anggaran::select('ro')->distinct()->pluck('ro');
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
    }

    public function update(Request $request, DokumenCapaian $dokumen)
    {
        $validated = $request->validate([
            'ro' => 'required|string|max:50',
            'sub_komponen' => 'required|string|max:255',
            'bulan' => 'required|string',
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
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('dokumen-capaian', $filename, 'public');

            $validated['file_path'] = $path;
        }

        $dokumen->update($validated);

        return redirect()->route('anggaran.dokumen.index')
            ->with('success', 'Dokumen capaian output berhasil diupdate');
    }

    public function getSubkomponen(Request $request)
    {
        try {
            Log::info('Dokumen getSubkomponen called', ['ro' => $request->ro]);

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

            Log::info('Dokumen getSubkomponen result', [
                'count' => $subkomponens->count(),
                'data' => $subkomponens->toArray()
            ]);

            return response()->json($subkomponens);
        } catch (\Exception $e) {
            Log::error('Dokumen getSubkomponen error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
