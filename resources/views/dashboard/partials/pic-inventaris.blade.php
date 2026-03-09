@include('dashboard.partials._chart_helpers')

@php
$inv = $inventarisStats ?? [];

$statCards = [
    ['label' => 'Total Aset',          'value' => number_format($inv['total_aset'] ?? 0),         'sub_text' => ($inv['aset_dipinjam'] ?? 0) . ' dipinjam',    'sub_color' => 'text-orange-500 dark:text-orange-400'],
    ['label' => 'Total ATK',           'value' => number_format($inv['total_atk'] ?? 0),          'sub_text' => 'Jenis ATK terdaftar',                         'sub_color' => 'text-gray-400 dark:text-gray-500'],
    ['label' => 'ATK Menipis',         'value' => $inv['atk_menipis'] ?? 0,                        'sub_text' => 'Perlu restock segera',                        'sub_color' => 'text-amber-500 dark:text-amber-400', 'value_color' => 'text-amber-600 dark:text-amber-400'],
    ['label' => 'Permintaan Pending',  'value' => $inv['permintaan_pending'] ?? 0,                 'sub_text' => 'Perlu diproses',                              'sub_color' => 'text-red-500 dark:text-red-400', 'value_color' => 'text-red-600 dark:text-red-400'],
];

$atkDetail = [
    ['key' => 'atk_tersedia',  'label' => 'Tersedia', 'dot' => 'bg-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400'],
    ['key' => 'atk_menipis',   'label' => 'Menipis',  'dot' => 'bg-amber-500',   'bg' => 'bg-amber-50   dark:bg-amber-900/20',   'text' => 'text-amber-700   dark:text-amber-400'],
    ['key' => 'atk_kosong',    'label' => 'Kosong',   'dot' => 'bg-red-500',     'bg' => 'bg-red-50     dark:bg-red-900/20',     'text' => 'text-red-700     dark:text-red-400'],
];

