<div class="space-y-6">

    {{-- Row 1: Anggaran Overview + User Distribution --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Anggaran Overview Card --}}
        <div class="lg:col-span-2 bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Realisasi Anggaran per Bulan</h3>
                <span class="text-xs text-gray-500 bg-gray-100 dark:bg-navy-700 dark:text-gray-400 px-2 py-1 rounded-full">Tahun {{ date('Y') }}</span>
            </div>
            <div class="h-56">
                <canvas id="chartAnggaranBulan"></canvas>
            </div>
        </div>

        {{-- User Per Role Donut --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Distribusi Role User</h3>
            <div class="h-40 flex items-center justify-center">
                <canvas id="chartUserRole"></canvas>
            </div>
            {{-- Legend --}}
            @if(isset($userPerRole))
            <div class="mt-4 space-y-2">
                @foreach($userPerRole as $ur)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $ur->role ?? 'Tanpa Role' }}</span>
                    <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $ur->total }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Row 2: Charts Kepegawaian --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Sebaran Bagian --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Sebaran Pegawai per Bagian</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ isset($chartSebaranBagian) ? $chartSebaranBagian->sum('total') : 0 }} org</span>
            </div>
            <div class="h-64">
                <canvas id="chartSebaranBagian"></canvas>
            </div>
        </div>

        {{-- Sebaran Grading --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Distribusi Grading</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ isset($chartSebaranGrading) ? $chartSebaranGrading->sum('total') : 0 }} org</span>
            </div>
            <div class="h-64">
                <canvas id="chartSebaranGrading"></canvas>
            </div>
        </div>
    </div>

    {{-- Row 3: Pie Charts --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Jenis Kelamin</h3>
            <div class="h-52">
                <canvas id="chartJenisKelamin"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Tingkat Pendidikan</h3>
            <div class="h-52">
                <canvas id="chartPendidikan"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Distribusi Usia</h3>
            <div class="h-52">
                <canvas id="chartRangeUsia"></canvas>
            </div>
        </div>
    </div>

    {{-- Row 4: Inventaris Stats + Aksi Cepat --}}
    @if(isset($inventarisStats))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ATK Status --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Status ATK & Inventaris</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Tersedia</span>
                    </div>
                    <span class="font-bold text-green-700 dark:text-green-400">{{ $inventarisStats['atk_tersedia'] ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Menipis</span>
                    </div>
                    <span class="font-bold text-yellow-700 dark:text-yellow-400">{{ $inventarisStats['atk_menipis'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-xl">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Kosong</span>
                    </div>
                    <span class="font-bold text-red-700 dark:text-red-400">{{ $inventarisStats['atk_kosong'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Permintaan Pending</span>
                    </div>
                    <span class="font-bold text-orange-700 dark:text-orange-400">{{ $inventarisStats['permintaan_pending'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="lg:col-span-2 bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-2 gap-3">
                @php
                $quickActions = [
                    ['route' => 'kepegawaian.sebaran', 'icon' => '👥', 'label' => 'Sebaran Pegawai', 'color' => 'navy'],
                    ['route' => 'kepegawaian.grading', 'icon' => '📈', 'label' => 'Kenaikan Grading', 'color' => 'gold'],
                    ['route' => 'anggaran.monitoring.index', 'icon' => '💰', 'label' => 'Monitoring Anggaran', 'color' => 'green'],
                    ['route' => 'inventaris.monitoring-atk.index', 'icon' => '📦', 'label' => 'Monitoring ATK', 'color' => 'orange'],
                    ['route' => 'users.index', 'icon' => '👤', 'label' => 'Manajemen User', 'color' => 'purple'],
                    ['route' => 'roles.index', 'icon' => '🔑', 'label' => 'Kelola Role', 'color' => 'red'],
                ];
                @endphp
                @foreach($quickActions as $action)
                <a href="{{ route($action['route']) }}"
                    class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-navy-700 hover:bg-gray-100 dark:hover:bg-navy-600 rounded-xl transition-colors group">
                    <span class="text-xl">{{ $action['icon'] }}</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-navy-600 dark:group-hover:text-gold-400">{{ $action['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Eselon Chart --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Sebaran Per Eselon</h3>
        <div class="h-48">
            <canvas id="chartSebaranEselon"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navyColors = ['#1e3a5f','#2d5986','#4a7ba7','#6fa3d0','#8ec5ea'];
    const goldColors = ['#f59e0b','#fbbf24','#fcd34d','#fde68a'];
    const mixed = ['#1e3a5f','#f59e0b','#2d5986','#fbbf24','#4a7ba7','#fcd34d','#6fa3d0'];

    const tooltipDefaults = {
        backgroundColor: '#1e3a5f',
        titleColor: '#fbbf24',
        bodyColor: '#fff',
        padding: 12,
        cornerRadius: 8,
        titleFont: { size: 13, weight: 'bold' },
        bodyFont: { size: 12 }
    };

    // Chart Anggaran Bulan
    const ctxAnggaran = document.getElementById('chartAnggaranBulan');
    if (ctxAnggaran) {
        const bulanData = @json($chartAnggaranBulan ?? []);
        new Chart(ctxAnggaran, {
            type: 'bar',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
                datasets: [{
                    label: 'Rencana Penarikan',
                    data: bulanData,
                    backgroundColor: 'rgba(30,58,95,0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipDefaults,
                    callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) }
                }},
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(0)+'M', color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { color: '#6b7280' }, grid: { display: false } }
                }
            }
        });
    }

    // Chart User Per Role
    const ctxUserRole = document.getElementById('chartUserRole');
    if (ctxUserRole) {
        const urData = @json($userPerRole ?? collect());
        new Chart(ctxUserRole, {
            type: 'doughnut',
            data: {
                labels: urData.map(u => u.role || 'Tanpa Role'),
                datasets: [{ data: urData.map(u => u.total), backgroundColor: mixed, borderColor: '#fff', borderWidth: 3, hoverOffset: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: { legend: { display: false }, tooltip: { ...tooltipDefaults } }
            }
        });
    }

    // Chart Sebaran Bagian
    const ctxBagian = document.getElementById('chartSebaranBagian');
    if (ctxBagian) {
        new Chart(ctxBagian, {
            type: 'bar',
            data: {
                labels: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('bagian') : []),
                datasets: [{ label: 'Pegawai', data: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('total') : []),
                    backgroundColor: navyColors, borderRadius: 6, borderSkipped: false }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ctx.parsed.y + ' pegawai' } } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { ticks: { color: '#6b7280', maxRotation: 45 }, grid: { display: false } } }
            }
        });
    }

    // Chart Grading (Line)
    const ctxGrading = document.getElementById('chartSebaranGrading');
    if (ctxGrading) {
        new Chart(ctxGrading, {
            type: 'line',
            data: {
                labels: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('grading')->map(fn($g) => 'Grade '.$g) : []),
                datasets: [{ label: 'Pegawai', data: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('total') : []),
                    borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)',
                    borderWidth: 3, fill: true, tension: 0.4,
                    pointBackgroundColor: '#1e3a5f', pointBorderColor: '#f59e0b', pointBorderWidth: 2, pointRadius: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ctx.parsed.y + ' pegawai' } } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { ticks: { color: '#6b7280' }, grid: { display: false } } }
            }
        });
    }

    // Chart Jenis Kelamin
    const ctxJK = document.getElementById('chartJenisKelamin');
    if (ctxJK) {
        new Chart(ctxJK, {
            type: 'doughnut',
            data: {
                labels: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('jenis_kelamin') : []),
                datasets: [{ data: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('total') : []),
                    backgroundColor: ['#1e3a5f','#f59e0b'], borderColor: '#fff', borderWidth: 4, hoverOffset: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } } },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => { const t = ctx.dataset.data.reduce((a,b)=>a+b,0); return ctx.label + ': ' + ctx.parsed + ' (' + ((ctx.parsed/t)*100).toFixed(1) + '%)'; } } }
                }
            }
        });
    }

    // Chart Pendidikan
    const ctxPend = document.getElementById('chartPendidikan');
    if (ctxPend) {
        new Chart(ctxPend, {
            type: 'pie',
            data: {
                labels: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('pendidikan') : []),
                datasets: [{ data: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('total') : []),
                    backgroundColor: mixed, borderColor: '#fff', borderWidth: 3 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, pointStyle: 'circle', font: { size: 10 } } },
                    tooltip: { ...tooltipDefaults }
                }
            }
        });
    }

    // Chart Range Usia
    const ctxUsia = document.getElementById('chartRangeUsia');
    if (ctxUsia) {
        new Chart(ctxUsia, {
            type: 'polarArea',
            data: {
                labels: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('range') : []),
                datasets: [{ data: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('total') : []),
                    backgroundColor: ['rgba(30,58,95,0.7)','rgba(45,89,134,0.7)','rgba(74,123,167,0.7)','rgba(245,158,11,0.7)','rgba(251,191,36,0.7)'],
                    borderColor: '#fff', borderWidth: 2 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 10, usePointStyle: true, font: { size: 10 } } },
                    tooltip: { ...tooltipDefaults }
                },
                scales: { r: { ticks: { backdropColor: 'transparent', color: '#9ca3af', font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.08)' } } }
            }
        });
    }

    // Chart Eselon
    const ctxEselon = document.getElementById('chartSebaranEselon');
    if (ctxEselon) {
        new Chart(ctxEselon, {
            type: 'bar',
            data: {
                labels: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('eselon') : []),
                datasets: [{ label: 'Pegawai', data: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('total') : []),
                    backgroundColor: goldColors, borderRadius: 6, borderSkipped: false }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ctx.parsed.x + ' pegawai' } } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } }, y: { ticks: { color: '#6b7280' }, grid: { display: false } } }
            }
        });
    }
});
</script>
@endpush
