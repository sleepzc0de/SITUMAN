@extends('layouts.app')

@section('title', 'Tambah Revisi Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.revisi.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Revisi Anggaran
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Tambah Revisi</li>
        </ol>
    </nav>

    <form action="{{ route('anggaran.revisi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Revisi</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group md:col-span-2">
                    <label class="input-label">Item Anggaran <span class="text-red-500">*</span></label>
                    <select name="anggaran_id" id="anggaran_id" class="input-field @error('anggaran_id') border-red-500 @enderror" required>
                        <option value="">Pilih Item Anggaran</option>
                        @foreach($anggarans as $anggaran)
                            <option value="{{ $anggaran->id }}"
                                    data-pagu="{{ $anggaran->pagu_anggaran }}"
                                    {{ old('anggaran_id') == $anggaran->id ? 'selected' : '' }}>
                                {{ $anggaran->ro }} - {{ $anggaran->kode_subkomponen }} - {{ $anggaran->kode_akun }} - {{ truncate_text($anggaran->program_kegiatan, 60) }}
                            </option>
                        @endforeach
                    </select>
                    @error('anggaran_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Jenis Revisi <span class="text-red-500">*</span></label>
                    <select name="jenis_revisi" class="input-field @error('jenis_revisi') border-red-500 @enderror" required>
                        <option value="">Pilih Jenis Revisi</option>
                        @foreach($jenisRevisi as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_revisi') == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_revisi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Tanggal Revisi <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_revisi" value="{{ old('tanggal_revisi') }}"
                           class="input-field @error('tanggal_revisi') border-red-500 @enderror" required>
                    @error('tanggal_revisi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Pagu Sebelum</label>
                    <input type="number" id="pagu_sebelum"
                           class="input-field bg-gray-100 dark:bg-navy-700"
                           placeholder="Otomatis terisi" step="0.01" readonly>
                </div>

                <div class="input-group">
                    <label class="input-label">Pagu Sesudah <span class="text-red-500">*</span></label>
                    <input type="number" name="pagu_sesudah" id="pagu_sesudah" value="{{ old('pagu_sesudah') }}"
                           class="input-field @error('pagu_sesudah') border-red-500 @enderror"
                           placeholder="0" step="0.01" required>
                    @error('pagu_sesudah')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Selisih</label>
                    <input type="number" id="selisih"
                           class="input-field bg-gray-100 dark:bg-navy-700"
                           placeholder="Otomatis terhitung" step="0.01" readonly>
                </div>

                <div class="input-group md:col-span-2">
                    <label class="input-label">Alasan Revisi <span class="text-red-500">*</span></label>
                    <textarea name="alasan_revisi" rows="4"
                              class="input-field @error('alasan_revisi') border-red-500 @enderror"
                              placeholder="Jelaskan alasan dilakukan revisi anggaran" required>{{ old('alasan_revisi') }}</textarea>
                    @error('alasan_revisi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group md:col-span-2">
                    <label class="input-label">Dokumen Pendukung</label>
                    <input type="file" name="dokumen_pendukung"
                           class="input-field @error('dokumen_pendukung') border-red-500 @enderror"
                           accept=".pdf">
                    <p class="text-xs text-gray-500 mt-1">Format: PDF (Max: 5MB)</p>
                    @error('dokumen_pendukung')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="flex justify-end gap-3">
                <a href="{{ route('anggaran.revisi.index') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Revisi
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const anggaranSelect = document.getElementById('anggaran_id');
    const paguSebelumInput = document.getElementById('pagu_sebelum');
    const paguSesudahInput = document.getElementById('pagu_sesudah');
    const selisihInput = document.getElementById('selisih');

    // Set pagu sebelum when anggaran is selected
    anggaranSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const pagu = selectedOption.dataset.pagu || 0;
        paguSebelumInput.value = parseFloat(pagu).toFixed(2);
        calculateSelisih();
    });

    // Calculate selisih when pagu sesudah changes
    paguSesudahInput.addEventListener('input', calculateSelisih);

    function calculateSelisih() {
        const paguSebelum = parseFloat(paguSebelumInput.value) || 0;
        const paguSesudah = parseFloat(paguSesudahInput.value) || 0;
        const selisih = paguSesudah - paguSebelum;
        selisihInput.value = selisih.toFixed(2);

        // Change color based on selisih
        if (selisih > 0) {
            selisihInput.classList.remove('text-red-600');
            selisihInput.classList.add('text-green-600', 'font-semibold');
        } else if (selisih < 0) {
            selisihInput.classList.remove('text-green-600');
            selisihInput.classList.add('text-red-600', 'font-semibold');
        } else {
            selisihInput.classList.remove('text-green-600', 'text-red-600', 'font-semibold');
        }
    }

    // Trigger change if anggaran already selected (old value)
    if (anggaranSelect.value) {
        anggaranSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
