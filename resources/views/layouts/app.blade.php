<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }" x-cloak>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Navbar -->
    <nav class="bg-navy-800 shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-white lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex items-center ml-4">
                        <span class="text-gold-400 text-2xl font-bold">SiTUMAN</span>
                    </div>
                </div>

                <div class="flex items-center">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center text-white hover:text-gold-400 transition">
                            <span class="mr-2">{{ Auth::user()->nama }}</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-navy-50">Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-800 hover:bg-navy-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex pt-16 min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 transform lg:relative lg:translate-x-0 transition duration-200 ease-in-out bg-navy-700 w-64 pt-16 z-40"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <nav class="mt-5 px-2">
                @hasrole('superadmin|admin|user')
                <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 text-white hover:bg-navy-600 rounded-lg mb-1 transition">
                    <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Menu Kepegawaian -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 text-white hover:bg-navy-600 rounded-lg mb-1 transition">
                        <div class="flex items-center">
                            <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Kepegawaian
                        </div>
                        <svg class="h-5 w-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="ml-8 space-y-1">
                        <a href="{{ route('kepegawaian.sebaran') }}" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-navy-600 rounded">Sebaran Pegawai</a>
                        <a href="{{ route('kepegawaian.grading') }}" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-navy-600 rounded">Rekomendasi Kenaikan Grading</a>
                        <a href="{{ route('kepegawaian.mutasi') }}" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-navy-600 rounded">Proyeksi Mutasi</a>
                    </div>
                </div>

                @hasrole('superadmin|admin')
                <a href="{{ route('users.index') }}" class="group flex items-center px-4 py-3 text-white hover:bg-navy-600 rounded-lg mb-1 transition">
                    <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Manajemen User
                </a>
                @endhasrole
                @endhasrole
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    <!-- Overlay untuk mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black opacity-50 z-30 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>
</body>
</html>
