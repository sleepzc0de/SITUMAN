@extends('layouts.app')

@section('title', 'Edit Data Anggaran')
@section('subtitle', 'Perbarui informasi data anggaran')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 text-sm">
        <li><a href="{{ route('anggaran.data.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-navy-600 dark:hover:text-navy-400 transition-colors">Kelola Data Anggaran</a></li>
        <li class="flex items-center">
            <svg class="w-4 h-4 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-700 dark:text-gray-300">Edit Data</span>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<form action="{{ route('anggaran.data.update', $data) }}" method="POST">
    @csrf @method('PUT')

    {{-- Hidden: pertahankan kode yang tidak boleh diubah --}}
    @if($data->kode_subkomponen)
        <input type="hidden" name="kode_subkomponen" value="{{ $data->kode_subkomponen }}">
    @endif
    @if($data->kode_akun)
        <input type="hidden" name="kode_akun" value="{{ $data->kode_akun }}">
    @endif

    <div class="space-y-6">

        {{-- Level Badge Info --}}
        @php
            if (!$data->kode_akun && !$data->kode_subkomponen) {
                $levelLabel = 'RO (Parent)';
                $levelBadge = 'badge-blue';
                $levelDesc  = 'Pagu dihitung otomatis dari Sub Komponen';
            } elseif (!$data->kode_akun) {
                $levelLabel = 'Sub Komponen';
                $levelBadge = 'badge-purple';
                $levelDesc  = 'Pagu dihitung otomatis dari Akun';
            } else {
                $levelLabel = 'Akun (Detail)';
                $levelBadge = 'badge-green';
                $levelDesc  = 'Pagu dapat diedit langsung';
            }
        @endphp

        <div class="card border-l-4 {{ $data->kode_akun ? 'border-l-green-500' : ($data->kode_subkomponen ? 'border-l-purple-500' : 'border-l-blue-500') }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="badge {{ $levelBadge }} text-sm px-3 py-1">{{ $levelLabel }}</span>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $levelDesc }}</p>
                </div>
                @if($data->total_penyerapan > 0)
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Realisasi saat ini</p>
                        <p class="font-semibold text-green-600 dark:text-green-400">{{ format_rupiah($data->total_penyerapan) }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kode Identifikasi --}}
        <div class="card">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">
                Kode Identifikasi
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="input-group">
                    <label class="input-label">Kode Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kegiatan" value="{{ old('kegiatan', $data->kegiatan) }}"
                           class="input-field @error('kegiatan') border-red-500 @enderror" required>
                    @error('kegiatan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="input-label">KRO <span class="text-red-500">*</span></label>
                    <input type="text" name="kro" value="{{ old('kro', $data->kro) }}"
                           class="input-field @error('kro') border-red-500 @enderror" required>
                    @error('kro')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="input-label">RO <span class="text-red-500">*</span></label>
                    <select name="ro" class="input-field @error('ro') border-red-500 @enderror" required>
                        @foreach($roList as $ro)
                            <option value="{{ $ro }}" {{ old('ro', $data->ro) == $ro ? 'selected' : '' }}>
                                {{ $ro }} – {{ get_ro_name($ro) }}
                            </option>
                        @endforeach
                    </select>
                    @error('ro')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                @if($data->kode_subkomponen)
                <div class="input-group">
                    <label class="input-label">Sub Komponen</label>
                    <div class="input-field bg-gray-50 dark:bg-navy-700/50 cursor-not-allowed font-mono text-gray-600 dark:text-gray-400">
                        {{ $data->kode_subkomponen }}
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tidak dapat diubah</p>
                </div>
                @endif

                @if($data->kode_akun)
                <div class="input-group">
                    <label class="input-label">Kode Akun</label>
                    <div class="input-field bg-gray-50 dark:bg-navy-700/50 cursor-not-allowed font-mono text-gray-600 dark:text-gray-400">
                        {{ $data->kode_akun }}
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tidak dapat diubah</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Detail --}}
        <div class="card">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-100 dark:border-navy-700">
                Informasi Detail
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="input-group sm:col-span-2">
                    <label class="input-label">Uraian Program/Kegiatan <span class="text-red-500">*</span></label>
                    <textarea name="program_kegiatan" rows="3"
                              class="input-field @error('program_kegiatan') border-red-500 @enderror" required>{{ old('program_kegiatan', $data->program_kegiatan) }}</textarea>
                    @error('program_kegiatan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="input-label">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" value="{{ old('pic', $data->pic) }}"
                           class="input-field @error('pic') border-red-500 @enderror" required>
                    @error('pic')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="input-label">
                        Pagu Anggaran
                        @if($data->kode_akun)<span class="text-red-500">*</span>@endif
                    </label>

                    @if($data->kode_akun)
                        <input type="number" name="pagu_anggaran"
                               value="{{ old('pagu_anggaran', $data->pagu_anggaran) }}"
                               class="input-field @error('pagu_anggaran') border-red-500 @enderror"
                               step="1" min="0" required>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                            ✏️ Pagu dapat diedit. Realisasi saat ini: <strong>{{ format_rupiah($data->total_penyerapan) }}</strong>
                        </p>
                        @error('pagu_anggaran')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @else
                        <div class="input-field bg-gray-50 dark:bg-navy-700/50 cursor-not-allowed text-gray-600 dark:text-gray-400">
                            {{ format_rupiah($data->pagu_anggaran) }}
                        </div>
                        <p class="text-xs text-orange-500 dark:text-orange-400 mt-1">
                            🔒 Dihitung otomatis dari {{ !$data->kode_subkomponen ? 'Sub Komponen' : 'Akun' }} di bawahnya
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Warning jika ada realisasi --}}
        @if($data->total_penyerapan > 0 || $data->tagihan_outstanding > 0)
        <div class="card border-l-4 border-l-yellow-500 bg-yellow-50/50 dark:bg-yellow-900/10">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold text-yellow-800 dark:text-yellow-300 text-sm">Perhatian!</p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-0.5">
                        Item ini sudah memiliki realisasi <strong>{{ format_rupiah($data->total_penyerapan) }}</strong>
                        dan outstanding <strong>{{ format_rupiah($data->tagihan_outstanding) }}</strong>.
                        Perubahan pagu akan mempengaruhi sisa anggaran.
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">Batal</a>
            <a href="{{ route('anggaran.data.show', $data) }}" class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Lihat Detail
            </a>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection
