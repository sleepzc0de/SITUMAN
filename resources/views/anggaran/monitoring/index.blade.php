@extends('layouts.app')
@section('title', 'Monitoring Anggaran')
@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Monitoring Anggaran</span>
</nav>
@endsection
@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Monitoring Anggaran</h1>
        <p class="page-subtitle">Pantau realisasi, SPP, dan sisa anggaran secara terintegrasi</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @hasrole('superadmin|admin')
        <form method="POST"
              action="{{ route('anggaran.monitoring.recalculate') }}"
              onsubmit="return confirm('Recalculate semua data anggaran dari SPP?\nProses ini mungkin memakan waktu beberapa saat.')">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11
                             11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Recalculate
            </button>
        </form>
        @endhasrole
        <button onclick="window.print()" class="btn btn-outline btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0
                         002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2
                         2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak
        </button>
        <a x-data x-bind:href="$store.monitoring ? $store.monitoring.exportUrl : '#'"
           class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                         1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
    </div>
</div>
@endsection

@push('scripts')
@php
    $jsGroupedData = $groupedData->map(fn($roData) => $roData->map(fn($item) => [
        'ro'                  => $item->ro,
        'kode_subkomponen'    => $item->kode_subkomponen,
        'kode_akun'           => $item->kode_akun,
        'program_kegiatan'    => $item->program_kegiatan,
        'pagu_anggaran'       => (float) $item->pagu_anggaran,
        'tagihan_outstanding' => (float) $item->tagihan_outstanding,
        'total_penyerapan'    => (float) $item->total_penyerapan,
        'sisa'                => (float) $item->sisa,
        'januari'             => (float) $item->januari,
        'februari'            => (float) $item->februari,
        'maret'               => (float) $item->maret,
        'april'               => (float) $item->april,
        'mei'                 => (float) $item->mei,
        'juni'                => (float) $item->juni,
        'juli'                => (float) $item->juli,
        'agustus'             => (float) $item->agustus,
        'september'           => (float) $item->september,
        'oktober'             => (float) $item->oktober,
        'november'            => (float) $item->november,
        'desember'            => (float) $item->desember,
    ])->values());
    $jsRecentSPP = $recentSPP->map(fn($s) => [
        'id'         => $s->id,
        'no_spp'     => $s->no_spp,
        'tgl_spp'    => $s->tgl_spp?->format('d/m/Y'),
        'uraian_spp' => $s->uraian_spp,
        'ro'         => $s->ro,
        'netto'      => (float) $s->netto,
        'status'     => $s->status,
    ]);
    $jsUsulanPending = $usulanPending->map(fn($u) => [
        'ro'           => $u->ro,
        'bulan'        => $u->bulan,
        'nilai_usulan' => (float) $u->nilai_usulan,
        'user'         => ['nama' => $u->user->nama ?? '-'],
    ]);
    $jsSubkomponenList = $subkomponenList;
    $jsRoNames         = collect($roList)->pluck('name', 'code');
