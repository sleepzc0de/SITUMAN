@extends('layouts.app')

@section('title', 'Dokumen Capaian Output')

@section('breadcrumb')
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Dokumen Capaian Output</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Dokumen Capaian Output</h1>
        <p class="page-subtitle">Kelola dokumen bukti capaian output per sub komponen kegiatan</p>
    </div>
    <a href="{{ route('anggaran.dokumen.create') }}" class="btn btn-primary self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Upload Dokumen
    </a>
</div>
@endsection

@section('content')

{{-- Siapkan data PHP sebelum masuk ke blade/JS --}}
@php
    $initialRows = $dokumens->getCollection()->map(function($d) {
        return [
            'id'               => $d->id,
            'nama_dokumen'     => $d->nama_dokumen,
            'ro'               => $d->ro,
            'sub_komponen'     => $d->sub_komponen,
            'bulan'            => $d->bulan,
            'keterangan'       => $d->keterangan ?? '',
            'file_count'       => count($d->getAllFiles()),
            'user_nama'        => $d->user ? $d->user->nama : '—',
            'created_at_short' => format_tanggal_short($d->created_at),
        ];
    })->values()->toArray();

    $initialStats = [
        'total'     => $dokumens->total(),
        'totalFile' => $dokumens->getCollection()->sum(function($d) { return count($d->getAllFiles()); }),
        'roCount'   => $dokumens->getCollection()->pluck('ro')->unique()->count(),
    ];
@endphp

<div
    class="space-y-5"
    x-data="dokumenIndex()"
    x-init="init()"
