{{-- resources/views/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah User')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Administrasi', 'url' => null, 'active' => false],
        ['title' => 'Manajemen User', 'url' => route('users.index'), 'active' => false],
        ['title' => 'Tambah User', 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="page-title">Tambah User Baru</h1>
        <p class="page-subtitle">Buat akun pengguna baru untuk sistem</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn-outline btn-sm flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Banner info batasan role --}}
    @if(!auth()->user()->isSuperadmin())
    <div class="alert-info mb-5 flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm">
            <p class="font-semibold">Batasan Wewenang</p>
            <p class="mt-0.5 opacity-90">
                Sebagai <strong>Administrator</strong>, Anda hanya dapat membuat user dengan role
                <strong>Eksekutif, PIC Kepegawaian, PIC Keuangan, PIC Inventaris,</strong> dan <strong>User Biasa</strong>.
            </p>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}"
          x-data="{ showPass: false, showPassConf: false }">
        @csrf

        {{-- ── Informasi Akun ── --}}
        <div class="card space-y-5">
            <div class="section-header !mb-0">
                <div>
                    <h2 class="section-title">Informasi Akun</h2>
                    <p class="section-desc">Data identitas dan akses pengguna</p>
                </div>
            </div>
            <div class="divider"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Nama --}}
                <div class="input-group md:col-span-2">
                    <label class="input-label" for="nama">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama"
                           value="{{ old('nama') }}"
                           class="input-field @error('nama') input-error @enderror"
                           placeholder="Masukkan nama lengkap..."
                           autocomplete="name" required>
                    @error('nama')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NIP --}}
                <div class="input-group">
                    <label class="input-label" for="nip">
                        NIP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nip" name="nip"
                           value="{{ old('nip') }}"
                           maxlength="18"
                           class="input-field font-mono @error('nip') input-error @enderror"
                           placeholder="18 digit NIP..."
                           autocomplete="off" required>
                    @error('nip')
                        <p class="input-hint-error">{{ $message }}</p>
                    @else
                        <p class="input-hint">Maksimal 18 karakter</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="input-group">
                    <label class="input-label" for="role">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select id="role" name="role"
                            class="input-field @error('role') input-error @enderror"
                            required>
                        <option value="">— Pilih Role —</option>
                        @foreach($allowedRoles as $value => $label)
                            <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="input-hint-error">{{ $message }}</p>
                    @else
                        @if(auth()->user()->isSuperadmin())
                            <p class="input-hint">Role Super Administrator tidak dapat dibuat lewat form ini</p>
                        @endif
                    @enderror
                </div>

                {{-- Email Kemenkeu --}}
                <div class="input-group">
                    <label class="input-label" for="email">
                        Email Kemenkeu <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           class="input-field @error('email') input-error @enderror"
                           placeholder="nip@kemenkeu.go.id"
                           autocomplete="email" required>
                    @error('email')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email Pribadi --}}
                <div class="input-group">
                    <label class="input-label" for="email_pribadi">
                        Email Pribadi
                    </label>
                    <input type="email" id="email_pribadi" name="email_pribadi"
                           value="{{ old('email_pribadi') }}"
                           class="input-field @error('email_pribadi') input-error @enderror"
                           placeholder="contoh@gmail.com">
                    @error('email_pribadi')
                        <p class="input-hint-error">{{ $message }}</p>
                    @else
                        <p class="input-hint">Opsional</p>
                    @enderror
                </div>

                {{-- No HP --}}
                <div class="input-group md:col-span-2">
                    <label class="input-label" for="no_hp">No. HP</label>
                    <input type="text" id="no_hp" name="no_hp"
                           value="{{ old('no_hp') }}"
                           class="input-field @error('no_hp') input-error @enderror"
                           placeholder="08xxxxxxxxxx"
                           maxlength="15">
                    @error('no_hp')
                        <p class="input-hint-error">{{ $message }}</p>
                    @else
                        <p class="input-hint">Opsional — maksimal 15 karakter</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ── Password ── --}}
        <div class="card space-y-5 mt-5">
            <div class="section-header !mb-0">
                <div>
                    <h2 class="section-title">Password</h2>
                    <p class="section-desc">Minimal 8 karakter, kombinasi huruf besar/kecil, angka, dan simbol</p>
                </div>
            </div>
            <div class="divider"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Password --}}
                <div class="input-group">
                    <label class="input-label" for="password">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'"
                               id="password" name="password"
                               class="input-field pr-10 @error('password') input-error @enderror"
                               autocomplete="new-password" required>
                        <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400
                                       hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="input-group">
                    <label class="input-label" for="password_confirmation">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPassConf ? 'text' : 'password'"
                               id="password_confirmation" name="password_confirmation"
                               class="input-field pr-10"
                               autocomplete="new-password" required>
                        <button type="button" @click="showPassConf = !showPassConf"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400
                                       hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg x-show="!showPassConf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassConf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Syarat password --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                @foreach(['Min. 8 karakter', 'Huruf besar & kecil', 'Mengandung angka', 'Mengandung simbol'] as $hint)
                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $hint }}
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="flex items-center justify-between gap-3 mt-5">
            <a href="{{ route('users.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat User
            </button>
        </div>

    </form>
</div>
@endsection
