<!DOCTYPE html>
<html lang="id"
    x-data="{
        get sidebarOpen() { return $store.app.sidebarOpen; },
        set sidebarOpen(val) { $store.app.sidebarOpen = val; },
        scrolled: false
    }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 }, { passive: true })"
    :class="{ 'dark': $store.app.darkMode }"
    x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', config('app.name') . ' — Sistem Informasi Kepegawaian')">
    <meta name="theme-color" content="#334e68">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'SiTUMAN') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-navy-950 font-sans antialiased min-h-screen transition-colors duration-300">

{{-- ── Loading Overlay ─────────────────────────────────────── --}}
<div x-show="$store.app.isLoading"
     x-transition:enter="transition duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex flex-col items-center justify-center
            bg-white/80 dark:bg-navy-950/80 backdrop-blur-sm"
     style="display:none;">
    <div class="relative w-14 h-14">
        <div class="absolute inset-0 rounded-full border-4 border-navy-200 dark:border-navy-700"></div>
        <div class="absolute inset-0 rounded-full border-4 border-t-navy-600 dark:border-t-gold-400 animate-spin"></div>
    </div>
    <p class="mt-4 text-sm text-navy-600 dark:text-navy-300 font-medium animate-pulse">Memuat…</p>
</div>

{{-- ── Toast Container ─────────────────────────────────────── --}}
<div id="toast-container"
     class="fixed top-4 right-4 z-[90] flex flex-col gap-2.5 w-80 pointer-events-none">
    {{-- Toasts appended via JS --}}
</div>

