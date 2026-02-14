@extends('layouts.app')

@section('title', 'Revisi Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="card">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Revisi Anggaran</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola revisi pagu anggaran</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('anggaran.revisi.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Revisi
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="input-group">
                <label class="input-label">Jenis Revisi</label>
                <select name="jenis_revisi" class="input-field" onchange="this.form.submit()">
                    <option value="all">Semua Jenis</option>
                    <option value="POK" {{ request('jenis_revisi') == 'POK' ? 'selected' : '' }}>POK</option>
                    <option value="DIPA" {{ request('jenis_revisi') == 'DIPA' ? 'selected' : '' }}>DIPA</option>
                    <option value="Revisi Anggaran" {{ request('jenis_revisi') == 'Revisi Anggaran' ? 'selected' : '' }}>Revisi Anggaran</option>
                    <option value="Pergeseran" {{ request('jenis_revisi') == 'Pergeseran' ? 'selected' : '' }}>Pergeseran</option>
                </select>
            </div>

            <div class="flex items-end">
                @if(request()->has('jenis_revisi') && request('jenis_revisi') !== 'all')
                    <a href="{{ route('anggaran.revisi.index') }}" class="btn btn-outline w-full">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal Revisi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jenis Revisi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Item Anggaran</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Pagu Sebelum</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Pagu Sesudah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Selisih</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-navy-900 divide-y divide-gray-200 dark:divide-navy-700">
                    @forelse($revisis as $index => $revisi)
                        @php
                            $selisih = $revisi->pagu_sesudah - $revisi->pagu_sebelum;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ table_row_number($revisis, $index) }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ formatTanggalIndo($revisi->tanggal_revisi) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                    {{ $revisi->jenis_revisi }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ truncate_text($revisi->anggaran->program_kegiatan ?? '-', 40) }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                {{ format_rupiah($revisi->pagu_sebelum) }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                {{ format_rupiah($revisi->pagu_sesudah) }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold {{ $selisih > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $selisih > 0 ? '+' : '' }}{{ format_rupiah($selisih) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('anggaran.revisi.show', $revisi) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                       title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    @if($revisi->dokumen_pendukung)
                                    <a href="{{ route('anggaran.revisi.download-dokumen', $revisi) }}"
                                       class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                                       title="Download Dokumen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada revisi anggaran</p>
                                <p class="mt-1">Silakan tambah revisi baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($revisis->hasPages())
            <div class="mt-6">
                {{ $revisis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
