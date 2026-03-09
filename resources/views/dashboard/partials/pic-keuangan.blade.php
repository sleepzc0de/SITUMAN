@include('dashboard.partials._chart_helpers')

@php
$angStats = $anggaranStats ?? [];
$statCards = [
    ['label' => 'Total Pagu',     'value' => 'Rp ' . number_format(($angStats['total_pagu'] ?? 0) / 1e6, 1) . 'M',        'sub' => 'Anggaran ' . date('Y'),   'color' => 'text-gray-900 dark:text-white',         'pct' => null, 'pct_color' => null],
    ['label' => 'Realisasi',      'value' => 'Rp ' . number_format(($angStats['total_realisasi'] ?? 0) / 1e6, 1) . 'M',   'sub' => number_format($angStats['persentase'] ?? 0, 1) . '% terserap', 'color' => 'text-emerald-600 dark:text-emerald-400', 'pct' => min($angStats['persentase'] ?? 0, 100), 'pct_color' => 'bg-emerald-500'],
    ['label' => 'Outstanding',    'value' => 'Rp ' . number_format(($angStats['total_outstanding'] ?? 0) / 1e6, 1) . 'M',  'sub' => 'Belum SP2D',              'color' => 'text-orange-600 dark:text-orange-400', 'pct' => null, 'pct_color' => null],
    ['label' => 'Sisa Anggaran',  'value' => 'Rp ' . number_format(($angStats['total_sisa'] ?? 0) / 1e6, 1) . 'M',        'sub' => 'Belum terserap',          'color' => 'text-navy-600 dark:text-navy-400',     'pct' => null, 'pct_color' => null],
];
@endphp

<div class="space-y-6">

    {{-- ── Stats ── --}}
    @if(isset($anggaranStats))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statCards as $card)
        <div class="stat-card">
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold {{ $card['color'] }} mt-1 tabular-nums">{{ $card['value'] }}</p>
            @if($card['pct'] !== null)
            <div class="mt-2.5 progress-bar">
                <div class="progress-fill {{ $card['pct_color'] }}" style="width: {{ $card['pct'] }}%"></div>
            </div>
            @endif
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">{{ $card['sub'] }}</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Charts ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(isset($chartAnggaranBulan))
        <div class="chart-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="chart-title mb-0">Rencana Penarikan per Bulan</h3>
                <span class="badge badge-info">{{ date('Y') }}</span>
            </div>
            <div class="h-64"><canvas id="chartAnggaranBulanPic"></canvas></div>
        </div>
        @endif

        @if(isset($chartSppBulan))
        <div class="chart-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="chart-title mb-0">Realisasi SPP per Bulan</h3>
                <span class="badge badge-success">Sudah SP2D</span>
            </div>
            <div class="h-64"><canvas id="chartSppBulanPic"></canvas></div>
        </div>
        @endif
    </div>

    {{-- ── Progress per RO ── --}}
    @if(isset($chartAnggaranPerRo) && $chartAnggaranPerRo->count())
    <div class="chart-card">
        <h3 class="chart-title">Realisasi Anggaran per RO</h3>
        <div class="space-y-4">
            @foreach($chartAnggaranPerRo as $ro)
            @php
                $pct = $ro->pagu > 0 ? round($ro->realisasi / $ro->pagu * 100, 1) : 0;
                $bar  = $pct >= 80 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-amber-500' : 'bg-red-500');
                $txt  = $pct >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($pct >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">RO {{ $ro->ro }}</span>
                    <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                        <span class="tabular-nums">Rp {{ number_format($ro->realisasi / 1e6, 1) }}M / Rp {{ number_format($ro->pagu / 1e6, 1) }}M</span>
                        <span class="font-bold {{ $txt }} tabular-nums">{{ $pct }}%</span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill {{ $bar }}" style="width: {{ min($pct, 100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Quick Links ── --}}
    <div class="chart-card">
        <h3 class="chart-title">Akses Cepat Anggaran</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @php
            $links = [
                ['route' => 'anggaran.data.index',      'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'label' => 'Data Anggaran',    'desc' => 'Kelola data anggaran'],
                ['route' => 'anggaran.monitoring.index', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Monitoring',        'desc' => 'Pantau realisasi'],
                ['route' => 'anggaran.spp.index',        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Data SPP',         'desc' => 'Kelola tagihan SPP'],
                ['route' => 'anggaran.usulan.index',     'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Usulan Penarikan', 'desc' => 'Ajukan penarikan dana'],
                ['route' => 'anggaran.dokumen.index',    'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z', 'label' => 'Dokumen Capaian', 'desc' => 'Upload dokumen output'],
                ['route' => 'anggaran.revisi.index',     'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'label' => 'Revisi Anggaran',  'desc' => 'Kelola revisi'],
            ];
            @endphp
            @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 p-3.5 bg-gray-50 dark:bg-navy-700/50 hover:bg-gray-100 dark:hover:bg-navy-700
                      rounded-xl border border-gray-100 dark:border-navy-600 transition-all duration-200 group">
                <div class="w-9 h-9 bg-navy-100 dark:bg-navy-600 rounded-xl flex items-center justify-center flex-shrink-0
                            group-hover:bg-navy-200 dark:group-hover:bg-navy-500 group-hover:scale-110 transition-all duration-150">
                    <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate group-hover:text-navy-600 dark:group-hover:text-gold-400 transition-colors">{{ $link['label'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $link['desc'] }}</p>
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

    initChart('chartAnggaranBulanPic', {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
            datasets: [{ data: @json($chartAnggaranBulan ?? []), backgroundColor: C.navy[0] + 'cc', borderRadius: 6, borderSkipped: false }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tp, callbacks: { label: ctx => C.formatIDR(ctx.parsed.y) } } }, scales: C.scales.currency() },
    });

    initChart('chartSppBulanPic', {
        type: 'line',
        data: {
            labels: @json(isset($chartSppBulan) ? $chartSppBulan->map(fn($d) => 'Bulan '.$d->bulan) : []),
            datasets: [{
                data: @json(isset($chartSppBulan) ? $chartSppBulan->pluck('total') : []),
                borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.12)',
                borderWidth: 2.5, fill: true, tension: 0.4,
                pointBackgroundColor: C.navy[0], pointBorderColor: '#10b981',
                pointBorderWidth: 2, pointRadius: 5,
            }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tp, callbacks: { label: ctx => C.formatIDR(ctx.parsed.y) } } }, scales: C.scales.currency() },
    });
});
</script>
@endpush
