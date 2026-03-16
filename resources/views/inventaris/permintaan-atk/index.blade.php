{{-- resources/views/inventaris/permintaan-atk/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Permintaan ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Permintaan ATK', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="page-title">Permintaan ATK</h1>
        <p class="page-subtitle">Kelola permintaan Alat Tulis Kantor dari seluruh pegawai</p>
    </div>
    <a href="{{ route('inventaris.permintaan-atk.create') }}" class="btn-primary self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Permintaan
    </a>
</div>
@endsection

@section('content')
<div class="space-y-5" x-data="permintaanIndex()" x-init="init()">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @php
        $statsConfig = [
            ['label' => 'Total',     'key' => 'total',     'color' => 'navy',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Pending',   'key' => 'pending',   'color' => 'yellow', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Disetujui', 'key' => 'disetujui', 'color' => 'green',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Ditolak',   'key' => 'ditolak',   'color' => 'red',    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Selesai',   'key' => 'selesai',   'color' => 'blue',   'icon' => 'M5 13l4 4L19 7'],
        ];
        $colorMap = [
            'navy'   => ['text' => 'text-navy-700 dark:text-white',        'bg' => 'bg-navy-100 dark:bg-navy-700',        'icon' => 'text-navy-600 dark:text-navy-300'],
            'yellow' => ['text' => 'text-yellow-700 dark:text-yellow-400', 'bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'icon' => 'text-yellow-600 dark:text-yellow-400'],
            'green'  => ['text' => 'text-green-700 dark:text-green-400',   'bg' => 'bg-green-100 dark:bg-green-900/30',   'icon' => 'text-green-600 dark:text-green-400'],
            'red'    => ['text' => 'text-red-700 dark:text-red-400',       'bg' => 'bg-red-100 dark:bg-red-900/30',       'icon' => 'text-red-600 dark:text-red-400'],
            'blue'   => ['text' => 'text-blue-700 dark:text-blue-400',     'bg' => 'bg-blue-100 dark:bg-blue-900/30',     'icon' => 'text-blue-600 dark:text-blue-400'],
        ];
        @endphp

        @foreach($statsConfig as $s)
        @php $c = $colorMap[$s['color']]; @endphp
        <div class="stat-card cursor-pointer transition-all duration-200"
             :class="filters.status === '{{ $s['key'] === 'total' ? '' : $s['key'] }}' && '{{ $s['key'] }}' !== 'total'
                     ? 'ring-2 ring-navy-400 dark:ring-navy-500'
                     : ''"
             @click="{{ $s['key'] !== 'total' ? 'filterByStatus(\'' . $s['key'] . '\')' : 'filterByStatus(\'\')' }}">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="stat-card-label">{{ $s['label'] }}</p>
                    <p class="stat-card-value {{ $c['text'] }}"
                       x-text="stats.{{ $s['key'] }} ?? '{{ $stats[$s['key']] }}'">
                        {{ $stats[$s['key']] }}
                    </p>
                </div>
                <div class="p-2.5 {{ $c['bg'] }} rounded-xl flex-shrink-0">
                    <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                    </svg>
                </div>
            </div>
            @if($s['key'] !== 'total')
            <div class="mt-3">
                <div class="progress-bar-wrap">
                    <div class="progress-bar {{ $s['color'] === 'yellow' ? 'bg-yellow-500' : ($s['color'] === 'green' ? 'bg-green-500' : ($s['color'] === 'red' ? 'bg-red-500' : 'bg-blue-500')) }}"
                         :style="'width: ' + (stats.total > 0 ? Math.round((stats.{{ $s['key'] }} / stats.total) * 100) : 0) + '%'">
                    </div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"
                   x-text="(stats.total > 0 ? Math.round((stats.{{ $s['key'] }} / stats.total) * 100) : 0) + '%'">
                </p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Filter Bar --}}
    <div class="card-flat">
        <div class="flex flex-col lg:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-model="filters.search"
                       @input.debounce.400ms="fetch()"
                       placeholder="Cari nomor permintaan atau nama pegawai..."
                       class="input-field pl-9">
            </div>

            {{-- Status --}}
            <select x-model="filters.status" @change="fetch()" class="input-field lg:w-44">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
                <option value="selesai">Selesai</option>
            </select>

            {{-- Date range --}}
            <div class="flex gap-2">
                <input type="date"
                       x-model="filters.tanggal_dari"
                       @change="fetch()"
                       class="input-field w-full lg:w-40"
                       title="Dari tanggal">
                <span class="self-center text-gray-400 text-sm flex-shrink-0">–</span>
                <input type="date"
                       x-model="filters.tanggal_sampai"
                       @change="fetch()"
                       class="input-field w-full lg:w-40"
                       title="Sampai tanggal">
            </div>

            {{-- Reset --}}
            <button type="button"
                    x-show="filters.search || filters.status || filters.tanggal_dari || filters.tanggal_sampai"
                    x-transition
                    @click="resetFilters()"
                    class="btn-ghost flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="card !p-0 overflow-hidden">

        {{-- Active filter chips --}}
        <div x-show="filters.search || filters.status || filters.tanggal_dari || filters.tanggal_sampai"
             x-transition
             class="px-5 py-3 border-b border-gray-100 dark:border-navy-700 flex flex-wrap gap-2 items-center">
            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Filter aktif:</span>
            <template x-if="filters.search">
                <span class="badge-info" x-text="'Kata kunci: &quot;' + filters.search + '&quot;'"></span>
            </template>
            <template x-if="filters.status">
                <span class="badge-info" x-text="'Status: ' + filters.status"></span>
            </template>
            <template x-if="filters.tanggal_dari || filters.tanggal_sampai">
                <span class="badge-info"
                      x-text="'Tanggal: ' + (filters.tanggal_dari || '...') + ' – ' + (filters.tanggal_sampai || '...')">
                </span>
            </template>
            <span class="text-xs text-gray-400" x-text="meta.total + ' hasil'"></span>
        </div>

        {{-- Loading overlay --}}
        <div x-show="loading" x-transition class="relative">
            <div class="absolute inset-0 bg-white/60 dark:bg-navy-900/60 z-10 flex items-center justify-center py-8">
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Memuat data...
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>No. Permintaan</th>
                        <th>Tanggal</th>
                        <th>Pegawai / Pemohon</th>
                        <th class="text-center">Item</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Empty state --}}
                    <template x-if="!loading && rows.length === 0">
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="empty-state-title">Belum ada permintaan ATK</p>
                                    <p class="empty-state-desc"
                                       x-text="(filters.search || filters.status || filters.tanggal_dari || filters.tanggal_sampai)
                                               ? 'Tidak ada hasil untuk filter yang dipilih.'
                                               : 'Mulai dengan membuat permintaan ATK baru.'">
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- Data rows --}}
                    <template x-for="(row, index) in rows" :key="row.id">
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-navy-700/30 transition-colors duration-150">
                            <td class="text-gray-400 text-xs"
                                x-text="(meta.current_page - 1) * meta.per_page + index + 1"></td>
                            <td>
                                <a :href="row.url_show"
                                   class="font-mono text-sm font-semibold text-navy-600 dark:text-navy-300 hover:underline"
                                   x-text="row.nomor_permintaan"></a>
                            </td>
                            <td class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap"
                                x-text="row.tanggal_formatted"></td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-navy-100 dark:bg-navy-700 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-navy-600 dark:text-navy-300"
                                              x-text="row.pegawai_initial"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[160px]"
                                           x-text="row.pegawai_nama"></p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[160px]"
                                           x-text="'oleh ' + row.user_nama"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge-gray" x-text="row.jumlah_item + ' item'"></span>
                            </td>
                            <td class="text-center">
                                <span :class="row.status_badge" x-text="row.status_label"></span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a :href="row.url_show" class="table-action-view" title="Lihat detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <template x-if="row.status === 'pending'">
                                        <a :href="row.url_edit" class="table-action-edit" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    </template>
                                    <template x-if="row.status === 'pending' || row.status === 'ditolak'">
                                        <button type="button"
                                                @click="confirmDelete(row)"
                                                class="table-action-delete" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div x-show="meta.last_page > 1"
             class="px-5 py-4 border-t border-gray-100 dark:border-navy-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Menampilkan
                <span class="font-semibold text-gray-700 dark:text-gray-300"
                      x-text="((meta.current_page - 1) * meta.per_page) + 1"></span>
                –
                <span class="font-semibold text-gray-700 dark:text-gray-300"
                      x-text="Math.min(meta.current_page * meta.per_page, meta.total)"></span>
                dari
                <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="meta.total"></span>
                data
            </p>
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                <button @click="goToPage(meta.current_page - 1)"
                        :disabled="meta.current_page <= 1"
                        class="btn-ghost btn-sm btn-icon disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Page numbers --}}
                <template x-for="page in pageNumbers" :key="page">
                    <button @click="page !== '...' && goToPage(page)"
                            :class="page === meta.current_page
                                ? 'bg-navy-600 text-white shadow-sm'
                                : page === '...'
                                    ? 'cursor-default text-gray-400'
                                    : 'btn-ghost'"
                            class="btn-sm btn-icon min-w-[32px] rounded-lg text-xs font-semibold"
                            x-text="page">
                    </button>
                </template>

                {{-- Next --}}
                <button @click="goToPage(meta.current_page + 1)"
                        :disabled="meta.current_page >= meta.last_page"
                        class="btn-ghost btn-sm btn-icon disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Delete confirm form (hidden) --}}
    <form id="deleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

