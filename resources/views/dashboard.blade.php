@extends('layouts.app')
@section('title', 'Dashboard')

@php
// ── Role meta config ──────────────────────────────────────
$roleMeta = [
    'superadmin'    => ['label' => 'Panel Super Administrator', 'desc' => 'Anda memiliki akses penuh ke seluruh modul sistem. Pantau semua aktivitas dari sini.',
        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
    'admin'         => ['label' => 'Panel Administrator',       'desc' => 'Anda memiliki akses penuh ke seluruh modul sistem. Pantau semua aktivitas dari sini.',
        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
    'eksekutif'     => ['label' => 'Dashboard Eksekutif',       'desc' => 'Ringkasan data operasional dan kinerja organisasi untuk pengambilan keputusan.',
        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    'picpegawai'    => ['label' => 'Dashboard PIC Kepegawaian', 'desc' => 'Kelola dan pantau data kepegawaian, sebaran, dan perkembangan grading pegawai.',
        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
    'pickeuangan'   => ['label' => 'Dashboard PIC Keuangan',   'desc' => 'Pantau realisasi anggaran, monitoring SPP, dan kelola dokumen keuangan.',
        'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    'picinventaris' => ['label' => 'Dashboard PIC Inventaris', 'desc' => 'Kelola stok ATK, aset end user, dan pantau permintaan inventaris.',
        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
];
$meta = $roleMeta[$role] ?? ['label' => 'Dashboard', 'desc' => 'Selamat datang di Sistem Informasi Tata Usaha dan Manajemen (SiTUMAN).', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'];

$pegawaiPct = $stats['total_pegawai'] > 0
    ? round($stats['pegawai_aktif'] / $stats['total_pegawai'] * 100)
    : 0;
@endphp

@push('styles')
<style>
    .stat-card { @apply bg-white dark:bg-navy-800 rounded-2xl p-5 border border-gray-100 dark:border-navy-700 shadow-sm hover:shadow-md transition-all duration-200; }
    .stat-icon { @apply w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0; }
    .chart-card { @apply bg-white dark:bg-navy-800 rounded-2xl p-5 border border-gray-100 dark:border-navy-700 shadow-sm; }
    .chart-title { @apply text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4; }
    .progress-bar { @apply w-full bg-gray-100 dark:bg-navy-700 rounded-full overflow-hidden; height: 6px; }
    .progress-fill { @apply h-full rounded-full transition-all duration-700; }
    .info-row { @apply flex items-center justify-between py-2.5 border-b border-gray-50 dark:border-navy-700/60 last:border-0; }
</style>
@endpush

@section('content')
<div class="space-y-6 animate-fade-in">

{{-- ══════════════════════════════════════════════════════════
     HERO HEADER
══════════════════════════════════════════════════════════ --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 text-white shadow-xl shadow-navy-900/20">
    {{-- Decorative blobs --}}
    <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full bg-gold-400/10 blur-2xl pointer-events-none"></div>
    <div class="absolute top-0 right-0 w-96 h-full bg-gradient-to-l from-navy-900/40 to-transparent pointer-events-none"></div>

    <div class="relative z-10 px-6 py-7 sm:px-8 flex flex-col sm:flex-row sm:items-center gap-5">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold tracking-widest uppercase text-navy-300">{{ $meta['label'] }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                Selamat Datang, <span class="text-gold-300">{{ Str::words($user->nama, 2, '') }}</span>!
            </h1>
            <p class="mt-2 text-sm text-navy-300 max-w-xl leading-relaxed">{{ $meta['desc'] }}</p>
        </div>

        <div class="flex-shrink-0 text-right sm:text-right bg-white/10 rounded-2xl px-6 py-4 backdrop-blur-sm border border-white/10">
            <p class="text-4xl font-bold text-gold-300 leading-none">{{ date('d') }}</p>
            <p class="text-sm text-navy-200 mt-1">{{ \Carbon\Carbon::now()->isoFormat('dddd') }}</p>
            <p class="text-xs text-navy-300 mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}</p>
            <p class="text-xs text-navy-400 mt-2 font-mono" x-data x-text="new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'}) + ' WIB'"></p>
        </div>
    </div>
</div>

{{-- No-role alert --}}
@if(session('no_role'))
<div x-data="{ show: true }" x-show="show"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="flex items-start gap-3 px-4 py-3.5 rounded-2xl bg-amber-50 dark:bg-amber-900/20
            border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-400">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div class="flex-1">
        <p class="text-sm font-semibold">Akun Belum Memiliki Role</p>
        <p class="text-sm opacity-80 mt-0.5">Akses Anda dibatasi. Silakan hubungi administrator untuk mendapatkan role yang sesuai.</p>
    </div>
    <button @click="show = false" class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     STATS GRID
══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

    {{-- Total Pegawai --}}
    <div class="stat-card col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Pegawai</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                    {{ number_format($stats['total_pegawai']) }}
                </p>
                <div class="flex items-center gap-1 mt-1.5">
                    <span class="inline-flex items-center gap-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        {{ number_format($stats['pegawai_aktif']) }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">aktif</span>
                </div>
            </div>
            <div class="stat-icon bg-navy-50 dark:bg-navy-700">
                <svg class="w-6 h-6 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 progress-bar">
            <div class="progress-fill bg-navy-500" style="width: {{ $pegawaiPct }}%"></div>
        </div>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">{{ $pegawaiPct }}% pegawai aktif</p>
    </div>

    {{-- Unit/Bagian --}}
    <div class="stat-card col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Unit / Bagian</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                    {{ $stats['pegawai_per_bagian']->count() }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Unit kerja aktif</p>
            </div>
            <div class="stat-icon bg-purple-50 dark:bg-purple-900/20">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Realisasi Anggaran --}}
    @if(in_array($role, ['superadmin','admin','eksekutif','pickeuangan']) && isset($anggaranStats))
    <div class="stat-card col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Realisasi</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                    {{ number_format($anggaranStats['persentase'], 1) }}<span class="text-lg">%</span>
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Penyerapan anggaran</p>
            </div>
            <div class="stat-icon bg-emerald-50 dark:bg-emerald-900/20">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 progress-bar">
            <div class="progress-fill bg-emerald-500" style="width: {{ min($anggaranStats['persentase'], 100) }}%"></div>
        </div>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">
            Rp {{ number_format($anggaranStats['total_realisasi'] / 1e6, 1) }}M dari Rp {{ number_format($anggaranStats['total_pagu'] / 1e6, 1) }}M
        </p>
    </div>

    {{-- Total Pagu --}}
    <div class="stat-card col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Pagu</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                    Rp {{ number_format($anggaranStats['total_pagu'] / 1e6, 1) }}M
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Anggaran tahun {{ date('Y') }}</p>
            </div>
            <div class="stat-icon bg-gold-50 dark:bg-gold-900/20">
                <svg class="w-6 h-6 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    @elseif(in_array($role, ['superadmin','admin']))
    {{-- Total Users --}}
    <div class="stat-card col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total User</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">{{ $stats['total_users'] }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Pengguna terdaftar</p>
            </div>
            <div class="stat-icon bg-sky-50 dark:bg-sky-900/20">
                <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Inventaris --}}
    @if(isset($inventarisStats))
    <div class="stat-card col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Aset</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                    {{ number_format($inventarisStats['total_aset']) }}
                </p>
                @if(($inventarisStats['atk_menipis'] ?? 0) > 0)
                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $inventarisStats['atk_menipis'] }} ATK menipis
                </p>
                @else
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Stok aman</p>
                @endif
            </div>
            <div class="stat-icon bg-orange-50 dark:bg-orange-900/20">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>
    @endif
    @endif

</div>

{{-- ══════════════════════════════════════════════════════════
     KONTEN PER ROLE
══════════════════════════════════════════════════════════ --}}
@switch($role)
    @case('superadmin') @case('admin')
        @include('dashboard.partials.admin')
        @break
    @case('eksekutif')
        @include('dashboard.partials.eksekutif')
        @break
    @case('picpegawai')
        @include('dashboard.partials.pic-pegawai')
        @break
    @case('pickeuangan')
        @include('dashboard.partials.pic-keuangan')
        @break
    @case('picinventaris')
        @include('dashboard.partials.pic-inventaris')
        @break
    @default
        @include('dashboard.partials.user-biasa')
@endswitch

</div>
@endsection
