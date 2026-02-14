@extends('layouts.app')

@section('title', 'Detail Revisi Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.revisi.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Revisi Anggaran
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Detail Revisi</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Revisi Anggaran</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $revisi->jenis_revisi }} - {{ formatTanggalIndo($revisi->tanggal_revisi) }}
                </p>
            </div>

            <div class="flex gap-2">
                @if($revisi->dokumen_pendukung)
                <a href="{{ route('anggaran.revisi.download-dokumen', $revisi) }}" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Dokumen
                </a>
                @endif
                <a href="{{ route('anggaran.revisi.index') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Comparison Card -->
    @php
        $selisih = $revisi->pagu_sesudah - $revisi->pagu_sebelum;
        $persentase = $revisi->pagu_sebelum > 0 ? (($selisih / $revisi->pagu_sebelum) * 100) : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-blue-200 dark:border-blue-700">
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-2">Pagu Sebelum</p>
            <p class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ format_rupiah($revisi->pagu_sebelum) }}</p>
        </div>

        <div class="card bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-green-200 dark:border-green-700">
            <p class="text-sm font-medium text-green-600 dark:text-green-400 mb-2">Pagu Sesudah</p>
            <p class="text-2xl font-bold text-green-900 dark:text-green-300">{{ format_rupiah($revisi->pagu_sesudah) }}</p>
        </div>

        <div class="card bg-gradient-to-br {{ $selisih >= 0 ? 'from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border-emerald-200 dark:border-emerald-700' : 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-red-200 dark:border-red-700' }}">
            <p class="text-sm font-medium {{ $selisih >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} mb-2">Selisih</p>
            <p class="text-2xl font-bold {{ $selisih >= 0 ? 'text-emerald-900 dark:text-emerald-300' : 'text-red-900 dark:text-red-300' }}">
                {{ $selisih > 0 ? '+' : '' }}{{ format_rupiah($selisih) }}
            </p>
            <p class="text-xs {{ $selisih >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                {{ $selisih > 0 ? '+' : '' }}{{ number_format($persentase, 2) }}%
            </p>
        </div>
    </div>

    <!-- Detail Revisi -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
            Informasi Revisi
        </h3>

        <div class="space-y-3">
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Jenis Revisi</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                    {{ $revisi->jenis_revisi }}
                </span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Tanggal Revisi</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($revisi->tanggal_revisi) }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Diinput Oleh</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $revisi->user->nama }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Tanggal Input</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($revisi->created_at) }}</span>
            </div>
        </div>
    </div>

    <!-- Item Anggaran -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
            Item Anggaran yang Direvisi
        </h3>

        <div class="space-y-3">
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">RO</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $revisi->anggaran->ro }} - {{ get_ro_name($revisi->anggaran->ro) }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Sub Komponen</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $revisi->anggaran->kode_subkomponen }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Kode Akun</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $revisi->anggaran->kode_akun }}</span>
            </div>
            <div class="py-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Uraian</p>
                <div class="bg-gray-50 dark:bg-navy-800 rounded-lg p-3">
                    <p class="text-gray-700 dark:text-gray-300">{{ $revisi->anggaran->program_kegiatan }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alasan Revisi -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Alasan Revisi</h3>
        <div class="bg-gray-50 dark:bg-navy-800 rounded-lg p-4">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $revisi->alasan_revisi }}</p>
        </div>
    </div>

    <!-- Dokumen Pendukung -->
    @if($revisi->dokumen_pendukung)
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Dokumen Pendukung</h3>

        <div class="bg-gray-50 dark:bg-navy-800 rounded-xl p-6 text-center">
            <svg class="w-16 h-16 mx-auto text-red-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ basename($revisi->dokumen_pendukung) }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">PDF</p>
            <a href="{{ route('anggaran.revisi.download-dokumen', $revisi) }}" class="btn btn-primary inline-flex">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download Dokumen
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
