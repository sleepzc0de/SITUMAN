@extends('layouts.app')

@section('title', 'Import Data Anggaran')

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
            <li class="text-navy-600 dark:text-navy-400 font-medium">Import Excel</li>
        </ol>
    </nav>

    <!-- Instructions -->
    <div class="card bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">Petunjuk Import Data Anggaran</h4>
                <ul class="text-sm text-blue-800 dark:text-blue-400 space-y-1">
                    <li>1. Download template Excel terlebih dahulu</li>
                    <li>2. Isi data sesuai dengan format yang telah ditentukan</li>
                    <li>3. Pastikan semua kolom wajib terisi</li>
                    <li>4. Upload file Excel yang sudah diisi</li>
                    <li>5. Sistem akan memvalidasi dan mengimport data secara otomatis</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Download Template -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Download Template</h3>

        <div class="bg-gray-50 dark:bg-navy-800 rounded-xl p-6 text-center">
            <svg class="w-16 h-16 mx-auto text-green-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Template Import Anggaran</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Download template Excel untuk import data anggaran</p>
            <a href="#" class="btn btn-primary inline-flex">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download Template
            </a>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Upload File Excel</h3>

        <form action="{{ route('anggaran.data.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-4">
                <div class="input-group">
                    <label class="input-label">File Excel <span class="text-red-500">*</span></label>
                    <input type="file" name="file"
                           class="input-field @error('file') border-red-500 @enderror"
                           accept=".xlsx,.xls,.csv" required>
                    <p class="text-xs text-gray-500 mt-1">Format: XLSX, XLS, atau CSV (Max: 10MB)</p>
                    @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-sm text-yellow-800 dark:text-yellow-400">
                            <p class="font-semibold mb-1">Perhatian!</p>
                            <p>Data yang sudah ada dengan kode yang sama akan diupdate. Pastikan data yang diimport sudah benar.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
