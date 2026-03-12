@extends('layouts.app')
@section('title', 'Rekomendasi Kenaikan Grading')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <nav class="breadcrumb mb-2" aria-label="Breadcrumb">
            <span class="text-gray-400 dark:text-gray-500 text-sm">Kepegawaian</span>
            <svg class="w-3.5 h-3.5 mx-1 text-gray-300 dark:text-gray-600"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kenaikan Grading</span>
        </nav>
        <h1 class="page-title">Rekomendasi Kenaikan Grading</h1>
        <p class="page-subtitle">
            Analisis dan rekomendasi kenaikan grading pegawai tahun {{ $tahun }}
        </p>
    </div>

    {{--
        Filter & Export dipindah ke dalam @section('content') supaya
        berada dalam scope x-data="gradingIndex()" yang sama.
        Di sini hanya tampilkan header teks saja.
    --}}
</div>
@endsection

@section('content')
{{-- SATU x-data untuk seluruh halaman --}}
<div class="space-y-6 animate-fade-in" x-data="gradingIndex()">

    {{-- Toolbar: Filter + Export (dalam scope Alpine) --}}
    <div class="flex flex-wrap items-center justify-end gap-2">
        {{-- Filter Tahun --}}
        <div class="relative">
            <select x-model="filters.tahun"
                    @change="fetchData()"
                    class="input-field !py-2 !pr-8 !pl-3 appearance-none min-w-[130px]">
                @for($y = date('Y') + 1; $y >= date('Y') - 4; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        {{-- Filter Bagian --}}
        <div class="relative">
            <select x-model="filters.bagian"
                    @change="fetchData()"
                    class="input-field !py-2 !pr-8 !pl-3 appearance-none min-w-[160px]">
                <option value="">Semua Bagian</option>
                @foreach($bagianList as $bag)
                <option value="{{ $bag }}">{{ $bag }}</option>
                @endforeach
            </select>
            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        {{-- Export --}}
        <a :href="`{{ route('kepegawaian.grading') }}?tahun=${filters.tahun}&export=1`"
           class="btn btn-outline btn-sm gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export Excel
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="card flex items-start gap-4 !p-5">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="stat-card-label">Total Eligible</p>
                <p class="stat-card-value text-emerald-600 dark:text-emerald-400" x-text="stats.total"></p>
                <p class="stat-card-sub text-gray-400">
                    dari <span x-text="stats.totalAktif"></span> pegawai aktif
                </p>
            </div>
        </div>

        <div class="card flex items-start gap-4 !p-5">
            <div class="w-12 h-12 rounded-xl bg-navy-100 dark:bg-navy-700
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-navy-600 dark:text-navy-300"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="stat-card-label">Naik 1 Grade</p>
                <p class="stat-card-value text-navy-700 dark:text-navy-200" x-text="stats.naik1"></p>
                <p class="stat-card-sub text-gray-400">pegawai</p>
            </div>
        </div>

        <div class="card flex items-start gap-4 !p-5">
            <div class="w-12 h-12 rounded-xl bg-gold-100 dark:bg-gold-900/20
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-gold-600 dark:text-gold-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="stat-card-label">Rata-rata Masa Kerja</p>
                <p class="stat-card-value text-gold-600 dark:text-gold-400" x-text="stats.avgMasaKerja"></p>
                <p class="stat-card-sub text-gray-400">tahun</p>
            </div>
        </div>

        <div class="card flex items-start gap-4 !p-5">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/20
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="stat-card-label">Grade Maks Rekomendasi</p>
                <p class="stat-card-value text-purple-600 dark:text-purple-400" x-text="stats.maxGrade"></p>
                <p class="stat-card-sub text-gray-400">tertinggi</p>
            </div>
        </div>

    </div>

    {{-- Chart + Per Bagian --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2 card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700">
                <h3 class="section-title">Distribusi Kenaikan Grade</h3>
                <p class="section-desc">Perbandingan grade sekarang vs rekomendasi</p>
            </div>
            <div class="p-5">
                <canvas id="gradingChart" height="110"></canvas>
            </div>
        </div>

        <div class="card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700">
                <h3 class="section-title">Per Bagian</h3>
                <p class="section-desc">Jumlah eligible per unit</p>
            </div>
            <div class="p-4 space-y-3 max-h-72 overflow-y-auto scrollbar-thin">
                <template x-if="perBagian.length === 0">
                    <p class="text-xs text-gray-400 text-center py-4">Tidak ada data</p>
                </template>
                <template x-for="item in perBagian" :key="item.bagian">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300
                                         truncate max-w-[160px]"
                                  x-text="item.bagian || 'Tanpa Bagian'"></span>
                            <span class="text-xs font-bold text-navy-600 dark:text-navy-300 ml-2"
                                  x-text="item.count"></span>
                        </div>
                        <div class="progress-bar-wrap !h-1.5">
                            <div class="progress-bar-navy transition-all duration-500"
                                 :style="`width: ${item.pct}%`"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    {{-- Tabel --}}
    <div class="card !p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="section-title">Daftar Rekomendasi Kenaikan Grading</h3>
                <p class="section-desc">
                    <span x-text="rows.length"></span> pegawai eligible
                    <template x-if="filters.bagian">
                        <span> · Bagian:
                            <span x-text="filters.bagian" class="font-semibold"></span>
                        </span>
                    </template>
                </p>
            </div>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-model="filters.search"
                       @input.debounce.300ms="fetchData()"
                       placeholder="Cari nama / NIP..."
                       class="input-field !pl-9 !py-2 !text-xs w-52">
            </div>
        </div>

        {{-- Loading --}}
        <div x-show="loading"
             x-transition:enter="transition-opacity duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="flex items-center justify-center py-20 gap-3"
             style="display:none;">
            <div class="w-6 h-6 border-2 border-navy-200 border-t-navy-600 rounded-full animate-spin"></div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Memuat data...</span>
        </div>

        {{-- Tabel Data --}}
        <div x-show="!loading" style="display:none;">
            <div class="table-wrapper !rounded-none !border-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th>Pegawai</th>
                            <th>Jabatan / Bagian</th>
                            <th class="text-center">Level</th>
                            <th class="text-center">Grade Sekarang</th>
                            <th class="text-center">Grade Baru</th>
                            <th class="text-center">Maks</th>
                            <th class="text-center">Masa Kerja</th>
                            <th>Alasan</th>
                            <th class="text-center w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <template x-if="rows.length === 0">
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                        </div>
                                        <p class="empty-state-title">Tidak ada rekomendasi</p>
                                        <p class="empty-state-desc"
                                           x-text="(filters.search || filters.bagian)
                                               ? 'Coba ubah filter pencarian'
                                               : `Belum ada pegawai yang memenuhi kriteria kenaikan grading tahun ${filters.tahun}`">
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-for="(row, index) in rows" :key="row.id">
                            <tr class="hover:bg-navy-50/30 dark:hover:bg-navy-700/30 transition-colors">

                                <td class="text-gray-400 dark:text-gray-500 text-center text-sm"
                                    x-text="index + 1"></td>

                                {{-- Pegawai --}}
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-gradient-to-br from-navy-500 to-navy-700
                                                    rounded-xl flex items-center justify-center
                                                    flex-shrink-0 shadow-sm">
                                            <span class="text-xs font-bold text-white uppercase"
                                                  x-text="row.initials"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <a :href="row.url_show"
                                               class="text-sm font-semibold text-gray-900 dark:text-white
                                                      hover:text-navy-600 dark:hover:text-navy-300
                                                      transition-colors truncate block max-w-[150px]"
                                               x-text="row.nama"></a>
                                            <p class="text-xs font-mono text-gray-400 dark:text-gray-500"
                                               x-text="row.nip"></p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Jabatan / Bagian --}}
                                <td>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-snug"
                                       x-text="row.jabatan || '—'"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"
                                       x-show="row.bagian" x-text="row.bagian"></p>
                                </td>

                                {{-- Level --}}
                                <td class="text-center">
                                    <span class="badge badge-info text-xs"
                                          x-text="row.level_jabatan"></span>
                                </td>

                                {{-- Grade Sekarang --}}
                                <td class="text-center">
                                    <span class="badge badge-gray font-bold px-3"
                                          x-text="`G${row.grading_sekarang}`"></span>
                                </td>

                                {{-- Grade Baru --}}
                                <td class="text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="badge badge-success font-bold px-3"
                                              x-text="`G${row.grading_baru}`"></span>
                                        <span x-show="row.grading_baru > row.grading_sekarang"
                                              class="text-[10px] font-bold text-emerald-600
                                                     dark:text-emerald-400 flex items-center gap-0.5">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            <span x-text="`+${row.grading_baru - row.grading_sekarang}`"></span>
                                        </span>
                                    </div>
                                </td>

                                {{-- Grading Max --}}
                                <td class="text-center">
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400"
                                          x-text="`G${row.grading_max}`"></span>
                                    <template x-if="row.grading_baru >= row.grading_max">
                                        <p class="text-[9px] font-semibold text-amber-500 mt-0.5">MAKS</p>
                                    </template>
                                </td>

                                {{-- Masa Kerja --}}
                                <td class="text-center">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300"
                                          x-text="row.masa_kerja_tahun"></span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500"> thn</span>
                                </td>

                                {{-- Alasan --}}
                                <td class="max-w-[200px]">
                                    <ul class="space-y-1">
                                        <template x-for="alasan in row.alasan" :key="alasan">
                                            <li class="flex items-start gap-1.5">
                                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full
                                                             mt-1.5 flex-shrink-0"></span>
                                                <span class="text-xs text-gray-600 dark:text-gray-400
                                                             leading-snug" x-text="alasan"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center">
                                    <a :href="row.url_show"
                                       class="table-action-view inline-flex items-center justify-center"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </td>

                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Referensi Batas Grading --}}
    <div class="card !p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700">
            <h3 class="section-title">Referensi Batas Maksimal Grading</h3>
            <p class="section-desc">Batas grading berdasarkan level jabatan</p>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                @php
                $referensi = [
                    ['label' => 'Eselon I',          'max' => 27, 'color' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300'],
                    ['label' => 'Eselon II',         'max' => 22, 'color' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300'],
                    ['label' => 'Eselon III',        'max' => 18, 'color' => 'bg-navy-100 dark:bg-navy-700 text-navy-700 dark:text-navy-200'],
                    ['label' => 'Eselon IV',         'max' => 16, 'color' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300'],
                    ['label' => 'Eselon V',          'max' => 13, 'color' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300'],
                    ['label' => 'Fung. Utama',       'max' => 17, 'color' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'],
                    ['label' => 'Fung. Madya',       'max' => 14, 'color' => 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300'],
                    ['label' => 'Fung. Muda',        'max' => 11, 'color' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'],
                    ['label' => 'Fung. Pertama',     'max' => 9,  'color' => 'bg-lime-100 dark:bg-lime-900/30 text-lime-700 dark:text-lime-300'],
                    ['label' => 'Pelaksana',         'max' => 12, 'color' => 'bg-gold-100 dark:bg-gold-900/20 text-gold-700 dark:text-gold-300'],
                ];
                @endphp
                @foreach($referensi as $ref)
                <div class="flex items-center justify-between px-3 py-2.5 rounded-xl {{ $ref['color'] }}">
                    <span class="text-xs font-semibold">{{ $ref['label'] }}</span>
                    <span class="text-base font-bold ml-2">G{{ $ref['max'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Kriteria --}}
    <div class="alert-info">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"/>
        </svg>
        <div>
            <h4 class="font-semibold mb-1.5">Kriteria Rekomendasi Kenaikan Grading</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1 text-sm">
                <span class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-navy-400 rounded-full flex-shrink-0"></span>
                    Masa kerja minimal 4 tahun (kenaikan reguler)
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-navy-400 rounded-full flex-shrink-0"></span>
                    Pendidikan S2/S3 sebagai pertimbangan akselerasi
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-navy-400 rounded-full flex-shrink-0"></span>
                    Batas grading ditentukan oleh level jabatan
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-navy-400 rounded-full flex-shrink-0"></span>
                    Pelaksana maks G12 · Eselon IV maks G16 · Eselon III maks G18
                </span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function gradingIndex() {
    return {
        loading: false,
        rows:      @json($initialRows),
        stats:     @json($initialStats),
        perBagian: @json($initialPerBagian),
        filters: {
            tahun:  '{{ $tahun }}',
            bagian: '{{ request("bagian", "") }}',
            search: '{{ request("search", "") }}',
        },
        chart: null,

        init() {
            // Pastikan chart hanya diinit sekali setelah DOM siap
            this.$nextTick(() => {
                this.initChart();
            });
        },

        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    tahun:  this.filters.tahun,
                    bagian: this.filters.bagian,
                    search: this.filters.search,
                    ajax:   1,
                });

                const res  = await fetch(`{{ route('kepegawaian.grading') }}?${params}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                this.rows      = data.rows;
                this.stats     = data.stats;
                this.perBagian = data.perBagian;

                // Update URL tanpa reload
                const url = new URL(window.location);
                url.searchParams.set('tahun',  this.filters.tahun);
                url.searchParams.set('bagian', this.filters.bagian);
                url.searchParams.set('search', this.filters.search);
                window.history.replaceState({}, '', url);

                this.$nextTick(() => this.updateChart());

            } catch (e) {
                console.error('Gagal memuat data grading:', e);
            } finally {
                this.loading = false;
            }
        },

        initChart() {
            const ctx = document.getElementById('gradingChart');
            if (!ctx) return;

            // Destroy dulu jika sudah ada instance sebelumnya
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }

            // Juga cek registry Chart.js (jaga-jaga double init)
            const existing = Chart.getChart('gradingChart');
            if (existing) {
                existing.destroy();
            }

            const isDark = document.documentElement.classList.contains('dark');
            this.chart   = new Chart(ctx, this.buildChartConfig(isDark));
        },

        updateChart() {
            if (!this.chart) {
                this.initChart();
                return;
            }
            const cfg = this.getChartData();
            this.chart.data.labels           = cfg.labels;
            this.chart.data.datasets[0].data = cfg.sekarang;
            this.chart.data.datasets[1].data = cfg.baru;
            this.chart.update('active');
        },

        getChartData() {
            const map = {};
            this.rows.forEach(r => {
                const gs = r.grading_sekarang;
                const gb = r.grading_baru;
                if (!map[gs]) map[gs] = { sekarang: 0, baru: 0 };
                if (!map[gb]) map[gb] = { sekarang: 0, baru: 0 };
                map[gs].sekarang++;
                map[gb].baru++;
            });
            const keys     = Object.keys(map).map(Number).sort((a, b) => a - b);
            const labels   = keys.map(g => 'G' + g);
            const sekarang = keys.map(g => map[g].sekarang);
            const baru     = keys.map(g => map[g].baru);
            return { labels, sekarang, baru };
        },

        buildChartConfig(isDark) {
            const cfg   = this.getChartData();
            const grid  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const label = isDark ? '#9fb3c8' : '#627d98';
            return {
                type: 'bar',
                data: {
                    labels: cfg.labels,
                    datasets: [
                        {
                            label: 'Grade Sekarang',
                            data:  cfg.sekarang,
                            backgroundColor: isDark
                                ? 'rgba(98,125,152,0.7)'
                                : 'rgba(72,101,129,0.7)',
                            borderRadius: 6,
                        },
                        {
                            label: 'Rekomendasi Baru',
                            data:  cfg.baru,
                            backgroundColor: isDark
                                ? 'rgba(52,211,153,0.7)'
                                : 'rgba(16,185,129,0.7)',
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { color: label, boxWidth: 12, padding: 16 },
                        },
                        tooltip: { mode: 'index', intersect: false },
                    },
                    scales: {
                        x: { grid: { color: grid }, ticks: { color: label } },
                        y: {
                            beginAtZero: true,
                            grid:  { color: grid },
                            ticks: { color: label, stepSize: 1 },
                        },
                    },
                },
            };
        },
    };
}
</script>
@endpush
