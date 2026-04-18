<?php
// app/Http/Controllers/Inventaris/PermintaanAtkController.php

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
    public function index(Request $request)
    {
        $query = PermintaanAtk::with(['user', 'pegawai', 'details.atk']);

        if ($request->filled('status') && in_array($request->status, ['pending', 'disetujui', 'ditolak', 'selesai'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
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
        $validated = $request->validate([
            'pegawai_id'          => 'required|exists:pegawai,id',
            'tanggal_permintaan'  => 'required|date|before_or_equal:today',
            'keterangan'          => 'nullable|string|max:1000',
            'atk_id'              => 'required|array|min:1|max:50',
            'atk_id.*'            => 'required|exists:atk,id',
            'jumlah'              => 'required|array|min:1',
            'jumlah.*'            => 'required|integer|min:1|max:9999',
            'keterangan_item'     => 'nullable|array',
            'keterangan_item.*'   => 'nullable|string|max:500',
        ]);

        // Pastikan tidak ada duplikasi ATK dalam satu permintaan
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
                    'keterangan'    => $validated['keterangan_item'][$index] ?? null,
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

        $validated = $request->validate([
            'pegawai_id'          => 'required|exists:pegawai,id',
            'tanggal_permintaan'  => 'required|date|before_or_equal:today',
            'keterangan'          => 'nullable|string|max:1000',
            'atk_id'              => 'required|array|min:1|max:50',
            'atk_id.*'            => 'required|exists:atk,id',
            'jumlah'              => 'required|array|min:1',
            'jumlah.*'            => 'required|integer|min:1|max:9999',
            'keterangan_item'     => 'nullable|array',
            'keterangan_item.*'   => 'nullable|string|max:500',
        ]);

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
                    'keterangan'    => $validated['keterangan_item'][$index] ?? null,
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
            // Load details dengan lock untuk hindari race condition
            $details = $permintaanAtk->details()->with('atk')->get();

            // Validasi stok SEMUA item dulu sebelum ada yang dikurangi
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

            // Kurangi stok setelah semua validasi passed
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
            'alasan_penolakan' => 'required|string|max:500',
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
            $query = PermintaanAtk::with(['user', 'pegawai', 'details']);

            if ($request->filled('status') && in_array($request->status, ['pending', 'disetujui', 'ditolak', 'selesai'])) {
                $query->where('status', $request->status);
            }
            if ($request->filled('search')) {
                $search = $request->search;
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
