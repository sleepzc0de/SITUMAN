{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit User')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Administrasi', 'url' => null, 'active' => false],
        ['title' => 'Manajemen User', 'url' => route('users.index'), 'active' => false],
        ['title' => $user->nama, 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="page-title">Edit User</h1>
        <div class="flex items-center gap-2 mt-1">
            <p class="page-subtitle">{{ $user->nama }}</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $user->role_color }}">
                {{ $user->role_label }}
            </span>
        </div>
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
    <form method="POST" action="{{ route('users.update', $user) }}"
          x-data="{ showPass: false, showPassConf: false, changePassword: false }">
        @csrf
        @method('PUT')

        {{-- ── Info strip ── --}}
        <div class="card-flat flex items-center gap-4 mb-5">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-navy-500 to-navy-700
                        flex items-center justify-center flex-shrink-0 shadow-md">
                <span class="text-sm font-bold text-white uppercase">
                    {{ strtoupper(substr($user->nama, 0, 2)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->nama }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">{{ $user->nip }}</p>
            </div>
            <div class="text-right text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">
                <p>Dibuat</p>
                <p class="font-medium text-gray-600 dark:text-gray-300">{{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>

        {{-- ── Informasi Akun ── --}}
        <div class="card space-y-5">
            <div class="section-header !mb-0">
                <div>
                    <h2 class="section-title">Informasi Akun</h2>
                    <p class="section-desc">Perbarui data identitas pengguna</p>
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
                           value="{{ old('nama', $user->nama) }}"
                           class="input-field @error('nama') input-error @enderror"
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
                           value="{{ old('nip', $user->nip) }}"
                           maxlength="18"
                           class="input-field font-mono @error('nip') input-error @enderror"
                           autocomplete="off" required>
                    @error('nip')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="input-group">
                    <label class="input-label" for="role">
                        Role <span class="text-red-500">*</span>
                    </label>
                    @if($user->role === 'superadmin' && !auth()->user()->isSuperadmin())
                        {{-- Non-superadmin tidak bisa ubah role superadmin --}}
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <div class="input-field bg-gray-50 dark:bg-navy-700/50 cursor-not-allowed flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $user->role_color }}">
                                {{ $user->role_label }}
                            </span>
                            <span class="text-xs text-gray-400">Tidak dapat diubah</span>
                        </div>
                    @else
                        <select id="role" name="role"
                                class="input-field @error('role') input-error @enderror"
                                required>
                            <option value="">— Pilih Role —</option>
                            @foreach($availableRoles as $value => $label)
                                {{-- Sembunyikan opsi superadmin jika bukan superadmin --}}
                                @if($value === 'superadmin' && !auth()->user()->isSuperadmin())
                                    @continue
                                @endif
                                <option value="{{ $value }}"
                                        {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('role')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email Kemenkeu --}}
                <div class="input-group">
                    <label class="input-label" for="email">
                        Email Kemenkeu <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           class="input-field @error('email') input-error @enderror"
                           autocomplete="email" required>
                    @error('email')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email Pribadi --}}
                <div class="input-group">
                    <label class="input-label" for="email_pribadi">Email Pribadi</label>
                    <input type="email" id="email_pribadi" name="email_pribadi"
                           value="{{ old('email_pribadi', $user->email_pribadi) }}"
                           class="input-field @error('email_pribadi') input-error @enderror"
                           placeholder="Opsional">
                    @error('email_pribadi')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No HP --}}
                <div class="input-group md:col-span-2">
                    <label class="input-label" for="no_hp">No. HP</label>
                    <input type="text" id="no_hp" name="no_hp"
                           value="{{ old('no_hp', $user->no_hp) }}"
                           class="input-field @error('no_hp') input-error @enderror"
                           placeholder="08xxxxxxxxxx" maxlength="15">
                    @error('no_hp')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ── Ubah Password ── --}}
        <div class="card mt-5 space-y-4">
            <div class="section-header !mb-0 cursor-pointer" @click="changePassword = !changePassword">
                <div>
                    <h2 class="section-title">Ubah Password</h2>
                    <p class="section-desc">Kosongkan jika tidak ingin mengubah password</p>
                </div>
                <button type="button" class="btn-ghost btn-sm flex-shrink-0">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="changePassword ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span x-text="changePassword ? 'Tutup' : 'Ubah Password'"></span>
                </button>
            </div>

            <div x-show="changePassword"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="display:none">
                <div class="divider !mt-0 mb-4"></div>

                @if($errors->has('password'))
                <div class="alert-warning mb-4">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm">{{ $errors->first('password') }}</p>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div class="input-group">
                        <label class="input-label" for="password">Password Baru</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'"
                                   id="password" name="password"
                                   class="input-field pr-10 @error('password') input-error @enderror"
                                   autocomplete="new-password">
                            <button type="button" @click="showPass = !showPass"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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
                        <p class="input-hint">Min. 8 karakter, huruf besar/kecil, angka, simbol</p>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="password_confirmation">Konfirmasi Password</label>
                        <div class="relative">
                            <input :type="showPassConf ? 'text' : 'password'"
                                   id="password_confirmation" name="password_confirmation"
                                   class="input-field pr-10"
                                   autocomplete="new-password">
                            <button type="button" @click="showPassConf = !showPassConf"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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
            </div>
        </div>

        {{-- ── Danger Zone (hapus) ── --}}
        @if($user->canBeDeleted() && $user->id !== auth()->id())
        <div class="card border-red-200 dark:border-red-800/50 mt-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-red-700 dark:text-red-400">Zona Berbahaya</p>
                    <p class="text-xs text-red-600/80 dark:text-red-400/70 mt-1">
                        Menghapus user bersifat permanen dan tidak dapat dibatalkan.
                    </p>
                </div>
                <button type="button"
                        x-data="confirmDelete('{{ route('users.destroy', $user) }}', '{{ addslashes($user->nama) }}')"
                        @click="submit()"
                        class="btn-danger btn-sm flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus User
                </button>
            </div>
        </div>
        @endif

        {{-- ── Actions ── --}}
        <div class="flex items-center justify-between gap-3 mt-5">
            <a href="{{ route('users.index') }}" class="btn-ghost">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection
