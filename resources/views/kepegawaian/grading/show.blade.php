@extends('layouts.app')

@section('title', 'Detail Rekomendasi Grading')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-800">Detail Rekomendasi Grading</h1>
            <p class="text-gray-600 mt-1">Analisis kenaikan grading pegawai</p>
        </div>
        <a href="{{ route('kepegawaian.grading') }}" class="btn-secondary">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Profile Card -->
    <div class="card">
        <div class="flex items-start space-x-6">
            <div class="flex-shrink-0">
                <div class="w-32 h-32 bg-gradient-to-br from-navy-600 to-gold-500 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                    {{ substr($pegawai->nama, 0, 2) }}
                </div>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-navy-800">{{ $pegawai->nama_gelar ?? $pegawai->nama }}</h2>
                <p class="text-gray-600 mt-1">{{ $pegawai->nip }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-navy-100 text-navy-800">
                        {{ $pegawai->jabatan }}
                    </span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ $pegawai->bagian }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekomendasi Status -->
    @if($rekomendasi['eligible'])
    <div class="card bg-green-50 border-2 border-green-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-green-800">Eligible untuk Kenaikan Grading</h3>
                <p class="text-green-700 mt-1">
                    Pegawai ini memenuhi kriteria untuk kenaikan grading pada tahun {{ $rekomendasi['tahun'] }}
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="card bg-yellow-50 border-2 border-yellow-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-12 h-12 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-yellow-800">Belum Memenuhi Kriteria</h3>
                <p class="text-yellow-700 mt-1">
                    Pegawai ini belum memenuhi kriteria untuk kenaikan grading
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Grading Comparison -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Current Grading -->
        <div class="card bg-gray-50">
            <h3 class="text-lg font-semibold text-navy-800 mb-4">Grading Saat Ini</h3>
            <div class="flex items-center justify-center">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-32 h-32 bg-gray-200 rounded-full">
                        <span class="text-5xl font-bold text-gray-700">{{ $rekomendasi['grading_sekarang'] }}</span>
                    </div>
                    <p class="text-gray-600 mt-4">Grade {{ $rekomendasi['grading_sekarang'] }}</p>
                </div>
            </div>
        </div>

        <!-- New Grading -->
        <div class="card bg-green-50">
            <h3 class="text-lg font-semibold text-navy-800 mb-4">Grading Rekomendasi</h3>
            <div class="flex items-center justify-center">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-32 h-32 bg-green-200 rounded-full">
                        <span class="text-5xl font-bold text-green-700">{{ $rekomendasi['grading_baru'] }}</span>
                    </div>
                    <p class="text-green-700 mt-4 font-semibold">Grade {{ $rekomendasi['grading_baru'] }}</p>
                    @if($rekomendasi['grading_baru'] > $rekomendasi['grading_sekarang'])
                    <p class="text-sm text-green-600 mt-1">
                        <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Naik {{ $rekomendasi['grading_baru'] - $rekomendasi['grading_sekarang'] }} grade
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alasan Rekomendasi -->
    @if($rekomendasi['eligible'] && count($rekomendasi['alasan']) > 0)
    <div class="card">
        <h3 class="text-lg font-semibold text-navy-800 mb-4">Alasan Rekomendasi</h3>
        <ul class="space-y-3">
            @foreach($rekomendasi['alasan'] as $alasan)
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700">{{ $alasan }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Data Pegawai -->
    <div class="card">
        <h3 class="text-lg font-semibold text-navy-800 mb-4">Data Pegawai</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->jabatan }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Eselon</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->eselon ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Bagian</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->bagian }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Pendidikan</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->pendidikan ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Jurusan</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->jurusan_s1 ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Masa Kerja</label>
                <p class="mt-1 text-gray-900">
                    {{ $pegawai->masa_kerja_tahun ?? 0 }} Tahun {{ $pegawai->masa_kerja_bulan ?? 0 }} Bulan
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">TMT CPNS</label>
                <p class="mt-1 text-gray-900">
                    {{ $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('d F Y') : '-' }}
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Pangkat</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->pangkat ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="card bg-navy-50 border border-navy-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-navy-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-navy-800">Catatan</h3>
                <div class="mt-2 text-sm text-navy-700">
                    <p>Rekomendasi ini berdasarkan pada analisis sistem dan perlu diverifikasi lebih lanjut oleh pejabat yang berwenang sesuai dengan peraturan kepegawaian yang berlaku.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
