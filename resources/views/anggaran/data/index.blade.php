@extends('layouts.app')

@section('title', 'Kelola Data Anggaran')
@section('subtitle', 'Master data pagu anggaran per RO, Sub Komponen, dan Akun')

@section('content')
<div class="space-y-6">

    {{-- Header Actions --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('anggaran.data.import-form') }}"
                   class="btn btn-outline">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Import
                </a>
                <a href="{{ route('anggaran.data.export') }}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}"
                   class="btn btn-outline">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </a>
                <a href="{{ route('anggaran.data.summary') }}"
                   class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Rekap
                </a>
            </div>
            <a href="{{ route('anggaran.data.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Data
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <option value="ro"          {{ request('level') == 'ro'          ? 'selected' : '' }}>RO (Parent)</option>
                    <option value="subkomponen" {{ request('level') == 'subkomponen' ? 'selected' : '' }}>Sub Komponen</option>
                    <option value="akun"        {{ request('level') == 'akun'        ? 'selected' : '' }}>Akun (Detail)</option>
                </select>
            </div>

            <div class="input-group sm:col-span-2">
                <label class="input-label">Cari</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Kode akun, program/kegiatan..."
                           class="input-field flex-1">
                    <button type="submit" class="btn btn-primary px-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    @if(request()->hasAny(['ro','level','search']))
                        <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline px-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-800 border-b border-gray-200 dark:border-navy-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider w-10">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Program / Kegiatan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Level</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pagu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Realisasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Sisa</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">%</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @forelse($anggarans as $index => $anggaran)
                        @php
                            if (!$anggaran->kode_akun && !$anggaran->kode_subkomponen) {
                                $level      = 'RO';
                                $levelClass = 'badge-blue';
                                $indent     = '';
                                $rowBg      = 'bg-blue-50/40 dark:bg-blue-900/10';
                            } elseif (!$anggaran->kode_akun) {
                                $level      = 'Sub Komponen';
                                $levelClass = 'badge-purple';
                                $indent     = 'pl-4';
                                $rowBg      = 'bg-purple-50/30 dark:bg-purple-900/10';
                            } else {
                                $level      = 'Akun';
                                $levelClass = 'badge-green';
                                $indent     = 'pl-8';
                                $rowBg      = '';
                            }
                            $persen = $anggaran->pagu_anggaran > 0
                                ? round(($anggaran->total_penyerapan / $anggaran->pagu_anggaran) * 100, 1)
                                : 0;
                            $sisaWarning = $anggaran->pagu_anggaran > 0 && $anggaran->sisa < ($anggaran->pagu_anggaran * 0.2);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/60 transition-colors {{ $rowBg }}">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                {{ table_row_number($anggarans, $index) }}
                            </td>
                            <td class="px-4 py-3 {{ $indent }}">
                                <div class="font-mono text-xs space-y-0.5">
                                    <div class="text-gray-500 dark:text-gray-400">RO: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $anggaran->ro }}</span></div>
                                    @if($anggaran->kode_subkomponen)
                                        <div class="text-gray-500 dark:text-gray-400">Sub: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $anggaran->kode_subkomponen }}</span></div>
                                    @endif
                                    @if($anggaran->kode_akun)
                                        <div class="text-navy-600 dark:text-navy-400 font-bold">Akun: {{ $anggaran->kode_akun }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-gray-900 dark:text-white text-sm truncate" title="{{ $anggaran->program_kegiatan }}">
                                    {{ truncate_text($anggaran->program_kegiatan, 55) }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">PIC: {{ $anggaran->pic }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge {{ $levelClass }}">{{ $level }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white text-sm whitespace-nowrap">
                                {{ format_rupiah($anggaran->pagu_anggaran) }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                <span class="{{ $anggaran->total_penyerapan > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-400' }}">
                                    {{ format_rupiah($anggaran->total_penyerapan) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                <span class="{{ $sisaWarning ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ format_rupiah($anggaran->sisa) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <span class="text-xs font-semibold {{ $persen >= 80 ? 'text-green-600 dark:text-green-400' : ($persen >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-500 dark:text-red-400') }}">
                                        {{ $persen }}%
                                    </span>
                                </div>
                                <div class="w-16 h-1.5 bg-gray-200 dark:bg-navy-700 rounded-full mt-1 ml-auto">
                                    <div class="h-1.5 rounded-full {{ $persen >= 80 ? 'bg-green-500' : ($persen >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                         style="width: {{ min($persen, 100) }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('anggaran.data.show', $anggaran) }}"
                                       class="p-1.5 text-navy-600 hover:text-navy-800 dark:text-navy-400 dark:hover:text-navy-200 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors"
                                       title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('anggaran.data.edit', $anggaran) }}"
                                       class="p-1.5 text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-200 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($anggaran->total_penyerapan == 0 && $anggaran->tagihan_outstanding == 0)
                                    <form action="{{ route('anggaran.data.destroy', $anggaran) }}" method="POST"
                                          x-data
                                          @submit.prevent="if(confirm('Hapus data anggaran ini?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-navy-800 rounded-2xl flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-gray-700 dark:text-gray-300">Tidak ada data anggaran</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tambahkan data anggaran baru atau ubah filter</p>
                                    <a href="{{ route('anggaran.data.create') }}" class="btn btn-primary mt-1">
                                        Tambah Data Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($anggarans->hasPages())
            <div class="px-4 py-4 border-t border-gray-100 dark:border-navy-700">
                {{ $anggarans->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
