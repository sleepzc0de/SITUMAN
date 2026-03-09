@include('dashboard.partials._chart_helpers')

@php
$totalBagian  = isset($chartSebaranBagian)  ? $chartSebaranBagian->sum('total')  : 0;
$totalGrading = isset($chartSebaranGrading) ? $chartSebaranGrading->sum('total') : 0;

$quickActions = [
    ['route' => 'kepegawaian.sebaran',             'label' => 'Sebaran Pegawai',     'color' => 'navy',
     'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    ['route' => 'kepegawaian.grading',             'label' => 'Kenaikan Grading',    'color' => 'gold',
     'icon'  => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
    ['route' => 'anggaran.monitoring.index',        'label' => 'Monitoring Anggaran', 'color' => 'green',
     'icon'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ['route' => 'inventaris.monitoring-atk.index', 'label' => 'Monitoring ATK',      'color' => 'orange',
     'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
    ['route' => 'users.index',                      'label' => 'Manajemen User',      'color' => 'purple',
     'icon'  => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
    ['route' => 'roles.index',                      'label' => 'Kelola Role',         'color' => 'red',
     'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
    ['route' => 'kepegawaian.pegawai.index',        'label' => 'Data Pegawai',        'color' => 'sky',
     'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ['route' => 'permissions.index',                'label' => 'Permission',          'color' => 'slate',
     'icon'  => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
];

$actionColors = [
    'navy'   => 'bg-navy-50   dark:bg-navy-700/50 hover:bg-navy-100   dark:hover:bg-navy-700 text-navy-700   dark:text-navy-200   border border-navy-100   dark:border-navy-600/50',
    'gold'   => 'bg-amber-50  dark:bg-navy-700/50 hover:bg-amber-100  dark:hover:bg-navy-700 text-amber-700  dark:text-amber-300  border border-amber-100  dark:border-navy-600/50',
    'green'  => 'bg-emerald-50 dark:bg-navy-700/50 hover:bg-emerald-100 dark:hover:bg-navy-700 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-navy-600/50',
    'orange' => 'bg-orange-50 dark:bg-navy-700/50 hover:bg-orange-100 dark:hover:bg-navy-700 text-orange-700 dark:text-orange-300 border border-orange-100 dark:border-navy-600/50',
    'purple' => 'bg-purple-50 dark:bg-navy-700/50 hover:bg-purple-100 dark:hover:bg-navy-700 text-purple-700 dark:text-purple-300 border border-purple-100 dark:border-navy-600/50',
    'red'    => 'bg-red-50    dark:bg-navy-700/50 hover:bg-red-100    dark:hover:bg-navy-700 text-red-700    dark:text-red-300    border border-red-100    dark:border-navy-600/50',
    'sky'    => 'bg-sky-50    dark:bg-navy-700/50 hover:bg-sky-100    dark:hover:bg-navy-700 text-sky-700    dark:text-sky-300    border border-sky-100    dark:border-navy-600/50',
    'slate'  => 'bg-slate-50  dark:bg-navy-700/50 hover:bg-slate-100  dark:hover:bg-navy-700 text-slate-700  dark:text-slate-300  border border-slate-100  dark:border-navy-600/50',
];

// SPP per bulan (index 0=Jan … 11=Des)
$sppBulanData = array_fill(0, 12, 0);
if (isset($chartSppBulan)) {
    foreach ($chartSppBulan as $row) {
        $idx = (int)$row->bulan - 1;
        if ($idx >= 0 && $idx < 12) {
            $sppBulanData[$idx] = (float)$row->total;
        }
    }
}

// ATK items + max (untuk progress bar di blade)
$atkItems = [
    ['key' => 'atk_tersedia',       'label' => 'ATK Tersedia',       'dot' => 'bg-emerald-500', 'bar' => 'bg-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-400'],
    ['key' => 'atk_menipis',        'label' => 'ATK Menipis',        'dot' => 'bg-amber-500',   'bar' => 'bg-amber-500',   'text' => 'text-amber-700 dark:text-amber-400'],
    ['key' => 'atk_kosong',         'label' => 'ATK Kosong',         'dot' => 'bg-red-500',     'bar' => 'bg-red-500',     'text' => 'text-red-700 dark:text-red-400'],
    ['key' => 'permintaan_pending', 'label' => 'Permintaan Pending', 'dot' => 'bg-orange-500',  'bar' => 'bg-orange-500',  'text' => 'text-orange-700 dark:text-orange-400'],
];
$atkMax = 1;
if (isset($inventarisStats)) {
    foreach ($atkItems as $itm) {
        $v = (int)($inventarisStats[$itm['key']] ?? 0);
        if ($v > $atkMax) $atkMax = $v;
    }
}

// Pegawai aktif %
$pegawaiPct = $stats['total_pegawai'] > 0
    ? round($stats['pegawai_aktif'] / $stats['total_pegawai'] * 100) : 0;
@endphp

<div class="space-y-6">

    {{-- ══ Row 1: Anggaran Bulanan + Role ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Anggaran Bulanan --}}
        <div class="chart-card lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="chart-title mb-0">Rencana vs Realisasi Anggaran</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Per bulan · Tahun {{ date('Y') }}</p>
                </div>
                <span class="badge badge-info">{{ date('Y') }}</span>
            </div>
            <div class="flex items-center gap-4 mb-3">
                <span class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <span class="w-3 h-2 rounded inline-block bg-navy-500"></span>Rencana
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <span class="w-3 h-2 rounded inline-block bg-emerald-500"></span>Realisasi (SPP)
                </span>
            </div>
            <div class="h-56"><canvas id="chartAnggaranBulan"></canvas></div>
        </div>

        {{-- Distribusi Role --}}
        <div class="chart-card flex flex-col">
            <h3 class="chart-title">Distribusi Role User</h3>
            <div class="h-36"><canvas id="chartUserRole"></canvas></div>
            @if(isset($userPerRole) && $userPerRole->count())
            <div class="mt-3 space-y-1 pt-3 border-t border-gray-50 dark:border-navy-700/60 flex-1">
                @foreach($userPerRole as $ur)
                <div class="info-row">
                    <span class="text-xs text-gray-600 dark:text-gray-400 capitalize">
                        {{ $ur->role ?? 'Tanpa Role' }}
                    </span>
                    <span class="text-xs font-bold tabular-nums text-gray-900 dark:text-white
                                 bg-gray-100 dark:bg-navy-700 px-2 py-0.5 rounded-md">
                        {{ $ur->total }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ══ Row 2: Sebaran Bagian + Grading ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="chart-title mb-0">Sebaran Pegawai per Bagian</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        {{ number_format($totalBagian) }} pegawai aktif
                    </p>
                </div>
                <span class="badge badge-gray">{{ $stats['pegawai_per_bagian']->count() }} unit</span>
            </div>
            <div class="h-64"><canvas id="chartSebaranBagian"></canvas></div>
        </div>

        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="chart-title mb-0">Distribusi Grading Pegawai</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        {{ number_format($totalGrading) }} pegawai tergrading
                    </p>
                </div>
                <span class="badge badge-gray">Aktif</span>
            </div>
            <div class="h-64"><canvas id="chartSebaranGrading"></canvas></div>
        </div>
    </div>

    {{-- ══ Row 3: Demografis 4 Chart ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="chart-card">
            <h3 class="chart-title text-center">Jenis Kelamin</h3>
            <div class="h-44"><canvas id="chartJenisKelamin"></canvas></div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title text-center">Tingkat Pendidikan</h3>
            <div class="h-44"><canvas id="chartPendidikan"></canvas></div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title text-center">Rentang Usia</h3>
            <div class="h-44"><canvas id="chartRangeUsia"></canvas></div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title text-center">Sebaran Eselon</h3>
            <div class="h-44"><canvas id="chartEselonPie"></canvas></div>
        </div>
    </div>

    {{-- ══ Row 4: Anggaran per RO + ATK Status ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        @if(isset($chartAnggaranPerRo) && $chartAnggaranPerRo->count())
        <div class="chart-card lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="chart-title mb-0">Realisasi Anggaran per RO</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        Perbandingan pagu vs penyerapan
                    </p>
                </div>
                <a href="{{ route('anggaran.monitoring.index') }}"
                   class="text-xs font-semibold text-navy-600 dark:text-navy-400 hover:underline">
                    Lihat semua →
                </a>
            </div>
            <div class="h-56"><canvas id="chartAnggaranPerRo"></canvas></div>
        </div>
        @endif

        @if(isset($inventarisStats))
        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="chart-title mb-0">Status Inventaris</h3>
                <a href="{{ route('inventaris.monitoring-atk.index') }}"
                   class="text-xs font-semibold text-navy-600 dark:text-navy-400 hover:underline">
                    Detail →
                </a>
            </div>
            <div class="space-y-3 mb-4">
                @foreach($atkItems as $itm)
                @php $val = (int)($inventarisStats[$itm['key']] ?? 0); @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 {{ $itm['dot'] }} rounded-full flex-shrink-0"></span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $itm['label'] }}</span>
                        </div>
                        <span class="text-xs font-bold {{ $itm['text'] }} tabular-nums">{{ $val }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-navy-700 rounded-full overflow-hidden">
                        <div class="{{ $itm['bar'] }} h-full rounded-full transition-all duration-700"
                             style="width: {{ $atkMax > 0 ? round($val / $atkMax * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="h-28"><canvas id="chartAtkDonut"></canvas></div>
        </div>
        @endif
    </div>

    {{-- ══ Row 5: KPI + Tren Kumulatif ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KPI --}}
        <div class="chart-card">
            <h3 class="chart-title">Ringkasan KPI</h3>
            @if(isset($anggaranStats))
            @php
            $kpis = [
                [
                    'label'     => 'Penyerapan Anggaran',
                    'value'     => number_format($anggaranStats['persentase'], 1).'%',
                    'target'    => '≥ 90%',
                    'ok'        => $anggaranStats['persentase'] >= 80,
                    'bar_pct'   => min((float)$anggaranStats['persentase'], 100),
                    'bar_color' => $anggaranStats['persentase'] >= 80
                                    ? 'bg-emerald-500'
                                    : ($anggaranStats['persentase'] >= 60 ? 'bg-amber-500' : 'bg-red-500'),
                ],
                [
                    'label'     => 'Rasio Aktif Pegawai',
                    'value'     => $pegawaiPct.'%',
                    'target'    => '≥ 95%',
                    'ok'        => $pegawaiPct >= 95,
                    'bar_pct'   => $pegawaiPct,
                    'bar_color' => $pegawaiPct >= 95 ? 'bg-emerald-500' : 'bg-amber-500',
                ],
                [
                    'label'     => 'Stok ATK',
                    'value'     => isset($inventarisStats)
                                    ? (($inventarisStats['atk_menipis'] ?? 0) == 0 &&
                                       ($inventarisStats['atk_kosong']  ?? 0) == 0 ? 'Baik' : 'Perhatian')
                                    : '-',
                    'target'    => 'Stok > min',
                    'ok'        => isset($inventarisStats) && ($inventarisStats['atk_menipis'] ?? 0) == 0,
                    'bar_pct'   => isset($inventarisStats) && ($inventarisStats['total_atk'] ?? 0) > 0
                                    ? round(($inventarisStats['atk_tersedia'] ?? 0) / $inventarisStats['total_atk'] * 100)
                                    : 0,
                    'bar_color' => 'bg-sky-500',
                ],
            ];
            @endphp
            <div class="space-y-5">
                @foreach($kpis as $kpi)
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $kpi['label'] }}</span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">
                                {{ $kpi['value'] }}
                            </span>
                            <span class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $kpi['ok']
                                    ? 'bg-emerald-100 dark:bg-emerald-900/30'
                                    : 'bg-amber-100 dark:bg-amber-900/30' }}">
                                <svg class="w-2.5 h-2.5
                                    {{ $kpi['ok'] ? 'text-emerald-600' : 'text-amber-600' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    @if($kpi['ok'])
                                    <path fill-rule="evenodd"
                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0
                                             01-1.414 0l-4-4a1 1 0 011.414-1.414L8
                                             12.586l7.293-7.293a1 1 0 011.414 0z"
                                          clip-rule="evenodd"/>
                                    @else
                                    <path fill-rule="evenodd"
                                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486
                                             0l5.58 9.92c.75 1.334-.213 2.98-1.742
                                             2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11
                                             13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0
                                             00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                          clip-rule="evenodd"/>
                                    @endif
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-navy-700 rounded-full overflow-hidden">
                        <div class="{{ $kpi['bar_color'] }} h-full rounded-full transition-all duration-700"
                             style="width: {{ $kpi['bar_pct'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                        Target: {{ $kpi['target'] }}
                    </p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Tren Kumulatif --}}
        <div class="chart-card lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="chart-title mb-0">Tren Penyerapan Kumulatif</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        Akumulasi realisasi vs target linear
                    </p>
                </div>
                <span class="badge badge-green">Kumulatif</span>
            </div>
            <div class="h-52"><canvas id="chartKumulatif"></canvas></div>
        </div>
    </div>

    {{-- ══ Row 6: Eselon Bar + Quick Actions ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="chart-card lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="chart-title mb-0">Sebaran per Eselon</h3>
                <span class="badge badge-gray">Horizontal</span>
            </div>
            <div class="h-52"><canvas id="chartSebaranEselon"></canvas></div>
        </div>

        <div class="chart-card lg:col-span-3">
            <h3 class="chart-title">Aksi Cepat</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @foreach($quickActions as $action)
                <a href="{{ route($action['route']) }}"
                   class="group flex flex-col items-center gap-2.5 p-3.5 rounded-xl
                          transition-all duration-200 hover:scale-105 hover:shadow-md
                          {{ $actionColors[$action['color']] }}">
                    <div class="w-10 h-10 rounded-xl bg-white/70 dark:bg-navy-800/70
                                flex items-center justify-center
                                group-hover:shadow-sm transition-all duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="{{ $action['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-semibold text-center leading-tight">
                        {{ $action['label'] }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Helper: buat chart, destroy dulu kalau sudah ada ─────
    function mkChart(id, config) {
        const el = document.getElementById(id);
        if (!el) return null;
        const old = Chart.getChart(el);
        if (old) old.destroy();
        return new Chart(el, config);
    }

    // ── Fungsi utama yang dipanggil setelah chartConfig siap ─
    function initAdminCharts() {
        const C  = window.chartConfig;
        const tp = C.tooltip();

        // ── Data dari PHP ─────────────────────────────────────
        const anggaranBulan = @json(array_values($chartAnggaranBulan ?? []));
        const sppBulan      = @json(array_values($sppBulanData));
        const totalPagu     = {{ (float)($anggaranStats['total_pagu'] ?? 0) }};
        const labels12      = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

        // Kumulatif rencana
        const kumulatif = anggaranBulan.reduce((acc, v, i) => {
            acc.push((acc[i - 1] || 0) + (parseFloat(v) || 0));
            return acc;
        }, []);
        // Target linear bulanan
        const targetLinear = Array.from({ length: 12 }, (_, i) => ((i + 1) / 12) * totalPagu);

        // ── 1. Anggaran Bulanan (Grouped Bar) ─────────────────
        mkChart('chartAnggaranBulan', {
            type: 'bar',
            data: {
                labels: labels12,
                datasets: [
                    {
                        label: 'Rencana',
                        data: anggaranBulan,
                        backgroundColor: C.navy[0] + 'cc',
                        borderRadius: 6,
                        borderSkipped: false,
                        order: 2,
                    },
                    {
                        label: 'Realisasi (SPP)',
                        data: sppBulan,
                        backgroundColor: '#10b981cc',
                        borderRadius: 6,
                        borderSkipped: false,
                        order: 1,
                    },
                ],
            },
            options: {
                animation: { duration: 600 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tp,
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${C.formatIDR(ctx.parsed.y)}`,
                        },
                    },
                },
                scales: C.scales.currency(),
            },
        });

        // ── 2. Tren Kumulatif (Line) ──────────────────────────
        mkChart('chartKumulatif', {
            type: 'line',
            data: {
                labels: labels12,
                datasets: [
                    {
                        label: 'Kumulatif Rencana',
                        data: kumulatif,
                        borderColor: C.navy[0],
                        backgroundColor: C.navy[0] + '18',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: C.navy[0],
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Target Linear',
                        data: targetLinear,
                        borderColor: C.gold[0],
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointRadius: 0,
                        tension: 0,
                    },
                ],
            },
            options: {
                animation: { duration: 600 },
                plugins: {
                    legend: C.legend('bottom'),
                    tooltip: {
                        ...tp,
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${C.formatIDR(ctx.parsed.y)}`,
                        },
                    },
                },
                scales: C.scales.currency(),
            },
        });

        // ── 3. User Per Role (Doughnut) ───────────────────────
        mkChart('chartUserRole', {
            type: 'doughnut',
            data: {
                labels: @json($userPerRole ? $userPerRole->pluck('role') : []),
                datasets: [{
                    data: @json($userPerRole ? $userPerRole->pluck('total') : []),
                    backgroundColor: C.mixed,
                    borderColor: 'transparent',
                    borderWidth: 3,
                    hoverOffset: 8,
                }],
            },
            options: {
                animation: { duration: 600 },
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: tp,
                },
            },
        });

        // ── 4. Sebaran Bagian (Bar) ───────────────────────────
        mkChart('chartSebaranBagian', {
            type: 'bar',
            data: {
                labels: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('bagian') : []),
                datasets: [{
                    data: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('total') : []),
                    backgroundColor: C.navy.map(c => c + 'cc'),
                    borderRadius: 7,
                    borderSkipped: false,
                }],
            },
            options: {
                animation: { duration: 600 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tp,
                        callbacks: { label: ctx => ` ${ctx.parsed.y} pegawai` },
                    },
                },
                scales: C.scales.count(),
            },
        });

        // ── 5. Distribusi Grading (Line) ──────────────────────
        mkChart('chartSebaranGrading', {
            type: 'line',
            data: {
                labels: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('grading')->map(fn($g) => 'Grade '.$g) : []),
                datasets: [{
                    data: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('total') : []),
                    borderColor: C.gold[0],
                    backgroundColor: C.gold[0] + '20',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: C.gold[0],
                    pointBorderWidth: 2.5,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }],
            },
            options: {
                animation: { duration: 600 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tp,
                        callbacks: { label: ctx => ` ${ctx.parsed.y} pegawai` },
                    },
                },
                scales: C.scales.count(),
            },
        });

        // ── 6. Jenis Kelamin (Doughnut) ───────────────────────
        mkChart('chartJenisKelamin', {
            type: 'doughnut',
            data: {
                labels: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('jenis_kelamin') : []),
                datasets: [{
                    data: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('total') : []),
                    backgroundColor: [C.navy[0], C.gold[0]],
                    borderColor: 'transparent',
                    borderWidth: 3,
                    hoverOffset: 8,
                }],
            },
            options: {
                animation: { duration: 600 },
                cutout: '60%',
                plugins: {
                    legend: C.legend(),
                    tooltip: { ...tp, callbacks: { label: C.pctLabel } },
                },
            },
        });

        // ── 7. Pendidikan (Pie) ───────────────────────────────
        mkChart('chartPendidikan', {
            type: 'pie',
            data: {
                labels: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('pendidikan') : []),
                datasets: [{
                    data: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('total') : []),
                    backgroundColor: C.mixed,
                    borderColor: 'transparent',
                    borderWidth: 3,
                }],
            },
            options: {
                animation: { duration: 600 },
                plugins: {
                    legend: C.legend(),
                    tooltip: { ...tp, callbacks: { label: C.pctLabel } },
                },
            },
        });

        // ── 8. Range Usia (PolarArea) ─────────────────────────
        mkChart('chartRangeUsia', {
            type: 'polarArea',
            data: {
                labels: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('range') : []),
                datasets: [{
                    data: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('total') : []),
                    backgroundColor: [...C.navy.slice(0,3), ...C.gold.slice(0,2)].map(c => c + 'b3'),
                    borderColor: 'transparent',
                    borderWidth: 2,
                }],
            },
            options: {
                animation: { duration: 600 },
                plugins: {
                    legend: C.legend(),
                    tooltip: tp,
                },
                scales: {
                    r: {
                        ticks: {
                            backdropColor: 'transparent',
                            color: C.textColor(),
                            font: { size: 9 },
                        },
                        grid: { color: C.gridColor() },
                    },
                },
            },
        });

        // ── 9. Eselon Pie (Doughnut) ──────────────────────────
        mkChart('chartEselonPie', {
            type: 'doughnut',
            data: {
                labels: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('eselon') : []),
                datasets: [{
                    data: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('total') : []),
                    backgroundColor: C.mixed,
                    borderColor: 'transparent',
                    borderWidth: 3,
                    hoverOffset: 6,
                }],
            },
            options: {
                animation: { duration: 600 },
                cutout: '55%',
                plugins: {
                    legend: C.legend(),
                    tooltip: { ...tp, callbacks: { label: C.pctLabel } },
                },
            },
        });

        // ── 10. Eselon Bar (Horizontal) ───────────────────────
        mkChart('chartSebaranEselon', {
            type: 'bar',
            data: {
                labels: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('eselon') : []),
                datasets: [{
                    data: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('total') : []),
                    backgroundColor: C.gold.map(c => c + 'dd'),
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                indexAxis: 'y',
                animation: { duration: 600 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tp,
                        callbacks: { label: ctx => ` ${ctx.parsed.x} pegawai` },
                    },
                },
                scales: C.scales.count('y'),
            },
        });

        // ── 11. ATK Donut ─────────────────────────────────────
        @if(isset($inventarisStats))
        mkChart('chartAtkDonut', {
            type: 'doughnut',
            data: {
                labels: ['Tersedia', 'Menipis', 'Kosong'],
                datasets: [{
                    data: [
                        {{ (int)($inventarisStats['atk_tersedia'] ?? 0) }},
                        {{ (int)($inventarisStats['atk_menipis']  ?? 0) }},
                        {{ (int)($inventarisStats['atk_kosong']   ?? 0) }},
                    ],
                    backgroundColor: ['#10b981cc', '#f59e0bcc', '#ef4444cc'],
                    borderColor: 'transparent',
                    borderWidth: 3,
                    hoverOffset: 6,
                }],
            },
            options: {
                animation: { duration: 600 },
                cutout: '60%',
                plugins: {
                    legend: C.legend('bottom'),
                    tooltip: tp,
                },
            },
        });
        @endif

        // ── 12. Anggaran per RO (Grouped Bar) ────────────────
        @if(isset($chartAnggaranPerRo) && $chartAnggaranPerRo->count())
        mkChart('chartAnggaranPerRo', {
            type: 'bar',
            data: {
                labels: @json($chartAnggaranPerRo->pluck('ro')),
                datasets: [
                    {
                        label: 'Pagu',
                        data: @json($chartAnggaranPerRo->pluck('pagu')),
                        backgroundColor: C.navy[0] + 'cc',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Realisasi',
                        data: @json($chartAnggaranPerRo->pluck('realisasi')),
                        backgroundColor: '#10b981cc',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                animation: { duration: 600 },
                plugins: {
                    legend: C.legend('bottom'),
                    tooltip: {
                        ...tp,
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${C.formatIDR(ctx.parsed.y)}`,
                        },
                    },
                },
                scales: C.scales.currency(),
            },
        });
        @endif
    }

    // ── Tunggu chartConfig siap, lalu init ────────────────────
    if (window.chartConfig) {
        initAdminCharts();
    } else {
        document.addEventListener('chartConfigReady', initAdminCharts, { once: true });
    }

})();
</script>
@endpush
