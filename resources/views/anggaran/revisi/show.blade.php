@extends('layouts.app')

@section('title', 'Detail Revisi Anggaran')

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.revisi.index') }}" class="breadcrumb-item">Revisi Anggaran</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Detail</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Detail Revisi Anggaran</h1>
        <p class="page-subtitle">
            <span class="badge badge-purple mr-2">{{ $revisi->jenis_revisi }}</span>
            {{ formatTanggalIndo($revisi->tanggal_revisi) }}
        </p>
    </div>
    <div class="flex gap-2">
        @if($revisi->dokumen_pendukung)
        <a href="{{ route('anggaran.revisi.download-dokumen', $revisi) }}" class="btn btn-success">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Unduh Dokumen
        </a>
        @endif
        <a href="{{ route('anggaran.revisi.index') }}" class="btn btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
@php
    $selisih    = $revisi->pagu_sesudah - $revisi->pagu_sebelum;
    $persentase = $revisi->pagu_sebelum > 0
        ? (($selisih / $revisi->pagu_sebelum) * 100)
        : 0;
@endphp

<div class="space-y-6">

    {{-- Perbandingan Pagu --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card bg-gradient-to-br from-blue-50 to-blue-100/60
                    dark:from-blue-900/20 dark:to-blue-800/10
                    border-blue-200 dark:border-blue-800/50 !p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-200 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-700 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Pagu Sebelum</p>
            </div>
            <p class="text-xl font-bold text-blue-900 dark:text-blue-200">
                {{ format_rupiah($revisi->pagu_sebelum) }}
            </p>
        </div>

        <div class="card bg-gradient-to-br from-emerald-50 to-emerald-100/60
                    dark:from-emerald-900/20 dark:to-emerald-800/10
                    border-emerald-200 dark:border-emerald-800/50 !p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-emerald-200 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Pagu Sesudah</p>
            </div>
            <p class="text-xl font-bold text-emerald-900 dark:text-emerald-200">
                {{ format_rupiah($revisi->pagu_sesudah) }}
            </p>
        </div>

        <div class="card !p-5
            {{ $selisih >= 0
                ? 'bg-gradient-to-br from-emerald-50 to-emerald-100/60 dark:from-emerald-900/20 dark:to-emerald-800/10 border-emerald-200 dark:border-emerald-800/50'
                : 'bg-gradient-to-br from-red-50 to-red-100/60 dark:from-red-900/20 dark:to-red-800/10 border-red-200 dark:border-red-800/50' }}">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center
                    {{ $selisih >= 0 ? 'bg-emerald-200 dark:bg-emerald-900/50' : 'bg-red-200 dark:bg-red-900/50' }}">
                    <svg class="w-5 h-5 {{ $selisih >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($selisih >= 0)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        @endif
                    </svg>
                </div>
                <p class="text-sm font-semibold {{ $selisih >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}">
                    Selisih
                </p>
            </div>
            <p class="text-xl font-bold {{ $selisih >= 0 ? 'text-emerald-900 dark:text-emerald-200' : 'text-red-900 dark:text-red-200' }}">
                {{ $selisih > 0 ? '+' : '' }}{{ format_rupiah($selisih) }}
            </p>
            <p class="text-xs mt-1 {{ $selisih >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $selisih > 0 ? '+' : '' }}{{ number_format($persentase, 2) }}%
                dari pagu sebelumnya
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kiri: Detail Informasi (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Item Anggaran --}}
            <div class="card">
                <h3 class="section-title mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">
                    Item Anggaran yang Direvisi
                </h3>
                <dl class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-1 py-2 border-b border-gray-50 dark:border-navy-700/50">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">RO</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $revisi->anggaran->ro }} — {{ get_ro_name($revisi->anggaran->ro) }}
                        </dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-1 py-2 border-b border-gray-50 dark:border-navy-700/50">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Sub Komponen</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $revisi->anggaran->kode_subkomponen ?? '-' }}
                        </dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-1 py-2 border-b border-gray-50 dark:border-navy-700/50">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Kode Akun</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $revisi->anggaran->kode_akun ?? '-' }}
                        </dd>
                    </div>
                    <div class="py-2">
                        <dt class="text-sm text-gray-500 dark:text-gray-400 mb-2">Uraian Kegiatan</dt>
                        <dd class="bg-gray-50 dark:bg-navy-800/50 rounded-lg p-3 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $revisi->anggaran->program_kegiatan }}
                        </dd>
                    </div>
                </dl>

                {{-- Link ke monitoring --}}
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-navy-700">
                    <a href="{{ route('anggaran.monitoring.index') }}?ro={{ $revisi->anggaran->ro }}"
                       class="text-sm text-navy-600 dark:text-navy-400 hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Lihat monitoring RO {{ $revisi->anggaran->ro }}
                    </a>
                </div>
            </div>

            {{-- Alasan Revisi --}}
            <div class="card">
                <h3 class="section-title mb-4">Alasan Revisi</h3>
                <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30 rounded-xl p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                        {{ $revisi->alasan_revisi }}
                    </p>
                </div>
            </div>

            {{-- Dokumen Pendukung --}}
            @if($revisi->dokumen_pendukung)
            <div class="card">
                <h3 class="section-title mb-4">Dokumen Pendukung</h3>
                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-navy-800/50 rounded-xl border border-gray-100 dark:border-navy-700">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ basename($revisi->dokumen_pendukung) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">PDF Document</p>
                    </div>
                    <a href="{{ route('anggaran.revisi.download-dokumen', $revisi) }}"
                       class="btn btn-success btn-sm flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"/>
                        </svg>
                        Unduh
                    </a>
                </div>
            </div>
            @endif

        </div>

        {{-- Kanan: Metadata (1/3) --}}
        <div class="space-y-6">

            {{-- Info Revisi --}}
            <div class="card">
                <h3 class="section-title mb-4">Informasi Revisi</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jenis Revisi</dt>
                        <dd><span class="badge badge-purple">{{ $revisi->jenis_revisi }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal Revisi</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ formatTanggalIndo($revisi->tanggal_revisi) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Diinput Oleh</dt>
                        <dd class="flex items-center gap-2 mt-1">
                            <div class="w-7 h-7 bg-gradient-to-br from-navy-500 to-navy-700 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-white uppercase">
                                    {{ substr($revisi->user->nama ?? '?', 0, 2) }}
                                </span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $revisi->user->nama ?? '-' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Waktu Input</dt>
                        <dd class="text-sm text-gray-700 dark:text-gray-300">
                            {{ formatTanggalIndo($revisi->created_at) }}
                        </dd>
                        <dd class="text-xs text-gray-400 mt-0.5">
                            {{ $revisi->created_at->format('H:i') }} WIB
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Riwayat revisi akun ini --}}
            @php
                $riwayat = \App\Models\RevisiAnggaran::where('anggaran_id', $revisi->anggaran_id)
                    ->where('id', '!=', $revisi->id)
                    ->orderBy('tanggal_revisi', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            @if($riwayat->count() > 0)
            <div class="card">
                <h3 class="section-title mb-4">Revisi Lain pada Akun Ini</h3>
                <div class="space-y-2">
                    @foreach($riwayat as $r)
                    @php $s = $r->pagu_sesudah - $r->pagu_sebelum; @endphp
                    <a href="{{ route('anggaran.revisi.show', $r) }}"
                       class="flex items-center justify-between p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors group">
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 group-hover:text-navy-600 dark:group-hover:text-navy-400">
                                {{ $r->jenis_revisi }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ formatTanggalIndo($r->tanggal_revisi) }}</p>
                        </div>
                        <span class="text-xs font-semibold ml-2 whitespace-nowrap {{ $s >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $s > 0 ? '+' : '' }}{{ format_rupiah_short($s) }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