{{-- ══════════════════════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════════════════════ --}}
<header class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
        :class="scrolled
            ? 'bg-white/95 dark:bg-navy-900/95 backdrop-blur-xl shadow-md shadow-gray-900/5 dark:shadow-navy-900/30 border-b border-gray-200/60 dark:border-navy-700/60'
            : 'bg-white/80  dark:bg-navy-900/80  backdrop-blur-md  border-b border-gray-200/40 dark:border-navy-700/40'">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-3">

            {{-- Left: Toggle + Logo --}}
            <div class="flex items-center gap-2.5">
                <button @click="$store.app.toggleSidebar()"
                        class="btn-icon text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-800"
                        aria-label="Toggle menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 bg-gradient-to-br from-navy-600 to-navy-800 rounded-xl flex items-center justify-center shadow-md
                                group-hover:shadow-navy-700/40 group-hover:shadow-lg transition-shadow duration-200">
                        <span class="text-white font-bold text-sm tracking-tight">ST</span>
                    </div>
                    <span class="hidden sm:block text-lg font-bold text-gradient animate-gradient tracking-tight">SiTUMAN</span>
                </a>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-1">

                {{-- Dark Mode Toggle --}}
                <button @click="$store.app.toggleDarkMode()"
                        class="btn-icon text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-800"
                        aria-label="Toggle theme">
                    {{-- Moon --}}
                    <svg x-show="!$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    {{-- Sun --}}
                    <svg x-show="$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                {{-- Notifications --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="btn-icon relative text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-800"
                            aria-label="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        {{-- Badge dot (aktifkan jika ada notif) --}}
                        {{-- <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-navy-900"></span> --}}
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-80 card z-50 py-0 overflow-hidden origin-top-right"
                         style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Hari ini</span>
                        </div>
                        <div class="px-4 py-10 text-center">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-navy-700 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada notifikasi</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Semua sudah terbaca</p>
                        </div>
                    </div>
                </div>

                {{-- User Menu --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-xl
                                   hover:bg-gray-100 dark:hover:bg-navy-800 transition-colors duration-200">
                        <div class="w-8 h-8 bg-gradient-to-br from-navy-500 to-navy-700 rounded-full
                                    flex items-center justify-center flex-shrink-0 shadow-sm ring-2 ring-white dark:ring-navy-800">
                            <span class="text-xs font-bold text-white uppercase">{{ substr(Auth::user()->nama, 0, 2) }}</span>
                        </div>
                        <div class="hidden sm:block text-left leading-none">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                {{ Str::limit(Auth::user()->nama, 18) }}
                            </p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ Auth::user()->role_label }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                             :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-64 card z-50 py-1.5 overflow-hidden origin-top-right"
                         style="display:none;">

                        {{-- Header --}}
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700 mb-1">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-navy-500 to-navy-700 rounded-full
                                            flex items-center justify-center flex-shrink-0 ring-2 ring-navy-100 dark:ring-navy-600">
                                    <span class="text-sm font-bold text-white uppercase">{{ substr(Auth::user()->nama, 0, 2) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->nama }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                        {{ Auth::user()->email ?? (Auth::user()->nip ?? '') }}
                                    </p>
                                    <span class="inline-block mt-1 {{ Auth::user()->role_color }} text-xs font-medium px-2 py-0.5 rounded-full">
                                        {{ Auth::user()->role_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Items --}}
                        @php
                            $menuItems = [
                                ['href' => route('profile'),           'icon_bg' => 'bg-navy-50 dark:bg-navy-700',   'icon_color' => 'text-navy-600 dark:text-navy-300',   'label' => 'Profil Saya',    'desc' => 'Lihat & edit profil',
                                 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                ['href' => route('profile').'#password','icon_bg' => 'bg-amber-50 dark:bg-navy-700', 'icon_color' => 'text-amber-600 dark:text-amber-400', 'label' => 'Pengaturan',    'desc' => 'Password & keamanan',
                                 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                            ];
                        @endphp

                        @foreach($menuItems as $item)
                        <a href="{{ $item['href'] }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200
                                  hover:bg-gray-50 dark:hover:bg-navy-700/60 transition-colors duration-150 group">
                            <div class="w-8 h-8 {{ $item['icon_bg'] }} rounded-lg flex items-center justify-center
                                        group-hover:scale-110 transition-transform duration-150 flex-shrink-0">
                                <svg class="w-4 h-4 {{ $item['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold leading-none">{{ $item['label'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                            </div>
                        </a>
                        @endforeach

                        @hasrole('superadmin|admin')
                        <div class="mx-4 my-1.5 border-t border-gray-100 dark:border-navy-700"></div>
                        <a href="{{ route('roles.index') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200
                                  hover:bg-gray-50 dark:hover:bg-navy-700/60 transition-colors duration-150 group">
                            <div class="w-8 h-8 bg-purple-50 dark:bg-navy-700 rounded-lg flex items-center justify-center
                                        group-hover:scale-110 transition-transform duration-150 flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold leading-none">Kelola Role</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Atur role & permissions</p>
                            </div>
                        </a>
                        @endhasrole

                        {{-- Logout --}}
                        <div class="mx-4 my-1.5 border-t border-gray-100 dark:border-navy-700"></div>
                        <form method="POST" action="{{ route('logout') }}" class="px-2 pb-1.5">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 dark:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors duration-150 group">
                                <div class="w-8 h-8 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center
                                            group-hover:scale-110 transition-transform duration-150 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold leading-none">Keluar</p>
                                    <p class="text-xs text-red-400 dark:text-red-500 mt-0.5">Logout dari sistem</p>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
                {{-- End User Menu --}}
            </div>
        </div>
    </div>
</header>
{{-- ══ END NAVBAR ══ --}}

{{-- ── Layout Wrapper ─────────────────────────────────────── --}}
<div class="flex pt-16 min-h-screen">

    {{-- ══════════════════════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════════════════════ --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 w-72 flex flex-col
                  bg-white dark:bg-navy-900
                  border-r border-gray-200/80 dark:border-navy-700/80
                  shadow-xl shadow-gray-900/5 dark:shadow-navy-900/20
                  transition-transform duration-300 ease-in-out will-change-transform"
           :class="$store.app.sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Sidebar Top: User card --}}
        <div class="pt-20 px-4 pb-4 border-b border-gray-100 dark:border-navy-700/80">
            <div class="flex items-center gap-3 px-3 py-3
                        bg-gradient-to-r from-navy-50 to-blue-50 dark:from-navy-800 dark:to-navy-700
                        rounded-2xl border border-navy-100 dark:border-navy-600">
                <div class="w-10 h-10 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl
                            flex items-center justify-center flex-shrink-0 shadow-md ring-2 ring-white dark:ring-navy-600">
                    <span class="text-white font-bold uppercase text-sm">{{ substr(Auth::user()->nama, 0, 2) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-none">
                        {{ Auth::user()->nama }}
                    </p>
                    <span class="inline-block mt-1.5 {{ Auth::user()->role_color }} text-xs font-medium px-2 py-0.5 rounded-full">
                        {{ Auth::user()->role_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto scrollbar-thin px-3 py-4 space-y-0.5">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'nav-item-active' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
                @if(request()->routeIs('dashboard'))
                    <span class="ml-auto w-1.5 h-1.5 bg-gold-400 rounded-full"></span>
                @endif
            </a>

            {{-- Kepegawaian --}}
            @canaccess('kepegawaian')
            @php $kepActive = request()->routeIs('kepegawaian.*'); @endphp
            <div x-data="{ open: {{ $kepActive ? 'true' : 'false' }} }" class="space-y-0.5">
                <button @click="open = !open"
                        class="{{ $kepActive ? 'nav-item-active' : 'nav-item-inactive' }} w-full justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Kepegawaian
                    </div>
                    <svg class="w-4 h-4 opacity-60 transition-transform duration-200 flex-shrink-0"
                         :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 space-y-0.5 pt-0.5">
                    @php $kepSubs = [
                        ['route' => 'kepegawaian.sebaran',       'match' => 'kepegawaian.sebaran*',       'label' => 'Sebaran Pegawai'],
                        ['route' => 'kepegawaian.grading',       'match' => 'kepegawaian.grading*',       'label' => 'Kenaikan Grading'],
                        ['route' => 'kepegawaian.mutasi',        'match' => 'kepegawaian.mutasi*',        'label' => 'Proyeksi Mutasi'],
                        ['route' => 'kepegawaian.pegawai.index', 'match' => 'kepegawaian.pegawai*',       'label' => 'Kelola Data Pegawai'],
                    ]; @endphp
                    @foreach($kepSubs as $sub)
                    <a href="{{ route($sub['route']) }}"
                       class="{{ request()->routeIs($sub['match']) ? 'nav-subitem-active' : 'nav-subitem-inactive' }}">
                        {{ $sub['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endcanaccess

            {{-- Anggaran --}}
            @canaccess('anggaran')
            @php $angActive = request()->routeIs('anggaran.*'); @endphp
            <div x-data="{ open: {{ $angActive ? 'true' : 'false' }} }" class="space-y-0.5">
                <button @click="open = !open"
                        class="{{ $angActive ? 'nav-item-active' : 'nav-item-inactive' }} w-full justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Anggaran
                    </div>
                    <svg class="w-4 h-4 opacity-60 transition-transform duration-200 flex-shrink-0"
                         :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 space-y-0.5 pt-0.5">
                    @php $angSubs = [
                        ['route' => 'anggaran.data.index',       'match' => 'anggaran.data.*',       'label' => 'Kelola Data Anggaran'],
                        ['route' => 'anggaran.monitoring.index', 'match' => 'anggaran.monitoring.*', 'label' => 'Monitoring Anggaran'],
                        ['route' => 'anggaran.spp.index',        'match' => 'anggaran.spp.*',        'label' => 'Data SPP'],
                        ['route' => 'anggaran.usulan.index',     'match' => 'anggaran.usulan.*',     'label' => 'Usulan Penarikan Dana'],
                        ['route' => 'anggaran.dokumen.index',    'match' => 'anggaran.dokumen.*',    'label' => 'Dokumen Capaian Output'],
                        ['route' => 'anggaran.revisi.index',     'match' => 'anggaran.revisi.*',     'label' => 'Revisi Anggaran'],
                    ]; @endphp
                    @foreach($angSubs as $sub)
                    <a href="{{ route($sub['route']) }}"
                       class="{{ request()->routeIs($sub['match']) ? 'nav-subitem-active' : 'nav-subitem-inactive' }}">
                        {{ $sub['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endcanaccess

            {{-- Inventaris --}}
            @canaccess('inventaris')
            @php $invActive = request()->routeIs('inventaris.*'); @endphp
            <div x-data="{ open: {{ $invActive ? 'true' : 'false' }} }" class="space-y-0.5">
                <button @click="open = !open"
                        class="{{ $invActive ? 'nav-item-active' : 'nav-item-inactive' }} w-full justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Inventaris
                    </div>
                    <svg class="w-4 h-4 opacity-60 transition-transform duration-200 flex-shrink-0"
                         :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 space-y-0.5 pt-0.5">
                    @php $invSubs = [
                        ['route' => 'inventaris.kategori-atk.index',    'match' => 'inventaris.kategori-atk.*',    'label' => 'Kategori ATK'],
                        ['route' => 'inventaris.monitoring-atk.index',  'match' => 'inventaris.monitoring-atk.*',  'label' => 'Monitoring ATK'],
                        ['route' => 'inventaris.permintaan-atk.index',  'match' => 'inventaris.permintaan-atk.*',  'label' => 'Permintaan ATK'],
                        ['route' => 'inventaris.kategori-aset.index',   'match' => 'inventaris.kategori-aset.*',   'label' => 'Kategori Aset'],
                        ['route' => 'inventaris.aset-end-user.index',   'match' => 'inventaris.aset-end-user.*',   'label' => 'Aset End User'],
                    ]; @endphp
                    @foreach($invSubs as $sub)
                    <a href="{{ route($sub['route']) }}"
                       class="{{ request()->routeIs($sub['match']) ? 'nav-subitem-active' : 'nav-subitem-inactive' }}">
                        {{ $sub['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endcanaccess

            {{-- Admin Section --}}
            @canaccess('users')
            <div class="pt-3 pb-1">
                <p class="section-label">Administrasi</p>
            </div>

            @php $adminLinks = [
                ['route' => 'users.index',       'match' => 'users.*',       'label' => 'Manajemen User',
                 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['route' => 'roles.index',       'match' => 'roles.*',       'label' => 'Kelola Role',
                 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['route' => 'permissions.index', 'match' => 'permissions.*', 'label' => 'Kelola Permission',
                 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
            ]; @endphp

            @foreach($adminLinks as $link)
            <a href="{{ route($link['route']) }}"
               class="{{ request()->routeIs($link['match']) ? 'nav-item-active' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                </svg>
                <span>{{ $link['label'] }}</span>
                @if(request()->routeIs($link['match']))
                    <span class="ml-auto w-1.5 h-1.5 bg-gold-400 rounded-full"></span>
                @endif
            </a>
            @endforeach
            @endcanaccess

        </nav>

        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-gray-100 dark:border-navy-700/80">
            <div class="flex items-center justify-between text-[11px] text-gray-400 dark:text-navy-500">
                <span class="font-medium">SiTUMAN v2.0</span>
                <span>© {{ date('Y') }}</span>
            </div>
        </div>
    </aside>
    {{-- ══ END SIDEBAR ══ --}}

    {{-- Overlay (Mobile) --}}
    <div x-show="$store.app.sidebarOpen"
         @click="$store.app.sidebarOpen = false"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-30 lg:hidden"
         style="display:none;">
    </div>

    {{-- ══════════════════════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════════════════════ --}}
    <main class="flex-1 min-w-0 transition-[margin] duration-300 ease-in-out"
          :class="$store.app.sidebarOpen ? 'lg:ml-72' : 'ml-0'">
        <div class="min-h-[calc(100vh-4rem)] px-4 sm:px-6 lg:px-8 py-6 lg:py-8
                    bg-gray-50 dark:bg-navy-950 transition-colors duration-300">

            {{-- Breadcrumb --}}
            @hasSection('breadcrumb')
            <div class="mb-5">@yield('breadcrumb')</div>
            @endif

            {{-- Page Header --}}
            @hasSection('page_header')
            <div class="mb-6">@yield('page_header')</div>
            @elsehasSection('title')
            <div class="mb-6">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    @yield('title')
                </h1>
                @hasSection('subtitle')
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">@yield('subtitle')</p>
                @endif
            </div>
            @endif

            {{-- Flash Messages --}}
            @foreach([
                'success' => ['bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400',
                              'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'error'   => ['bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-400',
                              'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'warning' => ['bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-400',
                              'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                'info'    => ['bg-sky-50 dark:bg-sky-900/20 border-sky-200 dark:border-sky-800 text-sky-800 dark:text-sky-400',
                              'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as $type => [$cls, $path])
            @if(session($type))
            <div class="mb-5 flex items-start gap-3 px-4 py-3.5 rounded-2xl border {{ $cls }} animate-slide-up"
                 data-auto-hide>
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                </svg>
                <p class="text-sm font-medium">{{ session($type) }}</p>
            </div>
            @endif
            @endforeach

            {{-- Page Content --}}
            <div class="animate-fade-in">
                @yield('content')
            </div>

        </div>
    </main>
</div>
{{-- ── End Layout ── --}}

{{-- Scroll to Top --}}
<button x-show="scrolled"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-90"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-90"
        class="fixed bottom-6 right-6 z-50 w-11 h-11 bg-navy-600 dark:bg-navy-500 text-white
               rounded-2xl shadow-lg shadow-navy-900/30 hover:bg-navy-700 dark:hover:bg-navy-400
               flex items-center justify-center transition-colors duration-200"
        style="display:none;"
        aria-label="Kembali ke atas">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
    </svg>
</button>

@stack('modals')
@stack('scripts')
</body>
</html>
