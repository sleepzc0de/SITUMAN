@include('dashboard.partials._chart_helpers')

@php
$pensiunCount = isset($pegawaiMendekatiPensiun) ? $pegawaiMendekatiPensiun->count() : 0;
$pct = $stats['total_pegawai'] > 0 ? round($stats['pegawai_aktif'] / $stats['total_pegawai'] * 100) : 0;

$statCards = [
    ['label' => 'Total Pegawai',  'value' => number_format($stats['total_pegawai']),       'color_val' => 'text-gray-900 dark:text-white',         'icon_bg' => 'bg-navy-50 dark:bg-navy-700',    'icon_color' => 'text-navy-600 dark:text-navy-300',    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'progress' => $pct, 'sub' => $pct . '% aktif'],
    ['label' => 'Pegawai Aktif',  'value' => number_format($stats['pegawai_aktif']),        'color_val' => 'text-emerald-600 dark:text-emerald-400','icon_bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'icon_color' => 'text-emerald-600 dark:text-emerald-400','icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'progress' => null, 'sub' => null],
    ['label' => 'Unit / Bagian',  'value' => $stats['pegawai_per_bagian']->count(),        'color_val' => 'text-purple-600 dark:text-purple-400',  'icon_bg' => 'bg-purple-50 dark:bg-purple-900/20', 'icon_color' => 'text-purple-600 dark:text-purple-400', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'progress' => null, 'sub' => 'Bagian aktif'],
    ['label' => 'Akan Pensiun',   'value' => $pensiunCount,                                'color_val' => 'text-red-600 dark:text-red-400',        'icon_bg' => 'bg-red-50 dark:bg-red-900/20',   'icon_color' => 'text-red-600 dark:text-red-400',      'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'progress' => null, 'sub' => 'Dalam 2 tahun'],
];
@endphp

<div class="space-y-6">

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statCards as $card)
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
                    <p class="text-3xl font-bold {{ $card['color_val'] }} mt-1 tabular-nums">{{ $card['value'] }}</p>
                    @if($card['sub'])
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">{{ $card['sub'] }}</p>
                    @endif
                </div>
                <div class="stat-icon {{ $card['icon_bg'] }} flex-shrink-0">
                    <svg class="w-5 h-5 {{ $card['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            @if($card['progress'] !== null)
            <div class="mt-3 progress-bar">
                <div class="progress-fill bg-navy-500" style="width: {{ $card['progress'] }}%"></div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ── Charts Row 1 ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="chart-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="chart-title mb-0">Sebaran per Bagian</h3>
                <span class="badge badge-gray">{{ isset($chartSebaranBagian) ? number_format($chartSebaranBagian->sum('total')) : 0 }} org</span>
            </div>
            <div class="h-64"><canvas id="chartBagianPic"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="chart-title mb-0">Distribusi Grading</h3>
                <span class="badge badge-gray">{{ isset($chartSebaranGrading) ? number_format($chartSebaranGrading->sum('total')) : 0 }} org</span>
            </div>
            <div class="h-64"><canvas id="chartGradingPic"></canvas></div>
        </div>
    </div>

    {{-- ── Charts Row 2 ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="chart-card">
            <h3 class="chart-title">Jenis Kelamin</h3>
            <div class="h-52"><canvas id="chartJKPic"></canvas></div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Tingkat Pendidikan</h3>
            <div class="h-52"><canvas id="chartPendidikanPic"></canvas></div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Distribusi Usia</h3>
            <div class="h-52"><canvas id="chartUsiaPic"></canvas></div>
        </div>
    </div>

    {{-- ── Eselon + Pensiun ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="chart-card">
            <h3 class="chart-title">Sebaran per Eselon</h3>
            <div class="h-56"><canvas id="chartEselonPic"></canvas></div>
        </div>

        {{-- Mendekati Pensiun --}}
        <div class="chart-card p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700/80 flex items-center justify-between">
                <h3 class="chart-title mb-0">Pegawai Mendekati Pensiun</h3>
                <span class="badge badge-danger">≤ 2 Tahun</span>
            </div>
            @if($pensiunCount > 0)
            <div class="divide-y divide-gray-50 dark:divide-navy-700/60 max-h-64 overflow-y-auto scrollbar-thin">
                @foreach($pegawaiMendekatiPensiun as $p)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-navy-700/30 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-red-600 dark:text-red-400 uppercase">
                                {{ substr($p->nama, 0, 2) }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $p->nama }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $p->bagian ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-3">
                        <p class="text-xs font-semibold text-red-600 dark:text-red-400 tabular-nums">
                            {{ $p->tanggal_pensiun ? \Carbon\Carbon::parse($p->tanggal_pensiun)->format('d/m/Y') : '-' }}
                        </p>
                        @if($p->tanggal_pensiun)
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ \Carbon\Carbon::parse($p->tanggal_pensiun)->diffForHumans() }}
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada dalam 2 tahun ke depan</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Quick Links ── --}}
    <div class="chart-card">
        <h3 class="chart-title">Akses Cepat Kepegawaian</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @php
            $links = [
                ['route' => 'kepegawaian.sebaran', 'bg' => 'bg-navy-600', 'label' => 'Sebaran Pegawai',  'desc' => 'Lihat distribusi lengkap',
                 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['route' => 'kepegawaian.grading',  'bg' => 'bg-amber-500',  'label' => 'Kenaikan Grading', 'desc' => 'Rekomendasi grading',
                 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                ['route' => 'kepegawaian.mutasi',   'bg' => 'bg-emerald-600','label' => 'Proyeksi Mutasi', 'desc' => 'Analisis mutasi pegawai',
                 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
            ];
            @endphp
            @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-navy-700/50 hover:bg-gray-100 dark:hover:bg-navy-700
                      rounded-xl border border-gray-100 dark:border-navy-600 transition-all duration-200 group">
                <div class="w-10 h-10 {{ $link['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0
                            group-hover:scale-110 transition-transform duration-150 shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white group-hover:text-navy-600 dark:group-hover:text-gold-400 transition-colors">
                        {{ $link['label'] }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $link['desc'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const C  = window.chartConfig;
    const tp = C.tooltip();

    function initChart(id, cfg) {
        const el = document.getElementById(id);
        return el ? new Chart(el, cfg) : null;
    }

    initChart('chartBagianPic', {
        type: 'bar',
        data: {
            labels: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('bagian') : []),
            datasets: [{ data: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('total') : []), backgroundColor: C.navy, borderRadius: 6, borderSkipped: false }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tp, callbacks: { label: ctx => ctx.parsed.y + ' pegawai' } } }, scales: C.scales.count() },
    });

    initChart('chartGradingPic', {
        type: 'line',
        data: {
            labels: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('grading')->map(fn($g) => 'Grade '.$g) : []),
            datasets: [{ data: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('total') : []), borderColor: C.gold[0], backgroundColor: C.gold[0] + '1a', borderWidth: 2.5, fill: true, tension: 0.4, pointBackgroundColor: C.navy[0], pointBorderColor: C.gold[0], pointBorderWidth: 2, pointRadius: 5 }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tp, callbacks: { label: ctx => ctx.parsed.y + ' pegawai' } } }, scales: C.scales.count() },
    });

    initChart('chartJKPic', {
        type: 'doughnut',
        data: {
            labels: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('jenis_kelamin') : []),
            datasets: [{ data: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('total') : []), backgroundColor: [C.navy[0], C.gold[0]], borderColor: 'transparent', borderWidth: 4, hoverOffset: 8 }],
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: C.legend(), tooltip: { ...tp, callbacks: { label: C.pctLabel } } } },
    });

    initChart('chartPendidikanPic', {
        type: 'pie',
        data: {
            labels: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('pendidikan') : []),
            datasets: [{ data: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('total') : []), backgroundColor: C.mixed, borderColor: 'transparent', borderWidth: 3 }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: C.legend(), tooltip: { ...tp, callbacks: { label: C.pctLabel } } } },
    });

    initChart('chartUsiaPic', {
        type: 'polarArea',
        data: {
            labels: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('range') : []),
            datasets: [{ data: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('total') : []), backgroundColor: C.navy.slice(0,3).concat(C.gold.slice(0,2)).map(c => c + 'b3'), borderColor: 'transparent', borderWidth: 2 }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: C.legend(), tooltip: tp }, scales: { r: { ticks: { backdropColor: 'transparent', color: '#9ca3af', font: { size: 10 } }, grid: { color: 'rgba(148,163,184,0.1)' } } } },
    });

    initChart('chartEselonPic', {
        type: 'bar',
        data: {
            labels: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('eselon') : []),
            datasets: [{ data: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('total') : []), backgroundColor: C.gold, borderRadius: 6, borderSkipped: false }],
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tp, callbacks: { label: ctx => ctx.parsed.x + ' pegawai' } } }, scales: C.scales.count('y') },
    });
});
</script>
@endpush
