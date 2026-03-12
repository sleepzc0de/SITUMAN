@extends('layouts.app')
@section('title', 'Detail Grading — '.$pegawai->nama)

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <nav class="breadcrumb mb-2" aria-label="Breadcrumb">
            <span class="text-gray-400 dark:text-gray-500 text-sm">Kepegawaian</span>
            <svg class="w-3.5 h-3.5 mx-1 text-gray-300 dark:text-gray-600"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('kepegawaian.grading') }}"
               class="text-sm text-gray-500 dark:text-gray-400
                      hover:text-navy-600 dark:hover:text-navy-300 transition-colors">
                Kenaikan Grading
            </a>
            <svg class="w-3.5 h-3.5 mx-1 text-gray-300 dark:text-gray-600"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Detail</span>
        </nav>
        <h1 class="page-title">Detail Rekomendasi Grading</h1>
        <p class="page-subtitle">Analisis kenaikan grading untuk {{ $pegawai->nama }}</p>
    </div>

    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}"
           class="btn btn-ghost btn-sm gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profil Pegawai
        </a>
        <a href="{{ route('kepegawaian.grading') }}"
           class="btn btn-outline btn-sm gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Profile Hero --}}
    <div class="relative overflow-hidden rounded-2xl shadow-xl
                bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 text-white">
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-gold-500/5 rounded-full pointer-events-none"></div>

        <div class="relative p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row gap-5 items-start">
                {{-- Avatar --}}
                <div class="w-20 h-20 bg-gradient-to-br from-navy-400 to-gold-500
                            rounded-2xl flex items-center justify-center shadow-xl flex-shrink-0">
                    <span class="text-3xl font-bold uppercase">
                        {{ get_initials($pegawai->nama) }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl lg:text-2xl font-bold leading-tight">
                        {{ $pegawai->nama_gelar ?? $pegawai->nama }}
                    </h2>
                    <p class="text-navy-300 font-mono text-sm mt-1">NIP {{ $pegawai->nip }}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        @if($pegawai->jabatan)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg
                                     bg-white/10 border border-white/15">
                            {{ $pegawai->jabatan }}
                        </span>
                        @endif
                        @if($pegawai->bagian)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg
                                     bg-gold-500/20 text-gold-300 border border-gold-500/25">
                            {{ $pegawai->bagian }}
                        </span>
                        @endif
                        @if($pegawai->eselon)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg
                                     bg-purple-500/20 text-purple-300 border border-purple-500/25">
                            Eselon {{ $pegawai->eselon }}
                        </span>
                        @endif
                        @if($pegawai->pendidikan)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg
                                     bg-emerald-500/20 text-emerald-300 border border-emerald-500/25">
                            {{ $pegawai->pendidikan }}
                        </span>
                        @endif
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg
                                     bg-sky-500/20 text-sky-300 border border-sky-500/25">
                            {{ $rekomendasi['level_jabatan'] }}
                        </span>
                    </div>
                </div>

                {{-- Badge Status --}}
                <div class="flex-shrink-0 self-start">
                    @if($rekomendasi['eligible'])
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl
                                 text-sm font-bold bg-emerald-500/20 text-emerald-300
                                 border border-emerald-500/30">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Eligible
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl
                                 text-sm font-bold bg-amber-500/20 text-amber-300
                                 border border-amber-500/30">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Belum Eligible
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Grade Comparison --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-stretch">

        {{-- Grade Sekarang --}}
        <div class="card !p-6 text-center flex flex-col items-center justify-between gap-4">
            <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                Grade Saat Ini
            </p>
            <div class="w-28 h-28 bg-gray-100 dark:bg-navy-700 rounded-full
                        flex items-center justify-center shadow-inner">
                <span class="text-5xl font-bold text-gray-600 dark:text-gray-300">
                    {{ $rekomendasi['grading_sekarang'] }}
                </span>
            </div>
            <div class="text-center">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                    Grade {{ $rekomendasi['grading_sekarang'] }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    {{ $rekomendasi['level_jabatan'] }}
                </p>
            </div>
        </div>

        {{-- Tengah: Panah + Batas Maks --}}
        <div class="flex flex-col items-center justify-center gap-3 py-4">
            @if($rekomendasi['grading_baru'] > $rekomendasi['grading_sekarang'])
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full
                        flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                +{{ $rekomendasi['grading_baru'] - $rekomendasi['grading_sekarang'] }} Grade
            </span>
            @else
            <div class="w-12 h-12 bg-gray-100 dark:bg-navy-700 rounded-full
                        flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-gray-400">Tidak berubah</span>
            @endif

            {{-- Batas Maks --}}
            <div class="mt-1 px-4 py-2 bg-navy-50 dark:bg-navy-700/60
                        border border-navy-200 dark:border-navy-600 rounded-xl text-center">
                <p class="text-[10px] font-semibold text-navy-500 dark:text-navy-400
                           uppercase tracking-wider">Batas Maks</p>
                <p class="text-xl font-bold text-navy-700 dark:text-navy-200">
                    G{{ $rekomendasi['grading_max'] }}
                </p>
                <p class="text-[10px] text-navy-400 dark:text-navy-500 mt-0.5">
                    {{ $rekomendasi['level_jabatan'] }}
                </p>
            </div>
        </div>

        {{-- Grade Rekomendasi --}}
        <div class="card !p-6 text-center flex flex-col items-center justify-between gap-4
                    {{ $rekomendasi['eligible'] ? '!border-emerald-200 dark:!border-emerald-700' : '' }}">
            <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                Grade Rekomendasi
            </p>
            <div class="w-28 h-28 rounded-full flex items-center justify-center shadow-inner
                        {{ $rekomendasi['eligible']
                            ? 'bg-emerald-100 dark:bg-emerald-900/30'
                            : 'bg-gray-100 dark:bg-navy-700' }}">
                <span class="text-5xl font-bold
                             {{ $rekomendasi['eligible']
                                 ? 'text-emerald-600 dark:text-emerald-400'
                                 : 'text-gray-400' }}">
                    {{ $rekomendasi['grading_baru'] }}
                </span>
            </div>
            <div class="text-center">
                <p class="text-sm font-semibold
                          {{ $rekomendasi['eligible']
                              ? 'text-emerald-600 dark:text-emerald-400'
                              : 'text-gray-400' }}">
                    Grade {{ $rekomendasi['grading_baru'] }}
                </p>
                @if($rekomendasi['grading_baru'] >= $rekomendasi['grading_max'])
                <span class="inline-block mt-1 px-2.5 py-0.5 text-[10px] font-bold rounded-full
                             bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                    Batas Maksimal
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Alasan & Catatan --}}
    @if(count($rekomendasi['alasan']) > 0 || count($rekomendasi['catatan'] ?? []) > 0)
    <div class="grid grid-cols-1 {{ (count($rekomendasi['alasan']) > 0 && count($rekomendasi['catatan'] ?? []) > 0) ? 'sm:grid-cols-2' : '' }} gap-4">

        @if(count($rekomendasi['alasan']) > 0)
        <div class="card">
            <h3 class="section-title mb-4">Alasan Rekomendasi</h3>
            <ul class="space-y-3">
                @foreach($rekomendasi['alasan'] as $alasan)
                <li class="flex items-start gap-3">
                    <div class="w-6 h-6 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg
                                flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $alasan }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($rekomendasi['catatan'] ?? []) > 0)
        <div class="card">
            <h3 class="section-title mb-4">Catatan Sistem</h3>
            <ul class="space-y-3">
                @foreach($rekomendasi['catatan'] as $cat)
                <li class="flex items-start gap-3">
                    <div class="w-6 h-6 bg-amber-100 dark:bg-amber-900/30 rounded-lg
                                flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $cat }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>
    @endif

    {{-- Data Kepegawaian --}}
    <div class="card !p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700
                    flex items-center justify-between">
            <div>
                <h3 class="section-title">Data Kepegawaian</h3>
                <p class="section-desc">Informasi dasar yang digunakan dalam analisis</p>
            </div>
            <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}"
               class="text-xs font-semibold text-navy-600 dark:text-navy-300
                      hover:text-navy-800 dark:hover:text-navy-100 transition-colors">
                Lihat lengkap →
            </a>
        </div>
        <div class="p-5">
            @php
            $fields = [
                ['label' => 'Pangkat / Golongan',  'value' => $pegawai->pangkat,       'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                ['label' => 'Pendidikan Terakhir', 'value' => $pegawai->pendidikan,    'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
                ['label' => 'Jurusan S1',          'value' => $pegawai->jurusan_s1,    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['label' => 'Jurusan S2',          'value' => $pegawai->jurusan_s2,    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['label' => 'Masa Kerja',          'value' => ($pegawai->masa_kerja_tahun ?? 0).' tahun '.($pegawai->masa_kerja_bulan ?? 0).' bulan', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'TMT CPNS',            'value' => $pegawai->tmt_cpns ? $pegawai->tmt_cpns->translatedFormat('d F Y') : null, 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['label' => 'Lokasi Kerja',        'value' => $pegawai->lokasi,        'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Jenis Pegawai',       'value' => $pegawai->jenis_pegawai, 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['label' => 'Proyeksi KP',         'value' => $pegawai->proyeksi_kp_1, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                ['label' => 'Subbagian',           'value' => $pegawai->subbagian,     'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ];
            @endphp

            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($fields as $field)
                @if(!empty($field['value']))
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-navy-700 rounded-lg
                                flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.8" d="{{ $field['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500
                                   uppercase tracking-wide">
                            {{ $field['label'] }}
                        </dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white mt-0.5 leading-snug">
                            {{ $field['value'] }}
                        </dd>
                    </div>
                </div>
                @endif
                @endforeach
            </dl>
        </div>
    </div>

    {{-- Disclaimer --}}
    <div class="alert-info">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="font-semibold text-sm mb-0.5">Catatan Penting</p>
            <p class="text-sm">
                Rekomendasi ini dihasilkan secara otomatis berdasarkan analisis sistem dan
                <strong>perlu diverifikasi lebih lanjut</strong> oleh pejabat yang berwenang
                sesuai peraturan kepegawaian yang berlaku.
            </p>
        </div>
    </div>

</div>
@endsection
