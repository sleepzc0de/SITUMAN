<div class="space-y-6">
    {{-- Welcome Card --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-navy-700 text-center">
        <div class="w-16 h-16 bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-700 dark:to-navy-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Selamat Datang di SiTUMAN</h2>
        <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-md mx-auto">
            Sistem Informasi Tata Usaha dan Manajemen. Hubungi administrator jika Anda memerlukan akses ke modul tertentu.
        </p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('profile') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white text-sm font-medium rounded-xl hover:bg-navy-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                Lihat Profil
            </a>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 bg-navy-100 dark:bg-navy-700 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Total Pegawai</h3>
            </div>
            <p class="text-3xl font-bold text-navy-600 dark:text-navy-400">{{ number_format($stats['total_pegawai']) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ number_format($stats['pegawai_aktif']) }} pegawai aktif</p>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Unit Kerja</h3>
            </div>
            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['pegawai_per_bagian']->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Bagian/Unit aktif</p>
        </div>
    </div>

    {{-- Informasi Akun --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-navy-700">
        <h3 class="font-bold text-gray-900 dark:text-white mb-4">Informasi Akun Anda</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-navy-700 rounded-xl">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Nama</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->nama }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-navy-700 rounded-xl">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">NIP</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->nip }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-navy-700 rounded-xl">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->email ?? '-' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-navy-700 rounded-xl">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Role</p>
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full {{ auth()->user()->role_color }}">
                        {{ auth()->user()->role_label }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
