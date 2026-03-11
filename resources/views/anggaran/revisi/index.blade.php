@extends('layouts.app')
@section('title', 'Revisi Anggaran')

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.data.index') }}" class="breadcrumb-item">Anggaran</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Revisi Anggaran</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Revisi Anggaran</h1>
        <p class="page-subtitle">Riwayat perubahan pagu anggaran & audit trail</p>
    </div>
    <a href="{{ route('anggaran.revisi.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Revisi
    </a>
</div>
@endsection

@section('content')
<div
    x-data="revisiIndex()"
    x-init="init()"
    class="space-y-6"
>
    {{-- ===================== STAT CARDS ===================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Revisi --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="stat-card-label">Total Revisi</span>
                <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="stat-card-value" x-text="stats.total.toLocaleString('id-ID')">{{ number_format($stats['total']) }}</p>
            <p class="stat-card-sub text-gray-500">semua jenis revisi</p>
        </div>

        {{-- Pagu Naik --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="stat-card-label">Pagu Naik</span>
                <div class="w-9 h-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </div>
            </div>
            <p class="stat-card-value text-emerald-600 dark:text-emerald-400" x-text="stats.naik">{{ $stats['naik'] }}</p>
            <p class="stat-card-sub text-emerald-600 dark:text-emerald-400">revisi penambahan</p>
        </div>

        {{-- Pagu Turun --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="stat-card-label">Pagu Turun</span>
                <div class="w-9 h-9 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </div>
            </div>
            <p class="stat-card-value text-red-600 dark:text-red-400" x-text="stats.turun">{{ $stats['turun'] }}</p>
            <p class="stat-card-sub text-red-600 dark:text-red-400">revisi pengurangan</p>
        </div>

        {{-- Net Perubahan --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="stat-card-label">Net Perubahan</span>
                <div class="w-9 h-9 bg-navy-100 dark:bg-navy-700 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="stat-card-value text-sm"
               :class="stats.selisih >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
               x-text="formatRupiahShort(stats.selisih)">
                {{ $stats['selisih'] >= 0 ? '+' : '' }}{{ format_rupiah_short($stats['selisih']) }}
            </p>
            <p class="stat-card-sub text-gray-500">total selisih pagu</p>
        </div>
    </div>

    {{-- ===================== FILTER ===================== --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Jenis Revisi --}}
            <div class="flex-1">
                <label class="input-label">Jenis Revisi</label>
                <select
                    x-model="filter.jenis_revisi"
                    @change="onFilterChange()"
                    class="input-field"
                >
                    <option value="all">Semua Jenis</option>
                    @foreach($jenisRevisi as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter RO --}}
            <div class="flex-1">
                <label class="input-label">Filter RO</label>
                <select
                    x-model="filter.ro"
                    @change="onFilterChange()"
                    class="input-field"
                >
                    <option value="">Semua RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}">{{ $ro }} - {{ get_ro_name($ro) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Reset --}}
            <div class="flex items-end">
                <button
                    type="button"
                    x-show="filter.jenis_revisi !== 'all' || filter.ro !== ''"
                    x-transition
                    @click="resetFilter()"
                    class="btn btn-ghost w-full sm:w-auto"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== TABLE ===================== --}}
    <div class="card !p-0">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700">
            <p class="section-title">Daftar Revisi Anggaran</p>
            <p class="section-desc">
                <span x-text="total.toLocaleString('id-ID')">{{ $revisis->total() }}</span> data ditemukan
            </p>
        </div>

        <div class="table-wrapper rounded-t-none border-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Item Anggaran</th>
                        <th class="text-right">Pagu Sebelum</th>
                        <th class="text-right">Pagu Sesudah</th>
                        <th class="text-right">Selisih</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="revisi-tbody">
                    {{-- Render awal dari server (SSR) --}}
                    @forelse($revisis as $index => $revisi)
                        @php $selisih = $revisi->pagu_sesudah - $revisi->pagu_sebelum; @endphp
                        <tr>
                            <td class="text-gray-500 text-xs">{{ table_row_number($revisis, $index) }}</td>
                            <td>
                                <p class="font-medium text-gray-900 dark:text-white text-xs">
                                    {{ formatTanggalIndo($revisi->tanggal_revisi) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">oleh {{ $revisi->user->nama ?? '-' }}</p>
                            </td>
                            <td><span class="badge badge-purple">{{ $revisi->jenis_revisi }}</span></td>
                            <td>
                                <p class="font-medium text-gray-900 dark:text-white text-xs leading-relaxed">
                                    {{ truncate_text($revisi->anggaran->program_kegiatan ?? '-', 45) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $revisi->anggaran->ro ?? '' }}
                                    @if($revisi->anggaran->kode_akun) · {{ $revisi->anggaran->kode_akun }} @endif
                                </p>
                            </td>
                            <td class="text-right text-xs text-gray-600 dark:text-gray-400">
                                {{ format_rupiah($revisi->pagu_sebelum) }}
                            </td>
                            <td class="text-right text-xs font-semibold text-gray-900 dark:text-white">
                                {{ format_rupiah($revisi->pagu_sesudah) }}
                            </td>
                            <td class="text-right">
                                <span class="font-semibold text-xs {{ $selisih > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($selisih < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500') }}">
                                    {{ $selisih > 0 ? '+' : '' }}{{ format_rupiah($selisih) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('anggaran.revisi.show', $revisi) }}" class="table-action-view" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($revisi->dokumen_pendukung)
                                    <a href="{{ route('anggaran.revisi.download-dokumen', $revisi) }}" class="table-action-download" title="Download Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                    @endif
                                    @hasrole('superadmin')
                                    <button
                                        x-data="confirmDelete('{{ route('anggaran.revisi.destroy', $revisi) }}', 'revisi ini')"
                                        @click="submit()"
                                        class="table-action-delete" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endhasrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="empty-state-title">Belum ada revisi anggaran</p>
                                    <p class="empty-state-desc">Klik tombol "Tambah Revisi" untuk membuat revisi pertama</p>
                                    <a href="{{ route('anggaran.revisi.create') }}" class="btn btn-primary btn-sm mt-2">Tambah Revisi</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Loading overlay --}}
            <div
                x-show="loading"
                x-transition.opacity
                class="absolute inset-0 bg-white/60 dark:bg-navy-800/60 flex items-center justify-center rounded-b-xl z-10"
            >
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Memuat data...
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div id="pagination-wrapper" class="px-5 py-4 border-t border-gray-100 dark:border-navy-700">
            {{ $revisis->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function revisiIndex() {
    return {
        loading: false,
        total: {{ $revisis->total() }},
        filter: {
            jenis_revisi: 'all',
            ro: '',
            page: 1,
        },
        stats: {
            total:   {{ $stats['total'] }},
            naik:    {{ $stats['naik'] }},
            turun:   {{ $stats['turun'] }},
            selisih: {{ $stats['selisih'] }},
        },

        init() {
            // Tangkap klik pagination yang di-render ulang (event delegation)
            document.getElementById('pagination-wrapper').addEventListener('click', (e) => {
                const link = e.target.closest('a[href]');
                if (!link) return;
                const url  = new URL(link.href);
                const page = url.searchParams.get('page') || 1;
                e.preventDefault();
                this.filter.page = parseInt(page);
                this.fetchData();
            });
        },

        onFilterChange() {
            this.filter.page = 1; // reset ke halaman 1 setiap filter berubah
            this.fetchData();
        },

        resetFilter() {
            this.filter.jenis_revisi = 'all';
            this.filter.ro           = '';
            this.filter.page         = 1;
            this.fetchData();
        },

        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    jenis_revisi: this.filter.jenis_revisi,
                    ro:           this.filter.ro,
                    page:         this.filter.page,
                });

                const res  = await fetch(`{{ route('anggaran.revisi.index') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept':           'application/json',
                    }
                });

                if (!res.ok) throw new Error('Request gagal');
                const data = await res.json();

                this.total        = data.total;
                this.stats        = data.stats;
                this.renderRows(data.rows);
                this.renderPagination(data.pagination);
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        renderRows(rows) {
            const tbody = document.getElementById('revisi-tbody');
            if (!rows || rows.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="empty-state-title">Data tidak ditemukan</p>
                                <p class="empty-state-desc">Coba ubah atau reset filter</p>
                            </div>
                        </td>
                    </tr>`;
                return;
            }

            const isSuperadmin = {{ auth()->user()?->hasRole('superadmin') ? 'true' : 'false' }};

            tbody.innerHTML = rows.map(r => {
                const selisihClass = r.selisih_class === 'naik'
                    ? 'text-emerald-600 dark:text-emerald-400'
                    : r.selisih_class === 'turun'
                        ? 'text-red-600 dark:text-red-400'
                        : 'text-gray-500';

                const dokumenBtn = r.dokumen
                    ? `<a href="${r.dokumen}" class="table-action-download" title="Download Dokumen">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                       </a>`
                    : '';

                const deleteBtn = isSuperadmin
                    ? `<button
                            x-data="confirmDelete('${r.delete_url}', 'revisi ini')"
                            @click="submit()"
                            class="table-action-delete" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                       </button>`
                    : '';

                return `
                    <tr>
                        <td class="text-gray-500 text-xs">${r.no}</td>
                        <td>
                            <p class="font-medium text-gray-900 dark:text-white text-xs">${r.tanggal}</p>
                            <p class="text-xs text-gray-400 mt-0.5">oleh ${r.user}</p>
                        </td>
                        <td><span class="badge badge-purple">${r.jenis_revisi}</span></td>
                        <td>
                            <p class="font-medium text-gray-900 dark:text-white text-xs leading-relaxed">${r.program_kegiatan}</p>
                            <p class="text-xs text-gray-400 mt-0.5">${r.ro}${r.kode_akun ? ' · ' + r.kode_akun : ''}</p>
                        </td>
                        <td class="text-right text-xs text-gray-600 dark:text-gray-400">${r.pagu_sebelum}</td>
                        <td class="text-right text-xs font-semibold text-gray-900 dark:text-white">${r.pagu_sesudah}</td>
                        <td class="text-right">
                            <span class="font-semibold text-xs ${selisihClass}">${r.selisih}</span>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="${r.show_url}" class="table-action-view" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                ${dokumenBtn}
                                ${deleteBtn}
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            // Re-init Alpine untuk tombol delete yang baru di-render
            if (typeof Alpine !== 'undefined') {
                Alpine.initTree(tbody);
            }
        },

        renderPagination(html) {
            const wrapper = document.getElementById('pagination-wrapper');
            if (html && html.trim() !== '') {
                wrapper.innerHTML = html;
                wrapper.style.display = '';
            } else {
                wrapper.innerHTML = '';
            }
        },

        formatRupiahShort(value) {
            const abs = Math.abs(value);
            const prefix = value >= 0 ? '+' : '-';
            if (abs >= 1_000_000_000) return prefix + 'Rp ' + (abs / 1_000_000_000).toFixed(1) + ' M';
            if (abs >= 1_000_000)     return prefix + 'Rp ' + (abs / 1_000_000).toFixed(1) + ' jt';
            if (abs >= 1_000)         return prefix + 'Rp ' + (abs / 1_000).toFixed(1) + ' rb';
            return prefix + 'Rp ' + abs.toLocaleString('id-ID');
        },
    }
}
</script>
@endpush
