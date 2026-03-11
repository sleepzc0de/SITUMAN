@extends('layouts.app')

@section('title', 'Usulan Penarikan Dana')

@section('breadcrumb')
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item">Anggaran</span>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Usulan Penarikan Dana</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="page-title">Usulan Penarikan Dana</h1>
        <p class="page-subtitle">Kelola dan pantau usulan rencana penarikan dana per sub komponen</p>
    </div>
    <a href="{{ route('anggaran.usulan.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Usulan
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6"
     x-data="{
         filters: {
             status: '{{ request('status', 'all') }}',
             bulan:  '{{ request('bulan',  'all') }}',
             ro:     '{{ request('ro',     'all') }}',
         },
         currentPage: {{ request('page', 1) }},
         loading: false,
         tableHtml: '',
         summaryData: {
             pending:  {{ $summary['pending'] }},
             approved: {{ $summary['approved'] }},
             rejected: {{ $summary['rejected'] }},
         },

         get hasActiveFilter() {
             return this.filters.status !== 'all'
                 || this.filters.bulan  !== 'all'
                 || this.filters.ro     !== 'all';
         },

         async fetchData(resetPage = true) {
             if (resetPage) this.currentPage = 1;
             this.loading = true;

             const params = new URLSearchParams({
                 status: this.filters.status,
                 bulan:  this.filters.bulan,
                 ro:     this.filters.ro,
                 page:   this.currentPage,
                 ajax:   '1',
             });

             try {
                 const res  = await fetch(`{{ route('anggaran.usulan.index') }}?${params}`, {
                     headers: {
                         'X-Requested-With': 'XMLHttpRequest',
                         'Accept': 'application/json',
                     }
                 });
                 const json = await res.json();

                 this.tableHtml   = json.tableHtml;
                 this.summaryData = json.summary;

                 // Update URL tanpa reload
                 const url = new URL(window.location);
                 Object.entries(this.filters).forEach(([k, v]) => {
                     v === 'all' ? url.searchParams.delete(k) : url.searchParams.set(k, v);
                 });
                 this.currentPage > 1
                     ? url.searchParams.set('page', this.currentPage)
                     : url.searchParams.delete('page');
                 window.history.replaceState({}, '', url);

             } catch (e) {
                 console.error('Fetch error:', e);
             } finally {
                 this.loading = false;
             }
         },

         changePage(p) {
             this.currentPage = p;
             this.fetchData(false);
             window.scrollTo({ top: 0, behavior: 'smooth' });
         },

         resetFilters() {
             this.filters = { status: 'all', bulan: 'all', ro: 'all' };
             this.fetchData();
         },

         formatRupiah(v) {
             v = parseFloat(v) || 0;
             if (v >= 1e12) return 'Rp ' + (v / 1e12).toFixed(1) + 'T';
             if (v >= 1e9)  return 'Rp ' + (v / 1e9).toFixed(1)  + 'M';
             if (v >= 1e6)  return 'Rp ' + (v / 1e6).toFixed(1)  + 'jt';
             if (v >= 1e3)  return 'Rp ' + (v / 1e3).toFixed(0)  + 'rb';
             return 'Rp ' + v;
         }
     }"
     x-init="
         tableHtml = $refs.tableContainer.innerHTML;
         window.addEventListener('change-page', e => changePage(e.detail.page));
     ">

    {{-- ===== SUMMARY CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="card flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/30
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="stat-card-label">Menunggu Persetujuan</p>
                <p class="stat-card-value text-yellow-600 dark:text-yellow-400"
                   x-text="formatRupiah(summaryData.pending)"></p>
                <p class="stat-card-sub text-yellow-500">Total nilai pending</p>
            </div>
        </div>

        <div class="card flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="stat-card-label">Disetujui</p>
                <p class="stat-card-value text-emerald-600 dark:text-emerald-400"
                   x-text="formatRupiah(summaryData.approved)"></p>
                <p class="stat-card-sub text-emerald-500">Total nilai disetujui</p>
            </div>
        </div>

        <div class="card flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="stat-card-label">Ditolak</p>
                <p class="stat-card-value text-red-600 dark:text-red-400"
                   x-text="summaryData.rejected"></p>
                <p class="stat-card-sub text-red-500">Jumlah usulan ditolak</p>
            </div>
        </div>
    </div>

    {{-- ===== FILTER & TABLE CARD ===== --}}
    <div class="card">

        {{-- Filter Bar --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-5">

            <div class="flex-1 min-w-0">
                <select x-model="filters.status" @change="fetchData()"
                        class="input-field">
                    <option value="all">Semua Status</option>
                    <option value="pending">⏳ Menunggu</option>
                    <option value="approved">✅ Disetujui</option>
                    <option value="rejected">❌ Ditolak</option>
                </select>
            </div>

            <div class="flex-1 min-w-0">
                <select x-model="filters.bulan" @change="fetchData()"
                        class="input-field">
                    <option value="all">Semua Bulan</option>
                    @foreach(['januari','februari','maret','april','mei','juni',
                               'juli','agustus','september','oktober','november','desember'] as $bulan)
                        <option value="{{ $bulan }}">{{ ucfirst($bulan) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-0">
                <select x-model="filters.ro" @change="fetchData()"
                        class="input-field">
                    <option value="all">Semua RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}">{{ $ro }} – {{ get_ro_name($ro) }}</option>
                    @endforeach
                </select>
            </div>

            <button type="button"
                    x-show="hasActiveFilter"
                    x-transition
                    @click="resetFilters()"
                    class="btn btn-ghost flex-shrink-0 text-xs"
                    style="display:none;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset
            </button>
        </div>

        {{-- Table Wrapper dengan loading state --}}
        <div class="relative">

            {{-- Loading Overlay --}}
            <div x-show="loading"
                 x-transition:enter="transition-opacity duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 z-10 flex items-center justify-center
                        bg-white/75 dark:bg-navy-900/75 rounded-xl backdrop-blur-sm"
                 style="display:none;">
                <div class="flex items-center gap-3 px-5 py-3 bg-white dark:bg-navy-800
                            rounded-xl shadow-lg border border-gray-100 dark:border-navy-700">
                    <svg class="w-5 h-5 text-navy-600 dark:text-navy-400 animate-spin"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Memuat data...
                    </span>
                </div>
            </div>

            {{-- Table Container --}}
            <div x-ref="tableContainer"
                 x-html="tableHtml"
                 :class="loading ? 'opacity-40 pointer-events-none select-none' : ''"
                 class="transition-opacity duration-200 min-h-[200px]">

                {{-- ── SSR: dirender server saat pertama load ── --}}
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-10">No</th>
                                <th>RO</th>
                                <th>Sub Komponen</th>
                                <th>Bulan</th>
                                <th class="text-right">Nilai Usulan</th>
                                <th>Pengusul</th>
                                <th>Tgl Pengajuan</th>
                                <th>Status</th>
                                <th class="text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usulans as $index => $usulan)
                            <tr>
                                <td class="text-gray-400 text-xs">
                                    {{ table_row_number($usulans, $index) }}
                                </td>

                                <td>
                                    <span class="badge badge-blue">{{ $usulan->ro }}</span>
                                </td>

                                <td>
                                    <p class="font-medium text-gray-800 dark:text-gray-200 line-clamp-2"
                                       title="{{ $usulan->sub_komponen }}">
                                        {{ truncate_text($usulan->sub_komponen, 45) }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ ucfirst($usulan->bulan) }}
                                    </span>
                                </td>

                                <td class="text-right">
                                    <span class="font-semibold text-gray-900 dark:text-white
                                                 text-sm tabular-nums">
                                        {{ format_rupiah($usulan->nilai_usulan) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-navy-100 dark:bg-navy-700
                                                    flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-bold text-navy-700 dark:text-navy-300">
                                                {{ get_initials($usulan->user->nama) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300
                                                     truncate max-w-[110px]">
                                            {{ $usulan->user->nama }}
                                        </span>
                                    </div>
                                </td>

                                <td class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ format_tanggal_short($usulan->created_at) }}
                                </td>

                                <td>
                                    @php
                                        $badgeMap = [
                                            'pending'  => 'badge-pending',
                                            'approved' => 'badge-approved',
                                            'rejected' => 'badge-rejected',
                                        ];
                                    @endphp
                                    <span class="{{ $badgeMap[$usulan->status] ?? 'badge-gray' }}">
                                        {{ status_text($usulan->status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="flex items-center justify-center gap-1">

                                        {{-- Detail --}}
                                        <a href="{{ route('anggaran.usulan.show', $usulan) }}"
                                           class="table-action-view" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        @if($usulan->status === 'pending')

                                            {{-- Edit --}}
                                            <a href="{{ route('anggaran.usulan.edit', $usulan) }}"
                                               class="table-action-edit" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            @hasrole('superadmin|admin')

                                            {{-- Approve --}}
                                            <form action="{{ route('anggaran.usulan.approve', $usulan) }}"
                                                  method="POST" class="inline"
                                                  x-data
                                                  @submit.prevent="if(confirm('Setujui usulan ini?')) $el.submit()">
                                                @csrf
                                                <button type="submit"
                                                        class="table-action text-emerald-600 hover:text-emerald-800
                                                               hover:bg-emerald-50 dark:text-emerald-400
                                                               dark:hover:bg-emerald-900/20"
                                                        title="Setujui">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            </form>

                                            {{-- Reject --}}
                                            <button type="button"
                                                    x-data
                                                    @click="$dispatch('open-reject-modal', {
                                                        action: '{{ route('anggaran.usulan.reject', $usulan) }}'
                                                    })"
                                                    class="table-action-delete" title="Tolak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                                                </svg>
                                            </button>

                                            @endhasrole

                                            {{-- Delete --}}
                                            <form action="{{ route('anggaran.usulan.destroy', $usulan) }}"
                                                  method="POST" class="inline"
                                                  x-data
                                                  @submit.prevent="if(confirm('Hapus usulan ini? Tidak dapat dibatalkan.')) $el.submit()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="table-action-delete" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>

                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <svg class="w-8 h-8 text-gray-400" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="1.5"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="empty-state-title">Belum ada usulan penarikan dana</p>
                                        <p class="empty-state-desc">
                                            Tidak ada data yang cocok dengan filter yang dipilih
                                        </p>
                                        <a href="{{ route('anggaran.usulan.create') }}"
                                           class="btn btn-primary btn-sm mt-2">
                                            Tambah Sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination SSR --}}
                @if($usulans->hasPages())
                <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3
                            text-sm text-gray-500 dark:text-gray-400">
                    <span>
                        Menampilkan {{ $usulans->firstItem() }}–{{ $usulans->lastItem() }}
                        dari {{ $usulans->total() }} data
                    </span>
                    <div class="flex items-center gap-1">
                        {{-- Prev --}}
                        @if($usulans->onFirstPage())
                            <span class="btn btn-ghost btn-sm opacity-40 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <button type="button" x-data
                                    @click="$dispatch('change-page', { page: {{ $usulans->currentPage() - 1 }} })"
                                    class="btn btn-ghost btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                        @endif

                        {{-- Page numbers --}}
                        @foreach($usulans->getUrlRange(
                            max(1, $usulans->currentPage() - 2),
                            min($usulans->lastPage(), $usulans->currentPage() + 2)
                        ) as $pg => $url)
                            <button type="button" x-data
                                    @click="$dispatch('change-page', { page: {{ $pg }} })"
                                    class="btn btn-sm {{ $pg == $usulans->currentPage() ? 'btn-primary' : 'btn-ghost' }}">
                                {{ $pg }}
                            </button>
                        @endforeach

                        {{-- Next --}}
                        @if($usulans->hasMorePages())
                            <button type="button" x-data
                                    @click="$dispatch('change-page', { page: {{ $usulans->currentPage() + 1 }} })"
                                    class="btn btn-ghost btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @else
                            <span class="btn btn-ghost btn-sm opacity-40 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
                @endif
                {{-- ── END SSR ── --}}

            </div>{{-- end x-ref="tableContainer" --}}
        </div>{{-- end relative --}}
    </div>{{-- end card --}}
</div>{{-- end space-y-6 --}}

{{-- ===== MODAL REJECT ===== --}}
<div x-data="{
         open: false,
         action: '',
         init() {
             window.addEventListener('open-reject-modal', e => {
                 this.action = e.detail.action;
                 this.open   = true;
             });
         }
     }"
     x-show="open"
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-50 flex items-center justify-center
            bg-black/50 backdrop-blur-sm p-4"
     style="display:none;">

    <div @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="modal-box w-full max-w-md">

        <div class="modal-header">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                Tolak Usulan
            </h3>
            <button @click="open = false" class="btn btn-ghost btn-icon">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form :action="action" method="POST">
            @csrf
            <div class="modal-body space-y-4">
                <div class="alert alert-warning">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="text-sm">
                        Penolakan bersifat permanen dan tidak dapat diubah kembali.
                    </span>
                </div>
                <div class="input-group">
                    <label class="input-label">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keterangan" rows="4" class="input-field"
                              placeholder="Jelaskan alasan penolakan secara detail..."
                              required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" @click="open = false" class="btn btn-outline">
                    Batal
                </button>
                <button type="submit" class="btn btn-danger">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                    </svg>
                    Tolak Usulan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
