@include('dashboard.partials._chart_helpers')

@php
$totalBagian  = isset($chartSebaranBagian)  ? $chartSebaranBagian->sum('total')  : 0;
$totalGrading = isset($chartSebaranGrading) ? $chartSebaranGrading->sum('total') : 0;

$quickActions = [
    ['route' => 'kepegawaian.sebaran',              'label' => 'Sebaran Pegawai',    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'navy'],
    ['route' => 'kepegawaian.grading',              'label' => 'Kenaikan Grading',   'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',                                                                                                                                                                                         'color' => 'gold'],
    ['route' => 'anggaran.monitoring.index',         'label' => 'Monitoring Anggaran','icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1',                                                                                                                                        'color' => 'green'],
    ['route' => 'inventaris.monitoring-atk.index',  'label' => 'Monitoring ATK',     'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',                                                                                                                                                        'color' => 'orange'],
    ['route' => 'users.index',                       'label' => 'Manajemen User',     'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',                                                                                                        'color' => 'purple'],
    ['route' => 'roles.index',                       'label' => 'Kelola Role',        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',                       'color' => 'red'],
    ['route' => 'kepegawaian.pegawai.index',         'label' => 'Kelola Data Pegawai','icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',                                                                                      'color' => 'sky'],
    ['route' => 'permissions.index',                 'label' => 'Kelola Permission',  'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',                                                                                           'color' => 'slate'],
];

$actionColors = [
    'navy'   => 'bg-navy-50   dark:bg-navy-700/60 hover:bg-navy-100   dark:hover:bg-navy-700 text-navy-700   dark:text-navy-200   border-navy-200   dark:border-navy-600',
    'gold'   => 'bg-amber-50  dark:bg-navy-700/60 hover:bg-amber-100  dark:hover:bg-navy-700 text-amber-700  dark:text-amber-300  border-amber-200  dark:border-navy-600',
    'green'  => 'bg-emerald-50 dark:bg-navy-700/60 hover:bg-emerald-100 dark:hover:bg-navy-700 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-navy-600',
    'orange' => 'bg-orange-50 dark:bg-navy-700/60 hover:bg-orange-100 dark:hover:bg-navy-700 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-navy-600',
    'purple' => 'bg-purple-50 dark:bg-navy-700/60 hover:bg-purple-100 dark:hover:bg-navy-700 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-navy-600',
    'red'    => 'bg-red-50    dark:bg-navy-700/60 hover:bg-red-100    dark:hover:bg-navy-700 text-red-700    dark:text-red-300    border-red-200    dark:border-navy-600',
    'sky'    => 'bg-sky-50    dark:bg-navy-700/60 hover:bg-sky-100    dark:hover:bg-navy-700 text-sky-700    dark:text-sky-300    border-sky-200    dark:border-navy-600',
    'slate'  => 'bg-slate-50  dark:bg-navy-700/60 hover:bg-slate-100  dark:hover:bg-navy-700 text-slate-700  dark:text-slate-300  border-slate-200  dark:border-navy-600',
];

$atkItems = [
    ['key' => 'atk_tersedia',        'label' => 'Tersedia',            'dot' => 'bg-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',  'text' => 'text-emerald-700 dark:text-emerald-400'],
    ['key' => 'atk_menipis',         'label' => 'Menipis',             'dot' => 'bg-amber-500',   'bg' => 'bg-amber-50   dark:bg-amber-900/20',    'text' => 'text-amber-700   dark:text-amber-400'],
    ['key' => 'atk_kosong',          'label' => 'Kosong',              'dot' => 'bg-red-500',     'bg' => 'bg-red-50     dark:bg-red-900/20',      'text' => 'text-red-700     dark:text-red-400'],
    ['key' => 'permintaan_pending',  'label' => 'Permintaan Pending',  'dot' => 'bg-orange-500',  'bg' => 'bg-orange-50  dark:bg-orange-900/20',  'text' => 'text-orange-700  dark:text-orange-400'],
];
@endphp

<div class="space-y-6">

    {{-- ── Row 1: Anggaran Bulanan + Distribusi Role ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Anggaran Bulanan --}}
        <div class="chart-card lg:col-span-2">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="chart-title mb-0">Rencana Penarikan per Bulan</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Tahun {{ date('Y') }}</p>
                </div>
                <span class="badge badge-info">{{ date('Y') }}</span>
            </div>
            <div class="h-56"><canvas id="chartAnggaranBulan"></canvas></div>
        </div>

        {{-- Distribusi Role --}}
        <div class="chart-card">
            <h3 class="chart-title">Distribusi Role User</h3>
            <div class="h-36"><canvas id="chartUserRole"></canvas></div>
            @if(isset($userPerRole) && $userPerRole->count())
            <div class="mt-4 space-y-2 pt-3 border-t border-gray-50 dark:border-navy-700/60">
                @foreach($userPerRole as $ur)
                <div class="info-row">
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $ur->role ?? 'Tanpa Role' }}</span>
                    <span class="text-xs font-bold tabular-nums text-gray-900 dark:text-white">{{ $ur->total }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── Row 2: Sebaran Bagian + Grading ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="chart-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="chart-title mb-0">Sebaran Pegawai per Bagian</h3>
                <span class="badge badge-gray">{{ number_format($totalBagian) }} org</span>
            </div>
            <div class="h-64"><canvas id="chartSebaranBagian"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="chart-title mb-0">Distribusi Grading</h3>
                <span class="badge badge-gray">{{ number_format($totalGrading) }} org</span>
            </div>
            <div class="h-64"><canvas id="chartSebaranGrading"></canvas></div>
        </div>
    </div>

    {{-- ── Row 3: Pie Charts ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="chart-card">
            <h3 class="chart-title">Jenis Kelamin</h3>
            <div class="h-52"><canvas id="chartJenisKelamin"></canvas></div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Tingkat Pendidikan</h3>
            <div class="h-52"><canvas id="chartPendidikan"></canvas></div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Distribusi Usia</h3>
            <div class="h-52"><canvas id="chartRangeUsia"></canvas></div>
        </div>
    </div>

    {{-- ── Row 4: ATK Status + Quick Actions ── --}}
    @if(isset($inventarisStats))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ATK Status --}}
        <div class="chart-card">
            <h3 class="chart-title">Status ATK & Inventaris</h3>
            <div class="space-y-2.5">
                @foreach($atkItems as $item)
                <div class="flex items-center justify-between px-3.5 py-3 {{ $item['bg'] }} rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 {{ $item['dot'] }} rounded-full flex-shrink-0"></span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                    </div>
                    <span class="text-sm font-bold {{ $item['text'] }} tabular-nums">
                        {{ $inventarisStats[$item['key']] ?? 0 }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="chart-card lg:col-span-2">
            <h3 class="chart-title">Aksi Cepat</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @foreach($quickActions as $action)
                <a href="{{ route($action['route']) }}"
                   class="group flex flex-col items-center gap-2 p-3.5 rounded-xl border transition-all duration-200
                          {{ $actionColors[$action['color']] }}">
                    <div class="w-9 h-9 rounded-xl bg-white/60 dark:bg-navy-800/60 flex items-center justify-center
                                group-hover:scale-110 transition-transform duration-150 shadow-sm">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-center leading-tight">{{ $action['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Row 5: Sebaran Eselon ── --}}
    <div class="chart-card">
        <h3 class="chart-title">Sebaran Per Eselon</h3>
        <div class="h-48"><canvas id="chartSebaranEselon"></canvas></div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const C  = window.chartConfig;
    const tp = C.tooltip();

    // ── Helper: init chart only if canvas exists ──────────
    function initChart(id, config) {
        const el = document.getElementById(id);
        if (!el) return null;
        return new Chart(el, config);
    }

    // ── Anggaran Bulanan ──────────────────────────────────
    initChart('chartAnggaranBulan', {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
            datasets: [{
                label: 'Rencana Penarikan',
                data: @json($chartAnggaranBulan ?? []),
                backgroundColor: C.navy[0] + 'cc',
                borderRadius: 7,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { ...tp, callbacks: { label: ctx => C.formatIDR(ctx.parsed.y) } },
            },
            scales: C.scales.currency(),
        },
    });

    // ── User Per Role ─────────────────────────────────────
    initChart('chartUserRole', {
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
            responsive: true, maintainAspectRatio: false, cutout: '62%',
            plugins: { legend: { display: false }, tooltip: tp },
        },
    });

    // ── Sebaran Bagian ────────────────────────────────────
    initChart('chartSebaranBagian', {
        type: 'bar',
        data: {
            labels: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('bagian') : []),
            datasets: [{
                data: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('total') : []),
                backgroundColor: C.navy,
                borderRadius: 6, borderSkipped: false,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { ...tp, callbacks: { label: ctx => ctx.parsed.y + ' pegawai' } },
            },
            scales: C.scales.count(),
        },
    });

    // ── Distribusi Grading ────────────────────────────────
    initChart('chartSebaranGrading', {
        type: 'line',
        data: {
            labels: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('grading')->map(fn($g) => 'Grade '.$g) : []),
            datasets: [{
                data: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('total') : []),
                borderColor: C.gold[0],
                backgroundColor: C.gold[0] + '1a',
                borderWidth: 2.5, fill: true, tension: 0.4,
                pointBackgroundColor: C.navy[0],
                pointBorderColor: C.gold[0],
                pointBorderWidth: 2, pointRadius: 5,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { ...tp, callbacks: { label: ctx => ctx.parsed.y + ' pegawai' } },
            },
            scales: C.scales.count(),
        },
    });

    // ── Jenis Kelamin ─────────────────────────────────────
    initChart('chartJenisKelamin', {
        type: 'doughnut',
        data: {
            labels: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('jenis_kelamin') : []),
            datasets: [{
                data: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('total') : []),
                backgroundColor: [C.navy[0], C.gold[0]],
                borderColor: 'transparent', borderWidth: 4, hoverOffset: 8,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '62%',
            plugins: {
                legend: C.legend(),
                tooltip: { ...tp, callbacks: { label: C.pctLabel } },
            },
        },
    });

    // ── Pendidikan ────────────────────────────────────────
    initChart('chartPendidikan', {
        type: 'pie',
        data: {
            labels: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('pendidikan') : []),
            datasets: [{
                data: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('total') : []),
                backgroundColor: C.mixed,
                borderColor: 'transparent', borderWidth: 3,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: C.legend(),
                tooltip: { ...tp, callbacks: { label: C.pctLabel } },
            },
        },
    });

    // ── Range Usia ────────────────────────────────────────
    initChart('chartRangeUsia', {
        type: 'polarArea',
        data: {
            labels: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('range') : []),
            datasets: [{
                data: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('total') : []),
                backgroundColor: C.navy.slice(0,3).concat(C.gold.slice(0,2)).map(c => c + 'b3'),
                borderColor: 'transparent', borderWidth: 2,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: C.legend(),
                tooltip: tp,
            },
            scales: { r: { ticks: { backdropColor: 'transparent', color: '#9ca3af', font: { size: 10 } }, grid: { color: 'rgba(148,163,184,0.1)' } } },
        },
    });

    // ── Eselon ────────────────────────────────────────────
    initChart('chartSebaranEselon', {
        type: 'bar',
        data: {
            labels: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('eselon') : []),
            datasets: [{
                data: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('total') : []),
                backgroundColor: C.gold,
                borderRadius: 6, borderSkipped: false,
            }],
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { ...tp, callbacks: { label: ctx => ctx.parsed.x + ' pegawai' } },
            },
            scales: C.scales.count('y'),
        },
    });
});
</script>
@endpush
