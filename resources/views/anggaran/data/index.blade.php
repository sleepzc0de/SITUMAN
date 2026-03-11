@extends('layouts.app')
@section('title', 'Kelola Data Anggaran')
@section('subtitle', 'Master data pagu anggaran per RO, Sub Komponen, dan Akun')

@section('content')
<div class="space-y-5">

    {{-- ===== HEADER ACTIONS ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('anggaran.data.import-form') }}" class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Import
            </a>
            <a href="{{ route('anggaran.data.export') }}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}"
               class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </a>
            <a href="{{ route('anggaran.data.summary') }}" class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Rekap
            </a>
        </div>
        <a href="{{ route('anggaran.data.create') }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Data
        </a>
    </div>

    {{-- ===== SUMMARY CARDS ===== --}}
    @php
        use App\Models\Anggaran;
        $summaryData = Anggaran::whereNull('kode_subkomponen')->whereNull('kode_akun')
            ->selectRaw('SUM(pagu_anggaran) as total_pagu, SUM(total_penyerapan) as total_realisasi, SUM(tagihan_outstanding) as total_outstanding, SUM(sisa) as total_sisa')
            ->first();
        $totalPagu       = $summaryData->total_pagu       ?? 0;
        $totalRealisasi  = $summaryData->total_realisasi  ?? 0;
        $totalOutstanding= $summaryData->total_outstanding?? 0;
        $totalSisa       = $summaryData->total_sisa       ?? 0;
        $persenTotal     = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 1) : 0;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Pagu --}}
        <div class="card-flat p-4 border-l-4 border-l-navy-500">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pagu</p>
            <p class="mt-1.5 text-xl font-bold text-gray-900 dark:text-white truncate" title="{{ number_format($totalPagu) }}">
                {{ format_rupiah_short($totalPagu) }}
            </p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Seluruh RO</p>
        </div>
        {{-- Realisasi --}}
        <div class="card-flat p-4 border-l-4 border-l-green-500">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Realisasi</p>
            <p class="mt-1.5 text-xl font-bold text-green-600 dark:text-green-400 truncate" title="{{ number_format($totalRealisasi) }}">
                {{ format_rupiah_short($totalRealisasi) }}
            </p>
            <p class="mt-1 text-xs text-green-500 dark:text-green-600 font-medium">{{ $persenTotal }}% terserap</p>
        </div>
        {{-- Outstanding --}}
        <div class="card-flat p-4 border-l-4 border-l-amber-500">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Outstanding</p>
            <p class="mt-1.5 text-xl font-bold text-amber-600 dark:text-amber-400 truncate" title="{{ number_format($totalOutstanding) }}">
                {{ format_rupiah_short($totalOutstanding) }}
            </p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tagihan belum bayar</p>
        </div>
        {{-- Sisa --}}
        <div class="card-flat p-4 border-l-4 border-l-{{ $totalSisa < ($totalPagu * 0.2) ? 'red' : 'navy' }}-500">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sisa Anggaran</p>
            <p class="mt-1.5 text-xl font-bold {{ $totalSisa < ($totalPagu * 0.2) ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} truncate"
               title="{{ number_format($totalSisa) }}">
                {{ format_rupiah_short($totalSisa) }}
            </p>
            {{-- Overall progress bar --}}
            <div class="mt-2 h-1.5 w-full bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500
                    {{ $persenTotal >= 80 ? 'bg-green-500' : ($persenTotal >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                     style="width: {{ min($persenTotal, 100) }}%"></div>
            </div>
        </div>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="card p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="input-group">
                <label class="input-label">RO</label>
                <select name="ro" class="input-field" data-auto-submit>
                    <option value="all">Semua RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}" {{ request('ro') == $ro ? 'selected' : '' }}>
                            {{ $ro }} — {{ get_ro_name($ro) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="input-group">
                <label class="input-label">Level</label>
                <select name="level" class="input-field" data-auto-submit>
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
                           placeholder="Kode akun, sub komponen, atau uraian..."
                           class="input-field flex-1">
                    <button type="submit" class="btn btn-primary btn-icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    @if(request()->hasAny(['ro','level','search']))
                        <a href="{{ route('anggaran.data.index') }}" class="btn btn-ghost btn-icon" title="Reset filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="card p-0 overflow-hidden">

        {{-- Table info bar --}}
        <div class="px-5 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan
                <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $anggarans->firstItem() ?? 0 }}–{{ $anggarans->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $anggarans->total() }}</span> data
            </p>
            <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-blue-200 dark:bg-blue-900/60 inline-block"></span> RO
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-purple-200 dark:bg-purple-900/60 inline-block"></span> Sub Komponen
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-600 inline-block"></span> Akun
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-800/80 border-b border-gray-200 dark:border-navy-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[140px]">Kode / Referensi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Uraian Program / Kegiatan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">Level</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">Pagu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">Realisasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">Serapan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700/60">
                    @forelse($anggarans as $index => $anggaran)
                        @php
                            $isRO          = !$anggaran->kode_akun && !$anggaran->kode_subkomponen;
                            $isSubkomponen = !$anggaran->kode_akun && $anggaran->kode_subkomponen;
                            $isAkun        = (bool) $anggaran->kode_akun;

                            if ($isRO) {
                                $level      = 'RO';
                                $levelClass = 'badge-blue';
                                $levelIcon  = 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10';
                                $rowClass   = 'bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-100/60 dark:hover:bg-blue-900/20';
                                $indentPx   = '';
                            } elseif ($isSubkomponen) {
                                $level      = 'Sub Komponen';
                                $levelClass = 'badge-purple';
                                $levelIcon  = 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z';
                                $rowClass   = 'bg-purple-50/40 dark:bg-purple-900/10 hover:bg-purple-100/50 dark:hover:bg-purple-900/20';
                                $indentPx   = 'pl-5';
                            } else {
                                $level      = 'Akun';
                                $levelClass = 'badge-green';
                                $levelIcon  = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                                $rowClass   = 'hover:bg-gray-50/80 dark:hover:bg-navy-800/60';
                                $indentPx   = 'pl-9';
                            }

                            $persen      = $anggaran->pagu_anggaran > 0
                                ? round(($anggaran->total_penyerapan / $anggaran->pagu_anggaran) * 100, 1)
                                : 0;
                            $sisaWarning = $anggaran->pagu_anggaran > 0 && $anggaran->sisa < ($anggaran->pagu_anggaran * 0.2);

                            $barColor = $persen >= 80 ? 'bg-green-500' : ($persen >= 50 ? 'bg-amber-500' : 'bg-red-400');
                            $pctColor = $persen >= 80 ? 'text-green-600 dark:text-green-400' : ($persen >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-500 dark:text-red-400');
                        @endphp
                        <tr class="transition-colors {{ $rowClass }}">

                            {{-- No --}}
                            <td class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500 tabular-nums">
                                {{ table_row_number($anggarans, $index) }}
                            </td>

                            {{-- Kode --}}
                            <td class="px-4 py-3 {{ $indentPx }}">
                                <div class="space-y-0.5 font-mono text-xs">
                                    {{-- RO badge --}}
                                    <div class="flex items-center gap-1">
                                        <span class="text-gray-400 dark:text-gray-500">RO</span>
                                        <span class="font-bold text-navy-700 dark:text-navy-300">{{ $anggaran->ro }}</span>
                                    </div>
                                    {{-- Sub Komponen --}}
                                    @if($anggaran->kode_subkomponen)
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-400 dark:text-gray-500">Sub</span>
                                            <span class="font-semibold text-purple-700 dark:text-purple-400">{{ $anggaran->kode_subkomponen }}</span>
                                        </div>
                                    @endif
                                    {{-- Akun --}}
                                    @if($anggaran->kode_akun)
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-400 dark:text-gray-500">Akun</span>
                                            <span class="font-bold text-green-700 dark:text-green-400">{{ $anggaran->kode_akun }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Uraian --}}
                            <td class="px-4 py-3">
                                <div class="{{ $isRO ? 'font-semibold' : ($isSubkomponen ? 'font-medium' : '') }} text-gray-900 dark:text-gray-100 text-sm leading-snug"
                                     title="{{ $anggaran->program_kegiatan }}">
                                    {{ truncate_text($anggaran->program_kegiatan, 60) }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $anggaran->pic }}</span>
                                </div>
                            </td>

                            {{-- Level Badge --}}
                            <td class="px-4 py-3 text-center">
                                <span class="badge {{ $levelClass }} gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $levelIcon }}"/>
                                    </svg>
                                    {{ $level }}
                                </span>
                            </td>

                            {{-- Pagu --}}
                            <td class="px-4 py-3 text-right tabular-nums">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm whitespace-nowrap">
                                    {{ format_rupiah($anggaran->pagu_anggaran) }}
                                </span>
                            </td>

                            {{-- Realisasi --}}
                            <td class="px-4 py-3 text-right tabular-nums">
                                <span class="text-sm whitespace-nowrap
                                    {{ $anggaran->total_penyerapan > 0 ? 'font-semibold text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ format_rupiah($anggaran->total_penyerapan) }}
                                </span>
                                @if($anggaran->tagihan_outstanding > 0)
                                    <div class="text-xs text-amber-500 dark:text-amber-400 whitespace-nowrap mt-0.5">
                                        +{{ format_rupiah($anggaran->tagihan_outstanding) }} otsd
                                    </div>
                                @endif
                            </td>

                            {{-- Sisa --}}
                            <td class="px-4 py-3 text-right tabular-nums">
                                <span class="text-sm font-medium whitespace-nowrap
                                    {{ $sisaWarning ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ format_rupiah($anggaran->sisa) }}
                                </span>
                                @if($sisaWarning)
                                    <div class="text-xs text-red-400 dark:text-red-500 whitespace-nowrap mt-0.5">⚠ hampir habis</div>
                                @endif
                            </td>

                            {{-- Serapan --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="text-sm font-bold tabular-nums {{ $pctColor }}">{{ $persen }}%</span>
                                    <div class="w-20 h-2 bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}"
                                             style="width: {{ min($persen, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('anggaran.data.show', $anggaran) }}"
                                       class="table-action-view" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('anggaran.data.edit', $anggaran) }}"
                                       class="table-action-edit" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($anggaran->total_penyerapan == 0 && $anggaran->tagihan_outstanding == 0)
                                        <form action="{{ route('anggaran.data.destroy', $anggaran) }}" method="POST"
                                              x-data
                                              @submit.prevent="if(confirm('Hapus data anggaran ini? Tindakan ini tidak dapat dibatalkan.')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="table-action-delete" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Placeholder agar kolom tidak geser --}}
                                        <span class="w-7 h-7 inline-block"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state py-20">
                                    <div class="empty-state-icon">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p class="empty-state-title">
                                        @if(request()->hasAny(['ro','level','search']))
                                            Tidak ada data yang sesuai filter
                                        @else
                                            Belum ada data anggaran
                                        @endif
                                    </p>
                                    <p class="empty-state-desc">
                                        @if(request()->hasAny(['ro','level','search']))
                                            Coba ubah atau reset filter pencarian
                                        @else
                                            Mulai dengan menambahkan data atau import dari Excel
                                        @endif
                                    </p>
                                    <div class="flex gap-2 mt-2">
                                        @if(request()->hasAny(['ro','level','search']))
                                            <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline btn-sm">
                                                Reset Filter
                                            </a>
                                        @else
                                            <a href="{{ route('anggaran.data.import-form') }}" class="btn btn-outline btn-sm">Import Excel</a>
                                            <a href="{{ route('anggaran.data.create') }}" class="btn btn-primary btn-sm">Tambah Manual</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Tabel footer total --}}
                @if($anggarans->count() > 0)
                <tfoot>
                    <tr class="bg-navy-50 dark:bg-navy-800/80 border-t-2 border-navy-200 dark:border-navy-600">
                        <td colspan="4" class="px-4 py-3 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Total Halaman Ini
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white text-sm tabular-nums whitespace-nowrap">
                            {{ format_rupiah($anggarans->sum('pagu_anggaran')) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400 text-sm tabular-nums whitespace-nowrap">
                            {{ format_rupiah($anggarans->sum('total_penyerapan')) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-sm tabular-nums whitespace-nowrap
                            {{ $anggarans->sum('sisa') < ($anggarans->sum('pagu_anggaran') * 0.2) ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                            {{ format_rupiah($anggarans->sum('sisa')) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Pagination --}}
        @if($anggarans->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 dark:border-navy-700">
                {{ $anggarans->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
