<?php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\SPP;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SPPController extends Controller
{
    public function index(Request $request)
    {
        $query = SPP::query();

        // Filter by bulan
        if ($request->has('bulan') && $request->bulan !== 'all') {
            $query->where('bulan', $request->bulan);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by RO
        if ($request->has('ro') && $request->ro !== 'all') {
            $query->where('ro', $request->ro);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_spp', 'like', "%{$search}%")
                  ->orWhere('uraian_spp', 'like', "%{$search}%")
                  ->orWhere('nama_pic', 'like', "%{$search}%");
            });
        }

        $spps = $query->orderBy('tgl_spp', 'desc')->paginate(20);

        // Get filter options
        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        $roList = Anggaran::select('ro')->distinct()->orderBy('ro')->pluck('ro');

        // Calculate statistics
        $totalBruto = $spps->sum('bruto');
        $totalNetto = $spps->sum('netto');
        $totalSP2D = SPP::where('status', 'Tagihan Telah SP2D')->sum('netto');
        $totalBelumSP2D = SPP::where('status', 'Tagihan Belum SP2D')->sum('netto');

        return view('anggaran.spp.index', compact(
            'spps',
            'bulanList',
            'roList',
            'totalBruto',
            'totalNetto',
            'totalSP2D',
            'totalBelumSP2D'
        ));
    }

    public function create()
    {
        $roList = Anggaran::select('ro')
            ->whereNotNull('ro')
            ->distinct()
            ->orderBy('ro')
            ->pluck('ro');

        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        $jenisBelanja = [
            'Kontraktual',
            'Non Kontraktual',
            'GUP',
            'TUP'
        ];

        $lsBendahara = ['LS', 'Bendahara'];

        return view('anggaran.spp.create', compact('roList', 'bulanList', 'jenisBelanja', 'lsBendahara'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'no_spp' => 'required|string|max:100|unique:spp,no_spp',
            'nominatif' => 'nullable|string|max:255',
            'tgl_spp' => 'required|date',
            'jenis_kegiatan' => 'required|string|max:255',
            'jenis_belanja' => 'required|string|in:Kontraktual,Non Kontraktual,GUP,TUP',
            'nomor_kontrak' => 'nullable|string|max:255',
            'no_bast' => 'nullable|string|max:255',
            'id_eperjadin' => 'nullable|string|max:255',
            'uraian_spp' => 'required|string',
            'bagian' => 'required|string|max:255',
            'nama_pic' => 'required|string|max:255',
            'kode_kegiatan' => 'required|string|max:50',
            'kro' => 'required|string|max:50',
            'ro' => 'required|string|max:50',
            'sub_komponen' => 'required|string|max:255',
            'mak' => 'required|string|max:50',
            'nomor_surat_tugas' => 'nullable|string|max:255',
            'tanggal_st' => 'nullable|date',
            'nomor_undangan' => 'nullable|string|max:255',
            'bruto' => 'required|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pph' => 'nullable|numeric|min:0',
            'netto' => 'required|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'ls_bendahara' => 'required|string|in:LS,Bendahara',
            'staff_ppk' => 'nullable|string|max:255',
            'no_sp2d' => 'nullable|string|max:255',
            'tgl_selesai_sp2d' => 'nullable|date',
            'tgl_sp2d' => 'nullable|date',
            'status' => 'required|in:Tagihan Telah SP2D,Tagihan Belum SP2D',
            'posisi_uang' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Create SPP (COA akan auto-generate di model)
            $spp = SPP::create($validated);

            // Update anggaran
            $this->updateAnggaran($spp);

            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data SPP: ' . $e->getMessage());
        }
    }

    public function show(SPP $spp)
    {
        return view('anggaran.spp.show', compact('spp'));
    }

    public function edit(SPP $spp)
    {
        $roList = Anggaran::select('ro')
            ->whereNotNull('ro')
            ->distinct()
            ->orderBy('ro')
            ->pluck('ro');

        $bulanList = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        $jenisBelanja = [
            'Kontraktual',
            'Non Kontraktual',
            'GUP',
            'TUP'
        ];

        $lsBendahara = ['LS', 'Bendahara'];

        // Get subkomponen list based on RO
        $subkomponenList = Anggaran::where('ro', $spp->ro)
            ->whereNotNull('kode_subkomponen')
            ->where('kode_subkomponen', '!=', '')
            ->distinct()
            ->get(['kode_subkomponen', 'program_kegiatan']);

        // Get akun list based on RO and subkomponen
        $akunList = Anggaran::where('ro', $spp->ro)
            ->where('kode_subkomponen', $spp->sub_komponen)
            ->whereNotNull('kode_akun')
            ->get(['kode_akun', 'program_kegiatan']);

        return view('anggaran.spp.edit', compact(
            'spp',
            'roList',
            'bulanList',
            'jenisBelanja',
            'lsBendahara',
            'subkomponenList',
            'akunList'
        ));
    }

    public function update(Request $request, SPP $spp)
    {
        $validated = $request->validate([
            'bulan' => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'no_spp' => 'required|string|max:100|unique:spp,no_spp,' . $spp->id,
            'nominatif' => 'nullable|string|max:255',
            'tgl_spp' => 'required|date',
            'jenis_kegiatan' => 'required|string|max:255',
            'jenis_belanja' => 'required|string|in:Kontraktual,Non Kontraktual,GUP,TUP',
            'nomor_kontrak' => 'nullable|string|max:255',
            'no_bast' => 'nullable|string|max:255',
            'id_eperjadin' => 'nullable|string|max:255',
            'uraian_spp' => 'required|string',
            'bagian' => 'required|string|max:255',
            'nama_pic' => 'required|string|max:255',
            'kode_kegiatan' => 'required|string|max:50',
            'kro' => 'required|string|max:50',
            'ro' => 'required|string|max:50',
            'sub_komponen' => 'required|string|max:255',
            'mak' => 'required|string|max:50',
            'nomor_surat_tugas' => 'nullable|string|max:255',
            'tanggal_st' => 'nullable|date',
            'nomor_undangan' => 'nullable|string|max:255',
            'bruto' => 'required|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pph' => 'nullable|numeric|min:0',
            'netto' => 'required|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'ls_bendahara' => 'required|string|in:LS,Bendahara',
            'staff_ppk' => 'nullable|string|max:255',
            'no_sp2d' => 'nullable|string|max:255',
            'tgl_selesai_sp2d' => 'nullable|date',
            'tgl_sp2d' => 'nullable|date',
            'status' => 'required|in:Tagihan Telah SP2D,Tagihan Belum SP2D',
            'posisi_uang' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Simpan data lama untuk rollback
            $oldCoa = $spp->coa;
            $oldBulan = $spp->bulan;
            $oldStatus = $spp->status;
            $oldNetto = $spp->netto;

            // Update SPP
            $spp->update($validated);

            // Generate COA baru jika ada perubahan
            $newCoa = $spp->kode_kegiatan . $spp->kro . $spp->ro . $spp->mak;
            $spp->coa = $newCoa;
            $spp->save();

            // Rollback anggaran lama
            $this->rollbackAnggaran($oldCoa, $oldBulan, $oldStatus, $oldNetto);

            // Update anggaran baru
            $this->updateAnggaran($spp);

            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data SPP: ' . $e->getMessage());
        }
    }

    public function destroy(SPP $spp)
    {
        DB::beginTransaction();
        try {
            // Rollback anggaran
            $this->rollbackAnggaran(
                $spp->coa,
                $spp->bulan,
                $spp->status,
                $spp->netto
            );

            $spp->delete();

            DB::commit();

            return redirect()->route('anggaran.spp.index')
                ->with('success', 'Data SPP berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data SPP: ' . $e->getMessage());
        }
    }

    /**
     * Update anggaran berdasarkan SPP
     */
    private function updateAnggaran(SPP $spp)
    {
        // Cari anggaran berdasarkan COA
        $anggaran = Anggaran::where('kegiatan', $spp->kode_kegiatan)
            ->where('kro', $spp->kro)
            ->where('ro', $spp->ro)
            ->where('kode_akun', $spp->mak)
            ->first();

        if (!$anggaran) {
            throw new \Exception('Data anggaran tidak ditemukan untuk COA: ' . $spp->coa);
        }

        $bulan = strtolower($spp->bulan);

        // Validasi bulan
        $validBulans = ['januari', 'februari', 'maret', 'april', 'mei', 'juni',
                        'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

        if (!in_array($bulan, $validBulans)) {
            throw new \Exception('Bulan tidak valid: ' . $bulan);
        }

        // Update berdasarkan status
        if ($spp->status === 'Tagihan Telah SP2D') {
            // Tambahkan ke realisasi bulan
            $anggaran->$bulan = ($anggaran->$bulan ?? 0) + $spp->netto;
        } else {
            // Tambahkan ke tagihan outstanding
            $anggaran->tagihan_outstanding = ($anggaran->tagihan_outstanding ?? 0) + $spp->netto;
        }

        // Update total penyerapan dan sisa
        $this->recalculateAnggaranTotals($anggaran);

        $anggaran->save();

        // Update parent rows (aggregate)
        $this->updateParentRows($anggaran);
    }

    /**
     * Rollback anggaran saat update/delete SPP
     */
    private function rollbackAnggaran($coa, $bulan, $status, $netto)
    {
        $parts = $this->parseCOA($coa);

        $anggaran = Anggaran::where('kegiatan', $parts['kegiatan'])
            ->where('kro', $parts['kro'])
            ->where('ro', $parts['ro'])
            ->where('kode_akun', $parts['mak'])
            ->first();

        if (!$anggaran) {
            return; // Silently return if anggaran not found
        }

        $bulan = strtolower($bulan);

        // Validasi bulan
        $validBulans = ['januari', 'februari', 'maret', 'april', 'mei', 'juni',
                        'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

        if (!in_array($bulan, $validBulans)) {
            return; // Silently return if bulan not valid
        }

        // Rollback berdasarkan status
        if ($status === 'Tagihan Telah SP2D') {
            $anggaran->$bulan = ($anggaran->$bulan ?? 0) - $netto;
            // Pastikan tidak negatif
            if ($anggaran->$bulan < 0) {
                $anggaran->$bulan = 0;
            }
        } else {
            $anggaran->tagihan_outstanding = ($anggaran->tagihan_outstanding ?? 0) - $netto;
            // Pastikan tidak negatif
            if ($anggaran->tagihan_outstanding < 0) {
                $anggaran->tagihan_outstanding = 0;
            }
        }

        // Recalculate totals
        $this->recalculateAnggaranTotals($anggaran);

        $anggaran->save();

        // Update parent rows
        $this->updateParentRows($anggaran);
    }

    /**
     * Parse COA string menjadi komponen-komponennya
     */
    private function parseCOA($coa)
    {
        // COA format: 4753EBAZ06AA521211
        // kegiatan: 4753 (4 char)
        // kro: EBA (3 char)
        // ro: Z06 atau 403 atau 405 atau 994 (3 char)
        // subkomp: AA (2 char) - optional
        // mak: 521211 (6 char)

        $kegiatan = substr($coa, 0, 4);
        $kro = substr($coa, 4, 3);
        $ro = substr($coa, 7, 3);
        $mak = substr($coa, -6);

        return [
            'kegiatan' => $kegiatan,
            'kro' => $kro,
            'ro' => $ro,
            'mak' => $mak
        ];
    }

    /**
     * Recalculate total penyerapan dan sisa anggaran
     */
    private function recalculateAnggaranTotals(Anggaran $anggaran)
    {
        $anggaran->total_penyerapan =
            ($anggaran->januari ?? 0) +
            ($anggaran->februari ?? 0) +
            ($anggaran->maret ?? 0) +
            ($anggaran->april ?? 0) +
            ($anggaran->mei ?? 0) +
            ($anggaran->juni ?? 0) +
            ($anggaran->juli ?? 0) +
            ($anggaran->agustus ?? 0) +
            ($anggaran->september ?? 0) +
            ($anggaran->oktober ?? 0) +
            ($anggaran->november ?? 0) +
            ($anggaran->desember ?? 0);

        $anggaran->sisa = $anggaran->pagu_anggaran - $anggaran->total_penyerapan;
    }

    /**
     * Update parent rows (subkomponen dan RO level)
     */
    private function updateParentRows(Anggaran $anggaran)
    {
        // Update subkomponen level
        $subkomp = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->where('kode_subkomponen', $anggaran->kode_subkomponen)
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp) {
            $children = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->where('kode_subkomponen', $anggaran->kode_subkomponen)
                ->whereNotNull('kode_akun')
                ->get();

            $bulans = ['januari', 'februari', 'maret', 'april', 'mei', 'juni',
                      'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

            foreach ($bulans as $bulan) {
                $subkomp->$bulan = $children->sum($bulan);
            }

            $subkomp->tagihan_outstanding = $children->sum('tagihan_outstanding');
            $this->recalculateAnggaranTotals($subkomp);
            $subkomp->save();
        }

        // Update RO level
        $ro = Anggaran::where('kegiatan', $anggaran->kegiatan)
            ->where('kro', $anggaran->kro)
            ->where('ro', $anggaran->ro)
            ->whereNull('kode_subkomponen')
            ->first();

        if ($ro) {
            $children = Anggaran::where('kegiatan', $anggaran->kegiatan)
                ->where('kro', $anggaran->kro)
                ->where('ro', $anggaran->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->get();

            $bulans = ['januari', 'februari', 'maret', 'april', 'mei', 'juni',
                      'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

            foreach ($bulans as $bulan) {
                $ro->$bulan = $children->sum($bulan);
            }

            $ro->tagihan_outstanding = $children->sum('tagihan_outstanding');
            $this->recalculateAnggaranTotals($ro);
            $ro->save();
        }
    }

    /**
     * Get subkomponen list berdasarkan RO (untuk AJAX)
     */
    public function getSubkomponen(Request $request)
    {
        $subkomponens = Anggaran::where('ro', $request->ro)
            ->whereNotNull('kode_subkomponen')
            ->where('kode_subkomponen', '!=', '')
            ->whereNull('kode_akun')
            ->distinct()
            ->get(['kode_subkomponen', 'program_kegiatan']);

        return response()->json($subkomponens);
    }

    /**
     * Get akun list berdasarkan RO dan subkomponen (untuk AJAX)
     */
    public function getAkun(Request $request)
    {
        $akuns = Anggaran::where('ro', $request->ro)
            ->where('kode_subkomponen', $request->subkomponen)
            ->whereNotNull('kode_akun')
            ->get(['kode_akun', 'kode_kegiatan', 'kro', 'program_kegiatan']);

        return response()->json($akuns);
    }
}