$quickLinks = [
    ['route' => 'inventaris.monitoring-atk.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'label' => 'Monitoring ATK',  'desc' => 'Pantau stok ATK'],
    ['route' => 'inventaris.permintaan-atk.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'Permintaan ATK', 'desc' => 'Kelola permintaan'],
    ['route' => 'inventaris.aset-end-user.index',  'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1', 'label' => 'Aset End User',  'desc' => 'Kelola aset'],
    ['route' => 'inventaris.kategori-atk.index',   'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'label' => 'Kategori ATK',   'desc' => 'Kelola kategori'],
    ['route' => 'inventaris.kategori-aset.index',  'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'label' => 'Kategori Aset',  'desc' => 'Kelola kategori'],
];
@endphp

<div class="space-y-6">

    {{-- ── Stat Cards ── --}}
    @if(isset($inventarisStats))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statCards as $card)
        <div class="stat-card">
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
            <p class="text-3xl font-bold {{ $card['value_color'] ?? 'text-gray-900 dark:text-white' }} mt-1 tabular-nums">{{ $card['value'] }}</p>
            <p class="text-xs {{ $card['sub_color'] }} mt-1.5">{{ $card['sub_text'] }}</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Charts ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @if(isset($chartAsetKondisi))
        <div class="chart-card">
            <h3 class="chart-title">Kondisi Aset</h3>
            <div class="h-52"><canvas id="chartKondisiAset"></canvas></div>
        </div>
        @endif
        @if(isset($chartAsetStatus))
        <div class="chart-card">
            <h3 class="chart-title">Status Aset</h3>
            <div class="h-52"><canvas id="chartStatusAset"></canvas></div>
        </div>
        @endif
        @if(isset($chartAtkStatus))
        <div class="chart-card">
            <h3 class="chart-title">Status ATK</h3>
            <div class="h-52"><canvas id="chartStatusAtk"></canvas></div>
        </div>
        @endif
    </div>

    {{-- ── Detail Status ── --}}
    @if(isset($inventarisStats))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- ATK Detail --}}
        <div class="chart-card">
            <h3 class="chart-title">Detail Status ATK</h3>
            <div class="space-y-2.5">
                @foreach($atkDetail as $item)
                <div class="flex items-center justify-between px-3.5 py-3 {{ $item['bg'] }} rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 {{ $item['dot'] }} rounded-full flex-shrink-0"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                    </div>
                    <span class="text-base font-bold {{ $item['text'] }} tabular-nums">{{ $inv[$item['key']] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Permintaan Status --}}
        <div class="chart-card">
            <h3 class="chart-title">Status Permintaan ATK</h3>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between px-3.5 py-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-orange-500 rounded-full animate-pulse flex-shrink-0"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Menunggu Persetujuan</span>
                    </div>
                    <span class="text-base font-bold text-orange-700 dark:text-orange-400 tabular-nums">{{ $inv['permintaan_pending'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between px-3.5 py-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full flex-shrink-0"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Disetujui</span>
                    </div>
                    <span class="text-base font-bold text-emerald-700 dark:text-emerald-400 tabular-nums">{{ $inv['permintaan_approved'] ?? 0 }}</span>
                </div>
            </div>
            @if(($inv['permintaan_pending'] ?? 0) > 0)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-navy-700">
                <a href="{{ route('inventaris.permintaan-atk.index') }}"
                   class="btn btn-danger w-full justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Proses {{ $inv['permintaan_pending'] }} Permintaan Pending
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Quick Links ── --}}
    <div class="chart-card">
        <h3 class="chart-title">Akses Cepat Inventaris</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($quickLinks as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 p-3.5 bg-gray-50 dark:bg-navy-700/50 hover:bg-gray-100 dark:hover:bg-navy-700
                      rounded-xl border border-gray-100 dark:border-navy-600 transition-all duration-200 group">
                <div class="w-9 h-9 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center flex-shrink-0
                            group-hover:scale-110 group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50 transition-all duration-150">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate group-hover:text-orange-600 dark:group-hover:text-gold-400 transition-colors">{{ $link['label'] }}</p>
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

    const kondisiColors = { baik: C.status.success, rusak: C.status.danger, sedang: C.status.warning, default: C.status.gray };
    const statusColors  = [C.navy[0], C.gold[0], C.status.success, C.status.danger];
    const atkColors     = [C.status.success, C.status.warning, C.status.danger];

    initChart('chartKondisiAset', {
        type: 'doughnut',
        data: {
            labels: @json(isset($chartAsetKondisi) ? $chartAsetKondisi->pluck('kondisi') : []),
            datasets: [{
                data: @json(isset($chartAsetKondisi) ? $chartAsetKondisi->pluck('total') : []),
                backgroundColor: @json(isset($chartAsetKondisi) ? $chartAsetKondisi->pluck('kondisi') : [])->map(k => kondisiColors[k] || kondisiColors.default),
                borderColor: 'transparent', borderWidth: 4, hoverOffset: 8,
            }],
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: C.legend(), tooltip: { ...tp, callbacks: { label: C.pctLabel } } } },
    });

    initChart('chartStatusAset', {
        type: 'pie',
        data: {
            labels: @json(isset($chartAsetStatus) ? $chartAsetStatus->pluck('status') : []),
            datasets: [{ data: @json(isset($chartAsetStatus) ? $chartAsetStatus->pluck('total') : []), backgroundColor: statusColors, borderColor: 'transparent', borderWidth: 3 }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: C.legend(), tooltip: { ...tp, callbacks: { label: C.pctLabel } } } },
    });

    initChart('chartStatusAtk', {
        type: 'bar',
        data: {
            labels: @json(isset($chartAtkStatus) ? $chartAtkStatus->pluck('status') : []),
            datasets: [{ data: @json(isset($chartAtkStatus) ? $chartAtkStatus->pluck('total') : []), backgroundColor: atkColors, borderRadius: 7, borderSkipped: false }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...tp, callbacks: { label: ctx => ctx.parsed.y + ' item' } } }, scales: C.scales.count() },
    });
});
</script>
@endpush
