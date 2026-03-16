{{-- resources/views/inventaris/kategori-atk/show.blade.php --}}
@extends('layouts.app')

@section('title', $kategoriAtk->nama)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris',       'url' => null, 'active' => false],
        ['title' => 'Kategori ATK',     'url' => route('inventaris.kategori-atk.index'), 'active' => false],
        ['title' => $kategoriAtk->nama, 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-navy-500 to-navy-700
                    flex items-center justify-center shadow-md flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
        </div>
        <div>
            <h2 class="page-title">{{ $kategoriAtk->nama }}</h2>
            <p class="page-subtitle">
                {{ $kategoriAtk->deskripsi ?? 'Tidak ada deskripsi' }}
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('inventaris.monitoring-atk.index', ['kategori_id' => $kategoriAtk->id]) }}"
           class="btn-outline btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Monitoring
        </a>
        <a href="{{ route('inventaris.kategori-atk.edit', $kategoriAtk) }}" class="btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-card-label">Total Item</p>
            <p class="stat-card-value">{{ $stats['total'] }}</p>
            <p class="stat-card-sub text-gray-500 dark:text-gray-400">item ATK</p>
        </div>
        <div class="stat-card">
            <p class="stat-card-label">Stok Tersedia</p>
            <p class="stat-card-value text-emerald-600 dark:text-emerald-400">{{ $stats['tersedia'] }}</p>
            <p class="stat-card-sub text-emerald-600 dark:text-emerald-400">item normal</p>
        </div>
        <div class="stat-card">
            <p class="stat-card-label">Stok Menipis</p>
            <p class="stat-card-value text-amber-600 dark:text-amber-400">{{ $stats['menipis'] }}</p>
            <p class="stat-card-sub text-amber-600 dark:text-amber-400">perlu restock</p>
        </div>
        <div class="stat-card">
            <p class="stat-card-label">Stok Kosong</p>
            <p class="stat-card-value text-red-600 dark:text-red-400">{{ $stats['kosong'] }}</p>
            <p class="stat-card-sub text-red-600 dark:text-red-400">tidak tersedia</p>
        </div>
    </div>

    {{-- ── Nilai Inventaris ── --}}
    @if($stats['nilai'] > 0)
    <div class="card-flat flex items-center gap-3">
        <div class="p-2.5 bg-gold-100 dark:bg-gold-900/20 rounded-xl">
            <svg class="w-5 h-5 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Estimasi Nilai Inventaris Kategori Ini</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ format_rupiah($stats['nilai']) }}</p>
        </div>
    </div>
    @endif

    {{-- ── Alert kritis ── --}}
    @if($stats['kosong'] > 0 || $stats['menipis'] > 0)
    <div class="alert-warning">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-sm">
            Terdapat <strong>{{ $stats['kosong'] }} item kosong</strong> dan
            <strong>{{ $stats['menipis'] }} item menipis</strong> pada kategori ini.
            Pertimbangkan untuk melakukan
            <a href="{{ route('inventaris.permintaan-atk.create') }}" class="underline font-semibold hover:no-underline">
                permintaan restock
            </a>.
        </p>
    </div>
    @endif

    {{-- ── ATK Table ── --}}
    <div class="card">
        <div class="section-header">
            <div>
                <h3 class="section-title">Daftar Item ATK</h3>
                <p class="section-desc">{{ $stats['total'] }} item dalam kategori ini</p>
            </div>
            <a href="{{ route('inventaris.monitoring-atk.index', ['kategori_id' => $kategoriAtk->id]) }}"
               class="btn-ghost btn-sm text-navy-600 dark:text-navy-400">
                Lihat di Monitoring →
            </a>
        </div>

        @if($atks->isEmpty())
            <div class="empty-state !py-10">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="empty-state-title">Belum ada ATK</p>
                <p class="empty-state-desc">Tambahkan item ATK melalui menu Monitoring ATK</p>
                <a href="{{ route('inventaris.monitoring-atk.create') }}" class="btn-primary btn-sm mt-2">
                    Tambah ATK Baru
                </a>
            </div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama ATK</th>
                            <th>Satuan</th>
                            <th>Stok Tersedia</th>
                            <th>Stok Minimum</th>
                            <th>Harga Satuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atks as $atk)
                        <tr>
                            <td class="text-gray-400 text-xs">
                                {{ ($atks->currentPage() - 1) * $atks->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <span class="font-mono text-xs bg-gray-100 dark:bg-navy-700 px-2 py-0.5 rounded
                                             text-gray-600 dark:text-gray-300">
                                    {{ $atk->kode_atk }}
                                </span>
                            </td>
                            <td>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $atk->nama }}</p>
                                @if($atk->deskripsi)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $atk->deskripsi }}</p>
                                @endif
                            </td>
                            <td class="text-gray-600 dark:text-gray-400">{{ $atk->satuan }}</td>
                            <td>
                                <span class="font-semibold
                                    {{ $atk->stok_tersedia == 0
                                        ? 'text-red-600 dark:text-red-400'
                                        : ($atk->stok_tersedia <= $atk->stok_minimum
                                            ? 'text-amber-600 dark:text-amber-400'
                                            : 'text-gray-900 dark:text-white') }}">
                                    {{ number_format($atk->stok_tersedia) }}
                                </span>
                            </td>
                            <td class="text-gray-500 dark:text-gray-400 text-sm">
                                {{ number_format($atk->stok_minimum) }}
                            </td>
                            <td class="text-gray-700 dark:text-gray-300 text-sm">
                                {{ format_rupiah($atk->harga_satuan) }}
                            </td>
                            <td>
                                @if($atk->status === 'tersedia')
                                    <span class="badge-success">Tersedia</span>
                                @elseif($atk->status === 'menipis')
                                    <span class="badge-warning">Menipis</span>
                                @else
                                    <span class="badge-danger">Kosong</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('inventaris.monitoring-atk.show', $atk) }}"
                                       class="table-action-view" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('inventaris.monitoring-atk.edit', $atk) }}"
                                       class="table-action-edit" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($atks->hasPages())
            <div class="mt-4">
                {{ $atks->links() }}
            </div>
            @endif
        @endif
    </div>

    {{-- ── Metadata ── --}}
    <div class="card-flat grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">ID Kategori</p>
            <p class="font-mono text-xs text-gray-600 dark:text-gray-300 mt-1 break-all">{{ $kategoriAtk->id }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Dibuat</p>
            <p class="text-gray-700 dark:text-gray-300 mt-1">{{ format_tanggal($kategoriAtk->created_at) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Terakhir Diperbarui</p>
            <p class="text-gray-700 dark:text-gray-300 mt-1">{{ format_tanggal($kategoriAtk->updated_at) }}</p>
        </div>
    </div>

</div>
@endsection
