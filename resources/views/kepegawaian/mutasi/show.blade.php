@extends('layouts.app')
@section('title', 'Detail Proyeksi Mutasi — '.$pegawai->nama)

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Proyeksi Mutasi', 'url' => route('kepegawaian.mutasi'), 'active' => false],
                ['title' => 'Detail', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1">Detail Proyeksi Mutasi</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Analisis dan rekomendasi mutasi pegawai</p>
        </div>
        <a href="{{ route('kepegawaian.mutasi') }}"
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
                    @if($pegawai->eselon)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30">{{ $pegawai->eselon }}</span>
                    @endif
                    @if($pegawai->bagian)
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gold-500/20 text-gold-300 border border-gold-500/30">{{ $pegawai->bagian }}</span>
                    @endif
                </div>
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
            </div>
        </div>
    </div>

    {{-- Priority Status --}}
    @if($analisis['perlu_mutasi'])
        @php
        $priority = $analisis['prioritas'];
        if ($priority >= 5) {
            $bgCard = 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700';
            $iconBg = 'bg-red-100 dark:bg-red-900/40';
            $iconColor = 'text-red-600 dark:text-red-400';
            $titleColor = 'text-red-800 dark:text-red-300';
            $descColor = 'text-red-700 dark:text-red-400';
            $label = 'Prioritas Tinggi — Segera perlu dimutasi';
        } elseif ($priority >= 3) {
            $bgCard = 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-700';
            $iconBg = 'bg-orange-100 dark:bg-orange-900/40';
            $iconColor = 'text-orange-600 dark:text-orange-400';
            $titleColor = 'text-orange-800 dark:text-orange-300';
            $descColor = 'text-orange-700 dark:text-orange-400';
            $label = 'Prioritas Sedang — Perlu dipertimbangkan untuk mutasi';
        } else {
            $bgCard = 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700';
            $iconBg = 'bg-amber-100 dark:bg-amber-900/40';
            $iconColor = 'text-amber-600 dark:text-amber-400';
            $titleColor = 'text-amber-800 dark:text-amber-300';
            $descColor = 'text-amber-700 dark:text-amber-400';
            $label = 'Prioritas Rendah — Dipantau untuk mutasi';
        }
        @endphp
        <div class="border-2 {{ $bgCard }} rounded-2xl p-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 {{ $iconBg }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold {{ $titleColor }}">{{ $label }}</h3>
                    <p class="text-sm {{ $descColor }} mt-1">Skor prioritas: <strong>{{ $priority }}</strong></p>
                </div>
            </div>
        </div>
    @else
        <div class="border-2 bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 rounded-2xl p-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-green-800 dark:text-green-300">Tidak Perlu Mutasi</h3>
                    <p class="text-sm text-green-700 dark:text-green-400 mt-1">Pegawai ini tidak memerlukan mutasi dalam waktu dekat</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Timeline & Recommendation --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-navy-50 dark:bg-navy-800/50 border border-navy-200 dark:border-navy-700 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Masa Jabatan</h3>
            <div class="text-center py-4">
                @if($pegawai->proyeksi_kp_1)
                @php
                try {
                    $tmt = \Carbon\Carbon::parse($pegawai->proyeksi_kp_1);
                    $lamaJabatan = $tmt->diffInMonths(now());
                    $tahunJ = floor($lamaJabatan / 12);
                    $bulanJ = $lamaJabatan % 12;
                } catch (\Exception $e) { $lamaJabatan = 0; $tahunJ = 0; $bulanJ = 0; }
                @endphp
                <div class="text-5xl font-bold text-navy-700 dark:text-navy-200">{{ $lamaJabatan }}</div>
                <p class="text-gray-600 dark:text-gray-400 mt-2 font-medium">Bulan di jabatan saat ini</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $tahunJ }} tahun {{ $bulanJ }} bulan</p>
                @else
                <div class="text-3xl font-bold text-gray-400 dark:text-gray-500">N/A</div>
                <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Data TMT tidak tersedia</p>
                @endif
            </div>
        </div>

        <div class="bg-gold-50 dark:bg-navy-800/50 border border-gold-200 dark:border-navy-700 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Rekomendasi Waktu</h3>
            <div class="text-center py-4">
                <div class="text-2xl font-bold text-gold-700 dark:text-gold-400">
                    {{ $analisis['rekomendasi_waktu'] ?? 'Belum Ditentukan' }}
                </div>
                <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm">Waktu mutasi yang direkomendasikan</p>
            </div>
        </div>
    </div>

    {{-- Alasan --}}
    @if($analisis['perlu_mutasi'] && count($analisis['alasan']) > 0)
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Pertimbangan Mutasi</h3>
        <ul class="space-y-3">
            @foreach($analisis['alasan'] as $alasan)
            <li class="flex items-start gap-3">
                <div class="w-6 h-6 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
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
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-5">Informasi Pegawai</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php
            $rows = [
                ['label' => 'Jabatan Saat Ini', 'value' => $pegawai->jabatan],
                ['label' => 'Eselon',           'value' => $pegawai->eselon],
                ['label' => 'Bagian',           'value' => $pegawai->bagian],
                ['label' => 'Subbagian',        'value' => $pegawai->subbagian],
                ['label' => 'Usia',             'value' => $pegawai->usia ? $pegawai->usia.' tahun' : null],
                ['label' => 'Tanggal Pensiun',  'value' => $pegawai->tanggal_pensiun ? $pegawai->tanggal_pensiun->translatedFormat('d F Y') : null],
                ['label' => 'Masa Kerja',       'value' => ($pegawai->masa_kerja_tahun ?? 0).' Tahun '.($pegawai->masa_kerja_bulan ?? 0).' Bulan'],
                ['label' => 'Grading',          'value' => $pegawai->grading ? 'Grade '.$pegawai->grading : null],
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
                <h4 class="text-sm font-semibold text-navy-800 dark:text-navy-200 mb-2">Informasi Penting</h4>
                <ul class="space-y-1 text-sm text-navy-700 dark:text-navy-300">
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 bg-navy-400 rounded-full mt-1.5 flex-shrink-0"></span>Proyeksi mutasi ini bersifat rekomendasi dan memerlukan persetujuan pejabat berwenang</li>
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 bg-navy-400 rounded-full mt-1.5 flex-shrink-0"></span>Waktu mutasi dapat berubah sesuai dengan kebutuhan organisasi dan kebijakan yang berlaku</li>
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 bg-navy-400 rounded-full mt-1.5 flex-shrink-0"></span>Mutasi biasanya dilaksanakan pada bulan <strong>April</strong> atau <strong>Oktober</strong> setiap tahun</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
