@extends('layouts.app')

@section('title', 'Detail SPP – ' . $spp->no_spp)

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.spp.index') }}" class="breadcrumb-item">Data SPP</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">{{ $spp->no_spp }}</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="page-title">{{ $spp->no_spp }}</h1>
            @if($spp->status === 'Tagihan Telah SP2D')
                <span class="badge badge-success">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Sudah SP2D
                </span>
            @else
                <span class="badge badge-warning">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Belum SP2D
                </span>
            @endif
        </div>
        <p class="page-subtitle">
            {{ ucfirst($spp->bulan) }} &middot; {{ $spp->jenis_belanja }} &middot; RO {{ $spp->ro }}
        </p>
    </div>
    <div class="flex gap-2 flex-shrink-0">
        <a href="{{ route('anggaran.spp.edit', $spp) }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
        <a href="{{ route('anggaran.spp.index') }}" class="btn btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ===== SUMMARY NILAI ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card border-l-4 border-blue-400">
            <p class="stat-card-label">Bruto</p>
            <p class="stat-card-value text-lg">{{ format_rupiah_short($spp->bruto) }}</p>
            <p class="stat-card-sub text-gray-400 text-xs">{{ format_rupiah($spp->bruto) }}</p>
        </div>
        <div class="stat-card border-l-4 border-red-400">
            <p class="stat-card-label">Pajak (PPN+PPh)</p>
            <p class="stat-card-value text-lg text-red-600 dark:text-red-400">
                {{ format_rupiah_short($spp->ppn + $spp->pph) }}
            </p>
            <p class="stat-card-sub text-gray-400 text-xs">
                PPN {{ format_rupiah($spp->ppn) }} + PPh {{ format_rupiah($spp->pph) }}
            </p>
        </div>
        <div class="stat-card border-l-4 border-emerald-400 col-span-2 lg:col-span-1">
            <p class="stat-card-label">Netto</p>
            <p class="stat-card-value text-xl text-emerald-600 dark:text-emerald-400">
                {{ format_rupiah_short($spp->netto) }}
            </p>
            <p class="stat-card-sub text-gray-400 text-xs">{{ format_rupiah($spp->netto) }}</p>
        </div>
        @if($anggaran)
        <div class="stat-card border-l-4 {{ $spp->status === 'Tagihan Telah SP2D' ? 'border-navy-400' : 'border-orange-400' }}">
            <p class="stat-card-label">Sisa Pagu</p>
            @php $sisaEfektif = $anggaran->sisa - ($spp->status === 'Tagihan Belum SP2D' ? 0 : 0); @endphp
            <p class="stat-card-value text-lg {{ $anggaran->sisa < ($anggaran->pagu_anggaran * 0.2) ? 'text-red-600 dark:text-red-400' : '' }}">
                {{ format_rupiah_short($anggaran->sisa) }}
            </p>
            <p class="stat-card-sub text-gray-400 text-xs">dari pagu {{ format_rupiah_short($anggaran->pagu_anggaran) }}</p>
        </div>
        @endif
    </div>

    {{-- ===== INTEGRASI ANGGARAN ===== --}}
    @if($anggaran)
    <div class="card-flat">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Posisi Anggaran COA</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $spp->coa }}</p>
                </div>
            </div>
            <a href="{{ route('anggaran.monitoring.index') }}"
               class="btn btn-outline btn-sm">
                Lihat Monitoring
            </a>
        </div>

        @php
            $persen = $anggaran->pagu_anggaran > 0
                ? ($anggaran->total_penyerapan / $anggaran->pagu_anggaran) * 100
                : 0;
        @endphp

        <div class="grid grid-cols-3 gap-4 mb-3">
            <div class="text-center p-3 bg-gray-50 dark:bg-navy-700/40 rounded-xl">
                <p class="text-xs text-gray-500 mb-1">Pagu</p>
                <p class="font-bold text-gray-900 dark:text-white text-sm">{{ format_rupiah_short($anggaran->pagu_anggaran) }}</p>
            </div>
            <div class="text-center p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                <p class="text-xs text-emerald-600 mb-1">Realisasi</p>
                <p class="font-bold text-emerald-700 dark:text-emerald-400 text-sm">{{ format_rupiah_short($anggaran->total_penyerapan) }}</p>
            </div>
            <div class="text-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                <p class="text-xs text-orange-600 mb-1">Outstanding</p>
                <p class="font-bold text-orange-700 dark:text-orange-400 text-sm">{{ format_rupiah_short($anggaran->tagihan_outstanding) }}</p>
            </div>
        </div>

        <div>
            <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                <span>Penyerapan {{ formatPersen($anggaran->total_penyerapan, $anggaran->pagu_anggaran) }}</span>
                <span class="{{ percentage_text_class($persen) }} font-semibold">{{ number_format($persen, 1) }}%</span>
            </div>
            <div class="progress-bar-wrap">
                <div class="{{ progress_bar_color($persen) }} h-2 rounded-full transition-all duration-700"
                     style="width: {{ min(100, $persen) }}%"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== DETAIL SECTION ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Informasi Dasar --}}
        <div class="card">
            <h3 class="section-title mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">Informasi Dasar</h3>
            <dl class="space-y-2.5">
                @foreach([
                    ['No SPP',          $spp->no_spp],
                    ['Nominatif',       $spp->nominatif ?? '-'],
                    ['Tanggal SPP',     formatTanggalIndo($spp->tgl_spp)],
                    ['Bulan',           ucfirst($spp->bulan)],
                    ['Jenis Kegiatan',  $spp->jenis_kegiatan],
                    ['Bagian',          $spp->bagian],
                    ['PIC',             $spp->nama_pic],
                    ['LS / Bendahara',  $spp->ls_bendahara],
                ] as [$label, $value])
                <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-navy-700/50 last:border-0">
                    <dt class="text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $label }}</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white text-right ml-4">{{ $value }}</dd>
                </div>
                @endforeach

                <div class="flex justify-between py-1.5">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Jenis Belanja</dt>
                    <dd><span class="badge badge-purple">{{ $spp->jenis_belanja }}</span></dd>
                </div>

                @if($spp->staff_ppk)
                <div class="flex justify-between py-1.5">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Staff PPK</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $spp->staff_ppk }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Kode Anggaran --}}
        <div class="card">
            <h3 class="section-title mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">Kode Anggaran (COA)</h3>
            <dl class="space-y-2.5">
                <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-navy-700/50">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">COA</dt>
                    <dd class="font-mono text-sm font-bold text-navy-600 dark:text-navy-400">{{ $spp->coa }}</dd>
                </div>
                @foreach([
                    ['Kode Kegiatan', $spp->kode_kegiatan],
                    ['KRO',           $spp->kro],
                    ['RO',            $spp->ro . ' – ' . get_ro_name($spp->ro)],
                    ['Sub Komponen',  $spp->sub_komponen],
                    ['MAK',           $spp->mak],
                ] as [$label, $value])
                <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-navy-700/50 last:border-0">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white text-right ml-4">{{ $value }}</dd>
                </div>
                @endforeach
            </dl>
        </div>
    </div>

    {{-- Uraian SPP --}}
    <div class="card">
        <h3 class="section-title mb-3">Uraian SPP</h3>
        <div class="bg-gray-50 dark:bg-navy-700/40 rounded-xl p-4">
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $spp->uraian_spp }}</p>
        </div>
    </div>

    {{-- Dokumen Pendukung --}}
    @php
        $hasDokumen = $spp->nomor_kontrak || $spp->no_bast || $spp->id_eperjadin
                   || $spp->nomor_surat_tugas || $spp->nomor_undangan
                   || $spp->tanggal_mulai || $spp->tanggal_selesai;
    @endphp
    @if($hasDokumen)
    <div class="card">
        <h3 class="section-title mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">Dokumen Pendukung</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2.5">
            @foreach([
                ['Nomor Kontrak / SPBy', $spp->nomor_kontrak],
                ['No BAST / Kuitansi',  $spp->no_bast],
                ['ID e-Perjadin',       $spp->id_eperjadin],
                ['Nomor Surat Tugas',   $spp->nomor_surat_tugas],
                ['Tanggal ST / SK',     $spp->tanggal_st ? formatTanggalIndo($spp->tanggal_st) : null],
                ['Nomor Undangan',      $spp->nomor_undangan],
                ['Tanggal Mulai',       $spp->tanggal_mulai ? formatTanggalIndo($spp->tanggal_mulai) : null],
                ['Tanggal Selesai',     $spp->tanggal_selesai ? formatTanggalIndo($spp->tanggal_selesai) : null],
            ] as [$label, $value])
                @if($value)
                <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-navy-700/50">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white text-right ml-4">{{ $value }}</dd>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Informasi SP2D --}}
    @if($spp->status === 'Tagihan Telah SP2D')
    <div class="card border-l-4 border-emerald-400">
        <h3 class="section-title mb-4 pb-3 border-b border-gray-100 dark:border-navy-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Informasi SP2D
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @if($spp->no_sp2d)
            <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-xl">
                <p class="text-xs text-emerald-600 dark:text-emerald-400">No SP2D</p>
                <p class="font-semibold text-emerald-900 dark:text-emerald-200 mt-1">{{ $spp->no_sp2d }}</p>
            </div>
            @endif
            @if($spp->tgl_sp2d)
            <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-xl">
                <p class="text-xs text-emerald-600 dark:text-emerald-400">Tanggal SP2D</p>
                <p class="font-semibold text-emerald-900 dark:text-emerald-200 mt-1">{{ formatTanggalIndo($spp->tgl_sp2d) }}</p>
            </div>
            @endif
            @if($spp->tgl_selesai_sp2d)
            <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-xl">
                <p class="text-xs text-emerald-600 dark:text-emerald-400">Selesai SP2D</p>
                <p class="font-semibold text-emerald-900 dark:text-emerald-200 mt-1">{{ formatTanggalIndo($spp->tgl_selesai_sp2d) }}</p>
            </div>
            @endif
        </div>
        @if($spp->posisi_uang)
        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-navy-700">
            <p class="text-xs text-gray-500 mb-1.5">Posisi Uang</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-navy-700/40 rounded-lg px-3 py-2">
                {{ $spp->posisi_uang }}
            </p>
        </div>
        @endif
    </div>
    @endif

    {{-- Metadata --}}
    <div class="card-flat">
        <div class="flex flex-wrap gap-x-8 gap-y-2 text-xs text-gray-400 dark:text-gray-600">
            <span>Dibuat: {{ format_datetime($spp->created_at) }}</span>
            <span>Diperbarui: {{ format_datetime($spp->updated_at) }}</span>
            <span>COA: <code class="font-mono">{{ $spp->coa }}</code></span>
        </div>
    </div>

</div>
@endsection
