@extends('layouts.app')
@section('title', 'Detail Proyeksi Mutasi — '.$pegawai->nama)

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <nav class="flex items-center gap-1.5 text-xs mb-2">
            <span class="text-gray-400 dark:text-gray-500">Kepegawaian</span>
            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('kepegawaian.mutasi') }}"
               class="text-navy-600 dark:text-navy-400 hover:text-navy-800 dark:hover:text-navy-200
                      font-medium transition-colors">Proyeksi Mutasi</a>
            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-navy-600 dark:text-navy-400 font-semibold truncate max-w-32">
                {{ $pegawai->nama }}
            </span>
        </nav>
        <h1 class="page-title">Detail <span class="text-gradient animate-gradient">Proyeksi Mutasi</span></h1>
        <p class="page-subtitle mt-1">Analisis mendalam dan rekomendasi mutasi pegawai</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}" class="btn btn-outline btn-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
            Profil Lengkap
        </a>
        <a href="{{ route('kepegawaian.mutasi') }}" class="btn btn-ghost btn-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
@php
    $skor = $analisis['prioritas'];
    $lama = $analisis['lama_jabatan'] ?? null;
    $prog = $analisis['progress_jabatan'] ?? null;

    if (!$analisis['perlu_mutasi']) {
        $statusConfig = [
            'grad'    => 'from-emerald-500 to-teal-600',
            'glow'    => 'shadow-emerald-500/30',
            'light'   => 'text-emerald-100',
            'bg'      => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800',
            'icon_bg' => 'bg-emerald-100 dark:bg-emerald-900/40',
            'icon_c'  => 'text-emerald-600 dark:text-emerald-400',
            'title_c' => 'text-emerald-800 dark:text-emerald-200',
            'desc_c'  => 'text-emerald-700 dark:text-emerald-400',
            'label'   => 'Tidak Perlu Mutasi',
            'desc'    => 'Pegawai ini tidak memenuhi kriteria mutasi dalam waktu dekat.',
            'badge'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
            'icon'    => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ];
    } elseif ($skor >= 5) {
        $statusConfig = [
            'grad'    => 'from-rose-500 to-red-600',
            'glow'    => 'shadow-rose-500/30',
            'light'   => 'text-rose-100',
            'bg'      => 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800',
            'icon_bg' => 'bg-rose-100 dark:bg-rose-900/40',
            'icon_c'  => 'text-rose-600 dark:text-rose-400',
            'title_c' => 'text-rose-800 dark:text-rose-200',
            'desc_c'  => 'text-rose-700 dark:text-rose-400',
            'label'   => 'Prioritas Tinggi',
            'desc'    => 'Pegawai ini memenuhi kriteria mutasi mendesak dan perlu segera ditindaklanjuti.',
            'badge'   => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
            'icon'    => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        ];
    } elseif ($skor >= 3) {
        $statusConfig = [
            'grad'    => 'from-orange-500 to-amber-600',
            'glow'    => 'shadow-orange-500/30',
            'light'   => 'text-orange-100',
            'bg'      => 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800',
            'icon_bg' => 'bg-orange-100 dark:bg-orange-900/40',
            'icon_c'  => 'text-orange-600 dark:text-orange-400',
            'title_c' => 'text-orange-800 dark:text-orange-200',
            'desc_c'  => 'text-orange-700 dark:text-orange-400',
            'label'   => 'Prioritas Sedang',
            'desc'    => 'Pegawai ini perlu segera direncanakan mutasinya dalam waktu dekat.',
            'badge'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            'icon'    => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
        ];
    } else {
        $statusConfig = [
            'grad'    => 'from-amber-400 to-yellow-500',
            'glow'    => 'shadow-amber-400/30',
            'light'   => 'text-amber-100',
            'bg'      => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800',
            'icon_bg' => 'bg-amber-100 dark:bg-amber-900/40',
            'icon_c'  => 'text-amber-600 dark:text-amber-400',
            'title_c' => 'text-amber-800 dark:text-amber-200',
            'desc_c'  => 'text-amber-700 dark:text-amber-400',
            'label'   => 'Prioritas Rendah',
            'desc'    => 'Pegawai ini berada dalam pantauan untuk mutasi.',
            'badge'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            'icon'    => 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        ];
    }
@endphp

