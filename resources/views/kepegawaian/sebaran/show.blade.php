@extends('layouts.app')
@section('title', 'Detail — '.$pegawai->nama)

@section('content')
<div class="space-y-6 animate-fade-in">

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
                <a href="{{ route('kepegawaian.sebaran') }}" class="breadcrumb-item">Sebaran Pegawai</a>
                <svg class="breadcrumb-sep w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="breadcrumb-current">{{ Str::limit($pegawai->nama, 30) }}</span>
            </nav>
            <h1 class="page-title">Detail Pegawai</h1>
            <p class="page-subtitle">Informasi lengkap dan riwayat kepegawaian</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            @canaccess('kepegawaian')
            <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}" class="btn btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Data
            </a>
            @endcanaccess
            <a href="{{ route('kepegawaian.sebaran') }}" class="btn btn-ghost btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         PROFILE HERO
    ══════════════════════════════════════════════════ --}}
    <div class="relative bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900
                rounded-2xl p-6 lg:p-8 text-white shadow-xl overflow-hidden">
        {{-- decorative --}}
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-40 translate-x-40 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-gold-500/10 rounded-full translate-y-28 -translate-x-20 pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row gap-6">

            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <div class="w-20 h-20 bg-gradient-to-br from-navy-400 to-gold-500 rounded-2xl
                            flex items-center justify-center shadow-xl ring-4 ring-white/10">
                    <span class="text-3xl font-bold text-white uppercase">
                        {{ substr($pegawai->nama, 0, 2) }}
                    </span>
                </div>
            </div>

            {{-- Info utama --}}
            <div class="flex-1 min-w-0">
                <h2 class="text-xl lg:text-2xl font-bold leading-tight">
                    {{ $pegawai->nama_gelar ?? $pegawai->nama }}
                </h2>
                <p class="text-navy-300 font-mono text-sm mt-1">{{ $pegawai->nip }}</p>

                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($pegawai->grading)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gold-500/20 text-gold-300 border border-gold-500/30">
                        Grade {{ $pegawai->grading }}
                    </span>
                    @endif
                    @if($pegawai->status)
                    @php
                        $heroBadge = [
                            'AKTIF'     => 'bg-green-500/20 text-green-300 border border-green-500/30',
                            'CLTN'      => 'bg-sky-500/20 text-sky-300 border border-sky-500/30',
                            'PENSIUN'   => 'bg-gray-500/20 text-gray-300 border border-gray-500/30',
                            'NON AKTIF' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                        ][$pegawai->status] ?? 'bg-gray-500/20 text-gray-300 border border-gray-500/30';
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $heroBadge }}">
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
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-sky-500/20 text-sky-300 border border-sky-500/30">
                        {{ $pegawai->jenis_pegawai }}
                    </span>
                    @endif
                </div>

                {{-- Jabatan / Bagian / Lokasi --}}
                @if($pegawai->jabatan || $pegawai->bagian || $pegawai->lokasi)
                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-1.5">
                    @if($pegawai->jabatan)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $pegawai->jabatan }}
                    </div>
                    @endif
                    @if($pegawai->bagian)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ $pegawai->bagian }}
                        @if($pegawai->subbagian)
                        <span class="text-navy-400">/ {{ $pegawai->subbagian }}</span>
                        @endif
                    </div>
                    @endif
                    @if($pegawai->lokasi)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        {{ $pegawai->lokasi }}
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Masa kerja summary --}}
            <div class="flex-shrink-0 hidden lg:flex flex-col items-end justify-start gap-1 text-right">
                <p class="text-xs text-navy-400 uppercase tracking-wide font-semibold">Masa Kerja</p>
                <p class="text-2xl font-bold text-white">
                    {{ $pegawai->masa_kerja_tahun ?? 0 }}
                    <span class="text-sm font-normal text-navy-300">th</span>
                    {{ $pegawai->masa_kerja_bulan ?? 0 }}
                    <span class="text-sm font-normal text-navy-300">bln</span>
                </p>
                @if($pegawai->tanggal_pensiun)
                <p class="text-xs text-navy-400 mt-1">
                    Pensiun {{ $pegawai->tanggal_pensiun->translatedFormat('d M Y') }}
                </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABS
    ══════════════════════════════════════════════════ --}}
    <div x-data="{ tab: 'personal' }">

        {{-- Tab nav --}}
        <div class="bg-white dark:bg-navy-800 rounded-t-2xl border border-gray-100 dark:border-navy-700 border-b-0 px-5 pt-4">
            <nav class="-mb-px flex gap-1 overflow-x-auto scrollbar-none">
                @php
                $tabs = [
                    ['id' => 'personal',    'label' => 'Data Personal',      'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['id' => 'jabatan',     'label' => 'Jabatan & Posisi',   'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['id' => 'kepegawaian','label' => 'Riwayat Kepegawaian','icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ];
                @endphp
                @foreach($tabs as $t)
                <button
                    @click="tab = '{{ $t['id'] }}'"
                    :class="tab === '{{ $t['id'] }}'
                        ? 'border-navy-600 text-navy-700 dark:text-gold-400 bg-navy-50 dark:bg-navy-700/50'
                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700/30'"
                    class="flex items-center gap-2 px-4 py-3 border-b-2 text-sm font-medium
                           transition-all whitespace-nowrap rounded-t-xl">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/>
                    </svg>
                    {{ $t['label'] }}
                </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab panels --}}
        <div class="bg-white dark:bg-navy-800 rounded-b-2xl border border-gray-100 dark:border-navy-700 border-t-0">

            {{-- ── Personal ─────────────────────────────────────────────── --}}
            <div x-show="tab === 'personal'" x-transition:enter="transition-opacity duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-5">
                    @php
                    $personalFields = [
                        ['label' => 'Nama Lengkap',   'value' => $pegawai->nama],
                        ['label' => 'Nama Gelar',     'value' => $pegawai->nama_gelar],
                        ['label' => 'NIP',            'value' => $pegawai->nip,            'mono' => true],
                        ['label' => 'Jenis Kelamin',  'value' => $pegawai->jenis_kelamin],
                        ['label' => 'Tanggal Lahir',  'value' => $pegawai->tanggal_lahir?->translatedFormat('d F Y')],
                        ['label' => 'Usia',           'value' => $pegawai->usia ? $pegawai->usia.' tahun' : null],
                        ['label' => 'Pendidikan',     'value' => $pegawai->pendidikan],
                        ['label' => 'Jurusan S1',     'value' => $pegawai->jurusan_s1],
                        ['label' => 'Jurusan S2',     'value' => $pegawai->jurusan_s2],
                        ['label' => 'Email Kemenkeu', 'value' => $pegawai->email_kemenkeu, 'link' => 'mailto:'],
                        ['label' => 'Email Pribadi',  'value' => $pegawai->email_pribadi,  'link' => 'mailto:'],
                        ['label' => 'No. HP',         'value' => $pegawai->no_hp,          'link' => 'tel:'],
                    ];
                    @endphp
                    @foreach($personalFields as $f)
                    @if(!empty($f['value']))
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">
                            {{ $f['label'] }}
                        </dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white {{ ($f['mono'] ?? false) ? 'font-mono' : '' }}">
                            @if(isset($f['link']))
                            <a href="{{ $f['link'] }}{{ $f['value'] }}"
                               class="text-navy-600 dark:text-navy-400 hover:underline">
                                {{ $f['value'] }}
                            </a>
                            @else
                            {{ $f['value'] }}
                            @endif
                        </dd>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- ── Jabatan ──────────────────────────────────────────────── --}}
            <div x-show="tab === 'jabatan'" x-transition:enter="transition-opacity duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 class="p-6" style="display:none">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-5">
                    @php
                    $jabatanFields = [
                        ['label' => 'Jabatan',       'value' => $pegawai->jabatan],
                        ['label' => 'Jenis Jabatan', 'value' => $pegawai->jenis_jabatan],
                        ['label' => 'Nama Jabatan',  'value' => $pegawai->nama_jabatan],
                        ['label' => 'Eselon',        'value' => $pegawai->eselon],
                        ['label' => 'Grading',       'value' => $pegawai->grading ? 'Grade '.$pegawai->grading : null],
                        ['label' => 'Jenis Pegawai', 'value' => $pegawai->jenis_pegawai],
                        ['label' => 'Bagian',        'value' => $pegawai->bagian],
                        ['label' => 'Subbagian',     'value' => $pegawai->subbagian],
                        ['label' => 'Lokasi',        'value' => $pegawai->lokasi],
                        ['label' => 'Pangkat',       'value' => $pegawai->pangkat],
                        ['label' => 'Status',        'value' => $pegawai->status],
                    ];
                    @endphp
                    @foreach($jabatanFields as $f)
                    @if(!empty($f['value']))
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">
                            {{ $f['label'] }}
                        </dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $f['value'] }}</dd>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- ── Kepegawaian ──────────────────────────────────────────── --}}
            <div x-show="tab === 'kepegawaian'" x-transition:enter="transition-opacity duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 class="p-6" style="display:none">

                {{-- Summary cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-navy-50 dark:bg-navy-700/40 rounded-xl p-4 border border-navy-100 dark:border-navy-600">
                        <p class="text-xs font-semibold text-navy-500 dark:text-navy-400 uppercase tracking-wide mb-1">TMT CPNS</p>
                        <p class="text-base font-bold text-gray-900 dark:text-white">
                            {{ $pegawai->tmt_cpns?->translatedFormat('d F Y') ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-gold-50 dark:bg-navy-700/40 rounded-xl p-4 border border-gold-100 dark:border-navy-600">
                        <p class="text-xs font-semibold text-gold-600 dark:text-gold-400 uppercase tracking-wide mb-1">Masa Kerja</p>
                        <p class="text-base font-bold text-gray-900 dark:text-white">
                            {{ $pegawai->masa_kerja_tahun ?? 0 }} th {{ $pegawai->masa_kerja_bulan ?? 0 }} bln
                        </p>
                    </div>
                    <div class="bg-red-50 dark:bg-navy-700/40 rounded-xl p-4 border border-red-100 dark:border-navy-600">
                        <p class="text-xs font-semibold text-red-500 dark:text-red-400 uppercase tracking-wide mb-1">Tanggal Pensiun</p>
                        <p class="text-base font-bold text-gray-900 dark:text-white">
                            {{ $pegawai->tanggal_pensiun?->translatedFormat('d F Y') ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Proyeksi KP --}}
                @if($pegawai->proyeksi_kp_1 || $pegawai->proyeksi_kp_2)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 mb-6">
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
                </div>
                @endif

                {{-- Keterangan KP --}}
                @if($pegawai->keterangan_kp)
                <div>
                    <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">
                        Keterangan KP
                    </dt>
                    <dd class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-navy-700
                               rounded-xl p-4 leading-relaxed border border-gray-100 dark:border-navy-600">
                        {{ $pegawai->keterangan_kp }}
                    </dd>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         NAVIGASI MODUL TERKAIT
    ══════════════════════════════════════════════════ --}}
    <div>
        <p class="section-label mb-3">Modul Terkait Pegawai Ini</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <a href="{{ route('kepegawaian.grading.show', $pegawai) }}"
               class="card-hover flex items-center gap-4 !p-4 group">
                <div class="w-11 h-11 bg-gold-100 dark:bg-navy-700 rounded-xl flex items-center justify-center
                            group-hover:scale-110 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Kenaikan Grading</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rekomendasi grading</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:translate-x-1 transition-transform flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <a href="{{ route('kepegawaian.mutasi.show', $pegawai) }}"
               class="card-hover flex items-center gap-4 !p-4 group">
                <div class="w-11 h-11 bg-green-100 dark:bg-navy-700 rounded-xl flex items-center justify-center
                            group-hover:scale-110 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Proyeksi Mutasi</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Analisis mutasi pegawai</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:translate-x-1 transition-transform flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            @canaccess('kepegawaian')
            <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}"
               class="card-hover flex items-center gap-4 !p-4 group">
                <div class="w-11 h-11 bg-navy-100 dark:bg-navy-700 rounded-xl flex items-center justify-center
                            group-hover:scale-110 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5 text-navy-600 dark:text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Edit Data Pegawai</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Perbarui informasi</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:translate-x-1 transition-transform flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endcanaccess

        </div>
    </div>

</div>
@endsection
