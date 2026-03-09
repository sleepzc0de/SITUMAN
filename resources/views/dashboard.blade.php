@extends('layouts.app')
@section('title', 'Dashboard')

@php
$roleMeta = [
    'superadmin'    => ['label' => 'Panel Super Administrator', 'desc' => 'Anda memiliki akses penuh ke seluruh modul sistem. Pantau semua aktivitas dari sini.',
        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'gradient' => 'from-navy-700 via-navy-800 to-slate-900', 'accent' => 'from-red-500/20 to-orange-500/10'],
    'admin'         => ['label' => 'Panel Administrator', 'desc' => 'Anda memiliki akses penuh ke seluruh modul sistem. Pantau semua aktivitas dari sini.',
        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'gradient' => 'from-navy-700 via-navy-800 to-navy-900', 'accent' => 'from-blue-500/20 to-navy-500/10'],
    'eksekutif'     => ['label' => 'Dashboard Eksekutif', 'desc' => 'Ringkasan data operasional dan kinerja organisasi untuk pengambilan keputusan strategis.',
        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'gradient' => 'from-purple-800 via-navy-800 to-navy-900', 'accent' => 'from-purple-500/20 to-pink-500/10'],
    'picpegawai'    => ['label' => 'Dashboard PIC Kepegawaian', 'desc' => 'Kelola dan pantau data kepegawaian, sebaran, dan perkembangan grading pegawai.',
        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'gradient' => 'from-sky-800 via-navy-800 to-navy-900', 'accent' => 'from-sky-500/20 to-blue-500/10'],
    'pickeuangan'   => ['label' => 'Dashboard PIC Keuangan', 'desc' => 'Pantau realisasi anggaran, monitoring SPP, dan kelola dokumen keuangan.',
        'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'gradient' => 'from-emerald-800 via-navy-800 to-navy-900', 'accent' => 'from-emerald-500/20 to-green-500/10'],
    'picinventaris' => ['label' => 'Dashboard PIC Inventaris', 'desc' => 'Kelola stok ATK, aset end user, dan pantau permintaan inventaris.',
        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'gradient' => 'from-orange-800 via-navy-800 to-navy-900', 'accent' => 'from-orange-500/20 to-amber-500/10'],
];
$meta = $roleMeta[$role] ?? [
    'label' => 'Dashboard', 'desc' => 'Selamat datang di Sistem Informasi Tata Usaha dan Manajemen (SiTUMAN).',
    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'gradient' => 'from-navy-700 via-navy-800 to-navy-900', 'accent' => 'from-gold-500/20 to-yellow-500/10'
];
$pegawaiPct = $stats['total_pegawai'] > 0
    ? round($stats['pegawai_aktif'] / $stats['total_pegawai'] * 100) : 0;
@endphp

@section('content')
<div class="space-y-6" x-data="dashboardPage()" x-init="init()">

    {{-- ══ HERO HEADER ══ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $meta['gradient'] }} text-white shadow-2xl shadow-navy-900/30">
        {{-- Decorative elements --}}
        <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-72 h-72 rounded-full bg-gold-400/10 blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-r {{ $meta['accent'] }} pointer-events-none"></div>
        {{-- Subtle grid pattern --}}
        <div class="absolute inset-0 opacity-5 pointer-events-none"
             style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 28px 28px;"></div>

        <div class="relative z-10 px-6 py-7 sm:px-8 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-white/15 backdrop-blur-sm flex items-center justify-center flex-shrink-0
                                ring-1 ring-white/20">
                        <svg class="w-5 h-5 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $meta['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold tracking-widest uppercase text-white/60">{{ $meta['label'] }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                    Selamat Datang, <span class="text-gold-300">{{ Str::words($user->nama, 2, '') }}</span>!
                </h1>
                <p class="mt-2 text-sm text-white/60 max-w-xl leading-relaxed">{{ $meta['desc'] }}</p>

                {{-- Mini stats strip --}}
                <div class="mt-4 flex flex-wrap gap-3">
                    <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1.5 text-xs font-medium">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        {{ number_format($stats['pegawai_aktif']) }} Pegawai Aktif
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1.5 text-xs font-medium">
                        <span class="w-1.5 h-1.5 bg-gold-300 rounded-full"></span>
                        {{ $stats['pegawai_per_bagian']->count() }} Unit Kerja
                    </div>
                    @if(isset($anggaranStats))
                    <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1.5 text-xs font-medium">
                        <span class="w-1.5 h-1.5 bg-sky-300 rounded-full"></span>
                        {{ number_format($anggaranStats['persentase'], 1) }}% Realisasi
                    </div>
                    @endif
                </div>
            </div>

            {{-- Date/Time card --}}
            <div class="flex-shrink-0 text-right bg-white/10 rounded-2xl px-6 py-4 backdrop-blur-sm
                        border border-white/15 min-w-[9rem]">
                <p class="text-5xl font-black text-gold-300 leading-none tabular-nums">{{ date('d') }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">{{ \Carbon\Carbon::now()->isoFormat('dddd') }}</p>
                <p class="text-xs text-white/50 mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}</p>
                <div class="mt-2 pt-2 border-t border-white/10">
                    <p class="text-xs text-white/50 font-mono"
                       x-data x-text="new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) + ' WIB'"></p>
                </div>
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
            <p class="text-sm opacity-80 mt-0.5">Akses Anda dibatasi. Silakan hubungi administrator.</p>
        </div>
        <button @click="show = false" class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- ══ STATS GRID ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Pegawai --}}
        <div class="stat-card group col-span-1"
             x-data="{ count: 0 }"
             x-intersect.once="animateCount($el, {{ $stats['total_pegawai'] }})">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Pegawai</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums counter-value">
                        {{ number_format($stats['total_pegawai']) }}
                    </p>
                    <div class="flex items-center gap-1 mt-1.5">
                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400
                                     bg-emerald-50 dark:bg-emerald-900/30 px-1.5 py-0.5 rounded-md">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            {{ number_format($stats['pegawai_aktif']) }}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">aktif</span>
                    </div>
                </div>
                <div class="stat-icon bg-navy-50 dark:bg-navy-700 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 progress-bar">
                <div class="progress-fill bg-gradient-to-r from-navy-500 to-navy-600"
                     style="width: 0%"
                     x-intersect.once="$el.style.width = '{{ $pegawaiPct }}%'"></div>
            </div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5 font-medium">{{ $pegawaiPct }}% pegawai aktif</p>
        </div>

        {{-- Unit/Bagian --}}
        <div class="stat-card group col-span-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Unit / Bagian</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                        {{ $stats['pegawai_per_bagian']->count() }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Unit kerja aktif</p>
                </div>
                <div class="stat-icon bg-purple-50 dark:bg-purple-900/20 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            {{-- Sparkline mini placeholder --}}
            <div class="mt-3 flex items-end gap-0.5 h-6">
                @foreach($stats['pegawai_per_bagian']->take(8) as $i => $b)
                @php $h = max(20, min(100, ($b->total / max($stats['pegawai_per_bagian']->max('total'), 1)) * 100)); @endphp
                <div class="flex-1 rounded-sm bg-purple-200 dark:bg-purple-800/40 transition-all duration-500 hover:bg-purple-400"
                     style="height: {{ $h }}%; opacity: {{ 0.4 + ($i * 0.08) }}"></div>
                @endforeach
            </div>
        </div>

        {{-- Realisasi Anggaran --}}
        @if(in_array($role, ['superadmin','admin','eksekutif','pickeuangan']) && isset($anggaranStats))
        <div class="stat-card group col-span-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Realisasi</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                        {{ number_format($anggaranStats['persentase'], 1) }}<span class="text-lg text-gray-400">%</span>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Penyerapan anggaran</p>
                </div>
                <div class="stat-icon bg-emerald-50 dark:bg-emerald-900/20 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            {{-- Circular progress indicator --}}
            <div class="mt-3 flex items-center gap-2.5">
                <div class="relative w-10 h-10 flex-shrink-0">
                    <svg class="w-10 h-10 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15" fill="none" stroke="#d1fae5" stroke-width="3"
                                class="dark:stroke-emerald-900/40"/>
                        <circle cx="18" cy="18" r="15" fill="none" stroke="#10b981" stroke-width="3"
                                stroke-dasharray="{{ min($anggaranStats['persentase'], 100) * 0.942 }} 94.2"
                                stroke-linecap="round"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-[8px] font-bold text-emerald-700 dark:text-emerald-400">
                        {{ round($anggaranStats['persentase']) }}%
                    </span>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                        Rp {{ number_format($anggaranStats['total_realisasi'] / 1e6, 1) }}M
                    </p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">
                        dari Rp {{ number_format($anggaranStats['total_pagu'] / 1e6, 1) }}M
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Pagu --}}
        <div class="stat-card group col-span-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Pagu</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                        Rp {{ number_format($anggaranStats['total_pagu'] / 1e6, 1) }}M
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Anggaran tahun {{ date('Y') }}</p>
                </div>
                <div class="stat-icon bg-gold-50 dark:bg-yellow-900/20 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-gold-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-1.5">
                <div class="bg-gray-50 dark:bg-navy-700/50 rounded-lg px-2 py-1.5 text-center">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">Sisa</p>
                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
                        Rp{{ number_format($anggaranStats['total_sisa'] / 1e6, 0) }}M
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-navy-700/50 rounded-lg px-2 py-1.5 text-center">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">Outstanding</p>
                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400">
                        Rp{{ number_format(($anggaranStats['total_outstanding'] ?? 0) / 1e6, 0) }}M
                    </p>
                </div>
            </div>
        </div>

        @elseif(in_array($role, ['superadmin','admin']))
        {{-- Total Users --}}
        <div class="stat-card group col-span-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total User</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Pengguna terdaftar</p>
                </div>
                <div class="stat-icon bg-sky-50 dark:bg-sky-900/20 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        @if(isset($inventarisStats))
        <div class="stat-card group col-span-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Aset</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1 tabular-nums">
                        {{ number_format($inventarisStats['total_aset']) }}
                    </p>
                    @if(($inventarisStats['atk_menipis'] ?? 0) > 0)
                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-1.5 flex items-center gap-1 font-medium">
                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse"></span>
                        {{ $inventarisStats['atk_menipis'] }} ATK menipis
                    </p>
                    @else
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1.5 flex items-center gap-1 font-medium">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                        Stok aman
                    </p>
                    @endif
                </div>
                <div class="stat-icon bg-orange-50 dark:bg-orange-900/20 group-hover:scale-110 transition-transform duration-200">
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

    {{-- ══ KONTEN PER ROLE ══ --}}
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

@push('scripts')
<script>
function dashboardPage() {
    return {
        init() {
            // Animate counters on intersect is handled per element via x-intersect
        },
        animateCount(el, target) {
            const el2 = el.querySelector('.counter-value');
            if (!el2) return;
            let start = 0;
            const duration = 1000;
            const step = (timestamp) => {
                if (!start) start = timestamp;
                const progress = Math.min((timestamp - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el2.textContent = Math.floor(eased * target).toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(step);
                else el2.textContent = target.toLocaleString('id-ID');
            };
            requestAnimationFrame(step);
        }
    }
}
</script>
@endpush
