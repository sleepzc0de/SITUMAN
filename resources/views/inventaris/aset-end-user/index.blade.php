{{-- resources/views/inventaris/aset-end-user/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Aset End User')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Aset End User', 'url' => null, 'active' => true],
    ]" />
@endsection

@push('styles')
    <style>
        /* Fix dropdown agar tidak terpotong */
        .dropdown-container {
            overflow: visible !important;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Aset</p>
                        <p class="text-2xl font-bold text-navy-700 dark:text-white mt-1">{{ $stats['total_aset'] }}</p>
                    </div>
                    <div class="p-3 bg-navy-100 dark:bg-navy-700 rounded-xl">
                        <svg class="w-8 h-8 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tersedia</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['tersedia'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Dipinjam</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['dipinjam'] }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Nilai</p>
                        <p class="text-xl font-bold text-gold-600 dark:text-gold-400 mt-1">
                            {{ format_rupiah($stats['total_nilai']) }}</p>
                    </div>
                    <div class="p-3 bg-gold-100 dark:bg-gold-900/30 rounded-xl">
                        <svg class="w-8 h-8 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Actions - SAMA SEPERTI ATK -->
        <div class="bg-white/80 dark:bg-navy-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 dark:border-navy-700 p-6 mb-6"
            style="position: relative; z-index: 20;">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4" style="position: relative;">
                <div class="flex-1">
                    <form method="GET" class="flex flex-col md:flex-row gap-3">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama aset, kode, nomor seri, atau pegawai..." class="input-field flex-1">

                        <select name="kategori" class="input-field">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" class="input-field">
                            <option value="">Semua Status</option>
                            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia
                            </option>
                            <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam
                            </option>
                            <option value="diperbaiki" {{ request('status') == 'diperbaiki' ? 'selected' : '' }}>Diperbaiki
                            </option>
                            <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak
                                Aktif</option>
                        </select>

                        <select name="kondisi" class="input-field">
                            <option value="">Semua Kondisi</option>
                            <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak ringan" {{ request('kondisi') == 'rusak ringan' ? 'selected' : '' }}>Rusak
                                Ringan</option>
                            <option value="rusak berat" {{ request('kondisi') == 'rusak berat' ? 'selected' : '' }}>Rusak
                                Berat</option>
                            <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>

                        <button type="submit" class="btn-primary whitespace-nowrap">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari
                        </button>

                        @if (request()->hasAny(['search', 'kategori', 'status', 'kondisi']))
                            <a href="{{ route('inventaris.aset-end-user.index') }}" class="btn-outline whitespace-nowrap">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <div class="flex items-center gap-3" style="position: relative; z-index: 30;">
                    <!-- Dropdown Export/Import -->
                    <div x-data="{ open: false }" class="relative" style="z-index: 50;">
                        <button @click="open = !open" type="button"
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 border-2 border-navy-600 text-navy-600 hover:bg-navy-50 focus:ring-navy-500 dark:border-navy-400 dark:text-navy-400 dark:hover:bg-navy-900/30 whitespace-nowrap">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export / Import
                            <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu - ROUTE SUDAH DIPERBAIKI -->
                        <div x-show="open" x-cloak @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-72 origin-top-right bg-white dark:bg-navy-800 rounded-xl shadow-2xl border border-gray-200 dark:border-navy-700 py-2"
                            style="z-index: 9999; position: absolute;">

                            <!-- Header Export -->
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-navy-700">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Export</p>
                            </div>

                            <!-- Export Data Aset - ROUTE DIPERBAIKI -->
                            <a href="{{ url('inventaris/aset-end-user-export') }}" @click="open = false"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                                <div
                                    class="flex items-center justify-center w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 dark:text-white">Export Data Aset</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Download sebagai file
                                        Excel</div>
                                </div>
                            </a>

                            <!-- Divider -->
                            <div class="border-t border-gray-100 dark:border-navy-700 my-2"></div>

                            <!-- Header Import -->
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-navy-700">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Import</p>
                            </div>

                            <!-- Download Template - ROUTE DIPERBAIKI -->
                            <a href="{{ url('inventaris/aset-end-user-template') }}" @click="open = false"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                                <div
                                    class="flex items-center justify-center w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 dark:text-white">Download Template</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Template Excel untuk
                                        import</div>
                                </div>
                            </a>

                            <!-- Import Data Aset - ROUTE DIPERBAIKI -->
                            <a href="{{ url('inventaris/aset-end-user-import') }}" @click="open = false"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors rounded-b-xl">
                                <div
                                    class="flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 dark:text-white">Import Data Aset</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Upload file Excel/CSV
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('inventaris.aset-end-user.create') }}" class="btn-primary whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Aset
                    </a>
                </div>

            </div>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-navy-800">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                No</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Kode</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama Aset</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Kategori</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Kondisi</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Pengguna</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-900 divide-y divide-gray-200 dark:divide-navy-700">
                        @forelse($aset as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-navy-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $aset->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-mono text-navy-600 dark:text-navy-300">{{ $item->kode_aset }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->nama_aset }}
                                    </div>
                                    @if ($item->merek)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $item->merek }} {{ $item->tipe ? '- ' . $item->tipe : '' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->kategori->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($item->kondisi == 'baik')
                                        <span class="badge-success">Baik</span>
                                    @elseif($item->kondisi == 'rusak ringan')
                                        <span class="badge-warning">Rusak Ringan</span>
                                    @elseif($item->kondisi == 'rusak berat')
                                        <span class="badge-danger">Rusak Berat</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400">Hilang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($item->status == 'tersedia')
                                        <span class="badge-success">Tersedia</span>
                                    @elseif($item->status == 'dipinjam')
                                        <span class="badge-warning">Dipinjam</span>
                                    @elseif($item->status == 'diperbaiki')
                                        <span class="badge-info">Diperbaiki</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400">Tidak
                                            Aktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->pegawai)
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->pegawai->nama }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ format_tanggal($item->tanggal_peminjaman, 'd/m/Y') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('inventaris.aset-end-user.show', $item) }}"
                                            class="text-navy-600 dark:text-navy-400 hover:text-navy-900 dark:hover:text-navy-300"
                                            title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('inventaris.aset-end-user.edit', $item) }}"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('inventaris.aset-end-user.destroy', $item) }}"
                                            method="POST" onsubmit="return confirm('Yakin ingin menghapus aset ini?')"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada data aset</p>
                                    <p class="text-sm mt-1">Mulai dengan menambahkan aset baru atau import data</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($aset->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-navy-700">
                    {{ $aset->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
