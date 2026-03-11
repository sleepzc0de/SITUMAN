@extends('layouts.app')

@section('title', 'Tambah Data Anggaran')
@section('subtitle', 'Tambah master data pagu anggaran baru')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 text-sm">
        <li><a href="{{ route('anggaran.data.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-navy-600 dark:hover:text-navy-400 transition-colors">Kelola Data Anggaran</a></li>
        <li class="flex items-center">
            <svg class="w-4 h-4 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-700 dark:text-gray-300">Tambah Data</span>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<form action="{{ route('anggaran.data.store') }}" method="POST" id="formAnggaran">
    @csrf
    <div class="space-y-6">

        {{-- Panduan --}}
        <div class="card border-l-4 border-l-navy-500 bg-navy-50/50 dark:bg-navy-800/50">
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-navy-600 dark:text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-navy-800 dark:text-navy-300 text-sm mb-1.5">Panduan Input Hierarki Anggaran</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs text-navy-700 dark:text-navy-400">
                        <div class="flex items-start gap-1.5">
                            <span class="badge badge-blue mt-0.5 flex-shrink-0">RO</span>
                            <span>Level tertinggi. Pagu dihitung otomatis dari Sub Komponen.</span>
                        </div>
                        <div class="flex items-start gap-1.5">
                            <span class="badge badge-purple mt-0.5 flex-shrink-0">Sub Komp</span>
                            <span>Di bawah RO. Pagu dihitung otomatis dari Akun.</span>
                        </div>
                        <div class="flex items-start gap-1.5">
                            <span class="badge badge-green mt-0.5 flex-shrink-0">Akun</span>
                            <span>Level detail. <strong>Hanya level ini yang input pagu.</strong></span>
                        </div>
                    </div>
                    <p class="text-xs text-navy-600 dark:text-navy-500 mt-2">
                        ⚡ Urutan input: <strong>RO → Sub Komponen → Akun</strong>
                    </p>
                </div>
            </div>
        </div>

        {{-- Kode Identifikasi --}}
        <div class="card">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-100 dark:border-navy-700 flex items-center gap-2">
                <div class="w-6 h-6 bg-navy-100 dark:bg-navy-700 rounded flex items-center justify-center">
                    <span class="text-xs font-bold text-navy-600 dark:text-navy-400">1</span>
                </div>
                Kode Identifikasi
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="input-group">
                    <label class="input-label">Kode Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kegiatan" value="{{ old('kegiatan', '4753') }}"
                           class="input-field @error('kegiatan') border-red-500 @enderror"
                           placeholder="Contoh: 4753" required>
                    @error('kegiatan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="input-label">KRO <span class="text-red-500">*</span></label>
                    <input type="text" name="kro" value="{{ old('kro', 'EBA') }}"
                           class="input-field @error('kro') border-red-500 @enderror"
                           placeholder="Contoh: EBA" required>
                    @error('kro')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="input-label">RO <span class="text-red-500">*</span></label>
                    <select name="ro" id="ro" class="input-field @error('ro') border-red-500 @enderror" required>
                        <option value="">Pilih RO</option>
                        @foreach($roList as $ro)
                            <option value="{{ $ro }}" {{ old('ro') == $ro ? 'selected' : '' }}>
                                {{ $ro }} – {{ get_ro_name($ro) }}
                            </option>
                        @endforeach
                    </select>
                    @error('ro')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Level Item <span class="text-red-500">*</span></label>
                    <select id="level_select" class="input-field">
                        <option value="">Pilih Level</option>
                        <option value="ro">RO (Parent)</option>
                        <option value="subkomponen">Sub Komponen</option>
                        <option value="akun">Akun (Detail)</option>
                    </select>
                </div>

                {{-- Sub Komponen --}}
                <div class="input-group" id="subkomponen_group" style="display:none;">
                    <label class="input-label" id="subkomp_label">Kode Sub Komponen <span class="text-red-500">*</span></label>

                    {{-- Dropdown: untuk level akun --}}
                    <select name="kode_subkomponen" id="kode_subkomponen_select"
                            class="input-field @error('kode_subkomponen') border-red-500 @enderror"
                            style="display:none;">
                        <option value="">Pilih Sub Komponen</option>
                    </select>

                    {{-- Input manual: untuk level subkomponen --}}
                    <input type="text" id="kode_subkomponen_input"
                           class="input-field @error('kode_subkomponen') border-red-500 @enderror"
                           placeholder="Contoh: AA, AB"
                           style="display:none; text-transform:uppercase;">

                    {{-- Hidden final value --}}
                    <input type="hidden" name="kode_subkomponen" id="kode_subkomponen_final" value="{{ old('kode_subkomponen') }}">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="subkomp_hint"></p>
                    @error('kode_subkomponen')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Kode Akun --}}
                <div class="input-group" id="akun_group" style="display:none;">
                    <label class="input-label">Kode Akun <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_akun" id="kode_akun" value="{{ old('kode_akun') }}"
                           class="input-field @error('kode_akun') border-red-500 @enderror"
                           placeholder="Contoh: 521211, 524111">
                    @error('kode_akun')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Detail --}}
        <div class="card">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-100 dark:border-navy-700 flex items-center gap-2">
                <div class="w-6 h-6 bg-navy-100 dark:bg-navy-700 rounded flex items-center justify-center">
                    <span class="text-xs font-bold text-navy-600 dark:text-navy-400">2</span>
                </div>
                Informasi Detail
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="input-group sm:col-span-2">
                    <label class="input-label">Uraian Program/Kegiatan <span class="text-red-500">*</span></label>
                    <textarea name="program_kegiatan" rows="3"
                              class="input-field @error('program_kegiatan') border-red-500 @enderror"
                              placeholder="Uraian lengkap program/kegiatan/output/komponen/akun" required>{{ old('program_kegiatan') }}</textarea>
                    @error('program_kegiatan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group">
                    <label class="input-label">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" value="{{ old('pic', 'SJ.7') }}"
                           class="input-field @error('pic') border-red-500 @enderror"
                           placeholder="Penanggung jawab" required>
                    @error('pic')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="input-group" id="pagu_group">
                    <label class="input-label" id="pagu_label">
                        Pagu Anggaran
                        <span class="text-red-500 hidden" id="pagu_required_mark">*</span>
                    </label>
                    <input type="number" name="pagu_anggaran" id="pagu_anggaran"
                           value="{{ old('pagu_anggaran') }}"
                           class="input-field @error('pagu_anggaran') border-red-500 @enderror"
                           placeholder="0" step="1" min="0">
                    <p class="text-xs mt-1" id="pagu_hint">
                        <span class="text-gray-500 dark:text-gray-400">Pilih level terlebih dahulu</span>
                    </p>
                    @error('pagu_anggaran')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Data
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const levelSelect    = document.getElementById('level_select');
    const roSelect       = document.getElementById('ro');
    const subkompGroup   = document.getElementById('subkomponen_group');
    const akunGroup      = document.getElementById('akun_group');
    const subkompSelect  = document.getElementById('kode_subkomponen_select');
    const subkompInput   = document.getElementById('kode_subkomponen_input');
    const subkompFinal   = document.getElementById('kode_subkomponen_final');
    const subkompHint    = document.getElementById('subkomp_hint');
    const akunInput      = document.getElementById('kode_akun');
    const paguInput      = document.getElementById('pagu_anggaran');
    const paguRequired   = document.getElementById('pagu_required_mark');
    const paguHint       = document.getElementById('pagu_hint');

    function resetSubkomp() {
        subkompGroup.style.display  = 'none';
        subkompSelect.style.display = 'none';
        subkompInput.style.display  = 'none';
        subkompSelect.removeAttribute('required');
        subkompInput.removeAttribute('required');
        subkompFinal.value = '';
    }

    function setPaguReadonly(msg) {
        paguInput.readOnly = true;
        paguInput.value    = '';
        paguInput.classList.add('bg-gray-100', 'dark:bg-navy-700', 'cursor-not-allowed');
        paguInput.removeAttribute('required');
        paguRequired.classList.add('hidden');
        paguHint.innerHTML = `<span class="text-orange-500 dark:text-orange-400">🔒 ${msg}</span>`;
    }

    function setPaguEditable() {
        paguInput.readOnly = false;
        paguInput.classList.remove('bg-gray-100', 'dark:bg-navy-700', 'cursor-not-allowed');
        paguInput.setAttribute('required', 'required');
        paguRequired.classList.remove('hidden');
        paguHint.innerHTML = '<span class="text-green-600 dark:text-green-400">✏️ Input pagu anggaran untuk akun ini</span>';
    }

    levelSelect.addEventListener('change', function () {
        const level = this.value;
        const ro    = roSelect.value;
        resetSubkomp();
        akunGroup.style.display = 'none';
        akunInput.removeAttribute('required');

        if (level === 'ro') {
            setPaguReadonly('Pagu dihitung otomatis dari Sub Komponen');

        } else if (level === 'subkomponen') {
            subkompGroup.style.display = 'block';
            subkompInput.style.display = 'block';
            subkompInput.setAttribute('required', 'required');
            subkompHint.textContent = 'Ketik kode baru (2 karakter, contoh: AA, AB)';
            setPaguReadonly('Pagu dihitung otomatis dari Akun');

        } else if (level === 'akun') {
            subkompGroup.style.display = 'block';
            akunGroup.style.display    = 'block';
            akunInput.setAttribute('required', 'required');
            setPaguEditable();

            if (ro) {
                loadSubkomponen(ro);
            } else {
                subkompSelect.innerHTML    = '<option value="">Pilih RO terlebih dahulu</option>';
                subkompSelect.style.display = 'block';
                subkompHint.textContent    = 'Pilih RO terlebih dahulu';
            }
        } else {
            paguInput.readOnly = false;
            paguInput.classList.remove('bg-gray-100', 'dark:bg-navy-700', 'cursor-not-allowed');
            paguInput.removeAttribute('required');
            paguRequired.classList.add('hidden');
            paguHint.innerHTML = '<span class="text-gray-500 dark:text-gray-400">Pilih level terlebih dahulu</span>';
        }
    });

    roSelect.addEventListener('change', function () {
        if (levelSelect.value === 'akun' && this.value) {
            loadSubkomponen(this.value);
        }
    });

    function loadSubkomponen(ro) {
        subkompSelect.innerHTML    = '<option value="">Memuat...</option>';
        subkompSelect.style.display = 'block';
        subkompHint.textContent    = 'Memuat daftar sub komponen...';

        fetch(`{{ route('anggaran.data.ajax.subkomponen') }}?ro=${ro}`)
            .then(r => r.ok ? r.json() : Promise.reject(r))
            .then(data => {
                subkompSelect.innerHTML = '<option value="">— Pilih Sub Komponen —</option>';

                if (data.error) {
                    subkompHint.innerHTML = `<span class="text-red-500">Error: ${data.error}</span>`;
                    return;
                }
                if (!data.length) {
                    subkompHint.innerHTML = '<span class="text-orange-500">Belum ada sub komponen untuk RO ini. Buat Sub Komponen dahulu.</span>';
                    return;
                }

                data.forEach(item => {
                    const opt = new Option(`${item.kode_subkomponen} – ${item.program_kegiatan}`, item.kode_subkomponen);
                    subkompSelect.add(opt);
                });

                subkompSelect.setAttribute('required', 'required');
                subkompHint.textContent = 'Pilih sub komponen yang sudah ada';
            })
            .catch(() => {
                subkompHint.innerHTML = '<span class="text-red-500">Gagal memuat data. Coba lagi.</span>';
            });
    }

    subkompSelect.addEventListener('change', () => subkompFinal.value = subkompSelect.value);
    subkompInput.addEventListener('input', function () {
        this.value         = this.value.toUpperCase();
        subkompFinal.value = this.value;
    });

    document.getElementById('formAnggaran').addEventListener('submit', function (e) {
        const level = levelSelect.value;
        if (!level) {
            e.preventDefault();
            alert('Pilih Level Item terlebih dahulu!');
            levelSelect.focus();
            return;
        }
        if (level === 'akun' && !subkompFinal.value) {
            e.preventDefault();
            alert('Pilih Sub Komponen terlebih dahulu!');
            subkompSelect.focus();
            return;
        }
        if (level === 'akun' && !paguInput.value) {
            e.preventDefault();
            alert('Masukkan Pagu Anggaran untuk level Akun!');
            paguInput.focus();
        }
    });
});
</script>
@endpush
