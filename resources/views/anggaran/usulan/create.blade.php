@extends('layouts.app')

@section('title', 'Tambah Usulan Penarikan Dana')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.usulan.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Usulan Penarikan Dana
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Tambah Usulan</li>
        </ol>
    </nav>

    <form action="{{ route('anggaran.usulan.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Usulan</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">RO <span class="text-red-500">*</span></label>
                    <select name="ro" id="ro" class="input-field @error('ro') border-red-500 @enderror" required>
                        <option value="">Pilih RO</option>
                        @foreach($roList as $ro)
                            <option value="{{ $ro }}" {{ old('ro') == $ro ? 'selected' : '' }}>
                                {{ $ro }} - {{ get_ro_name($ro) }}
                            </option>
                        @endforeach
                    </select>
                    @error('ro')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Sub Komponen <span class="text-red-500">*</span></label>
                    <input type="text" name="sub_komponen" value="{{ old('sub_komponen') }}"
                           class="input-field @error('sub_komponen') border-red-500 @enderror"
                           placeholder="Kode atau nama sub komponen" required>
                    @error('sub_komponen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Bulan <span class="text-red-500">*</span></label>
                    <select name="bulan" class="input-field @error('bulan') border-red-500 @enderror" required>
                        <option value="">Pilih Bulan</option>
                        @foreach($bulanList as $bulan)
                            <option value="{{ $bulan }}" {{ old('bulan') == $bulan ? 'selected' : '' }}>
                                {{ ucfirst($bulan) }}
                            </option>
                        @endforeach
                    </select>
                    @error('bulan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Nilai Usulan <span class="text-red-500">*</span></label>
                    <input type="number" name="nilai_usulan" value="{{ old('nilai_usulan') }}"
                           class="input-field @error('nilai_usulan') border-red-500 @enderror"
                           placeholder="0" step="0.01" required>
                    @error('nilai_usulan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group md:col-span-2">
                    <label class="input-label">Keterangan</label>
                    <textarea name="keterangan" rows="4"
                              class="input-field @error('keterangan') border-red-500 @enderror"
                              placeholder="Keterangan usulan penarikan dana">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="flex justify-end gap-3">
                <a href="{{ route('anggaran.usulan.index') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Usulan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
