<!DOCTYPE html>
<html lang="id"
    x-data="{
        get sidebarOpen() { return $store.app.sidebarOpen },
        set sidebarOpen(v) { $store.app.sidebarOpen = v },
        get darkMode() { return $store.app.darkMode },
        isScrolled: false,
        _scrollTimer: null,
    }"
    x-init="
        window.addEventListener('scroll', () => {
            if (this._scrollTimer) return;
            this._scrollTimer = requestAnimationFrame(() => {
                isScrolled = window.scrollY > 20;
                this._scrollTimer = null;
            });
        }, { passive: true });
    "
    :class="{ 'dark': $store.app.darkMode }"
    x-cloak>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', config('app.name') . ' - Sistem Informasi Kepegawaian Modern')">
    <meta name="theme-color" content="#334e68">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'SiTUMAN') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('head')
</head>
<body class="bg-gray-50 dark:bg-navy-950 font-sans antialiased min-h-screen">

{{-- ===== PAGE LOADER ===== --}}
<div id="page-loader"
     class="fixed inset-0 z-[200] flex flex-col items-center justify-center overflow-hidden"
     style="background: linear-gradient(135deg, #0f172a 0%, #1a2332 50%, #243b53 100%);"
     aria-hidden="true">
    {{-- Grid pattern --}}
    <div class="absolute inset-0 pointer-events-none"
         style="background-image: radial-gradient(circle, rgba(148,163,184,0.07) 1px, transparent 1px); background-size: 28px 28px;"></div>
    {{-- Ambient glow --}}
    <div class="absolute pointer-events-none"
         style="top:20%;left:20%;width:400px;height:400px;background:radial-gradient(circle,rgba(71,101,129,0.2) 0%,transparent 65%);filter:blur(40px);border-radius:50%;"></div>
    <div class="absolute pointer-events-none"
         style="bottom:20%;right:20%;width:320px;height:320px;background:radial-gradient(circle,rgba(245,158,11,0.08) 0%,transparent 65%);filter:blur(40px);border-radius:50%;"></div>
    {{-- Floating particles --}}
    @php
        $particles = [
            ['size' => 4, 'color' => 'rgba(251,191,36,0.5)',   'top' => 15, 'left' => 20, 'dur' => 3.5, 'delay' => 0.0],
            ['size' => 3, 'color' => 'rgba(255,255,255,0.15)', 'top' => 70, 'left' => 75, 'dur' => 4.2, 'delay' => 0.8],
            ['size' => 5, 'color' => 'rgba(251,191,36,0.3)',   'top' => 30, 'left' => 55, 'dur' => 3.8, 'delay' => 0.4],
            ['size' => 3, 'color' => 'rgba(255,255,255,0.1)',  'top' => 55, 'left' => 35, 'dur' => 5.0, 'delay' => 1.2],
            ['size' => 4, 'color' => 'rgba(251,191,36,0.25)',  'top' => 80, 'left' => 15, 'dur' => 4.5, 'delay' => 0.6],
            ['size' => 6, 'color' => 'rgba(255,255,255,0.1)',  'top' => 20, 'left' => 85, 'dur' => 3.2, 'delay' => 1.5],
            ['size' => 3, 'color' => 'rgba(251,191,36,0.2)',   'top' => 65, 'left' => 45, 'dur' => 4.8, 'delay' => 0.2],
            ['size' => 4, 'color' => 'rgba(255,255,255,0.18)', 'top' => 45, 'left' => 65, 'dur' => 3.9, 'delay' => 1.0],
        ];
    @endphp
    @foreach($particles as $p)
    <div class="absolute rounded-full loader-particle pointer-events-none"
         style="width:{{ $p['size'] }}px;height:{{ $p['size'] }}px;background:{{ $p['color'] }};top:{{ $p['top'] }}%;left:{{ $p['left'] }}%;animation-duration:{{ $p['dur'] }}s;animation-delay:{{ $p['delay'] }}s;"></div>
    @endforeach
    {{-- Main content --}}
    <div class="relative flex flex-col items-center gap-10 select-none px-8">
        {{-- Logo --}}
        <div class="loader-logo flex flex-col items-center gap-5">
            <div class="loader-logo-box w-24 h-24 rounded-3xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#486581 0%,#243b53 100%);box-shadow:0 0 0 1px rgba(255,255,255,0.08);">
                <span class="text-white font-bold text-4xl" style="font-family:'Inter',sans-serif;letter-spacing:-1px;">ST</span>
            </div>
            <div class="text-center">
                <h1 class="loader-title text-4xl font-bold text-white" style="letter-spacing:-1px;">SiTUMAN</h1>
                <p class="loader-subtitle text-sm font-medium mt-1" style="color:#7898b8;letter-spacing:0.05em;">
                    Sistem Informasi TU Biro Manajemen BMN dan Pengadaan
                </p>
            </div>
        </div>
        {{-- Spinner + progress --}}
        <div class="flex flex-col items-center gap-5" style="width:220px;">
            <div class="relative flex items-center justify-center" style="width:56px;height:56px;">
                <svg style="position:absolute;width:56px;height:56px;" viewBox="0 0 56 56">
                    <circle cx="28" cy="28" r="22" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="3"/>
                </svg>
                <svg style="position:absolute;width:56px;height:56px;transform:rotate(-90deg);" viewBox="0 0 56 56">
                    <defs>
                        <linearGradient id="ringFill" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%"   stop-color="#f59e0b"/>
                            <stop offset="100%" stop-color="#fbbf24"/>
                        </linearGradient>
                    </defs>
                    <circle cx="28" cy="28" r="22" fill="none"
                            stroke="url(#ringFill)" stroke-width="3" stroke-linecap="round"
                            stroke-dasharray="138.2" stroke-dashoffset="138.2"
                            class="loader-circle"/>
                </svg>
                <svg class="loader-spin-arc" style="position:absolute;width:56px;height:56px;" viewBox="0 0 56 56">
                    <circle cx="28" cy="28" r="22" fill="none"
                            stroke="rgba(251,191,36,0.8)" stroke-width="3" stroke-linecap="round"
                            stroke-dasharray="10 128.2"/>
                </svg>
                <div class="loader-dot" style="width:8px;height:8px;background:#fbbf24;border-radius:50%;position:relative;z-index:1;"></div>
            </div>
            <div style="width:100%;">
                <div style="height:2px;width:100%;background:rgba(255,255,255,0.07);border-radius:999px;overflow:hidden;">
                    <div class="loader-bar" style="height:100%;border-radius:999px;background:linear-gradient(90deg,#f59e0b,#fbbf24);width:0%;"></div>
                </div>
                <p class="loader-status" style="color:#627d98;font-size:12px;text-align:center;margin-top:10px;font-weight:500;letter-spacing:0.03em;">
                    Memuat sistem...
                </p>
            </div>
        </div>
    </div>
    {{-- Footer --}}
    <div style="position:absolute;bottom:24px;color:#334e68;font-size:11px;display:flex;align-items:center;gap:8px;">
        <span>SiTUMAN v2.0</span>
        <span style="width:3px;height:3px;background:#334e68;border-radius:50%;display:inline-block;"></span>
        <span>© {{ date('Y') }}</span>
    </div>
