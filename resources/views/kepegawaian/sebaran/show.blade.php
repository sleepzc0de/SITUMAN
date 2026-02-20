@extends('layouts.app')
@section('title', 'Detail Sebaran — '.$pegawai->nama)

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Sebaran Pegawai', 'url' => route('kepegawaian.sebaran'), 'active' => false],
                ['title' => 'Detail', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1">Detail Pegawai</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Informasi lengkap pegawai</p>
        </div>
        <a href="{{ route('kepegawaian.sebaran') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border-2 border-gray-200 dark:border-navy-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700 transition-all self-start">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Profile Hero --}}
    <div class="bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 rounded-2xl p-6 lg:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-gold-500/10 rounded-full translate-y-24 -translate-x-20 pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row gap-6">
            <div class="flex-shrink-0">
                <div class="w-20 h-20 bg-gradient-to-br from-navy-400 to-gold-500 rounded-2xl flex items-center justify-center shadow-xl">
                    <span class="text-3xl font-bold text-white uppercase">{{ substr($pegawai->nama, 0, 2) }}</span>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl lg:text-2xl font-bold">{{ $pegawai->nama_gelar ?? $pegawai->nama }}</h2>
                <p class="text-navy-300 mt-1 font-mono text-sm">{{ $pegawai->nip }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($pegawai->grading)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gold-500/20 text-gold-300 border border-gold-500/30">Grade {{ $pegawai->grading }}</span>
                    @endif
                    @if($pegawai->status)
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pegawai->status == 'AKTIF' ? 'bg-green-500/20 text-green-300 border border-green-500/30' : 'bg-gray-500/20 text-gray-300 border border-gray-500/30' }}">{{ $pegawai->status }}</span>
                    @endif
                    @if($pegawai->jenis_kelamin)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-white/10 text-white border border-white/20">{{ $pegawai->jenis_kelamin }}</span>
                    @endif
                    @if($pegawai->eselon)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30">{{ $pegawai->eselon }}</span>
                    @endif
                </div>
                @if($pegawai->jabatan || $pegawai->bagian)
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1">
                    @if($pegawai->jabatan)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $pegawai->jabatan }}
                    </div>
                    @endif
                    @if($pegawai->bagian)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $pegawai->bagian }}
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ activeTab: 'personal' }" class="space-y-0">
        <div class="bg-white dark:bg-navy-800 rounded-t-2xl shadow-sm border border-gray-100 dark:border-navy-700 border-b-0">
            <div class="px-6 pt-4">
                <nav class="-mb-px flex gap-1 overflow-x-auto">
                    @php
                    $tabs = [
                        ['id' => 'personal',    'label' => 'Data Personal', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['id' => 'jabatan',     'label' => 'Jabatan',       'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['id' => 'kepegawaian', 'label' => 'Kepegawaian',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ];
                    @endphp
                    @foreach($tabs as $tab)
                    <button @click="activeTab = '{{ $tab['id'] }}'"
                        :class="activeTab === '{{ $tab['id'] }}'
                            ? 'border-navy-600 text-navy-700 dark:text-gold-400 bg-navy-50 dark:bg-navy-700/50'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="flex items-center gap-2 px-4 py-3 border-b-2 text-sm font-medium transition-all whitespace-nowrap rounded-t-xl">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                        </svg>
                        {{ $tab['label'] }}
                    </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-b-2xl shadow-sm border border-gray-100 dark:border-navy-700 border-t-0">

            {{-- Personal Tab --}}
            <div x-show="activeTab === 'personal'" class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                    $rows = [
                        ['label' => 'Nama Lengkap',  'value' => $pegawai->nama],
                        ['label' => 'Nama Gelar',    'value' => $pegawai->nama_gelar],
                        ['label' => 'NIP',           'value' => $pegawai->nip, 'mono' => true],
                        ['label' => 'Jenis Kelamin', 'value' => $pegawai->jenis_kelamin],
                        ['label' => 'Tanggal Lahir', 'value' => $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->translatedFormat('d F Y') : null],
                        ['label' => 'Usia',          'value' => $pegawai->usia ? $pegawai->usia.' tahun' : null],
                        ['label' => 'Email Kemenkeu','value' => $pegawai->email_kemenkeu, 'link' => 'mailto:'],
                        ['label' => 'Email Pribadi', 'value' => $pegawai->email_pribadi,  'link' => 'mailto:'],
                        ['label' => 'No. HP',        'value' => $pegawai->no_hp,          'link' => 'tel:'],
                        ['label' => 'Pendidikan',    'value' => $pegawai->pendidikan],
                        ['label' => 'Jurusan S1',    'value' => $pegawai->jurusan_s1],
                        ['label' => 'Jurusan S2',    'value' => $pegawai->jurusan_s2],
                    ];
                    @endphp
                    @foreach($rows as $row)
                    @if(!empty($row['value']))
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white {{ isset($row['mono']) ? 'font-mono' : '' }}">
                            @if(isset($row['link']))
                            <a href="{{ $row['link'] }}{{ $row['value'] }}" class="text-navy-600 dark:text-navy-400 hover:underline">{{ $row['value'] }}</a>
                            @else
                            {{ $row['value'] }}
                            @endif
                        </dd>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Jabatan Tab --}}
            <div x-show="activeTab === 'jabatan'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                    $rows = [
                        ['label' => 'Jabatan',        'value' => $pegawai->jabatan],
                        ['label' => 'Jenis Jabatan',  'value' => $pegawai->jenis_jabatan],
                        ['label' => 'Nama Jabatan',   'value' => $pegawai->nama_jabatan],
                        ['label' => 'Eselon',         'value' => $pegawai->eselon],
                        ['label' => 'Grading',        'value' => $pegawai->grading ? 'Grade '.$pegawai->grading : null],
                        ['label' => 'Jenis Pegawai',  'value' => $pegawai->jenis_pegawai],
                        ['label' => 'Bagian',         'value' => $pegawai->bagian],
                        ['label' => 'Subbagian',      'value' => $pegawai->subbagian],
                        ['label' => 'Lokasi',         'value' => $pegawai->lokasi],
                        ['label' => 'Pangkat',        'value' => $pegawai->pangkat],
                        ['label' => 'Status',         'value' => $pegawai->status],
                    ];
                    @endphp
                    @foreach($rows as $row)
                    @if(!empty($row['value']))
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $row['value'] }}</dd>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Kepegawaian Tab --}}
            <div x-show="activeTab === 'kepegawaian'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">TMT CPNS</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $pegawai->tmt_cpns ? $pegawai->tmt_cpns->translatedFormat('d F Y') : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Masa Kerja</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ ($pegawai->masa_kerja_tahun ?? 0) }} tahun {{ ($pegawai->masa_kerja_bulan ?? 0) }} bulan
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Tanggal Pensiun</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $pegawai->tanggal_pensiun ? $pegawai->tanggal_pensiun->translatedFormat('d F Y') : '—' }}
                        </dd>
                    </div>
                    @if($pegawai->proyeksi_kp_1)
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Proyeksi KP 1</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $pegawai->proyeksi_kp_1 }}</dd>
                    </div>
                    @endif
                    @if($pegawai->proyeksi_kp_2)
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Proyeksi KP 2</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $pegawai->proyeksi_kp_2 }}</dd>
                    </div>
                    @endif
                    @if($pegawai->keterangan_kp)
                    <div class="sm:col-span-2 lg:col-span-3">
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Keterangan KP</dt>
                        <dd class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-navy-700 rounded-xl p-4 leading-relaxed">{{ $pegawai->keterangan_kp }}</dd>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Related Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('kepegawaian.grading.show', $pegawai) }}"
            class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:border-gold-200 dark:hover:border-navy-500 hover:shadow-md transition-all group flex items-center space-x-3">
            <div class="w-10 h-10 bg-gold-100 dark:bg-navy-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Kenaikan Grading</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Lihat rekomendasi grading</p>
            </div>
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="{{ route('kepegawaian.mutasi.show', $pegawai) }}"
            class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:border-green-200 dark:hover:border-navy-500 hover:shadow-md transition-all group flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-navy-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Proyeksi Mutasi</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Analisis mutasi pegawai</p>
            </div>
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
@endsection
