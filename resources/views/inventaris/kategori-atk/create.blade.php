{{-- resources/views/inventaris/kategori-atk/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Kategori ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris',   'url' => null, 'active' => false],
        ['title' => 'Kategori ATK', 'url' => route('inventaris.kategori-atk.index'), 'active' => false],
        ['title' => 'Tambah',       'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div>
    <h2 class="page-title">Tambah Kategori ATK</h2>
    <p class="page-subtitle">Buat kategori baru untuk mengelompokkan Alat Tulis Kantor</p>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- ── Form Card ── --}}
    <div class="card">
        <form action="{{ route('inventaris.kategori-atk.store') }}" method="POST"
              x-data="{ nama: '{{ old('nama') }}', deskripsi: '{{ old('deskripsi') }}' }">
            @csrf

            <div class="space-y-5">

                {{-- Nama Kategori --}}
                <div class="input-group">
                    <label for="nama" class="input-label">
                        Nama Kategori
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama"
                        x-model="nama"
                        class="input-field @error('nama') input-error @enderror"
                        placeholder="Contoh: Alat Tulis, Kertas, Tinta..."
                        autocomplete="off"
                        required>
                    @error('nama')
                        <p class="input-hint-error">{{ $message }}</p>
                    @else
                        <p class="input-hint">Nama kategori harus unik dan deskriptif</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="input-group">
                    <label for="deskripsi" class="input-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                        x-model="deskripsi"
                        class="input-field @error('deskripsi') input-error @enderror"
                        placeholder="Deskripsi singkat tentang jenis ATK dalam kategori ini...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="input-hint-error">{{ $message }}</p>
                    @else
                        <p class="input-hint" x-text="deskripsi.length + ' karakter'"></p>
                    @enderror
                </div>

                {{-- Preview --}}
                <div x-show="nama.length > 0" x-transition
                     class="rounded-xl border border-dashed border-navy-300 dark:border-navy-600 p-4
                            bg-navy-50/50 dark:bg-navy-800/50">
                    <p class="text-xs font-semibold text-navy-600 dark:text-navy-400 mb-2 uppercase tracking-wider">
                        Preview Kategori
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-navy-100 to-navy-200
                                    dark:from-navy-700 dark:to-navy-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm" x-text="nama"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"
                               x-text="deskripsi.length > 0 ? deskripsi : 'Tidak ada deskripsi'"></p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-navy-700">
                <a href="{{ route('inventaris.kategori-atk.index') }}" class="btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>

    {{-- ── Info Box ── --}}
    <div class="alert-info">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm">
            <p class="font-semibold">Panduan Kategori ATK</p>
            <p class="mt-1 opacity-90">
                Kategori digunakan untuk mengelompokkan item ATK di modul <strong>Monitoring ATK</strong>.
                Setelah kategori dibuat, Anda dapat menambahkan item ATK melalui menu Monitoring ATK.
            </p>
        </div>
    </div>

</div>
@endsection
