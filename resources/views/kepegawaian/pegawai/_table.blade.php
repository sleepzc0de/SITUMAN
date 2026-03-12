{{-- Partial: rows tabel pegawai (dirender ulang via AJAX) --}}
@forelse($pegawai as $index => $p)
<tr class="hover:bg-navy-50/30 dark:hover:bg-navy-700/30 transition-colors duration-150">
    <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500 tabular-nums">
        {{ $pegawai->firstItem() + $index }}
    </td>
    <td class="px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                <span class="text-xs font-bold text-white uppercase">{{ substr($p->nama, 0, 2) }}</span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-44">
                    {{ $p->nama_gelar ?? $p->nama }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $p->nip }}</p>
            </div>
        </div>
    </td>
    <td class="px-5 py-4">
        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-44">{{ $p->jabatan ?? '—' }}</p>
        <div class="flex items-center gap-1 mt-0.5 flex-wrap">
            @if($p->bagian)<span class="text-xs text-gray-500 dark:text-gray-400">{{ $p->bagian }}</span>@endif
            @if($p->eselon)<span class="text-xs text-purple-600 dark:text-purple-400">· {{ $p->eselon }}</span>@endif
        </div>
    </td>
    <td class="px-5 py-4">
        @if($p->grading)
        <span class="badge badge-info font-bold">G{{ $p->grading }}</span>
        @else
        <span class="text-gray-300 dark:text-gray-600">—</span>
        @endif
    </td>
    <td class="px-5 py-4">
        @if($p->pendidikan)
        <span class="badge badge-purple">{{ $p->pendidikan }}</span>
        @else
        <span class="text-gray-300 dark:text-gray-600">—</span>
        @endif
    </td>
    <td class="px-5 py-4">
        @php
        $sc = ['AKTIF'=>'badge-success','CLTN'=>'badge-blue','PENSIUN'=>'badge-gray','NON AKTIF'=>'badge-danger'];
        @endphp
        <span class="badge {{ $sc[$p->status] ?? 'badge-gray' }}">{{ $p->status ?? '—' }}</span>
    </td>
    <td class="px-5 py-4">
        @if($p->tanggal_pensiun)
        @php
        $pensiun   = \Carbon\Carbon::parse($p->tanggal_pensiun);
        $bulanLagi = now()->diffInMonths($pensiun, false);
        $isSegera  = $bulanLagi <= 12 && $bulanLagi > 0;
        $isDekat   = $bulanLagi <= 24 && $bulanLagi > 12;
        @endphp
        <p class="text-xs font-semibold {{ $isSegera ? 'text-red-600 dark:text-red-400' : ($isDekat ? 'text-orange-500 dark:text-orange-400' : 'text-gray-600 dark:text-gray-400') }}">
            {{ $pensiun->format('Y') }}
        </p>
        @if($bulanLagi > 0 && $bulanLagi <= 24)
        <p class="text-xs {{ $isSegera ? 'text-red-400' : 'text-orange-400' }}">{{ $bulanLagi }} bln</p>
        @elseif($bulanLagi <= 0)
        <p class="text-xs text-gray-400">Sudah pensiun</p>
        @else
        <p class="text-xs text-gray-400">{{ $pensiun->format('d/m/Y') }}</p>
        @endif
        @else
        <span class="text-gray-300 dark:text-gray-600">—</span>
        @endif
    </td>
    <td class="px-5 py-4">
        <div class="flex items-center justify-center gap-1">
            <a href="{{ route('kepegawaian.pegawai.show', $p) }}"
                class="table-action-view" title="Detail">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </a>
            <a href="{{ route('kepegawaian.pegawai.edit', $p) }}"
                class="table-action-edit" title="Edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <form method="POST" action="{{ route('kepegawaian.pegawai.destroy', $p) }}"
                x-data
                @submit.prevent="if(confirm('Hapus data {{ addslashes($p->nama) }}?\nTindakan ini tidak bisa dibatalkan.')) $el.submit()">
                @csrf @method('DELETE')
                <button type="submit" class="table-action-delete" title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="px-6 py-16">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="empty-state-title">Tidak ada data pegawai</p>
            <p class="empty-state-desc">Coba ubah filter atau tambahkan data baru</p>
            <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn-primary btn-sm mt-3">
                + Tambah Pegawai
            </a>
        </div>
    </td>
</tr>
@endforelse
