@extends('layouts.app')

@section('title', 'Edit Dokumen Capaian Output')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.dokumen.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Dokumen Capaian Output
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Edit Dokumen</li>
        </ol>
    </nav>

    <form action="{{ route('anggaran.dokumen.update', $dokumen) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Dokumen</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">RO <span class="text-red-500">*</span></label>
                    <select name="ro" id="ro" class="input-field @error('ro') border-red-500 @enderror" required>
                        <option value="">Pilih RO</option>
                        @foreach($roList as $ro)
                            <option value="{{ $ro }}" {{ old('ro', $dokumen->ro) == $ro ? 'selected' : '' }}>
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
                    <select name="sub_komponen" id="sub_komponen" class="input-field @error('sub_komponen') border-red-500 @enderror" required>
                        <option value="">Pilih Sub Komponen</option>
                    </select>
                    @error('sub_komponen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Bulan <span class="text-red-500">*</span></label>
                    <select name="bulan" class="input-field @error('bulan') border-red-500 @enderror" required>
                        <option value="">Pilih Bulan</option>
                        @foreach($bulanList as $bulan)
                            <option value="{{ $bulan }}" {{ old('bulan', $dokumen->bulan) == $bulan ? 'selected' : '' }}>
                                {{ ucfirst($bulan) }}
                            </option>
                        @endforeach
                    </select>
                    @error('bulan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Nama Dokumen <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}"
                           class="input-field @error('nama_dokumen') border-red-500 @enderror"
                           placeholder="Nama dokumen capaian output" required>
                    @error('nama_dokumen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group md:col-span-2">
                    <label class="input-label">Keterangan</label>
                    <textarea name="keterangan" rows="4" class="input-field @error('keterangan') border-red-500 @enderror"
                              placeholder="Keterangan dokumen">{{ old('keterangan', $dokumen->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Existing Files -->
        @php
            $existingFiles = $dokumen->getAllFiles();
        @endphp

        @if(count($existingFiles) > 0)
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">File yang Sudah Ada ({{ count($existingFiles) }})</h3>

            <div class="space-y-2">
                @foreach($existingFiles as $index => $file)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-navy-800 rounded-lg">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="remove_files[]" value="{{ $index }}"
                               class="w-4 h-4 text-red-600 rounded">
                        <svg class="w-5 h-5 {{ file_icon_class($file['path']) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $file['name'] }}</span>
                    </div>
                    <a href="{{ route('anggaran.dokumen.download-single', [$dokumen->id, $index]) }}"
                       class="text-green-600 hover:text-green-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </a>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-red-500 mt-2">
                <strong>Centang file yang ingin dihapus</strong>
            </p>
        </div>
        @endif

        <!-- Upload New Files -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah File Baru (Opsional)</h3>

            <div class="input-group">
                <label class="input-label">Upload File Baru</label>
                <input type="file" name="files[]"
                       class="input-field @error('files.*') border-red-500 @enderror"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                       multiple
                       id="file-input">
                <p class="text-xs text-gray-500 mt-1">
                    Format: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max: 10MB per file)
                </p>
                @error('files.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <div id="file-preview" class="mt-3 space-y-2"></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="flex justify-end gap-3">
                <a href="{{ route('anggaran.dokumen.show', $dokumen) }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Dokumen
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roSelect = document.getElementById('ro');
    const subKomponenSelect = document.getElementById('sub_komponen');
    const currentSubkomp = '{{ old('sub_komponen', $dokumen->sub_komponen) }}';

    roSelect.addEventListener('change', function() {
        const ro = this.value;
        subKomponenSelect.innerHTML = '<option value="">Pilih Sub Komponen</option>';

        if (ro) {
            subKomponenSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`{{ route('anggaran.dokumen.ajax.subkomponen') }}?ro=${ro}`)
                .then(response => response.json())
                .then(data => {
                    subKomponenSelect.innerHTML = '<option value="">Pilih Sub Komponen</option>';

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.kode_subkomponen;
                        option.textContent = `${item.kode_subkomponen} - ${item.program_kegiatan}`;
                        if (item.kode_subkomponen === currentSubkomp) {
                            option.selected = true;
                        }
                        subKomponenSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    subKomponenSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        }
    });

    // Trigger on load
    if (roSelect.value) {
        roSelect.dispatchEvent(new Event('change'));
    }

    // File preview
    document.getElementById('file-input').addEventListener('change', function(e) {
        const preview = document.getElementById('file-preview');
        preview.innerHTML = '';

        if (this.files.length > 0) {
            const title = document.createElement('p');
            title.className = 'text-sm font-semibold text-gray-700 dark:text-gray-300';
            title.textContent = `${this.files.length} file baru dipilih:`;
            preview.appendChild(title);

            Array.from(this.files).forEach((file, index) => {
                const fileDiv = document.createElement('div');
                fileDiv.className = 'flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 p-2 rounded border border-blue-200 dark:border-blue-700';
                fileDiv.innerHTML = `
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-blue-900 dark:text-blue-300">${file.name}</span>
                    <span class="text-xs text-blue-600">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                `;
                preview.appendChild(fileDiv);
            });
        }
    });
});
</script>
@endpush
@endsection
