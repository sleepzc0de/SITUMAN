{{-- resources/views/inventaris/kategori-atk/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit — ' . $kategoriAtk->nama)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris',           'url' => null, 'active' => false],
        ['title' => 'Kategori ATK',         'url' => route('inventaris.kategori-atk.index'), 'active' => false],
        ['title' => $kategoriAtk->nama,     'url' => route('inventaris.kategori-atk.show', $kategoriAtk), 'active' => false],
        ['title' => 'Edit',                 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div class="flex items-start justify-between gap-4">
    <div>
        <h2 class="page-title">Edit Kategori ATK</h2>
        <p class="page-subtitle">Perbarui informasi kategori "{{ $kategoriAtk->nama }}"</p>
    </div>
    <a href="{{ route('inventaris.kategori-atk.show', $kategoriAtk) }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Lihat Detail
    </a>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- ── Info strip ── --}}
    <div class="card-flat flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-navy-100 to-navy-200
                    dark:from-navy-700 dark:to-navy-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $kategoriAtk->nama }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ $kategoriAtk->atk_count ?? $kategoriAtk->atk()->count() }} item ATK terdaftar
                · Dibuat {{ $kategoriAtk->created_at->diffForHumans() }}
            </p>
        </div>
    </div>

    {{-- ── Form Card ── --}}
    <div class="card">
        <form action="{{ route('inventaris.kategori-atk.update', $kategoriAtk) }}" method="POST"
              x-data="{
                  nama: '{{ old('nama', addslashes($kategoriAtk->nama)) }}',
                  deskripsi: '{{ old('deskripsi', addslashes($kategoriAtk->deskripsi ?? '')) }}'
              }">
            @csrf
            @method('PUT')

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
                        autocomplete="off"
                        required>
                    @error('nama')
                        <p class="input-hint-error">{{ $message }}</p>
                    @else
                        <p class="input-hint">Nama kategori harus unik di seluruh sistem</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="input-group">
                    <label for="deskripsi" class="input-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                        x-model="deskripsi"
                        class="input-field @error('deskripsi') input-error @enderror">{{ old('deskripsi', $kategoriAtk->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="input-hint-error">{{ $message }}</p>
                    @else
                        <p class="input-hint" x-text="deskripsi.length + ' karakter'"></p>
                    @enderror
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-navy-700">
                <a href="{{ route('inventaris.kategori-atk.index') }}" class="btn-ghost btn-sm text-gray-500">
                    ← Kembali ke Daftar
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('inventaris.kategori-atk.show', $kategoriAtk) }}" class="btn-outline">
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Danger Zone ── --}}
    @if(($kategoriAtk->atk_count ?? $kategoriAtk->atk()->count()) === 0)
    <div class="card border-red-200 dark:border-red-800/50">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-700 dark:text-red-400">Zona Berbahaya</p>
                <p class="text-xs text-red-600/80 dark:text-red-400/70 mt-1">
                    Kategori ini tidak memiliki ATK terdaftar dan dapat dihapus secara permanen.
                </p>
            </div>
            <button type="button"
                x-data="confirmDelete('{{ route('inventaris.kategori-atk.destroy', $kategoriAtk) }}', '{{ addslashes($kategoriAtk->nama) }}')"
                @click="submit()"
                class="btn-danger btn-sm flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus Kategori
            </button>
        </div>
    </div>
    @endif

</div>
@endsection
