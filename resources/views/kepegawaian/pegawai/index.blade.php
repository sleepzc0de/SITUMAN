@extends('layouts.app')
@section('title', 'Kelola Data Pegawai')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Kelola Data Pegawai', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1">Kelola Data Pegawai</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manajemen lengkap data seluruh pegawai — tambah, ubah, hapus, export & import</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
            <a href="{{ route('kepegawaian.pegawai.template') }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-xl border-2 border-gray-200 dark:border-navy-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Template
            </a>
            <a href="{{ route('kepegawaian.pegawai.import-form') }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-xl border-2 border-navy-300 dark:border-navy-500 text-navy-600 dark:text-navy-300 hover:bg-navy-50 dark:hover:bg-navy-700 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import
            </a>
            <a href="{{ route('kepegawaian.pegawai.export', request()->query()) }}"
                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-xl border-2 border-gold-400 text-gold-700 dark:text-gold-400 hover:bg-gold-50 dark:hover:bg-navy-700 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn-primary text-sm px-4 py-2">
                <svg class="w-4 h-4 mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pegawai
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
        $statCards = [
            ['label' => 'Total Pegawai',  'value' => number_format($analytics['total']),        'sub' => $analytics['aktif'].' aktif',            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'bg' => 'bg-navy-100 dark:bg-navy-700', 'ico' => 'text-navy-600 dark:text-navy-300', 'val' => 'text-navy-700 dark:text-white'],
            ['label' => 'Pegawai Aktif',  'value' => number_format($analytics['aktif']),        'sub' => number_format($analytics['tidak_aktif']).' non-aktif', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-green-100 dark:bg-green-900/30', 'ico' => 'text-green-600 dark:text-green-400', 'val' => 'text-green-700 dark:text-green-400'],
            ['label' => 'Rata-rata Grade','value' => $analytics['avg_grading'],                 'sub' => 'Dari grade 1–16',                        'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',                 'bg' => 'bg-gold-100 dark:bg-gold-900/30',  'ico' => 'text-gold-600 dark:text-gold-400',  'val' => 'text-gold-700 dark:text-gold-400'],
            ['label' => 'Rata-rata Usia', 'value' => $analytics['avg_usia'].' th',              'sub' => 'Masa kerja '.$analytics['avg_masa_kerja'].' th', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'bg' => 'bg-purple-100 dark:bg-purple-900/30','ico' => 'text-purple-600 dark:text-purple-400','val' => 'text-purple-700 dark:text-purple-400'],
            ['label' => 'Pensiun ≤1 Th', 'value' => $analytics['akan_pensiun_1th'],            'sub' => 'Segera pensiun',                         'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',    'bg' => 'bg-red-100 dark:bg-red-900/30',    'ico' => 'text-red-600 dark:text-red-400',    'val' => 'text-red-700 dark:text-red-400'],
            ['label' => 'Pensiun ≤2 Th', 'value' => $analytics['akan_pensiun_2th'],            'sub' => 'Perlu diperhatikan',                    'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z','bg' => 'bg-orange-100 dark:bg-orange-900/30','ico' => 'text-orange-600 dark:text-orange-400','val' => 'text-orange-700 dark:text-orange-400'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md transition-all duration-200">
            <div class="w-9 h-9 {{ $card['bg'] }} rounded-xl flex items-center justify-center mb-3">
                <svg class="w-4 h-4 {{ $card['ico'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <p class="text-xl font-bold {{ $card['val'] }}">{{ $card['value'] }}</p>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">{{ $card['label'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $card['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- === CHARTS ROW 1: Sebaran Bagian + Distribusi Grading === --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Sebaran per Bagian</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $analytics['per_bagian']->sum('total') }} pegawai</p>
                </div>
                <span class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </span>
            </div>
            <div class="h-60"><canvas id="chartBagian"></canvas></div>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Distribusi Grading</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rata-rata grade {{ $analytics['avg_grading'] }}</p>
                </div>
                <span class="w-8 h-8 bg-gold-100 dark:bg-navy-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </span>
            </div>
            <div class="h-60"><canvas id="chartGrading"></canvas></div>
        </div>
    </div>

    {{-- === CHARTS ROW 2: Demografi (JK, Pendidikan, Usia, Masa Kerja) === --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Jenis Kelamin</h3>
            <div class="h-44"><canvas id="chartJK"></canvas></div>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Pendidikan</h3>
            <div class="h-44"><canvas id="chartPendidikan"></canvas></div>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Rentang Usia</h3>
            <div class="h-44"><canvas id="chartUsia"></canvas></div>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wide">Masa Kerja</h3>
            <div class="h-44"><canvas id="chartMasaKerja"></canvas></div>
        </div>
    </div>

    {{-- === CHARTS ROW 3: Eselon + Jenis Jabatan + Pensiun Timeline === --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Sebaran per Eselon</h3>
            <div class="h-52"><canvas id="chartEselon"></canvas></div>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Jenis Pegawai</h3>
            <div class="h-52"><canvas id="chartJenisJabatan"></canvas></div>
        </div>

        {{-- Pensiun Alert Panel --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Alert Pensiun</h3>
                <span class="text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2.5 py-1 rounded-full">
                    {{ $analytics['akan_pensiun_2th'] }} orang
                </span>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-navy-700 max-h-52 overflow-y-auto">
                @php
                $pensiunList = \App\Models\Pegawai::where('status','AKTIF')
                    ->whereNotNull('tanggal_pensiun')
                    ->whereBetween('tanggal_pensiun',[now(), now()->addYears(2)])
                    ->orderBy('tanggal_pensiun')
                    ->take(8)
                    ->get();
                @endphp
                @forelse($pensiunList as $pp)
                @php
                $bulanLagi = now()->diffInMonths(\Carbon\Carbon::parse($pp->tanggal_pensiun), false);
                $isSegera = $bulanLagi <= 12;
                @endphp
                <div class="px-4 py-2.5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-navy-700/50">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate max-w-32">{{ $pp->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $pp->bagian ?? '-' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <p class="text-xs font-bold {{ $isSegera ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400' }}">
                            {{ $bulanLagi }} bln lagi
                        </p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($pp->tanggal_pensiun)->format('m/Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center">
                    <p class="text-xs text-gray-400">Tidak ada yang akan pensiun dalam 2 tahun</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- === FILTER + TABLE === --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">

        {{-- Filter Bar --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50/50 dark:bg-navy-750">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-52">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Cari</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama, NIP, jabatan..."
                            class="w-full pl-9 pr-4 py-2 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl focus:border-navy-400 focus:ring-4 focus:ring-navy-100 dark:focus:ring-navy-700/50 bg-white dark:bg-navy-700 text-gray-900 dark:text-white transition-all">
                    </div>
                </div>
                <div class="min-w-36">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Bagian</label>
                    <select name="bagian" class="w-full py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 focus:ring-4 focus:ring-navy-100 dark:focus:ring-navy-700/50 transition-all">
                        <option value="">Semua</option>
                        @foreach($bagianList as $b)
                        <option value="{{ $b }}" {{ request('bagian') == $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-32">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Status</label>
                    <select name="status" class="w-full py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 focus:ring-4 focus:ring-navy-100 dark:focus:ring-navy-700/50 transition-all">
                        <option value="">Semua</option>
                        @foreach(['AKTIF','CLTN','PENSIUN','NON AKTIF'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-28">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Gender</label>
                    <select name="jenis_kelamin" class="w-full py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 focus:ring-4 focus:ring-navy-100 dark:focus:ring-navy-700/50 transition-all">
                        <option value="">Semua</option>
                        <option value="Laki-laki" {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="min-w-28">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Pendidikan</label>
                    <select name="pendidikan" class="w-full py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 focus:ring-4 focus:ring-navy-100 dark:focus:ring-navy-700/50 transition-all">
                        <option value="">Semua</option>
                        @foreach($pendidikanList as $pd)
                        <option value="{{ $pd }}" {{ request('pendidikan') == $pd ? 'selected' : '' }}>{{ $pd }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-28">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Eselon</label>
                    <select name="eselon" class="w-full py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 focus:ring-4 focus:ring-navy-100 dark:focus:ring-navy-700/50 transition-all">
                        <option value="">Semua</option>
                        @foreach($eselonList as $e)
                        <option value="{{ $e }}" {{ request('eselon') == $e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-20">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Per halaman</label>
                    <select name="per_page" onchange="this.form.submit()" class="w-full py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 transition-all">
                        @foreach([10,20,50,100] as $pp)
                        <option value="{{ $pp }}" {{ request('per_page', 20) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-navy-700 to-navy-800 text-white text-sm font-medium rounded-xl hover:from-navy-800 hover:to-navy-900 focus:ring-4 focus:ring-navy-200 shadow-sm transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter
                    </button>
                    @if(request()->anyFilled(['search','bagian','status','jenis_kelamin','pendidikan','eselon']))
                    <a href="{{ route('kepegawaian.pegawai.index') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border-2 border-gray-200 dark:border-navy-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700 transition-all">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Result Info --}}
        <div class="px-6 py-3 bg-white dark:bg-navy-800 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->firstItem() ?? 0 }}</span>–<span class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->total() }}</span> pegawai
            </p>
            @if(request()->anyFilled(['search','bagian','status','jenis_kelamin','pendidikan','eselon']))
            <span class="badge badge-info text-xs">Filter aktif</span>
            @endif
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-750 border-b border-gray-200 dark:border-navy-700">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10">No</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pegawai</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jabatan & Bagian</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pendidikan</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pensiun</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @forelse($pegawai as $index => $p)
                    <tr class="hover:bg-navy-50/30 dark:hover:bg-navy-700/30 transition-colors">
                        <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500">{{ $pegawai->firstItem() + $index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="text-xs font-bold text-white uppercase">{{ substr($p->nama, 0, 2) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-44">{{ $p->nama_gelar ?? $p->nama }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $p->nip }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-900 dark:text-white font-medium truncate max-w-44">{{ $p->jabatan ?? '—' }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                @if($p->bagian)<span class="text-xs text-gray-500 dark:text-gray-400">{{ $p->bagian }}</span>@endif
                                @if($p->eselon)<span class="text-xs text-purple-600 dark:text-purple-400">· {{ $p->eselon }}</span>@endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($p->grading)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-navy-100 dark:bg-navy-700 text-navy-700 dark:text-navy-200">G{{ $p->grading }}</span>
                            @else
                            <span class="text-gray-300 dark:text-gray-600 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($p->pendidikan)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">{{ $p->pendidikan }}</span>
                            @else
                            <span class="text-gray-300 dark:text-gray-600 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php
                            $sc = ['AKTIF' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', 'CLTN' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', 'PENSIUN' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400', 'NON AKTIF' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'];
                            $cls = $sc[$p->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $cls }}">{{ $p->status ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($p->tanggal_pensiun)
                            @php
                                $pensiun = \Carbon\Carbon::parse($p->tanggal_pensiun);
                                $bulanLagi = now()->diffInMonths($pensiun, false);
                                $isSegera = $bulanLagi <= 12 && $bulanLagi > 0;
                                $isDekat  = $bulanLagi <= 24 && $bulanLagi > 12;
                            @endphp
                            <p class="text-xs font-semibold {{ $isSegera ? 'text-red-600 dark:text-red-400' : ($isDekat ? 'text-orange-600 dark:text-orange-400' : 'text-gray-600 dark:text-gray-400') }}">
                                {{ $pensiun->format('Y') }}
                            </p>
                            @if($bulanLagi > 0 && $bulanLagi <= 24)
                            <p class="text-xs {{ $isSegera ? 'text-red-400' : 'text-orange-400' }}">{{ $bulanLagi }} bln</p>
                            @elseif($bulanLagi <= 0)
                            <p class="text-xs text-gray-400">Sudah pensiun</p>
                            @else
                            <p class="text-xs text-gray-400">{{ $pensiun->format('d/m/Y') }}</p>
                            @endif
                            @else
                            <span class="text-gray-300 dark:text-gray-600 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('kepegawaian.pegawai.show', $p) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-navy-500 dark:text-navy-400 hover:bg-navy-100 dark:hover:bg-navy-700 transition-colors" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('kepegawaian.pegawai.edit', $p) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gold-600 dark:text-gold-400 hover:bg-gold-50 dark:hover:bg-navy-700 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('kepegawaian.pegawai.destroy', $p) }}"
                                    x-data
                                    @submit.prevent="if(confirm('Hapus data {{ addslashes($p->nama) }}?\nTindakan ini tidak bisa dibatalkan.')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-navy-700 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-semibold">Tidak ada data pegawai</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Coba ubah filter atau tambahkan data baru</p>
                                <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn-primary text-sm px-4 py-2 mt-4">+ Tambah Pegawai</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pegawai->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-navy-700 bg-gray-50/50 dark:bg-navy-750">
            {{ $pegawai->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark    = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
    const tickColor = isDark ? '#6b7280' : '#9ca3af';
    const bgDark    = isDark ? '#1a2d47' : '#fff';

    const tooltip = {
        backgroundColor : '#1a2332',
        titleColor      : '#fbbf24',
        bodyColor       : '#e5e7eb',
        padding         : 12,
        cornerRadius    : 10,
        titleFont       : { size: 12, weight: 'bold' },
        bodyFont        : { size: 11 },
        borderColor     : 'rgba(251,191,36,0.2)',
        borderWidth     : 1,
    };

    const navy  = ['#1e3a5f','#2d5986','#3d6e9e','#4a7ba7','#6fa3d0','#8ec5ea','#afd8f5'];
    const gold  = ['#d97706','#f59e0b','#fbbf24','#fcd34d','#fde68a'];
    const mixed = ['#1e3a5f','#f59e0b','#2d5986','#fbbf24','#4a7ba7','#fcd34d','#6fa3d0','#d97706'];

    function scales(axis = 'y') {
        const opp = axis === 'y' ? 'x' : 'y';
        return {
            [axis]: { beginAtZero: true, ticks: { precision: 0, color: tickColor, font: { size: 10 } }, grid: { color: gridColor } },
            [opp]: { ticks: { color: tickColor, font: { size: 10 } }, grid: { display: false } }
        };
    }

    // ── Sebaran Bagian ──────────────────────────────
    new Chart(document.getElementById('chartBagian'), {
        type: 'bar',
        data: {
            labels: @json($analytics['per_bagian']->pluck('bagian')),
            datasets: [{ data: @json($analytics['per_bagian']->pluck('total')), backgroundColor: navy, borderRadius: 6, borderSkipped: false, barPercentage: 0.6 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.y + ' pegawai' } } }, scales: scales('y') }
    });

    // ── Distribusi Grading ──────────────────────────
    new Chart(document.getElementById('chartGrading'), {
        type: 'line',
        data: {
            labels: @json($analytics['per_grading']->pluck('grading')->map(fn($g) => 'G'.$g)),
            datasets: [{ data: @json($analytics['per_grading']->pluck('total')), borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)', borderWidth: 2.5, fill: true, tension: 0.4, pointBackgroundColor: '#1e3a5f', pointBorderColor: '#f59e0b', pointRadius: 4, pointHoverRadius: 6, pointBorderWidth: 2 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.y + ' pegawai' } } }, scales: scales('y') }
    });

    // ── Jenis Kelamin ───────────────────────────────
    new Chart(document.getElementById('chartJK'), {
        type: 'doughnut',
        data: {
            labels: @json($analytics['per_jenis_kelamin']->pluck('jenis_kelamin')),
            datasets: [{ data: @json($analytics['per_jenis_kelamin']->pluck('total')), backgroundColor: ['#1e3a5f','#f59e0b'], borderColor: bgDark, borderWidth: 3, hoverOffset: 6 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { padding: 8, usePointStyle: true, pointStyle: 'circle', font: { size: 10 }, color: tickColor } }, tooltip: { ...tooltip, callbacks: { label: c => { const t = c.dataset.data.reduce((a,b)=>a+b,0); return ` ${c.label}: ${c.parsed} (${((c.parsed/t)*100).toFixed(1)}%)`; } } } } }
    });

    // ── Pendidikan ──────────────────────────────────
    new Chart(document.getElementById('chartPendidikan'), {
        type: 'pie',
        data: {
            labels: @json($analytics['per_pendidikan']->pluck('pendidikan')),
            datasets: [{ data: @json($analytics['per_pendidikan']->pluck('total')), backgroundColor: mixed, borderColor: bgDark, borderWidth: 3, hoverOffset: 6 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { padding: 6, usePointStyle: true, pointStyle: 'circle', font: { size: 9 }, color: tickColor } }, tooltip: { ...tooltip } } }
    });

    // ── Range Usia ──────────────────────────────────
    new Chart(document.getElementById('chartUsia'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($analytics['range_usia'])),
            datasets: [{ data: @json(array_values($analytics['range_usia'])), backgroundColor: navy.slice(0,5), borderRadius: 5, borderSkipped: false, barPercentage: 0.7 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.y + ' org' } } }, scales: scales('y') }
    });

    // ── Masa Kerja ──────────────────────────────────
    new Chart(document.getElementById('chartMasaKerja'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($analytics['range_masa_kerja'])),
            datasets: [{ data: @json(array_values($analytics['range_masa_kerja'])), backgroundColor: gold, borderRadius: 5, borderSkipped: false, barPercentage: 0.7 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.y + ' org' } } }, scales: scales('y') }
    });

    // ── Eselon ──────────────────────────────────────
    new Chart(document.getElementById('chartEselon'), {
        type: 'bar',
        data: {
            labels: @json($analytics['per_eselon']->pluck('eselon')),
            datasets: [{ data: @json($analytics['per_eselon']->pluck('total')), backgroundColor: gold, borderRadius: 5, borderSkipped: false, barPercentage: 0.6 }]
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.x + ' pegawai' } } }, scales: scales('x') }
    });

    // ── Jenis Jabatan ───────────────────────────────
    new Chart(document.getElementById('chartJenisJabatan'), {
        type: 'doughnut',
        data: {
            labels: @json($analytics['per_jenis_pegawai']->pluck('jenis_pegawai')),
            datasets: [{ data: @json($analytics['per_jenis_pegawai']->pluck('total')), backgroundColor: navy.slice(0,4), borderColor: bgDark, borderWidth: 3, hoverOffset: 6 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '55%', plugins: { legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, pointStyle: 'circle', font: { size: 10 }, color: tickColor } }, tooltip: { ...tooltip } } }
    });
});
</script>
@endpush
