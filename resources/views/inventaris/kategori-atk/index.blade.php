{{-- resources/views/inventaris/kategori-atk/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kategori ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Kategori ATK', 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="page-title">Kategori ATK</h2>
        <p class="page-subtitle">Kelola kategori untuk pengelompokan Alat Tulis Kantor</p>
    </div>
    <a href="{{ route('inventaris.kategori-atk.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kategori
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Summary Stats ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Total Kategori</p>
                    <p class="stat-card-value">{{ $totalKategori }}</p>
                </div>
                <div class="p-2.5 bg-navy-100 dark:bg-navy-700 rounded-xl">
                    <svg class="w-6 h-6 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Total Item ATK</p>
                    <p class="stat-card-value">{{ $totalAtk }}</p>
                </div>
                <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Stok Menipis</p>
                    <p class="stat-card-value text-amber-600 dark:text-amber-400">{{ $totalMenipis }}</p>
                </div>
                <div class="p-2.5 bg-amber-100 dark:bg-amber-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Stok Kosong</p>
                    <p class="stat-card-value text-red-600 dark:text-red-400">{{ $totalKosong }}</p>
                </div>
                <div class="p-2.5 bg-red-100 dark:bg-red-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Alert stok kritis ── --}}
    @if($totalKosong > 0 || $totalMenipis > 0)
    <div class="alert-warning">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="flex-1">
            <p class="font-semibold text-sm">Perhatian Stok ATK</p>
            <p class="text-sm mt-0.5">
                Terdapat <strong>{{ $totalKosong }} item kosong</strong> dan <strong>{{ $totalMenipis }} item menipis</strong>.
                <a href="{{ route('inventaris.monitoring-atk.index') }}" class="underline font-medium hover:no-underline ml-1">Lihat Monitoring ATK →</a>
            </p>
        </div>
    </div>
    @endif

    {{-- ── Kategori Grid ── --}}
    @if($kategoris->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <p class="empty-state-title">Belum ada kategori ATK</p>
                <p class="empty-state-desc">Mulai dengan menambahkan kategori untuk mengelompokkan ATK</p>
                <a href="{{ route('inventaris.kategori-atk.create') }}" class="btn-primary btn-sm mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kategori Pertama
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($kategoris as $kategori)
            @php
                $countMenipis = $kategori->atk->where('status', 'menipis')->count();
                $countKosong  = $kategori->atk->where('status', 'kosong')->count();
                $hasCritical  = $countMenipis > 0 || $countKosong > 0;
            @endphp
            <div class="card group hover:-translate-y-0.5 transition-all duration-200 hover:shadow-md
                        {{ $hasCritical ? 'border-amber-200 dark:border-amber-700/50' : '' }}">

                {{-- Card Header --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-navy-100 to-navy-200
                                    dark:from-navy-700 dark:to-navy-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $kategori->nama }}</h3>
                            @if($kategori->deskripsi)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ $kategori->deskripsi }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <a href="{{ route('inventaris.kategori-atk.edit', $kategori) }}"
                           class="table-action-edit" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <button type="button"
                            x-data="confirmDelete('{{ route('inventaris.kategori-atk.destroy', $kategori) }}', '{{ addslashes($kategori->nama) }}')"
                            @click="submit()"
                            class="table-action-delete" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Status Pills --}}
                @if($hasCritical)
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @if($countKosong > 0)
                        <span class="badge-danger">{{ $countKosong }} kosong</span>
                    @endif
                    @if($countMenipis > 0)
                        <span class="badge-warning">{{ $countMenipis }} menipis</span>
                    @endif
                </div>
                @endif

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="bg-gray-50 dark:bg-navy-700/50 rounded-lg px-3 py-2 text-center">
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $kategori->atk_count }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Item ATK</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-navy-700/50 rounded-lg px-3 py-2 text-center">
                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($kategori->atk_sum_stok_tersedia ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Stok</p>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-navy-700">
                    <a href="{{ route('inventaris.kategori-atk.show', $kategori) }}"
                       class="btn-outline btn-sm flex-1 justify-center">
                        Lihat Detail
                    </a>
                    <a href="{{ route('inventaris.monitoring-atk.index', ['kategori_id' => $kategori->id]) }}"
                       class="btn-ghost btn-sm px-2.5" title="Monitoring ATK kategori ini">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- ── Pagination ── --}}
    @if($kategoris->hasPages())
    <div class="card !p-3">
        {{ $kategoris->links() }}
    </div>
    @endif

    {{-- ── Quick Links ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('inventaris.monitoring-atk.index') }}"
           class="card-flat flex items-center gap-3 hover:border-navy-300 dark:hover:border-navy-500 transition-colors group">
            <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-xl group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Monitoring ATK</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Lihat stok & pergerakan</p>
            </div>
        </a>

        <a href="{{ route('inventaris.permintaan-atk.index') }}"
           class="card-flat flex items-center gap-3 hover:border-navy-300 dark:hover:border-navy-500 transition-colors group">
            <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50 transition-colors">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Permintaan ATK</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kelola permintaan ATK</p>
            </div>
        </a>

        <a href="{{ route('inventaris.kategori-aset.index') }}"
           class="card-flat flex items-center gap-3 hover:border-navy-300 dark:hover:border-navy-500 transition-colors group">
            <div class="p-2.5 bg-purple-100 dark:bg-purple-900/30 rounded-xl group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Kategori Aset</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kelola kategori aset</p>
            </div>
        </a>
    </div>

</div>
@endsection