@endphp
<script>
// ── Debounce utility (defined BEFORE alpine:init) ─────────────
function _debounce(fn, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

// ── Alpine Store + Component — registered inside alpine:init ──
document.addEventListener('alpine:init', () => {

    // Store
    Alpine.store('monitoring', {
        filters: { ro: 'all', subkomponen: 'all' },
        get exportUrl() {
            const p = new URLSearchParams();
            if (this.filters.ro          !== 'all') p.set('ro',          this.filters.ro);
            if (this.filters.subkomponen !== 'all') p.set('subkomponen', this.filters.subkomponen);
            const qs = p.toString();
            return `{{ route('anggaran.monitoring.export') }}${qs ? '?' + qs : ''}`;
        },
    });

    // Component
    Alpine.data('monitoringAnggaran', () => ({

        // ── State ────────────────────────────────────────────
        loading: false,
        filters: {
            ro:          '{{ $ro }}',
            subkomponen: '{{ $subkomponen }}',
            bulan:       '{{ $bulan }}',
        },
        totals: {
            pagu:        {{ (float) $totalPagu }},
            realisasi:   {{ (float) $totalRealisasi }},
            sisa:        {{ (float) $totalSisa }},
            outstanding: {{ (float) $totalOutstanding }},
        },
        sppStats: {
            total:      {{ $sppStats['total'] }},
            sudah_sp2d: {{ $sppStats['sudah_sp2d'] }},
            belum_sp2d: {{ $sppStats['belum_sp2d'] }},
            nilai_sp2d: {{ (float) $sppStats['nilai_sp2d'] }},
        },
        recentSPP:          @json($jsRecentSPP),
        usulanPending:      @json($jsUsulanPending),
        totalUsulanPending: {{ (float) $totalUsulanPending }},
        dokumenCount:       {{ $dokumenCount }},
        chartData:          @json($chartData),
        chartLabels:        @json($chartLabels),
        groupedData:        @json($jsGroupedData),
        subkomponenList:    @json($jsSubkomponenList),
        roNames:            @json($jsRoNames),
        chartInstance:      null,
        _debouncedFetch:    null,   // will hold the debounced wrapper

        // ── Computed ─────────────────────────────────────────
        get pctRealisasi() {
            return this.totals.pagu > 0
                ? (this.totals.realisasi / this.totals.pagu) * 100 : 0;
        },
        get pctSisa() {
            return this.totals.pagu > 0
                ? (this.totals.sisa / this.totals.pagu) * 100 : 0;
        },

        // ── Init ─────────────────────────────────────────────
        init() {
            // Build debounced fetch once — avoids window.debounce dependency
            this._debouncedFetch = _debounce(() => this.fetchData(), 400);

            Alpine.store('monitoring').filters.ro          = this.filters.ro;
            Alpine.store('monitoring').filters.subkomponen = this.filters.subkomponen;
            this.$nextTick(() => this.initChart());
        },

        // ── Filter ───────────────────────────────────────────
        onFilterChange() {
            Alpine.store('monitoring').filters.ro          = this.filters.ro;
            Alpine.store('monitoring').filters.subkomponen = this.filters.subkomponen;
            this._debouncedFetch();
        },
        resetFilters() {
            this.filters = { ro: 'all', subkomponen: 'all', bulan: 'all' };
            Alpine.store('monitoring').filters.ro          = 'all';
            Alpine.store('monitoring').filters.subkomponen = 'all';
            this.fetchData();
        },

        // ── Fetch ─────────────────────────────────────────────
        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    ro:          this.filters.ro,
                    subkomponen: this.filters.subkomponen,
                    bulan:       this.filters.bulan,
                });
                const res = await fetch(
                    `{{ route('anggaran.monitoring.data') }}?${params}`,
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name=csrf-token]').content,
                        }
                    }
                );
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();
                this.totals             = json.totals;
                this.sppStats           = json.sppStats;
                this.recentSPP          = json.recentSPP;
                this.usulanPending      = json.usulanPending;
                this.totalUsulanPending = json.totalUsulanPending;
                this.dokumenCount       = json.dokumenCount;
                this.subkomponenList    = json.subkomponenList;
                this.groupedData        = json.groupedData;
                this.roNames            = json.roNames;
                this.chartData          = json.chartData;
                this.updateChart();
            } catch (e) {
                console.error(e);
                if (typeof window.showToast === 'function') {
                    window.showToast('Gagal memuat data. Silakan coba lagi.', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        // ── Chart ─────────────────────────────────────────────
        initChart() {
            const ctx = document.getElementById('chartRealisasi');
            if (!ctx || typeof Chart === 'undefined') return;
            const isDark     = document.documentElement.classList.contains('dark');
            const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const labelColor = isDark ? '#9fb3c8' : '#627d98';
            this.chartInstance = new Chart(ctx, {
                data: {
                    labels: this.chartLabels,
                    datasets: [
                        {
                            type:            'bar',
                            label:           'Realisasi Bulanan',
                            data:            [...this.chartData],
                            backgroundColor: 'rgba(16,185,129,0.55)',
                            borderColor:     'rgba(16,185,129,0.85)',
                            borderWidth:     1.5,
                            borderRadius:    5,
                            borderSkipped:   false,
                            order:           2,
                        },
                        {
                            type:                 'line',
                            label:                'Kumulatif',
                            data:                 this.cumulative(this.chartData),
                            borderColor:          '#486581',
                            backgroundColor:      'rgba(72,101,129,0.07)',
                            borderWidth:          2,
                            pointRadius:          3,
                            pointHoverRadius:     5,
                            pointBackgroundColor: '#486581',
                            tension:              0.35,
                            fill:                 true,
                            order:                1,
                        }
                    ]
                },
                options: {
                    responsive:          true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display:  true,
                            position: 'top',
                            labels: {
                                color:    labelColor,
                                font:     { size: 11 },
                                boxWidth: 12,
                                padding:  14,
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx =>
                                    `${ctx.dataset.label}: ${
                                        typeof window.formatCurrencyShort === 'function'
                                            ? window.formatCurrencyShort(ctx.parsed.y)
                                            : ctx.parsed.y
                                    }`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid:  { color: gridColor },
                            ticks: { color: labelColor, font: { size: 10 } },
                        },
                        y: {
                            grid:  { color: gridColor },
                            ticks: {
                                color:    labelColor,
                                font:     { size: 10 },
                                callback: v =>
                                    typeof window.formatCurrencyShort === 'function'
                                        ? window.formatCurrencyShort(v) : v,
                            },
                            beginAtZero: true,
                        }
                    }
                }
            });
        },
        updateChart() {
            if (!this.chartInstance) return;
            this.chartInstance.data.datasets[0].data = [...this.chartData];
            this.chartInstance.data.datasets[1].data = this.cumulative(this.chartData);
            this.chartInstance.update('active');
        },

        // ── Helpers ───────────────────────────────────────────
        cumulative(data) {
            return data.reduce((acc, v, i) => {
                acc.push((acc[i - 1] ?? 0) + v);
                return acc;
            }, []);
        },
        fmt(v) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(v ?? 0));
        },
        fmtShort(v) {
            v = v ?? 0;
            if (v >= 1e12) return 'Rp ' + (v / 1e12).toFixed(1) + ' T';
            if (v >= 1e9)  return 'Rp ' + (v / 1e9).toFixed(1)  + ' M';
            if (v >= 1e6)  return 'Rp ' + (v / 1e6).toFixed(1)  + ' jt';
            if (v >= 1e3)  return 'Rp ' + (v / 1e3).toFixed(0)  + ' rb';
            return 'Rp ' + v;
        },
        capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        itemPct(item) {
            return item.pagu_anggaran > 0
                ? (item.total_penyerapan / item.pagu_anggaran) * 100 : 0;
        },
        roPct(roData) {
            const arr      = Array.isArray(roData) ? roData : Object.values(roData);
            const roRow    = arr.find(r => !r.kode_subkomponen && !r.kode_akun);
            const children = arr.filter(r => !r.kode_akun);
            const pagu     = roRow?.pagu_anggaran
                ?? children.reduce((s, r) => s + r.pagu_anggaran, 0);
            const real     = roRow?.total_penyerapan
                ?? children.reduce((s, r) => s + r.total_penyerapan, 0);
            return pagu > 0 ? (real / pagu) * 100 : 0;
        },
        roSummary(roData) {
            const arr   = Array.isArray(roData) ? roData : Object.values(roData);
            const roRow = arr.find(r => !r.kode_subkomponen && !r.kode_akun);
            if (roRow) {
                return {
                    pagu:        roRow.pagu_anggaran,
                    realisasi:   roRow.total_penyerapan,
                    sisa:        roRow.sisa,
                    outstanding: roRow.tagihan_outstanding,
                };
            }
            const children = arr.filter(r => !r.kode_akun);
            return {
                pagu:        children.reduce((s, r) => s + r.pagu_anggaran, 0),
                realisasi:   children.reduce((s, r) => s + r.total_penyerapan, 0),
                sisa:        children.reduce((s, r) => s + r.sisa, 0),
                outstanding: children.reduce((s, r) => s + r.tagihan_outstanding, 0),
            };
        },
        badgeClass(pct) {
            if (pct >= 80) return 'badge-success';
            if (pct >= 50) return 'badge-warning';
            return 'badge-danger';
        },
        barColor(pct) {
            if (pct >= 80) return 'bg-green-500';
            if (pct >= 50) return 'bg-amber-500';
            return 'bg-red-400';
        },
    }));
});
</script>
@endpush

