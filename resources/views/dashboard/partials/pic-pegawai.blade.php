<div class="space-y-6">

    {{-- Stats Kepegawaian --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Pegawai</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_pegawai']) }}</p>
                </div>
                <div class="w-11 h-11 bg-navy-100 dark:bg-navy-700 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 w-full bg-gray-100 dark:bg-navy-700 rounded-full h-1.5">
                @php $pct = $stats['total_pegawai'] > 0 ? round(($stats['pegawai_aktif']/$stats['total_pegawai'])*100) : 0; @endphp
                <div class="bg-navy-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $pct }}% aktif</p>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pegawai Aktif</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">{{ number_format($stats['pegawai_aktif']) }}</p>
                </div>
                <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Unit/Bagian</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $stats['pegawai_per_bagian']->count() }}</p>
                </div>
                <div class="w-11 h-11 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Akan Pensiun</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">
                        {{ isset($pegawaiMendekatiPensiun) ? $pegawaiMendekatiPensiun->count() : 0 }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Dalam 2 tahun</p>
                </div>
                <div class="w-11 h-11 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Sebaran Bagian --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Sebaran per Bagian</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-navy-700 px-2 py-1 rounded-full">
                    {{ isset($chartSebaranBagian) ? $chartSebaranBagian->sum('total') : 0 }} pegawai
                </span>
            </div>
            <div class="h-64">
                <canvas id="chartBagianPic"></canvas>
            </div>
        </div>

        {{-- Sebaran Grading --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Distribusi Grading</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-navy-700 px-2 py-1 rounded-full">
                    {{ isset($chartSebaranGrading) ? $chartSebaranGrading->sum('total') : 0 }} pegawai
                </span>
            </div>
            <div class="h-64">
                <canvas id="chartGradingPic"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        {{-- Jenis Kelamin --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Jenis Kelamin</h3>
            <div class="h-52">
                <canvas id="chartJKPic"></canvas>
            </div>
        </div>

        {{-- Pendidikan --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Tingkat Pendidikan</h3>
            <div class="h-52">
                <canvas id="chartPendidikanPic"></canvas>
            </div>
        </div>

        {{-- Range Usia --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Distribusi Usia</h3>
            <div class="h-52">
                <canvas id="chartUsiaPic"></canvas>
            </div>
        </div>
    </div>

    {{-- Eselon + Pensiun Table --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Eselon --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Sebaran per Eselon</h3>
            <div class="h-56">
                <canvas id="chartEselonPic"></canvas>
            </div>
        </div>

        {{-- Akan Pensiun --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Pegawai Mendekati Pensiun</h3>
                <span class="text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-1 rounded-full font-medium">
                    ≤ 2 Tahun
                </span>
            </div>
            @if(isset($pegawaiMendekatiPensiun) && $pegawaiMendekatiPensiun->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-navy-700 max-h-56 overflow-y-auto">
                @foreach($pegawaiMendekatiPensiun as $p)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-navy-700/50">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-red-600 dark:text-red-400">
                                {{ strtoupper(substr($p->nama, 0, 2)) }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $p->nama }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $p->bagian ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-3">
                        <p class="text-xs font-semibold text-red-600 dark:text-red-400">
                            {{ $p->tanggal_pensiun ? \Carbon\Carbon::parse($p->tanggal_pensiun)->format('d/m/Y') : '-' }}
                        </p>
                        @if($p->tanggal_pensiun)
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($p->tanggal_pensiun)->diffForHumans() }}
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-10 text-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-gray-400 dark:text-gray-500">Tidak ada pegawai yang akan pensiun dalam 2 tahun ke depan</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Akses Cepat Kepegawaian</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <a href="{{ route('kepegawaian.sebaran') }}"
                class="flex items-center space-x-3 p-4 bg-navy-50 dark:bg-navy-700 hover:bg-navy-100 dark:hover:bg-navy-600 rounded-xl transition-colors border border-navy-200 dark:border-navy-600 group">
                <div class="w-10 h-10 bg-navy-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-navy-800 dark:text-white group-hover:text-navy-600 dark:group-hover:text-gold-400">Sebaran Pegawai</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Lihat distribusi lengkap</p>
                </div>
            </a>
            <a href="{{ route('kepegawaian.grading') }}"
                class="flex items-center space-x-3 p-4 bg-gold-50 dark:bg-navy-700 hover:bg-gold-100 dark:hover:bg-navy-600 rounded-xl transition-colors border border-gold-200 dark:border-navy-600 group">
                <div class="w-10 h-10 bg-gold-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-navy-800 dark:text-white group-hover:text-gold-600 dark:group-hover:text-gold-400">Kenaikan Grading</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Rekomendasi grading</p>
                </div>
            </a>
            <a href="{{ route('kepegawaian.mutasi') }}"
                class="flex items-center space-x-3 p-4 bg-green-50 dark:bg-navy-700 hover:bg-green-100 dark:hover:bg-navy-600 rounded-xl transition-colors border border-green-200 dark:border-navy-600 group">
                <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-navy-800 dark:text-white group-hover:text-green-600 dark:group-hover:text-gold-400">Proyeksi Mutasi</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Analisis mutasi pegawai</p>
                </div>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tooltipDefaults = {
        backgroundColor: '#1e3a5f',
        titleColor: '#fbbf24',
        bodyColor: '#fff',
        padding: 10,
        cornerRadius: 8,
        titleFont: { size: 12, weight: 'bold' },
        bodyFont: { size: 11 }
    };

    // Chart Sebaran Bagian
    const ctxBagian = document.getElementById('chartBagianPic');
    if (ctxBagian) {
        new Chart(ctxBagian, {
            type: 'bar',
            data: {
                labels: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('bagian') : []),
                datasets: [{
                    data: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('total') : []),
                    backgroundColor: ['#1e3a5f','#2d5986','#4a7ba7','#6fa3d0','#8ec5ea'],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ctx.parsed.y + ' pegawai' } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { color: '#6b7280', maxRotation: 45 }, grid: { display: false } }
                }
            }
        });
    }

    // Chart Grading (Line)
    const ctxGrading = document.getElementById('chartGradingPic');
    if (ctxGrading) {
        new Chart(ctxGrading, {
            type: 'line',
            data: {
                labels: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('grading')->map(fn($g) => 'Grade '.$g) : []),
                datasets: [{
                    data: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('total') : []),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.1)',
                    borderWidth: 3, fill: true, tension: 0.4,
                    pointBackgroundColor: '#1e3a5f',
                    pointBorderColor: '#f59e0b',
                    pointRadius: 5, pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ctx.parsed.y + ' pegawai' } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { color: '#6b7280' }, grid: { display: false } }
                }
            }
        });
    }

    // Chart Jenis Kelamin
    const ctxJK = document.getElementById('chartJKPic');
    if (ctxJK) {
        new Chart(ctxJK, {
            type: 'doughnut',
            data: {
                labels: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('jenis_kelamin') : []),
                datasets: [{
                    data: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('total') : []),
                    backgroundColor: ['#1e3a5f', '#f59e0b'],
                    borderColor: '#fff', borderWidth: 4, hoverOffset: 8
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } } },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => { const t = ctx.dataset.data.reduce((a,b)=>a+b,0); return ctx.label+': '+ctx.parsed+' ('+((ctx.parsed/t)*100).toFixed(1)+'%)'; } } }
                }
            }
        });
    }

    // Chart Pendidikan
    const ctxPend = document.getElementById('chartPendidikanPic');
    if (ctxPend) {
        new Chart(ctxPend, {
            type: 'pie',
            data: {
                labels: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('pendidikan') : []),
                datasets: [{
                    data: @json(isset($chartSebaranPendidikan) ? $chartSebaranPendidikan->pluck('total') : []),
                    backgroundColor: ['#1e3a5f','#f59e0b','#2d5986','#fbbf24','#4a7ba7','#fcd34d'],
                    borderColor: '#fff', borderWidth: 3
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 8, usePointStyle: true, font: { size: 10 } } },
                    tooltip: { ...tooltipDefaults }
                }
            }
        });
    }

    // Chart Usia
    const ctxUsia = document.getElementById('chartUsiaPic');
    if (ctxUsia) {
        new Chart(ctxUsia, {
            type: 'polarArea',
            data: {
                labels: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('range') : []),
                datasets: [{
                    data: @json(isset($chartRangeUsia) ? $chartRangeUsia->pluck('total') : []),
                    backgroundColor: ['rgba(30,58,95,0.7)','rgba(45,89,134,0.7)','rgba(74,123,167,0.7)','rgba(245,158,11,0.7)','rgba(251,191,36,0.7)'],
                    borderColor: '#fff', borderWidth: 2
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 8, usePointStyle: true, font: { size: 10 } } },
                    tooltip: { ...tooltipDefaults }
                },
                scales: { r: { ticks: { backdropColor: 'transparent', color: '#9ca3af', font: { size: 9 } }, grid: { color: 'rgba(0,0,0,0.08)' } } }
            }
        });
    }

    // Chart Eselon
    const ctxEselon = document.getElementById('chartEselonPic');
    if (ctxEselon) {
        new Chart(ctxEselon, {
            type: 'bar',
            data: {
                labels: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('eselon') : []),
                datasets: [{
                    data: @json(isset($chartSebaranEselon) ? $chartSebaranEselon->pluck('total') : []),
                    backgroundColor: ['#f59e0b','#fbbf24','#fcd34d','#fde68a'],
                    borderRadius: 6, borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ctx.parsed.x + ' pegawai' } }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    y: { ticks: { color: '#6b7280' }, grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush
