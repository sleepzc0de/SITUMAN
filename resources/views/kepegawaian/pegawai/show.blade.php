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
            <h1 class="page-title mt-1">Detail Pegawai</h1>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Data
            </a>
            <a href="{{ route('kepegawaian.pegawai.index') }}" class="btn-ghost btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Profile Hero --}}
    <div class="bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 rounded-2xl p-6 lg:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-36 translate-x-36 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-gold-500/10 rounded-full translate-y-28 -translate-x-24 pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row gap-6">
            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <div class="w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-navy-400 to-gold-500 rounded-2xl flex items-center justify-center shadow-xl">
                    <span class="text-2xl lg:text-3xl font-bold text-white uppercase">{{ substr($pegawai->nama, 0, 2) }}</span>
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <h2 class="text-xl lg:text-2xl font-bold leading-tight">{{ $pegawai->nama_gelar ?? $pegawai->nama }}</h2>
                <p class="text-navy-300 mt-1 font-mono text-sm tracking-wide">{{ $pegawai->nip }}</p>

                <div class="flex flex-wrap gap-2 mt-3">
                    @if($pegawai->grading)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-gold-500/20 text-gold-300 border border-gold-500/30">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Grade {{ $pegawai->grading }}
                    </span>
                    @endif
                    @foreach([
                        [$pegawai->status,     $pegawai->status=='AKTIF' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-gray-500/20 text-gray-300 border-gray-500/30'],
                        [$pegawai->jenis_kelamin, 'bg-white/10 text-white border-white/20'],
                        [$pegawai->eselon,      'bg-purple-500/20 text-purple-300 border-purple-500/30'],
                        [$pegawai->jenis_pegawai,'bg-blue-500/20 text-blue-300 border-blue-500/30'],
                    ] as [$val, $cls])
                    @if($val)
                    <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $cls }}">{{ $val }}</span>
                    @endif
                    @endforeach
                </div>

                @if($pegawai->jabatan || $pegawai->bagian || $pegawai->lokasi)
                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-1.5">
                    @foreach([
                        [$pegawai->jabatan, 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        [$pegawai->bagian ? $pegawai->bagian.($pegawai->subbagian ? ' · '.$pegawai->subbagian : '') : null, 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        [$pegawai->lokasi, 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                    ] as [$val, $icon])
                    @if($val)
                    <div class="flex items-center gap-1.5 text-sm text-navy-200">
                        <svg class="w-4 h-4 text-navy-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                        {{ $val }}
                    </div>
                    @endif
                    @endforeach
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
    <div x-data="{ tab: 'personal' }">
        <div class="card p-0 rounded-b-none overflow-hidden">
            <div class="px-5 pt-4">
                <nav class="-mb-px flex gap-1 overflow-x-auto scrollbar-none">
                    @php
                    $tabs = [
                        ['id'=>'personal',   'label'=>'Data Personal', 'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['id'=>'jabatan',    'label'=>'Jabatan & Unit','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['id'=>'kepegawaian','label'=>'Kepegawaian',  'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['id'=>'pendidikan', 'label'=>'Pendidikan',   'icon'=>'M12 14l9-5-9-5-9 5 9 5z'],
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

        <div class="card p-0 rounded-t-none overflow-hidden border-t-0">

            {{-- Personal --}}
            <div x-show="tab === 'personal'" class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @php
                    $personal = [
                        ['label'=>'Nama Lengkap',  'value'=>$pegawai->nama],
                        ['label'=>'Nama Gelar',    'value'=>$pegawai->nama_gelar],
                        ['label'=>'NIP',           'value'=>$pegawai->nip, 'mono'=>true],
                        ['label'=>'Jenis Kelamin', 'value'=>$pegawai->jenis_kelamin],
                        ['label'=>'Tanggal Lahir', 'value'=>$pegawai->tanggal_lahir?->translatedFormat('d F Y')],
                        ['label'=>'Usia',          'value'=>$pegawai->usia ? $pegawai->usia.' tahun' : null],
                        ['label'=>'Email Kemenkeu','value'=>$pegawai->email_kemenkeu, 'link'=>'mailto:'],
                        ['label'=>'Email Pribadi', 'value'=>$pegawai->email_pribadi,  'link'=>'mailto:'],
                        ['label'=>'No. HP',        'value'=>$pegawai->no_hp,          'link'=>'tel:'],
                    ];
                    @endphp
                    @foreach($personal as $row)
                    @if(!empty($row['value']))
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white {{ isset($row['mono']) ? 'font-mono' : '' }}">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @php
                    $jabatan = [
                        ['label'=>'Jabatan',        'value'=>$pegawai->jabatan],
                        ['label'=>'Jenis Jabatan',  'value'=>$pegawai->jenis_jabatan],
                        ['label'=>'Nama Jabatan',   'value'=>$pegawai->nama_jabatan, 'span'=>true],
                        ['label'=>'Eselon',         'value'=>$pegawai->eselon],
                        ['label'=>'Jenis Pegawai',  'value'=>$pegawai->jenis_pegawai],
                        ['label'=>'Status',         'value'=>$pegawai->status],
                        ['label'=>'Grading',        'value'=>$pegawai->grading ? 'Grade '.$pegawai->grading : null],
                        ['label'=>'Pangkat/Gol.',   'value'=>$pegawai->pangkat],
                        ['label'=>'Bagian',         'value'=>$pegawai->bagian],
                        ['label'=>'Subbagian',      'value'=>$pegawai->subbagian],
                        ['label'=>'Lokasi',         'value'=>$pegawai->lokasi],
                    ];
                    @endphp
                    @foreach($jabatan as $row)
                    @if(!empty($row['value']))
                    <div class="{{ isset($row['span']) ? 'sm:col-span-2' : '' }}">
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $row['value'] }}</dd>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Kepegawaian --}}
            <div x-show="tab === 'kepegawaian'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @php
                    $kepeg = [
                        ['label'=>'TMT CPNS',       'value'=>$pegawai->tmt_cpns?->translatedFormat('d F Y')],
                        ['label'=>'Masa Kerja',     'value'=>($pegawai->masa_kerja_tahun??0).' tahun '.($pegawai->masa_kerja_bulan??0).' bulan'],
                        ['label'=>'Tgl. Pensiun',   'value'=>$pegawai->tanggal_pensiun?->translatedFormat('d F Y')],
                        ['label'=>'Tahun Pensiun',  'value'=>$pegawai->tahun_pensiun],
                        ['label'=>'Proyeksi KP 1',  'value'=>$pegawai->proyeksi_kp_1],
                        ['label'=>'Proyeksi KP 2',  'value'=>$pegawai->proyeksi_kp_2],
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
                        <dd class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-navy-700/60 rounded-xl p-4 leading-relaxed">{{ $pegawai->keterangan_kp }}</dd>
                    </div>
                    @endif
                </div>

                {{-- Career Progress --}}
                @if($pegawai->tanggal_pensiun)
                @php
                    $pensiunDate    = \Carbon\Carbon::parse($pegawai->tanggal_pensiun);
                    $bulanLagi      = now()->diffInMonths($pensiunDate, false);
                    $masaKerjaBulan = ($pegawai->masa_kerja_tahun??0)*12+($pegawai->masa_kerja_bulan??0);
                    $pct            = min(100, round(($masaKerjaBulan / 480) * 100));
                @endphp
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-navy-700">
                    <p class="section-label mb-3">Progres Karier</p>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 flex-shrink-0">TMT CPNS</span>
                        <div class="flex-1 bg-gray-200 dark:bg-navy-600 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full bg-gradient-to-r from-navy-500 to-gold-500 transition-all duration-700"
                                style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 flex-shrink-0">Pensiun {{ $pensiunDate->format('Y') }}</span>
                    </div>
                    <div class="flex justify-between mt-1.5">
                        <span class="text-xs text-navy-600 dark:text-navy-400 font-medium">{{ $masaKerjaBulan }} bulan dilalui</span>
                        <span class="text-xs {{ $bulanLagi <= 12 ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-500' }}">
                            {{ max(0,$bulanLagi) }} bulan lagi
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Pendidikan --}}
            <div x-show="tab === 'pendidikan'" class="p-6" style="display:none">
                @if($pegawai->pendidikan || $pegawai->jurusan_s1 || $pegawai->jurusan_s2 || $pegawai->jurusan_s3)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach([
                        ['Jenjang Pendidikan', $pegawai->pendidikan, true],
                        ['Jurusan S1', $pegawai->jurusan_s1, false],
                        ['Jurusan S2', $pegawai->jurusan_s2, false],
                        ['Jurusan S3', $pegawai->jurusan_s3, false],
                    ] as [$label, $val, $badge])
                    @if($val)
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1.5">{{ $label }}</dt>
                        <dd>
                            @if($badge)
                            <span class="badge badge-purple text-sm px-3 py-1.5 font-bold">{{ $val }}</span>
                            @else
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $val }}</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="empty-state py-10">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title">Data pendidikan belum diisi</p>
                    <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}" class="text-xs text-navy-600 dark:text-navy-400 hover:underline">Lengkapi data →</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Related Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @php
        $links = [
            ['route'=>'kepegawaian.sebaran.show', 'label'=>'Sebaran Pegawai',  'desc'=>'Distribusi & sebaran',     'bg'=>'bg-navy-100 dark:bg-navy-700', 'ico'=>'text-navy-600 dark:text-navy-300', 'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['route'=>'kepegawaian.grading.show',  'label'=>'Kenaikan Grading', 'desc'=>'Rekomendasi grade',        'bg'=>'bg-gold-100 dark:bg-navy-700', 'ico'=>'text-gold-600 dark:text-gold-400', 'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['route'=>'kepegawaian.mutasi.show',   'label'=>'Proyeksi Mutasi',  'desc'=>'Analisis mutasi pegawai',  'bg'=>'bg-emerald-100 dark:bg-navy-700', 'ico'=>'text-emerald-600 dark:text-emerald-400', 'icon'=>'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        ];
        @endphp
        @foreach($links as $link)
        <a href="{{ route($link['route'], $pegawai) }}"
            class="card hover:-translate-y-0.5 hover:border-navy-200 dark:hover:border-navy-500 cursor-pointer transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 {{ $link['bg'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5 {{ $link['ico'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-navy-700 dark:group-hover:text-gold-400 transition-colors">{{ $link['label'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $link['desc'] }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
