@extends('layouts.app')

@section('title', 'Detail Dokumen Capaian Output')

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
                <li class="text-navy-600 dark:text-navy-400 font-medium">Detail Dokumen</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="card">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dokumen->nama_dokumen }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Upload oleh {{ $dokumen->user ? $dokumen->user->nama : 'Unknown' }}
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('anggaran.dokumen.edit', $dokumen->id) }}" class="btn btn-secondary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('anggaran.dokumen.download', $dokumen->id) }}" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Download
                    </a>
                    <a href="{{ route('anggaran.dokumen.index') }}" class="btn btn-outline">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Dokumen -->
        <div class="card">
            <h3
                class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
                Informasi Dokumen
            </h3>

            <div class="space-y-3">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Nama Dokumen</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $dokumen->nama_dokumen }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">RO</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $dokumen->ro }} -
                        {{ get_ro_name($dokumen->ro) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Sub Komponen</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $dokumen->sub_komponen }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Bulan</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($dokumen->bulan) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Upload Oleh</span>
                    <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $dokumen->user ? $dokumen->user->nama : 'Unknown' }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Tanggal Upload</span>
                    <span
                        class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($dokumen->created_at) }}</span>
                </div>
            </div>

            @if ($dokumen->keterangan)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-navy-700">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan</p>
                    <div class="bg-gray-50 dark:bg-navy-800 rounded-lg p-4">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $dokumen->keterangan }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- File Preview -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                File Dokumen ({{ count($dokumen->getAllFiles()) }})
            </h3>

            @php
                $allFiles = $dokumen->getAllFiles();
            @endphp

            @if (count($allFiles) === 1)
                {{-- Single file display --}}
                @php $file = $allFiles[0]; @endphp
                <div class="bg-gray-50 dark:bg-navy-800 rounded-xl p-8 text-center">
                    <svg class="w-20 h-20 mx-auto {{ file_icon_class($file['path']) }} mb-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $file['name'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ strtoupper(pathinfo($file['path'], PATHINFO_EXTENSION)) }}
                    </p>
                    <a href="{{ route('anggaran.dokumen.download', $dokumen->id) }}" class="btn btn-primary inline-flex">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Download File
                    </a>
                </div>
            @else
                {{-- Multiple files list --}}
                <div class="space-y-3">
                    @foreach ($allFiles as $index => $file)
                        <div
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-navy-800 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 {{ file_icon_class($file['path']) }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $file['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ strtoupper(pathinfo($file['path'], PATHINFO_EXTENSION)) }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('anggaran.dokumen.download-single', [$dokumen->id, $index]) }}"
                                class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </a>
                        </div>
                    @endforeach

                    <div class="pt-4 border-t border-gray-200 dark:border-navy-700">
                        <a href="{{ route('anggaran.dokumen.download', $dokumen->id) }}" class="btn btn-primary w-full">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Download Semua File (ZIP)
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
