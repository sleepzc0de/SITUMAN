<div class="space-y-6">

    {{-- Stats Inventaris --}}
    @if(isset($inventarisStats))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Aset</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($inventarisStats['total_aset']) }}</p>
            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">{{ $inventarisStats['aset_dipinjam'] }} dipinjam</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total ATK</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($inventarisStats['total_atk']) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Jenis ATK terdaftar</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">ATK Menipis</p>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $inventarisStats['atk_menipis'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Perlu restock segera</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Permintaan Pending</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $inventarisStats['permintaan_pending'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Perlu diproses</p>
        </div>
    </div>
    @endif

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        {{-- Kondisi Aset --}}
        @if(isset($chartAsetKondisi))
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Kondisi Aset</h3>
            <div class="h-52">
                <canvas id="chartKondisiAset"></canvas>
            </div>
        </div>
        @endif

        {{-- Status Aset --}}
        @if(isset($chartAsetStatus))
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Status Aset</h3>
            <div class="h-52">
                <canvas id="chartStatusAset"></canvas>
            </div>
        </div>
        @endif

        {{-- Status ATK --}}
        @if(isset($chartAtkStatus))
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Status ATK</h3>
            <div class="h-52">
                <canvas id="chartStatusAtk"></canvas>
            </div>
        </div>
        @endif
    </div>

    {{-- Detail Status Cards --}}
    @if(isset($inventarisStats))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- ATK Status Detail --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Detail Status ATK</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tersedia</span>
                    </div>
                    <span class="text-lg font-bold text-green-700 dark:text-green-400">{{ $inventarisStats['atk_tersedia'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Menipis</span>
                    </div>
                    <span class="text-lg font-bold text-yellow-700 dark:text-yellow-400">{{ $inventarisStats['atk_menipis'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kosong</span>
                    </div>
                    <span class="text-lg font-bold text-red-700 dark:text-red-400">{{ $inventarisStats['atk_kosong'] }}</span>
                </div>
            </div>
        </div>

        {{-- Permintaan Status --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Status Permintaan ATK</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-orange-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Menunggu Persetujuan</span>
                    </div>
                    <span class="text-lg font-bold text-orange-700 dark:text-orange-400">{{ $inventarisStats['permintaan_pending'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Disetujui</span>
                    </div>
                    <span class="text-lg font-bold text-green-700 dark:text-green-400">{{ $inventarisStats['permintaan_approved'] }}</span>
                </div>
            </div>

            @if($inventarisStats['permintaan_pending'] > 0)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-navy-700">
                <a href="{{ route('inventaris.permintaan-atk.index') }}"
                    class="w-full flex items-center justify-center px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Proses {{ $inventarisStats['permintaan_pending'] }} Permintaan Pending
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Quick Links --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Akses Cepat Inventaris</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @php
            $links = [
                ['route' => 'inventaris.monitoring-atk.index',  'icon' => '📦', 'label' => 'Monitoring ATK',  'desc' => 'Pantau stok ATK'],
                ['route' => 'inventaris.permintaan-atk.index',  'icon' => '📋', 'label' => 'Permintaan ATK',  'desc' => 'Kelola permintaan'],
                ['route' => 'inventaris.aset-end-user.index',   'icon' => '💻', 'label' => 'Aset End User',   'desc' => 'Kelola aset'],
                ['route' => 'inventaris.kategori-atk.index',    'icon' => '🏷️', 'label' => 'Kategori ATK',    'desc' => 'Kelola kategori'],
                ['route' => 'inventaris.kategori-aset.index',   'icon' => '🗂️', 'label' => 'Kategori Aset',   'desc' => 'Kelola kategori'],
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

    // Chart Kondisi Aset
    const ctxK = document.getElementById('chartKondisiAset');
    if (ctxK) {
        const kondisiData = @json($chartAsetKondisi ?? collect());
        new Chart(ctxK, {
            type: 'doughnut',
            data: {
                labels: kondisiData.map(d => d.kondisi),
                datasets: [{
                    data: kondisiData.map(d => d.total),
                    backgroundColor: ['#10b981','#f59e0b','#ef4444','#6b7280'],
                    borderColor: '#fff', borderWidth: 4, hoverOffset: 8
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, font: { size: 10 } } },
                    tooltip: { ...tooltipDefaults }
                }
            }
        });
    }

    // Chart Status Aset
    const ctxS = document.getElementById('chartStatusAset');
    if (ctxS) {
        const statusData = @json($chartAsetStatus ?? collect());
        new Chart(ctxS, {
            type: 'pie',
            data: {
                labels: statusData.map(d => d.status),
                datasets: [{
                    data: statusData.map(d => d.total),
                    backgroundColor: ['#1e3a5f','#f59e0b','#10b981','#ef4444'],
                    borderColor: '#fff', borderWidth: 3
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, font: { size: 10 } } },
                    tooltip: { ...tooltipDefaults }
                }
            }
        });
    }

    // Chart Status ATK
    const ctxA = document.getElementById('chartStatusAtk');
    if (ctxA) {
        const atkData = @json($chartAtkStatus ?? collect());
        new Chart(ctxA, {
            type: 'bar',
            data: {
                labels: atkData.map(d => d.status),
                datasets: [{
                    data: atkData.map(d => d.total),
                    backgroundColor: ['#10b981','#f59e0b','#ef4444'],
                    borderRadius: 6, borderSkipped: false
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ctx.parsed.y + ' item' } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { color: '#6b7280' }, grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush
