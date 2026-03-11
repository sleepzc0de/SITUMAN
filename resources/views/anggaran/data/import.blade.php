@extends('layouts.app')
@section('title', 'Import Data Anggaran')
@section('subtitle', 'Upload file Excel untuk import data anggaran secara massal')

@section('breadcrumb')
<nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li><a href="{{ route('anggaran.data.index') }}" class="breadcrumb-item">Kelola Data Anggaran</a></li>
        <li><svg class="w-3.5 h-3.5 breadcrumb-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
        <li><span class="breadcrumb-current">Import Excel</span></li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- ===== PETUNJUK ===== --}}
    <div class="alert-info rounded-xl p-4">
        <div class="flex gap-3">
            <div class="w-8 h-8 bg-navy-200/60 dark:bg-navy-700 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-navy-700 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-navy-800 dark:text-navy-200 text-sm mb-2.5">Petunjuk Import</p>
                <ol class="space-y-2">
                    @php
                        $steps = [
                            'Download template Excel di bawah terlebih dahulu.',
                            'Isi data sesuai kolom: <code class="font-mono text-xs bg-navy-100 dark:bg-navy-700 px-1 rounded">kegiatan, kro, ro, kode_subkomponen, kode_akun, program_kegiatan, pic, pagu_anggaran</code>',
                            'Level <strong>RO</strong> & <strong>Sub Komponen</strong>: kosongkan <code class="font-mono text-xs bg-navy-100 dark:bg-navy-700 px-1 rounded">kode_akun</code>, pagu dihitung otomatis.',
                            'Level <strong>Akun</strong>: isi semua kolom termasuk <code class="font-mono text-xs bg-navy-100 dark:bg-navy-700 px-1 rounded">pagu_anggaran</code>.',
                            'Data existing dengan kode yang sama akan diperbarui (<em>upsert</em>).',
                            'Upload file — sistem akan memvalidasi dan memproses secara otomatis.',
                        ];
                    @endphp
                    @foreach($steps as $i => $step)
                    <li class="flex gap-2.5 text-xs text-navy-700 dark:text-navy-400">
                        <span class="w-5 h-5 bg-navy-600 dark:bg-navy-500 text-white rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">
                            {{ $i + 1 }}
                        </span>
                        <span class="leading-relaxed">{!! $step !!}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

    {{-- ===== STRUKTUR KOLOM ===== --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-navy-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Struktur Kolom Template</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-navy-800/80">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kolom</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contoh</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-2.5 text-center font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Wajib?</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700/60">
                    @php
                        $cols = [
                            ['kegiatan',          '4753',           'Kode kegiatan DIPA',                true],
                            ['kro',               'EBA',            'Kode KRO',                          true],
                            ['ro',                '403',            'Kode RO (Z06/403/405/994)',          true],
                            ['kode_subkomponen',  'AA',             'Kosong untuk level RO',             false],
                            ['kode_akun',         '521211',         'Kosong untuk level RO/Sub Komp',    false],
                            ['program_kegiatan',  'Belanja ATK',    'Uraian lengkap',                    true],
                            ['pic',               'SJ.7',           'Kode unit penanggung jawab',        true],
                            ['pagu_anggaran',     '5000000',        'Angka saja, 0 untuk RO/Sub Komp',   false],
                        ];
                    @endphp
                    @foreach($cols as [$col, $contoh, $ket, $wajib])
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/40">
                        <td class="px-4 py-2.5 font-mono font-semibold text-navy-700 dark:text-navy-300">{{ $col }}</td>
                        <td class="px-4 py-2.5 font-mono text-gray-500 dark:text-gray-400">{{ $contoh }}</td>
                        <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400">{{ $ket }}</td>
                        <td class="px-4 py-2.5 text-center">
                            @if($wajib)
                                <span class="badge badge-danger">Ya</span>
                            @else
                                <span class="badge badge-gray">Opsional</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== DOWNLOAD TEMPLATE ===== --}}
    <div class="card p-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Template Import Anggaran</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    File XLSX dengan header kolom dan contoh data yang sudah disiapkan
                </p>
            </div>
            <a href="{{ route('anggaran.data.template') }}"
               class="btn btn-success btn-sm flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Template
            </a>
        </div>
    </div>

    {{-- ===== UPLOAD FORM ===== --}}
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-navy-700">
            <div class="w-7 h-7 rounded-lg bg-navy-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Upload File</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Format XLSX, XLS, atau CSV — Maks. 10 MB</p>
            </div>
        </div>

        <form action="{{ route('anggaran.data.import') }}" method="POST"
              enctype="multipart/form-data" id="importForm"
              x-data="{
                  fileName: '',
                  fileSize: '',
                  dragging: false,
                  handleDrop(e) {
                      this.dragging = false;
                      const file = e.dataTransfer?.files[0];
                      if (file) {
                          this.$refs.fileInput.files = e.dataTransfer.files;
                          this.fileName = file.name;
                          this.fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                      }
                  },
                  handleChange(e) {
                      const file = e.target.files[0];
                      this.fileName = file?.name || '';
                      this.fileSize = file ? (file.size / 1024 / 1024).toFixed(2) + ' MB' : '';
                  },
                  clearFile() {
                      this.fileName = '';
                      this.fileSize = '';
                      this.$refs.fileInput.value = '';
                  }
              }">
            @csrf

            {{-- Dropzone --}}
            <div class="input-group mb-4">
                <label class="input-label">
                    File Excel / CSV <span class="text-red-500">*</span>
                </label>

                {{-- Drop area --}}
                <div class="relative rounded-xl border-2 border-dashed transition-all duration-200 cursor-pointer"
                     :class="dragging
                         ? 'border-navy-500 bg-navy-50 dark:bg-navy-700/50 scale-[1.01]'
                         : 'border-gray-300 dark:border-navy-600 hover:border-navy-400 dark:hover:border-navy-500 hover:bg-navy-50/40 dark:hover:bg-navy-700/20'"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="handleDrop($event)">

                    <input type="file" name="file" accept=".xlsx,.xls,.csv"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                           x-ref="fileInput" required
                           @change="handleChange($event)">

                    {{-- Empty state --}}
                    <div x-show="!fileName" class="flex flex-col items-center justify-center py-10 gap-3 pointer-events-none">
                        <div class="w-14 h-14 bg-gray-100 dark:bg-navy-700 rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Drag & drop file ke sini, atau
                                <span class="text-navy-600 dark:text-navy-400 font-semibold">klik untuk pilih</span>
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">XLSX · XLS · CSV (Maks. 10 MB)</p>
                        </div>
                    </div>

                    {{-- File selected state --}}
                    <div x-show="fileName" class="flex items-center gap-4 px-5 py-4 pointer-events-none">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="fileName"></p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="fileSize"></p>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Siap diimport
                        </div>
                    </div>
                </div>

                {{-- Clear button --}}
                <div x-show="fileName" class="mt-2 flex items-center justify-between">
                    <p class="input-hint text-green-600 dark:text-green-400">File dipilih. Klik dropzone untuk mengganti.</p>
                    <button type="button" @click.prevent="clearFile()"
                            class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                        Hapus pilihan
                    </button>
                </div>

                @error('file')
                    <p class="input-hint-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Warning --}}
            <div class="alert-warning rounded-xl p-3.5 mb-5">
                <div class="flex gap-2.5">
                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-xs text-yellow-800 dark:text-yellow-400">
                        <p class="font-semibold">Perhatian!</p>
                        <p class="mt-0.5">Data dengan kode yang sama akan diperbarui. Pastikan file sudah benar sebelum diimport.</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline btn-sm">Batal</a>
                    <button type="submit" id="submitBtn"
                            class="btn btn-primary"
                            :disabled="!fileName"
                            :class="!fileName ? 'opacity-50 cursor-not-allowed' : ''"
                            @click="if(fileName) { $store.app.isLoading = true; const btn = $el; btn.disabled = true; btn.innerHTML = '<svg class=\'w-4 h-4 animate-spin\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z\'></path></svg> Memproses...'; }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Import Data
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection
