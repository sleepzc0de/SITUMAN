@extends('layouts.app')

@section('title', 'Edit Data Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.data.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Kelola Data Anggaran
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Edit Data</li>
        </ol>
    </nav>

    <form action="{{ route('anggaran.data.update', $data) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Informasi Kode -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Kode</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">Kode Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kegiatan" value="{{ old('kegiatan', $data->kegiatan) }}"
                           class="input-field @error('kegiatan') border-red-500 @enderror"
                           placeholder="Contoh: 4753" required>
                    @error('kegiatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">KRO <span class="text-red-500">*</span></label>
                    <input type="text" name="kro" value="{{ old('kro', $data->kro) }}"
                           class="input-field @error('kro') border-red-500 @enderror"
                           placeholder="Contoh: EBA" required>
                    @error('kro')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">RO <span class="text-red-500">*</span></label>
                    <select name="ro" class="input-field @error('ro') border-red-500 @enderror" required>
                        <option value="">Pilih RO</option>
                        @foreach($roList as $ro)
                            <option value="{{ $ro }}" {{ old('ro', $data->ro) == $ro ? 'selected' : '' }}>
                                {{ $ro }} - {{ get_ro_name($ro) }}
                            </option>
                        @endforeach
                    </select>
                    @error('ro')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Level Item</label>
                    <input type="text" class="input-field bg-gray-100 dark:bg-navy-700"
                           value="@if(!$data->kode_akun)@if(!$data->kode_subkomponen)RO (Parent)@else Sub Komponen @endif @else Akun (Detail) @endif"
                           readonly>
                </div>

                @if($data->kode_subkomponen)
                <div class="input-group">
                    <label class="input-label">Kode Sub Komponen</label>
                    <input type="text" name="kode_subkomponen" value="{{ old('kode_subkomponen', $data->kode_subkomponen) }}"
                           class="input-field @error('kode_subkomponen') border-red-500 @enderror"
                           placeholder="Contoh: AA, AB, AC">
                    @error('kode_subkomponen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                @if($data->kode_akun)
                <div class="input-group">
                    <label class="input-label">Kode Akun</label>
                    <input type="text" name="kode_akun" value="{{ old('kode_akun', $data->kode_akun) }}"
                           class="input-field @error('kode_akun') border-red-500 @enderror"
                           placeholder="Contoh: 521211, 524111">
                    @error('kode_akun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif
            </div>
        </div>

        <!-- Informasi Detail -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Detail</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group md:col-span-2">
                    <label class="input-label">Program/Kegiatan/Output/Komponen/Akun <span class="text-red-500">*</span></label>
                    <textarea name="program_kegiatan" rows="3"
                              class="input-field @error('program_kegiatan') border-red-500 @enderror"
                              placeholder="Uraian lengkap program/kegiatan" required>{{ old('program_kegiatan', $data->program_kegiatan) }}</textarea>
                    @error('program_kegiatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" value="{{ old('pic', $data->pic) }}"
                           class="input-field @error('pic') border-red-500 @enderror"
                           placeholder="Penanggung Jawab" required>
                    @error('pic')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Pagu Anggaran <span class="text-red-500">*</span></label>
                    @if(!$data->kode_akun)
                        <input type="number" class="input-field bg-gray-100 dark:bg-navy-700"
                               value="{{ old('pagu_anggaran', $data->pagu_anggaran) }}"
                               placeholder="0" step="0.01" readonly>
                        <p class="text-xs text-gray-500 mt-1">
                            Pagu otomatis dihitung dari child items. Edit pagu pada level Akun (Detail).
                        </p>
                        <input type="hidden" name="pagu_anggaran" value="{{ $data->pagu_anggaran }}">
                    @else
                        <input type="number" name="pagu_anggaran" value="{{ old('pagu_anggaran', $data->pagu_anggaran) }}"
                               class="input-field @error('pagu_anggaran') border-red-500 @enderror"
                               placeholder="0" step="0.01" required>
                        @error('pagu_anggaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
            </div>
        </div>

        <!-- Info Warning -->
        @if($data->total_penyerapan > 0 || $data->tagihan_outstanding > 0)
        <div class="card bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-700">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h4 class="font-semibold text-yellow-900 dark:text-yellow-300 mb-2">Perhatian!</h4>
                    <p class="text-sm text-yellow-800 dark:text-yellow-400">
                        Item ini sudah memiliki realisasi sebesar {{ format_rupiah($data->total_penyerapan) }}.
                        Perubahan pagu akan mempengaruhi sisa anggaran.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="card">
            <div class="flex justify-end gap-3">
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Data Anggaran
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
