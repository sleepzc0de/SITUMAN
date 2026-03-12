@extends('layouts.app')
@section('title', 'Kelola Data Pegawai')

@section('content')
<div
    class="space-y-6 animate-fade-in"
    x-data="pegawaiIndex()"
    x-init="init()"
>

    {{-- ═══════════════════════════════════════════════════════
         PAGE HEADER
    ════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Kelola Data Pegawai', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="page-title mt-1">Kelola Data Pegawai</h1>
            <p class="page-subtitle">Manajemen lengkap data seluruh pegawai — tambah, ubah, hapus, export & import</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
            <a href="{{ route('kepegawaian.pegawai.template') }}" class="btn btn-ghost btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Template
            </a>
            <a href="{{ route('kepegawaian.pegawai.import-form') }}" class="btn btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import
            </a>
            {{-- Export: pass filter saat ini via x-bind --}}
            <a :href="exportUrl"
               class="btn btn-sm border-2 border-gold-400 text-gold-700 dark:text-gold-400 hover:bg-gold-50 dark:hover:bg-navy-700 bg-transparent">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pegawai
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         KPI CARDS
    ════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
        $statCards = [
            ['label'=>'Total Pegawai',   'value'=>number_format($analytics['total']),     'sub'=>$analytics['aktif'].' aktif',                         'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','bg'=>'bg-navy-100 dark:bg-navy-700','ico'=>'text-navy-600 dark:text-navy-300','val'=>'text-navy-700 dark:text-white'],
            ['label'=>'Pegawai Aktif',   'value'=>number_format($analytics['aktif']),     'sub'=>number_format($analytics['tidak_aktif']).' non-aktif','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','bg'=>'bg-emerald-100 dark:bg-emerald-900/30','ico'=>'text-emerald-600 dark:text-emerald-400','val'=>'text-emerald-700 dark:text-emerald-400'],
            ['label'=>'Rata-rata Grade', 'value'=>$analytics['avg_grading'],              'sub'=>'Dari grade 1–16',                                    'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6','bg'=>'bg-gold-100 dark:bg-gold-900/30','ico'=>'text-gold-600 dark:text-gold-400','val'=>'text-gold-700 dark:text-gold-400'],
            ['label'=>'Rata-rata Usia',  'value'=>$analytics['avg_usia'].' th',           'sub'=>'Masa kerja '.$analytics['avg_masa_kerja'].' th',     'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z','bg'=>'bg-purple-100 dark:bg-purple-900/30','ico'=>'text-purple-600 dark:text-purple-400','val'=>'text-purple-700 dark:text-purple-400'],
            ['label'=>'Pensiun ≤1 Th',  'value'=>$analytics['akan_pensiun_1th'],          'sub'=>'Segera pensiun',                                     'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','bg'=>'bg-red-100 dark:bg-red-900/30','ico'=>'text-red-500 dark:text-red-400','val'=>'text-red-600 dark:text-red-400'],
            ['label'=>'Pensiun ≤2 Th',  'value'=>$analytics['akan_pensiun_2th'],          'sub'=>'Perlu diperhatikan',                                 'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z','bg'=>'bg-orange-100 dark:bg-orange-900/30','ico'=>'text-orange-500 dark:text-orange-400','val'=>'text-orange-600 dark:text-orange-400'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="card flex flex-col gap-1 p-4">
            <div class="w-9 h-9 {{ $card['bg'] }} rounded-xl flex items-center justify-center mb-2">
                <svg class="w-4 h-4 {{ $card['ico'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <p class="text-xl font-bold {{ $card['val'] }}">{{ $card['value'] }}</p>
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $card['label'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $card['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════════════
         CHARTS ROW 1
    ════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="section-title">Sebaran per Bagian</h3>
                    <p class="section-desc">{{ $analytics['per_bagian']->sum('total') }} pegawai terdaftar</p>
                </div>
                <div class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="h-60"><canvas id="chartBagian"></canvas></div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="section-title">Distribusi Grading</h3>
                    <p class="section-desc">Rata-rata grade {{ $analytics['avg_grading'] }}</p>
                </div>
                <div class="w-8 h-8 bg-gold-100 dark:bg-navy-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="h-60"><canvas id="chartGrading"></canvas></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         CHARTS ROW 2
    ════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['id'=>'chartJK',        'label'=>'Jenis Kelamin'],
            ['id'=>'chartPendidikan','label'=>'Pendidikan'],
            ['id'=>'chartUsia',      'label'=>'Rentang Usia'],
            ['id'=>'chartMasaKerja', 'label'=>'Masa Kerja'],
        ] as $chart)
        <div class="card p-4">
            <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">{{ $chart['label'] }}</h3>
            <div class="h-44"><canvas id="{{ $chart['id'] }}"></canvas></div>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════════════
         CHARTS ROW 3
    ════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="card">
            <h3 class="section-title mb-4">Sebaran per Eselon</h3>
            <div class="h-52"><canvas id="chartEselon"></canvas></div>
        </div>
        <div class="card">
            <h3 class="section-title mb-4">Jenis Pegawai</h3>
            <div class="h-52"><canvas id="chartJenisJabatan"></canvas></div>
        </div>

        {{-- Alert Pensiun --}}
        <div class="card p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
                <h3 class="section-title">Alert Pensiun</h3>
                <span class="badge badge-danger">{{ $analytics['akan_pensiun_2th'] }} orang</span>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-navy-700/60 max-h-52 overflow-y-auto scrollbar-thin">
                @php
                $pensiunList = \App\Models\Pegawai::where('status','AKTIF')
                    ->whereNotNull('tanggal_pensiun')
                    ->whereBetween('tanggal_pensiun',[now(), now()->addYears(2)])
                    ->orderBy('tanggal_pensiun')->take(8)->get();
                @endphp
                @forelse($pensiunList as $pp)
                @php $bulanLagi = now()->diffInMonths(\Carbon\Carbon::parse($pp->tanggal_pensiun), false); @endphp
                <div class="px-4 py-2.5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-navy-700/40 transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-red-600 dark:text-red-400">{{ substr($pp->nama,0,1) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate max-w-32">{{ $pp->nama }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $pp->bagian ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <p class="text-xs font-bold {{ $bulanLagi <= 12 ? 'text-red-600 dark:text-red-400' : 'text-orange-500 dark:text-orange-400' }}">
                            {{ $bulanLagi }} bln
                        </p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($pp->tanggal_pensiun)->format('m/Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Tidak ada yang pensiun dalam 2 tahun</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         FILTER + TABLE
    ════════════════════════════════════════════════════════ --}}
    <div class="card p-0 overflow-hidden">

        {{-- ── Filter Bar ──────────────────────────────────── --}}
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50/60 dark:bg-navy-800/60">
            <div class="flex flex-wrap gap-3 items-end">

                {{-- Search --}}
                <div class="flex-1 min-w-52">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Cari</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        {{-- debounce 400ms saat mengetik --}}
                        <input type="text"
                               x-model="filters.search"
                               @input="debouncedFetch()"
                               placeholder="Nama, NIP, jabatan..."
                               class="input-field pl-9">
                    </div>
                </div>

                {{-- Bagian --}}
                <div class="min-w-36">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Bagian</label>
                    <select x-model="filters.bagian" @change="fetchData()" class="input-field py-2 px-3 text-sm">
                        <option value="">Semua</option>
                        @foreach($bagianList as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="min-w-32">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Status</label>
                    <select x-model="filters.status" @change="fetchData()" class="input-field py-2 px-3 text-sm">
                        <option value="">Semua</option>
                        @foreach(['AKTIF','CLTN','PENSIUN','NON AKTIF'] as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Gender --}}
                <div class="min-w-28">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Gender</label>
                    <select x-model="filters.jenis_kelamin" @change="fetchData()" class="input-field py-2 px-3 text-sm">
                        <option value="">Semua</option>
                        @foreach(['Laki-laki','Perempuan'] as $jk)
                        <option value="{{ $jk }}">{{ $jk }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pendidikan --}}
                <div class="min-w-28">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Pendidikan</label>
                    <select x-model="filters.pendidikan" @change="fetchData()" class="input-field py-2 px-3 text-sm">
                        <option value="">Semua</option>
                        @foreach($pendidikanList as $pd)
                        <option value="{{ $pd }}">{{ $pd }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Eselon --}}
                <div class="min-w-28">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Eselon</label>
                    <select x-model="filters.eselon" @change="fetchData()" class="input-field py-2 px-3 text-sm">
                        <option value="">Semua</option>
                        @foreach($eselonList as $e)
                        <option value="{{ $e }}">{{ $e }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Per Halaman --}}
                <div class="min-w-20">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Per hal.</label>
                    <select x-model="filters.per_page" @change="fetchData()" class="input-field py-2 px-3 text-sm">
                        @foreach([10,20,50,100] as $pp)
                        <option value="{{ $pp }}" {{ $pp == 20 ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset --}}
                <div class="flex items-end">
                    <button type="button"
                        x-show="hasActiveFilter"
                        @click="resetFilters()"
                        class="btn-ghost btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Result Info Bar ──────────────────────────────── --}}
        <div class="px-5 py-3 flex items-center justify-between border-b border-gray-100 dark:border-navy-700 bg-white dark:bg-navy-800 min-h-[44px]">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <template x-if="!loading">
                    <span>
                        Menampilkan
                        <span class="font-semibold text-gray-900 dark:text-white" x-text="info.from + '–' + info.to"></span>
                        dari <span class="font-semibold text-gray-900 dark:text-white" x-text="info.total"></span> pegawai
                    </span>
                </template>
                <template x-if="loading">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 animate-spin text-navy-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Memuat data...
                    </span>
                </template>
            </p>
            <span x-show="hasActiveFilter" class="badge badge-info">Filter aktif</span>
        </div>

        {{-- ── Table ────────────────────────────────────────── --}}
        <div class="overflow-x-auto relative">
            {{-- Loading overlay --}}
            <div x-show="loading"
                 x-transition:enter="transition-opacity duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-white/70 dark:bg-navy-800/70 backdrop-blur-[2px] z-10 flex items-center justify-center"
                 style="display:none;">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-8 h-8 animate-spin text-navy-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Memuat data...</span>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Pegawai</th>
                        <th>Jabatan & Bagian</th>
                        <th>Grade</th>
                        <th>Pendidikan</th>
                        <th>Status</th>
                        <th>Pensiun</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body" x-html="tableHtml">
                    {{-- Initial render dari server --}}
                    @include('kepegawaian.pegawai._table', ['pegawai' => $pegawai])
                </tbody>
            </table>
        </div>

        {{-- ── Pagination ───────────────────────────────────── --}}
        <div id="paginator-wrap"
             class="px-5 py-4 border-t border-gray-100 dark:border-navy-700 bg-gray-50/50 dark:bg-navy-800/60"
             x-html="paginatorHtml">
            @include('kepegawaian.pegawai._paginator', ['pegawai' => $pegawai])
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
/* ─────────────────────────────────────────────────────────────
   Alpine Component: pegawaiIndex
   Menangani filter AJAX tanpa full-page reload
───────────────────────────────────────────────────────────── */
function pegawaiIndex() {
    return {
        /* ── State ── */
        loading: false,
        tableHtml: '',
        paginatorHtml: '',
        info: {
            from:       {{ $pegawai->firstItem() ?? 0 }},
            to:         {{ $pegawai->lastItem()   ?? 0 }},
            total:      {{ $pegawai->total() }},
            has_filter: false,
        },
        filters: {
            search:        '',
            bagian:        '',
            status:        '',
            jenis_kelamin: '',
            pendidikan:    '',
            eselon:        '',
            per_page:      20,
            page:          1,
        },
        _debounceTimer: null,

        /* ── Computed ── */
        get hasActiveFilter() {
            return this.filters.search       !== '' ||
                   this.filters.bagian       !== '' ||
                   this.filters.status       !== '' ||
                   this.filters.jenis_kelamin !== '' ||
                   this.filters.pendidikan   !== '' ||
                   this.filters.eselon       !== '';
        },

        get exportUrl() {
            const p = new URLSearchParams();
            if (this.filters.bagian)        p.set('bagian',       this.filters.bagian);
            if (this.filters.status)        p.set('status',       this.filters.status);
            if (this.filters.jenis_kelamin) p.set('jenis_kelamin',this.filters.jenis_kelamin);
            return '{{ route('kepegawaian.pegawai.export') }}?' + p.toString();
        },

        /* ── Init ── */
        init() {
            /* Setelah Alpine init, pasang delegation klik pagination
               agar tombol halaman berikutnya juga trigger AJAX       */
            this.$el.addEventListener('click', (e) => {
                const link = e.target.closest('[data-paginate]');
                if (!link) return;
                e.preventDefault();
                const page = new URL(link.href).searchParams.get('page') || 1;
                this.filters.page = parseInt(page);
                this.fetchData();
            });

            /* Pasang delegation untuk semua anchor pagination
               (Laravel default pagination menggunakan <a href="?page=N">) */
            document.addEventListener('click', (e) => {
                const paginatorWrap = document.getElementById('paginator-wrap');
                if (!paginatorWrap) return;
                const link = e.target.closest('a[href*="page="]');
                if (!link || !paginatorWrap.contains(link)) return;
                e.preventDefault();
                const page = new URL(link.href).searchParams.get('page') || 1;
                this.filters.page = parseInt(page);
                this.fetchData();
            });
        },

        /* ── Methods ── */
        debouncedFetch() {
            clearTimeout(this._debounceTimer);
            this._debounceTimer = setTimeout(() => {
                this.filters.page = 1;
                this.fetchData();
            }, 400);
        },

        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                Object.entries(this.filters).forEach(([k, v]) => {
                    if (v !== '' && v !== null && v !== undefined) {
                        params.set(k, v);
                    }
                });

                const response = await axios.get('{{ route('kepegawaian.pegawai.index') }}', {
                    params: Object.fromEntries(params),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                const data = response.data;
                this.tableHtml     = data.html;
                this.paginatorHtml = data.paginator;
                this.info          = data.info;

                /* Scroll ke top tabel secara halus */
                const tableCard = this.$el.querySelector('.card.p-0');
                if (tableCard) {
                    tableCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } catch (err) {
                console.error('Filter AJAX error:', err);
                if (typeof showToast === 'function') {
                    showToast('Gagal memuat data. Silakan coba lagi.', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            this.filters = {
                search:        '',
                bagian:        '',
                status:        '',
                jenis_kelamin: '',
                pendidikan:    '',
                eselon:        '',
                per_page:      20,
                page:          1,
            };
            this.fetchData();
        },
    };
}

/* ─────────────────────────────────────────────────────────────
   Chart.js initialization
───────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const isDark    = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.04)';
    const tickColor = isDark ? '#6b7280' : '#9ca3af';
    const bgDark    = isDark ? '#1a2d47' : '#ffffff';

    const tooltip = {
        backgroundColor: '#1a2332',
        titleColor:      '#fbbf24',
        bodyColor:       '#e5e7eb',
        padding:         12,
        cornerRadius:    10,
        titleFont:       { size: 12, weight: 'bold' },
        bodyFont:        { size: 11 },
        borderColor:     'rgba(251,191,36,0.2)',
        borderWidth:     1,
    };

    const navy  = ['#1e3a5f','#2d5986','#3d6e9e','#4a7ba7','#6fa3d0','#8ec5ea','#afd8f5'];
    const gold  = ['#d97706','#f59e0b','#fbbf24','#fcd34d','#fde68a'];
    const mixed = ['#1e3a5f','#f59e0b','#2d5986','#fbbf24','#4a7ba7','#fcd34d','#6fa3d0','#d97706'];

    function scales(axis = 'y') {
        const opp = axis === 'y' ? 'x' : 'y';
        return {
            [axis]: {
                beginAtZero: true,
                ticks: { precision: 0, color: tickColor, font: { size: 10 } },
                grid:  { color: gridColor },
            },
            [opp]: {
                ticks: { color: tickColor, font: { size: 10 } },
                grid:  { display: false },
            },
        };
    }

    /* Chart: Sebaran per Bagian */
    new Chart(document.getElementById('chartBagian'), {
        type: 'bar',
        data: {
            labels:   @json($analytics['per_bagian']->pluck('bagian')),
            datasets: [{
                data:            @json($analytics['per_bagian']->pluck('total')),
                backgroundColor: navy,
                borderRadius:    6,
                borderSkipped:   false,
                barPercentage:   0.6,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend:  { display: false },
                tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.y + ' pegawai' } },
            },
            scales: scales('y'),
        },
    });

    /* Chart: Distribusi Grading */
    new Chart(document.getElementById('chartGrading'), {
        type: 'line',
        data: {
            labels:   @json($analytics['per_grading']->pluck('grading')->map(fn($g) => 'G'.$g)),
            datasets: [{
                data:                 @json($analytics['per_grading']->pluck('total')),
                borderColor:          '#f59e0b',
                backgroundColor:      'rgba(245,158,11,0.1)',
                borderWidth:          2.5,
                fill:                 true,
                tension:              0.4,
                pointBackgroundColor: '#1e3a5f',
                pointBorderColor:     '#f59e0b',
                pointRadius:          4,
                pointHoverRadius:     6,
                pointBorderWidth:     2,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend:  { display: false },
                tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.y + ' pegawai' } },
            },
            scales: scales('y'),
        },
    });

    /* Chart: Jenis Kelamin */
    new Chart(document.getElementById('chartJK'), {
        type: 'doughnut',
        data: {
            labels:   @json($analytics['per_jenis_kelamin']->pluck('jenis_kelamin')),
            datasets: [{
                data:            @json($analytics['per_jenis_kelamin']->pluck('total')),
                backgroundColor: ['#1e3a5f','#f59e0b'],
                borderColor:     bgDark,
                borderWidth:     3,
                hoverOffset:     6,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: {
                legend:  { position: 'bottom', labels: { padding: 8, usePointStyle: true, pointStyle: 'circle', font: { size: 10 }, color: tickColor } },
                tooltip: { ...tooltip, callbacks: {
                    label: c => {
                        const t = c.dataset.data.reduce((a, b) => a + b, 0);
                        return ` ${c.label}: ${c.parsed} (${((c.parsed / t) * 100).toFixed(1)}%)`;
                    },
                }},
            },
        },
    });

    /* Chart: Pendidikan */
    new Chart(document.getElementById('chartPendidikan'), {
        type: 'pie',
        data: {
            labels:   @json($analytics['per_pendidikan']->pluck('pendidikan')),
            datasets: [{
                data:            @json($analytics['per_pendidikan']->pluck('total')),
                backgroundColor: mixed,
                borderColor:     bgDark,
                borderWidth:     3,
                hoverOffset:     6,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend:  { position: 'bottom', labels: { padding: 6, usePointStyle: true, pointStyle: 'circle', font: { size: 9 }, color: tickColor } },
                tooltip,
            },
        },
    });

    /* Chart: Rentang Usia */
    new Chart(document.getElementById('chartUsia'), {
        type: 'bar',
        data: {
            labels:   @json(array_keys($analytics['range_usia'])),
            datasets: [{
                data:            @json(array_values($analytics['range_usia'])),
                backgroundColor: navy.slice(0, 5),
                borderRadius:    5,
                borderSkipped:   false,
                barPercentage:   0.7,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend:  { display: false },
                tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.y + ' org' } },
            },
            scales: scales('y'),
        },
    });

    /* Chart: Masa Kerja */
    new Chart(document.getElementById('chartMasaKerja'), {
        type: 'bar',
        data: {
            labels:   @json(array_keys($analytics['range_masa_kerja'])),
            datasets: [{
                data:            @json(array_values($analytics['range_masa_kerja'])),
                backgroundColor: gold,
                borderRadius:    5,
                borderSkipped:   false,
                barPercentage:   0.7,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend:  { display: false },
                tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.y + ' org' } },
            },
            scales: scales('y'),
        },
    });

    /* Chart: Eselon */
    new Chart(document.getElementById('chartEselon'), {
        type: 'bar',
        data: {
            labels:   @json($analytics['per_eselon']->pluck('eselon')),
            datasets: [{
                data:            @json($analytics['per_eselon']->pluck('total')),
                backgroundColor: gold,
                borderRadius:    5,
                borderSkipped:   false,
                barPercentage:   0.6,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend:  { display: false },
                tooltip: { ...tooltip, callbacks: { label: c => ' ' + c.parsed.x + ' pegawai' } },
            },
            scales: scales('x'),
        },
    });

    /* Chart: Jenis Jabatan */
    new Chart(document.getElementById('chartJenisJabatan'), {
        type: 'doughnut',
        data: {
            labels:   @json($analytics['per_jenis_pegawai']->pluck('jenis_pegawai')),
            datasets: [{
                data:            @json($analytics['per_jenis_pegawai']->pluck('total')),
                backgroundColor: navy.slice(0, 4),
                borderColor:     bgDark,
                borderWidth:     3,
                hoverOffset:     6,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '55%',
            plugins: {
                legend:  { position: 'bottom', labels: { padding: 10, usePointStyle: true, pointStyle: 'circle', font: { size: 10 }, color: tickColor } },
                tooltip,
            },
        },
    });
});
</script>
@endpush
