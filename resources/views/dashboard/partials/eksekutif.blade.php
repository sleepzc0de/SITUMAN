@include('dashboard.partials._chart_helpers')

@php
$kpis = [
    [
        'show'  => isset($anggaranStats),
        'gradient' => 'from-emerald-500 to-emerald-700',
        'value' => isset($anggaranStats) ? number_format($anggaranStats['persentase'], 1) . '%' : '-',
        'label' => 'Realisasi Anggaran',
        'sub'   => 'Pagu: Rp ' . (isset($anggaranStats) ? number_format($anggaranStats['total_pagu'] / 1e9, 2) : '0') . 'M',
        'pct'   => isset($anggaranStats) ? min($anggaranStats['persentase'], 100) : 0,
        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1',
    ],
    [
        'show'  => true,
        'gradient' => 'from-navy-600 to-navy-800',
        'value' => number_format($stats['pegawai_aktif']),
        'label' => 'Pegawai Aktif',
        'sub'   => 'Dari ' . number_format($stats['total_pegawai']) . ' total pegawai',
        'pct'   => null,
        'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    ],
    [
        'show'  => isset($inventarisStats),
        'gradient' => 'from-amber-500 to-amber-700',
        'value' => isset($inventarisStats) ? number_format($inventarisStats['total_aset']) : '-',
        'label' => 'Total Aset',
        'sub'   => (isset($inventarisStats) ? $inventarisStats['aset_dipinjam'] : 0) . ' aset dipinjam',
        'pct'   => null,
        'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    ],
];
@endphp

<div class="space-y-6">

    {{-- ── KPI Row ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @foreach($kpis as $kpi)
        @if($kpi['show'])
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $kpi['gradient'] }} text-white shadow-lg p-6">
            <div class="absolute -top-8 -right-8 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold tabular-nums">{{ $kpi['value'] }}</span>
                </div>
                <p class="font-semibold text-base leading-tight">{{ $kpi['label'] }}</p>
                <p class="text-xs opacity-70 mt-1">{{ $kpi['sub'] }}</p>
                @if(!is_null($kpi['pct']))
                <div class="mt-3 h-1.5 bg-white/25 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full" style="width: {{ $kpi['pct'] }}%"></div>
                </div>
                @endif
            </div>
        </div>
        @endif
        @endforeach
    </div>

    {{-- ── Charts Row 1 ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(isset($chartAnggaranBulan))
        <div class="chart-card">
            <h3 class="chart-title">Tren Anggaran Bulanan</h3>
            <div class="h-64"><canvas id="chartAnggaranEksekutif"></canvas></div>
        </div>
        @endif
        <div class="chart-card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="chart-title mb-0">Sebaran Pegawai per Bagian</h3>
                <span class="badge badge-gray">{{ isset($chartSebaranBagian) ? number_format($chartSebaranBagian->sum('total')) : 0 }} org</span>
            </div>
            <div class="h-64"><canvas id="chartBagianEksekutif"></canvas></div>
        </div>
    </div>

    {{-- ── Charts Row 2 ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="chart-card">
            <h3 class="chart-title">Distribusi Grading</h3>
            <div class="h-56"><canvas id="chartGradingEksekutif"></canvas></div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Komposisi Gender</h3>
            <div class="h-56"><canvas id="chartGenderEksekutif"></canvas></div>
        </div>
    </div>

    {{-- ── Info Box ── --}}
    <div class="flex items-start gap-3 px-4 py-3.5 rounded-2xl
                bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800
                text-sky-700 dark:text-sky-300">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm">
            Dashboard eksekutif menampilkan ringkasan data operasional untuk keperluan monitoring dan pengambilan keputusan.
            <span class="font-medium">Data diperbarui setiap 5 menit.</span>
        </p>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const C  = window.chartConfig;
    const tp = C.tooltip();

    function initChart(id, config) {
        const el = document.getElementById(id);
        return el ? new Chart(el, config) : null;
    }

    initChart('chartAnggaranEksekutif', {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
            datasets: [{
                data: @json($chartAnggaranBulan ?? []),
                borderColor: C.gold[0], backgroundColor: C.gold[0] + '1a',
                borderWidth: 2.5, fill: true, tension: 0.4,
                pointBackgroundColor: C.navy[0], pointBorderColor: C.gold[0],
                pointBorderWidth: 2, pointRadius: 4,
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

    initChart('chartBagianEksekutif', {
        type: 'bar',
        data: {
            labels: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('bagian') : []),
            datasets: [{
                data: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('total') : []),
                backgroundColor: C.navy,
                borderRadius: 5, borderSkipped: false,
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

    initChart('chartGradingEksekutif', {
        type: 'bar',
        data: {
            labels: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('grading')->map(fn($g) => 'Grd '.$g) : []),
            datasets: [{
                data: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('total') : []),
                backgroundColor: C.navy[0] + 'cc',
                borderRadius: 5, borderSkipped: false,
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

    initChart('chartGenderEksekutif', {
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
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: {
                legend: C.legend(),
                tooltip: { ...tp, callbacks: { label: C.pctLabel } },
            },
        },
    });
});
</script>
@endpush
