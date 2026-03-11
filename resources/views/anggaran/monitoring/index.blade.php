@extends('layouts.app')

@section('title', 'Monitoring Anggaran')

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Monitoring Anggaran</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Monitoring Anggaran</h1>
        <p class="page-subtitle">Pantau realisasi, SPP, dan sisa anggaran secara terintegrasi</p>
    </div>
    <div class="flex flex-wrap gap-2">

        @hasrole('superadmin|admin')
        <form method="POST"
              action="{{ route('anggaran.monitoring.recalculate') }}"
              onsubmit="return confirm('Recalculate semua data anggaran dari SPP?\nProses ini mungkin memakan waktu beberapa saat.')">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11
                             11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Recalculate
            </button>
        </form>
        @endhasrole

        <button onclick="window.print()" class="btn btn-outline btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0
                         002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2
                         2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak
        </button>

        <a href="{{ route('anggaran.monitoring.export', array_filter(['ro' => $ro, 'subkomponen' => $subkomponen], fn($v) => $v !== 'all')) }}"
           class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                         1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>

    </div>
</div>
@endsection


@section('content')
<div class="space-y-6">

    {{-- ===== FILTERS ===== --}}
    <div class="card">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="input-group">
                <label class="input-label">RO</label>
                <select name="ro" class="input-field" data-auto-submit>
                    <option value="all">Semua RO</option>
                    @foreach($roList as $item)
                        <option value="{{ $item['code'] }}"
                                {{ $ro === $item['code'] ? 'selected' : '' }}>
                            {{ $item['code'] }} — {{ Str::limit($item['name'], 35) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Sub Komponen</label>
                <select name="subkomponen" class="input-field" data-auto-submit>
                    <option value="all">Semua Sub Komponen</option>
                    @foreach($subkomponenList as $code => $name)
                        <option value="{{ $code }}"
                                {{ $subkomponen === $code ? 'selected' : '' }}>
                            {{ $code }} — {{ Str::limit($name, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Bulan</label>
                <select name="bulan" class="input-field" data-auto-submit>
                    <option value="all">Semua Bulan</option>
                    @foreach($bulanList as $b)
                        <option value="{{ $b }}"
                                {{ $bulan === $b ? 'selected' : '' }}>
                            {{ ucfirst($b) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">&nbsp;</label>
                <a href="{{ route('anggaran.monitoring.index') }}" class="btn btn-ghost w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </a>
            </div>

        </form>
    </div>

    {{-- ===== KPI CARDS ===== --}}
    @php
        $pctRealisasi  = calculate_percentage($totalRealisasi, $totalPagu);
        $pctSisa       = calculate_percentage($totalSisa, $totalPagu);
        $gradRealisasi = $pctRealisasi >= 80
            ? 'from-emerald-500 to-emerald-700'
            : ($pctRealisasi >= 50 ? 'from-yellow-500 to-yellow-600' : 'from-red-500 to-red-700');
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Pagu --}}
        <div class="card bg-gradient-to-br from-navy-600 to-navy-800 border-0 text-white">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold text-navy-200 uppercase tracking-wider">
                        Total Pagu
                    </p>
                    <p class="text-lg lg:text-xl font-bold mt-1 truncate">
                        {{ formatRupiah($totalPagu) }}
                    </p>
                    <p class="text-[11px] text-navy-300 mt-1">Anggaran ditetapkan</p>
                </div>
                <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3
                                 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11
                                 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Realisasi --}}
        <div class="card bg-gradient-to-br {{ $gradRealisasi }} border-0 text-white">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold text-white/70 uppercase tracking-wider">
                        Total Realisasi
                    </p>
                    <p class="text-lg lg:text-xl font-bold mt-1 truncate">
                        {{ formatRupiah($totalRealisasi) }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-2">
                        <div class="flex-1 h-1 bg-white/25 rounded-full overflow-hidden">
                            <div class="h-full bg-white rounded-full transition-all duration-700"
                                 style="width:{{ min($pctRealisasi, 100) }}%"></div>
                        </div>
                        <span class="text-[11px] font-bold">{{ number_format($pctRealisasi, 1) }}%</span>
                    </div>
                </div>
                <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Outstanding --}}
        <div class="card bg-gradient-to-br from-amber-500 to-orange-600 border-0 text-white">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold text-white/70 uppercase tracking-wider">
                        Outstanding
                    </p>
                    <p class="text-lg lg:text-xl font-bold mt-1 truncate">
                        {{ formatRupiah($totalOutstanding) }}
                    </p>
                    <p class="text-[11px] text-white/70 mt-1">Tagihan belum SP2D</p>
                </div>
                <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Sisa Anggaran --}}
        <div class="card bg-gradient-to-br from-purple-600 to-purple-800 border-0 text-white">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold text-white/70 uppercase tracking-wider">
                        Sisa Anggaran
                    </p>
                    <p class="text-lg lg:text-xl font-bold mt-1 truncate">
                        {{ formatRupiah($totalSisa) }}
                    </p>
                    <p class="text-[11px] text-white/70 mt-1">
                        {{ number_format($pctSisa, 1) }}% dari pagu
                    </p>
                </div>
                <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0
                                 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== ALERT THRESHOLD ===== --}}
    @if($pctRealisasi < 50 && $totalPagu > 0)
    <div class="alert alert-warning">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732
                     4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold">
                Penyerapan Anggaran Rendah — {{ number_format($pctRealisasi, 1) }}%
            </p>
            <p class="text-sm mt-0.5">
                Segera tindaklanjuti
                <a href="{{ route('anggaran.usulan.index') }}"
                   class="underline font-medium hover:no-underline">
                    usulan penarikan
                </a>
                dan percepat realisasi kegiatan.
            </p>
        </div>
    </div>
    @endif

    {{-- ===== CHART + PANEL INTEGRASI ===== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Chart Realisasi per Bulan --}}
        <div class="xl:col-span-2 card">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Tren Realisasi per Bulan</h3>
                    <p class="section-desc">
                        Penyerapan bulanan (SP2D) + kumulatif sepanjang tahun
                    </p>
                </div>
            </div>
            <div class="relative h-64">
                <canvas id="chartRealisasi"></canvas>
            </div>
        </div>

        {{-- Panel Kanan --}}
        <div class="flex flex-col gap-4">

            {{-- SPP Stats --}}
            <div class="card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="section-title">Status SPP</h3>
                    <a href="{{ route('anggaran.spp.index') }}"
                       class="text-xs font-medium text-navy-600 dark:text-navy-400 hover:underline">
                        Lihat semua →
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-gray-50 dark:bg-navy-700/50 rounded-xl py-3 px-2">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $sppStats['total'] }}
                        </p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Total</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl py-3 px-2">
                        <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
                            {{ $sppStats['sudah_sp2d'] }}
                        </p>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-500 mt-0.5">SP2D</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl py-3 px-2">
                        <p class="text-2xl font-bold text-amber-700 dark:text-amber-400">
                            {{ $sppStats['belum_sp2d'] }}
                        </p>
                        <p class="text-[11px] text-amber-600 dark:text-amber-500 mt-0.5">Pending</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Nilai sudah SP2D</p>
                    <p class="text-base font-bold text-gray-900 dark:text-white mt-0.5">
                        {{ formatRupiah($sppStats['nilai_sp2d']) }}
                    </p>
                </div>
            </div>

            {{-- Usulan Pending --}}
            <div class="card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="section-title">Usulan Pending</h3>
                    <a href="{{ route('anggaran.usulan.index') }}"
                       class="text-xs font-medium text-navy-600 dark:text-navy-400 hover:underline">
                        Lihat semua →
                    </a>
                </div>

                @if($usulanPending->isEmpty())
                    <div class="text-center py-4">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Tidak ada usulan pending
                        </p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($usulanPending->take(3) as $usulan)
                        <div class="flex items-center justify-between gap-2 p-2.5
                                    bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    RO {{ $usulan->ro }} · {{ ucfirst($usulan->bulan) }}
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                    {{ $usulan->user->nama ?? '-' }}
                                </p>
                            </div>
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400 flex-shrink-0">
                                {{ formatRupiahShort($usulan->nilai_usulan) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700
                                flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Total nilai pending
                        </span>
                        <span class="text-sm font-bold text-amber-600 dark:text-amber-400">
                            {{ formatRupiah($totalUsulanPending) }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Dokumen Capaian --}}
            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="section-title">Dokumen Capaian</h3>
                        <p class="mt-1.5">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $dokumenCount }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                dokumen terupload
                            </span>
                        </p>
                    </div>
                    <a href="{{ route('anggaran.dokumen.index') }}" class="btn btn-outline btn-sm">
                        Kelola
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== SPP TERBARU ===== --}}
    @if($recentSPP->isNotEmpty())
    <div class="card">
        <div class="section-header">
            <div>
                <h3 class="section-title">SPP Terbaru</h3>
                <p class="section-desc">10 transaksi SPP terakhir yang tercatat</p>
            </div>
            <a href="{{ route('anggaran.spp.index') }}" class="btn btn-outline btn-sm">
                Lihat Semua SPP
            </a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. SPP</th>
                        <th>Tanggal</th>
                        <th>Uraian</th>
                        <th>RO</th>
                        <th class="text-right">Netto</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSPP as $spp)
                    <tr>
                        <td class="font-mono text-xs whitespace-nowrap">{{ $spp->no_spp }}</td>
                        <td class="text-xs whitespace-nowrap">
                            {{ $spp->tgl_spp ? format_tanggal_short($spp->tgl_spp) : '-' }}
                        </td>
                        <td>
                            <span class="line-clamp-2 text-xs">{{ $spp->uraian_spp }}</span>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $spp->ro }}</span>
                        </td>
                        <td class="text-right font-semibold text-sm whitespace-nowrap">
                            {{ formatRupiah($spp->netto) }}
                        </td>
                        <td class="text-center">
                            @if($spp->status === 'Tagihan Telah SP2D')
                                <span class="badge badge-success">SP2D</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ===== TABEL DETAIL PER RO ===== --}}
    @forelse($groupedData as $roCode => $roData)
        @php
            $roLevel            = $roData->whereNull('kode_subkomponen')->whereNull('kode_akun')->first();
            $roLevelPagu        = $roLevel?->pagu_anggaran       ?? $roData->whereNull('kode_akun')->sum('pagu_anggaran');
            $roLevelRealisasi   = $roLevel?->total_penyerapan    ?? $roData->whereNull('kode_akun')->sum('total_penyerapan');
            $roLevelSisa        = $roLevel?->sisa                ?? $roData->whereNull('kode_akun')->sum('sisa');
            $roLevelOutstanding = $roLevel?->tagihan_outstanding ?? 0;
            $roPct              = calculate_percentage($roLevelRealisasi, $roLevelPagu);
            $roBarColor         = progressBarColor($roPct);
            $roBadge            = statusAnggaranBadge($roPct);
            $roName             = collect($roList)->firstWhere('code', $roCode)['name'] ?? $roCode;
        @endphp

        <div class="card" x-data="{ expanded: true }">

            {{-- RO Header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="badge badge-info font-mono">RO {{ $roCode }}</span>
                        <span class="badge {{ $roBadge }}">{{ number_format($roPct, 1) }}%</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-snug">
                        {{ $roName }}
                    </h3>
                    <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-2
                                text-xs text-gray-500 dark:text-gray-400">
                        <span>
                            Pagu:
                            <strong class="text-gray-900 dark:text-white">
                                {{ formatRupiah($roLevelPagu) }}
                            </strong>
                        </span>
                        <span>
                            Realisasi:
                            <strong class="text-emerald-600 dark:text-emerald-400">
                                {{ formatRupiah($roLevelRealisasi) }}
                            </strong>
                        </span>
                        @if($roLevelOutstanding > 0)
                        <span>
                            Outstanding:
                            <strong class="text-amber-600 dark:text-amber-400">
                                {{ formatRupiah($roLevelOutstanding) }}
                            </strong>
                        </span>
                        @endif
                        <span>
                            Sisa:
                            <strong class="text-purple-600 dark:text-purple-400">
                                {{ formatRupiah($roLevelSisa) }}
                            </strong>
                        </span>
                    </div>
                    <div class="mt-2.5 progress-bar-wrap">
                        <div class="{{ $roBarColor }} progress-bar"
                             style="width:{{ min($roPct, 100) }}%"></div>
                    </div>
                </div>
                <button @click="expanded = !expanded"
                        class="btn btn-ghost btn-sm flex-shrink-0 self-start">
                    <svg class="w-4 h-4 transition-transform duration-200"
                         :class="{ 'rotate-180': !expanded }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span x-text="expanded ? 'Sembunyikan' : 'Tampilkan'"></span>
                </button>
            </div>

            {{-- Tabel Collapsible --}}
            <div x-show="expanded" x-collapse>
                <div class="table-wrapper">
                    <table class="table text-xs" id="tbl-ro-{{ $roCode }}">
                        <thead>
                            <tr>
                                <th class="min-w-52">Sub Komponen / Akun</th>
                                <th class="text-right whitespace-nowrap">Pagu</th>
                                @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $bl)
                                    <th class="text-right whitespace-nowrap">{{ $bl }}</th>
                                @endforeach
                                <th class="text-right whitespace-nowrap">Outstanding</th>
                                <th class="text-right whitespace-nowrap">Realisasi</th>
                                <th class="text-right whitespace-nowrap">Sisa</th>
                                <th class="text-center whitespace-nowrap">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roData as $item)
                                @php
                                    $isAkun  = !empty($item->kode_akun);
                                    $isSubk  = !$isAkun && !empty($item->kode_subkomponen);
                                    $isRORow = !$isAkun && !$isSubk;
                                    $pct     = calculate_percentage($item->total_penyerapan, $item->pagu_anggaran);

                                    $rowClass = match(true) {
                                        $isRORow => 'bg-navy-50 dark:bg-navy-700/40 font-bold',
                                        $isSubk  => 'bg-gray-50 dark:bg-navy-800/60 font-semibold',
                                        default  => '',
                                    };
                                    $indent = match(true) {
                                        $isRORow => 'pl-3',
                                        $isSubk  => 'pl-6',
                                        default  => 'pl-10',
                                    };
                                @endphp
                                <tr class="{{ $rowClass }} hover:bg-blue-50/40
                                           dark:hover:bg-navy-700/20 transition-colors duration-100">

                                    {{-- Label --}}
                                    <td class="py-2.5 {{ $indent }}">
                                        @if(!$isRORow)
                                            <span class="font-mono text-[10px] text-gray-400
                                                         mr-1.5 select-all">
                                                {{ $isSubk ? $item->kode_subkomponen : $item->kode_akun }}
                                            </span>
                                        @endif
                                        <span class="{{ $isRORow
                                            ? 'text-navy-700 dark:text-navy-300'
                                            : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ Str::limit($item->program_kegiatan, $isRORow ? 60 : 50) }}
                                        </span>
                                    </td>

                                    {{-- Pagu --}}
                                    <td class="py-2.5 text-right font-semibold whitespace-nowrap
                                               text-gray-900 dark:text-white">
                                        {{ formatRupiah($item->pagu_anggaran) }}
                                    </td>

                                    {{-- Bulan Jan–Des --}}
                                    @foreach([
                                        'januari','februari','maret','april','mei','juni',
                                        'juli','agustus','september','oktober','november','desember'
                                    ] as $bf)
                                        <td class="py-2.5 text-right whitespace-nowrap
                                            {{ $item->$bf > 0
                                                ? 'text-emerald-600 dark:text-emerald-400'
                                                : 'text-gray-300 dark:text-gray-600' }}">
                                            {{ $item->$bf > 0 ? formatRupiah($item->$bf) : '—' }}
                                        </td>
                                    @endforeach

                                    {{-- Outstanding --}}
                                    <td class="py-2.5 text-right whitespace-nowrap
                                        {{ $item->tagihan_outstanding > 0
                                            ? 'text-amber-600 dark:text-amber-400 font-medium'
                                            : 'text-gray-300 dark:text-gray-600' }}">
                                        {{ $item->tagihan_outstanding > 0
                                            ? formatRupiah($item->tagihan_outstanding)
                                            : '—' }}
                                    </td>

                                    {{-- Realisasi --}}
                                    <td class="py-2.5 text-right font-semibold whitespace-nowrap
                                               text-emerald-600 dark:text-emerald-400">
                                        {{ formatRupiah($item->total_penyerapan) }}
                                    </td>

                                    {{-- Sisa --}}
                                    <td class="py-2.5 text-right font-semibold whitespace-nowrap
                                               text-purple-600 dark:text-purple-400">
                                        {{ formatRupiah($item->sisa) }}
                                    </td>

                                    {{-- % --}}
                                    <td class="py-2.5 text-center">
                                        <span class="badge {{ statusAnggaranBadge($pct) }}">
                                            {{ number_format($pct, 1) }}%
                                        </span>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @empty
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                                 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="empty-state-title">Tidak ada data anggaran</p>
                <p class="empty-state-desc">
                    Sesuaikan filter atau tambahkan data anggaran terlebih dahulu.
                </p>
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-primary btn-sm mt-3">
                    Kelola Data Anggaran
                </a>
            </div>
        </div>
    @endforelse

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const ctx = document.getElementById('chartRealisasi');
    if (!ctx || typeof Chart === 'undefined') return;

    const labels     = @json($chartLabels);
    const data       = @json($chartData);
    const isDark     = document.documentElement.classList.contains('dark');
    const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const labelColor = isDark ? '#9fb3c8' : '#627d98';

    // Kumulatif
    const cumulative = data.reduce((acc, v, i) => {
        acc.push((acc[i - 1] ?? 0) + v);
        return acc;
    }, []);

    new Chart(ctx, {
        data: {
            labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Realisasi Bulanan',
                    data,
                    backgroundColor: 'rgba(16,185,129,0.55)',
                    borderColor:     'rgba(16,185,129,0.85)',
                    borderWidth:     1.5,
                    borderRadius:    5,
                    borderSkipped:   false,
                    order:           2,
                },
                {
                    type: 'line',
                    label: 'Kumulatif',
                    data: cumulative,
                    borderColor:        '#486581',
                    backgroundColor:    'rgba(72,101,129,0.07)',
                    borderWidth:        2,
                    pointRadius:        3,
                    pointHoverRadius:   5,
                    pointBackgroundColor: '#486581',
                    tension:            0.35,
                    fill:               true,
                    order:              1,
                }
            ]
        },
        options: {
            responsive:           true,
            maintainAspectRatio:  false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display:  true,
                    position: 'top',
                    labels: {
                        color:    labelColor,
                        font:     { size: 11 },
                        boxWidth: 12,
                        padding:  14,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx =>
                            `${ctx.dataset.label}: ${window.formatCurrencyShort(ctx.parsed.y)}`
                    }
                }
            },
            scales: {
                x: {
                    grid:  { color: gridColor },
                    ticks: { color: labelColor, font: { size: 10 } },
                },
                y: {
                    grid:  { color: gridColor },
                    ticks: {
                        color:    labelColor,
                        font:     { size: 10 },
                        callback: v => window.formatCurrencyShort(v),
                    },
                    beginAtZero: true,
                }
            }
        }
    });
});
</script>
@endpush
