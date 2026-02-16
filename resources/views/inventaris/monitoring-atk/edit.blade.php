{{-- resources/views/inventaris/monitoring-atk/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Monitoring ATK', 'url' => route('inventaris.monitoring-atk.index'), 'active' => false],
        ['title' => 'Edit ATK', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card">
        <div class="border-b border-gray-200 dark:border-navy-700 pb-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit ATK</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $monitoringAtk->nama }}</p>
        </div>

        <form action="{{ route('inventaris.monitoring-atk.update', $monitoringAtk) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kategori -->
                <div class="input-group">
                    <label class="input-label">Kategori ATK <span class="text-red-500">*</span></label>
                    <select name="kategori_id" class="input-field @error('kategori_id') border-red-500 @enderror" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ old('kategori_id', $monitoringAtk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nama ATK -->
                <div class="input-group">
                    <label class="input-label">Nama ATK <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $monitoringAtk->nama) }}"
                        class="input-field @error('nama') border-red-500 @enderror" required>
                    @error('nama')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Satuan -->
                <div class="input-group">
                    <label class="input-label">Satuan <span class="text-red-500">*</span></label>
                    <select name="satuan" class="input-field @error('satuan') border-red-500 @enderror" required>
                        <option value="">Pilih Satuan</option>
                        <option value="pcs" {{ old('satuan', $monitoringAtk->satuan) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="rim" {{ old('satuan', $monitoringAtk->satuan) == 'rim' ? 'selected' : '' }}>Rim</option>
                        <option value="box" {{ old('satuan', $monitoringAtk->satuan) == 'box' ? 'selected' : '' }}>Box</option>
                        <option value="lusin" {{ old('satuan', $monitoringAtk->satuan) == 'lusin' ? 'selected' : '' }}>Lusin</option>
                        <option value="pack" {{ old('satuan', $monitoringAtk->satuan) == 'pack' ? 'selected' : '' }}>Pack</option>
                        <option value="unit" {{ old('satuan', $monitoringAtk->satuan) == 'unit' ? 'selected' : '' }}>Unit</option>
                        <option value="set" {{ old('satuan', $monitoringAtk->satuan) == 'set' ? 'selected' : '' }}>Set</option>
                    </select>
                    @error('satuan')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Stok Minimum -->
                <div class="input-group">
                    <label class="input-label">Stok Minimum <span class="text-red-500">*</span></label>
                    <input type="number" name="stok_minimum"
                        value="{{ old('stok_minimum', $monitoringAtk->stok_minimum) }}"
                        class="input-field @error('stok_minimum') border-red-500 @enderror"
                        min="0" required>
                    @error('stok_minimum')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Stok Tersedia -->
                <div class="input-group">
                    <label class="input-label">Stok Tersedia <span class="text-red-500">*</span></label>
                    <input type="number" name="stok_tersedia"
                        value="{{ old('stok_tersedia', $monitoringAtk->stok_tersedia) }}"
                        class="input-field @error('stok_tersedia') border-red-500 @enderror"
                        min="0" required>
                    @error('stok_tersedia')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Harga Satuan -->
                <div class="input-group">
                    <label class="input-label">Harga Satuan <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_satuan"
                        value="{{ old('harga_satuan', $monitoringAtk->harga_satuan) }}"
                        class="input-field @error('harga_satuan') border-red-500 @enderror"
                        min="0" step="0.01" required>
                    @error('harga_satuan')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="input-group md:col-span-2">
                    <label class="input-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                        class="input-field @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $monitoringAtk->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('inventaris.monitoring-atk.index') }}" class="btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update ATK
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
