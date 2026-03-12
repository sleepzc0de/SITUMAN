@extends('layouts.app')
@section('title', 'Proyeksi Mutasi Pegawai')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <nav class="flex items-center gap-1.5 text-xs mb-2">
            <span class="text-gray-400 dark:text-gray-500">Kepegawaian</span>
            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-navy-600 dark:text-navy-400 font-semibold">Proyeksi Mutasi</span>
        </nav>
        <h1 class="page-title">Proyeksi Mutasi <span class="text-gradient animate-gradient">Pegawai</span></h1>
        <p class="page-subtitle mt-1">Analisis cerdas berdasarkan masa jabatan & data kepegawaian · Tahun <span id="header-tahun" class="font-semibold text-gray-700 dark:text-gray-300">{{ $tahun }}</span></p>
    </div>
    <button id="btn-export"
            class="btn btn-secondary self-start flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        Export Excel
    </button>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- ── Stat Cards ── --}}
    <div id="stats-container" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @include('kepegawaian.mutasi._stats', ['proyeksi' => $proyeksi])
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-navy-800
                border border-gray-100 dark:border-navy-700 shadow-sm">

        {{-- Top accent line --}}
        <div class="absolute top-0 inset-x-0 h-0.5 bg-gradient-to-r from-navy-500 via-gold-400 to-navy-500"></div>

        <div class="px-5 py-4">
            <div class="flex flex-col lg:flex-row gap-3 items-start lg:items-end">

                {{-- Search --}}
                <div class="flex-1 min-w-0 w-full lg:w-auto">
                    <label class="input-label">Cari Pegawai</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4
                                    text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" id="filter-search" value="{{ $search ?? '' }}"
                               placeholder="Nama atau NIP…"
                               class="input-field pl-10 pr-10 w-full"
                               autocomplete="off">
                        <button id="btn-clear-search"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300
                                       hover:text-gray-500 dark:hover:text-gray-300 transition-colors
                                       {{ ($search ?? '') ? '' : 'hidden' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tahun --}}
                <div class="w-full sm:w-auto">
                    <label class="input-label">Tahun</label>
                    <select id="filter-tahun" class="input-field w-full sm:w-28">
                        @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Bagian --}}
                <div class="w-full sm:w-auto">
                    <label class="input-label">Unit / Bagian</label>
                    <select id="filter-bagian" class="input-field w-full sm:w-44">
                        <option value="">Semua Bagian</option>
                        @foreach($bagianList as $b)
                        <option value="{{ $b }}" @selected(($bagian ?? '') == $b)>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Prioritas --}}
                <div class="w-full sm:w-auto">
                    <label class="input-label">Prioritas</label>
                    <div class="flex items-center gap-2" id="filter-prioritas-group">
                        @foreach(['' => 'Semua', 'tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $val => $lbl)
                        <button type="button"
                                data-value="{{ $val }}"
                                class="prioritas-btn px-3 py-2 rounded-xl text-xs font-semibold
                                       border-2 transition-all duration-150 whitespace-nowrap
                                       {{ ($prioritas ?? '') === $val
                                           ? 'border-navy-600 bg-navy-600 text-white shadow-sm'
                                           : 'border-gray-200 dark:border-navy-600 text-gray-600 dark:text-gray-400 hover:border-navy-400 hover:text-navy-600 dark:hover:border-navy-400 dark:hover:text-navy-300' }}">
                            {{ $lbl }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Active filters & result count --}}
            <div class="flex items-center justify-between mt-3 pt-3
                        border-t border-gray-100 dark:border-navy-700">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Menampilkan</span>
                    <span id="result-count"
                          class="px-2 py-0.5 rounded-full text-xs font-bold
                                 bg-navy-100 dark:bg-navy-700 text-navy-700 dark:text-navy-300">
                        {{ $proyeksi->count() }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">pegawai</span>
                </div>

                {{-- Loading indicator --}}
                <div id="filter-loading" class="hidden items-center gap-2">
                    <div class="w-3.5 h-3.5 border-2 border-navy-300 border-t-navy-600
                                rounded-full animate-spin"></div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Memuat…</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Content Grid ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

        {{-- Table (3/4) --}}
        <div class="xl:col-span-3">
            <div class="overflow-hidden rounded-2xl bg-white dark:bg-navy-800
                        border border-gray-100 dark:border-navy-700 shadow-sm">

                {{-- Table header --}}
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-navy-700
                            bg-gray-50/80 dark:bg-navy-750">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-4 rounded-full bg-gradient-to-b from-navy-500 to-navy-700"></div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            Daftar Proyeksi Mutasi
                        </h3>
                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-1" id="table-year-label">
                            · {{ $tahun }}
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-navy-700/80">
                                <th class="pl-6 pr-3 py-3 text-left text-xs font-bold text-gray-400
                                           dark:text-gray-500 uppercase tracking-widest w-10">#</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-400
                                           dark:text-gray-500 uppercase tracking-widest">Pegawai</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-400
                                           dark:text-gray-500 uppercase tracking-widest">Jabatan</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-400
                                           dark:text-gray-500 uppercase tracking-widest min-w-[140px]">Masa Jabatan</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-400
                                           dark:text-gray-500 uppercase tracking-widest">Prioritas</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-400
                                           dark:text-gray-500 uppercase tracking-widest">Waktu</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-400
                                           dark:text-gray-500 uppercase tracking-widest">Pertimbangan</th>
                                <th class="pl-3 pr-6 py-3 text-left text-xs font-bold text-gray-400
                                           dark:text-gray-500 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="divide-y divide-gray-50 dark:divide-navy-700/50">
                            @include('kepegawaian.mutasi._table', ['proyeksi' => $proyeksi])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar (1/4) --}}
        <div class="xl:col-span-1 space-y-5">

            {{-- Sebaran Bagian --}}
            <div class="rounded-2xl bg-white dark:bg-navy-800
                        border border-gray-100 dark:border-navy-700 shadow-sm overflow-hidden">
                <div class="px-4 py-3.5 border-b border-gray-100 dark:border-navy-700
                            bg-gray-50/80 dark:bg-navy-750">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-4 rounded-full bg-gradient-to-b from-gold-400 to-gold-600"></div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Sebaran Bagian</h3>
                    </div>
                </div>
                <div class="p-4" id="stats-bagian-container">
                    @include('kepegawaian.mutasi._stats_bagian', ['statsBagian' => $statsBagian, 'proyeksi' => $proyeksi])
                </div>
            </div>

            {{-- Kriteria --}}
            <div class="rounded-2xl border border-navy-200 dark:border-navy-700
                        bg-gradient-to-br from-navy-50 to-white dark:from-navy-800/80 dark:to-navy-800
                        overflow-hidden shadow-sm">
                <div class="px-4 py-3.5 border-b border-navy-100 dark:border-navy-700">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-4 rounded-full bg-gradient-to-b from-navy-500 to-navy-700"></div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Kriteria Penilaian</h3>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @php
                    $kriteria = [
                        ['dot' => 'bg-rose-400', 'color' => 'text-rose-600 dark:text-rose-400',
                         'label' => 'Tinggi (≥5)',
                         'desc' => 'Masa jabatan ≥24 bln atau pensiun ≤12 bln'],
                        ['dot' => 'bg-orange-400', 'color' => 'text-orange-600 dark:text-orange-400',
                         'label' => 'Sedang (3–4)',
                         'desc' => 'Masa jabatan 18–24 bln atau pensiun 12–24 bln'],
                        ['dot' => 'bg-amber-400', 'color' => 'text-amber-600 dark:text-amber-400',
                         'label' => 'Rendah (<3)',
                         'desc' => 'Pejabat struktural, masa jabatan normal'],
                    ];
                    @endphp
                    @foreach($kriteria as $k)
                    <div class="flex items-start gap-3 p-3 rounded-xl
                                bg-white dark:bg-navy-700/50
                                border border-gray-100 dark:border-navy-600">
                        <div class="w-2.5 h-2.5 rounded-full {{ $k['dot'] }} mt-1 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-bold {{ $k['color'] }}">{{ $k['label'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">
                                {{ $k['desc'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                    <div class="flex items-start gap-3 p-3 rounded-xl
                                bg-gold-50 dark:bg-gold-900/10
                                border border-gold-100 dark:border-gold-900/20 mt-1">
                        <svg class="w-3.5 h-3.5 text-gold-500 mt-0.5 flex-shrink-0"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Mutasi umumnya dilaksanakan <strong class="text-gold-700 dark:text-gold-400">April</strong>
                            atau <strong class="text-gold-700 dark:text-gold-400">Oktober</strong>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── State ─────────────────────────────────────────── */
    let state = {
        search:    '{{ $search ?? '' }}',
        tahun:     '{{ $tahun }}',
        bagian:    '{{ $bagian ?? '' }}',
        prioritas: '{{ $prioritas ?? '' }}',
    };

    let debounceTimer = null;
    let abortCtrl    = null;

    /* ── Elements ──────────────────────────────────────── */
    const elSearch       = document.getElementById('filter-search');
    const elClearSearch  = document.getElementById('btn-clear-search');
    const elTahun        = document.getElementById('filter-tahun');
    const elBagian       = document.getElementById('filter-bagian');
    const elPrioGroup    = document.getElementById('filter-prioritas-group');
    const elTableBody    = document.getElementById('table-body');
    const elStats        = document.getElementById('stats-container');
    const elStatsBagian  = document.getElementById('stats-bagian-container');
    const elCount        = document.getElementById('result-count');
    const elLoading      = document.getElementById('filter-loading');
    const elYearLabel    = document.getElementById('table-year-label');
    const elHeaderTahun  = document.getElementById('header-tahun');
    const elBtnExport    = document.getElementById('btn-export');

    /* ── Fetch ─────────────────────────────────────────── */
    function fetchData(immediate = false) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(_doFetch, immediate ? 0 : 380);
    }

    function _doFetch() {
        if (abortCtrl) abortCtrl.abort();
        abortCtrl = new AbortController();

        elLoading.classList.remove('hidden');
        elLoading.classList.add('flex');
        elTableBody.style.opacity = '0.4';

        const params = new URLSearchParams({
            search:    state.search,
            tahun:     state.tahun,
            bagian:    state.bagian,
            prioritas: state.prioritas,
        });

        /* Update URL tanpa reload */
        window.history.replaceState(null, '', '?' + params.toString());

        /* Update export link */
        elBtnExport.href = '?' + params.toString() + '&export=1';

        /* Update label tahun */
        elYearLabel.textContent  = '· ' + state.tahun;
        elHeaderTahun.textContent = state.tahun;

        fetch('{{ route('kepegawaian.mutasi') }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal:  abortCtrl.signal,
        })
        .then(r => r.json())
        .then(data => {
            /* Table */
            elTableBody.innerHTML    = data.html;
            elTableBody.style.opacity = '1';

            /* Stats */
            elStats.innerHTML        = data.stats;
            elStatsBagian.innerHTML  = data.statsBagian;

            /* Count */
            elCount.textContent      = data.count;

            /* Animate rows */
            elTableBody.querySelectorAll('tr').forEach((row, i) => {
                row.style.opacity   = '0';
                row.style.transform = 'translateY(6px)';
                setTimeout(() => {
                    row.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                    row.style.opacity    = '1';
                    row.style.transform  = 'translateY(0)';
                }, i * 30);
            });
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                elTableBody.style.opacity = '1';
                console.error('Fetch error:', err);
            }
        })
        .finally(() => {
            elLoading.classList.add('hidden');
            elLoading.classList.remove('flex');
        });
    }

    /* ── Event Listeners ───────────────────────────────── */
    /* Search — debounce 380ms */
    elSearch.addEventListener('input', function () {
        state.search = this.value.trim();
        elClearSearch.classList.toggle('hidden', !state.search);
        fetchData();
    });

    /* Clear search */
    elClearSearch.addEventListener('click', function () {
        elSearch.value = '';
        state.search   = '';
        elClearSearch.classList.add('hidden');
        fetchData(true);
        elSearch.focus();
    });

    /* Tahun */
    elTahun.addEventListener('change', function () {
        state.tahun = this.value;
        fetchData(true);
    });

    /* Bagian */
    elBagian.addEventListener('change', function () {
        state.bagian = this.value;
        fetchData(true);
    });

    /* Prioritas pills */
    elPrioGroup.addEventListener('click', function (e) {
        const btn = e.target.closest('.prioritas-btn');
        if (!btn) return;

        state.prioritas = btn.dataset.value;

        /* Update active style */
        elPrioGroup.querySelectorAll('.prioritas-btn').forEach(b => {
            const active = b.dataset.value === state.prioritas;
            b.classList.toggle('border-navy-600',  active);
            b.classList.toggle('bg-navy-600',      active);
            b.classList.toggle('text-white',        active);
            b.classList.toggle('shadow-sm',         active);
            b.classList.toggle('border-gray-200',  !active);
            b.classList.toggle('dark:border-navy-600', !active);
            b.classList.toggle('text-gray-600',    !active);
            b.classList.toggle('dark:text-gray-400',!active);
        });

        fetchData(true);
    });

    /* Export */
    elBtnExport.addEventListener('click', function () {
        const params = new URLSearchParams({
            search:    state.search,
            tahun:     state.tahun,
            bagian:    state.bagian,
            prioritas: state.prioritas,
            export:    '1',
        });
        window.location.href = '{{ route('kepegawaian.mutasi') }}?' + params.toString();
    });

})();
</script>
@endpush
