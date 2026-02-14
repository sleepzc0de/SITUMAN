@extends('layouts.app')

@section('title', 'Detail Data Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.data.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Kelola Data Anggaran
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Detail Data</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data->program_kegiatan }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    RO {{ $data->ro }}
                    @if($data->kode_subkomponen) / {{ $data->kode_subkomponen }} @endif
                    @if($data->kode_akun) / {{ $data->kode_akun }} @endif
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('anggaran.data.edit', $data) }}" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-blue-200 dark:border-blue-700">
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-2">Pagu Anggaran</p>
            <p class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ format_rupiah($data->pagu_anggaran) }}</p>
        </div>

        <div class="card bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-green-200 dark:border-green-700">
            <p class="text-sm font-medium text-green-600 dark:text-green-400 mb-2">Total Realisasi</p>
            <p class="text-2xl font-bold text-green-900 dark:text-green-300">{{ format_rupiah($data->total_penyerapan) }}</p>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                {{ calculate_percentage($data->total_penyerapan, $data->pagu_anggaran) }}%
            </p>
        </div>

        <div class="card bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border-orange-200 dark:border-orange-700">
            <p class="text-sm font-medium text-orange-600 dark:text-orange-400 mb-2">Tagihan Outstanding</p>
            <p class="text-2xl font-bold text-orange-900 dark:text-orange-300">{{ format_rupiah($data->tagihan_outstanding) }}</p>
        </div>

        <div class="card bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border-purple-200 dark:border-purple-700">
            <p class="text-sm font-medium text-purple-600 dark:text-purple-400 mb-2">Sisa Anggaran</p>
            <p class="text-2xl font-bold text-purple-900 dark:text-purple-300">{{ format_rupiah($data->sisa) }}</p>
            <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                {{ calculate_percentage($data->sisa, $data->pagu_anggaran) }}%
            </p>
        </div>
    </div>

    <!-- Detail Information -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
            Informasi Detail
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Kode Kegiatan</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $data->kegiatan }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">KRO</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $data->kro }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">RO</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $data->ro }} - {{ get_ro_name($data->ro) }}</span>
            </div>
            @if($data->kode_subkomponen)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Sub Komponen</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $data->kode_subkomponen }}</span>
            </div>
            @endif
            @if($data->kode_akun)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Kode Akun</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $data->kode_akun }}</span>
            </div>
            @endif
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">PIC</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $data->pic }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Level</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if(!$data->kode_akun)
                        @if(!$data->kode_subkomponen)
                            bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @else
                            bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                        @endif
                    @else
                        bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                    @endif">
                    @if(!$data->kode_akun)
                        @if(!$data->kode_subkomponen)
                            RO (Parent)
                        @else
                            Sub Komponen
                        @endif
                    @else
                        Akun (Detail)
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Realisasi per Bulan -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Realisasi per Bulan</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">{{ $bulan }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-navy-900">
                    <tr>
                        @foreach(['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'] as $bulan)
                            <td class="px-3 py-3 text-center {{ $data->$bulan > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-500' }}">
                                {{ $data->$bulan > 0 ? format_rupiah($data->$bulan, '') : '-' }}
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Child Items -->
    @if($children && $children->count() > 0)
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
            @if(!$data->kode_subkomponen)
                Sub Komponen
            @else
                Detail Akun
            @endif
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Uraian</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Pagu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Realisasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-navy-900 divide-y divide-gray-200 dark:divide-navy-700">
                    @foreach($children as $child)
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800">
                            <td class="px-4 py-3 font-mono text-gray-900 dark:text-white">
                                {{ $child->kode_akun ?? $child->kode_subkomponen }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ $child->program_kegiatan }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                {{ format_rupiah($child->pagu_anggaran) }}
                            </td>
                            <td class="px-4 py-3 text-right text-green-600 dark:text-green-400">
                                {{ format_rupiah($child->total_penyerapan) }}
                            </td>
                            <td class="px-4 py-3 text-right text-purple-600 dark:text-purple-400">
                                {{ format_rupiah($child->sisa) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('anggaran.data.show', $child) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
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