>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="stat-card-label">Total Dokumen</span>
                <div class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <p class="stat-card-value" x-text="stats.total.toLocaleString('id-ID')">{{ $dokumens->total() }}</p>
            <p class="stat-card-sub text-gray-500 dark:text-gray-400">dokumen terupload</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="stat-card-label">Total File</span>
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </div>
            </div>
            <p class="stat-card-value" x-text="stats.totalFile.toLocaleString('id-ID')">{{ $initialStats['totalFile'] }}</p>
            <p class="stat-card-sub text-gray-500 dark:text-gray-400">file di halaman ini</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="stat-card-label">RO Aktif</span>
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
            <p class="stat-card-value" x-text="stats.roCount">{{ $initialStats['roCount'] }}</p>
            <p class="stat-card-sub text-gray-500 dark:text-gray-400">RO di hasil ini</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="stat-card-label">Filter Bulan</span>
                <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="stat-card-value text-sm font-semibold capitalize"
               x-text="filterBulan && filterBulan !== 'all'
                   ? (filterBulan.charAt(0).toUpperCase() + filterBulan.slice(1))
                   : 'Semua'">
                {{ request('bulan') && request('bulan') !== 'all' ? ucfirst(request('bulan')) : 'Semua' }}
            </p>
            <p class="stat-card-sub text-gray-500 dark:text-gray-400">periode aktif</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="input-group flex-1">
                <label class="input-label">Filter RO</label>
                <select class="input-field" x-model="filterRO" @change="fetchData(1)">
                    <option value="all">Semua RO</option>
                    @foreach ($roList as $ro)
                        <option value="{{ $ro }}">{{ $ro }} — {{ get_ro_name($ro) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group flex-1">
                <label class="input-label">Filter Bulan</label>
                <select class="input-field" x-model="filterBulan" @change="fetchData(1)">
                    <option value="all">Semua Bulan</option>
                    @foreach ($bulanList as $bulan)
                        <option value="{{ $bulan }}">{{ ucfirst($bulan) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 pb-0.5">
                <button type="button"
                        @click="resetFilter()"
                        class="btn btn-ghost btn-sm"
                        x-show="filterRO !== 'all' || filterBulan !== 'all'"
                        x-transition>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card !p-0 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-navy-700">
            <h3 class="section-title">Daftar Dokumen</h3>
            <span class="badge badge-info" x-text="stats.total + ' dokumen'">{{ $dokumens->total() }} dokumen</span>
        </div>

        {{-- Loading Skeleton --}}
        <div x-show="loading" class="p-5 space-y-3">
            <div class="skeleton h-11 w-full rounded-xl"></div>
            <div class="skeleton h-11 w-full rounded-xl"></div>
            <div class="skeleton h-11 w-full rounded-xl"></div>
            <div class="skeleton h-11 w-full rounded-xl"></div>
            <div class="skeleton h-11 w-full rounded-xl"></div>
        </div>

        {{-- Table --}}
        <div x-show="!loading"
             x-transition:enter="transition-opacity duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="table-wrapper !rounded-none !border-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-10">No</th>
                            <th>Nama Dokumen</th>
                            <th>RO</th>
                            <th>Sub Komponen</th>
                            <th>Bulan</th>
                            <th>File</th>
                            <th>Upload Oleh</th>
                            <th>Tgl Upload</th>
                            <th class="text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Empty State --}}
                        <template x-if="rows.length === 0 && !loading">
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state py-16">
                                        <div class="empty-state-icon">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="empty-state-title">Belum ada dokumen capaian</p>
                                        <p class="empty-state-desc"
                                           x-text="(filterRO !== 'all' || filterBulan !== 'all')
                                               ? 'Tidak ada hasil untuk filter yang dipilih.'
                                               : 'Mulai dengan mengupload dokumen baru.'">
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Data Rows --}}
                        <template x-for="(row, index) in rows" :key="row.id">
                            <tr>
                                <td class="text-gray-500 text-xs"
                                    x-text="((currentPage - 1) * perPage) + index + 1"></td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-navy-50 dark:bg-navy-700 rounded-lg
                                                    flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-navy-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-white text-sm truncate max-w-xs"
                                               x-text="row.nama_dokumen"></p>
                                            <p x-show="row.keterangan"
                                               class="text-xs text-gray-400 truncate max-w-xs"
                                               x-text="row.keterangan"></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-blue" x-text="row.ro"></span>
                                </td>
                                <td>
                                    <span class="text-sm text-gray-700 dark:text-gray-300"
                                          :title="row.sub_komponen"
                                          x-text="row.sub_komponen.length > 28
                                              ? row.sub_komponen.substring(0, 28) + '…'
                                              : row.sub_komponen">
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-gray capitalize" x-text="row.bulan"></span>
                                </td>
                                <td>
                                    <span class="badge"
                                          :class="row.file_count > 1 ? 'badge-success' : 'badge-info'"
                                          x-text="row.file_count + ' file'"></span>
                                </td>
                                <td class="text-sm text-gray-600 dark:text-gray-400"
                                    x-text="row.user_nama"></td>
                                <td class="text-sm text-gray-500"
                                    x-text="row.created_at_short"></td>
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- Detail --}}
                                        <a :href="baseUrl + '/' + row.id"
                                           class="table-action-view" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        {{-- Edit --}}
                                        <a :href="baseUrl + '/' + row.id + '/edit'"
                                           class="table-action-edit" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        {{-- Download --}}
                                        <a :href="baseUrl + '/' + row.id + '/download'"
                                           class="table-action-download" title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                        {{-- Hapus --}}
                                        <button type="button"
                                                @click="hapus(row.id, row.nama_dokumen)"
                                                class="table-action-delete" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div x-show="totalPages > 1"
                 class="flex flex-col sm:flex-row sm:items-center sm:justify-between
                        gap-3 px-5 py-4 border-t border-gray-100 dark:border-navy-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan
                    <span class="font-semibold text-gray-700 dark:text-gray-200"
                          x-text="((currentPage - 1) * perPage) + 1"></span>–<span
                          class="font-semibold text-gray-700 dark:text-gray-200"
                          x-text="Math.min(currentPage * perPage, stats.total)"></span>
                    dari
                    <span class="font-semibold text-gray-700 dark:text-gray-200"
                          x-text="stats.total"></span> dokumen
                </p>
                <div class="flex items-center gap-1">
                    {{-- First --}}
                    <button @click="fetchData(1)"
                            :disabled="currentPage === 1"
                            class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700
                                   disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>
                    {{-- Prev --}}
                    <button @click="fetchData(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700
                                   disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    {{-- Page Numbers --}}
                    <template x-for="p in pageNumbers" :key="p">
                        <button @click="p !== '…' && fetchData(p)"
                                :disabled="p === '…'"
                                class="min-w-[32px] h-8 px-2 rounded-lg text-sm font-medium transition-colors"
                                :class="{
                                    'bg-navy-600 text-white shadow-sm':                          p === currentPage,
                                    'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700': p !== currentPage && p !== '…',
                                    'text-gray-400 cursor-default':                              p === '…'
                                }"
                                x-text="p">
                        </button>
                    </template>

                    {{-- Next --}}
                    <button @click="fetchData(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700
                                   disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    {{-- Last --}}
                    <button @click="fetchData(totalPages)"
                            :disabled="currentPage === totalPages"
                            class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700
                                   disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function dokumenIndex() {
    return {
        // ── URL base untuk aksi baris
        baseUrl: '{{ url('anggaran/dokumen') }}',
        ajaxUrl: '{{ route('anggaran.dokumen.index') }}',
        csrfToken: '{{ csrf_token() }}',

        // ── Filter
        filterRO:    '{{ request('ro', 'all') }}',
        filterBulan: '{{ request('bulan', 'all') }}',

        // ── State
        loading:     false,
        rows:        [],

        // ── Stats
        stats: {
            total:     {{ (int)$dokumens->total() }},
            totalFile: {{ (int)$initialStats['totalFile'] }},
            roCount:   {{ (int)$initialStats['roCount'] }},
        },

        // ── Pagination
        currentPage: {{ (int)$dokumens->currentPage() }},
        perPage:     {{ (int)$dokumens->perPage() }},
        totalPages:  {{ (int)$dokumens->lastPage() }},

        // ── Computed: nomor halaman
        get pageNumbers() {
            const pages = [];
            const total = this.totalPages;
            const cur   = this.currentPage;

            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
            } else {
                pages.push(1);
                if (cur > 3) pages.push('…');
                const from = Math.max(2, cur - 1);
                const to   = Math.min(total - 1, cur + 1);
                for (let i = from; i <= to; i++) pages.push(i);
                if (cur < total - 2) pages.push('…');
                pages.push(total);
            }
            return pages;
        },

        // ── Init: pakai data SSR supaya tidak ada AJAX saat pertama load
        init() {
            this.rows = {!! json_encode($initialRows) !!};
        },

        // ── Fetch data via AJAX (filter / pagination)
        async fetchData(page) {
            if (page < 1 || page > this.totalPages) return;
            this.loading = true;

            try {
                const params = new URLSearchParams({
                    ro:    this.filterRO,
                    bulan: this.filterBulan,
                    page:  page,
                    json:  1,
                });

                const res  = await axios.get(this.ajaxUrl + '?' + params.toString());
                const data = res.data;

                this.rows        = data.rows;
                this.currentPage = data.current_page;
                this.totalPages  = data.last_page;
                this.perPage     = data.per_page;

                this.stats.total     = data.total;
                this.stats.totalFile = data.total_file;
                this.stats.roCount   = data.ro_count;

                // Scroll ke atas area tabel
                this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' });

            } catch (err) {
                console.error(err);
                window.showToast('Gagal memuat data. Silakan coba lagi.', 'error');
            } finally {
                this.loading = false;
            }
        },

        // ── Reset semua filter
        resetFilter() {
            this.filterRO    = 'all';
            this.filterBulan = 'all';
            this.fetchData(1);
        },

        // ── Hapus dokumen
        hapus(id, nama) {
            if (!confirm('Hapus dokumen "' + nama + '"?\nTindakan ini tidak dapat dibatalkan.')) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = this.baseUrl + '/' + id;
            form.innerHTML =
                '<input type="hidden" name="_token" value="' + this.csrfToken + '">' +
                '<input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        },
    };
}
</script>
@endpush