@section('content')
<div
    class="space-y-6"
    x-data="monitoringAnggaran"
>
    {{-- ===== FILTERS ===== --}}
    <div class="card">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="input-group">
                <label class="input-label">RO</label>
                <select class="input-field" x-model="filters.ro" @change="onFilterChange()">
                    <option value="all">Semua RO</option>
                    @foreach($roList as $item)
                        <option value="{{ $item['code'] }}">
                            {{ $item['code'] }} — {{ Str::limit($item['name'], 35) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="input-group">
                <label class="input-label">Sub Komponen</label>
                <select class="input-field" x-model="filters.subkomponen" @change="onFilterChange()">
                    <option value="all">Semua Sub Komponen</option>
                    <template x-for="[code, name] in Object.entries(subkomponenList)" :key="code">
                        <option :value="code"
                                :selected="filters.subkomponen === code"
                                x-text="`${code} — ${String(name).substring(0, 35)}`">
                        </option>
                    </template>
                </select>
            </div>
            <div class="input-group">
                <label class="input-label">Bulan</label>
                <select class="input-field" x-model="filters.bulan" @change="onFilterChange()">
                    <option value="all">Semua Bulan</option>
                    @foreach($bulanList as $b)
                        <option value="{{ $b }}">{{ ucfirst($b) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group">
                <label class="input-label">&nbsp;</label>
                <button @click="resetFilters()" class="btn btn-ghost w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- ===== LOADING OVERLAY ===== --}}
    <div x-show="loading"
         x-transition:enter="transition-opacity duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center
                bg-white/60 dark:bg-navy-900/60 backdrop-blur-sm"
         style="display:none;">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 border-4 border-navy-200 border-t-navy-600
                        rounded-full animate-spin"></div>
            <p class="text-sm font-medium text-navy-600 dark:text-navy-300">Memuat data...</p>
        </div>
    </div>

    {{-- ===== KPI CARDS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Pagu --}}
        <div class="card bg-gradient-to-br from-navy-600 to-navy-800 border-0 text-white">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold text-navy-200 uppercase tracking-wider">
                        Total Pagu
                    </p>
                    <p class="text-lg lg:text-xl font-bold mt-1 truncate"
                       x-text="fmt(totals.pagu)"></p>
                    <p class="text-[11px] text-navy-300 mt-1">Anggaran ditetapkan</p>
                </div>
                <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343
                                 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1
                                 c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Realisasi --}}
        <div class="card border-0 text-white"
             :class="pctRealisasi >= 80
                ? 'bg-gradient-to-br from-emerald-500 to-emerald-700'
                : (pctRealisasi >= 50
                    ? 'bg-gradient-to-br from-yellow-500 to-yellow-600'
                    : 'bg-gradient-to-br from-red-500 to-red-700')">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold text-white/70 uppercase tracking-wider">
                        Total Realisasi
                    </p>
                    <p class="text-lg lg:text-xl font-bold mt-1 truncate"
                       x-text="fmt(totals.realisasi)"></p>
                    <div class="flex items-center gap-1.5 mt-2">
                        <div class="flex-1 h-1 bg-white/25 rounded-full overflow-hidden">
                            <div class="h-full bg-white rounded-full transition-all duration-700"
                                 :style="`width:${Math.min(pctRealisasi, 100)}%`"></div>
                        </div>
                        <span class="text-[11px] font-bold"
                              x-text="`${pctRealisasi.toFixed(1)}%`"></span>
                    </div>
                </div>
                <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Outstanding --}}
        <div class="card bg-gradient-to-br from-amber-500 to-orange-600 border-0 text-white">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold text-white/70 uppercase tracking-wider">
                        Outstanding
                    </p>
                    <p class="text-lg lg:text-xl font-bold mt-1 truncate"
                       x-text="fmt(totals.outstanding)"></p>
                    <p class="text-[11px] text-white/70 mt-1">Tagihan belum SP2D</p>
                </div>
                <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Sisa Anggaran --}}
        <div class="card bg-gradient-to-br from-purple-600 to-purple-800 border-0 text-white">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-semibold text-white/70 uppercase tracking-wider">
                        Sisa Anggaran
                    </p>
                    <p class="text-lg lg:text-xl font-bold mt-1 truncate"
                       x-text="fmt(totals.sisa)"></p>
                    <p class="text-[11px] text-white/70 mt-1"
                       x-text="`${pctSisa.toFixed(1)}% dari pagu`"></p>
                </div>
                <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0
                                 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ALERT THRESHOLD ===== --}}
    <div x-show="pctRealisasi < 50 && totals.pagu > 0"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="alert alert-warning"
         style="display:none;">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                     1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34
                     16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold">
                Penyerapan Anggaran Rendah —
                <span x-text="`${pctRealisasi.toFixed(1)}%`"></span>
            </p>
            <p class="text-sm mt-0.5">
                Segera tindaklanjuti
                <a href="{{ route('anggaran.usulan.index') }}"
                   class="underline font-medium hover:no-underline">
                    usulan penarikan
                </a>
                dan percepat realisasi kegiatan.
            </p>
        </div>
    </div>

    {{-- ===== CHART + PANEL INTEGRASI ===== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Chart --}}
        <div class="xl:col-span-2 card">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Tren Realisasi per Bulan</h3>
                    <p class="section-desc">
                        Penyerapan bulanan (SP2D) + kumulatif sepanjang tahun
                    </p>
                </div>
            </div>
            <div class="relative h-64">
                <canvas id="chartRealisasi"></canvas>
            </div>
        </div>

        {{-- Panel Kanan --}}
        <div class="flex flex-col gap-4">
            {{-- SPP Stats --}}
            <div class="card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="section-title">Status SPP</h3>
                    <a href="{{ route('anggaran.spp.index') }}"
                       class="text-xs font-medium text-navy-600 dark:text-navy-400 hover:underline">
                        Lihat semua →
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-gray-50 dark:bg-navy-700/50 rounded-xl py-3 px-2">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"
                           x-text="sppStats.total"></p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Total</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl py-3 px-2">
                        <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400"
                           x-text="sppStats.sudah_sp2d"></p>
                        <p class="text-[11px] text-emerald-600 dark:text-emerald-500 mt-0.5">SP2D</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl py-3 px-2">
                        <p class="text-2xl font-bold text-amber-700 dark:text-amber-400"
                           x-text="sppStats.belum_sp2d"></p>
                        <p class="text-[11px] text-amber-600 dark:text-amber-500 mt-0.5">Pending</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Nilai sudah SP2D</p>
                    <p class="text-base font-bold text-gray-900 dark:text-white mt-0.5"
                       x-text="fmt(sppStats.nilai_sp2d)"></p>
                </div>
            </div>

            {{-- Usulan Pending --}}
            <div class="card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="section-title">Usulan Pending</h3>
                    <a href="{{ route('anggaran.usulan.index') }}"
                       class="text-xs font-medium text-navy-600 dark:text-navy-400 hover:underline">
                        Lihat semua →
                    </a>
                </div>
                <template x-if="usulanPending.length === 0">
                    <div class="text-center py-4">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Tidak ada usulan pending
                        </p>
                    </div>
                </template>
                <template x-if="usulanPending.length > 0">
                    <div>
                        <div class="space-y-2">
                            <template x-for="(u, i) in usulanPending.slice(0, 3)" :key="i">
                                <div class="flex items-center justify-between gap-2 p-2.5
                                            bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-gray-800
                                                   dark:text-gray-200 truncate"
                                           x-text="`RO ${u.ro} · ${capitalize(u.bulan)}`"></p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate"
                                           x-text="u.user.nama"></p>
                                    </div>
                                    <span class="text-xs font-bold text-amber-700
                                                 dark:text-amber-400 flex-shrink-0"
                                          x-text="fmtShort(u.nilai_usulan)"></span>
                                </div>
                            </template>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700
                                    flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Total nilai pending
                            </span>
                            <span class="text-sm font-bold text-amber-600 dark:text-amber-400"
                                  x-text="fmt(totalUsulanPending)"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Dokumen Capaian --}}
            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="section-title">Dokumen Capaian</h3>
                        <p class="mt-1.5">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white"
                                  x-text="dokumenCount"></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                dokumen terupload
                            </span>
                        </p>
                    </div>
                    <a href="{{ route('anggaran.dokumen.index') }}" class="btn btn-outline btn-sm">
                        Kelola
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SPP TERBARU ===== --}}
    <div class="card" x-show="recentSPP.length > 0" style="display:none;"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="section-header">
            <div>
                <h3 class="section-title">SPP Terbaru</h3>
                <p class="section-desc">10 transaksi SPP terakhir yang tercatat</p>
            </div>
            <a href="{{ route('anggaran.spp.index') }}" class="btn btn-outline btn-sm">
                Lihat Semua SPP
            </a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. SPP</th>
                        <th>Tanggal</th>
                        <th>Uraian</th>
                        <th>RO</th>
                        <th class="text-right">Netto</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="spp in recentSPP" :key="spp.id">
                        <tr>
                            <td class="font-mono text-xs whitespace-nowrap"
                                x-text="spp.no_spp"></td>
                            <td class="text-xs whitespace-nowrap"
                                x-text="spp.tgl_spp ?? '-'"></td>
                            <td>
                                <span class="line-clamp-2 text-xs"
                                      x-text="spp.uraian_spp"></span>
                            </td>
                            <td>
                                <span class="badge badge-info" x-text="spp.ro"></span>
                            </td>
                            <td class="text-right font-semibold text-sm whitespace-nowrap"
                                x-text="fmt(spp.netto)"></td>
                            <td class="text-center">
                                <span :class="spp.status === 'Tagihan Telah SP2D'
                                              ? 'badge badge-success'
                                              : 'badge badge-warning'"
                                      x-text="spp.status === 'Tagihan Telah SP2D'
                                              ? 'SP2D' : 'Pending'">
                                </span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== TABEL DETAIL PER RO ===== --}}
    <template x-if="Object.keys(groupedData).length === 0">
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0
                                 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                                 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="empty-state-title">Tidak ada data anggaran</p>
                <p class="empty-state-desc">
                    Sesuaikan filter atau tambahkan data anggaran terlebih dahulu.
                </p>
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-primary btn-sm mt-3">
                    Kelola Data Anggaran
                </a>
            </div>
        </div>
    </template>

    <template x-for="[roCode, roData] in Object.entries(groupedData)" :key="roCode">
        <div class="card" x-data="{ expanded: true }">
            {{-- RO Header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="badge badge-info font-mono"
                              x-text="`RO ${roCode}`"></span>
                        <span :class="`badge ${badgeClass(roPct(roData))}`"
                              x-text="`${roPct(roData).toFixed(1)}%`"></span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-snug"
                        x-text="roNames[roCode] ?? roCode"></h3>
                    <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-2
                                text-xs text-gray-500 dark:text-gray-400">
                        <span>Pagu:
                            <strong class="text-gray-900 dark:text-white"
                                    x-text="fmt(roSummary(roData).pagu)"></strong>
                        </span>
                        <span>Realisasi:
                            <strong class="text-emerald-600 dark:text-emerald-400"
                                    x-text="fmt(roSummary(roData).realisasi)"></strong>
                        </span>
                        <template x-if="roSummary(roData).outstanding > 0">
                            <span>Outstanding:
                                <strong class="text-amber-600 dark:text-amber-400"
                                        x-text="fmt(roSummary(roData).outstanding)"></strong>
                            </span>
                        </template>
                        <span>Sisa:
                            <strong class="text-purple-600 dark:text-purple-400"
                                    x-text="fmt(roSummary(roData).sisa)"></strong>
                        </span>
                    </div>
                    <div class="mt-2.5 progress-bar-wrap">
                        <div class="progress-bar transition-all duration-700"
                             :class="barColor(roPct(roData))"
                             :style="`width:${Math.min(roPct(roData), 100)}%`"></div>
                    </div>
                </div>
                <button @click="expanded = !expanded"
                        class="btn btn-ghost btn-sm flex-shrink-0 self-start">
                    <svg class="w-4 h-4 transition-transform duration-200"
                         :class="{ 'rotate-180': !expanded }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span x-text="expanded ? 'Sembunyikan' : 'Tampilkan'"></span>
                </button>
            </div>

            {{-- Tabel --}}
            <div x-show="expanded" x-collapse>
                <div class="table-wrapper">
                    <table class="table text-xs">
                        <thead>
                            <tr>
                                <th class="min-w-52">Sub Komponen / Akun</th>
                                <th class="text-right whitespace-nowrap">Pagu</th>
                                <template x-for="bl in ['Jan','Feb','Mar','Apr','Mei','Jun',
                                                        'Jul','Agu','Sep','Okt','Nov','Des']"
                                          :key="bl">
                                    <th class="text-right whitespace-nowrap" x-text="bl"></th>
                                </template>
                                <th class="text-right whitespace-nowrap">Outstanding</th>
                                <th class="text-right whitespace-nowrap">Realisasi</th>
                                <th class="text-right whitespace-nowrap">Sisa</th>
                                <th class="text-center whitespace-nowrap">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in roData" :key="idx">
                                <tr :class="{
                                        'bg-navy-50 dark:bg-navy-700/40 font-bold':
                                            !item.kode_akun && !item.kode_subkomponen,
                                        'bg-gray-50 dark:bg-navy-800/60 font-semibold':
                                            !item.kode_akun && item.kode_subkomponen,
                                    }"
                                    class="hover:bg-blue-50/40 dark:hover:bg-navy-700/20
                                           transition-colors duration-100">
                                    <td :class="{
                                            'pl-3':  !item.kode_akun && !item.kode_subkomponen,
                                            'pl-6':  !item.kode_akun && item.kode_subkomponen,
                                            'pl-10':  item.kode_akun,
                                        }"
                                        class="py-2.5">
                                        <span x-show="item.kode_akun || item.kode_subkomponen"
                                              class="font-mono text-[10px] text-gray-400
                                                     mr-1.5 select-all"
                                              x-text="item.kode_akun ?? item.kode_subkomponen">
                                        </span>
                                        <span :class="(!item.kode_akun && !item.kode_subkomponen)
                                                ? 'text-navy-700 dark:text-navy-300'
                                                : 'text-gray-700 dark:text-gray-300'"
                                              x-text="String(item.program_kegiatan ?? '').substring(0, 60)">
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-right font-semibold whitespace-nowrap
                                               text-gray-900 dark:text-white"
                                        x-text="fmt(item.pagu_anggaran)"></td>
                                    <template x-for="bf in ['januari','februari','maret','april',
                                                            'mei','juni','juli','agustus',
                                                            'september','oktober','november','desember']"
                                              :key="bf">
                                        <td class="py-2.5 text-right whitespace-nowrap"
                                            :class="item[bf] > 0
                                                ? 'text-emerald-600 dark:text-emerald-400'
                                                : 'text-gray-300 dark:text-gray-600'"
                                            x-text="item[bf] > 0 ? fmt(item[bf]) : '—'">
                                        </td>
                                    </template>
                                    <td class="py-2.5 text-right whitespace-nowrap"
                                        :class="item.tagihan_outstanding > 0
                                            ? 'text-amber-600 dark:text-amber-400 font-medium'
                                            : 'text-gray-300 dark:text-gray-600'"
                                        x-text="item.tagihan_outstanding > 0
                                            ? fmt(item.tagihan_outstanding) : '—'">
                                    </td>
                                    <td class="py-2.5 text-right font-semibold whitespace-nowrap
                                               text-emerald-600 dark:text-emerald-400"
                                        x-text="fmt(item.total_penyerapan)"></td>
                                    <td class="py-2.5 text-right font-semibold whitespace-nowrap
                                               text-purple-600 dark:text-purple-400"
                                        x-text="fmt(item.sisa)"></td>
                                    <td class="py-2.5 text-center">
                                        <span :class="`badge ${badgeClass(itemPct(item))}`"
                                              x-text="`${itemPct(item).toFixed(1)}%`"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
