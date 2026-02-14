@extends('layouts.app')

@section('title', 'Kelola Data Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="card">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Data Anggaran</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola master data anggaran dan pagu</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('anggaran.data.import-form') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Import Excel
                </a>
                <a href="{{ route('anggaran.data.export') }}" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('anggaran.data.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Data
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="input-group">
                <label class="input-label">RO</label>
                <select name="ro" class="input-field" onchange="this.form.submit()">
                    <option value="all">Semua RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}" {{ request('ro') == $ro ? 'selected' : '' }}>
                            {{ $ro }} - {{ get_ro_name($ro) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Level</label>
                <select name="level" class="input-field" onchange="this.form.submit()">
                    <option value="">Semua Level</option>
                    <option value="ro" {{ request('level') == 'ro' ? 'selected' : '' }}>RO (Parent)</option>
                    <option value="subkomponen" {{ request('level') == 'subkomponen' ? 'selected' : '' }}>Sub Komponen</option>
                    <option value="akun" {{ request('level') == 'akun' ? 'selected' : '' }}>Akun (Detail)</option>
                </select>
            </div>

            <div class="input-group md:col-span-2">
                <label class="input-label">Cari</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Kode akun, program/kegiatan..." class="input-field">
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    @if(request()->hasAny(['ro', 'level', 'search']))
                        <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">Reset</a>
                    @endif
                </div>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Program/Kegiatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Level</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Pagu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Realisasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-navy-900 divide-y divide-gray-200 dark:divide-navy-700">
                    @forelse($anggarans as $index => $anggaran)
                        @php
                            $level = 'Akun';
                            $levelClass = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                            $indentClass = 'pl-8';

                            if (!$anggaran->kode_akun) {
                                if (!$anggaran->kode_subkomponen) {
                                    $level = 'RO';
                                    $levelClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
                                    $indentClass = '';
                                } else {
                                    $level = 'Sub Komponen';
                                    $levelClass = 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400';
                                    $indentClass = 'pl-4';
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ table_row_number($anggarans, $index) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-mono text-xs">
                                    <div class="text-gray-600 dark:text-gray-400">RO: {{ $anggaran->ro }}</div>
                                    @if($anggaran->kode_subkomponen)
                                        <div class="text-gray-600 dark:text-gray-400">Sub: {{ $anggaran->kode_subkomponen }}</div>
                                    @endif
                                    @if($anggaran->kode_akun)
                                        <div class="text-gray-900 dark:text-white font-semibold">Akun: {{ $anggaran->kode_akun }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 {{ $indentClass }}">
                                <div class="text-gray-900 dark:text-white">
                                    {{ truncate_text($anggaran->program_kegiatan, 60) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $levelClass }}">
                                    {{ $level }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                {{ format_rupiah($anggaran->pagu_anggaran) }}
                            </td>
                            <td class="px-4 py-3 text-right {{ $anggaran->total_penyerapan > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-500' }}">
                                {{ format_rupiah($anggaran->total_penyerapan) }}
                            </td>
                            <td class="px-4 py-3 text-right {{ $anggaran->sisa < ($anggaran->pagu_anggaran * 0.2) ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-900 dark:text-white' }}">
                                {{ format_rupiah($anggaran->sisa) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('anggaran.data.show', $anggaran) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                       title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('anggaran.data.edit', $anggaran) }}"
                                       class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300"
                                       title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    @if($anggaran->total_penyerapan == 0 && $anggaran->tagihan_outstanding == 0)
                                    <form action="{{ route('anggaran.data.destroy', $anggaran) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data anggaran ini?')"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data anggaran</p>
                                <p class="mt-1">Silakan tambah data anggaran baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($anggarans->hasPages())
            <div class="mt-6">
                {{ $anggarans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
