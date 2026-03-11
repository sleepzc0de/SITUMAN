@extends('layouts.app')
@section('title', 'Detail Data Anggaran')

@section('breadcrumb')
<nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li><a href="{{ route('anggaran.data.index') }}" class="breadcrumb-item">Kelola Data Anggaran</a></li>
        <li><svg class="w-3.5 h-3.5 breadcrumb-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
        <li><span class="breadcrumb-current">Detail</span></li>
    </ol>
</nav>
@endsection

@section('content')
@php
    $isRO          = !$data->kode_akun && !$data->kode_subkomponen;
    $isSubkomponen = !$data->kode_akun && $data->kode_subkomponen;
    $isAkun        = (bool) $data->kode_akun;

    if ($isRO) {
        $levelLabel  = 'RO (Parent)';
        $levelBadge  = 'badge-blue';
        $levelBorder = 'border-l-blue-500';
        $levelIcon   = 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10';
    } elseif ($isSubkomponen) {
        $levelLabel  = 'Sub Komponen';
        $levelBadge  = 'badge-purple';
        $levelBorder = 'border-l-purple-500';
        $levelIcon   = 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z';
    } else {
        $levelLabel  = 'Akun (Detail)';
        $levelBadge  = 'badge-green';
        $levelBorder = 'border-l-green-500';
        $levelIcon   = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
    }

    $persen       = $data->pagu_anggaran > 0
        ? round(($data->total_penyerapan / $data->pagu_anggaran) * 100, 1) : 0;
    $persenSisa   = $data->pagu_anggaran > 0
        ? round(($data->sisa / $data->pagu_anggaran) * 100, 1) : 0;
    $barColor     = progress_bar_color($persen);
    $pctColor     = percentage_text_class($persen);
    $sisaInfo     = sisa_anggaran_info($data->pagu_anggaran, $data->sisa);
    $statusInfo   = anggaran_status($data->pagu_anggaran, $data->total_penyerapan);

    $bulanList    = ['januari','februari','maret','april','mei','juni',
                     'juli','agustus','september','oktober','november','desember'];
    $bulanShort   = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
    $maxBulan     = collect($bulanList)->map(fn($b) => (float)($data->$b ?? 0))->max() ?: 1;
@endphp

