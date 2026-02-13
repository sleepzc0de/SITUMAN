@extends('layouts.app')

@section('title', 'Detail Pegawai')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-800">Detail Pegawai</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap pegawai</p>
        </div>
        <a href="{{ route('kepegawaian.sebaran') }}" class="btn-secondary">
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
                        Grade {{ $pegawai->grading }}
                    </span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $pegawai->status == 'AKTIF' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $pegawai->status }}
                    </span>
                    @if($pegawai->jenis_kelamin)
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ $pegawai->jenis_kelamin }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: 'personal' }">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'personal'"
                        :class="activeTab === 'personal' ? 'border-navy-500 text-navy-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                    Data Personal
                </button>
                <button @click="activeTab = 'jabatan'"
                        :class="activeTab === 'jabatan' ? 'border-navy-500 text-navy-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                    Jabatan
                </button>
                <button @click="activeTab = 'kepegawaian'"
                        :class="activeTab === 'kepegawaian' ? 'border-navy-500 text-navy-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                    Kepegawaian
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="mt-6">
            <!-- Personal Tab -->
            <div x-show="activeTab === 'personal'" class="card">
                <h3 class="text-lg font-semibold text-navy-800 mb-4">Informasi Personal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->nama }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIP</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->nip }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <p class="mt-1 text-gray-900">
                            {{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('d F Y') : '-' }}
                            @if($pegawai->usia)
                            <span class="text-gray-500">({{ $pegawai->usia }} tahun)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->jenis_kelamin ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Kemenkeu</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->email_kemenkeu ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Pribadi</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->email_pribadi ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No. HP</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->no_hp ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pendidikan</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->pendidikan ?? '-' }}</p>
                    </div>
                    @if($pegawai->jurusan_s1)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jurusan S1</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->jurusan_s1 }}</p>
                    </div>
                    @endif
                    @if($pegawai->jurusan_s2)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jurusan S2</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->jurusan_s2 }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Jabatan Tab -->
            <div x-show="activeTab === 'jabatan'" class="card">
                <h3 class="text-lg font-semibold text-navy-800 mb-4">Informasi Jabatan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->jabatan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Jabatan</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->jenis_jabatan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Jabatan</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->nama_jabatan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Eselon</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->eselon ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Grading</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->grading ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Pegawai</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->jenis_pegawai ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bagian</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->bagian ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subbagian</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->subbagian ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->lokasi ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pangkat</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->pangkat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Kepegawaian Tab -->
            <div x-show="activeTab === 'kepegawaian'" class="card">
                <h3 class="text-lg font-semibold text-navy-800 mb-4">Informasi Kepegawaian</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">TMT CPNS</label>
                        <p class="mt-1 text-gray-900">
                            {{ $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('d F Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Masa Kerja</label>
                        <p class="mt-1 text-gray-900">
                            {{ $pegawai->masa_kerja_tahun ?? 0 }} Tahun {{ $pegawai->masa_kerja_bulan ?? 0 }} Bulan
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Pensiun</label>
                        <p class="mt-1 text-gray-900">
                            {{ $pegawai->tanggal_pensiun ? $pegawai->tanggal_pensiun->format('d F Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p class="mt-1">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $pegawai->status == 'AKTIF' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $pegawai->status }}
                            </span>
                        </p>
                    </div>
                    @if($pegawai->proyeksi_kp_1)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Proyeksi Kenaikan Pangkat 1</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->proyeksi_kp_1 }}</p>
                    </div>
                    @endif
                    @if($pegawai->proyeksi_kp_2)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Proyeksi Kenaikan Pangkat 2</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->proyeksi_kp_2 }}</p>
                    </div>
                    @endif
                    @if($pegawai->keterangan_kp)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <p class="mt-1 text-gray-900">{{ $pegawai->keterangan_kp }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
