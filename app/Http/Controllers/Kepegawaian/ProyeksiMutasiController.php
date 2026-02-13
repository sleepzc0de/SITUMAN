<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ProyeksiMutasiController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');

        // Proyeksi mutasi berdasarkan masa jabatan dan pola historis
        $proyeksi = Cache::remember("proyeksi_mutasi_{$tahun}_{$bulan}", 300, function() use ($tahun, $bulan) {
            return Pegawai::where('status', 'AKTIF')
                ->get()
                ->map(function($pegawai) use ($tahun, $bulan) {
                    $analisis = $this->analisisMutasi($pegawai, $tahun, $bulan);
                    if ($analisis['perlu_mutasi']) {
                        $pegawai->analisis_mutasi = $analisis;
                        return $pegawai;
                    }
                    return null;
                })
                ->filter()
                ->sortByDesc('analisis_mutasi.prioritas')
                ->values();
        });

        return view('kepegawaian.mutasi.index', compact('proyeksi', 'tahun', 'bulan'));
    }

    private function analisisMutasi(Pegawai $pegawai, int $tahun, ?int $bulan): array
    {
        $perluMutasi = false;
        $alasan = [];
        $prioritas = 0;

        // Analisis masa jabatan (asumsi TMT dari data proyeksi_kp_1)
        if ($pegawai->proyeksi_kp_1) {
            // Parse tanggal dari proyeksi_kp_1
            // Contoh: "1 April 2023"
            try {
                $tmt = Carbon::parse($pegawai->proyeksi_kp_1);
                $lamaJabatan = $tmt->diffInMonths(now());

                // Jika sudah lebih dari 24 bulan (2 tahun)
                if ($lamaJabatan >= 24) {
                    $perluMutasi = true;
                    $prioritas += 3;
                    $alasan[] = "Sudah {$lamaJabatan} bulan di jabatan saat ini";
                }

                // Jika mendekati 2 tahun (18-24 bulan)
                if ($lamaJabatan >= 18 && $lamaJabatan < 24) {
                    $perluMutasi = true;
                    $prioritas += 2;
                    $alasan[] = "Mendekati batas masa jabatan (18-24 bulan)";
                }
            } catch (\Exception $e) {
                // Skip jika parsing gagal
            }
        }

        // Analisis berdasarkan usia menjelang pensiun
        if ($pegawai->tanggal_pensiun) {
            $bulanSampaiPensiun = Carbon::parse($pegawai->tanggal_pensiun)->diffInMonths(now());

            if ($bulanSampaiPensiun <= 12 && $bulanSampaiPensiun > 0) {
                $perluMutasi = true;
                $prioritas += 5;
                $alasan[] = "Akan pensiun dalam {$bulanSampaiPensiun} bulan";
            }
        }

        // Analisis berdasarkan jabatan struktural
        if ($pegawai->eselon && in_array($pegawai->eselon, ['Eselon III', 'Eselon IV'])) {
            $prioritas += 1;
            $alasan[] = "Pejabat struktural eselon " . $pegawai->eselon;
        }

        return [
            'perlu_mutasi' => $perluMutasi,
            'prioritas' => $prioritas,
            'alasan' => $alasan,
            'rekomendasi_waktu' => $this->rekomendasiWaktuMutasi($pegawai, $tahun),
        ];
    }

    private function rekomendasiWaktuMutasi(Pegawai $pegawai, int $tahun): ?string
    {
        // Rekomendasi waktu mutasi biasanya April atau Oktober
        $bulanMutasi = ['April', 'Oktober'];

        if ($pegawai->proyeksi_kp_1) {
            try {
                $tmt = Carbon::parse($pegawai->proyeksi_kp_1);
                $duaTahun = $tmt->copy()->addYears(2);

                if ($duaTahun->year == $tahun) {
                    return $duaTahun->format('F Y');
                }
            } catch (\Exception $e) {
                // Return default
            }
        }

        return "April {$tahun} atau Oktober {$tahun}";
    }

    public function show(Pegawai $pegawai)
    {
        $analisis = $this->analisisMutasi($pegawai, date('Y'), null);
        return view('kepegawaian.mutasi.show', compact('pegawai', 'analisis'));
    }
}
