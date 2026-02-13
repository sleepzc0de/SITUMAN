@extends('layouts.app')

@section('title', 'Detail Proyeksi Mutasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-800">Detail Proyeksi Mutasi</h1>
            <p class="text-gray-600 mt-1">Analisis dan rekomendasi mutasi pegawai</p>
        </div>
        <a href="{{ route('kepegawaian.mutasi') }}" class="btn-secondary">
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
                    @if($pegawai->eselon)
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ $pegawai->eselon }}
                    </span>
                    @endif
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gold-100 text-gold-800">
                        {{ $pegawai->bagian }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Status -->
    @if($analisis['perlu_mutasi'])
        @php
            $priority = $analisis['prioritas'];
            if ($priority >= 5) {
                $bgClass = 'bg-red-50 border-red-200';
                $iconColor = 'text-red-500';
                $textColor = 'text-red-800';
                $label = 'Prioritas Tinggi';
                $description = 'Segera perlu dimutasi';
            } elseif ($priority >= 3) {
                $bgClass = 'bg-orange-50 border-orange-200';
                $iconColor = 'text-orange-500';
                $textColor = 'text-orange-800';
                $label = 'Prioritas Sedang';
                $description = 'Perlu dipertimbangkan untuk mutasi';
            } else {
                $bgClass = 'bg-yellow-50 border-yellow-200';
                $iconColor = 'text-yellow-500';
                $textColor = 'text-yellow-800';
                $label = 'Prioritas Rendah';
                $description = 'Dipantau untuk mutasi';
            }
        @endphp

        <div class="card {{ $bgClass }} border-2">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold {{ $textColor }}">{{ $label }}</h3>
                    <p class="{{ $textColor }} mt-1">
                        {{ $description }} - Skor prioritas: {{ $priority }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="card bg-green-50 border-2 border-green-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-green-800">Tidak Perlu Mutasi</h3>
                    <p class="text-green-700 mt-1">
                        Pegawai ini tidak memerlukan mutasi dalam waktu dekat
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Timeline & Recommendation -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Current Position Duration -->
        <div class="card bg-navy-50">
            <h3 class="text-lg font-semibold text-navy-800 mb-4">Masa Jabatan</h3>
            <div class="text-center py-6">
                @if($pegawai->proyeksi_kp_1)
                    @php
                        try {
                            $tmt = \Carbon\Carbon::parse($pegawai->proyeksi_kp_1);
                            $lamaJabatan = $tmt->diffInMonths(now());
                            $tahun = floor($lamaJabatan / 12);
                            $bulan = $lamaJabatan % 12;
                        } catch (\Exception $e) {
                            $tahun = 0;
                            $bulan = 0;
                        }
                    @endphp
                    <div class="text-5xl font-bold text-navy-700">{{ $lamaJabatan ?? 0 }}</div>
                    <p class="text-gray-600 mt-2">Bulan di jabatan saat ini</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $tahun }} tahun {{ $bulan }} bulan</p>
                @else
                    <div class="text-3xl font-bold text-gray-400">N/A</div>
                    <p class="text-gray-500 mt-2">Data TMT tidak tersedia</p>
                @endif
            </div>
        </div>

        <!-- Recommended Time -->
        <div class="card bg-gold-50">
            <h3 class="text-lg font-semibold text-navy-800 mb-4">Rekomendasi Waktu</h3>
            <div class="text-center py-6">
                <div class="text-2xl font-bold text-gold-700">
                    {{ $analisis['rekomendasi_waktu'] ?? 'Belum Ditentukan' }}
                </div>
                <p class="text-gray-600 mt-2">Waktu mutasi yang direkomendasikan</p>
            </div>
        </div>
    </div>

    <!-- Alasan Mutasi -->
    @if($analisis['perlu_mutasi'] && count($analisis['alasan']) > 0)
    <div class="card">
        <h3 class="text-lg font-semibold text-navy-800 mb-4">Pertimbangan Mutasi</h3>
        <ul class="space-y-3">
            @foreach($analisis['alasan'] as $alasan)
            <li class="flex items-start">
                <svg class="w-6 h-6 text-navy-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700">{{ $alasan }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Data Pegawai -->
    <div class="card">
        <h3 class="text-lg font-semibold text-navy-800 mb-4">Informasi Pegawai</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Jabatan Saat Ini</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->jabatan }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Eselon</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->eselon ?? 'Tidak ada' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Bagian</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->bagian }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Subbagian</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->subbagian ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Usia</label>
                <p class="mt-1 text-gray-900">{{ $pegawai->usia ?? '-' }} tahun</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Pensiun</label>
                <p class="mt-1 text-gray-900">
                    {{ $pegawai->tanggal_pensiun ? $pegawai->tanggal_pensiun->format('d F Y') : '-' }}
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Masa Kerja</label>
                <p class="mt-1 text-gray-900">
                    {{ $pegawai->masa_kerja_tahun ?? 0 }} Tahun {{ $pegawai->masa_kerja_bulan ?? 0 }} Bulan
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Grading</label>
                <p class="mt-1 text-gray-900">Grade {{ $pegawai->grading ?? '-' }}</p>
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
                <h3 class="text-sm font-medium text-navy-800">Informasi Penting</h3>
                <div class="mt-2 text-sm text-navy-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Proyeksi mutasi ini bersifat rekomendasi dan memerlukan persetujuan pejabat berwenang</li>
                        <li>Waktu mutasi dapat berubah sesuai dengan kebutuhan organisasi dan kebijakan yang berlaku</li>
                        <li>Mutasi biasanya dilaksanakan pada bulan April atau Oktober setiap tahun</li>
                        <li>Pertimbangkan juga faktor kompetensi dan kebutuhan unit kerja</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
