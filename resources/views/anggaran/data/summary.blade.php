@extends('layouts.app')
@section('title', 'Rekap Anggaran')
@section('subtitle', 'Summary serapan anggaran per RO dan realisasi bulanan')

@section('breadcrumb')
<nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li><a href="{{ route('anggaran.data.index') }}" class="breadcrumb-item">Kelola Data Anggaran</a></li>
        <li><svg class="w-3.5 h-3.5 breadcrumb-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
        <li><span class="breadcrumb-current">Rekap</span></li>
    </ol>
</nav>
@endsection

@section('content')
@php
    $totalPersen  = $totals->total_pagu > 0
        ? round(($totals->total_realisasi / $totals->total_pagu) * 100, 1) : 0;
    $barColor     = progress_bar_color($totalPersen);
    $pctColor     = percentage_text_class($totalPersen);

    $bulanList  = ['januari','februari','maret','april','mei','juni',
                   'juli','agustus','september','oktober','november','desember'];
    $bulanShort = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

    $maxBulan = collect($bulanList)
        ->map(fn($b) => (float)($realisasiPerBulan->$b ?? 0))
        ->max() ?: 1;

    $totalBulanan = collect($bulanList)->sum(fn($b) => (float)($realisasiPerBulan->$b ?? 0));
    $bulanAktif   = collect($bulanList)->filter(fn($b) => ($realisasiPerBulan->$b ?? 0) > 0)->count();

    // Status map konsisten dengan helpers
    $statusMap = [
        'Sangat Baik' => 'badge-success',
        'Baik'        => 'badge-info',
        'Cukup'       => 'badge-warning',
        'Rendah'      => 'badge-danger',
        'Belum Serap' => 'badge-gray',
        'Belum Ada Pagu' => 'badge-gray',
    ];
@endphp

