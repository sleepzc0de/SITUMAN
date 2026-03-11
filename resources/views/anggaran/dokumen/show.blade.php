@extends('layouts.app')

@section('title', 'Detail Dokumen Capaian Output')

@section('breadcrumb')
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.dokumen.index') }}" class="breadcrumb-item">Dokumen Capaian Output</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Detail</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div class="flex items-start gap-3">
        <a href="{{ route('anggaran.dokumen.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 dark:border-navy-600
                  text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="page-title">{{ $dokumen->nama_dokumen }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1.5">
                <span class="badge badge-blue">{{ $dokumen->ro }}</span>
                <span class="badge badge-gray capitalize">{{ $dokumen->bulan }}</span>
                @php $fileCount = count($dokumen->getAllFiles()); @endphp
                <span class="badge {{ $fileCount > 1 ? 'badge-success' : 'badge-info' }}">
                    {{ $fileCount }} file
                </span>
            </div>
        </div>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('anggaran.dokumen.edit', $dokumen->id) }}" class="btn btn-outline btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
        <a href="{{ route('anggaran.dokumen.download', $dokumen->id) }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download{{ $fileCount > 1 ? ' ZIP' : '' }}
        </a>
    </div>
</div>
@endsection

@section('content')
@php $allFiles = $dokumen->getAllFiles(); @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Kolom Kiri ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Detail Dokumen --}}
        <div class="card">
            <h3 class="section-title mb-4">Informasi Dokumen</h3>

            <div class="divide-y divide-gray-100 dark:divide-navy-700">
                @php
                    $rows = [
                        ['Nama Dokumen',   $dokumen->nama_dokumen],
                        ['RO',             $dokumen->ro . ' — ' . get_ro_name($dokumen->ro)],
                        ['Sub Komponen',   $dokumen->sub_komponen],
                        ['Bulan',          ucfirst($dokumen->bulan)],
                        ['Upload Oleh',    $dokumen->user?->nama ?? '—'],
                        ['Tanggal Upload', formatTanggalIndo($dokumen->created_at)],
                    ];
                @endphp
                @foreach($rows as [$label, $value])
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500 dark:text-gray-400 flex-shrink-0 w-36">{{ $label }}</dt>
                    <dd class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ $value }}</dd>
                </div>
                @endforeach
            </div>

            @if($dokumen->keterangan)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-navy-700">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                    Keterangan
                </p>
                <div class="bg-gray-50 dark:bg-navy-900/50 rounded-xl p-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                        {{ $dokumen->keterangan }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        {{-- File Dokumen --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="section-title">File Dokumen</h3>
                <span class="badge {{ count($allFiles) > 1 ? 'badge-success' : 'badge-info' }}">
                    {{ count($allFiles) }} file
                </span>
            </div>

            @if(count($allFiles) === 1)
            {{-- Single file --}}
            @php $file = $allFiles[0]; @endphp
            <div class="flex flex-col items-center justify-center py-10 px-6
                        bg-gray-50 dark:bg-navy-900/40 rounded-2xl
                        border-2 border-dashed border-gray-200 dark:border-navy-700 text-center gap-4">
                <div class="w-16 h-16 bg-white dark:bg-navy-800 rounded-2xl shadow-sm
                            flex items-center justify-center">
                    <svg class="w-8 h-8 {{ file_icon_class($file['path']) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $file['name'] }}</p>
                    <p class="text-sm text-gray-400 mt-0.5 uppercase text-xs">
                        {{ pathinfo($file['path'], PATHINFO_EXTENSION) }}
                    </p>
                </div>
                <a href="{{ route('anggaran.dokumen.download', $dokumen->id) }}" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download File
                </a>
            </div>

            @else
            {{-- Multiple files --}}
            <div class="space-y-2">
                @foreach($allFiles as $index => $file)
                <div class="flex items-center gap-3 p-3.5 rounded-xl
                            border border-gray-100 dark:border-navy-700
                            hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors group">
                    <div class="w-9 h-9 bg-gray-100 dark:bg-navy-700 rounded-lg
                                flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 {{ file_icon_class($file['path']) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $file['name'] }}
                        </p>
                        <p class="text-xs text-gray-400 uppercase">
                            {{ pathinfo($file['path'], PATHINFO_EXTENSION) }}
                        </p>
                    </div>
                    <a href="{{ route('anggaran.dokumen.download-single', [$dokumen->id, $index]) }}"
                       class="table-action-download opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"
                       title="Download file ini">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>
                </div>
                @endforeach
            </div>

            <div class="pt-4 mt-2 border-t border-gray-100 dark:border-navy-700">
                <a href="{{ route('anggaran.dokumen.download', $dokumen->id) }}" class="btn btn-primary w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Semua (ZIP)
                </a>
            </div>
            @endif
        </div>

    </div>

    {{-- ── Kolom Kanan ── --}}
    <div class="space-y-5">

        {{-- Posisi Anggaran --}}
        @if($anggaranSubkomp)
        @php
            $pagu        = (float)($anggaranSubkomp->pagu_anggaran ?? 0);
            $realisasi   = (float)($anggaranSubkomp->total_penyerapan ?? 0);
            $sisa        = (float)($anggaranSubkomp->sisa ?? 0);
            $outstanding = (float)($anggaranSubkomp->tagihan_outstanding ?? 0);
            $persen      = $pagu > 0 ? round(($realisasi / $pagu) * 100, 1) : 0;
        @endphp
        <div class="card">
            <h4 class="section-title mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-navy-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Posisi Anggaran
            </h4>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Pagu</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ format_rupiah($pagu) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Realisasi</span>
                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                        {{ format_rupiah($realisasi) }}
                    </span>
                </div>
                @if($outstanding > 0)
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Outstanding</span>
                    <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">
                        {{ format_rupiah($outstanding) }}
                    </span>
                </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Sisa</span>
                    <span class="text-sm font-semibold {{ $sisa < 0 ? 'text-red-500' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ format_rupiah($sisa) }}
                    </span>
                </div>

                <div class="pt-2 border-t border-gray-100 dark:border-navy-700">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-500">Penyerapan</span>
                        <span class="{{ $persen >= 80 ? 'text-green-600 dark:text-green-400' : ($persen >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-500 dark:text-red-400') }} font-semibold">
                            {{ $persen }}%
                        </span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="{{ progress_bar_color($persen) }} progress-bar"
                             style="width: {{ min($persen, 100) }}%"></div>
                    </div>
                    <div class="mt-2">
                        <span class="{{ statusAnggaranBadge($persen) }}">
                            {{ anggaran_status($pagu, $realisasi)['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-navy-700">
                <a href="{{ route('anggaran.monitoring.index') }}?ro={{ $dokumen->ro }}"
                   class="btn btn-ghost btn-sm w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Lihat Monitoring Anggaran
                </a>
            </div>
        </div>
        @endif

        {{-- Aksi --}}
        <div class="card">
            <h4 class="section-title mb-3">Aksi</h4>
            <div class="flex flex-col gap-2">
                <a href="{{ route('anggaran.dokumen.edit', $dokumen->id) }}" class="btn btn-outline w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Dokumen
                </a>
                <a href="{{ route('anggaran.dokumen.download', $dokumen->id) }}" class="btn btn-success w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download File
                </a>
                <button type="button"
                        x-data="confirmDelete('{{ route('anggaran.dokumen.destroy', $dokumen->id) }}', '{{ addslashes($dokumen->nama_dokumen) }}')"
                        @click="submit()"
                        class="btn btn-danger w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Dokumen
                </button>
            </div>
        </div>

        {{-- Navigasi Kontekstual --}}
        <div class="card">
            <h4 class="section-title mb-1">Lihat Juga</h4>
            <p class="section-desc mb-3">Dokumen terkait</p>
            <div class="flex flex-col gap-1">
                <a href="{{ route('anggaran.dokumen.index') }}?ro={{ $dokumen->ro }}&bulan={{ $dokumen->bulan }}"
                   class="btn btn-ghost btn-sm justify-start gap-2 text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    RO {{ $dokumen->ro }} — {{ ucfirst($dokumen->bulan) }}
                </a>
                <a href="{{ route('anggaran.dokumen.index') }}?ro={{ $dokumen->ro }}"
                   class="btn btn-ghost btn-sm justify-start gap-2 text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    Semua dokumen RO {{ $dokumen->ro }}
                </a>
                <a href="{{ route('anggaran.dokumen.create') }}"
                   class="btn btn-ghost btn-sm justify-start gap-2 text-navy-600 dark:text-navy-400">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Upload dokumen baru
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
