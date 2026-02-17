{{-- resources/views/inventaris/monitoring-atk/import.blade.php --}}
@extends('layouts.app')

@section('title', 'Import Data ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Monitoring ATK', 'url' => route('inventaris.monitoring-atk.index'), 'active' => false],
        ['title' => 'Import Data', 'url' => null, 'active' => true],
    ]" />
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="card">
            <div class="border-b border-gray-200 dark:border-navy-700 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Import Data ATK dari Excel/CSV</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload file Excel atau CSV untuk mengimport data ATK
                    secara massal</p>
            </div>

            <!-- Instruksi -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Panduan Import
                </h3>
                <ol class="text-sm text-blue-800 dark:text-blue-300 space-y-1 list-decimal list-inside">
                    <li>Download template Excel terlebih dahulu dengan klik tombol "Download Template" di bawah</li>
                    <li>Isi data ATK sesuai dengan format yang ada di template</li>
                    <li>Pastikan nama kategori sesuai dengan kategori yang sudah ada di sistem</li>
                    <li>Kolom yang wajib diisi ditandai dengan tanda bintang (*)</li>
                    <li>Upload file yang sudah diisi ke form di bawah ini</li>
                </ol>
            </div>

            <!-- Kategori yang Tersedia -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-navy-800 rounded-xl">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Kategori ATK yang Tersedia:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($kategoris as $kategori)
                        <span class="badge-info">{{ $kategori->nama }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Download Template Button -->
            <div class="mb-6">
                <a href="{{ route('inventaris.monitoring-atk.template') }}" class="btn-secondary inline-flex">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Download Template Excel
                </a>
            </div>

            <!-- Upload Form -->
            <form action="{{ url('inventaris/monitoring-atk-import') }}" method="POST" enctype="multipart/form-data">
                @csrf


                <div class="input-group mb-6">
                    <label class="input-label">Upload File Excel/CSV <span class="text-red-500">*</span></label>
                    <input type="file" name="file" class="input-field @error('file') border-red-500 @enderror"
                        accept=".xlsx,.xls,.csv" required>
                    @error('file')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Format yang didukung: .xlsx, .xls, .csv (Maksimal 2MB)
                    </p>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-navy-700">
                    <a href="{{ route('inventaris.monitoring-atk.index') }}" class="btn-outline">
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
