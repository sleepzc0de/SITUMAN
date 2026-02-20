@extends('layouts.app')
@section('title', 'Detail Pegawai — '.$pegawai->nama)

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Kelola Data Pegawai', 'url' => route('kepegawaian.pegawai.index'), 'active' => false],
                ['title' => 'Detail Pegawai', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1">Detail Pegawai</h1>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}" class="btn-secondary text-sm px-4 py-2">
                <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Data
            </a>
            <a href="{{ route('kepegawaian.pegawai.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border-2 border-gray-200 dark:border-navy-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Profile Hero --}}
    <div class="bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 rounded-2xl p-6 lg:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-gold-500/10 rounded-full translate-y-24 -translate-x-20 pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row gap-6">
            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <div class="w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-navy-400 to-gold-500 rounded-2xl flex items-center justify-center shadow-xl">
                    <span class="text-2xl lg:text-3xl font-bold text-white uppercase">{{ substr($pegawai->nama, 0, 2) }}</span>
                </div>
            </div>

            {{-- Info Utama --}}
            <div class="flex-1 min-w-0">
                <h2 class="text-xl lg:text-2xl font-bold leading-tight">{{ $pegawai->nama_gelar ?? $pegawai->nama }}</h2>
                <p class="text-navy-300 mt-1 font-mono text-sm tracking-wide">{{ $pegawai->nip }}</p>

                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($pegawai->grading)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-gold-500/20 text-gold-300 border border-gold-500/30">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Grade {{ $pegawai->grading }}
                    </span>
                    @endif
                    @if($pegawai->status)
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $pegawai->status == 'AKTIF' ? 'bg-green-500/20 text-green-300 border border-green-500/30' : 'bg-gray-500/20 text-gray-300 border border-gray-500/30' }}">
                        {{ $pegawai->status }}
                    </span>
                    @endif
                    @if($pegawai->jenis_kelamin)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-white/10 text-white border border-white/20">
                        {{ $pegawai->jenis_kelamin }}
                    </span>
                    @endif
                    @if($pegawai->eselon)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        {{ $pegawai->eselon }}
                    </span>
                    @endif
                    @if($pegawai->jenis_pegawai)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        {{ $pegawai->jenis_pegawai }}
                    </span>
                    @endif
                </div>

                {{-- Jabatan & Bagian --}}
                @if($pegawai->jabatan || $pegawai->bagian)
                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-1">
                    @if($pegawai->jabatan)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $pegawai->jabatan }}</span>
                    </div>
                    @endif
                    @if($pegawai->bagian)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>{{ $pegawai->bagian }}{{ $pegawai->subbagian ? ' · '.$pegawai->subbagian : '' }}</span>
                    </div>
                    @endif
                    @if($pegawai->lokasi)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $pegawai->lokasi }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Quick Stats --}}
            <div class="flex flex-row sm:flex-col gap-3 flex-shrink-0">
                <div class="bg-white/10 rounded-xl px-4 py-3 text-center min-w-20">
                    <p class="text-2xl font-bold text-gold-300">{{ $pegawai->usia ?? '—' }}</p>
                    <p class="text-xs text-navy-300 mt-0.5">Usia (th)</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 py-3 text-center min-w-20">
                    <p class="text-2xl font-bold text-gold-300">{{ $pegawai->masa_kerja_tahun ?? '—' }}</p>
                    <p class="text-xs text-navy-300 mt-0.5">Masa Kerja</p>
                </div>
                @if($pegawai->tanggal_pensiun)
                @php $bulanLagi = now()->diffInMonths(\Carbon\Carbon::parse($pegawai->tanggal_pensiun), false); @endphp
                <div class="bg-white/10 rounded-xl px-4 py-3 text-center min-w-20">
                    <p class="text-2xl font-bold {{ $bulanLagi <= 12 ? 'text-red-300' : 'text-gold-300' }}">{{ max(0, $bulanLagi) }}</p>
                    <p class="text-xs text-navy-300 mt-0.5">Bln Pensiun</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'personal' }" class="space-y-0">

        {{-- Tab Nav --}}
        <div class="bg-white dark:bg-navy-800 rounded-t-2xl shadow-sm border border-gray-100 dark:border-navy-700 border-b-0">
            <div class="px-6 pt-4">
                <nav class="-mb-px flex gap-1 overflow-x-auto">
                    @php
                    $tabs = [
                        ['id' => 'personal',    'label' => 'Data Personal',  'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['id' => 'jabatan',     'label' => 'Jabatan & Unit', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['id' => 'kepegawaian', 'label' => 'Kepegawaian',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['id' => 'pendidikan',  'label' => 'Pendidikan',    'icon' => 'M12 14l9-5-9-5-9 5 9 5z'],
                    ];
                    @endphp
                    @foreach($tabs as $t)
                    <button @click="tab = '{{ $t['id'] }}'"
                        :class="tab === '{{ $t['id'] }}'
                            ? 'border-navy-600 text-navy-700 dark:text-gold-400 bg-navy-50 dark:bg-navy-700/50'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="flex items-center gap-2 px-4 py-3 border-b-2 text-sm font-medium transition-all whitespace-nowrap rounded-t-xl">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/>
                        </svg>
                        {{ $t['label'] }}
                    </button>
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="bg-white dark:bg-navy-800 rounded-b-2xl shadow-sm border border-gray-100 dark:border-navy-700 border-t-0">

            {{-- Personal --}}
            <div x-show="tab === 'personal'" class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                    $personal = [
                        ['label' => 'Nama Lengkap',   'value' => $pegawai->nama,         'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['label' => 'Nama Gelar',     'value' => $pegawai->nama_gelar,   'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['label' => 'NIP',            'value' => $pegawai->nip,           'icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2', 'mono' => true],
                        ['label' => 'Jenis Kelamin',  'value' => $pegawai->jenis_kelamin,'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['label' => 'Tanggal Lahir',  'value' => $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->translatedFormat('d F Y') : null, 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['label' => 'Usia',           'value' => $pegawai->usia ? $pegawai->usia.' tahun' : null, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Email Kemenkeu', 'value' => $pegawai->email_kemenkeu, 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'link' => 'mailto:'],
                        ['label' => 'Email Pribadi',  'value' => $pegawai->email_pribadi,  'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'link' => 'mailto:'],
                        ['label' => 'No. HP',         'value' => $pegawai->no_hp,          'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'link' => 'tel:'],
                    ];
                    @endphp
                    @foreach($personal as $row)
                    @if(!empty($row['value']))
                    <div class="group">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $row['icon'] }}"/>
                            </svg>
                            <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">{{ $row['label'] }}</dt>
                        </div>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white pl-5 {{ isset($row['mono']) ? 'font-mono' : '' }}">
                            @if(isset($row['link']))
                            <a href="{{ $row['link'] }}{{ $row['value'] }}" class="text-navy-600 dark:text-navy-400 hover:underline break-all">{{ $row['value'] }}</a>
                            @else
                            {{ $row['value'] }}
                            @endif
                        </dd>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Jabatan --}}
            <div x-show="tab === 'jabatan'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                    $jabatan = [
                        ['label' => 'Jabatan',          'value' => $pegawai->jabatan],
                        ['label' => 'Jenis Jabatan',    'value' => $pegawai->jenis_jabatan],
                        ['label' => 'Nama Jabatan',     'value' => $pegawai->nama_jabatan],
                        ['label' => 'Eselon',           'value' => $pegawai->eselon],
                        ['label' => 'Jenis Pegawai',    'value' => $pegawai->jenis_pegawai],
                        ['label' => 'Status',           'value' => $pegawai->status],
                        ['label' => 'Grading',          'value' => $pegawai->grading ? 'Grade '.$pegawai->grading : null],
                        ['label' => 'Pangkat/Golongan', 'value' => $pegawai->pangkat],
                        ['label' => 'Bagian',           'value' => $pegawai->bagian],
                        ['label' => 'Subbagian',        'value' => $pegawai->subbagian],
                        ['label' => 'Lokasi',           'value' => $pegawai->lokasi],
                    ];
                    @endphp
                    @foreach($jabatan as $row)
                    @if(!empty($row['value']))
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $row['value'] }}</dd>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Kepegawaian --}}
            <div x-show="tab === 'kepegawaian'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                    $kepeg = [
                        ['label' => 'TMT CPNS',        'value' => $pegawai->tmt_cpns ? $pegawai->tmt_cpns->translatedFormat('d F Y') : null],
                        ['label' => 'Masa Kerja',      'value' => ($pegawai->masa_kerja_tahun ?? 0).' tahun '.($pegawai->masa_kerja_bulan ?? 0).' bulan'],
                        ['label' => 'Tanggal Pensiun', 'value' => $pegawai->tanggal_pensiun ? $pegawai->tanggal_pensiun->translatedFormat('d F Y') : null],
                        ['label' => 'Tahun Pensiun',   'value' => $pegawai->tahun_pensiun],
                        ['label' => 'Proyeksi KP 1',   'value' => $pegawai->proyeksi_kp_1],
                        ['label' => 'Proyeksi KP 2',   'value' => $pegawai->proyeksi_kp_2],
                    ];
                    @endphp
                    @foreach($kepeg as $row)
                    @if(!empty($row['value']))
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $row['value'] }}</dd>
                    </div>
                    @endif
                    @endforeach
                    @if($pegawai->keterangan_kp)
                    <div class="sm:col-span-2 lg:col-span-3">
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Keterangan KP</dt>
                        <dd class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-navy-700 rounded-xl p-4 leading-relaxed">{{ $pegawai->keterangan_kp }}</dd>
                    </div>
                    @endif
                </div>

                {{-- Timeline visual pensiun --}}
                @if($pegawai->tanggal_pensiun)
                @php
                    $pensiunDate = \Carbon\Carbon::parse($pegawai->tanggal_pensiun);
                    $bulanLagi   = now()->diffInMonths($pensiunDate, false);
                    $totalBulan  = 480; // ~40 tahun karier
                    $masaKerjaBulan = ($pegawai->masa_kerja_tahun ?? 0) * 12 + ($pegawai->masa_kerja_bulan ?? 0);
                    $pct = min(100, round(($masaKerjaBulan / $totalBulan) * 100));
                @endphp
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-navy-700">
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-3">Progres Karier</p>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500">TMT CPNS</span>
                        <div class="flex-1 bg-gray-200 dark:bg-navy-600 rounded-full h-3 relative overflow-hidden">
                            <div class="h-3 rounded-full bg-gradient-to-r from-navy-500 to-gold-500 transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500">Pensiun {{ $pensiunDate->format('Y') }}</span>
                    </div>
                    <div class="flex justify-between mt-1.5">
                        <span class="text-xs text-navy-600 dark:text-navy-400 font-medium">{{ $masaKerjaBulan }} bulan telah dilalui</span>
                        <span class="text-xs {{ $bulanLagi <= 12 ? 'text-red-600 dark:text-red-400' : 'text-gray-500' }} font-medium">
                            {{ max(0, $bulanLagi) }} bulan lagi
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Pendidikan --}}
            <div x-show="tab === 'pendidikan'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @php
                    $pendidikanData = [
                        ['label' => 'Jenjang Pendidikan', 'value' => $pegawai->pendidikan, 'badge' => true],
                        ['label' => 'Jurusan S1',         'value' => $pegawai->jurusan_s1],
                        ['label' => 'Jurusan S2',         'value' => $pegawai->jurusan_s2],
                        ['label' => 'Jurusan S3',         'value' => $pegawai->jurusan_s3],
                    ];
                    @endphp
                    @foreach($pendidikanData as $row)
                    @if(!empty($row['value']))
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">{{ $row['label'] }}</dt>
                        <dd>
                            @if(isset($row['badge']) && $row['badge'])
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                                {{ $row['value'] }}
                            </span>
                            @else
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $row['value'] }}</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                    @endforeach
                </div>

                @if(!$pegawai->pendidikan && !$pegawai->jurusan_s1 && !$pegawai->jurusan_s2 && !$pegawai->jurusan_s3)
                <div class="text-center py-10">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                    <p class="text-sm text-gray-400">Data pendidikan belum diisi</p>
                    <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}" class="text-xs text-navy-600 dark:text-navy-400 hover:underline mt-1 inline-block">Lengkapi data →</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Related Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @php
        $links = [
            ['route' => 'kepegawaian.sebaran.show', 'label' => 'Sebaran Pegawai', 'desc' => 'Info distribusi & sebaran', 'bg' => 'bg-navy-100 dark:bg-navy-700', 'hover' => 'hover:border-navy-200 dark:hover:border-navy-500', 'ico' => 'text-navy-600 dark:text-navy-300', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['route' => 'kepegawaian.grading.show',  'label' => 'Kenaikan Grading', 'desc' => 'Rekomendasi kenaikan grade', 'bg' => 'bg-gold-100 dark:bg-navy-700', 'hover' => 'hover:border-gold-200 dark:hover:border-navy-500', 'ico' => 'text-gold-600 dark:text-gold-400', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['route' => 'kepegawaian.mutasi.show',   'label' => 'Proyeksi Mutasi',  'desc' => 'Analisis mutasi pegawai', 'bg' => 'bg-green-100 dark:bg-navy-700', 'hover' => 'hover:border-green-200 dark:hover:border-navy-500', 'ico' => 'text-green-600 dark:text-green-400', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        ];
        @endphp
        @foreach($links as $link)
        <a href="{{ route($link['route'], $pegawai) }}"
            class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 {{ $link['hover'] }} hover:shadow-md transition-all group">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 {{ $link['bg'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 {{ $link['ico'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-navy-700 dark:group-hover:text-gold-400 transition-colors">{{ $link['label'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $link['desc'] }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Metadata --}}
    <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-600 px-1">
        <span>Dibuat: {{ $pegawai->created_at->translatedFormat('d F Y, H:i') }}</span>
        <span>Diperbarui: {{ $pegawai->updated_at->diffForHumans() }}</span>
    </div>

</div>
@endsection
