{{-- resources/views/inventaris/monitoring-atk/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Monitoring ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Monitoring ATK', 'url' => null, 'active' => true],
    ]" />
@endsection

@section('content')
    <div class="space-y-6">

        {{-- ── Stat Cards ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @php
                $statCards = [
                    [
                        'label' => 'Total Item',
                        'value' => $stats['total_item'],
                        'color' => 'navy',
                        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    ],
                    [
                        'label' => 'Stok Tersedia',
                        'value' => $stats['stok_tersedia'],
                        'color' => 'green',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'label' => 'Stok Menipis',
                        'value' => $stats['stok_menipis'],
                        'color' => 'yellow',
                        'icon' =>
                            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    ],
                    [
                        'label' => 'Stok Kosong',
                        'value' => $stats['stok_kosong'],
                        'color' => 'red',
                        'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                ];
                $colorMap = [
                    'navy' => [
                        'bg' => 'bg-navy-100 dark:bg-navy-700',
                        'icon' => 'text-navy-600 dark:text-navy-300',
                        'val' => 'text-navy-700 dark:text-white',
                    ],
                    'green' => [
                        'bg' => 'bg-green-100 dark:bg-green-900/30',
                        'icon' => 'text-green-600 dark:text-green-400',
                        'val' => 'text-green-600 dark:text-green-400',
                    ],
                    'yellow' => [
                        'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                        'icon' => 'text-yellow-600 dark:text-yellow-400',
                        'val' => 'text-yellow-600 dark:text-yellow-400',
                    ],
                    'red' => [
                        'bg' => 'bg-red-100 dark:bg-red-900/30',
                        'icon' => 'text-red-600 dark:text-red-400',
                        'val' => 'text-red-600 dark:text-red-400',
                    ],
                ];
            @endphp

            @foreach ($statCards as $card)
                @php $c = $colorMap[$card['color']] @endphp
                <div class="card flex items-center gap-4">
                    <div class="p-3 {{ $c['bg'] }} rounded-xl flex-shrink-0">
                        <svg class="w-7 h-7 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold {{ $c['val'] }} mt-0.5">{{ $card['value'] }}</p>
                    </div>
                </div>
            @endforeach

            {{-- Total Nilai --}}
            <div class="card flex items-center gap-4 col-span-2 lg:col-span-1">
                <div class="p-3 bg-gold-100 dark:bg-gold-900/30 rounded-xl flex-shrink-0">
                    <svg class="w-7 h-7 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Nilai Stok</p>
                    <p class="text-lg font-bold text-gold-600 dark:text-gold-400 mt-0.5 truncate">
                        {{ format_rupiah_short($stats['total_nilai']) }}</p>
                </div>
            </div>
        </div>

        {{-- ── Alert stok kritis ── --}}
        @if ($stats['stok_kosong'] > 0 || $stats['stok_menipis'] > 0)
            <div class="alert-warning flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-semibold text-sm">Perhatian — kondisi stok kritis</p>
                    <p class="text-sm mt-0.5">
                        Terdapat <strong>{{ $stats['stok_kosong'] }}</strong> item habis dan
                        <strong>{{ $stats['stok_menipis'] }}</strong> item menipis.
                        Segera lakukan pengadaan atau pembaruan stok.
                    </p>
                </div>
            </div>
        @endif

        {{-- ── Filter & Aksi ── --}}
        <div class="card">
            <div class="flex flex-col xl:flex-row xl:items-end gap-4">
                {{-- Form filter --}}
                <form method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="input-group">
                        <label class="input-label">Cari</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Nama atau kode ATK…" maxlength="100" class="input-field pl-9">
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Kategori</label>
                        <select name="kategori" class="input-field">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoris as $k)
                                <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Status</label>
                        <select name="status" class="input-field">
                            <option value="">Semua Status</option>
                            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>✓ Tersedia
                            </option>
                            <option value="menipis" {{ request('status') == 'menipis' ? 'selected' : '' }}>⚠ Menipis
                            </option>
                            <option value="kosong" {{ request('status') == 'kosong' ? 'selected' : '' }}>✕ Kosong
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary flex-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filter
                        </button>
                        @if (request()->hasAny(['search', 'kategori', 'status']))
                            <a href="{{ route('inventaris.monitoring-atk.index') }}" class="btn-ghost btn-icon"
                                title="Reset filter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Tombol aksi --}}
                <div class="flex items-center gap-2 flex-wrap xl:flex-nowrap xl:flex-shrink-0">
                    {{-- Dropdown Export/Import --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button" class="btn-outline gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export / Import
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 top-full mt-2 w-64 bg-white dark:bg-navy-800 rounded-xl shadow-xl border border-gray-100 dark:border-navy-700 py-1 z-50 origin-top-right"
                            style="display:none;">

                            <p
                                class="px-4 pt-2 pb-1 text-[10px] font-bold text-gray-400 dark:text-navy-500 uppercase tracking-widest">
                                Export</p>
                            <a href="{{ route('inventaris.monitoring-atk.export') }}" @click="open=false"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700/60 transition-colors">
                                <span
                                    class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="font-medium">Export Excel</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Download seluruh data ATK</p>
                                </div>
                            </a>

                            <div class="divider my-1"></div>
                            <p
                                class="px-4 pt-1 pb-1 text-[10px] font-bold text-gray-400 dark:text-navy-500 uppercase tracking-widest">
                                Import</p>

                            <a href="{{ route('inventaris.monitoring-atk.template') }}" @click="open=false"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700/60 transition-colors">
                                <span
                                    class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="font-medium">Download Template</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Template Excel untuk import</p>
                                </div>
                            </a>

                            <a href="{{ route('inventaris.monitoring-atk.import-form') }}" @click="open=false"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700/60 transition-colors rounded-b-xl">
                                <span
                                    class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="font-medium">Import Data</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Upload file Excel/CSV</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('inventaris.monitoring-atk.create') }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah ATK
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Tabel ── --}}
        <div class="card !p-0 overflow-hidden">
            {{-- Header tabel --}}
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between gap-3">
                <div>
                    <h3 class="section-title">Daftar ATK</h3>
                    <p class="section-desc">{{ $atk->total() }} item ditemukan</p>
                </div>
                @if (request()->hasAny(['search', 'kategori', 'status']))
                    <div class="flex flex-wrap gap-1.5">
                        @if (request('search'))
                            <span class="badge-info">Cari: "{{ request('search') }}"</span>
                        @endif
                        @if (request('kategori'))
                            <span class="badge-info">Kat:
                                {{ $kategoris->firstWhere('id', request('kategori'))?->nama }}</span>
                        @endif
                        @if (request('status'))
                            <span class="badge-info">Status: {{ ucfirst(request('status')) }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="table-wrapper !rounded-none !border-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">No</th>
                            <th>Kode</th>
                            <th>Nama ATK</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Harga Satuan</th>
                            <th class="w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($atk as $index => $item)
                            <tr>
                                <td class="text-gray-400 dark:text-gray-500 text-xs">
                                    {{ $atk->firstItem() + $index }}
                                </td>
                                <td>
                                    <code
                                        class="text-xs bg-navy-50 dark:bg-navy-700 text-navy-700 dark:text-navy-300 px-2 py-0.5 rounded-md font-mono">
                                        {{ $item->kode_atk }}
                                    </code>
                                </td>
                                <td>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $item->nama }}</p>
                                    @if ($item->deskripsi)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 line-clamp-2">
                                            {{ $item->deskripsi }}</p>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-gray">{{ $item->kategori->nama }}</span>
                                </td>
                                <td class="text-gray-600 dark:text-gray-400">{{ $item->satuan }}</td>
                                <td>
                                    @php
                                        $pct =
                                            $item->stok_minimum > 0
                                                ? min(100, round(($item->stok_tersedia / $item->stok_minimum) * 100))
                                                : 100;
                                        $barColor =
                                            $item->status === 'tersedia'
                                                ? 'bg-green-500'
                                                : ($item->status === 'menipis'
                                                    ? 'bg-yellow-500'
                                                    : 'bg-red-500');
                                    @endphp
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                        {{ number_format($item->stok_tersedia) }}
                                        <span class="text-xs font-normal text-gray-400">/ min
                                            {{ number_format($item->stok_minimum) }}</span>
                                    </p>
                                    <div class="progress-bar-wrap mt-1.5" style="height:4px;">
                                        <div class="{{ $barColor }} h-full rounded-full transition-all duration-500"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->status === 'tersedia')
                                        <span class="badge-success">Tersedia</span>
                                    @elseif($item->status === 'menipis')
                                        <span class="badge-warning">Menipis</span>
                                    @else
                                        <span class="badge-danger">Kosong</span>
                                    @endif
                                </td>
                                <td class="text-gray-600 dark:text-gray-400 tabular-nums">
                                    {{ format_rupiah($item->harga_satuan) }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('inventaris.monitoring-atk.show', $item) }}"
                                            class="table-action-view" title="Lihat detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('inventaris.monitoring-atk.edit', $item) }}"
                                            class="table-action-edit" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button type="button" x-data="confirmDelete('{{ route('inventaris.monitoring-atk.destroy', $item) }}', '{{ addslashes($item->nama) }}')" @click="submit()"
                                            class="table-action-delete" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="empty-state-title">Tidak ada data ATK</p>
                                        <p class="empty-state-desc">
                                            @if (request()->hasAny(['search', 'kategori', 'status']))
                                                Coba ubah filter pencarian Anda
                                            @else
                                                Mulai dengan menambahkan ATK baru atau import data
                                            @endif
                                        </p>
                                        @if (!request()->hasAny(['search', 'kategori', 'status']))
                                            <a href="{{ route('inventaris.monitoring-atk.create') }}"
                                                class="btn-primary btn-sm mt-2">
                                                Tambah ATK Pertama
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($atk->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-navy-700">
                    {{ $atk->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
