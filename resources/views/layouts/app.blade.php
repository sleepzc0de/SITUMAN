<!DOCTYPE html>
<html lang="id"
    x-data="{
        get sidebarOpen() { return $store.app.sidebarOpen; },
        set sidebarOpen(val) { $store.app.sidebarOpen = val; },
        get darkMode() { return $store.app.darkMode; },
        isScrolled: false
    }"
    x-init="window.addEventListener('scroll', () => { isScrolled = window.scrollY > 20; })"
    :class="{ 'dark': $store.app.darkMode }"
    x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', config('app.name') . ' - Sistem Informasi Kepegawaian Modern')">
    <meta name="theme-color" content="#334e68">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'SiTUMAN') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-navy-950 dark:to-navy-900 font-sans antialiased min-h-screen transition-colors duration-300">

    <!-- Loading Screen -->
    <div x-show="$store.app.isLoading"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-white/90 dark:bg-navy-900/90 backdrop-blur-sm"
        style="display:none;">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-navy-200 border-t-navy-600 rounded-full animate-spin mx-auto"></div>
            <p class="mt-4 text-navy-600 font-medium animate-pulse">Memuat...</p>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-[60] space-y-3 w-80"></div>

    <!-- ===== NAVBAR ===== -->
    <nav class="bg-white/90 dark:bg-navy-800/90 backdrop-blur-md border-b border-gray-200 dark:border-navy-700 shadow-sm fixed w-full z-40 transition-all duration-300"
        :class="{ 'shadow-lg': isScrolled }">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <!-- LEFT: Menu Button + Logo -->
                <div class="flex items-center space-x-3">
                    <!-- Mobile/Toggle Sidebar -->
                    <button @click="$store.app.sidebarOpen = !$store.app.sidebarOpen"
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-9 h-9 bg-gradient-to-br from-navy-600 to-navy-800 rounded-xl flex items-center justify-center shadow-md">
                            <span class="text-white font-bold text-sm">ST</span>
                        </div>
                        <span class="hidden sm:block text-xl font-bold text-gradient animate-gradient">SiTUMAN</span>
                    </a>
                </div>

                <!-- RIGHT: Dark Mode + Notif + User -->
                <div class="flex items-center space-x-1 sm:space-x-2">

                    <!-- Dark Mode Toggle -->
                    <button @click="$store.app.toggleDarkMode()"
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">
                        <!-- Moon icon (light mode) -->
                        <svg x-show="!$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <!-- Sun icon (dark mode) -->
                        <svg x-show="$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="relative p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if(false){{-- placeholder notif count --}}
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        </button>

                        <!-- Dropdown Notif -->
                        <div x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                            class="absolute right-0 mt-2 w-80 bg-white dark:bg-navy-800 rounded-xl shadow-xl border border-gray-100 dark:border-navy-700 py-2 z-50 origin-top-right"
                            style="display: none;">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Hari ini</span>
                            </div>
                            <div class="px-4 py-8 text-center">
                                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                            </div>
                        </div>
                    </div>

                    <!-- ✅ USER MENU DROPDOWN (Fixed) -->
                    <div class="relative" x-data="{ open: false }">
                        <!-- Trigger Button -->
                        <button @click="open = !open"
                            class="flex items-center space-x-2 pl-2 pr-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors duration-200">
                            <!-- Avatar -->
                            <div class="w-8 h-8 bg-gradient-to-br from-navy-500 to-navy-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="text-xs font-bold text-white uppercase">
                                    {{ substr(Auth::user()->nama, 0, 2) }}
                                </span>
                            </div>
                            <!-- Name + Role (hidden on mobile) -->
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 leading-none">
                                    {{ Str::limit(Auth::user()->nama, 20) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-none mt-0.5">
                                    {{ Auth::user()->role_label }}
                                </p>
                            </div>
                            <!-- Chevron -->
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200 flex-shrink-0"
                                :class="{ 'rotate-180': open }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- ✅ Dropdown Menu -->
                        <div x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                            class="absolute right-0 mt-2 w-60 bg-white dark:bg-navy-800 rounded-xl shadow-xl border border-gray-100 dark:border-navy-700 py-1 z-50 origin-top-right"
                            style="display: none;">

                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-navy-500 to-navy-700 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-white uppercase">
                                            {{ substr(Auth::user()->nama, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ Auth::user()->nama }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ Auth::user()->email ?? Auth::user()->nip ?? '' }}
                                        </p>
                                        <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full {{ Auth::user()->role_color }}">
                                            {{ Auth::user()->role_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-1">
                                <a href="{{ route('profile') }}"
                                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700/70 transition-colors group">
                                    <div class="w-8 h-8 bg-navy-50 dark:bg-navy-700 rounded-lg flex items-center justify-center mr-3 group-hover:bg-navy-100 dark:group-hover:bg-navy-600 transition-colors">
                                        <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Profil Saya</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Lihat & edit profil</p>
                                    </div>
                                </a>

                                <a href="{{ route('profile') }}#password"
                                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700/70 transition-colors group">
                                    <div class="w-8 h-8 bg-gold-50 dark:bg-navy-700 rounded-lg flex items-center justify-center mr-3 group-hover:bg-gold-100 dark:group-hover:bg-navy-600 transition-colors">
                                        <svg class="w-4 h-4 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Pengaturan</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Password & keamanan</p>
                                    </div>
                                </a>

                                @hasrole('superadmin|admin')
                                <div class="mx-4 my-1 border-t border-gray-100 dark:border-navy-700"></div>
                                <a href="{{ route('roles.index') }}"
                                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700/70 transition-colors group">
                                    <div class="w-8 h-8 bg-purple-50 dark:bg-navy-700 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-100 dark:group-hover:bg-navy-600 transition-colors">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Kelola Role</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Atur role & permissions</p>
                                    </div>
                                </a>
                                @endhasrole
                            </div>

                            <!-- Divider & Logout -->
                            <div class="border-t border-gray-100 dark:border-navy-700 pt-1 pb-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors group">
                                        <div class="w-8 h-8 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-red-100 dark:group-hover:bg-red-900/30 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <p class="font-medium">Keluar</p>
                                            <p class="text-xs text-red-400 dark:text-red-500">Logout dari sistem</p>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- END User Menu -->

                </div>
            </div>
        </div>
    </nav>
    <!-- ===== END NAVBAR ===== -->

    <!-- Main Layout -->
    <div class="flex pt-16 min-h-screen">

        <!-- ===== SIDEBAR ===== -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-30 w-72 bg-white dark:bg-navy-800 border-r border-gray-200 dark:border-navy-700 shadow-sm transition-transform duration-300 flex flex-col"
            :class="$store.app.sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Sidebar Header (User Info) -->
            <div class="p-4 border-b border-gray-200 dark:border-navy-700 pt-20">
                <div class="flex items-center space-x-3 p-3 bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 rounded-xl">
                    <div class="w-11 h-11 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                        <span class="text-white font-bold uppercase">
                            {{ substr(Auth::user()->nama, 0, 2) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->nama }}
                        </p>
                        <span class="inline-block mt-0.5 px-2 py-0.5 text-xs font-medium rounded-full {{ Auth::user()->role_color }}">
                            {{ Auth::user()->role_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

                <!-- Dashboard - Semua Role -->
                <a href="{{ route('dashboard') }}"
                    class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                        {{ request()->routeIs('dashboard') ? 'bg-navy-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                    @if(request()->routeIs('dashboard'))
                        <span class="ml-auto w-1.5 h-1.5 bg-gold-400 rounded-full"></span>
                    @endif
                </a>

                <!-- Kepegawaian - superadmin, admin, picpegawai -->
                @canaccess('kepegawaian')
                <div x-data="{ open: {{ request()->routeIs('kepegawaian.*') ? 'true' : 'false' }} }" class="space-y-0.5">
                    <button @click="open = !open"
                        class="w-full group flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                            {{ request()->routeIs('kepegawaian.*') ? 'bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 text-navy-700 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kepegawaian.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Kepegawaian
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="pl-10 space-y-0.5">
                        <a href="{{ route('kepegawaian.sebaran') }}"
                            class="block px-3 py-2 text-sm rounded-lg transition-colors
                                {{ request()->routeIs('kepegawaian.sebaran*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">
                            Sebaran Pegawai
                        </a>
                        <a href="{{ route('kepegawaian.grading') }}"
                            class="block px-3 py-2 text-sm rounded-lg transition-colors
                                {{ request()->routeIs('kepegawaian.grading*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">
                            Kenaikan Grading
                        </a>
                        <a href="{{ route('kepegawaian.mutasi') }}"
                            class="block px-3 py-2 text-sm rounded-lg transition-colors
                                {{ request()->routeIs('kepegawaian.mutasi*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">
                            Proyeksi Mutasi
                        </a>
                    </div>
                </div>
                @endcanaccess

                <!-- Anggaran - superadmin, admin, pickeuangan -->
                @canaccess('anggaran')
                <div x-data="{ open: {{ request()->routeIs('anggaran.*') ? 'true' : 'false' }} }" class="space-y-0.5">
                    <button @click="open = !open"
                        class="w-full group flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                            {{ request()->routeIs('anggaran.*') ? 'bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 text-navy-700 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('anggaran.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Anggaran
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-10 space-y-0.5">
                        <a href="{{ route('anggaran.data.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('anggaran.data.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Kelola Data Anggaran</a>
                        <a href="{{ route('anggaran.monitoring.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('anggaran.monitoring.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Monitoring Anggaran</a>
                        <a href="{{ route('anggaran.spp.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('anggaran.spp.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Data SPP</a>
                        <a href="{{ route('anggaran.usulan.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('anggaran.usulan.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Usulan Penarikan Dana</a>
                        <a href="{{ route('anggaran.dokumen.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('anggaran.dokumen.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Dokumen Capaian Output</a>
                        <a href="{{ route('anggaran.revisi.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('anggaran.revisi.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Revisi Anggaran</a>
                    </div>
                </div>
                @endcanaccess

                <!-- Inventaris - superadmin, admin, picinventaris -->
                @canaccess('inventaris')
                <div x-data="{ open: {{ request()->routeIs('inventaris.*') ? 'true' : 'false' }} }" class="space-y-0.5">
                    <button @click="open = !open"
                        class="w-full group flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                            {{ request()->routeIs('inventaris.*') ? 'bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 text-navy-700 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('inventaris.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Inventaris
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-10 space-y-0.5">
                        <a href="{{ route('inventaris.kategori-atk.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('inventaris.kategori-atk.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Kategori ATK</a>
                        <a href="{{ route('inventaris.monitoring-atk.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('inventaris.monitoring-atk.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Monitoring ATK</a>
                        <a href="{{ route('inventaris.permintaan-atk.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('inventaris.permintaan-atk.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Permintaan ATK</a>
                        <a href="{{ route('inventaris.kategori-aset.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('inventaris.kategori-aset.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Kategori Aset</a>
                        <a href="{{ route('inventaris.aset-end-user.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('inventaris.aset-end-user.*') ? 'text-navy-700 dark:text-gold-400 font-medium bg-navy-50 dark:bg-navy-700' : 'text-gray-500 dark:text-gray-400 hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700' }}">Aset End User</a>
                    </div>
                </div>
                @endcanaccess

                <!-- Divider Admin Section -->
                @canaccess('users')
                <div class="pt-2 pb-1">
                    <p class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Administrasi</p>
                </div>

                <!-- Manajemen User -->
                <a href="{{ route('users.index') }}"
                    class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                        {{ request()->routeIs('users.*') ? 'bg-navy-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manajemen User
                    @if(request()->routeIs('users.*'))
                        <span class="ml-auto w-1.5 h-1.5 bg-gold-400 rounded-full"></span>
                    @endif
                </a>

                <!-- Kelola Role -->
                <a href="{{ route('roles.index') }}"
                    class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                        {{ request()->routeIs('roles.*') ? 'bg-navy-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('roles.*') ? 'text-white' : 'text-gray-400 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Kelola Role
                    @if(request()->routeIs('roles.*'))
                        <span class="ml-auto w-1.5 h-1.5 bg-gold-400 rounded-full"></span>
                    @endif
                </a>

                <!-- Permission -->
                <a href="{{ route('permissions.index') }}"
                    class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                        {{ request()->routeIs('permissions.*') ? 'bg-navy-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('permissions.*') ? 'text-white' : 'text-gray-400 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Kelola Permission
                </a>
                @endcanaccess

            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-200 dark:border-navy-700">
                <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-600">
                    <span>SiTUMAN v2.0</span>
                    <span>© {{ date('Y') }}</span>
                </div>
            </div>
        </aside>
        <!-- ===== END SIDEBAR ===== -->

        <!-- Overlay (Mobile) -->
        <div x-show="$store.app.sidebarOpen"
            @click="$store.app.sidebarOpen = false"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20 lg:hidden"
            x-transition:enter="transition-opacity duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"></div>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="flex-1 min-w-0 transition-all duration-300 overflow-x-hidden"
            :class="$store.app.sidebarOpen ? 'lg:ml-72' : 'ml-0'">
            <div class="px-4 sm:px-6 lg:px-8 py-6 lg:py-8 min-h-screen bg-gray-50 dark:bg-navy-900 transition-colors duration-300">

                <!-- Breadcrumb -->
                @hasSection('breadcrumb')
                    <div class="mb-6">@yield('breadcrumb')</div>
                @endif

                <!-- Page Header -->
                @hasSection('page_header')
                    <div class="mb-6">@yield('page_header')</div>
                @else
                    @hasSection('title')
                    <div class="mb-6">
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">@yield('title')</h1>
                        @hasSection('subtitle')
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@yield('subtitle')</p>
                        @endif
                    </div>
                    @endif
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-400 px-4 py-3 rounded-xl flex items-center space-x-3" data-auto-hide>
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-400 px-4 py-3 rounded-xl flex items-center space-x-3" data-auto-hide>
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-400 px-4 py-3 rounded-xl flex items-center space-x-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('warning') }}</span>
                    </div>
                @endif

                <!-- Content -->
                <div class="animate-fade-in">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Scroll to Top -->
    <button x-show="isScrolled"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 p-3 bg-navy-600 text-white rounded-full shadow-lg hover:bg-navy-700 transform hover:scale-110 transition-all duration-200 z-40"
        style="display: none;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    @stack('modals')
    @stack('scripts')
</body>
</html>
