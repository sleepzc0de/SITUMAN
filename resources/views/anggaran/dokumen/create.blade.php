@extends('layouts.app')

@section('title', 'Upload Dokumen Capaian Output')

@section('content')
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <a href="{{ route('anggaran.dokumen.index') }}"
                        class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                        Dokumen Capaian Output
                    </a>
                </li>
                <li>
                    <span class="mx-2 text-gray-400">/</span>
                </li>
                <li class="text-navy-600 dark:text-navy-400 font-medium">Upload Dokumen</li>
            </ol>
        </nav>

        <form action="{{ route('anggaran.dokumen.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="card">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Dokumen</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="input-group">
                        <label class="input-label">RO <span class="text-red-500">*</span></label>
                        <select name="ro" id="ro" class="input-field @error('ro') border-red-500 @enderror"
                            required>
                            <option value="">Pilih RO</option>
                            @foreach ($roList as $ro)
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
                        <select name="sub_komponen" id="sub_komponen"
                            class="input-field @error('sub_komponen') border-red-500 @enderror" required>
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
                            @foreach ($bulanList as $bulan)
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
                        <label class="input-label">Nama Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen') }}"
                            class="input-field @error('nama_dokumen') border-red-500 @enderror"
                            placeholder="Nama dokumen capaian output" required>
                        @error('nama_dokumen')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="input-group md:col-span-2">
                        <label class="input-label">File Dokumen <span class="text-red-500">*</span></label>
                        <input type="file" name="files[]" class="input-field @error('files.*') border-red-500 @enderror"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple required id="file-input">
                        <p class="text-xs text-gray-500 mt-1">
                            Format: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max: 10MB per file)
                            <br>
                            <strong>Anda dapat memilih beberapa file sekaligus (multiple files)</strong>
                        </p>
                        @error('files.*')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Preview selected files -->
                        <div id="file-preview" class="mt-3 space-y-2"></div>
                    </div>
                    @push('scripts')
                        <script>
                            document.getElementById('file-input').addEventListener('change', function(e) {
                                const preview = document.getElementById('file-preview');
                                preview.innerHTML = '';

                                if (this.files.length > 0) {
                                    const title = document.createElement('p');
                                    title.className = 'text-sm font-semibold text-gray-700 dark:text-gray-300';
                                    title.textContent = `${this.files.length} file dipilih:`;
                                    preview.appendChild(title);

                                    Array.from(this.files).forEach((file, index) => {
                                        const fileDiv = document.createElement('div');
                                        fileDiv.className =
                                            'flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-navy-800 p-2 rounded';
                                        fileDiv.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>${file.name}</span>
                <span class="text-xs text-gray-500">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
            `;
                                        preview.appendChild(fileDiv);
                                    });
                                }
                            });
                        </script>
                    @endpush


                    <div class="input-group md:col-span-2">
                        <label class="input-label">Keterangan</label>
                        <textarea name="keterangan" rows="4" class="input-field @error('keterangan') border-red-500 @enderror"
                            placeholder="Keterangan dokumen">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="flex justify-end gap-3">
                    <a href="{{ route('anggaran.dokumen.index') }}" class="btn btn-outline">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        Upload Dokumen
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

                roSelect.addEventListener('change', function() {
                    const ro = this.value;
                    subKomponenSelect.innerHTML = '<option value="">Pilih Sub Komponen</option>';

                    if (ro) {
                        subKomponenSelect.innerHTML = '<option value="">Loading...</option>';

                        // ✅ PERBAIKAN: Template literal yang benar
                        fetch(`{{ route('anggaran.dokumen.ajax.subkomponen') }}?ro=${ro}`)
                            .then(response => response.json())
                            .then(data => {
                                subKomponenSelect.innerHTML =
                                    '<option value="">Pilih Sub Komponen</option>';

                                if (data.error) {
                                    console.error('Error:', data.error);
                                    return;
                                }

                                if (data.length === 0) {
                                    subKomponenSelect.innerHTML =
                                        '<option value="">Tidak ada sub komponen</option>';
                                    return;
                                }

                                data.forEach(item => {
                                    const option = document.createElement('option');
                                    option.value = item.kode_subkomponen;
                                    option.textContent =
                                        `${item.kode_subkomponen} - ${item.program_kegiatan}`;
                                    subKomponenSelect.appendChild(option);
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                subKomponenSelect.innerHTML =
                                    '<option value="">Error loading data</option>';
                            });
                    }
                });
            });
        </script>
    @endpush
@endsection
