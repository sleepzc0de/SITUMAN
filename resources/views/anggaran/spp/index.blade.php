@extends('layouts.app')

@section('title', 'Data SPP')

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Data SPP</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Data SPP</h1>
        <p class="page-subtitle">Kelola Surat Perintah Pembayaran dan pantau status tagihan</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('anggaran.monitoring.index') }}" class="btn btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Monitoring
        </a>
        <a href="{{ route('anggaran.spp.create') }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah SPP
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-5" x-data="sppIndex()" x-init="init()">

    {{-- ===== STATS ===== --}}
    <div id="stats-wrapper">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" id="stats-cards">
            <div class="stat-card border-l-4 border-blue-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-card-label">Total Bruto</p>
                        <p class="stat-card-value text-xl" id="stat-bruto">{{ format_rupiah_short($totalBruto) }}</p>
                        <p class="stat-card-sub text-gray-400 text-xs" id="stat-bruto-full">{{ format_rupiah($totalBruto) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card border-l-4 border-navy-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-card-label">Total Netto</p>
                        <p class="stat-card-value text-xl" id="stat-netto">{{ format_rupiah_short($totalNetto) }}</p>
                        <p class="stat-card-sub text-gray-400 text-xs" id="stat-netto-full">{{ format_rupiah($totalNetto) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-navy-100 dark:bg-navy-700 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card border-l-4 border-emerald-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-card-label">Sudah SP2D</p>
                        <p class="stat-card-value text-xl text-emerald-600 dark:text-emerald-400" id="stat-sp2d">{{ format_rupiah_short($totalSP2D) }}</p>
                        <p class="stat-card-sub text-emerald-500 text-xs" id="stat-sp2d-pct">
                            @if($totalNetto > 0){{ formatPersen($totalSP2D, $totalNetto) }}@endif
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card border-l-4 border-orange-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-card-label">Belum SP2D</p>
                        <p class="stat-card-value text-xl text-orange-600 dark:text-orange-400" id="stat-belum">{{ format_rupiah_short($totalBelumSP2D) }}</p>
                        <p class="stat-card-sub text-orange-500 text-xs" id="stat-belum-pct">
                            @if($totalNetto > 0){{ formatPersen($totalBelumSP2D, $totalNetto) }}@endif
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Bar SP2D --}}
        <div class="card-flat mt-4" id="progress-wrap"
             style="{{ $totalNetto <= 0 ? 'display:none' : '' }}">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Progress SP2D</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white" id="progress-pct">
                    {{ $totalNetto > 0 ? formatPersen($totalSP2D, $totalNetto) : '0%' }}
                </p>
            </div>
            <div class="progress-bar-wrap">
                <div id="progress-bar"
                     class="h-2 rounded-full transition-all duration-700 {{ $totalNetto > 0 ? progress_bar_color(($totalSP2D / $totalNetto) * 100) : 'bg-gray-300' }}"
                     style="width: {{ $totalNetto > 0 ? min(100, ($totalSP2D / $totalNetto) * 100) : 0 }}%">
                </div>
            </div>
            <div class="flex justify-between mt-1.5 text-xs text-gray-400">
                <span>SP2D: <span id="progress-sp2d">{{ format_rupiah_short($totalSP2D) }}</span></span>
                <span>Outstanding: <span id="progress-belum">{{ format_rupiah_short($totalBelumSP2D) }}</span></span>
            </div>
        </div>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="card-flat">
        <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" @submit.prevent>

            <div class="input-group">
                <label class="input-label">Bulan</label>
                <select name="bulan" class="input-field" @change="onFilterChange()">
                    <option value="all">Semua Bulan</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan }}" {{ request('bulan') == $bulan ? 'selected' : '' }}>
                            {{ ucfirst($bulan) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Status</label>
                <select name="status" class="input-field" @change="onFilterChange()">
                    <option value="all">Semua Status</option>
                    <option value="Tagihan Telah SP2D" {{ request('status') == 'Tagihan Telah SP2D' ? 'selected' : '' }}>
                        Sudah SP2D
                    </option>
                    <option value="Tagihan Belum SP2D" {{ request('status') == 'Tagihan Belum SP2D' ? 'selected' : '' }}>
                        Belum SP2D
                    </option>
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">RO</label>
                <select name="ro" class="input-field" @change="onFilterChange()">
                    <option value="all">Semua RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}" {{ request('ro') == $ro ? 'selected' : '' }}>
                            {{ $ro }} – {{ get_ro_name($ro) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Cari</label>
                <div class="flex gap-2">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="No SPP, Uraian, PIC…"
                           class="input-field"
                           @input="onSearchInput()">
                    <button type="button"
                            x-show="hasFilters"
                            x-transition
                            @click="resetFilter()"
                            class="btn btn-ghost btn-icon flex-shrink-0"
                            title="Reset filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="card-flat overflow-hidden">

        {{-- Info bar filter aktif --}}
        <div x-show="hasFilters"
             x-transition
             class="px-5 py-2 bg-navy-50 dark:bg-navy-700/40 border-b border-gray-100 dark:border-navy-700
                    text-xs text-navy-600 dark:text-navy-300 flex items-center gap-2">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter aktif — menampilkan <strong class="mx-1" x-text="totalData"></strong> data
        </div>

        {{-- Loading skeleton --}}
        <div x-show="loading"
             x-transition:enter="transition-opacity duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="p-5 space-y-3">
            @for($i = 0; $i < 5; $i++)
            <div class="flex gap-4 items-center">
                <div class="skeleton h-4 w-6 rounded"></div>
                <div class="skeleton h-4 w-32 rounded"></div>
                <div class="skeleton h-4 w-20 rounded"></div>
                <div class="skeleton h-4 w-16 rounded"></div>
                <div class="skeleton h-4 flex-1 rounded"></div>
                <div class="skeleton h-4 w-24 rounded"></div>
                <div class="skeleton h-4 w-12 rounded"></div>
                <div class="skeleton h-4 w-28 rounded"></div>
                <div class="skeleton h-5 w-20 rounded-full"></div>
                <div class="skeleton h-4 w-20 rounded"></div>
            </div>
            @endfor
        </div>

        {{-- Tabel konten (diupdate AJAX) --}}
        <div id="table-container" x-show="!loading">
            @include('anggaran.spp._table_content')
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function sppIndex() {
    return {
        loading:    false,
        hasFilters: {{ request()->hasAny(['bulan','status','ro','search']) ? 'true' : 'false' }},
        totalData:  {{ $spps->total() }},
        _searchTimer: null,

        // ── Init ──────────────────────────────────────────────────
        init() {
            this.$nextTick(() => this._bindPagination());

            // Back/forward browser
            window.addEventListener('popstate', (e) => {
                if (e.state?.spp !== undefined) {
                    this._applyParamsToForm(new URLSearchParams(e.state.spp));
                    this._fetch(new URLSearchParams(e.state.spp), false);
                }
            });

            // Simpan state awal
            const initParams = this._buildParams({{ request('page', 1) }});
            history.replaceState({ spp: initParams.toString() }, '');
        },

        // ── Handlers ─────────────────────────────────────────────
        onFilterChange() {
            clearTimeout(this._searchTimer);
            this._fetch(this._buildParams(1), true);
        },

        onSearchInput() {
            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(() => {
                this._fetch(this._buildParams(1), true);
            }, 420);
        },

        resetFilter() {
            const form = document.getElementById('filter-form');
            form.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
            form.querySelector('input[name="search"]').value = '';
            this._fetch(this._buildParams(1), true);
        },

        // ── Build URLSearchParams dari form ───────────────────────
        _buildParams(page = 1) {
            const form   = document.getElementById('filter-form');
            const params = new URLSearchParams();
            form.querySelectorAll('select').forEach(s => {
                if (s.value && s.value !== 'all') params.set(s.name, s.value);
            });
            const search = form.querySelector('input[name="search"]').value.trim();
            if (search) params.set('search', search);
            if (page > 1) params.set('page', page);
            return params;
        },

        // ── Core AJAX fetch ───────────────────────────────────────
        _fetch(params, pushState = true) {
            this.loading = true;

            const url = '{{ route('anggaran.spp.index') }}?' + params.toString();

            axios.get(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(({ data }) => {
                // Update tabel
                const container = document.getElementById('table-container');
                container.innerHTML = data.table;
                this.$nextTick(() => {
                    Alpine.initTree(container);
                    this._bindPagination();
                });

                // Update stats
                this._updateStats(data.stats);

                // Update state Alpine
                this.totalData  = data.total;
                this.hasFilters = data.hasFilters;

                if (pushState) {
                    history.pushState({ spp: params.toString() }, '', url);
                }
            })
            .catch(() => {
                showToast('Gagal memuat data, silakan coba lagi.', 'error');
            })
            .finally(() => {
                this.loading = false;
            });
        },

        // ── Update stats cards & progress bar ─────────────────────
        _updateStats(stats) {
            const fmt      = window.formatCurrencyShort;
            const fmtFull  = window.formatCurrency;
            const fmtPct   = (a, b) => b > 0 ? (a / b * 100).toFixed(1) + '%' : '0%';
            const idNumber = v => new Intl.NumberFormat('id-ID').format(v);

            const bruto  = stats.totalBruto;
            const netto  = stats.totalNetto;
            const sp2d   = stats.totalSP2D;
            const belum  = stats.totalBelumSP2D;
            const pct    = netto > 0 ? (sp2d / netto) * 100 : 0;

            // Stat cards
            this._setText('stat-bruto',      fmt(bruto));
            this._setText('stat-bruto-full', fmtFull(bruto));
            this._setText('stat-netto',      fmt(netto));
            this._setText('stat-netto-full', fmtFull(netto));
            this._setText('stat-sp2d',       fmt(sp2d));
            this._setText('stat-sp2d-pct',   netto > 0 ? fmtPct(sp2d, netto) : '');
            this._setText('stat-belum',      fmt(belum));
            this._setText('stat-belum-pct',  netto > 0 ? fmtPct(belum, netto) : '');

            // Progress bar
            const wrap = document.getElementById('progress-wrap');
            if (wrap) {
                wrap.style.display = netto > 0 ? '' : 'none';
                this._setText('progress-pct',  fmtPct(sp2d, netto));
                this._setText('progress-sp2d', fmt(sp2d));
                this._setText('progress-belum', fmt(belum));

                const bar = document.getElementById('progress-bar');
                if (bar) {
                    bar.style.width = Math.min(100, pct) + '%';
                    // Update warna progress
                    bar.className = bar.className.replace(/bg-\w+-\d+/g, '');
                    bar.classList.add(pct >= 80 ? 'bg-green-500' : pct >= 50 ? 'bg-amber-500' : 'bg-red-400');
                }
            }
        },

        _setText(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        },

        // ── Intercept pagination links ────────────────────────────
        _bindPagination() {
            const container = document.getElementById('table-container');
            if (!container) return;
            container.querySelectorAll('nav[role="navigation"] a, [data-pagination] a').forEach(link => {
                if (link.dataset.ajaxBound) return;
                link.dataset.ajaxBound = '1';
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page   = new URL(link.href).searchParams.get('page') || 1;
                    const params = this._buildParams(page);
                    this._fetch(params, true);
                    document.getElementById('table-container')
                            ?.closest('.card-flat')
                            ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });
        },

        // ── Restore form dari URLSearchParams ─────────────────────
        _applyParamsToForm(params) {
            const form = document.getElementById('filter-form');
            form.querySelectorAll('select').forEach(s => {
                s.value = params.get(s.name) || 'all';
            });
            const searchEl = form.querySelector('input[name="search"]');
            if (searchEl) searchEl.value = params.get('search') || '';
        },
    };
}
</script>
@endpush
