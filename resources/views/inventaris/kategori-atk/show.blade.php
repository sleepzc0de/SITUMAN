{{-- resources/views/inventaris/kategori-atk/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Kategori ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Kategori ATK', 'url' => route('inventaris.kategori-atk.index'), 'active' => false],
        ['title' => 'Detail', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kategoriAtk->nama }}</h2>
            @if($kategoriAtk->deskripsi)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $kategoriAtk->deskripsi }}</p>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('inventaris.kategori-atk.edit', $kategoriAtk) }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total ATK</p>
                    <p class="text-2xl font-bold text-navy-700 dark:text-white mt-1">{{ $kategoriAtk->atk->count() }}</p>
                </div>
                <div class="p-3 bg-navy-100 dark:bg-navy-700 rounded-xl">
                    <svg class="w-8 h-8 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Stok Tersedia</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        {{ $kategoriAtk->atk->where('status', 'tersedia')->count() }}
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
                    <p class="text-sm text-gray-600 dark:text-gray-400">Stok Menipis</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                        {{ $kategoriAtk->atk->where('status', 'menipis')->count() }}
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ATK List -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daftar ATK</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama ATK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-navy-700">
                    @forelse($kategoriAtk->atk as $atk)
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $atk->nama }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $atk->kode_atk }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $atk->stok_tersedia }} {{ $atk->satuan }}
                            </td>
                            <td class="px-6 py-4">
                                @if($atk->status == 'tersedia')
                                    <span class="badge-success">Tersedia</span>
                                @elseif($atk->status == 'menipis')
                                    <span class="badge-warning">Menipis</span>
                                @else
                                    <span class="badge-danger">Kosong</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('inventaris.monitoring-atk.show', $atk) }}"
                                    class="text-navy-600 dark:text-navy-400 hover:text-navy-900">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                Belum ada ATK dalam kategori ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
