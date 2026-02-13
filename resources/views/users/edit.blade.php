@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-800">Edit User</h1>
            <p class="text-gray-600 mt-1">Ubah informasi pengguna</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn-secondary">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="card max-w-3xl">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div class="md:col-span-2">
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama', $user->nama) }}"
                           class="input-field mt-1 @error('nama') border-red-500 @enderror"
                           required>
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIP -->
                <div>
                    <label for="nip" class="block text-sm font-medium text-gray-700">NIP *</label>
                    <input type="text"
                           name="nip"
                           id="nip"
                           value="{{ old('nip', $user->nip) }}"
                           maxlength="18"
                           class="input-field mt-1 @error('nip') border-red-500 @enderror"
                           required>
                    @error('nip')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                    <select name="role"
                            id="role"
                            class="input-field mt-1 @error('role') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Role</option>
                        <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Kemenkeu *</label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $user->email) }}"
                           class="input-field mt-1 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Pribadi -->
                <div>
                    <label for="email_pribadi" class="block text-sm font-medium text-gray-700">Email Pribadi</label>
                    <input type="email"
                           name="email_pribadi"
                           id="email_pribadi"
                           value="{{ old('email_pribadi', $user->email_pribadi) }}"
                           class="input-field mt-1 @error('email_pribadi') border-red-500 @enderror">
                    @error('email_pribadi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No HP -->
                <div class="md:col-span-2">
                    <label for="no_hp" class="block text-sm font-medium text-gray-700">No. HP</label>
                    <input type="text"
                           name="no_hp"
                           id="no_hp"
                           value="{{ old('no_hp', $user->no_hp) }}"
                           class="input-field mt-1 @error('no_hp') border-red-500 @enderror">
                    @error('no_hp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 border-t pt-6">
                    <h3 class="text-lg font-semibold text-navy-800 mb-4">Ubah Password (Opsional)</h3>
                    <p class="text-sm text-gray-600 mb-4">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="input-field mt-1 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Minimal 8 karakter</p>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="input-field mt-1">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('users.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
