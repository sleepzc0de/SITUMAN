<?php
namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Atk;
use App\Models\Pegawai;
use App\Models\PermintaanAtk;
use App\Models\PermintaanAtkDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanAtkController extends Controller
{
    /**
     * Aturan validasi terpusat untuk store & update permintaan.
     */
    private function permintaanRules(): array
    {
        return [
            'pegawai_id'          => 'required|uuid|exists:pegawai,id',
            'tanggal_permintaan'  => 'required|date|before_or_equal:today',
            'keterangan'          => 'nullable|string|max:1000',
            'atk_id'              => 'required|array|min:1|max:50',
            'atk_id.*'            => 'required|uuid|exists:atk,id',
            'jumlah'              => 'required|array|min:1|max:50',
            'jumlah.*'            => 'required|integer|min:1|max:9999',
            'keterangan_item'     => 'nullable|array|max:50',
            'keterangan_item.*'   => 'nullable|string|max:500',
        ];
    }

    private function permintaanMessages(): array
    {
        return [
            'pegawai_id.required'         => 'Pegawai peminta harus dipilih.',
            'pegawai_id.exists'           => 'Pegawai tidak valid.',
            'tanggal_permintaan.required' => 'Tanggal permintaan harus diisi.',
            'tanggal_permintaan.date'     => 'Format tanggal tidak valid.',
            'tanggal_permintaan.before_or_equal' => 'Tanggal permintaan tidak boleh di masa depan.',
            'keterangan.max'              => 'Keterangan maksimal 1000 karakter.',
            'atk_id.required'             => 'Minimal 1 item ATK harus dipilih.',
            'atk_id.min'                  => 'Minimal 1 item ATK harus dipilih.',
            'atk_id.max'                  => 'Maksimal 50 item ATK per permintaan.',
            'atk_id.*.required'           => 'ATK pada item :position harus dipilih.',
            'atk_id.*.exists'             => 'ATK pada item :position tidak valid.',
            'jumlah.*.required'           => 'Jumlah pada item :position harus diisi.',
            'jumlah.*.integer'            => 'Jumlah pada item :position harus bilangan bulat.',
            'jumlah.*.min'                => 'Jumlah pada item :position minimal 1.',
            'jumlah.*.max'                => 'Jumlah pada item :position maksimal 9.999.',
            'keterangan_item.*.max'       => 'Keterangan item :position maksimal 500 karakter.',
        ];
    }

    public function index(Request $request)
    {
        $request->validate([
            'search'         => 'nullable|string|max:100',
            'status'         => 'nullable|in:pending,disetujui,ditolak,selesai',
            'tanggal_dari'   => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
        ]);

        $query = PermintaanAtk::with(['user', 'pegawai', 'details.atk']);

        if ($request->filled('status') && in_array($request->status, ['pending', 'disetujui', 'ditolak', 'selesai'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = mb_substr($request->search, 0, 100);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_permintaan', 'like', '%' . $search . '%')
                    ->orWhereHas('pegawai', fn($qq) => $qq->where('nama', 'like', '%' . $search . '%'));
            });
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_sampai);
        }

        $permintaan = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => PermintaanAtk::count(),
            'pending'   => PermintaanAtk::where('status', 'pending')->count(),
            'disetujui' => PermintaanAtk::where('status', 'disetujui')->count(),
            'ditolak'   => PermintaanAtk::where('status', 'ditolak')->count(),
            'selesai'   => PermintaanAtk::where('status', 'selesai')->count(),
        ];

        return view('inventaris.permintaan-atk.index', compact('permintaan', 'stats'));
    }

    public function create()
    {
        $pegawai = Pegawai::orderBy('nama')->get();
        $atk     = Atk::where('status', '!=', 'kosong')->orderBy('nama')->get();
        return view('inventaris.permintaan-atk.create', compact('pegawai', 'atk'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->permintaanRules(), $this->permintaanMessages());

        // Pastikan jumlah array atk_id == jumlah
        if (count($validated['atk_id']) !== count($validated['jumlah'])) {
            return back()->withInput()->with('error', 'Data item tidak konsisten.');
        }

        // Pastikan tidak ada duplikasi ATK
        $atkIds = $validated['atk_id'];
        if (count($atkIds) !== count(array_unique($atkIds))) {
            return back()->withInput()->with('error', 'Terdapat item ATK yang duplikat dalam permintaan.');
        }

        DB::beginTransaction();
        try {
            $permintaan = PermintaanAtk::create([
                'user_id'            => auth()->id(),
                'pegawai_id'         => $validated['pegawai_id'],
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'keterangan'         => $validated['keterangan'] ?? null,
                'status'             => 'pending',
            ]);

            foreach ($validated['atk_id'] as $index => $atkId) {
                PermintaanAtkDetail::create([
                    'permintaan_id' => $permintaan->id,
                    'atk_id'        => $atkId,
                    'jumlah'        => $validated['jumlah'][$index],
                    'keterangan'    => isset($validated['keterangan_item'][$index])
                        ? mb_substr($validated['keterangan_item'][$index], 0, 500)
                        : null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventaris.permintaan-atk.index')
                ->with('success', 'Permintaan ATK berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal membuat permintaan ATK.', ['action' => 'store']);
            return back()->withInput()->with('error', 'Gagal membuat permintaan ATK. Silakan coba lagi.');
        }
    }

    public function show(PermintaanAtk $permintaanAtk)
    {
        $permintaanAtk->load(['user', 'pegawai', 'penyetuju', 'details.atk.kategori']);
        return view('inventaris.permintaan-atk.show', compact('permintaanAtk'));
    }

    public function edit(PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan dengan status pending yang dapat diedit.');
        }

        $pegawai = Pegawai::orderBy('nama')->get();
        $atk     = Atk::where('status', '!=', 'kosong')->orderBy('nama')->get();
        $permintaanAtk->load('details.atk');

        return view('inventaris.permintaan-atk.edit', compact('permintaanAtk', 'pegawai', 'atk'));
    }

    public function update(Request $request, PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan dengan status pending yang dapat diedit.');
        }

        $validated = $request->validate($this->permintaanRules(), $this->permintaanMessages());

        if (count($validated['atk_id']) !== count($validated['jumlah'])) {
            return back()->withInput()->with('error', 'Data item tidak konsisten.');
        }

        $atkIds = $validated['atk_id'];
        if (count($atkIds) !== count(array_unique($atkIds))) {
            return back()->withInput()->with('error', 'Terdapat item ATK yang duplikat dalam permintaan.');
        }

        DB::beginTransaction();
        try {
            $permintaanAtk->update([
                'pegawai_id'         => $validated['pegawai_id'],
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'keterangan'         => $validated['keterangan'] ?? null,
            ]);

            $permintaanAtk->details()->delete();

            foreach ($validated['atk_id'] as $index => $atkId) {
                PermintaanAtkDetail::create([
                    'permintaan_id' => $permintaanAtk->id,
                    'atk_id'        => $atkId,
                    'jumlah'        => $validated['jumlah'][$index],
                    'keterangan'    => isset($validated['keterangan_item'][$index])
                        ? mb_substr($validated['keterangan_item'][$index], 0, 500)
                        : null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventaris.permintaan-atk.index')
                ->with('success', 'Permintaan ATK berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal memperbarui permintaan ATK.', [
                'action'        => 'update',
                'permintaan_id' => $permintaanAtk->id,
            ]);
            return back()->withInput()->with('error', 'Gagal memperbarui permintaan ATK. Silakan coba lagi.');
        }
    }

    public function destroy(PermintaanAtk $permintaanAtk)
    {
        if (!in_array($permintaanAtk->status, ['pending', 'ditolak'])) {
            return back()->with('error', 'Permintaan yang sudah disetujui atau selesai tidak dapat dihapus.');
        }

        try {
            $permintaanAtk->delete();
            return redirect()->route('inventaris.permintaan-atk.index')
                ->with('success', 'Permintaan ATK berhasil dihapus.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menghapus permintaan ATK.', [
                'action'        => 'destroy',
                'permintaan_id' => $permintaanAtk->id,
            ]);
            return back()->with('error', 'Gagal menghapus permintaan ATK. Silakan coba lagi.');
        }
    }

    public function approve(Request $request, PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            $details = $permintaanAtk->details()->with('atk')->get();

            foreach ($details as $detail) {
                $atk = Atk::lockForUpdate()->find($detail->atk_id);
                if (!$atk || $atk->stok_tersedia < $detail->jumlah) {
                    DB::rollBack();
                    $nama  = $atk->nama ?? 'ATK tidak ditemukan';
                    $stok  = $atk->stok_tersedia ?? 0;
                    $sat   = $atk->satuan ?? '';
                    return back()->with(
                        'error',
                        "Stok {$nama} tidak mencukupi. Tersedia: {$stok} {$sat}, dibutuhkan: {$detail->jumlah} {$sat}."
                    );
                }
            }

            foreach ($details as $detail) {
                $atk = Atk::lockForUpdate()->find($detail->atk_id);
                $atk->stok_tersedia -= $detail->jumlah;
                $atk->save();
                $atk->updateStatus();
            }

            $permintaanAtk->update([
                'status'            => 'disetujui',
                'disetujui_oleh'    => auth()->id(),
                'tanggal_disetujui' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Permintaan ATK berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e, 'Gagal menyetujui permintaan ATK.', [
                'action'        => 'approve',
                'permintaan_id' => $permintaanAtk->id,
            ]);
            return back()->with('error', 'Gagal menyetujui permintaan ATK. Silakan coba lagi.');
        }
    }

    public function reject(Request $request, PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan harus diisi.',
            'alasan_penolakan.min'      => 'Alasan penolakan minimal 10 karakter.',
            'alasan_penolakan.max'      => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        try {
            $permintaanAtk->update([
                'status'            => 'ditolak',
                'alasan_penolakan'  => $validated['alasan_penolakan'],
                'disetujui_oleh'    => auth()->id(),
                'tanggal_disetujui' => now(),
            ]);
            return back()->with('success', 'Permintaan ATK berhasil ditolak.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menolak permintaan ATK.', [
                'action'        => 'reject',
                'permintaan_id' => $permintaanAtk->id,
            ]);
            return back()->with('error', 'Gagal menolak permintaan ATK. Silakan coba lagi.');
        }
    }

    public function complete(PermintaanAtk $permintaanAtk)
    {
        if ($permintaanAtk->status !== 'disetujui') {
            return back()->with('error', 'Hanya permintaan yang disetujui yang dapat diselesaikan.');
        }

        try {
            $permintaanAtk->update(['status' => 'selesai']);
            return back()->with('success', 'Permintaan ATK ditandai sebagai selesai.');
        } catch (\Exception $e) {
            $this->handleException($e, 'Gagal menyelesaikan permintaan ATK.', [
                'action'        => 'complete',
                'permintaan_id' => $permintaanAtk->id,
            ]);
            return back()->with('error', 'Gagal menyelesaikan permintaan ATK. Silakan coba lagi.');
        }
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'search'         => 'nullable|string|max:100',
                'status'         => 'nullable|in:pending,disetujui,ditolak,selesai',
                'tanggal_dari'   => 'nullable|date',
                'tanggal_sampai' => 'nullable|date',
                'page'           => 'nullable|integer|min:1|max:10000',
            ]);

            $query = PermintaanAtk::with(['user', 'pegawai', 'details']);

            if ($request->filled('status') && in_array($request->status, ['pending', 'disetujui', 'ditolak', 'selesai'])) {
                $query->where('status', $request->status);
            }
            if ($request->filled('search')) {
                $search = mb_substr($request->search, 0, 100);
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_permintaan', 'like', '%' . $search . '%')
                        ->orWhereHas('pegawai', fn($qq) => $qq->where('nama', 'like', '%' . $search . '%'));
                });
            }
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_sampai);
            }

            $page = max(1, (int) $request->get('page', 1));
            $paginator = $query->latest()->paginate(15, ['*'], 'page', $page);

            $badgeMap = [
                'pending'   => 'badge-warning',
                'disetujui' => 'badge-success',
                'ditolak'   => 'badge-danger',
                'selesai'   => 'badge-info',
            ];
            $labelMap = [
                'pending'   => 'Pending',
                'disetujui' => 'Disetujui',
                'ditolak'   => 'Ditolak',
                'selesai'   => 'Selesai',
            ];

            $data = $paginator->getCollection()->map(function ($item) use ($badgeMap, $labelMap) {
                $nama    = $item->pegawai->nama ?? '?';
                $words   = explode(' ', trim($nama));
                $initial = implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), array_slice($words, 0, 2)));
                return [
                    'id'                => $item->id,
                    'nomor_permintaan'  => $item->nomor_permintaan,
                    'tanggal_formatted' => \Carbon\Carbon::parse($item->tanggal_permintaan)->translatedFormat('d M Y'),
                    'pegawai_nama'      => $nama,
                    'pegawai_initial'   => $initial,
                    'user_nama'         => $item->user->nama ?? '-',
                    'jumlah_item'       => $item->details->count(),
                    'status'            => $item->status,
                    'status_badge'      => $badgeMap[$item->status] ?? 'badge-gray',
                    'status_label'      => $labelMap[$item->status] ?? $item->status,
                    'url_show'          => route('inventaris.permintaan-atk.show', $item->id),
                    'url_edit'          => route('inventaris.permintaan-atk.edit', $item->id),
                    'url_destroy'       => route('inventaris.permintaan-atk.destroy', $item->id),
                ];
            });

            return response()->json([
                'data'  => $data,
                'stats' => [
                    'total'     => PermintaanAtk::count(),
                    'pending'   => PermintaanAtk::where('status', 'pending')->count(),
                    'disetujui' => PermintaanAtk::where('status', 'disetujui')->count(),
                    'ditolak'   => PermintaanAtk::where('status', 'ditolak')->count(),
                    'selesai'   => PermintaanAtk::where('status', 'selesai')->count(),
                ],
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->handleExceptionJson($e, 'Gagal mengambil data permintaan ATK.', 500, [
                'action' => 'data',
            ]);
        }
    }
}