</div>
@endsection

@push('scripts')
<script>
function permintaanIndex() {
    return {
        rows: [],
        stats: @json($stats),
        meta: {
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0,
        },
        filters: {
            search: '',
            status: '',
            tanggal_dari: '',
            tanggal_sampai: '',
        },
        loading: false,

        init() {
            this.fetch();
        },

        get pageNumbers() {
            const cur  = this.meta.current_page;
            const last = this.meta.last_page;
            if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);
            const pages = [];
            if (cur <= 4) {
                pages.push(1, 2, 3, 4, 5, '...', last);
            } else if (cur >= last - 3) {
                pages.push(1, '...', last-4, last-3, last-2, last-1, last);
            } else {
                pages.push(1, '...', cur-1, cur, cur+1, '...', last);
            }
            return pages;
        },

        async fetch() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filters.search)        params.set('search', this.filters.search);
                if (this.filters.status)         params.set('status', this.filters.status);
                if (this.filters.tanggal_dari)   params.set('tanggal_dari', this.filters.tanggal_dari);
                if (this.filters.tanggal_sampai) params.set('tanggal_sampai', this.filters.tanggal_sampai);
                params.set('page', this.meta.current_page);

                const res = await axios.get('{{ route('inventaris.permintaan-atk.data') }}', { params });
                this.rows  = res.data.data;
                this.stats = res.data.stats;
                this.meta  = res.data.meta;
            } catch (err) {
                showToast('Gagal memuat data', 'error');
            } finally {
                this.loading = false;
            }
        },

        goToPage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.meta.current_page = page;
            this.fetch();
        },

        filterByStatus(status) {
            this.filters.status = this.filters.status === status ? '' : status;
            this.meta.current_page = 1;
            this.fetch();
        },

        resetFilters() {
            this.filters = { search: '', status: '', tanggal_dari: '', tanggal_sampai: '' };
            this.meta.current_page = 1;
            this.fetch();
        },

        confirmDelete(row) {
            if (!confirm(`Hapus permintaan ${row.nomor_permintaan}? Tindakan ini tidak dapat dibatalkan.`)) return;
            const form = document.getElementById('deleteForm');
            form.action = row.url_destroy;
            form.submit();
        }
    }
}
</script>
@endpush