</div>
{{-- ===== END PAGE LOADER ===== --}}

{{-- Alpine loading overlay --}}
<div x-show="$store.app.isLoading"
     x-transition:enter="transition-opacity duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-white/80 dark:bg-navy-900/80 backdrop-blur-sm"
     style="display:none;">
    <div class="text-center">
        <div class="w-12 h-12 border-4 border-navy-200 border-t-navy-600 rounded-full animate-spin mx-auto"></div>
        <p class="mt-3 text-navy-600 dark:text-navy-300 font-medium text-sm">Memuat...</p>
    </div>
</div>

{{-- Toast Container --}}
<div id="toast-container"
     class="fixed top-4 right-4 z-[60] flex flex-col gap-2 w-80"
     style="pointer-events:none;">
</div>

{{-- ===== NAVBAR ===== --}}
<nav class="bg-white/90 dark:bg-navy-800/90 backdrop-blur-md border-b border-gray-200 dark:border-navy-700
            fixed w-full z-40 transition-[box-shadow] duration-200"
     :class="isScrolled ? 'shadow-md' : 'shadow-sm'">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- LEFT --}}
            <div class="flex items-center gap-3">
                <button @click="$store.app.toggleSidebar()"
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700
                               transition-colors duration-150"
                        :aria-expanded="$store.app.sidebarOpen"
                        aria-label="Toggle sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 bg-gradient-to-br from-navy-600 to-navy-800 rounded-xl
                                flex items-center justify-center shadow-md
                                group-hover:shadow-lg group-hover:scale-105 transition-transform duration-200">
                        <span class="text-white font-bold text-sm">ST</span>
                    </div>
                    <span class="hidden sm:block text-xl font-bold text-gradient animate-gradient">SiTUMAN</span>
                </a>
            </div>
            {{-- RIGHT --}}
            <div class="flex items-center gap-1 sm:gap-2">
                {{-- Dark Mode --}}
                <button @click="$store.app.toggleDarkMode()"
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700
                               transition-colors duration-150"
                        :aria-label="darkMode ? 'Mode terang' : 'Mode gelap'">
                    <svg x-show="!$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="$store.app.darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                {{-- Notifications --}}
                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button @click="open = !open"
                            class="relative p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700
                                   transition-colors duration-150"
                            :aria-expanded="open" aria-haspopup="true" aria-label="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(false)
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-navy-800"></span>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-80 bg-white dark:bg-navy-800 rounded-xl shadow-xl
                                border border-gray-100 dark:border-navy-700 py-2 z-50 origin-top-right"
                         style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Hari ini</span>
                        </div>
                        <div class="px-4 py-8 text-center">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                        </div>
                    </div>
                </div>

                {{-- User Menu --}}
                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl
                                   hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors duration-150"
                            :aria-expanded="open" aria-haspopup="true">
                        <div class="w-8 h-8 bg-gradient-to-br from-navy-500 to-navy-700 rounded-full
                                    flex items-center justify-center flex-shrink-0 shadow-sm">
                            <span class="text-xs font-bold text-white uppercase">
                                {{ substr(Auth::user()->nama, 0, 2) }}
                            </span>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 leading-none">
                                {{ Str::limit(Auth::user()->nama, 20) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ Auth::user()->role_label }}
                            </p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0"
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
                         class="absolute right-0 mt-2 w-60 bg-white dark:bg-navy-800 rounded-xl shadow-xl
                                border border-gray-100 dark:border-navy-700 py-1 z-50 origin-top-right"
                         style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-navy-500 to-navy-700 rounded-full
                                            flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-white uppercase">
                                        {{ substr(Auth::user()->nama, 0, 2) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ Auth::user()->nama }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ Auth::user()->email ?? (Auth::user()->nip ?? '') }}
                                    </p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full {{ Auth::user()->role_color }}">
                                        {{ Auth::user()->role_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile') }}"
                               class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200
                                      hover:bg-gray-50 dark:hover:bg-navy-700/70 transition-colors duration-100 group">
                                <div class="w-8 h-8 bg-navy-50 dark:bg-navy-700 rounded-lg flex items-center justify-center mr-3
                                            group-hover:bg-navy-100 dark:group-hover:bg-navy-600 transition-colors">
                                    <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">Profil Saya</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Lihat & edit profil</p>
                                </div>
                            </a>
                            <a href="{{ route('profile') }}#password"
                               class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200
                                      hover:bg-gray-50 dark:hover:bg-navy-700/70 transition-colors duration-100 group">
                                <div class="w-8 h-8 bg-gold-50 dark:bg-navy-700 rounded-lg flex items-center justify-center mr-3
                                            group-hover:bg-gold-100 dark:group-hover:bg-navy-600 transition-colors">
                                    <svg class="w-4 h-4 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
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
                               class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200
                                      hover:bg-gray-50 dark:hover:bg-navy-700/70 transition-colors duration-100 group">
                                <div class="w-8 h-8 bg-purple-50 dark:bg-navy-700 rounded-lg flex items-center justify-center mr-3
                                            group-hover:bg-purple-100 dark:group-hover:bg-navy-600 transition-colors">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">Kelola Role</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Atur role & permissions</p>
                                </div>
                            </a>
                            @endhasrole
                        </div>
                        <div class="border-t border-gray-100 dark:border-navy-700 pt-1 pb-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 dark:text-red-400
                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-100 group">
                                    <div class="w-8 h-8 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center mr-3
                                                group-hover:bg-red-100 dark:group-hover:bg-red-900/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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
            </div>
        </div>
    </div>
</nav>
{{-- ===== END NAVBAR ===== --}}

<div class="flex pt-16 min-h-screen">
    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-30 w-72 bg-white dark:bg-navy-800
                  border-r border-gray-200 dark:border-navy-700 shadow-sm
                  transition-transform duration-300 ease-in-out flex flex-col"
           :class="$store.app.sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           aria-label="Sidebar navigasi">
        <div class="p-4 border-b border-gray-200 dark:border-navy-700 pt-20">
            <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600 rounded-xl">
                <div class="w-11 h-11 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl
                            flex items-center justify-center shadow-md flex-shrink-0">
                    <span class="text-white font-bold uppercase">{{ substr(Auth::user()->nama, 0, 2) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->nama }}</p>
                    <span class="inline-block mt-0.5 px-2 py-0.5 text-xs font-medium rounded-full {{ Auth::user()->role_color }}">
                        {{ Auth::user()->role_label }}
                    </span>
                </div>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 scrollbar-thin" aria-label="Menu utama">
            @php $isDash = request()->routeIs('dashboard') @endphp
            <a href="{{ route('dashboard') }}" class="{{ $isDash ? 'nav-item-active' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
                @if($isDash)<span class="ml-auto w-1.5 h-1.5 bg-gold-400 rounded-full"></span>@endif
            </a>

            @canaccess('kepegawaian')
            @php $isKepeg = request()->routeIs('kepegawaian.*') @endphp
            <div x-data="{ open: @js($isKepeg) }" class="space-y-0.5">
                <button @click="open = !open" class="w-full {{ $isKepeg ? 'nav-item-active' : 'nav-item-inactive' }} justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Kepegawaian
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-10 space-y-0.5">
                    @php $kepegawaianLinks = [
                        ['route' => 'kepegawaian.sebaran',       'pattern' => 'kepegawaian.sebaran*', 'label' => 'Sebaran Pegawai'],
                        ['route' => 'kepegawaian.grading',       'pattern' => 'kepegawaian.grading*', 'label' => 'Kenaikan Grading'],
                        ['route' => 'kepegawaian.mutasi',        'pattern' => 'kepegawaian.mutasi*',  'label' => 'Proyeksi Mutasi'],
                        ['route' => 'kepegawaian.pegawai.index', 'pattern' => 'kepegawaian.pegawai*', 'label' => 'Kelola Data Pegawai'],
                    ]; @endphp
                    @foreach($kepegawaianLinks as $link)
                    <a href="{{ route($link['route']) }}" class="{{ request()->routeIs($link['pattern']) ? 'nav-subitem-active' : 'nav-subitem-inactive' }}">
                        {{ $link['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endcanaccess

            @canaccess('anggaran')
            @php $isAnggaran = request()->routeIs('anggaran.*') @endphp
            <div x-data="{ open: @js($isAnggaran) }" class="space-y-0.5">
                <button @click="open = !open" class="w-full {{ $isAnggaran ? 'nav-item-active' : 'nav-item-inactive' }} justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Anggaran
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-10 space-y-0.5">
                    @php $anggaranLinks = [
                        ['route' => 'anggaran.data.index',       'pattern' => 'anggaran.data.*',       'label' => 'Kelola Data Anggaran'],
                        ['route' => 'anggaran.monitoring.index', 'pattern' => 'anggaran.monitoring.*', 'label' => 'Monitoring Anggaran'],
                        ['route' => 'anggaran.spp.index',        'pattern' => 'anggaran.spp.*',        'label' => 'Data SPP'],
                        ['route' => 'anggaran.usulan.index',     'pattern' => 'anggaran.usulan.*',     'label' => 'Usulan Penarikan Dana'],
                        ['route' => 'anggaran.dokumen.index',    'pattern' => 'anggaran.dokumen.*',    'label' => 'Dokumen Capaian Output'],
                        ['route' => 'anggaran.revisi.index',     'pattern' => 'anggaran.revisi.*',     'label' => 'Revisi Anggaran'],
                    ]; @endphp
                    @foreach($anggaranLinks as $link)
                    <a href="{{ route($link['route']) }}" class="{{ request()->routeIs($link['pattern']) ? 'nav-subitem-active' : 'nav-subitem-inactive' }}">
                        {{ $link['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endcanaccess

            @canaccess('inventaris')
            @php $isInv = request()->routeIs('inventaris.*') @endphp
            <div x-data="{ open: @js($isInv) }" class="space-y-0.5">
                <button @click="open = !open" class="w-full {{ $isInv ? 'nav-item-active' : 'nav-item-inactive' }} justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Inventaris
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-10 space-y-0.5">
                    @php $inventarisLinks = [
                        ['route' => 'inventaris.kategori-atk.index',   'pattern' => 'inventaris.kategori-atk.*',   'label' => 'Kategori ATK'],
                        ['route' => 'inventaris.monitoring-atk.index', 'pattern' => 'inventaris.monitoring-atk.*', 'label' => 'Monitoring ATK'],
                        ['route' => 'inventaris.permintaan-atk.index', 'pattern' => 'inventaris.permintaan-atk.*', 'label' => 'Permintaan ATK'],
                        ['route' => 'inventaris.kategori-aset.index',  'pattern' => 'inventaris.kategori-aset.*',  'label' => 'Kategori Aset'],
                        ['route' => 'inventaris.aset-end-user.index',  'pattern' => 'inventaris.aset-end-user.*',  'label' => 'Aset End User'],
                    ]; @endphp
                    @foreach($inventarisLinks as $link)
                    <a href="{{ route($link['route']) }}" class="{{ request()->routeIs($link['pattern']) ? 'nav-subitem-active' : 'nav-subitem-inactive' }}">
                        {{ $link['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endcanaccess

            @canaccess('users')
            <div class="pt-3 pb-1">
                <p class="section-label">Administrasi</p>
            </div>
            @php $adminLinks = [
                ['route' => 'users.index',       'pattern' => 'users.*',       'label' => 'Manajemen User',
                 'path' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['route' => 'roles.index',       'pattern' => 'roles.*',       'label' => 'Kelola Role',
                 'path' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['route' => 'permissions.index', 'pattern' => 'permissions.*', 'label' => 'Kelola Permission',
                 'path' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
            ]; @endphp
            @foreach($adminLinks as $link)
            @php $isActive = request()->routeIs($link['pattern']) @endphp
            <a href="{{ route($link['route']) }}" class="{{ $isActive ? 'nav-item-active' : 'nav-item-inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['path'] }}"/>
                </svg>
                {{ $link['label'] }}
                @if($isActive)<span class="ml-auto w-1.5 h-1.5 bg-gold-400 rounded-full"></span>@endif
            </a>
            @endforeach
            @endcanaccess
        </nav>
        <div class="p-4 border-t border-gray-200 dark:border-navy-700">
            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-600">
                <span>SiTUMAN v2.0</span>
                <span>© {{ date('Y') }}</span>
            </div>
        </div>
    </aside>
    {{-- ===== END SIDEBAR ===== --}}

    {{-- Overlay mobile --}}
    <div x-show="$store.app.sidebarOpen"
         @click="$store.app.sidebarOpen = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20 lg:hidden"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none;" aria-hidden="true"></div>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-1 min-w-0 transition-[margin] duration-300 ease-in-out overflow-x-hidden"
          :class="$store.app.sidebarOpen ? 'lg:ml-72' : 'ml-0'">
        <div class="px-4 sm:px-6 lg:px-8 py-6 lg:py-8 min-h-screen bg-gray-50 dark:bg-navy-900 transition-colors duration-200">

            @hasSection('breadcrumb')
            <div class="mb-6">@yield('breadcrumb')</div>
            @endif

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

            {{-- Flash messages --}}
            @foreach([
                'success' => ['bg-green-50 dark:bg-green-900/20',   'border-green-200 dark:border-green-700',   'text-green-800 dark:text-green-400',   'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'error'   => ['bg-red-50 dark:bg-red-900/20',       'border-red-200 dark:border-red-700',       'text-red-800 dark:text-red-400',       'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'warning' => ['bg-yellow-50 dark:bg-yellow-900/20', 'border-yellow-200 dark:border-yellow-700', 'text-yellow-800 dark:text-yellow-400', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                'info'    => ['bg-sky-50 dark:bg-sky-900/20',       'border-sky-200 dark:border-sky-700',       'text-sky-800 dark:text-sky-400',       'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as $type => [$bg, $border, $text, $path])
                @if(session($type))
                <div class="mb-5 {{ $bg }} border {{ $border }} {{ $text }} px-4 py-3 rounded-xl flex items-center gap-3"
                     data-auto-hide role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session($type) }}</span>
                </div>
                @endif
            @endforeach

            <div class="animate-fade-in">
                @yield('content')
            </div>
        </div>
    </main>
</div>

{{-- Scroll to top --}}
<button x-show="isScrolled"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 p-3 bg-navy-600 text-white rounded-full shadow-lg
               hover:bg-navy-700 hover:scale-110 active:scale-95
               transition-[transform,background-color] duration-200 z-40"
        style="display:none;" aria-label="Kembali ke atas">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
    </svg>
</button>

@stack('modals')
@stack('scripts')
</body>
</html>
