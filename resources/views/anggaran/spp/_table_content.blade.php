<div class="table-wrapper rounded-none border-0">
    <table class="table">
        <thead>
            <tr>
                <th class="w-10">#</th>
                <th>No SPP</th>
                <th>Tanggal</th>
                <th>Bulan</th>
                <th>Uraian</th>
                <th>PIC</th>
                <th>RO</th>
                <th class="text-right">Netto</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($spps as $index => $spp)
            <tr>
                <td class="text-gray-400 text-xs">{{ table_row_number($spps, $index) }}</td>
                <td>
                    <a href="{{ route('anggaran.spp.show', $spp) }}"
                       class="font-semibold text-navy-600 dark:text-navy-400 hover:text-navy-800 dark:hover:text-navy-200 hover:underline">
                        {{ $spp->no_spp }}
                    </a>
                    @if($spp->jenis_belanja)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $spp->jenis_belanja }}</p>
                    @endif
                </td>
                <td class="text-gray-600 dark:text-gray-400 text-sm whitespace-nowrap">
                    {{ format_tanggal_short($spp->tgl_spp) }}
                </td>
                <td>
                    <span class="badge badge-gray">{{ ucfirst($spp->bulan) }}</span>
                </td>
                <td class="max-w-xs">
                    <p class="text-gray-900 dark:text-white text-sm line-clamp-2" title="{{ $spp->uraian_spp }}">
                        {{ truncate_text($spp->uraian_spp, 60) }}
                    </p>
                </td>
                <td class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                    {{ $spp->nama_pic }}
                </td>
                <td>
                    <span class="badge badge-info">{{ $spp->ro }}</span>
                </td>
                <td class="text-right font-semibold text-gray-900 dark:text-white text-sm whitespace-nowrap">
                    {{ format_rupiah($spp->netto) }}
                </td>
                <td>
                    @if($spp->status === 'Tagihan Telah SP2D')
                        <span class="badge badge-success">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            SP2D
                        </span>
                    @else
                        <span class="badge badge-warning">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Outstanding
                        </span>
                    @endif
                </td>
                <td>
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('anggaran.spp.show', $spp) }}"
                           class="table-action-view" title="Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('anggaran.spp.edit', $spp) }}"
                           class="table-action-edit" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <button x-data="confirmDelete('{{ route('anggaran.spp.destroy', $spp) }}', 'SPP {{ $spp->no_spp }}')"
                                @click="submit()"
                                class="table-action-delete" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="empty-state-title">Tidak ada data SPP</p>
                        <p class="empty-state-desc">Tidak ada data yang cocok dengan filter yang dipilih.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($spps->hasPages())
<div class="px-5 py-4 border-t border-gray-100 dark:border-navy-700">
    {{ $spps->onEachSide(1)->links() }}
</div>
@endif