<div class="space-y-5">

    {{-- ===== HEADER ACTIONS ===== --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="badge badge-info">Tahun {{ $tahun }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $summaryRO->count() }} RO aktif
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('anggaran.data.export') }}" class="btn btn-success btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Kelola Data
            </a>
        </div>
    </div>

    {{-- ===== SUMMARY CARDS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Pagu --}}
        <div class="card-flat p-4 border-l-4 border-l-navy-500">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pagu</p>
                <div class="w-7 h-7 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-navy-600 dark:text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white leading-tight"
               title="{{ number_format($totals->total_pagu) }}">
                {{ format_rupiah_short($totals->total_pagu) }}
            </p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ format_rupiah($totals->total_pagu) }}</p>
        </div>

        {{-- Realisasi --}}
        <div class="card-flat p-4 border-l-4 border-l-green-500">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Realisasi</p>
                <div class="w-7 h-7 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-green-600 dark:text-green-400 leading-tight">
                {{ format_rupiah_short($totals->total_realisasi) }}
            </p>
            <p class="mt-1 text-xs font-semibold {{ $pctColor }}">{{ $totalPersen }}% terserap</p>
        </div>

        {{-- Outstanding --}}
        <div class="card-flat p-4 border-l-4 border-l-amber-500">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Outstanding</p>
                <div class="w-7 h-7 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-amber-600 dark:text-amber-400 leading-tight">
                {{ format_rupiah_short($totals->total_outstanding) }}
            </p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tagihan belum SP2D</p>
        </div>

        {{-- Sisa --}}
        @php $sisaTotalInfo = sisa_anggaran_info($totals->total_pagu, $totals->total_sisa); @endphp
        <div class="card-flat p-4 border-l-4 {{ $totals->total_sisa < ($totals->total_pagu * 0.2) ? 'border-l-red-500' : 'border-l-navy-400' }}">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sisa</p>
                <div class="w-7 h-7 {{ $totals->total_sisa < ($totals->total_pagu * 0.2) ? 'bg-red-100 dark:bg-red-900/30' : 'bg-navy-100 dark:bg-navy-700' }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 {{ $totals->total_sisa < ($totals->total_pagu * 0.2) ? 'text-red-600 dark:text-red-400' : 'text-navy-600 dark:text-navy-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xl font-bold {{ $sisaTotalInfo['class'] }} leading-tight">
                {{ format_rupiah_short($totals->total_sisa) }}
            </p>
            @if($sisaTotalInfo['warning'])
                <p class="mt-1 text-xs {{ $sisaTotalInfo['class'] }}">{{ $sisaTotalInfo['warning'] }}</p>
            @else
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    {{ $totals->total_pagu > 0 ? round(($totals->total_sisa / $totals->total_pagu) * 100, 1) : 0 }}% dari pagu
                </p>
            @endif
        </div>

    </div>

    {{-- ===== PROGRESS TOTAL ===== --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Progress Penyerapan Total</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    {{ format_rupiah($totals->total_realisasi) }} dari {{ format_rupiah($totals->total_pagu) }}
                </p>
            </div>
            <span class="text-2xl font-bold {{ $pctColor }}">{{ $totalPersen }}%</span>
        </div>
        <div class="w-full h-3 bg-gray-100 dark:bg-navy-700 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700 {{ $barColor }}"
                 style="width: {{ min($totalPersen, 100) }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mt-1.5">
            <span>0%</span>
            <span>100%</span>
        </div>
    </div>

    {{-- ===== TABEL PER RO ===== --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Serapan per RO</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rincian anggaran dan realisasi tiap Rincian Output</p>
            </div>
            <span class="badge badge-info">{{ $summaryRO->count() }} RO</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-800/80 border-b border-gray-200 dark:border-navy-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">RO</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">Pagu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">Realisasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Outstanding</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">Serapan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700/60">
                    @forelse($summaryRO as $row)
                        @php
                            $rPct     = (float) $row['persen_serap'];
                            $rBar     = progress_bar_color($rPct);
                            $rPctClr  = percentage_text_class($rPct);
                            $rStatus  = anggaran_status($row['pagu'], $row['realisasi']);
                            $rSisa    = sisa_anggaran_info($row['pagu'], $row['sisa']);
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-navy-800/60 transition-colors">

                            {{-- RO --}}
                            <td class="px-4 py-3">
                                <p class="font-bold text-navy-700 dark:text-navy-300 font-mono text-sm">{{ $row['ro'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-snug">{{ $row['nama'] }}</p>
                            </td>

                            {{-- Pagu --}}
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                    {{ format_rupiah($row['pagu']) }}
                                </span>
                            </td>

                            {{-- Realisasi --}}
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap">
                                <span class="font-semibold text-green-600 dark:text-green-400 text-sm">
                                    {{ format_rupiah($row['realisasi']) }}
                                </span>
                            </td>

                            {{-- Outstanding --}}
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap">
                                <span class="{{ $row['outstanding'] > 0 ? 'text-amber-600 dark:text-amber-400 font-medium' : 'text-gray-400 dark:text-gray-500' }} text-sm">
                                    {{ format_rupiah($row['outstanding']) }}
                                </span>
                            </td>

                            {{-- Sisa --}}
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap">
                                <span class="{{ $rSisa['class'] }} text-sm">{{ format_rupiah($row['sisa']) }}</span>
                                @if($rSisa['warning'])
                                    <div class="text-xs {{ $rSisa['class'] }}">{{ $rSisa['warning'] }}</div>
                                @endif
                            </td>

                            {{-- Progress --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="text-sm font-bold tabular-nums {{ $rPctClr }}">
                                        {{ number_format($rPct, 1) }}%
                                    </span>
                                    <div class="w-24 h-2 bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $rBar }}"
                                             style="width: {{ min($rPct, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 text-center">
                                <span class="badge {{ $rStatus['class'] }}">{{ $rStatus['label'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state py-16">
                                    <div class="empty-state-icon">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p class="empty-state-title">Belum ada data anggaran</p>
                                    <p class="empty-state-desc">Import atau tambahkan data terlebih dahulu</p>
                                    <a href="{{ route('anggaran.data.create') }}" class="btn btn-primary btn-sm mt-2">Tambah Data</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Footer total --}}
                @if($summaryRO->count() > 0)
                <tfoot>
                    <tr class="bg-navy-50 dark:bg-navy-800/80 border-t-2 border-navy-200 dark:border-navy-600">
                        <td class="px-4 py-3 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">
                            Grand Total
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white tabular-nums whitespace-nowrap text-sm">
                            {{ format_rupiah($totals->total_pagu) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400 tabular-nums whitespace-nowrap text-sm">
                            {{ format_rupiah($totals->total_realisasi) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-amber-600 dark:text-amber-400 tabular-nums whitespace-nowrap text-sm">
                            {{ format_rupiah($totals->total_outstanding) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white tabular-nums whitespace-nowrap text-sm">
                            {{ format_rupiah($totals->total_sisa) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-bold {{ $pctColor }}">{{ $totalPersen }}%</span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ===== REALISASI BULANAN ===== --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Realisasi Bulanan — Semua RO</h3>
                @if($totalBulanan > 0)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $bulanAktif }} bulan aktif · Total: {{ format_rupiah($totalBulanan) }}
                </p>
                @endif
            </div>
        </div>

        {{-- Mini bar chart --}}
        @if($maxBulan > 0)
        <div class="px-5 pt-4 pb-2">
            <div class="flex items-end gap-1.5 h-24">
                @foreach($bulanList as $i => $bln)
                    @php
                        $val    = (float)($realisasiPerBulan->$bln ?? 0);
                        $height = $maxBulan > 0 ? round(($val / $maxBulan) * 100) : 0;
                        $isNow  = (int)date('n') === ($i + 1);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group"
                         title="{{ $bulanShort[$i] }}: {{ format_rupiah($val) }}">
                        <div class="w-full rounded-t transition-all duration-500
                            {{ $val > 0 ? 'bg-navy-500 dark:bg-navy-400 group-hover:bg-navy-600 dark:group-hover:bg-navy-300' : 'bg-gray-100 dark:bg-navy-800' }}
                            {{ $isNow ? 'ring-1 ring-offset-1 ring-gold-400' : '' }}"
                             style="height: {{ max($height, $val > 0 ? 6 : 2) }}%"></div>
                        <span class="text-[9px] {{ $isNow ? 'text-navy-600 dark:text-navy-400 font-bold' : 'text-gray-400 dark:text-gray-600' }}">
                            {{ $bulanShort[$i] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Tabel rincian --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-navy-800/80 border-t border-gray-100 dark:border-navy-700">
                    <tr>
                        @foreach($bulanShort as $i => $bln)
                            @php $isNow = (int)date('n') === ($i + 1); @endphp
                            <th class="px-3 py-2.5 text-center font-semibold uppercase tracking-wider
                                {{ $isNow ? 'text-navy-600 dark:text-navy-400 bg-navy-50 dark:bg-navy-700/40' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $bln }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white dark:bg-navy-900/30">
                        @foreach($bulanList as $i => $bln)
                            @php
                                $val   = (float)($realisasiPerBulan->$bln ?? 0);
                                $isNow = (int)date('n') === ($i + 1);
                            @endphp
                            <td class="px-3 py-3 text-center tabular-nums
                                {{ $isNow ? 'bg-navy-50/60 dark:bg-navy-700/30' : '' }}
                                {{ $val > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-300 dark:text-gray-700' }}">
                                @if($val > 0)
                                    {{ format_rupiah($val, '') }}
                                @else
                                    <span>–</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
                @if($totalBulanan > 0)
                <tfoot>
                    <tr class="bg-navy-50 dark:bg-navy-800/60 border-t border-navy-200 dark:border-navy-600">
                        @foreach($bulanList as $i => $bln)
                            @php
                                $val   = (float)($realisasiPerBulan->$bln ?? 0);
                                $pct   = $totals->total_pagu > 0
                                    ? round(($val / $totals->total_pagu) * 100, 1) : 0;
                                $isNow = (int)date('n') === ($i + 1);
                            @endphp
                            <td class="px-3 py-2 text-center tabular-nums text-[10px]
                                {{ $isNow ? 'bg-navy-50/80 dark:bg-navy-700/30' : '' }}
                                {{ $val > 0 ? 'text-navy-600 dark:text-navy-400 font-semibold' : 'text-gray-300 dark:text-gray-700' }}">
                                {{ $val > 0 ? $pct.'%' : '–' }}
                            </td>
                        @endforeach
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection
