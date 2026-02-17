{{-- resources/views/inventaris/kategori-aset/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Kategori Aset')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Kategori Aset', 'url' => route('inventaris.kategori-aset.index'), 'active' => false],
        ['title' => 'Detail', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kategoriAset->nama }}</h2>
            @if($kategoriAset->deskripsi)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $kategoriAset->deskripsi }}</p>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('inventaris.kategori-aset.edit', $kategoriAset) }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Aset</p>
                    <p class="text-2xl font-bold text-navy-700 dark:text-white mt-1">{{ $kategoriAset->aset->count() }}</p>
                </div>
                <div class="p-3 bg-navy-100 dark:bg-navy-700 rounded-xl">
                    <svg class="w-8 h-8 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Tersedia</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        {{ $kategoriAset->aset->where('status', 'tersedia')->count() }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Dipinjam</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                        {{ $kategoriAset->aset->where('status', 'dipinjam')->count() }}
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Nilai</p>
                    <p class="text-lg font-bold text-gold-600 dark:text-gold-400 mt-1">
                        {{ format_rupiah($kategoriAset->aset->sum('nilai_perolehan')) }}
                    </p>
                </div>
                <div class="p-3 bg-gold-100 dark:bg-gold-900/30 rounded-xl">
                    <svg class="w-8 h-8 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Aset List -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daftar Aset</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Merek</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-navy-700">
                    @forelse($kategoriAset->aset as $aset)
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $aset->nama_aset }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $aset->kode_aset }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $aset->merek ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($aset->kondisi == 'baik')
                                    <span class="badge-success">Baik</span>
                                @elseif($aset->kondisi == 'rusak ringan')
                                    <span class="badge-warning">Rusak Ringan</span>
                                @elseif($aset->kondisi == 'rusak berat')
                                    <span class="badge-danger">Rusak Berat</span>
                                @else
                                    <span class="badge">Hilang</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($aset->status == 'tersedia')
                                    <span class="badge-success">Tersedia</span>
                                @elseif($aset->status == 'dipinjam')
                                    <span class="badge-warning">Dipinjam</span>
                                @elseif($aset->status == 'diperbaiki')
                                    <span class="badge-info">Diperbaiki</span>
                                @else
                                    <span class="badge">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $aset->pegawai->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('inventaris.aset-end-user.show', $aset) }}"
                                    class="text-navy-600 dark:text-navy-400 hover:text-navy-900">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                Belum ada aset dalam kategori ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
