{{-- resources/views/inventaris/kategori-atk/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kategori ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Kategori ATK', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori ATK</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola kategori untuk ATK</p>
        </div>
        <a href="{{ route('inventaris.kategori-atk.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </a>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($kategoris as $kategori)
            <div class="card-hover">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $kategori->nama }}</h3>
                        @if($kategori->deskripsi)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($kategori->deskripsi, 100) }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <a href="{{ route('inventaris.kategori-atk.edit', $kategori) }}"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                            title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('inventaris.kategori-atk.destroy', $kategori) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-navy-700">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="font-semibold">{{ $kategori->atk_count }}</span>
                        <span class="ml-1">ATK</span>
                    </div>
                    <a href="{{ route('inventaris.kategori-atk.show', $kategori) }}"
                        class="text-navy-600 dark:text-navy-400 hover:text-navy-900 dark:hover:text-navy-300 text-sm font-medium">
                        Lihat Detail →
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="card text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada kategori</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Mulai dengan menambahkan kategori ATK</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($kategoris->hasPages())
        <div class="card">
            {{ $kategoris->links() }}
        </div>
    @endif
</div>
@endsection
