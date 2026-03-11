@extends('layouts.app')

@section('title', 'Rekap Anggaran')
@section('subtitle', 'Summary serapan anggaran per RO dan realisasi bulanan')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 text-sm">
        <li><a href="{{ route('anggaran.data.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-navy-600 dark:hover:text-navy-400 transition-colors">Kelola Data Anggaran</a></li>
        <li class="flex items-center">
            <svg class="w-4 h-4 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-700 dark:text-gray-300">Rekap</span>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Actions --}}
    <div class="flex flex-wrap gap-2 justify-end">
        <a href="{{ route('anggaran.data.export') }}" class="btn btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
        <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Kelola Data
        </a>
    </div>

    {{-- Total Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $totalPersen = $totals->total_pagu > 0
                ? round(($totals->total_realisasi / $totals->total_pagu) * 100, 1) : 0;
        @endphp

        <div class="card bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/10 border-blue-200 dark:border-blue-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">Total Pagu</p>
                    <p class="text-xl font-bold text-blue-900 dark:text-blue-300">{{ format_rupiah($totals->total_pagu) }}</p>
                </div>
                <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-br from-green-50 to-green-100/50 dark:from-green-900/20 dark:to-green-800/10 border-green-200 dark:border-green-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">Total Realisasi</p>
                    <p class="text-xl font-bold text-green-900 dark:text-green-300">{{ format_rupiah($totals->total_realisasi) }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-medium">{{ $totalPersen }}% terserap</p>
                </div>
                <div class="w-9 h-9 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-br from-orange-50 to-orange-100/50 dark:from-orange-900/20 dark:to-orange-800/10 border-orange-200 dark:border-orange-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-orange-600 dark:text-orange-400 mb-1">Outstanding</p>
                    <p class="text-xl font-bold text-orange-900 dark:text-orange-300">{{ format_rupiah($totals->total_outstanding) }}</p>
                </div>
                <div class="w-9 h-9 bg-orange-100 dark:bg-orange-900/40 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-br from-purple-50 to-purple-100/50 dark:from-purple-900/20 dark:to-purple-800/10 border-purple-200 dark:border-purple-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-purple-600 dark:text-purple-400 mb-1">Sisa Anggaran</p>
                    <p class="text-xl font-bold text-purple-900 dark:text-purple-300">{{ format_rupiah($totals->total_sisa) }}</p>
                </div>
                <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/40 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Global --}}
    <div class="card">
        <div class="flex items-center justify-between mb-3">
            <p class="font-semibold text-gray-900 dark:text-white text-sm">Progress Penyerapan Total</p>
            <span class="font-bold text-lg {{ $totalPersen >= 80 ? 'text-green-600' : ($totalPersen >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                {{ $totalPersen }}%
            </span>
        </div>
        <div class="w-full h-4 bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
            <div class="h-4 rounded-full transition-all duration-700 {{ $totalPersen >= 80 ? 'bg-gradient-to-r from-green-400 to-green-600' : ($totalPersen >= 50 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-red-400 to-red-600') }}"
                 style="width: {{ min($totalPersen, 100) }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-1.5">
            <span>0%</span>
            <span>Realisasi: {{ format_rupiah($totals->total_realisasi) }} dari {{ format_rupiah($totals->total_pagu) }}</span>
            <span>100%</span>
        </div>
    </div>

    {{-- Per RO Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Serapan per RO</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">RO</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Pagu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Realisasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Outstanding</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase w-40">% Serap</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @forelse($summaryRO as $row)
                        @php
                            $statusMap = [
                                'sangat_baik' => ['label' => 'Sangat Baik', 'badge' => 'badge-green'],
                                'baik'        => ['label' => 'Baik',        'badge' => 'badge-blue'],
                                'cukup'       => ['label' => 'Cukup',       'badge' => 'badge-yellow'],
                                'rendah'      => ['label' => 'Rendah',      'badge' => 'badge-red'],
                            ];
                            $s = $statusMap[$row['status']] ?? $statusMap['rendah'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-bold text-gray-900 dark:text-white font-mono">{{ $row['ro'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $row['nama'] }}</p>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ format_rupiah($row['pagu']) }}</td>
                            <td class="px-4 py-3 text-right text-green-600 dark:text-green-400 font-semibold whitespace-nowrap">{{ format_rupiah($row['realisasi']) }}</td>
                            <td class="px-4 py-3 text-right text-orange-600 dark:text-orange-400 whitespace-nowrap">{{ format_rupiah($row['outstanding']) }}</td>
                            <td class="px-4 py-3 text-right text-purple-600 dark:text-purple-400 whitespace-nowrap">{{ format_rupiah($row['sisa']) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
                                        <div class="h-2 rounded-full {{ $row['persen_serap'] >= 80 ? 'bg-green-500' : ($row['persen_serap'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                             style="width: {{ min($row['persen_serap'], 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold w-10 text-right {{ $row['persen_serap'] >= 80 ? 'text-green-600' : ($row['persen_serap'] >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                                        {{ number_format($row['persen_serap'], 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge {{ $s['badge'] }}">{{ $s['label'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                Belum ada data anggaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Realisasi per Bulan --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Realisasi Bulanan (Total Semua RO)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'] as $bln)
                            <th class="px-3 py-2.5 text-center font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ $bln }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-navy-900">
                    <tr>
                        @foreach(['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'] as $bln)
                            @php $val = $realisasiPerBulan->$bln ?? 0; @endphp
                            <td class="px-3 py-3 text-center {{ $val > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-300 dark:text-gray-600' }}">
                                {{ $val > 0 ? format_rupiah($val, '') : '–' }}
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        @php
            $maxBulan = 0;
            foreach(['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'] as $b) {
                $maxBulan = max($maxBulan, $realisasiPerBulan->$b ?? 0);
            }
        @endphp
        @if($maxBulan > 0)
        <div class="px-4 pb-4 pt-2">
            <div class="flex items-end gap-1 h-16">
                @foreach(['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'] as $bln)
                    @php
                        $val  = $realisasiPerBulan->$bln ?? 0;
                        $pct  = $maxBulan > 0 ? ($val / $maxBulan) * 100 : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-0.5" title="{{ ucfirst($bln) }}: {{ format_rupiah($val) }}">
                        <div class="w-full rounded-t-sm transition-all duration-500 {{ $val > 0 ? 'bg-navy-400 dark:bg-navy-500 hover:bg-navy-500 dark:hover:bg-navy-400' : 'bg-gray-100 dark:bg-navy-800' }}"
                             style="height: {{ max($pct, 2) }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
