@extends('layouts.app')

@section('title', 'Tambah Data Anggaran')

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
            <li class="text-navy-600 dark:text-navy-400 font-medium">Tambah Data</li>
        </ol>
    </nav>

    <form action="{{ route('anggaran.data.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Informasi Kode -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Kode</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">Kode Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kegiatan" value="{{ old('kegiatan', '4753') }}"
                           class="input-field @error('kegiatan') border-red-500 @enderror"
                           placeholder="Contoh: 4753" required>
                    @error('kegiatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">KRO <span class="text-red-500">*</span></label>
                    <input type="text" name="kro" value="{{ old('kro', 'EBA') }}"
                           class="input-field @error('kro') border-red-500 @enderror"
                           placeholder="Contoh: EBA" required>
                    @error('kro')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

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
                    <label class="input-label">Level Item <span class="text-red-500">*</span></label>
                    <select id="level_select" class="input-field" required>
                        <option value="">Pilih Level</option>
                        <option value="ro">RO (Parent)</option>
                        <option value="subkomponen">Sub Komponen</option>
                        <option value="akun">Akun (Detail)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tentukan level hierarki item anggaran</p>
                </div>

                <!-- Sub Komponen - DIPERBAIKI: Sekarang Dropdown -->
                <div class="input-group" id="subkomponen_group" style="display: none;">
                    <label class="input-label">Kode Sub Komponen <span class="text-red-500">*</span></label>

                    <!-- Tampilkan dropdown jika level = akun dan RO sudah dipilih -->
                    <select name="kode_subkomponen" id="kode_subkomponen_select"
                            class="input-field @error('kode_subkomponen') border-red-500 @enderror"
                            style="display: none;">
                        <option value="">Pilih Sub Komponen</option>
                    </select>

                    <!-- Tampilkan input manual jika level = subkomponen -->
                    <input type="text" name="kode_subkomponen_manual" id="kode_subkomponen_input"
                           value="{{ old('kode_subkomponen') }}"
                           class="input-field @error('kode_subkomponen') border-red-500 @enderror"
                           placeholder="Contoh: AA, AB, AC"
                           style="display: none;">

                    <!-- Hidden input untuk nilai final -->
                    <input type="hidden" name="kode_subkomponen" id="kode_subkomponen_final">

                    <p class="text-xs text-gray-500 mt-1" id="subkomp_hint">Masukkan kode sub komponen (2 karakter)</p>

                    @error('kode_subkomponen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group" id="akun_group" style="display: none;">
                    <label class="input-label">Kode Akun</label>
                    <input type="text" name="kode_akun" id="kode_akun" value="{{ old('kode_akun') }}"
                           class="input-field @error('kode_akun') border-red-500 @enderror"
                           placeholder="Contoh: 521211, 524111">
                    @error('kode_akun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
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
                              placeholder="Uraian lengkap program/kegiatan" required>{{ old('program_kegiatan') }}</textarea>
                    @error('program_kegiatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" value="{{ old('pic', 'SJ.7') }}"
                           class="input-field @error('pic') border-red-500 @enderror"
                           placeholder="Penanggung Jawab" required>
                    @error('pic')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Pagu Anggaran <span class="text-red-500">*</span></label>
                    <input type="number" name="pagu_anggaran" value="{{ old('pagu_anggaran') }}"
                           class="input-field @error('pagu_anggaran') border-red-500 @enderror"
                           placeholder="0" step="0.01" required>
                    @error('pagu_anggaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="card bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">Panduan Input Data Anggaran</h4>
                    <ul class="text-sm text-blue-800 dark:text-blue-400 space-y-1">
                        <li>• <strong>RO (Parent):</strong> Item tingkat tertinggi, hanya isi Kode Kegiatan, KRO, dan RO</li>
                        <li>• <strong>Sub Komponen:</strong> Item di bawah RO, ketik manual Kode Sub Komponen (2 karakter, contoh: AA, AB)</li>
                        <li>• <strong>Akun (Detail):</strong> Item detail, pilih Sub Komponen dari dropdown lalu isi Kode Akun</li>
                        <li>• Pagu pada RO dan Sub Komponen akan otomatis dihitung dari child items</li>
                        <li>• Pastikan urutan input: RO → Sub Komponen → Akun</li>
                    </ul>
                </div>
            </div>
        </div>

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
                    Simpan Data Anggaran
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('level_select');
    const roSelect = document.getElementById('ro');
    const subkomponenGroup = document.getElementById('subkomponen_group');
    const akunGroup = document.getElementById('akun_group');

    // Sub komponen elements
    const subkomponenSelect = document.getElementById('kode_subkomponen_select');
    const subkomponenInput = document.getElementById('kode_subkomponen_input');
    const subkomponenFinal = document.getElementById('kode_subkomponen_final');
    const subkompHint = document.getElementById('subkomp_hint');

    const akunInput = document.getElementById('kode_akun');

    // ========================================
    // Handle Level Selection Change
    // ========================================
    levelSelect.addEventListener('change', function() {
        const level = this.value;
        const ro = roSelect.value;

        // Reset semua field
        subkomponenGroup.style.display = 'none';
        akunGroup.style.display = 'none';
        subkomponenSelect.style.display = 'none';
        subkomponenInput.style.display = 'none';
        subkomponenFinal.value = '';
        akunInput.value = '';
        subkomponenInput.removeAttribute('required');
        akunInput.removeAttribute('required');

        if (level === 'subkomponen') {
            // Level Sub Komponen: tampilkan input manual
            subkomponenGroup.style.display = 'block';
            subkomponenInput.style.display = 'block';
            subkomponenInput.setAttribute('required', 'required');
            subkompHint.textContent = 'Ketik kode sub komponen baru (2 karakter, contoh: AA, AB, CC)';

        } else if (level === 'akun') {
            // Level Akun: tampilkan dropdown sub komponen
            subkomponenGroup.style.display = 'block';
            akunGroup.style.display = 'block';
            akunInput.setAttribute('required', 'required');

            if (ro) {
                // Jika RO sudah dipilih, load sub komponen
                loadSubkomponen(ro);
            } else {
                // Jika RO belum dipilih, tampilkan peringatan
                subkomponenSelect.innerHTML = '<option value="">Pilih RO terlebih dahulu</option>';
                subkomponenSelect.style.display = 'block';
                subkompHint.textContent = 'Pilih RO terlebih dahulu untuk melihat daftar Sub Komponen';
            }
        }
    });

    // ========================================
    // Handle RO Selection Change
    // ========================================
    roSelect.addEventListener('change', function() {
        const ro = this.value;
        const level = levelSelect.value;

        // Jika level = akun dan RO dipilih, load sub komponen
        if (level === 'akun' && ro) {
            loadSubkomponen(ro);
        }
    });

    // ========================================
    // Function: Load Sub Komponen via AJAX
    // ========================================
    function loadSubkomponen(ro) {
        subkomponenSelect.innerHTML = '<option value="">Loading...</option>';
        subkomponenSelect.style.display = 'block';
        subkompHint.textContent = 'Memuat daftar sub komponen...';

        fetch(`{{ route('anggaran.data.ajax.subkomponen') }}?ro=${ro}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                subkomponenSelect.innerHTML = '<option value="">Pilih Sub Komponen</option>';

                if (data.error) {
                    console.error('Error:', data.error);
                    subkomponenSelect.innerHTML = '<option value="">Error: ' + data.error + '</option>';
                    subkompHint.textContent = 'Gagal memuat data sub komponen';
                    return;
                }

                if (data.length === 0) {
                    subkomponenSelect.innerHTML = '<option value="">Belum ada sub komponen</option>';
                    subkompHint.innerHTML = '<span class="text-orange-600">Belum ada sub komponen untuk RO ini. Buat Sub Komponen terlebih dahulu.</span>';
                    return;
                }

                // Populate dropdown
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.kode_subkomponen;
                    option.textContent = `${item.kode_subkomponen} - ${item.program_kegiatan}`;
                    subkomponenSelect.appendChild(option);
                });

                subkomponenSelect.setAttribute('required', 'required');
                subkompHint.textContent = 'Pilih sub komponen yang sudah ada';
            })
            .catch(error => {
                console.error('Error:', error);
                subkomponenSelect.innerHTML = '<option value="">Error loading data</option>';
                subkompHint.innerHTML = '<span class="text-red-600">Gagal memuat data. Silakan coba lagi.</span>';
            });
    }

    // ========================================
    // Sync nilai ke hidden input sebelum submit
    // ========================================
    subkomponenSelect.addEventListener('change', function() {
        subkomponenFinal.value = this.value;
    });

    subkomponenInput.addEventListener('input', function() {
        subkomponenFinal.value = this.value.toUpperCase();
        this.value = this.value.toUpperCase(); // Auto uppercase
    });

    // ========================================
    // Validation sebelum submit
    // ========================================
    document.querySelector('form').addEventListener('submit', function(e) {
        const level = levelSelect.value;

        if (level === 'akun') {
            // Untuk level akun, pastikan sub komponen dipilih
            if (!subkomponenFinal.value) {
                e.preventDefault();
                alert('Pilih Sub Komponen terlebih dahulu!');
                subkomponenSelect.focus();
                return false;
            }
        } else if (level === 'subkomponen') {
            // Untuk level subkomponen, pastikan input manual terisi
            if (!subkomponenInput.value) {
                e.preventDefault();
                alert('Masukkan Kode Sub Komponen!');
                subkomponenInput.focus();
                return false;
            }
        }
    });
});
</script>
@endpush
@endsection
