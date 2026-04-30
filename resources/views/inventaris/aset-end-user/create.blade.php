{{-- resources/views/inventaris/aset-end-user/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Aset')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Aset End User', 'url' => route('inventaris.aset-end-user.index'), 'active' => false],
        ['title' => 'Tambah Aset', 'url' => null, 'active' => true],
    ]" />
@endsection

@push('head')
    <style>
        select.input-field {
            -webkit-appearance: auto;
            appearance: auto;
            pointer-events: auto;
        }
        form [contenteditable] {
            -webkit-user-modify: read-only;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="card">
            <div class="border-b border-gray-200 dark:border-navy-700 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Aset Baru</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi form di bawah ini untuk menambahkan aset baru</p>
            </div>

            <form action="{{ route('inventaris.aset-end-user.store') }}" method="POST"
                  x-data="{
                      nama_aset: @js(old('nama_aset', '')),
                      merek: @js(old('merek', '')),
                      tipe: @js(old('tipe', '')),
                      nomor_seri: @js(old('nomor_seri', '')),
                      deskripsi: @js(old('deskripsi', '')),
                      catatan: @js(old('catatan', ''))
                  }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kategori --}}
                    <div class="input-group">
                        <label class="input-label">Kategori Aset <span class="text-red-500">*</span></label>
                        <select name="kategori_id" class="input-field @error('kategori_id') border-red-500 @enderror" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nama Aset --}}
                    <div class="input-group">
                        <label class="input-label">Nama Aset <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_aset" value="{{ old('nama_aset') }}"
                            x-model="nama_aset"
                            class="input-field @error('nama_aset') border-red-500 @enderror"
                            placeholder="Contoh: Laptop Dell Latitude"
                            minlength="3"
                            maxlength="255"
                            required>
                        @error('nama_aset')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @else
                            <span class="text-xs text-gray-500 mt-1">
                                <span x-text="nama_aset.length"></span>/255 karakter
                            </span>
                        @enderror
                    </div>

                    {{-- Merek --}}
                    <div class="input-group">
                        <label class="input-label">Merek</label>
                        <input type="text" name="merek" value="{{ old('merek') }}"
                            x-model="merek"
                            class="input-field @error('merek') border-red-500 @enderror"
                            placeholder="Contoh: Dell"
                            maxlength="100">
                        @error('merek')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @else
                            <span class="text-xs text-gray-500 mt-1">
                                <span x-text="merek.length"></span>/100 karakter
                            </span>
                        @enderror
                    </div>

                    {{-- Tipe --}}
                    <div class="input-group">
                        <label class="input-label">Tipe/Model</label>
                        <input type="text" name="tipe" value="{{ old('tipe') }}"
                            x-model="tipe"
                            class="input-field @error('tipe') border-red-500 @enderror"
                            placeholder="Contoh: Latitude 5420"
                            maxlength="100">
                        @error('tipe')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @else
                            <span class="text-xs text-gray-500 mt-1">
                                <span x-text="tipe.length"></span>/100 karakter
                            </span>
                        @enderror
                    </div>

                    {{-- Nomor Seri --}}
                    <div class="input-group">
                        <label class="input-label">Nomor Seri</label>
                        <input type="text" name="nomor_seri" value="{{ old('nomor_seri') }}"
                            x-model="nomor_seri"
                            class="input-field @error('nomor_seri') border-red-500 @enderror"
                            placeholder="Contoh: SN123456789"
                            maxlength="100">
                        @error('nomor_seri')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @else
                            <span class="text-xs text-gray-500 mt-1">
                                <span x-text="nomor_seri.length"></span>/100 karakter
                            </span>
                        @enderror
                    </div>

                    {{-- Tanggal Perolehan --}}
                    <div class="input-group">
                        <label class="input-label">Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan" value="{{ old('tanggal_perolehan') }}"
                            class="input-field @error('tanggal_perolehan') border-red-500 @enderror"
                            max="{{ date('Y-m-d') }}">
                        @error('tanggal_perolehan')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nilai Perolehan --}}
                    <div class="input-group">
                        <label class="input-label">Nilai Perolehan <span class="text-red-500">*</span></label>
                        <input type="number" name="nilai_perolehan" value="{{ old('nilai_perolehan', 0) }}"
                            class="input-field @error('nilai_perolehan') border-red-500 @enderror"
                            min="0" max="99999999999" step="0.01" required>
                        @error('nilai_perolehan')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Kondisi --}}
                    <div class="input-group">
                        <label class="input-label">Kondisi <span class="text-red-500">*</span></label>
                        <select name="kondisi" class="input-field @error('kondisi') border-red-500 @enderror" required autocomplete="off">
                            <option value="baik" {{ old('kondisi', 'baik') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak ringan" {{ old('kondisi') == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak berat" {{ old('kondisi') == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="hilang" {{ old('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                        @error('kondisi')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="input-group md:col-span-2">
                        <label class="input-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="3"
                            x-model="deskripsi"
                            maxlength="2000"
                            class="input-field @error('deskripsi') border-red-500 @enderror"
                            placeholder="Deskripsi detail aset...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @else
                            <span class="text-xs text-gray-500 mt-1">
                                <span x-text="deskripsi.length"></span>/2000 karakter
                            </span>
                        @enderror
                    </div>

                    {{-- Catatan --}}
                    <div class="input-group md:col-span-2">
                        <label class="input-label">Catatan</label>
                        <textarea name="catatan" rows="2"
                            x-model="catatan"
                            maxlength="2000"
                            class="input-field @error('catatan') border-red-500 @enderror"
                            placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @else
                            <span class="text-xs text-gray-500 mt-1">
                                <span x-text="catatan.length"></span>/2000 karakter
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-navy-700">
                    <a href="{{ route('inventaris.aset-end-user.index') }}" class="btn-outline">Batal</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Aset
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
