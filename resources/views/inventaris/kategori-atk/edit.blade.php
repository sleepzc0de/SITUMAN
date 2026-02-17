{{-- resources/views/inventaris/kategori-atk/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Kategori ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Kategori ATK', 'url' => route('inventaris.kategori-atk.index'), 'active' => false],
        ['title' => 'Edit Kategori', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="border-b border-gray-200 dark:border-navy-700 pb-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Kategori ATK</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $kategoriAtk->nama }}</p>
        </div>

        <form action="{{ route('inventaris.kategori-atk.update', $kategoriAtk) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Nama Kategori -->
                <div class="input-group">
                    <label class="input-label">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $kategoriAtk->nama) }}"
                        class="input-field @error('nama') border-red-500 @enderror" required>
                    @error('nama')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="input-group">
                    <label class="input-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="4"
                        class="input-field @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $kategoriAtk->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('inventaris.kategori-atk.index') }}" class="btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
