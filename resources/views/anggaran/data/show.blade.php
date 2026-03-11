@extends('layouts.app')

@section('title', 'Detail Data Anggaran')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 text-sm">
        <li><a href="{{ route('anggaran.data.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-navy-600 dark:hover:text-navy-400 transition-colors">Kelola Data Anggaran</a></li>
        <li class="flex items-center">
            <svg class="w-4 h-4 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-700 dark:text-gray-300">Detail</span>
        </li>
    </ol>
</nav>
@endsection

@section('content')
@php
    $persen = $data->pagu_anggaran > 0
        ? round(($data->total_penyerapan / $data->pagu_anggaran) * 100, 1) : 0;
    if (!$data->kode_akun && !$data->kode_subkomponen) {
        $levelLabel = 'RO (Parent)'; $levelBadge = 'badge-blue';
    } elseif (!$data->kode_akun) {
        $levelLabel = 'Sub Komponen'; $levelBadge = 'badge-purple';
    } else {
        $levelLabel = 'Akun (Detail)'; $levelBadge = 'badge-green';
    }
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="badge {{ $levelBadge }}">{{ $levelLabel }}</span>
                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                        {{ $data->ro }}{{ $data->kode_subkomponen ? ' / '.$data->kode_subkomponen : '' }}{{ $data->kode_akun ? ' / '.$data->kode_akun : '' }}
                    </span>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">
                    {{ $data->program_kegiatan }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">PIC: {{ $data->pic }}</p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('anggaran.data.edit', $data) }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card bg-gradient-to-br from-blue-50 to-blue-100/60 dark:from-blue-900/20 dark:to-blue-800/10 border-blue-200 dark:border-blue-800">
            <p class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">Pagu Anggaran</p>
            <p class="text-xl font-bold text-blue-900 dark:text-blue-300">{{ format_rupiah($data->pagu_anggaran) }}</p>
        </div>
        <div class="card bg-gradient-to-br from-green-50 to-green-100/60 dark:from-green-900/20 dark:to-green-800/10 border-green-200 dark:border-green-800">
            <p class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">Total Realisasi</p>
            <p class="text-xl font-bold text-green-900 dark:text-green-300">{{ format_rupiah($data->total_penyerapan) }}</p>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-medium">{{ $persen }}%</p>
        </div>
        <div class="card bg-gradient-to-br from-orange-50 to-orange-100/60 dark:from-orange-900/20 dark:to-orange-800/10 border-orange-200 dark:border-orange-800">
            <p class="text-xs font-medium text-orange-600 dark:text-orange-400 mb-1">Outstanding</p>
            <p class="text-xl font-bold text-orange-900 dark:text-orange-300">{{ format_rupiah($data->tagihan_outstanding) }}</p>
        </div>
        <div class="card bg-gradient-to-br from-purple-50 to-purple-100/60 dark:from-purple-900/20 dark:to-purple-800/10 border-purple-200 dark:border-purple-800">
            <p class="text-xs font-medium text-purple-600 dark:text-purple-400 mb-1">Sisa Anggaran</p>
            <p class="text-xl font-bold text-purple-900 dark:text-purple-300">{{ format_rupiah($data->sisa) }}</p>
            <p class="text-xs text-purple-600 dark:text-purple-400 mt-1 font-medium">
                {{ $data->pagu_anggaran > 0 ? round(($data->sisa / $data->pagu_anggaran) * 100, 1) : 0 }}%
            </p>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="card">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Progress Penyerapan</p>
            <span class="text-sm font-bold {{ $persen >= 80 ? 'text-green-600' : ($persen >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                {{ $persen }}%
            </span>
        </div>
        <div class="w-full h-3 bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
            <div class="h-3 rounded-full transition-all duration-500 {{ $persen >= 80 ? 'bg-green-500' : ($persen >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                 style="width: {{ min($persen, 100) }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mt-1.5">
            <span>Rp 0</span>
            <span>Target: {{ format_rupiah($data->pagu_anggaran) }}</span>
        </div>
    </div>

    {{-- Info Detail --}}
    <div class="card">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">
            Informasi Detail
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
            @php
                $rows = [
                    ['label' => 'Kode Kegiatan',  'value' => $data->kegiatan],
                    ['label' => 'KRO',             'value' => $data->kro],
                    ['label' => 'RO',              'value' => $data->ro . ' – ' . get_ro_name($data->ro)],
                    ['label' => 'PIC',             'value' => $data->pic],
                    ['label' => 'Referensi',       'value' => $data->referensi],
                ];
                if ($data->kode_subkomponen) $rows[] = ['label' => 'Sub Komponen', 'value' => $data->kode_subkomponen];
                if ($data->kode_akun)        $rows[] = ['label' => 'Kode Akun',    'value' => $data->kode_akun];
            @endphp
            @foreach($rows as $row)
            <div class="flex justify-between items-start py-2 border-b border-gray-50 dark:border-navy-700/50">
                <span class="text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $row['label'] }}</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white text-right ml-4 font-mono">{{ $row['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Realisasi per Bulan --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Realisasi per Bulan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'] as $bln)
                            <th class="px-3 py-2.5 text-center font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ $bln }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-navy-900">
                    <tr>
                        @foreach(['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'] as $bln)
                            @php $val = $data->$bln ?? 0; @endphp
                            <td class="px-3 py-3 text-center {{ $val > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-300 dark:text-gray-600' }}">
                                {{ $val > 0 ? format_rupiah($val, '') : '–' }}
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Child Items --}}
    @if($children && $children->count() > 0)
    <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                {{ !$data->kode_subkomponen ? 'Sub Komponen' : 'Detail Akun' }}
            </h3>
            <span class="badge badge-blue">{{ $children->count() }} item</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Uraian</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Pagu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Realisasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">%</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @foreach($children as $child)
                        @php
                            $cp = $child->pagu_anggaran > 0
                                ? round(($child->total_penyerapan / $child->pagu_anggaran) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-semibold text-navy-600 dark:text-navy-400">
                                {{ $child->kode_akun ?? $child->kode_subkomponen }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 max-w-xs">
                                <p class="truncate" title="{{ $child->program_kegiatan }}">{{ truncate_text($child->program_kegiatan, 50) }}</p>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ format_rupiah($child->pagu_anggaran) }}
                            </td>
                            <td class="px-4 py-3 text-right text-green-600 dark:text-green-400 whitespace-nowrap">
                                {{ format_rupiah($child->total_penyerapan) }}
                            </td>
                            <td class="px-4 py-3 text-right text-purple-600 dark:text-purple-400 whitespace-nowrap">
                                {{ format_rupiah($child->sisa) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-semibold {{ $cp >= 80 ? 'text-green-600' : ($cp >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                                    {{ $cp }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('anggaran.data.show', $child) }}"
                                   class="p-1.5 inline-flex text-navy-600 hover:text-navy-800 dark:text-navy-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors"
                                   title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
