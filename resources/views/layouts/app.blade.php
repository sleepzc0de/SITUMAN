<!DOCTYPE html>
<html lang="id" x-data="{
    sidebarOpen: $store.app.sidebarOpen,
    darkMode: $store.app.darkMode,
    isScrolled: false
}" x-init="$watch('darkMode', val => $store.app.toggleDarkMode());
window.addEventListener('scroll', () => isScrolled = window.scrollY > 20);" :class="{ 'dark': darkMode }" x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', config('app.name') . ' - Sistem Informasi Kepegawaian Modern')">
    <meta name="author" content="SiTUMAN">
    <meta name="theme-color" content="#334e68">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', 'Sistem Informasi Kepegawaian Modern')">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'SiTUMAN') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Extra Styles -->
    @stack('styles')
</head>

<body
    class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-navy-950 dark:to-navy-900 font-sans antialiased min-h-screen transition-colors duration-300"
    :class="{ 'dark:bg-navy-950': darkMode }">

    <!-- Loading Screen -->
    <div x-show="$store.app.isLoading" x-transition:enter="transition-opacity duration-300"
        x-transition:leave="transition-opacity duration-300"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-white/90 dark:bg-navy-900/90 backdrop-blur-sm">
        <div class="text-center">
            <div class="relative">
                <div
                    class="w-20 h-20 border-4 border-navy-200 dark:border-navy-700 border-t-navy-600 dark:border-t-gold-500 rounded-full animate-spin">
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold text-gradient animate-gradient">S</span>
                </div>
            </div>
            <p class="mt-4 text-navy-600 dark:text-navy-400 font-medium animate-pulse">Memuat...</p>
        </div>
    </div>

    <!-- Notifications Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-3"></div>

    <!-- Navbar -->
    <nav class="bg-white/80 dark:bg-navy-800/80 backdrop-blur-md border-b border-gray-200 dark:border-navy-700 shadow-soft fixed w-full z-40 transition-all duration-300"
        :class="{ 'shadow-strong': isScrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left section -->
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen; $store.app.sidebarOpen = sidebarOpen"
                        class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors duration-200">
                        <svg class="w-6 h-6" :class="{ 'hidden': sidebarOpen }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg class="w-6 h-6" :class="{ 'hidden': !sidebarOpen }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Logo & Brand -->
                    <div class="flex items-center ml-2 lg:ml-0">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-navy-600 to-navy-800 dark:from-navy-500 dark:to-navy-700 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white text-2xl font-bold">ST</span>
                        </div>
                        <span class="ml-3 text-2xl font-bold text-gradient animate-gradient">SiTUMAN</span>
                    </div>
                </div>

                <!-- Right section -->
                <div class="flex items-center space-x-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="$store.app.toggleDarkMode()"
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors duration-200">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                            </path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707">
                            </path>
                        </svg>
                    </button>

                    <!-- Notifications -->
                    <div x-data="dropdown()" class="relative">
                        <button @click="toggle()"
                            class="relative p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span
                                class="absolute top-1 right-1 w-2.5 h-2.5 bg-gold-500 rounded-full animate-pulse"></span>
                        </button>

                        <div x-show="open" @click.away="close()"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 bg-white dark:bg-navy-800 rounded-xl shadow-strong border border-gray-100 dark:border-navy-700 py-2 z-50"
                            style="display: none;">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                            </div>
                            <template x-for="notification in $store.app.notifications" :key="notification.id">
                                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                                    <p class="text-sm text-gray-800 dark:text-gray-200" x-text="notification.message">
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                        x-text="formatDate(notification.time)"></p>
                                </div>
                            </template>
                            <div x-show="$store.app.notifications.length === 0" class="px-4 py-6 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div x-data="dropdown()" class="relative">
                        <button @click="toggle()"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors duration-200 group">
                            <div
                                class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-700 dark:to-navy-600 rounded-full">
                                <span
                                    class="text-sm font-bold text-navy-700 dark:text-navy-200 group-hover:scale-110 transition-transform">
                                    {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                                </span>
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ Auth::user()->nama }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="close()"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-navy-800 rounded-xl shadow-strong border border-gray-100 dark:border-navy-700 py-2 z-50"
                            style="display: none;">

                            <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->nama }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ Auth::user()->email ?? (Auth::user()->nip ?? '') }}</p>
                            </div>

                            <a href="{{ route('profile') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profil Saya
                            </a>

                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Pengaturan
                            </a>

                            <div class="border-t border-gray-100 dark:border-navy-700 my-2"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Layout -->
    <div class="flex pt-16 min-h-screen">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 transform lg:relative lg:translate-x-0 transition-all duration-300 ease-in-out w-72 lg:w-80 bg-white dark:bg-navy-800 border-r border-gray-200 dark:border-navy-700 shadow-soft z-30"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            <!-- User Info Card -->
            <div class="p-6 border-b border-gray-200 dark:border-navy-700">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-navy-500 to-navy-700 dark:from-navy-400 dark:to-navy-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-white text-xl font-bold">
                                {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->nama }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                            @hasrole('superadmin')
                                Super Administrator
                            @endhasrole
                            @hasrole('admin')
                                Administrator
                            @endhasrole
                            @hasrole('user')
                                Pegawai
                            @endhasrole
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto max-h-[calc(100vh-12rem)]">
                @hasrole('superadmin|admin|user')
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 text-navy-700 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        Dashboard

                        @if (request()->routeIs('dashboard'))
                            <span class="ml-auto w-2 h-2 bg-gold-500 rounded-full animate-pulse"></span>
                        @endif
                    </a>

                    <!-- Menu Kepegawaian -->
                    <div x-data="{ open: {{ request()->routeIs('kepegawaian.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('kepegawaian.*') ? 'bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 text-navy-700 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('kepegawaian.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Kepegawaian
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('kepegawaian.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 dark:text-gray-500' }}"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div x-show="open" x-collapse class="ml-8 space-y-1">
                            <a href="{{ route('kepegawaian.sebaran') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('kepegawaian.sebaran') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Sebaran Pegawai
                            </a>
                            <a href="{{ route('kepegawaian.grading') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('kepegawaian.grading') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Rekomendasi Kenaikan Grading
                            </a>
                            <a href="{{ route('kepegawaian.mutasi') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('kepegawaian.mutasi') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Proyeksi Mutasi
                            </a>
                        </div>
                    </div>

                    <!-- Menu Anggaran -->
                    <div x-data="{ open: {{ request()->routeIs('anggaran.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('anggaran.*') ? 'bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 text-navy-700 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('anggaran.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                Anggaran
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('anggaran.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 dark:text-gray-500' }}"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div x-show="open" x-collapse class="ml-8 space-y-1">
                            <a href="{{ route('anggaran.data.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('anggaran.data.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Kelola Data Anggaran
                            </a>
                            <a href="{{ route('anggaran.monitoring.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('anggaran.monitoring.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Monitoring Anggaran
                            </a>
                            <a href="{{ route('anggaran.spp.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('anggaran.spp.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Data SPP
                            </a>
                            <a href="{{ route('anggaran.usulan.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('anggaran.usulan.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Usulan Penarikan Dana
                            </a>
                            <a href="{{ route('anggaran.dokumen.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('anggaran.dokumen.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Dokumen Capaian Output
                            </a>
                            <a href="{{ route('anggaran.revisi.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('anggaran.revisi.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Revisi Anggaran
                            </a>
                        </div>
                    </div>

                    <!-- Menu Inventaris -->
                    <div x-data="{ open: {{ request()->routeIs('inventaris.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('inventaris.*') ? 'bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 text-navy-700 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('inventaris.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Inventaris
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('inventaris.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 dark:text-gray-500' }}"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div x-show="open" x-collapse class="ml-8 space-y-1">
                            <a href="{{ route('inventaris.monitoring-atk.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('inventaris.monitoring-atk.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Monitoring ATK
                            </a>
                            <a href="{{ route('inventaris.permintaan-atk.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('inventaris.permintaan-atk.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Permintaan ATK
                            </a>
                            <a href="{{ route('inventaris.aset-end-user.index') }}"
                                class="block px-4 py-2 text-sm {{ request()->routeIs('inventaris.aset-end-user.*') ? 'text-navy-700 dark:text-gold-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-navy-700 dark:hover:text-gold-400 hover:bg-navy-50 dark:hover:bg-navy-700 rounded-lg transition-colors">
                                Aset End User
                            </a>
                        </div>
                    </div>

                    @hasrole('superadmin|admin')
                        <!-- Manajemen User -->
                        <a href="{{ route('users.index') }}"
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 text-navy-700 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('users.*') ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-navy-500 dark:group-hover:text-gold-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            Manajemen User

                            @if (request()->routeIs('users.*'))
                                <span class="ml-auto w-2 h-2 bg-gold-500 rounded-full animate-pulse"></span>
                            @endif
                        </a>
                    @endhasrole
                @endhasrole
            </nav>

            <!-- Sidebar Footer -->
            <div
                class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Versi 2.0.0</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">© 2024 SiTUMAN</span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main
            class="flex-1 w-full min-w-0 px-4 sm:px-6 lg:px-8 py-6 lg:py-8 bg-gray-50 dark:bg-navy-900 transition-colors duration-300">
            <!-- Breadcrumb -->
            @hasSection('breadcrumb')
                <div class="mb-6 animate-slide-in">
                    @yield('breadcrumb')
                </div>
            @endif

            <!-- Page Header -->
            @hasSection('page_header')
                <div class="mb-6 animate-slide-in">
                    @yield('page_header')
                </div>
            @else
                <div class="mb-6 animate-slide-in">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                        @yield('title')
                    </h1>
                    @hasSection('subtitle')
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            @yield('subtitle')
                        </p>
                    @endif
                </div>
            @endif

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-6 animate-slide-in" x-init="toast.show('{{ session('success') }}', 'success')">
                    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                    d="M9 12l2 2 4-5m6 3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span
                                class="text-sm font-medium text-green-800 dark:text-green-400">{{ session('success') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 animate-slide-in" x-init="toast.show('{{ session('error') }}', 'error')">
                    <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span
                                class="text-sm font-medium text-red-800 dark:text-red-400">{{ session('error') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="animate-fade-in">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Overlay untuk mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false; $store.app.sidebarOpen = false"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20 lg:hidden animate-fade-in"
        x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    <!-- Scroll to top button -->
    <button x-show="isScrolled" @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 p-3 bg-navy-600 dark:bg-navy-700 text-white rounded-full shadow-lg hover:bg-navy-700 dark:hover:bg-navy-600 hover:shadow-xl transform hover:scale-110 transition-all duration-200 z-40">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18">
            </path>
        </svg>
    </button>

    @stack('modals')
    @stack('scripts')
</body>

</html>
