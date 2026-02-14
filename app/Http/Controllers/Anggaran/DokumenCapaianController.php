<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\DokumenCapaian;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function download(DokumenCapaian $dokumen)
    {
        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return redirect()->route('anggaran.dokumen.index')
                ->with('error', 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->nama_dokumen);
    }

    public function destroy(DokumenCapaian $dokumen)
    {
        if (Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return redirect()->route('anggaran.dokumen.index')
            ->with('success', 'Dokumen capaian output berhasil dihapus');
    }
}
