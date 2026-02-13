@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-800">Dashboard</h1>
            <p class="text-gray-600 mt-1">Selamat datang di Sistem Informasi Tata Usaha dan Manajemen (SiTUMAN)</p>
        </div>
    </div>

    @if(session('no_role'))
    <!-- Modal Alert No Role -->
    <div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="show = false" class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-gold-100 rounded-full">
                    <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <h3 class="mt-4 text-lg font-semibold text-center text-navy-800">
                    Perlu Pengajuan Role
                </h3>

                <p class="mt-2 text-sm text-center text-gray-600">
                    Akun Anda belum memiliki role. Silakan hubungi administrator untuk mengajukan role yang sesuai agar dapat mengakses fitur-fitur sistem.
                </p>

                <div class="mt-6 flex justify-center">
                    <button @click="show = false" class="btn-primary">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card bg-gradient-to-br from-navy-600 to-navy-800 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-navy-200 text-sm">Total Pegawai</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $stats['total_pegawai'] }}</h3>
                </div>
                <div class="bg-navy-500 bg-opacity-50 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-br from-gold-400 to-gold-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gold-100 text-sm">Pegawai Aktif</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $stats['pegawai_aktif'] }}</h3>
                </div>
                <div class="bg-gold-300 bg-opacity-50 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-br from-green-500 to-green-700 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total User</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $stats['total_users'] }}</h3>
                </div>
                <div class="bg-green-400 bg-opacity-50 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card bg-gradient-to-br from-purple-500 to-purple-700 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Bagian</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $stats['pegawai_per_bagian']->count() }}</h3>
                </div>
                <div class="bg-purple-400 bg-opacity-50 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sebaran Per Bagian -->
        <div class="card">
            <h3 class="text-lg font-semibold text-navy-800 mb-4">Sebaran Pegawai Per Bagian</h3>
            <div class="space-y-3">
                @foreach($stats['pegawai_per_bagian']->take(5) as $bagian)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-700">{{ $bagian->bagian ?? 'Tidak Ada Bagian' }}</span>
                        <span class="text-sm font-semibold text-navy-700">{{ $bagian->total }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-navy-600 to-gold-500 h-2 rounded-full"
                             style="width: {{ ($bagian->total / $stats['total_pegawai']) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('kepegawaian.sebaran') }}" class="text-navy-600 hover:text-navy-800 text-sm font-medium">
                    Lihat Selengkapnya →
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h3 class="text-lg font-semibold text-navy-800 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('kepegawaian.sebaran') }}"
                   class="flex items-center p-4 bg-navy-50 hover:bg-navy-100 rounded-lg transition border border-navy-200">
                    <div class="bg-navy-600 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-navy-800">Sebaran Pegawai</h4>
                        <p class="text-sm text-gray-600">Lihat distribusi pegawai</p>
                    </div>
                </a>

                <a href="{{ route('kepegawaian.grading') }}"
                   class="flex items-center p-4 bg-gold-50 hover:bg-gold-100 rounded-lg transition border border-gold-200">
                    <div class="bg-gold-500 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-navy-800">Kenaikan Grading</h4>
                        <p class="text-sm text-gray-600">Rekomendasi kenaikan grading</p>
                    </div>
                </a>

                <a href="{{ route('kepegawaian.mutasi') }}"
                   class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition border border-green-200">
                    <div class="bg-green-600 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-navy-800">Proyeksi Mutasi</h4>
                        <p class="text-sm text-gray-600">Analisis dan proyeksi mutasi</p>
                    </div>
                </a>

                @can('viewAny', App\Models\User::class)
                <a href="{{ route('users.index') }}"
                   class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition border border-purple-200">
                    <div class="bg-purple-600 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-navy-800">Manajemen User</h4>
                        <p class="text-sm text-gray-600">Kelola pengguna sistem</p>
                    </div>
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
