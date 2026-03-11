<?php
// app/Http/Controllers/Anggaran/UsulanPenarikanController.php

namespace App\Http\Controllers\Anggaran;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\UsulanPenarikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UsulanPenarikanController extends Controller
{
    public function index(Request $request)
    {
        $query = UsulanPenarikan::with(['user', 'anggaran'])->orderBy('created_at', 'desc');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('bulan') && $request->bulan !== 'all') {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('ro') && $request->ro !== 'all') {
            $query->where('ro', $request->ro);
        }

        $usulans = $query->paginate(20)->withQueryString();

        $summary = [
            'pending'  => UsulanPenarikan::where('status', 'pending')->sum('nilai_usulan'),
            'approved' => UsulanPenarikan::where('status', 'approved')->sum('nilai_usulan'),
            'rejected' => UsulanPenarikan::where('status', 'rejected')->count(),
        ];

        $roList    = Anggaran::select('ro')->distinct()->pluck('ro');
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
            'desember',
        ];

        // ── AJAX: kembalikan JSON dengan HTML tabel --
        if ($request->ajax() || $request->get('ajax') === '1') {
            $tableHtml = $this->renderTableHtml($usulans);
            return response()->json([
                'tableHtml' => $tableHtml,
                'summary'   => $summary,
            ]);
        }

        return view('anggaran.usulan.index', compact(
            'usulans',
            'summary',
            'roList',
            'bulanList'
        ));
    }

    private function renderTableHtml($usulans): string
    {
        $badgeMap = [
            'pending'  => 'badge-pending',
            'approved' => 'badge-approved',
            'rejected' => 'badge-rejected',
        ];

        $html  = '<div class="table-wrapper">';
        $html .= '<table class="table">';
        $html .= '<thead><tr>';
        foreach (
            [
                'w-10' => 'No',
                '' => 'RO',
                '' => 'Sub Komponen',
                '' => 'Bulan',
                'text-right' => 'Nilai Usulan',
                '' => 'Pengusul',
                '' => 'Tgl Pengajuan',
                '' => 'Status',
                'text-center w-32' => 'Aksi'
            ] as $cls => $label
        ) {
            $html .= '<th' . ($cls ? ' class="' . e($cls) . '"' : '') . '>' . e($label) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        if ($usulans->isEmpty()) {
            $html .= '<tr><td colspan="9">';
            $html .= '<div class="empty-state">';
            $html .= '<div class="empty-state-icon"><svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>';
            $html .= '<p class="empty-state-title">Belum ada usulan penarikan dana</p>';
            $html .= '<p class="empty-state-desc">Tidak ada data yang cocok dengan filter yang dipilih</p>';
            $html .= '</div></td></tr>';
        } else {
            foreach ($usulans as $index => $usulan) {
                $no       = table_row_number($usulans, $index);
                $badge    = $badgeMap[$usulan->status] ?? 'badge-gray';
                $statusTx = status_text($usulan->status);
                $initial  = get_initials($usulan->user->nama);
                $nama     = e($usulan->user->nama);
                $subkomp  = e(truncate_text($usulan->sub_komponen, 45));
                $subFull  = e($usulan->sub_komponen);
                $nilai    = format_rupiah($usulan->nilai_usulan);
                $tgl      = format_tanggal_short($usulan->created_at);
                $bulan    = ucfirst($usulan->bulan);
                $ro       = e($usulan->ro);

                $showUrl    = route('anggaran.usulan.show',    $usulan);
                $editUrl    = route('anggaran.usulan.edit',    $usulan);
                $approveUrl = route('anggaran.usulan.approve', $usulan);
                $rejectUrl  = route('anggaran.usulan.reject',  $usulan);
                $deleteUrl  = route('anggaran.usulan.destroy', $usulan);
                $csrf       = csrf_field();

                $html .= <<<HTML
<tr>
  <td class="text-gray-400 text-xs">{$no}</td>
  <td><span class="badge badge-blue">{$ro}</span></td>
  <td><p class="font-medium text-gray-800 dark:text-gray-200 line-clamp-2" title="{$subFull}">{$subkomp}</p></td>
  <td class="whitespace-nowrap"><span class="text-sm text-gray-700 dark:text-gray-300">{$bulan}</span></td>
  <td class="text-right"><span class="font-semibold text-gray-900 dark:text-white text-sm tabular-nums">{$nilai}</span></td>
  <td>
    <div class="flex items-center gap-2">
      <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-700 flex items-center justify-center flex-shrink-0">
        <span class="text-xs font-bold text-navy-700 dark:text-navy-300">{$initial}</span>
      </div>
      <span class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[110px]">{$nama}</span>
    </div>
  </td>
  <td class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{$tgl}</td>
  <td><span class="{$badge}">{$statusTx}</span></td>
  <td>
    <div class="flex items-center justify-center gap-1">
      <a href="{$showUrl}" class="table-action-view" title="Detail">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
      </a>
HTML;

                if ($usulan->status === 'pending') {
                    $html .= <<<HTML
      <a href="{$editUrl}" class="table-action-edit" title="Edit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
      </a>
HTML;
                    // Approve & Reject hanya untuk admin — cek role
                    if (auth()->user() && (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))) {
                        $html .= <<<HTML
      <form action="{$approveUrl}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Setujui usulan ini?')) \$el.submit()">
        {$csrf}
        <button type="submit" class="table-action text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20" title="Setujui">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </button>
      </form>
      <button type="button" x-data @click="\$dispatch('open-reject-modal', { action: '{$rejectUrl}' })" class="table-action-delete" title="Tolak">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
        </svg>
      </button>
HTML;
                    }

                    $html .= <<<HTML
      <form action="{$deleteUrl}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Hapus usulan ini? Tidak dapat dibatalkan.')) \$el.submit()">
        {$csrf}<input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="table-action-delete" title="Hapus">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </form>
HTML;
                }

                $html .= '</div></td></tr>';
            }
        }

        $html .= '</tbody></table></div>';

        // Pagination
        if ($usulans->hasPages()) {
            $cur  = $usulans->currentPage();
            $last = $usulans->lastPage();
            $from = $usulans->firstItem();
            $to   = $usulans->lastItem();
            $tot  = $usulans->total();

            $html .= <<<HTML
<div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-500 dark:text-gray-400">
  <span>Menampilkan {$from}–{$to} dari {$tot} data</span>
  <div class="flex items-center gap-1">
HTML;

            // Prev
            if ($cur <= 1) {
                $html .= '<span class="btn btn-ghost btn-sm opacity-40 cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>';
            } else {
                $prev = $cur - 1;
                $html .= "<button type=\"button\" x-data @click=\"\$dispatch('change-page',{page:{$prev}})\" class=\"btn btn-ghost btn-sm\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 19l-7-7 7-7\"/></svg></button>";
            }

            // Page numbers
            $rangeStart = max(1, $cur - 2);
            $rangeEnd   = min($last, $cur + 2);
            for ($pg = $rangeStart; $pg <= $rangeEnd; $pg++) {
                $active = $pg === $cur ? 'btn-primary' : 'btn-ghost';
                $html  .= "<button type=\"button\" x-data @click=\"\$dispatch('change-page',{page:{$pg}})\" class=\"btn btn-sm {$active}\">{$pg}</button>";
            }

            // Next
            if (!$usulans->hasMorePages()) {
                $html .= '<span class="btn btn-ghost btn-sm opacity-40 cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>';
            } else {
                $next = $cur + 1;
                $html .= "<button type=\"button\" x-data @click=\"\$dispatch('change-page',{page:{$next}})\" class=\"btn btn-ghost btn-sm\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 5l7 7-7 7\"/></svg></button>";
            }

            $html .= '</div></div>';
        }

        return $html;
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

        return view('anggaran.usulan.create', compact('roList', 'bulanList'));
    }

    public function getSubkomponen(Request $request)
    {
        try {
            if (!$request->ro) {
                return response()->json(['error' => 'RO harus diisi'], 400);
            }

            // Kembalikan info sisa anggaran subkomponen untuk referensi user
            $subkomponens = Anggaran::where('ro', $request->ro)
                ->whereNotNull('kode_subkomponen')
                ->whereNull('kode_akun')
                ->distinct()
                ->orderBy('kode_subkomponen')
                ->get(['kode_subkomponen', 'program_kegiatan', 'pagu_anggaran', 'sisa', 'total_penyerapan']);

            return response()->json($subkomponens);
        } catch (\Exception $e) {
            Log::error('Usulan getSubkomponen error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ro'            => 'required|string|max:50',
            'sub_komponen'  => 'required|string|max:255',
            'anggaran_id'   => 'nullable|exists:anggaran,id',
            'bulan'         => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'nilai_usulan'  => 'required|numeric|min:1',
            'keterangan'    => 'nullable|string|max:500',
        ]);

        // Validasi nilai usulan tidak melebihi sisa subkomponen
        $subkomp = Anggaran::where('ro', $validated['ro'])
            ->where('kode_subkomponen', $validated['sub_komponen'])
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp && $validated['nilai_usulan'] > $subkomp->sisa) {
            return back()->withInput()->with(
                'error',
                'Nilai usulan (Rp ' . number_format($validated['nilai_usulan'], 0, ',', '.') . ') ' .
                    'melebihi sisa anggaran subkomponen (Rp ' . number_format($subkomp->sisa, 0, ',', '.') . ')'
            );
        }

        // Auto-set anggaran_id dari SubKomponen jika tidak diisi
        if (empty($validated['anggaran_id']) && $subkomp) {
            $validated['anggaran_id'] = $subkomp->id;
        }

        $validated['user_id'] = Auth::id();
        $validated['status']  = 'pending';

        UsulanPenarikan::create($validated);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil diajukan');
    }

    public function show(UsulanPenarikan $usulan)
    {
        $usulan->load(['user', 'anggaran']);

        // Ambil info anggaran terkait untuk konteks
        $anggaranSubkomp = null;
        if ($usulan->anggaran_id) {
            $anggaranSubkomp = $usulan->anggaran;
        } else {
            $anggaranSubkomp = Anggaran::where('ro', $usulan->ro)
                ->where('kode_subkomponen', $usulan->sub_komponen)
                ->whereNull('kode_akun')
                ->first();
        }

        return view('anggaran.usulan.show', compact('usulan', 'anggaranSubkomp'));
    }

    public function edit(UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return redirect()->route('anggaran.usulan.index')
                ->with('error', 'Usulan yang sudah diproses tidak dapat diedit');
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

        return view('anggaran.usulan.edit', compact('usulan', 'roList', 'bulanList'));
    }

    public function update(Request $request, UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return redirect()->route('anggaran.usulan.index')
                ->with('error', 'Usulan yang sudah diproses tidak dapat diedit');
        }

        $validated = $request->validate([
            'ro'           => 'required|string|max:50',
            'sub_komponen' => 'required|string|max:255',
            'bulan'        => 'required|string|in:januari,februari,maret,april,mei,juni,juli,agustus,september,oktober,november,desember',
            'nilai_usulan' => 'required|numeric|min:1',
            'keterangan'   => 'nullable|string|max:500',
        ]);

        // Re-validasi sisa
        $subkomp = Anggaran::where('ro', $validated['ro'])
            ->where('kode_subkomponen', $validated['sub_komponen'])
            ->whereNull('kode_akun')
            ->first();

        if ($subkomp && $validated['nilai_usulan'] > $subkomp->sisa) {
            return back()->withInput()->with(
                'error',
                'Nilai usulan melebihi sisa anggaran subkomponen (Rp ' .
                    number_format($subkomp->sisa, 0, ',', '.') . ')'
            );
        }

        // Update anggaran_id jika RO/subkomponen berubah
        if ($subkomp) {
            $validated['anggaran_id'] = $subkomp->id;
        }

        $usulan->update($validated);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil diupdate');
    }

    public function destroy(UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return redirect()->route('anggaran.usulan.index')
                ->with('error', 'Usulan yang sudah diproses tidak dapat dihapus');
        }

        $usulan->delete();

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil dihapus');
    }

    public function approve(Request $request, UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return back()->with('error', 'Usulan sudah diproses sebelumnya');
        }

        $usulan->update([
            'status'     => 'approved',
            'keterangan' => $request->get('catatan', $usulan->keterangan),
        ]);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil disetujui');
    }

    public function reject(Request $request, UsulanPenarikan $usulan)
    {
        if ($usulan->status !== 'pending') {
            return back()->with('error', 'Usulan sudah diproses sebelumnya');
        }

        $request->validate([
            'keterangan' => 'required|string|max:500',
        ]);

        $usulan->update([
            'status'     => 'rejected',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('anggaran.usulan.index')
            ->with('success', 'Usulan penarikan dana berhasil ditolak');
    }
}
