@extends('layouts.app')
@section('title', 'Sebaran Pegawai')

@section('content')
<div
    x-data="sebaranPegawai()"
    x-init="init()"
    class="space-y-6 animate-fade-in"
>

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <nav class="breadcrumb mb-2">
                <span class="text-gray-400 dark:text-gray-500">Kepegawaian</span>
                <svg class="breadcrumb-sep w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="breadcrumb-current">Sebaran Pegawai</span>
            </nav>
            <h1 class="page-title">Sebaran Pegawai</h1>
            <p class="page-subtitle">Distribusi pegawai per bagian, eselon, dan unit kerja</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('kepegawaian.pegawai.index') }}" class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Kelola Data
            </a>
            <button @click="exportData()" :disabled="loading" class="btn btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         STAT CARDS
    ══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Pegawai --}}
        <div class="bg-gradient-to-br from-navy-600 to-navy-800 rounded-2xl p-5 text-white shadow-lg col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-navy-200 uppercase tracking-wide">Total Pegawai</p>
                    <p class="text-4xl font-bold mt-1">
                        <span x-show="meta.total !== null" x-text="meta.total"></span>
                        <span x-show="meta.total === null"
                              class="inline-block w-16 h-9 bg-white/20 rounded-lg animate-pulse align-middle"></span>
                    </p>
                    <p class="text-xs text-navy-200 mt-1">
                        <span x-show="!hasFilter()">semua status</span>
                        <span x-show="hasFilter()" class="text-gold-300">hasil filter</span>
                    </p>
                </div>
                <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Per Bagian --}}
        <div class="card flex items-center gap-4">
            <div class="w-12 h-12 bg-navy-100 dark:bg-navy-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="stat-card-label">Bagian</p>
                <p class="stat-card-value">
                    <span x-show="!loading" x-text="stats.bagian_count ?? 0"></span>
                    <span x-show="loading" class="skeleton inline-block h-7 w-8 rounded align-middle"></span>
                </p>
                <p class="stat-card-sub text-gray-400">unit kerja</p>
            </div>
        </div>

        {{-- Per Eselon --}}
        <div class="card flex items-center gap-4">
            <div class="w-12 h-12 bg-gold-100 dark:bg-navy-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="stat-card-label">Eselon</p>
                <p class="stat-card-value">
                    <span x-show="!loading" x-text="stats.per_eselon_count ?? 0"></span>
                    <span x-show="loading" class="skeleton inline-block h-7 w-8 rounded align-middle"></span>
                </p>
                <p class="stat-card-sub text-gray-400">kategori</p>
            </div>
        </div>

        {{-- Rasio Gender --}}
        <div class="card">
            <p class="stat-card-label mb-2">Rasio Gender</p>
            <div class="flex items-end gap-4 min-h-[36px]">
                <template x-if="!loading">
                    <template x-for="jk in (stats.per_jenis_kelamin ?? [])" :key="jk.jenis_kelamin">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="jk.total"></p>
                            <p class="text-xs font-medium mt-0.5"
                               :class="jk.jenis_kelamin === 'Laki-laki' ? 'text-blue-500' : 'text-pink-500'"
                               x-text="jk.jenis_kelamin === 'Laki-laki' ? 'L' : 'P'"></p>
                        </div>
                    </template>
                </template>
                <template x-if="loading">
                    <div class="flex gap-4">
                        <div class="skeleton h-8 w-10 rounded"></div>
                        <div class="skeleton h-8 w-10 rounded"></div>
                    </div>
                </template>
            </div>
            <div class="mt-3 h-1.5 w-full bg-pink-200 dark:bg-pink-900/30 rounded-full overflow-hidden">
                <div class="h-full bg-blue-400 rounded-full transition-all duration-700"
                     :style="`width: ${genderPct}%`"></div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         CHARTS
         canvas dibungkus x-show agar tidak dirender
         sebelum data siap
    ══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- Donut Gender --}}
        <div class="card lg:col-span-2">
            <div class="section-header mb-4">
                <div>
                    <p class="section-title">Distribusi Gender</p>
                    <p class="section-desc">Komposisi pegawai</p>
                </div>
            </div>
            <div class="relative flex items-center justify-center" style="height:180px">
                {{-- Skeleton chart --}}
                <div x-show="!chartsReady"
                     class="absolute inset-0 flex items-center justify-center">
                    <div class="w-32 h-32 rounded-full skeleton"></div>
                </div>
                {{-- Canvas hanya dirender setelah chartsReady --}}
                <canvas id="chartGender" x-show="chartsReady"></canvas>
                {{-- Loading overlay saat re-fetch --}}
                <div x-show="loading && chartsReady"
                     class="absolute inset-0 flex items-center justify-center
                            bg-white/70 dark:bg-navy-800/70 rounded-xl backdrop-blur-[2px]">
                    <div class="w-6 h-6 border-2 border-navy-300 border-t-navy-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>

        {{-- Bar Bagian --}}
        <div class="card lg:col-span-3">
            <div class="section-header mb-4">
                <div>
                    <p class="section-title">Distribusi per Bagian</p>
                    <p class="section-desc">Jumlah pegawai tiap unit</p>
                </div>
            </div>
            <div class="relative" style="height:180px">
                {{-- Skeleton chart --}}
                <div x-show="!chartsReady"
                     class="absolute inset-0 flex items-end gap-2 px-4 pb-2">
                    <template x-for="h in [60,90,45,75,55,80,40]" :key="h">
                        <div class="flex-1 skeleton rounded-t-md" :style="`height:${h}%`"></div>
                    </template>
                </div>
                <canvas id="chartBagian" x-show="chartsReady"></canvas>
                <div x-show="loading && chartsReady"
                     class="absolute inset-0 flex items-center justify-center
                            bg-white/70 dark:bg-navy-800/70 rounded-xl backdrop-blur-[2px]">
                    <div class="w-6 h-6 border-2 border-navy-300 border-t-navy-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         FILTER
    ══════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="flex flex-wrap gap-3 items-end">

            {{-- Search --}}
            <div class="flex-1 min-w-52">
                <label class="input-label text-xs uppercase tracking-wide">Cari Pegawai</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        x-model="filters.search"
                        @input="debouncedFetch()"
                        placeholder="Nama atau NIP..."
                        class="input-field pl-9 pr-9"
                        autocomplete="off"
                    >
                    <button x-show="filters.search"
                            @click="filters.search = ''; fetchData()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400
                                   hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Bagian --}}
            <div class="min-w-44">
                <label class="input-label text-xs uppercase tracking-wide">Bagian</label>
                <select x-model="filters.bagian" @change="filters.page = 1; fetchData()" class="input-field">
                    <option value="">Semua Bagian</option>
                    @foreach($bagianList as $bagian)
                    <option value="{{ $bagian }}">{{ $bagian }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="min-w-36">
                <label class="input-label text-xs uppercase tracking-wide">Status</label>
                <select x-model="filters.status" @change="filters.page = 1; fetchData()" class="input-field">
                    <option value="">Semua Status</option>
                    @foreach(['AKTIF','CLTN','PENSIUN','NON AKTIF'] as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Per Page --}}
            <div class="min-w-28">
                <label class="input-label text-xs uppercase tracking-wide">Tampil</label>
                <select x-model="filters.per_page" @change="filters.page = 1; fetchData()" class="input-field">
                    <option value="10">10 baris</option>
                    <option value="20">20 baris</option>
                    <option value="50">50 baris</option>
                    <option value="100">100 baris</option>
                </select>
            </div>

            {{-- Reset --}}
            <div class="flex items-end">
                <button x-show="hasFilter()" x-transition @click="resetFilter()" class="btn btn-ghost btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>

        {{-- Active filter chips --}}
        <div x-show="hasFilter()"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-navy-700">
            <span class="text-xs text-gray-400 dark:text-gray-500 self-center">Filter aktif:</span>
            <template x-if="filters.search">
                <span class="badge badge-info gap-1">
                    Cari: <strong x-text="filters.search"></strong>
                    <button @click="filters.search = ''; fetchData()"
                            class="ml-1 opacity-60 hover:opacity-100 hover:text-red-400 transition-all">✕</button>
                </span>
            </template>
            <template x-if="filters.bagian">
                <span class="badge badge-blue gap-1">
                    Bagian: <strong x-text="filters.bagian"></strong>
                    <button @click="filters.bagian = ''; fetchData()"
                            class="ml-1 opacity-60 hover:opacity-100 hover:text-red-400 transition-all">✕</button>
                </span>
            </template>
            <template x-if="filters.status">
                <span class="badge badge-success gap-1">
                    Status: <strong x-text="filters.status"></strong>
                    <button @click="filters.status = ''; fetchData()"
                            class="ml-1 opacity-60 hover:opacity-100 hover:text-red-400 transition-all">✕</button>
                </span>
            </template>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════════════ --}}
    <div class="card !p-0 overflow-hidden">

        {{-- Result info bar --}}
        <div class="px-5 py-3 border-b border-gray-100 dark:border-navy-700
                    flex items-center justify-between min-h-[48px]">
            <div x-show="!loading" class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan
                <span class="font-semibold text-gray-900 dark:text-white"
                      x-text="`${meta.from ?? 0}–${meta.to ?? 0}`"></span>
                dari
                <span class="font-semibold text-gray-900 dark:text-white"
                      x-text="meta.total ?? 0"></span>
                pegawai
            </div>
            <div x-show="loading"
                 class="flex items-center gap-2 text-sm text-navy-600 dark:text-navy-400">
                <div class="w-4 h-4 border-2 border-navy-300 border-t-navy-600 rounded-full animate-spin"></div>
                <span>Memuat data...</span>
            </div>
            <span x-show="hasFilter() && !loading" class="badge badge-info">Filter aktif</span>
        </div>

        {{-- Table --}}
        <div class="table-wrapper rounded-none border-0 relative">

            {{-- Re-fetch overlay --}}
            <div x-show="loading && rows.length > 0"
                 x-transition:enter="transition-opacity duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-white/50 dark:bg-navy-800/50
                        backdrop-blur-[2px] z-10 pointer-events-none">
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th class="w-10">No</th>
                        <th>Pegawai</th>
                        <th class="hidden md:table-cell">NIP</th>
                        <th class="hidden sm:table-cell">Bagian</th>
                        <th class="hidden lg:table-cell">Jabatan</th>
                        <th>Grading</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- Skeleton rows (first load) --}}
                    <template x-if="loading && rows.length === 0">
                        <template x-for="i in [1,2,3,4,5,6,7,8]" :key="i">
                            <tr>
                                <td><div class="skeleton h-4 w-5 rounded"></div></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="skeleton w-9 h-9 rounded-xl flex-shrink-0"></div>
                                        <div class="space-y-2">
                                            <div class="skeleton h-3.5 w-36 rounded"></div>
                                            <div class="skeleton h-3 w-24 rounded"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell"><div class="skeleton h-3.5 w-32 rounded"></div></td>
                                <td class="hidden sm:table-cell"><div class="skeleton h-3.5 w-28 rounded"></div></td>
                                <td class="hidden lg:table-cell">
                                    <div class="space-y-1.5">
                                        <div class="skeleton h-3.5 w-32 rounded"></div>
                                        <div class="skeleton h-4 w-14 rounded-full"></div>
                                    </div>
                                </td>
                                <td><div class="skeleton h-6 w-10 rounded-lg"></div></td>
                                <td><div class="skeleton h-6 w-16 rounded-full"></div></td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <div class="skeleton w-7 h-7 rounded-lg"></div>
                                        <div class="skeleton w-7 h-7 rounded-lg"></div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="rows.length > 0">
                        <template x-for="row in rows" :key="row.id">
                            <tr>
                                <td class="text-xs text-gray-400 dark:text-gray-500 tabular-nums" x-text="row.no"></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-gradient-to-br from-navy-500 to-navy-700
                                                    rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <span class="text-xs font-bold text-white uppercase" x-text="row.initials"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white
                                                       truncate max-w-44" x-text="row.nama"></p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate"
                                               x-text="row.email || row.nip"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell font-mono text-xs text-gray-600 dark:text-gray-400"
                                    x-text="row.nip"></td>
                                <td class="hidden sm:table-cell">
                                    <span class="text-sm text-gray-700 dark:text-gray-300"
                                          x-text="row.bagian || '—'"></span>
                                    <p class="text-xs text-gray-400 mt-0.5"
                                       x-show="row.subbagian" x-text="row.subbagian"></p>
                                </td>
                                <td class="hidden lg:table-cell">
                                    <p class="text-sm text-gray-900 dark:text-white" x-text="row.jabatan || '—'"></p>
                                    <span x-show="row.eselon" class="badge badge-purple mt-0.5"
                                          x-text="row.eselon"></span>
                                </td>
                                <td>
                                    <template x-if="row.grading">
                                        <span class="badge badge-info font-bold" x-text="`G${row.grading}`"></span>
                                    </template>
                                    <template x-if="!row.grading">
                                        <span class="text-gray-300 dark:text-gray-600 text-sm">—</span>
                                    </template>
                                </td>
                                <td>
                                    <span :class="statusBadge(row.status)" x-text="row.status || '—'"></span>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a :href="row.show_url" class="table-action-view" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @canaccess('kepegawaian')
                                        <a :href="row.edit_url" class="table-action-edit" title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @endcanaccess
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && rows.length === 0">
                        <tr>
                            <td colspan="8">
                                <div class="empty-state py-16">
                                    <div class="empty-state-icon">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <p class="empty-state-title">Tidak ada data pegawai</p>
                                    <p class="empty-state-desc">Coba ubah filter atau kata kunci pencarian</p>
                                    <button x-show="hasFilter()" @click="resetFilter()"
                                            class="btn btn-outline btn-sm mt-3">Reset Filter</button>
                                </div>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div x-show="meta.last_page > 1"
             class="px-5 py-4 border-t border-gray-100 dark:border-navy-700
                    flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Halaman
                <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="meta.current_page"></span>
                dari
                <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="meta.last_page"></span>
            </p>
            <div class="flex items-center gap-1 flex-wrap">
                <button @click="goToPage(1)" :disabled="meta.current_page <= 1 || loading"
                        class="w-8 h-8 rounded-lg text-xs font-medium transition-colors
                               disabled:opacity-40 disabled:cursor-not-allowed
                               text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-700">«</button>
                <button @click="goToPage(meta.current_page - 1)" :disabled="meta.current_page <= 1 || loading"
                        class="w-8 h-8 rounded-lg text-xs font-medium transition-colors
                               disabled:opacity-40 disabled:cursor-not-allowed
                               text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-700">‹</button>
                <template x-for="p in pageNumbers" :key="p">
                    <button @click="p !== '…' && goToPage(p)"
                            :disabled="loading"
                            :class="{
                                'bg-navy-600 text-white shadow-sm': p === meta.current_page,
                                'cursor-default text-gray-300 dark:text-gray-600 pointer-events-none': p === '…',
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-700': p !== meta.current_page && p !== '…'
                            }"
                            class="w-8 h-8 rounded-lg text-xs font-medium transition-colors disabled:opacity-40"
                            x-text="p">
                    </button>
                </template>
                <button @click="goToPage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page || loading"
                        class="w-8 h-8 rounded-lg text-xs font-medium transition-colors
                               disabled:opacity-40 disabled:cursor-not-allowed
                               text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-700">›</button>
                <button @click="goToPage(meta.last_page)" :disabled="meta.current_page >= meta.last_page || loading"
                        class="w-8 h-8 rounded-lg text-xs font-medium transition-colors
                               disabled:opacity-40 disabled:cursor-not-allowed
                               text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-700">»</button>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function sebaranPegawai() {
    return {
        rows:    [],
        meta: {
            total:        null,
            per_page:     20,
            current_page: 1,
            last_page:    1,
            from:         0,
            to:           0,
        },
        stats: {
            per_bagian:        [],
            bagian_count:      0,
            per_eselon_count:  0,
            per_jenis_kelamin: [],
        },
        loading:        true,
        chartsReady:    false,
        _chartGender:   null,
        _chartBagian:   null,
        _debounceTimer: null,
        _renderTimer:   null,

        filters: {
            search:   '',
            bagian:   '',
            status:   '',
            per_page: 20,
            page:     1,
        },

        // ── lifecycle ────────────────────────────────────────────────────────
        init() {
            this.fetchData();
        },

        // ── computed ─────────────────────────────────────────────────────────
        get genderPct() {
            const jk    = this.stats.per_jenis_kelamin ?? [];
            const total = jk.reduce((s, x) => s + Number(x.total), 0);
            const laki  = Number(jk.find(x => x.jenis_kelamin === 'Laki-laki')?.total ?? 0);
            return total ? Math.round(laki / total * 100) : 0;
        },

        get pageNumbers() {
            const cur  = this.meta.current_page;
            const last = this.meta.last_page;
            if (last <= 1) return [];
            if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);
            if (cur <= 4)        return [1, 2, 3, 4, 5, '…', last];
            if (cur >= last - 3) return [1, '…', last-4, last-3, last-2, last-1, last];
            return [1, '…', cur-1, cur, cur+1, '…', last];
        },

        // ── helpers ──────────────────────────────────────────────────────────
        hasFilter() {
            return !!(this.filters.search || this.filters.bagian || this.filters.status);
        },

        resetFilter() {
            this.filters.search   = '';
            this.filters.bagian   = '';
            this.filters.status   = '';
            this.filters.per_page = 20;
            this.filters.page     = 1;
            this.fetchData();
        },

        debouncedFetch() {
            clearTimeout(this._debounceTimer);
            this._debounceTimer = setTimeout(() => {
                this.filters.page = 1;
                this.fetchData();
            }, 450);
        },

        goToPage(p) {
            if (p < 1 || p > this.meta.last_page || p === this.meta.current_page) return;
            this.filters.page = p;
            this.fetchData();
            this.$el.querySelector('table')
                ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        statusBadge(status) {
            const map = {
                'AKTIF':     'badge badge-success',
                'CLTN':      'badge badge-blue',
                'PENSIUN':   'badge badge-gray',
                'NON AKTIF': 'badge badge-danger',
            };
            return map[status] ?? 'badge badge-gray';
        },

        // ── fetch ────────────────────────────────────────────────────────────
        async fetchData() {
            this.loading = true;

            const params = new URLSearchParams();
            if (this.filters.search) params.set('search',   this.filters.search);
            if (this.filters.bagian) params.set('bagian',   this.filters.bagian);
            if (this.filters.status) params.set('status',   this.filters.status);
            params.set('per_page', this.filters.per_page);
            params.set('page',     this.filters.page);

            try {
                const res  = await window.axios.get(
                    '{{ route("kepegawaian.sebaran.data") }}?' + params.toString()
                );
                const json = res.data;

                this.rows  = json.data  ?? [];
                this.meta  = json.meta  ?? this.meta;
                this.stats = json.stats ?? this.stats;

                // Tunggu DOM Alpine selesai render, lalu jadwalkan render chart
                await this.$nextTick();
                this._scheduleCharts();

            } catch (err) {
                console.error('[SebaranPegawai] fetch error:', err);
                window.showToast?.('Gagal memuat data pegawai', 'error');
            } finally {
                this.loading = false;
            }
        },

        exportData() {
            const params = new URLSearchParams();
            if (this.filters.search) params.set('search', this.filters.search);
            if (this.filters.bagian) params.set('bagian', this.filters.bagian);
            if (this.filters.status) params.set('status', this.filters.status);
            const qs = params.toString();
            window.location.href =
                '{{ route("kepegawaian.pegawai.export") }}' + (qs ? '?' + qs : '');
        },

        // ── charts ───────────────────────────────────────────────────────────

        /**
         * Jadwalkan render chart di luar call stack saat ini.
         * Menggunakan double-rAF untuk memastikan browser sudah
         * selesai layout sebelum Chart.js membaca dimensi canvas.
         */
        _scheduleCharts() {
            clearTimeout(this._renderTimer);
            this._renderTimer = setTimeout(() => {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        this._renderCharts();
                    });
                });
            }, 0);
        },

        /**
         * Selalu destroy + rebuild chart dari nol.
         * Ini menghindari semua bug internal state Chart.js
         * yang terjadi saat .update() dipanggil.
         */
        _renderCharts() {
            // 1. Destroy semua chart yang ada
            this._safeDestroy('chartGender', '_chartGender');
            this._safeDestroy('chartBagian', '_chartBagian');
            this.chartsReady = false;

            const isDark    = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#829ab1' : '#627d98';
            const gridColor = isDark ? 'rgba(148,163,184,0.08)' : 'rgba(148,163,184,0.15)';

            // 2. Siapkan data
            const jk        = this.stats.per_jenis_kelamin ?? [];
            const laki      = Number(jk.find(x => x.jenis_kelamin === 'Laki-laki')?.total   ?? 0);
            const perempuan = Number(jk.find(x => x.jenis_kelamin === 'Perempuan')?.total ?? 0);
            const bagian    = this.stats.per_bagian ?? [];

            // 3. Buat Gender chart
            const canvasG = document.getElementById('chartGender');
            if (canvasG && canvasG.getContext) {
                try {
                    this._chartGender = new Chart(canvasG, {
                        type: 'doughnut',
                        data: {
                            labels:   ['Laki-laki', 'Perempuan'],
                            datasets: [{
                                data:            [laki, perempuan],
                                backgroundColor: ['#60a5fa', '#f472b6'],
                                borderWidth:     0,
                                hoverOffset:     8,
                            }]
                        },
                        options: {
                            responsive:          true,
                            maintainAspectRatio: true,
                            animation:           false,
                            plugins: {
                                legend: {
                                    display:  true,
                                    position: 'right',
                                    labels: {
                                        color:    textColor,
                                        font:     { size: 12 },
                                        boxWidth: 12,
                                        padding:  12,
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => ` ${ctx.label}: ${ctx.raw} orang`
                                    }
                                }
                            }
                        }
                    });
                } catch (e) {
                    console.warn('[Chart] Gender init error:', e);
                }
            }

            // 4. Buat Bagian chart
            const canvasB = document.getElementById('chartBagian');
            if (canvasB && canvasB.getContext) {
                try {
                    this._chartBagian = new Chart(canvasB, {
                        type: 'bar',
                        data: {
                            labels:   bagian.map(d => d.bagian ?? 'Tidak diketahui'),
                            datasets: [{
                                label:               'Jumlah Pegawai',
                                data:                bagian.map(d => Number(d.total)),
                                backgroundColor:     isDark ? '#486581bb' : '#486581cc',
                                borderRadius:        6,
                                hoverBackgroundColor:'#fbbf24',
                            }]
                        },
                        options: {
                            responsive:          true,
                            maintainAspectRatio: false,
                            animation:           false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color:    textColor,
                                        font:     { size: 11 },
                                    },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: {
                                        color:       textColor,
                                        maxRotation: 35,
                                        font:        { size: 11 },
                                        callback(val) {
                                            const lbl = this.getLabelForValue(val);
                                            return lbl?.length > 18
                                                ? lbl.substring(0, 16) + '…'
                                                : lbl;
                                        }
                                    },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                } catch (e) {
                    console.warn('[Chart] Bagian init error:', e);
                }
            }

            this.chartsReady = !!(this._chartGender || this._chartBagian);
        },

        /**
         * Destroy satu chart dengan aman.
         * @param {string} canvasId  - id elemen canvas
         * @param {string} stateKey  - key di this (misal '_chartGender')
         */
        _safeDestroy(canvasId, stateKey) {
            // Destroy via instance tersimpan
            if (this[stateKey]) {
                try {
                    this[stateKey].stop();
                    this[stateKey].destroy();
                } catch (_) {}
                this[stateKey] = null;
            }
            // Destroy via Chart.js global registry (fallback Vite HMR)
            try {
                const canvas = document.getElementById(canvasId);
                if (canvas) {
                    const existing = Chart.getChart(canvas);
                    if (existing) {
                        existing.stop();
                        existing.destroy();
                    }
                }
            } catch (_) {}
        },
    };
}
</script>
@endpush
