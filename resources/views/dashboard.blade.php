@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

{{-- ============================================================
     HEADER - Berbeda per role
     ============================================================ --}}
<div class="bg-gradient-to-r from-navy-700 via-navy-800 to-navy-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-gold-500/10 rounded-full translate-y-24 -translate-x-24"></div>

    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    @switch($role)
                        @case('superadmin') @case('admin')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            @break
                        @case('eksekutif')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            @break
                        @default
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    @endswitch
                </div>
                <span class="text-sm font-medium text-navy-300 uppercase tracking-wide">
                    @switch($role)
                        @case('superadmin') Panel Super Administrator @break
                        @case('admin') Panel Administrator @break
                        @case('eksekutif') Dashboard Eksekutif @break
                        @case('picpegawai') Dashboard PIC Kepegawaian @break
                        @case('pickeuangan') Dashboard PIC Keuangan @break
                        @case('picinventaris') Dashboard PIC Inventaris @break
                        @default Dashboard @break
                    @endswitch
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold">
                Selamat Datang, {{ Str::words($user->nama, 2, '') }}!
            </h1>
            <p class="mt-1 text-navy-300 text-sm">
                @switch($role)
                    @case('superadmin') @case('admin')
                        Anda memiliki akses penuh ke seluruh modul sistem. Pantau semua aktivitas dari sini.
                        @break
                    @case('eksekutif')
                        Ringkasan data operasional dan kinerja organisasi untuk pengambilan keputusan.
                        @break
                    @case('picpegawai')
                        Kelola dan pantau data kepegawaian, sebaran, dan perkembangan grading pegawai.
                        @break
                    @case('pickeuangan')
                        Pantau realisasi anggaran, monitoring SPP, dan kelola dokumen keuangan.
                        @break
                    @case('picinventaris')
                        Kelola stok ATK, aset end user, dan pantau permintaan inventaris.
                        @break
                    @default
                        Selamat datang di Sistem Informasi Tata Usaha dan Manajemen (SiTUMAN).
                        @break
                @endswitch
            </p>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-3xl font-bold text-gold-400">{{ date('d') }}</p>
            <p class="text-sm text-navy-300">{{ \Carbon\Carbon::now()->isoFormat('dddd, MMMM Y') }}</p>
            <p class="text-xs text-navy-400 mt-1">{{ \Carbon\Carbon::now()->format('H:i') }} WIB</p>
        </div>
    </div>
</div>

{{-- Alert no role --}}
@if(session('no_role'))
<div x-data="{ show: true }" x-show="show"
    class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4 flex items-start space-x-3">
    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
    </svg>
    <div class="flex-1">
        <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-400">Akun Belum Memiliki Role</p>
        <p class="text-sm text-yellow-700 dark:text-yellow-500 mt-0.5">Akses Anda dibatasi. Silakan hubungi administrator untuk mendapatkan role yang sesuai.</p>
    </div>
    <button @click="show = false" class="text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-300 flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>
</div>
@endif

{{-- ============================================================
     STATS CARDS - Selalu tampil (adaptif per role)
     ============================================================ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total Pegawai -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Pegawai</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_pegawai']) }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    {{ number_format($stats['pegawai_aktif']) }} aktif
                </p>
            </div>
            <div class="w-12 h-12 bg-navy-100 dark:bg-navy-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-3 w-full bg-gray-100 dark:bg-navy-700 rounded-full h-1.5">
            @php $pct = $stats['total_pegawai'] > 0 ? round(($stats['pegawai_aktif']/$stats['total_pegawai'])*100) : 0; @endphp
            <div class="bg-navy-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $pct }}% aktif</p>
    </div>

    <!-- Bagian -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Unit/Bagian</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pegawai_per_bagian']->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unit kerja aktif</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>
    </div>

    @if(in_array($role, ['superadmin', 'admin', 'eksekutif', 'pickeuangan']) && isset($anggaranStats))
    <!-- Realisasi Anggaran -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Realisasi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($anggaranStats['persentase'] ?? 0, 1) }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Penyerapan anggaran</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-3 w-full bg-gray-100 dark:bg-navy-700 rounded-full h-1.5">
            <div class="bg-green-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ min($anggaranStats['persentase'] ?? 0, 100) }}%"></div>
        </div>
    </div>

    <!-- Total Pagu -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Pagu</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                    Rp {{ number_format(($anggaranStats['total_pagu'] ?? 0)/1000000, 1) }}M
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Anggaran tahun ini</p>
            </div>
            <div class="w-12 h-12 bg-gold-100 dark:bg-gold-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>
    @elseif(in_array($role, ['superadmin', 'admin']))
    <!-- Total Users -->
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total User</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_users'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pengguna aktif</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
    </div>
    <!-- Inventaris -->
    @if(isset($inventarisStats))
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Aset</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($inventarisStats['total_aset']) }}</p>
                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">{{ $inventarisStats['atk_menipis'] ?? 0 }} ATK menipis</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>

{{-- ============================================================
     KONTEN UTAMA - Berdasarkan Role
     ============================================================ --}}

{{-- SUPERADMIN & ADMIN: Tampilan lengkap semua modul --}}
@if(in_array($role, ['superadmin', 'admin']))
    @include('dashboard.partials.admin')
@endif

{{-- EKSEKUTIF: Ringkasan eksekutif --}}
@if($role === 'eksekutif')
    @include('dashboard.partials.eksekutif')
@endif

{{-- PIC PEGAWAI: Fokus kepegawaian --}}
@if($role === 'picpegawai')
    @include('dashboard.partials.pic-pegawai')
@endif

{{-- PIC KEUANGAN: Fokus anggaran --}}
@if($role === 'pickeuangan')
    @include('dashboard.partials.pic-keuangan')
@endif

{{-- PIC INVENTARIS: Fokus inventaris --}}
@if($role === 'picinventaris')
    @include('dashboard.partials.pic-inventaris')
@endif

{{-- USER BIASA: Tampilan minimal --}}
@if($role === 'user' || !$role)
    @include('dashboard.partials.user-biasa')
@endif

</div>
@endsection
