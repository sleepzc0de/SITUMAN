<div class="space-y-6">

    {{-- Ringkasan Eksekutif: 3 KPI Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KPI Anggaran --}}
        @if(isset($anggaranStats))
        <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1" />
                    </svg>
                </div>
                <span class="text-3xl font-bold">{{ number_format($anggaranStats['persentase'] ?? 0, 1) }}%</span>
            </div>
            <p class="font-semibold text-lg">Realisasi Anggaran</p>
            <p class="text-green-100 text-sm mt-1">Total pagu: Rp {{ number_format(($anggaranStats['total_pagu'] ?? 0)/1000000000, 2) }}M</p>
            <div class="mt-3 bg-white/20 rounded-full h-2">
                <div class="bg-white h-2 rounded-full" style="width: {{ min($anggaranStats['persentase'] ?? 0, 100) }}%"></div>
            </div>
        </div>
        @endif

        {{-- KPI Kepegawaian --}}
        <div class="bg-gradient-to-br from-navy-600 to-navy-800 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="text-3xl font-bold">{{ number_format($stats['pegawai_aktif']) }}</span>
            </div>
            <p class="font-semibold text-lg">Pegawai Aktif</p>
            <p class="text-navy-300 text-sm mt-1">Dari {{ number_format($stats['total_pegawai']) }} total pegawai</p>
        </div>

        {{-- KPI Inventaris --}}
        @if(isset($inventarisStats))
        <div class="bg-gradient-to-br from-gold-500 to-gold-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="text-3xl font-bold">{{ number_format($inventarisStats['total_aset'] ?? 0) }}</span>
            </div>
            <p class="font-semibold text-lg">Total Aset</p>
            <p class="text-gold-100 text-sm mt-1">{{ $inventarisStats['aset_dipinjam'] ?? 0 }} aset dipinjam</p>
        </div>
        @endif
    </div>

    {{-- Grafik Ringkasan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Anggaran per Bulan --}}
        @if(isset($chartAnggaranBulan))
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Tren Anggaran Bulanan</h3>
            <div class="h-64">
                <canvas id="chartAnggaranEksekutif"></canvas>
            </div>
        </div>
        @endif

        {{-- Sebaran Pegawai --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Sebaran Pegawai per Bagian</h3>
            <div class="h-64">
                <canvas id="chartBagianEksekutif"></canvas>
            </div>
        </div>
    </div>

    {{-- Distribusi Grading + Jenis Kelamin --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Distribusi Grading</h3>
            <div class="h-56">
                <canvas id="chartGradingEksekutif"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Komposisi Gender</h3>
            <div class="h-56">
                <canvas id="chartGenderEksekutif"></canvas>
            </div>
        </div>
    </div>

    {{-- Info box --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4 flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm text-blue-700 dark:text-blue-300">
            Dashboard eksekutif menampilkan ringkasan data operasional untuk keperluan monitoring dan pengambilan keputusan. Data diperbarui setiap 5 menit.
        </p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tooltipDefaults = { backgroundColor: '#1e3a5f', titleColor: '#fbbf24', bodyColor: '#fff', padding: 10, cornerRadius: 8 };

    // Chart Anggaran Eksekutif
    const ctxA = document.getElementById('chartAnggaranEksekutif');
    if (ctxA) {
        new Chart(ctxA, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
                datasets: [{
                    label: 'Anggaran',
                    data: @json($chartAnggaranBulan ?? []),
                    borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)',
                    borderWidth: 3, fill: true, tension: 0.4, pointRadius: 4,
                    pointBackgroundColor: '#1e3a5f', pointBorderColor: '#f59e0b'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipDefaults, callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) } } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp'+(v/1000000).toFixed(0)+'M', color:'#6b7280' }, grid: { color:'rgba(0,0,0,0.04)' } }, x: { ticks: { color:'#6b7280' }, grid: { display:false } } }
            }
        });
    }

    // Chart Bagian Eksekutif (Horizontal Bar)
    const ctxB = document.getElementById('chartBagianEksekutif');
    if (ctxB) {
        new Chart(ctxB, {
            type: 'bar',
            data: {
                labels: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('bagian') : []),
                datasets: [{ data: @json(isset($chartSebaranBagian) ? $chartSebaranBagian->pluck('total') : []),
                    backgroundColor: ['#1e3a5f','#2d5986','#4a7ba7','#6fa3d0','#8ec5ea'],
                    borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ctx.parsed.x + ' pegawai' } } },
                scales: { x: { beginAtZero: true, ticks: { precision:0, color:'#6b7280' }, grid: { color:'rgba(0,0,0,0.04)' } }, y: { ticks: { color:'#6b7280' }, grid: { display:false } } }
            }
        });
    }

    // Chart Grading
    const ctxG = document.getElementById('chartGradingEksekutif');
    if (ctxG) {
        new Chart(ctxG, {
            type: 'bar',
            data: {
                labels: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('grading')->map(fn($g) => 'Grd '.$g) : []),
                datasets: [{ data: @json(isset($chartSebaranGrading) ? $chartSebaranGrading->pluck('total') : []),
                    backgroundColor: 'rgba(30,58,95,0.8)', borderRadius: 5 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipDefaults } },
                scales: { y: { beginAtZero:true, ticks: { precision:0, color:'#6b7280' }, grid: { color:'rgba(0,0,0,0.04)' } }, x: { ticks: { color:'#6b7280' }, grid: { display:false } } }
            }
        });
    }

    // Chart Gender
    const ctxJK = document.getElementById('chartGenderEksekutif');
    if (ctxJK) {
        new Chart(ctxJK, {
            type: 'doughnut',
            data: {
                labels: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('jenis_kelamin') : []),
                datasets: [{ data: @json(isset($chartJenisKelamin) ? $chartJenisKelamin->pluck('total') : []),
                    backgroundColor: ['#1e3a5f','#f59e0b'], borderColor:'#fff', borderWidth:4, hoverOffset:8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { position:'bottom', labels: { padding:12, usePointStyle:true, pointStyle:'circle' } },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => { const t=ctx.dataset.data.reduce((a,b)=>a+b,0); return ctx.label+': '+ctx.parsed+' ('+((ctx.parsed/t)*100).toFixed(1)+'%)'; } } }
                }
            }
        });
    }
});
</script>
@endpush
