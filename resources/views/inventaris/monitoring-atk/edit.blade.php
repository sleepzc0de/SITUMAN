{{-- resources/views/inventaris/monitoring-atk/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit ATK')

@section('breadcrumb')
<x-breadcrumb :items="[
    ['title' => 'Inventaris', 'url' => null, 'active' => false],
    ['title' => 'Monitoring ATK', 'url' => route('inventaris.monitoring-atk.index'), 'active' => false],
    ['title' => $monitoringAtk->nama, 'url' => route('inventaris.monitoring-atk.show', $monitoringAtk), 'active' => false],
    ['title' => 'Edit', 'url' => null, 'active' => true],
]" />
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('inventaris.monitoring-atk.show', $monitoringAtk) }}"
           class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors mt-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="page-title truncate">{{ $monitoringAtk->nama }}</h1>
            <div class="flex items-center gap-2 mt-1">
                <code class="text-xs bg-navy-50 dark:bg-navy-700 text-navy-700 dark:text-navy-300 px-2 py-0.5 rounded font-mono">
                    {{ $monitoringAtk->kode_atk }}
                </code>
                @if($monitoringAtk->status === 'tersedia')
                    <span class="badge-success">Tersedia</span>
                @elseif($monitoringAtk->status === 'menipis')
                    <span class="badge-warning">Menipis</span>
                @else
                    <span class="badge-danger">Kosong</span>
                @endif
            </div>
        </div>
    </div>

    <form action="{{ route('inventaris.monitoring-atk.update', $monitoringAtk) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Section: Identitas --}}
        <div class="card space-y-5">
            <div class="section-header !mb-0">
                <div>
                    <h3 class="section-title">Identitas ATK</h3>
                    <p class="section-desc">Terakhir diperbarui {{ format_tanggal($monitoringAtk->updated_at, 'd M Y H:i') }}</p>
                </div>
            </div>
            <div class="divider"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Kategori --}}
                <div class="input-group">
                    <label class="input-label" for="kategori_id">
                        Kategori ATK <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori_id" name="kategori_id"
                        class="input-field @error('kategori_id') input-error @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $k)
                        <option value="{{ $k->id }}"
                            {{ old('kategori_id', $monitoringAtk->kategori_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                    <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama --}}
                <div class="input-group">
                    <label class="input-label" for="nama">
                        Nama ATK <span class="text-red-500">*</span>
                    </label>
                    <input id="nama" type="text" name="nama"
                        value="{{ old('nama', $monitoringAtk->nama) }}"
                        class="input-field @error('nama') input-error @enderror" required>
                    @error('nama')
                    <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Satuan --}}
                <div class="input-group">
                    <label class="input-label" for="satuan">
                        Satuan <span class="text-red-500">*</span>
                    </label>
                    <select id="satuan" name="satuan"
                        class="input-field @error('satuan') input-error @enderror" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach(['pcs' => 'Pcs', 'rim' => 'Rim', 'box' => 'Box', 'lusin' => 'Lusin', 'pack' => 'Pack', 'unit' => 'Unit', 'set' => 'Set'] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('satuan', $monitoringAtk->satuan) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('satuan')
                    <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga --}}
                <div class="input-group">
                    <label class="input-label" for="harga_satuan">
                        Harga Satuan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 dark:text-gray-500 text-sm pointer-events-none">Rp</span>
                        <input id="harga_satuan" type="number" name="harga_satuan"
                            value="{{ old('harga_satuan', $monitoringAtk->harga_satuan) }}"
                            class="input-field pl-9 @error('harga_satuan') input-error @enderror"
                            min="0" step="1" required>
                    </div>
                    @error('harga_satuan')
                    <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="input-group">
                <label class="input-label" for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    class="input-field @error('deskripsi') input-error @enderror">{{ old('deskripsi', $monitoringAtk->deskripsi) }}</textarea>
                @error('deskripsi')
                <p class="input-hint-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Section: Stok --}}
        <div class="card space-y-5 mt-6">
            <div class="section-header !mb-0">
                <div>
                    <h3 class="section-title">Pengaturan Stok</h3>
                    <p class="section-desc">Perubahan stok lewat form ini akan langsung diterapkan</p>
                </div>
            </div>
            <div class="divider"></div>

            {{-- Info stok saat ini --}}
            <div class="grid grid-cols-3 gap-3 p-4 bg-gray-50 dark:bg-navy-700/40 rounded-xl">
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Stok Saat Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $monitoringAtk->stok_tersedia }}</p>
                    <p class="text-xs text-gray-400">{{ $monitoringAtk->satuan }}</p>
                </div>
                <div class="text-center border-x border-gray-200 dark:border-navy-600">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Stok Minimum</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $monitoringAtk->stok_minimum }}</p>
                    <p class="text-xs text-gray-400">{{ $monitoringAtk->satuan }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Nilai Stok</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                        {{ format_rupiah_short($monitoringAtk->stok_tersedia * $monitoringAtk->harga_satuan) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="input-group">
                    <label class="input-label" for="stok_tersedia">
                        Stok Tersedia <span class="text-red-500">*</span>
                    </label>
                    <input id="stok_tersedia" type="number" name="stok_tersedia"
                        value="{{ old('stok_tersedia', $monitoringAtk->stok_tersedia) }}"
                        class="input-field @error('stok_tersedia') input-error @enderror"
                        min="0" required>
                    @error('stok_tersedia')
                    <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label" for="stok_minimum">
                        Stok Minimum <span class="text-red-500">*</span>
                    </label>
                    <input id="stok_minimum" type="number" name="stok_minimum"
                        value="{{ old('stok_minimum', $monitoringAtk->stok_minimum) }}"
                        class="input-field @error('stok_minimum') input-error @enderror"
                        min="0" required>
                    @error('stok_minimum')
                    <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between gap-3 mt-6">
            <a href="{{ route('inventaris.monitoring-atk.show', $monitoringAtk) }}" class="btn-ghost">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