<div class="space-y-5">

    {{-- ===== HEADER CARD ===== --}}
    <div class="card-flat border-l-4 {{ $levelBorder }} p-5">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            {{-- Info kiri --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="badge {{ $levelBadge }} gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $levelIcon }}"/>
                        </svg>
                        {{ $levelLabel }}
                    </span>
                    <span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                    <span class="font-mono text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-navy-700 px-2 py-0.5 rounded-md">
                        {{ $data->ro }}
                        @if($data->kode_subkomponen) / {{ $data->kode_subkomponen }} @endif
                        @if($data->kode_akun) / {{ $data->kode_akun }} @endif
                    </span>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">
                    {{ $data->program_kegiatan }}
                </h2>
                <div class="flex items-center gap-1.5 mt-1.5">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $data->pic }}</span>
                </div>
            </div>
            {{-- Aksi kanan --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('anggaran.data.edit', $data) }}" class="btn btn-secondary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-ghost btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- ===== SUMMARY CARDS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Pagu --}}
        <div class="card-flat p-4 border-l-4 border-l-navy-500">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pagu</p>
                <div class="w-7 h-7 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-navy-600 dark:text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white leading-tight" title="{{ number_format($data->pagu_anggaran) }}">
                {{ format_rupiah_short($data->pagu_anggaran) }}
            </p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ format_rupiah($data->pagu_anggaran) }}</p>
        </div>

        {{-- Realisasi --}}
        <div class="card-flat p-4 border-l-4 border-l-green-500">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Realisasi</p>
                <div class="w-7 h-7 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-lg font-bold text-green-600 dark:text-green-400 leading-tight">
                {{ format_rupiah_short($data->total_penyerapan) }}
            </p>
            <p class="mt-1 text-xs font-semibold {{ $pctColor }}">{{ $persen }}% terserap</p>
        </div>

        {{-- Outstanding --}}
        <div class="card-flat p-4 border-l-4 border-l-amber-500">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Outstanding</p>
                <div class="w-7 h-7 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-lg font-bold text-amber-600 dark:text-amber-400 leading-tight">
                {{ format_rupiah_short($data->tagihan_outstanding) }}
            </p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tagihan belum SP2D</p>
        </div>

        {{-- Sisa --}}
        <div class="card-flat p-4 border-l-4 {{ $data->sisa < ($data->pagu_anggaran * 0.2) ? 'border-l-red-500' : 'border-l-navy-400' }}">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sisa</p>
                <div class="w-7 h-7 {{ $data->sisa < ($data->pagu_anggaran * 0.2) ? 'bg-red-100 dark:bg-red-900/30' : 'bg-navy-100 dark:bg-navy-700' }} rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 {{ $data->sisa < ($data->pagu_anggaran * 0.2) ? 'text-red-600 dark:text-red-400' : 'text-navy-600 dark:text-navy-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-lg font-bold {{ $sisaInfo['class'] }} leading-tight">
                {{ format_rupiah_short($data->sisa) }}
            </p>
            @if($sisaInfo['warning'])
                <p class="mt-1 text-xs {{ $sisaInfo['class'] }}">{{ $sisaInfo['warning'] }}</p>
            @else
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $persenSisa }}% dari pagu</p>
            @endif
        </div>

    </div>

    {{-- ===== PROGRESS PENYERAPAN ===== --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Progress Penyerapan</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    Realisasi {{ format_rupiah($data->total_penyerapan) }}
                    dari pagu {{ format_rupiah($data->pagu_anggaran) }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-2xl font-bold {{ $pctColor }}">{{ $persen }}%</span>
                <p class="text-xs text-gray-400 dark:text-gray-500">terserap</p>
            </div>
        </div>
        <div class="w-full h-3 bg-gray-100 dark:bg-navy-700 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700 {{ $barColor }}"
                 style="width: {{ min($persen, 100) }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mt-1.5">
            <span>Rp 0</span>
            <span>{{ format_rupiah($data->pagu_anggaran) }}</span>
        </div>

        {{-- Outstanding stack --}}
        @if($data->tagihan_outstanding > 0)
        @php
            $persenOutstanding = $data->pagu_anggaran > 0
                ? min(round(($data->tagihan_outstanding / $data->pagu_anggaran) * 100, 1), 100 - $persen)
                : 0;
        @endphp
        <div class="mt-3 flex items-center gap-2 text-xs text-amber-600 dark:text-amber-400">
            <div class="w-3 h-3 rounded-sm bg-amber-400 flex-shrink-0"></div>
            <span>Outstanding {{ format_rupiah($data->tagihan_outstanding) }} ({{ $persenOutstanding }}%)</span>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- ===== INFO DETAIL ===== --}}
        <div class="card">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">
                <div class="w-7 h-7 rounded-lg bg-navy-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Informasi Detail</h3>
            </div>
            <dl class="space-y-2.5">
                @php
                    $infoRows = [
                        ['label' => 'Kode Kegiatan', 'value' => $data->kegiatan,                         'mono' => true],
                        ['label' => 'KRO',            'value' => $data->kro,                              'mono' => true],
                        ['label' => 'RO',             'value' => $data->ro . ' – ' . get_ro_name($data->ro), 'mono' => false],
                        ['label' => 'PIC',            'value' => $data->pic,                              'mono' => false],
                        ['label' => 'Referensi',      'value' => $data->referensi,                        'mono' => true],
                    ];
                    if ($data->kode_subkomponen) {
                        $infoRows[] = ['label' => 'Sub Komponen', 'value' => $data->kode_subkomponen, 'mono' => true];
                    }
                    if ($data->kode_akun) {
                        $infoRows[] = ['label' => 'Kode Akun', 'value' => $data->kode_akun, 'mono' => true];
                    }
                @endphp
                @foreach($infoRows as $row)
                <div class="flex items-start justify-between py-1.5 border-b border-gray-50 dark:border-navy-700/50 last:border-0">
                    <dt class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 w-32">{{ $row['label'] }}</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white text-right ml-2 {{ $row['mono'] ? 'font-mono' : '' }}">
                        {{ $row['value'] }}
                    </dd>
                </div>
                @endforeach
            </dl>
        </div>

        {{-- ===== REALISASI PER BULAN (chart mini) ===== --}}
        <div class="card">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">
                <div class="w-7 h-7 rounded-lg bg-navy-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Realisasi per Bulan</h3>
            </div>
            {{-- Bar chart mini --}}
            <div class="flex items-end gap-1 h-20 mb-2">
                @foreach($bulanList as $i => $bln)
                    @php
                        $val    = (float)($data->$bln ?? 0);
                        $height = $maxBulan > 0 ? round(($val / $maxBulan) * 100) : 0;
                        $isNow  = (int)date('n') === ($i + 1);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group" title="{{ $bulanShort[$i] }}: {{ format_rupiah($val) }}">
                        <div class="w-full rounded-t transition-all duration-300
                            {{ $val > 0 ? 'bg-navy-500 dark:bg-navy-400 group-hover:bg-navy-600' : 'bg-gray-100 dark:bg-navy-700' }}
                            {{ $isNow ? 'ring-1 ring-gold-400' : '' }}"
                             style="height: {{ max($height, $val > 0 ? 8 : 2) }}%"></div>
                    </div>
                @endforeach
            </div>
            {{-- Label bulan --}}
            <div class="flex gap-1">
                @foreach($bulanShort as $i => $bln)
                    @php $isNow = (int)date('n') === ($i + 1); @endphp
                    <div class="flex-1 text-center text-[9px] {{ $isNow ? 'text-navy-600 dark:text-navy-400 font-bold' : 'text-gray-400 dark:text-gray-600' }}">
                        {{ $bln }}
                    </div>
                @endforeach
            </div>
            {{-- Total kumulatif --}}
            @php
                $totalBulan = collect($bulanList)->sum(fn($b) => (float)($data->$b ?? 0));
                $bulanAktif = collect($bulanList)->filter(fn($b) => ($data->$b ?? 0) > 0)->count();
            @endphp
            @if($totalBulan > 0)
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>{{ $bulanAktif }} bulan aktif</span>
                <span class="font-semibold text-green-600 dark:text-green-400">Total: {{ format_rupiah($totalBulan) }}</span>
            </div>
            @endif
        </div>

    </div>

    {{-- ===== TABEL REALISASI BULAN (detail) ===== --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Rincian Realisasi Bulanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-navy-800/80">
                    <tr>
                        @foreach($bulanShort as $i => $bln)
                            @php $isNow = (int)date('n') === ($i + 1); @endphp
                            <th class="px-3 py-2.5 text-center font-semibold uppercase tracking-wider
                                {{ $isNow ? 'text-navy-600 dark:text-navy-400 bg-navy-50 dark:bg-navy-700/50' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $bln }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white dark:bg-navy-900/30">
                        @foreach($bulanList as $i => $bln)
                            @php
                                $val   = (float)($data->$bln ?? 0);
                                $isNow = (int)date('n') === ($i + 1);
                            @endphp
                            <td class="px-3 py-3 text-center
                                {{ $isNow ? 'bg-navy-50/60 dark:bg-navy-700/30' : '' }}
                                {{ $val > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-300 dark:text-gray-700' }}">
                                @if($val > 0)
                                    {{ format_rupiah($val, '') }}
                                @else
                                    <span class="text-gray-300 dark:text-gray-700">–</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== CHILD ITEMS ===== --}}
    @if($children && $children->count() > 0)
    <div class="card p-0 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $isRO ? 'Daftar Sub Komponen' : 'Daftar Akun' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $isRO ? 'Sub komponen di bawah RO ini' : 'Akun belanja di bawah sub komponen ini' }}
                </p>
            </div>
            <span class="badge badge-info">{{ $children->count() }} item</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-800/80 border-b border-gray-200 dark:border-navy-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Uraian</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Pagu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Realisasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">Serapan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700/60">
                    @foreach($children as $child)
                        @php
                            $cp       = $child->pagu_anggaran > 0
                                ? round(($child->total_penyerapan / $child->pagu_anggaran) * 100, 1) : 0;
                            $cBar     = progress_bar_color($cp);
                            $cPct     = percentage_text_class($cp);
                            $cSisa    = sisa_anggaran_info($child->pagu_anggaran, $child->sisa);
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-navy-800/60 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-mono font-semibold text-sm
                                    {{ $isRO ? 'text-purple-700 dark:text-purple-400' : 'text-green-700 dark:text-green-400' }}">
                                    {{ $child->kode_akun ?? $child->kode_subkomponen }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-gray-800 dark:text-gray-200 text-sm truncate"
                                   title="{{ $child->program_kegiatan }}">
                                    {{ truncate_text($child->program_kegiatan, 52) }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $child->pic }}</p>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white tabular-nums whitespace-nowrap text-sm">
                                {{ format_rupiah($child->pagu_anggaran) }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap text-sm">
                                <span class="{{ $child->total_penyerapan > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ format_rupiah($child->total_penyerapan) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap text-sm">
                                <span class="{{ $cSisa['class'] }}">{{ format_rupiah($child->sisa) }}</span>
                                @if($cSisa['warning'])
                                    <div class="text-xs {{ $cSisa['class'] }}">{{ $cSisa['warning'] }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-sm font-bold tabular-nums {{ $cPct }}">{{ $cp }}%</span>
                                    <div class="w-16 h-1.5 bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $cBar }}"
                                             style="width: {{ min($cp, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('anggaran.data.show', $child) }}"
                                   class="table-action-view" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                {{-- Footer total --}}
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-navy-800/60 border-t-2 border-gray-200 dark:border-navy-600">
                        <td colspan="2" class="px-4 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">
                            Total
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white tabular-nums whitespace-nowrap text-sm">
                            {{ format_rupiah($children->sum('pagu_anggaran')) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400 tabular-nums whitespace-nowrap text-sm">
                            {{ format_rupiah($children->sum('total_penyerapan')) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold tabular-nums whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ format_rupiah($children->sum('sisa')) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
