<div class="space-y-6">

    {{-- Stats Anggaran --}}
    @if(isset($anggaranStats))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Pagu</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                Rp {{ number_format(($anggaranStats['total_pagu'] ?? 0)/1000000, 1) }}M
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Anggaran {{ date('Y') }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Realisasi</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                Rp {{ number_format(($anggaranStats['total_realisasi'] ?? 0)/1000000, 1) }}M
            </p>
            <div class="mt-2 w-full bg-gray-100 dark:bg-navy-700 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ min($anggaranStats['persentase'] ?? 0, 100) }}%"></div>
            </div>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1">{{ number_format($anggaranStats['persentase'] ?? 0, 1) }}%</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Outstanding</p>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1">
                Rp {{ number_format(($anggaranStats['total_outstanding'] ?? 0)/1000000, 1) }}M
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Belum SP2D</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sisa Anggaran</p>
            <p class="text-2xl font-bold text-navy-600 dark:text-navy-400 mt-1">
                Rp {{ number_format(($anggaranStats['total_sisa'] ?? 0)/1000000, 1) }}M
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Belum terserap</p>
        </div>
    </div>
    @endif

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Anggaran per Bulan --}}
        @if(isset($chartAnggaranBulan))
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Rencana Penarikan per Bulan</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-navy-700 px-2 py-1 rounded-full">{{ date('Y') }}</span>
            </div>
            <div class="h-64">
                <canvas id="chartAnggaranBulanPic"></canvas>
            </div>
        </div>
        @endif

        {{-- Realisasi SPP per Bulan --}}
        @if(isset($chartSppBulan))
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Realisasi SPP per Bulan</h3>
                <span class="text-xs text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded-full font-medium">Sudah SP2D</span>
            </div>
            <div class="h-64">
                <canvas id="chartSppBulanPic"></canvas>
            </div>
        </div>
        @endif
    </div>

    {{-- Anggaran per RO --}}
    @if(isset($chartAnggaranPerRo) && $chartAnggaranPerRo->count() > 0)
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Realisasi Anggaran per RO</h3>
        <div class="space-y-3">
            @foreach($chartAnggaranPerRo as $ro)
            @php
                $pct = $ro->pagu > 0 ? round(($ro->realisasi / $ro->pagu) * 100, 1) : 0;
                $barColor = $pct >= 80 ? 'bg-green-500' : ($pct >= 50 ? 'bg-gold-500' : 'bg-red-500');
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">RO {{ $ro->ro }}</span>
                    <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
                        <span>Rp {{ number_format($ro->realisasi/1000000, 1) }}M / Rp {{ number_format($ro->pagu/1000000, 1) }}M</span>
                        <span class="font-bold {{ $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-gold-600' : 'text-red-600') }}">
                            {{ $pct }}%
                        </span>
                    </div>
                </div>
                <div class="w-full bg-gray-100 dark:bg-navy-700 rounded-full h-2">
                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-700" style="width: {{ min($pct, 100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick Links --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Akses Cepat Anggaran</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @php
            $links = [
                ['route' => 'anggaran.data.index',       'icon' => '📊', 'label' => 'Data Anggaran',        'desc' => 'Kelola data anggaran'],
                ['route' => 'anggaran.monitoring.index',  'icon' => '👁️', 'label' => 'Monitoring',           'desc' => 'Pantau realisasi'],
                ['route' => 'anggaran.spp.index',         'icon' => '📄', 'label' => 'Data SPP',             'desc' => 'Kelola tagihan SPP'],
                ['route' => 'anggaran.usulan.index',      'icon' => '💸', 'label' => 'Usulan Penarikan',     'desc' => 'Ajukan penarikan dana'],
                ['route' => 'anggaran.dokumen.index',     'icon' => '📁', 'label' => 'Dokumen Capaian',      'desc' => 'Upload dokumen output'],
                ['route' => 'anggaran.revisi.index',      'icon' => '✏️', 'label' => 'Revisi Anggaran',      'desc' => 'Kelola revisi'],
            ];
            @endphp
            @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
                class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-navy-700 hover:bg-gray-100 dark:hover:bg-navy-600 rounded-xl transition-colors group">
                <span class="text-xl flex-shrink-0">{{ $link['icon'] }}</span>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-navy-600 dark:group-hover:text-gold-400 truncate">{{ $link['label'] }}</p>
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
    const tooltipDefaults = {
        backgroundColor: '#1e3a5f', titleColor: '#fbbf24', bodyColor: '#fff',
        padding: 10, cornerRadius: 8
    };

    // Chart Anggaran Bulan
    const ctxA = document.getElementById('chartAnggaranBulanPic');
    if (ctxA) {
        new Chart(ctxA, {
            type: 'bar',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
                datasets: [{
                    label: 'Rencana',
                    data: @json($chartAnggaranBulan ?? []),
                    backgroundColor: 'rgba(30,58,95,0.8)',
                    borderRadius: 6, borderSkipped: false
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => 'Rp'+(v/1000000).toFixed(0)+'M', color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { color: '#6b7280' }, grid: { display: false } }
                }
            }
        });
    }

    // Chart SPP Bulan
    const ctxS = document.getElementById('chartSppBulanPic');
    if (ctxS) {
        const sppData = @json($chartSppBulan ?? collect());
        new Chart(ctxS, {
            type: 'line',
            data: {
                labels: sppData.map(d => 'Bulan '+d.bulan),
                datasets: [{
                    label: 'Realisasi SPP',
                    data: sppData.map(d => d.total),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    borderWidth: 3, fill: true, tension: 0.4,
                    pointBackgroundColor: '#1e3a5f',
                    pointBorderColor: '#10b981',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => 'Rp'+(v/1000000).toFixed(0)+'M', color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { color: '#6b7280' }, grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush
