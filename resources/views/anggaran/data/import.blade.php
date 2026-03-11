@extends('layouts.app')

@section('title', 'Import Data Anggaran')
@section('subtitle', 'Upload file Excel untuk import data anggaran secara massal')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 text-sm">
        <li><a href="{{ route('anggaran.data.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-navy-600 dark:hover:text-navy-400 transition-colors">Kelola Data Anggaran</a></li>
        <li class="flex items-center">
            <svg class="w-4 h-4 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-700 dark:text-gray-300">Import Excel</span>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Petunjuk --}}
    <div class="card border-l-4 border-l-navy-500">
        <div class="flex gap-3">
            <div class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-navy-600 dark:text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm mb-2">Petunjuk Import</p>
                <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-1.5">
                    @foreach([
                        'Download template Excel di bawah terlebih dahulu',
                        'Isi data sesuai format: kegiatan, kro, ro, kode_subkomponen, kode_akun, program_kegiatan, pic, pagu_anggaran',
                        'Level RO & Sub Komponen: kosongkan kolom kode_akun, pagu_anggaran akan dihitung otomatis',
                        'Level Akun: isi semua kolom termasuk pagu_anggaran',
                        'Data existing dengan kode sama akan diupdate (upsert)',
                        'Upload file dan sistem akan memvalidasi otomatis',
                    ] as $i => $step)
                    <li class="flex gap-2">
                        <span class="w-5 h-5 bg-navy-100 dark:bg-navy-700 rounded-full flex items-center justify-center text-xs font-bold text-navy-600 dark:text-navy-400 flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                        <span>{{ $step }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

    {{-- Download Template --}}
    <div class="card">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Download Template</h3>
        <div class="flex items-center gap-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Template Import Anggaran</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Format XLSX dengan contoh data & kolom yang sudah disiapkan</p>
            </div>
            <a href="{{ route('anggaran.data.template') }}" class="btn btn-secondary flex-shrink-0">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download
            </a>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="card">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Upload File</h3>

        <form action="{{ route('anggaran.data.import') }}" method="POST" enctype="multipart/form-data"
              x-data="{ fileName: '', dragging: false }" id="importForm">
            @csrf

            {{-- Dropzone --}}
            <div class="input-group mb-4">
                <label class="input-label">File Excel / CSV <span class="text-red-500">*</span></label>
                <label
                    class="relative flex flex-col items-center justify-center w-full h-36 rounded-xl border-2 border-dashed cursor-pointer transition-colors duration-200"
                    :class="dragging
                        ? 'border-navy-500 bg-navy-50 dark:bg-navy-700/50'
                        : 'border-gray-300 dark:border-navy-600 bg-gray-50 dark:bg-navy-800/30 hover:border-navy-400 hover:bg-navy-50/50 dark:hover:bg-navy-700/30'"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name || ''">

                    <input type="file" name="file" accept=".xlsx,.xls,.csv"
                           class="absolute inset-0 opacity-0 cursor-pointer"
                           x-ref="fileInput" required
                           @change="fileName = $event.target.files[0]?.name || ''">

                    <template x-if="!fileName">
                        <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-500 pointer-events-none">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm font-medium">Drag & drop file atau <span class="text-navy-600 dark:text-navy-400">klik untuk pilih</span></p>
                            <p class="text-xs">XLSX, XLS, CSV (Maks. 10MB)</p>
                        </div>
                    </template>
                    <template x-if="fileName">
                        <div class="flex flex-col items-center gap-2 text-navy-600 dark:text-navy-400 pointer-events-none">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-semibold" x-text="fileName"></p>
                            <p class="text-xs text-gray-400">Klik untuk ganti file</p>
                        </div>
                    </template>
                </label>
                @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Warning --}}
            <div class="flex gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl mb-5">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-sm text-yellow-800 dark:text-yellow-400">
                    <p class="font-semibold">Perhatian!</p>
                    <p class="mt-0.5">Data dengan kode yang sama akan diupdate. Pastikan file sudah benar sebelum diimport.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit"
                        x-data
                        @click="$store.app.isLoading = true"
                        class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Import Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
