<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class KenaikanGradingController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        // Logika rekomendasi kenaikan grading
        // Berdasarkan masa kerja, pendidikan, dan grading saat ini
        $rekomendasi = Cache::remember("rekomendasi_grading_{$tahun}", 300, function() use ($tahun) {
            return Pegawai::where('status', 'AKTIF')
                ->whereNotNull('grading')
                ->get()
                ->map(function($pegawai) use ($tahun) {
                    $rekomendasi = $this->hitungRekomendasi($pegawai, $tahun);
                    if ($rekomendasi['eligible']) {
                        $pegawai->rekomendasi = $rekomendasi;
                        return $pegawai;
                    }
                    return null;
                })
                ->filter()
                ->values();
        });

        return view('kepegawaian.grading.index', compact('rekomendasi', 'tahun'));
    }

    private function hitungRekomendasi(Pegawai $pegawai, int $tahun): array
    {
        $masaKerja = $pegawai->masa_kerja_tahun ?? 0;
        $gradingSekarang = $pegawai->grading ?? 0;
        $pendidikan = $pegawai->pendidikan;

        $eligible = false;
        $alasan = [];
        $gradingBaru = $gradingSekarang;

        // Contoh logika sederhana
        // Sesuaikan dengan aturan kenaikan grading Kemenkeu

        if ($masaKerja >= 4 && $gradingSekarang < 16) {
            $eligible = true;
            $gradingBaru = min($gradingSekarang + 1, 16);
            $alasan[] = "Masa kerja sudah {$masaKerja} tahun";
        }

        // Pertimbangan pendidikan
        if (in_array($pendidikan, ['S2', 'S3']) && $gradingSekarang < 15) {
            $eligible = true;
            $alasan[] = "Memiliki pendidikan {$pendidikan}";
        }

        // Pertimbangan jabatan
        if ($pegawai->eselon && $gradingSekarang < 15) {
            $eligible = true;
            $alasan[] = "Memiliki jabatan eselon {$pegawai->eselon}";
        }

        return [
            'eligible' => $eligible,
            'grading_sekarang' => $gradingSekarang,
            'grading_baru' => $gradingBaru,
            'alasan' => $alasan,
            'tahun' => $tahun
        ];
    }

    public function show(Pegawai $pegawai)
    {
        $rekomendasi = $this->hitungRekomendasi($pegawai, date('Y'));
        return view('kepegawaian.grading.show', compact('pegawai', 'rekomendasi'));
    }
}
