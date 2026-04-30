{{-- resources/views/inventaris/kategori-aset/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Kategori Aset')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Kategori Aset', 'url' => route('inventaris.kategori-aset.index'), 'active' => false],
        ['title' => 'Tambah Kategori', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="border-b border-gray-200 dark:border-navy-700 pb-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Kategori Aset</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Buat kategori baru untuk mengorganisir aset end user</p>
        </div>

        <form action="{{ route('inventaris.kategori-aset.store') }}" method="POST"
              x-data="{
                  nama: @js(old('nama', '')),
                  deskripsi: @js(old('deskripsi', ''))
              }">
            @csrf
            <div class="space-y-6">
                <div class="input-group">
                    <label class="input-label">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                        x-model="nama"
                        class="input-field @error('nama') border-red-500 @enderror"
                        placeholder="Contoh: Komputer & Laptop"
                        minlength="3"
                        maxlength="100"
                        required>
                    @error('nama')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @else
                        <span class="text-xs text-gray-500 mt-1">
                            <span x-text="nama.length"></span>/100 karakter
                        </span>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="4"
                        x-model="deskripsi"
                        maxlength="2000"
                        class="input-field @error('deskripsi') border-red-500 @enderror"
                        placeholder="Deskripsi kategori...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @else
                        <span class="text-xs text-gray-500 mt-1">
                            <span x-text="deskripsi.length"></span>/2000 karakter
                        </span>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('inventaris.kategori-aset.index') }}" class="btn-outline">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
