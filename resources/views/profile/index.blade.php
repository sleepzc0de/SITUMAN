{{-- resources/views/profile/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Saya')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Profil Saya', 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div>
    <h1 class="page-title">Profil Saya</h1>
    <p class="page-subtitle">Informasi akun dan pengaturan keamanan</p>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- ── Kartu Profil Utama ── --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">

            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <div class="w-20 h-20 bg-gradient-to-br from-navy-500 to-navy-700
                            rounded-2xl flex items-center justify-center shadow-md">
                    <span class="text-white text-2xl font-bold uppercase">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 2)) }}
                    </span>
                </div>
            </div>

            {{-- Info utama --}}
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                    {{ Auth::user()->nama }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                    NIP {{ Auth::user()->nip }}
                </p>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 {{ Auth::user()->role_color }}">
                        {{ Auth::user()->role_label }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        Bergabung {{ Auth::user()->created_at->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Informasi Pribadi ── --}}
    <div class="card space-y-4">
        <div class="section-header !mb-0">
            <div>
                <h2 class="section-title">Informasi Akun</h2>
                <p class="section-desc">Data identitas yang terdaftar di sistem</p>
            </div>
        </div>
        <div class="divider"></div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
            <div>
                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Nama Lengkap
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ Auth::user()->nama }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    NIP
                </dt>
                <dd>
                    <code class="text-sm font-mono bg-gray-100 dark:bg-navy-700
                                  text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded">
                        {{ Auth::user()->nip }}
                    </code>
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Email Kemenkeu
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white break-all">
                    {{ Auth::user()->email }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Email Pribadi
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white break-all">
                    {{ Auth::user()->email_pribadi ?? '—' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    No. HP
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white">
                    {{ Auth::user()->no_hp ?? '—' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Terakhir Diperbarui
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white">
                    {{ Auth::user()->updated_at->translatedFormat('d F Y, H:i') }}
                </dd>
            </div>
        </dl>

        {{-- Akses Modul --}}
        <div class="pt-2">
            <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Akses Modul
            </dt>
            @php
                $moduleLabels = [
                    'dashboard'   => ['label' => 'Dashboard',   'color' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
                    'kepegawaian' => ['label' => 'Kepegawaian', 'color' => 'bg-navy-100 text-navy-700 dark:bg-navy-700 dark:text-navy-300'],
                    'anggaran'    => ['label' => 'Anggaran',    'color' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'],
                    'inventaris'  => ['label' => 'Inventaris',  'color' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'],
                    'users'       => ['label' => 'Manajemen User', 'color' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'],
                    'roles'       => ['label' => 'Kelola Role', 'color' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'],
                ];
                $accessibleModules = array_filter(
                    array_keys($moduleLabels),
                    fn($m) => Auth::user()->canAccessModule($m)
                );
            @endphp
            <div class="flex flex-wrap gap-2">
                @forelse($accessibleModules as $module)
                    @php $ml = $moduleLabels[$module]; @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ml['color'] }}">
                        {{ $ml['label'] }}
                    </span>
                @empty
                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">Tidak ada akses modul</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Keamanan / Ubah Password ── --}}
    <div class="card space-y-4"
         id="password"
         x-data="{
             open: {{ $errors->has('current_password') || $errors->has('password') || session('password_success') ? 'true' : 'false' }},
             showCurrent: false,
             showNew: false,
             showConfirm: false,
         }">

        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
            <div>
                <h2 class="section-title">Keamanan</h2>
                <p class="section-desc">Ubah password akun Anda</p>
            </div>
            <button type="button" class="btn-ghost btn-sm flex-shrink-0 pointer-events-none">
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                <span x-text="open ? 'Tutup' : 'Ubah Password'"></span>
            </button>
        </div>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display:none">
            <div class="divider !mt-0 mb-4"></div>

            {{-- Success --}}
            @if(session('password_success'))
            <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20
                         border border-green-200 dark:border-green-700 rounded-xl mb-4"
                 x-data="{ show: true }" x-show="show">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-300 flex-1">
                    {{ session('password_success') }}
                </p>
                <button type="button" @click="show = false"
                        class="text-green-500 hover:text-green-700 dark:hover:text-green-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endif

            <form method="POST" action="{{ route('profile.password.update') }}"
                  autocomplete="off">
                @csrf
                @method('PUT')

                <div class="space-y-5">

                    {{-- Password Lama --}}
                    <div class="input-group">
                        <label class="input-label" for="current_password">
                            Password Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showCurrent ? 'text' : 'password'"
                                   id="current_password" name="current_password"
                                   class="input-field pr-10 @error('current_password') input-error @enderror"
                                   autocomplete="current-password" required>
                            <button type="button" @click="showCurrent = !showCurrent"
                                    class="absolute inset-y-0 right-3 flex items-center
                                           text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <svg x-show="!showCurrent" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showCurrent" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="input-hint-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div class="input-group">
                        <label class="input-label" for="password">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'"
                                   id="password" name="password"
                                   class="input-field pr-10 @error('password') input-error @enderror"
                                   autocomplete="new-password" required>
                            <button type="button" @click="showNew = !showNew"
                                    class="absolute inset-y-0 right-3 flex items-center
                                           text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="input-hint-error">{{ $message }}</p>
                        @else
                            <p class="input-hint">Min. 8 karakter, huruf besar/kecil, angka, dan simbol</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="input-group">
                        <label class="input-label" for="password_confirmation">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'"
                                   id="password_confirmation" name="password_confirmation"
                                   class="input-field pr-10"
                                   autocomplete="new-password" required>
                            <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-3 flex items-center
                                           text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Syarat password --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 p-3
                                bg-gray-50 dark:bg-navy-800/60 rounded-xl">
                        @foreach([
                            'Min. 8 karakter',
                            'Huruf besar & kecil',
                            'Mengandung angka',
                            'Mengandung simbol',
                        ] as $hint)
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 flex-shrink-0"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"/>
                            </svg>
                            {{ $hint }}
                        </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-1">
                        <button type="button" @click="open = false" class="btn-ghost">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Simpan Password
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ── Info Sesi ── --}}
    <div class="card-flat">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-gray-100 dark:bg-navy-700 rounded-xl flex-shrink-0">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Sesi Aktif</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Login sebagai <strong>{{ Auth::user()->role_label }}</strong>
                    sejak sesi ini dimulai
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="btn-danger btn-sm flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
