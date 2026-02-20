@extends('layouts.app')
@section('title', 'Detail Rekomendasi Grading — '.$pegawai->nama)

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Kenaikan Grading', 'url' => route('kepegawaian.grading'), 'active' => false],
                ['title' => 'Detail', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1">Detail Rekomendasi Grading</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Analisis kenaikan grading pegawai</p>
        </div>
        <a href="{{ route('kepegawaian.grading') }}"
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
                    @if($pegawai->jabatan)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-white/10 border border-white/20">{{ $pegawai->jabatan }}</span>
                    @endif
                    @if($pegawai->bagian)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gold-500/20 text-gold-300 border border-gold-500/30">{{ $pegawai->bagian }}</span>
                    @endif
                    @if($pegawai->eselon)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30">{{ $pegawai->eselon }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Status Card --}}
    @if($rekomendasi['eligible'])
    <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-700 rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-green-800 dark:text-green-300">Eligible untuk Kenaikan Grading</h3>
                <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                    Pegawai ini memenuhi kriteria untuk kenaikan grading pada tahun {{ $rekomendasi['tahun'] }}
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-700 rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-amber-800 dark:text-amber-300">Belum Memenuhi Kriteria</h3>
                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Pegawai ini belum memenuhi kriteria untuk kenaikan grading</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Grading Comparison --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-5 text-center">Grading Saat Ini</h3>
            <div class="flex flex-col items-center justify-center py-4">
                <div class="w-32 h-32 bg-gray-100 dark:bg-navy-700 rounded-full flex items-center justify-center shadow-inner">
                    <span class="text-5xl font-bold text-gray-600 dark:text-gray-300">{{ $rekomendasi['grading_sekarang'] }}</span>
                </div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mt-4">Grade {{ $rekomendasi['grading_sekarang'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-5 text-center">Grading Rekomendasi</h3>
            <div class="flex flex-col items-center justify-center py-4">
                <div class="w-32 h-32 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center shadow-inner">
                    <span class="text-5xl font-bold text-green-600 dark:text-green-400">{{ $rekomendasi['grading_baru'] }}</span>
                </div>
                <p class="text-sm font-semibold text-green-600 dark:text-green-400 mt-4">Grade {{ $rekomendasi['grading_baru'] }}</p>
                @if($rekomendasi['grading_baru'] > $rekomendasi['grading_sekarang'])
                <div class="flex items-center gap-1 mt-2">
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs font-semibold text-green-600 dark:text-green-400">
                        Naik {{ $rekomendasi['grading_baru'] - $rekomendasi['grading_sekarang'] }} grade
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Alasan --}}
    @if($rekomendasi['eligible'] && count($rekomendasi['alasan']) > 0)
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Alasan Rekomendasi</h3>
        <ul class="space-y-3">
            @foreach($rekomendasi['alasan'] as $alasan)
            <li class="flex items-start gap-3">
                <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $alasan }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Data Pegawai --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-5">Data Pegawai</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php
            $rows = [
                ['label' => 'Jabatan',     'value' => $pegawai->jabatan],
                ['label' => 'Eselon',      'value' => $pegawai->eselon],
                ['label' => 'Bagian',      'value' => $pegawai->bagian],
                ['label' => 'Pendidikan',  'value' => $pegawai->pendidikan],
                ['label' => 'Jurusan S1',  'value' => $pegawai->jurusan_s1],
                ['label' => 'Masa Kerja',  'value' => ($pegawai->masa_kerja_tahun ?? 0).' Tahun '.($pegawai->masa_kerja_bulan ?? 0).' Bulan'],
                ['label' => 'TMT CPNS',    'value' => $pegawai->tmt_cpns ? $pegawai->tmt_cpns->translatedFormat('d F Y') : null],
                ['label' => 'Pangkat',     'value' => $pegawai->pangkat],
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

    {{-- Info Box --}}
    <div class="bg-navy-50 dark:bg-navy-800/50 border border-navy-200 dark:border-navy-700 rounded-2xl p-5">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-navy-800 dark:text-navy-200 mb-1">Catatan</h4>
                <p class="text-sm text-navy-700 dark:text-navy-300">Rekomendasi ini berdasarkan analisis sistem dan perlu diverifikasi lebih lanjut oleh pejabat yang berwenang sesuai peraturan kepegawaian yang berlaku.</p>
            </div>
        </div>
    </div>
</div>
@endsection