<div class="space-y-6 animate-fade-in">

    {{-- ── Hero Profile ── --}}
    <div class="relative overflow-hidden rounded-3xl shadow-2xl {{ $statusConfig['glow'] }}">
        {{-- Background gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br {{ $statusConfig['grad'] }} opacity-95"></div>

        {{-- Pattern overlay --}}
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px);
                    background-size: 20px 20px;"></div>

        {{-- Decorative circles --}}
        <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-8 w-48 h-48 rounded-full bg-black/15 pointer-events-none"></div>
        <div class="absolute top-1/2 right-1/4 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="relative p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row gap-6 items-start">

                {{-- Avatar --}}
                <div class="relative flex-shrink-0">
                    <div class="w-24 h-24 rounded-2xl bg-white/20 backdrop-blur-sm
                                border-2 border-white/30 flex items-center justify-center shadow-xl">
                        <span class="text-4xl font-black text-white uppercase tracking-wider">
                            {{ get_initials($pegawai->nama) }}
                        </span>
                    </div>
                    <div class="absolute -bottom-2 -right-2 px-2.5 py-1 rounded-lg
                                bg-white/20 backdrop-blur-sm border border-white/30">
                        <span class="text-xs font-bold text-white">Skor {{ $skor }}</span>
                    </div>
                </div>

                {{-- Identity --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start gap-3 flex-wrap">
                        <h2 class="text-2xl lg:text-3xl font-black text-white leading-tight">
                            {{ $pegawai->nama_gelar ?? $pegawai->nama }}
                        </h2>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusConfig['badge'] }}
                                     self-center">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>
                    <p class="{{ $statusConfig['light'] }} mt-1 font-mono text-sm tracking-widest">
                        NIP {{ $pegawai->nip }}
                    </p>

                    <div class="flex flex-wrap gap-2 mt-4">
                        @if($pegawai->jabatan)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                                     bg-white/15 backdrop-blur-sm border border-white/25 text-white text-xs font-semibold">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $pegawai->jabatan }}
                        </span>
                        @endif
                        @if($pegawai->eselon)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                                     bg-white/15 backdrop-blur-sm border border-white/25 text-white text-xs font-semibold">
                            {{ $pegawai->eselon }}
                        </span>
                        @endif
                        @if($pegawai->bagian)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl
                                     bg-white/15 backdrop-blur-sm border border-white/25 text-white text-xs font-semibold">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $pegawai->bagian }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Quick Metrics --}}
                <div class="flex flex-row sm:flex-col gap-2 flex-shrink-0 w-full sm:w-auto">
                    @php
                    $metrics = [
                        ['val' => $pegawai->usia ?? '—',                            'lbl' => 'Usia', 'unit' => 'tahun'],
                        ['val' => $pegawai->masa_kerja_tahun ?? '—',                'lbl' => 'Masa Kerja', 'unit' => 'tahun'],
                        ['val' => $pegawai->grading ? 'G'.$pegawai->grading : '—', 'lbl' => 'Grading', 'unit' => ''],
                    ];
                    @endphp
                    @foreach($metrics as $m)
                    <div class="flex-1 sm:flex-none bg-white/15 backdrop-blur-sm
                                border border-white/20 rounded-2xl px-4 py-3 text-center min-w-0 sm:min-w-[80px]">
                        <p class="text-2xl font-black text-white tabular-nums">{{ $m['val'] }}</p>
                        <p class="text-xs {{ $statusConfig['light'] }} mt-0.5 font-medium">{{ $m['lbl'] }}</p>
                        @if($m['unit'])
                        <p class="text-xs {{ $statusConfig['light'] }} opacity-70">{{ $m['unit'] }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Status Banner ── --}}
    <div class="flex items-start gap-4 p-5 rounded-2xl border-2
                {{ $statusConfig['bg'] }} transition-all">
        <div class="w-12 h-12 {{ $statusConfig['icon_bg'] }} rounded-xl
                    flex items-center justify-center flex-shrink-0 shadow-sm">
            <svg class="w-6 h-6 {{ $statusConfig['icon_c'] }}" fill="none"
                 stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $statusConfig['icon'] }}"/>
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="text-base font-bold {{ $statusConfig['title_c'] }}">{{ $statusConfig['label'] }}</h3>
            <p class="text-sm {{ $statusConfig['desc_c'] }} mt-1">{{ $statusConfig['desc'] }}</p>
        </div>
        <div class="flex-shrink-0 text-right">
            <p class="text-xs {{ $statusConfig['desc_c'] }} font-medium">Skor Prioritas</p>
            <p class="text-3xl font-black {{ $statusConfig['title_c'] }} tabular-nums">{{ $skor }}</p>
        </div>
    </div>

    {{-- ── Metrics Row ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Masa Jabatan --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-navy-800
                    border border-gray-100 dark:border-navy-700 shadow-sm p-5
                    hover:shadow-md hover:border-navy-200 dark:hover:border-navy-600 transition-all duration-200">
            <div class="absolute top-0 inset-x-0 h-0.5 bg-gradient-to-r from-navy-400 to-navy-600"></div>

            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-navy-50 dark:bg-navy-700
                            flex items-center justify-center">
                    <svg class="w-4 h-4 text-navy-600 dark:text-navy-400"
                         fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                    Masa Jabatan
                </h4>
            </div>

            @if($lama !== null)
            @php
                $thn = floor($lama / 12);
                $bln = $lama % 12;
                $barC = $prog >= 100 ? 'from-rose-400 to-red-500'
                      : ($prog >= 75  ? 'from-orange-400 to-amber-500'
                      : 'from-navy-400 to-navy-600');
            @endphp
            <div class="text-center mb-4">
                <span class="text-5xl font-black text-gray-900 dark:text-white tabular-nums">{{ $lama }}</span>
                <span class="text-lg font-semibold text-gray-400 dark:text-gray-500 ml-1">bln</span>
            </div>
            <p class="text-xs text-center text-gray-500 dark:text-gray-400 mb-3">
                {{ $thn }} tahun {{ $bln }} bulan dari 24 bulan
            </p>
            <div class="relative h-2 bg-gray-100 dark:bg-navy-700 rounded-full overflow-hidden">
                <div class="absolute inset-y-0 left-0 rounded-full
                            bg-gradient-to-r {{ $barC }} transition-all duration-700"
                     style="width: {{ min(100, $prog) }}%"></div>
            </div>
            <div class="flex justify-between mt-1.5">
                <span class="text-xs text-gray-400">0</span>
                <span class="text-xs font-bold {{ $prog >= 100 ? 'text-rose-500' : 'text-navy-600 dark:text-navy-400' }}">
                    {{ min(100, $prog) }}%
                </span>
                <span class="text-xs text-gray-400">24 bln</span>
            </div>
            @else
            <div class="text-center py-4">
                <p class="text-4xl font-black text-gray-200 dark:text-gray-700">—</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Data TMT jabatan tidak tersedia</p>
                <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}"
                   class="mt-3 inline-flex items-center gap-1 text-xs font-semibold
                          text-navy-600 dark:text-navy-400 underline underline-offset-2">
                    Lengkapi data
                </a>
            </div>
            @endif
        </div>

        {{-- Rekomendasi Waktu --}}
        <div class="group relative overflow-hidden rounded-2xl
                    bg-gradient-to-br from-gold-50 to-amber-50
                    dark:from-navy-800 dark:to-navy-800/80
                    border border-gold-200 dark:border-navy-700 shadow-sm p-5
                    hover:shadow-md transition-all duration-200">
            <div class="absolute top-0 inset-x-0 h-0.5 bg-gradient-to-r from-gold-400 to-amber-500"></div>

            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gold-100 dark:bg-gold-900/20
                            flex items-center justify-center">
                    <svg class="w-4 h-4 text-gold-600 dark:text-gold-400"
                         fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                    Rekomendasi Waktu
                </h4>
            </div>

            <div class="text-center py-3">
                <p class="text-2xl font-black text-gold-700 dark:text-gold-400 leading-tight">
                    {{ $analisis['rekomendasi_waktu'] ?? 'Belum Ditentukan' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Waktu mutasi yang direkomendasikan
                </p>
                <div class="mt-3 flex items-center justify-center gap-1.5 text-xs text-gold-600 dark:text-gold-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    April atau Oktober setiap tahun
                </div>
            </div>
        </div>

        {{-- Sisa Pensiun --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-navy-800
                    border border-gray-100 dark:border-navy-700 shadow-sm p-5
                    hover:shadow-md transition-all duration-200">
            @if($pegawai->tanggal_pensiun)
            @php
                $bulanPensiun = (int) now()->diffInMonths($pegawai->tanggal_pensiun, false);
                $thnP = floor(max(0,$bulanPensiun) / 12);
                $blnP = max(0,$bulanPensiun) % 12;
                $pensiunGrad = $bulanPensiun <= 12 ? 'from-rose-400 to-red-500'
                             : ($bulanPensiun <= 24 ? 'from-orange-400 to-amber-500'
                             : 'from-emerald-400 to-teal-500');
                $pensiunTopLine = $bulanPensiun <= 12 ? 'from-rose-400 to-red-500'
                                : ($bulanPensiun <= 24 ? 'from-orange-400 to-amber-500'
                                : 'from-emerald-400 to-teal-500');
            @endphp
            <div class="absolute top-0 inset-x-0 h-0.5 bg-gradient-to-r {{ $pensiunTopLine }}"></div>
            @else
            <div class="absolute top-0 inset-x-0 h-0.5 bg-gradient-to-r from-gray-200 to-gray-300 dark:from-navy-600 dark:to-navy-600"></div>
            @endif

            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-navy-700
                            flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                         fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                    Sisa Waktu Pensiun
                </h4>
            </div>

            @if($pegawai->tanggal_pensiun)
            <div class="text-center">
                <span class="text-5xl font-black tabular-nums
                             {{ $bulanPensiun <= 12 ? 'text-rose-600 dark:text-rose-400'
                              : ($bulanPensiun <= 24 ? 'text-orange-500 dark:text-orange-400'
                              : 'text-emerald-600 dark:text-emerald-400') }}">
                    {{ max(0, $bulanPensiun) }}
                </span>
                <span class="text-lg font-semibold text-gray-400 ml-1">bln</span>
            </div>
            <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-1">
                {{ $thnP }} thn {{ $blnP }} bln lagi
            </p>
            <div class="mt-3 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-navy-700/50
                        border border-gray-100 dark:border-navy-600 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $pegawai->tanggal_pensiun->translatedFormat('d F Y') }}
                </p>
            </div>
            @else
            <div class="text-center py-4">
                <p class="text-4xl font-black text-gray-200 dark:text-gray-700">—</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Data pensiun tidak tersedia</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Two Column Layout ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Pertimbangan (3/5) --}}
        @if($analisis['perlu_mutasi'] && count($analisis['alasan']) > 0)
        <div class="lg:col-span-3 rounded-2xl bg-white dark:bg-navy-800
                    border border-gray-100 dark:border-navy-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700
                        bg-gray-50/80 dark:bg-navy-750">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-4 rounded-full bg-gradient-to-b from-orange-400 to-orange-600"></div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pertimbangan Mutasi</h3>
                    <span class="ml-auto px-2 py-0.5 rounded-full text-xs font-bold
                                 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400">
                        {{ count($analisis['alasan']) }} faktor
                    </span>
                </div>
            </div>
            <div class="p-5 space-y-3">
                @foreach($analisis['alasan'] as $i => $alasan)
                <div class="flex items-start gap-3 p-4 rounded-xl
                            bg-gradient-to-r from-orange-50 to-amber-50/50
                            dark:from-orange-900/10 dark:to-amber-900/5
                            border border-orange-100 dark:border-orange-900/20">
                    <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-900/30
                                flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-black text-orange-700 dark:text-orange-400">
                            {{ $i + 1 }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed pt-0.5">
                        {{ $alasan }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Info Kepegawaian (2/5) --}}
        <div class="{{ $analisis['perlu_mutasi'] && count($analisis['alasan']) > 0 ? 'lg:col-span-2' : 'lg:col-span-5' }}
                    rounded-2xl bg-white dark:bg-navy-800
                    border border-gray-100 dark:border-navy-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700
                        bg-gray-50/80 dark:bg-navy-750">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-4 rounded-full bg-gradient-to-b from-navy-400 to-navy-600"></div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Informasi Kepegawaian</h3>
                </div>
            </div>
            <div class="p-5">
                @php
                $infoRows = [
                    ['label' => 'NIP',           'value' => $pegawai->nip,             'mono' => true],
                    ['label' => 'Pangkat',        'value' => $pegawai->pangkat],
                    ['label' => 'Pendidikan',     'value' => $pegawai->pendidikan],
                    ['label' => 'Jenis Pegawai',  'value' => $pegawai->jenis_pegawai],
                    ['label' => 'Eselon',         'value' => $pegawai->eselon],
                    ['label' => 'Grading',        'value' => $pegawai->grading ? 'Grade '.$pegawai->grading : null],
                    ['label' => 'TMT Jabatan',    'value' => $pegawai->tmt_jabatan
                        ? $pegawai->tmt_jabatan->translatedFormat('d F Y') : null],
                    ['label' => 'Tgl Pensiun',    'value' => $pegawai->tanggal_pensiun
                        ? $pegawai->tanggal_pensiun->translatedFormat('d F Y') : null],
                    ['label' => 'Masa Kerja',     'value' => ($pegawai->masa_kerja_tahun ?? 0)
                        .' Thn '.($pegawai->masa_kerja_bulan ?? 0).' Bln'],
                ];
                @endphp
                <dl class="space-y-3">
                    @foreach($infoRows as $row)
                    @if(!empty($row['value']))
                    <div class="flex items-start justify-between gap-3 py-2.5
                                border-b border-gray-50 dark:border-navy-700/50 last:border-0">
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase
                                   tracking-wide flex-shrink-0">
                            {{ $row['label'] }}
                        </dt>
                        <dd class="text-xs font-semibold text-gray-900 dark:text-white text-right
                                   {{ ($row['mono'] ?? false) ? 'font-mono' : '' }}">
                            {{ $row['value'] }}
                        </dd>
                    </div>
                    @endif
                    @endforeach
                </dl>
                <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}"
                   class="mt-4 flex items-center justify-center gap-2 w-full px-4 py-2.5
                          rounded-xl border-2 border-navy-200 dark:border-navy-600
                          text-navy-600 dark:text-navy-400 text-xs font-semibold
                          hover:bg-navy-50 dark:hover:bg-navy-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    Profil Lengkap
                </a>
            </div>
        </div>
    </div>

    {{-- ── Rekan Sebagian ── --}}
    @if(isset($rekanSebagian) && $rekanSebagian->isNotEmpty())
    <div class="rounded-2xl bg-white dark:bg-navy-800
                border border-gray-100 dark:border-navy-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50/80 dark:bg-navy-750">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-4 rounded-full bg-gradient-to-b from-purple-400 to-purple-600"></div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                    Rekan di {{ $pegawai->bagian }}
                </h3>
                <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">
                    {{ $rekanSebagian->count() }} pegawai
                </span>
            </div>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-navy-700/50">
            @foreach($rekanSebagian as $rekan)
            @php
                $rSkor = $rekan->analisis_mutasi['prioritas'];
                [$rBadge, $rLabel, $rDot] = match(true) {
                    $rekan->analisis_mutasi['perlu_mutasi'] && $rSkor >= 5
                        => ['badge-danger',  'Tinggi', 'bg-rose-400'],
                    $rekan->analisis_mutasi['perlu_mutasi'] && $rSkor >= 3
                        => ['badge-warning', 'Sedang', 'bg-orange-400'],
                    $rekan->analisis_mutasi['perlu_mutasi']
                        => ['badge-yellow',  'Rendah', 'bg-amber-400'],
                    default
                        => ['badge-success', 'Aman',   'bg-emerald-400'],
                };
            @endphp
            <div class="flex items-center justify-between px-5 py-3.5 group
                        hover:bg-gray-50/80 dark:hover:bg-navy-700/30 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative flex-shrink-0">
                        <div class="w-9 h-9 bg-gradient-to-br from-navy-400 to-navy-600 rounded-xl
                                    flex items-center justify-center shadow-sm">
                            <span class="text-xs font-black text-white uppercase">
                                {{ get_initials($rekan->nama) }}
                            </span>
                        </div>
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full {{ $rDot }}
                                     border-2 border-white dark:border-navy-800"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[140px]">
                            {{ $rekan->nama }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ $rekan->jabatan ?? '—' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="{{ $rBadge }} text-xs">{{ $rLabel }}</span>
                    <a href="{{ route('kepegawaian.mutasi.show', $rekan) }}"
                       class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-navy-700 flex items-center justify-center
                              text-gray-500 dark:text-gray-400 hover:bg-navy-100 dark:hover:bg-navy-600
                              hover:text-navy-600 dark:hover:text-navy-300 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Note ── --}}
    <div class="flex items-start gap-4 p-5 rounded-2xl
                bg-navy-50 dark:bg-navy-800/50
                border border-navy-100 dark:border-navy-700">
        <div class="w-9 h-9 rounded-xl bg-navy-100 dark:bg-navy-700
                    flex items-center justify-center flex-shrink-0">
            <svg class="w-4.5 h-4.5 text-navy-600 dark:text-navy-400"
                 fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-bold text-navy-800 dark:text-navy-200 mb-2">Catatan Penting</h4>
            <ul class="space-y-1.5 text-xs text-navy-700 dark:text-navy-300 leading-relaxed">
                <li class="flex items-start gap-2">
                    <span class="w-1 h-1 rounded-full bg-navy-400 mt-1.5 flex-shrink-0"></span>
                    Proyeksi ini bersifat rekomendasi dan memerlukan persetujuan pejabat berwenang.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1 h-1 rounded-full bg-navy-400 mt-1.5 flex-shrink-0"></span>
                    Waktu mutasi dapat berubah sesuai kebutuhan organisasi dan kebijakan yang berlaku.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1 h-1 rounded-full bg-navy-400 mt-1.5 flex-shrink-0"></span>
                    Pastikan field <strong>TMT Jabatan</strong> terisi agar analisis masa jabatan akurat.
                </li>
            </ul>
        </div>
    </div>

</div>
@endsection
