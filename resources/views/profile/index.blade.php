@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-navy-800">Profil Saya</h1>
        <p class="text-gray-600 mt-1">Informasi akun dan pengaturan</p>
    </div>

    <!-- Profile Card -->
    <div class="card">
        <div class="flex items-start space-x-6">
            <div class="flex-shrink-0">
                <div class="w-24 h-24 bg-gradient-to-br from-navy-600 to-gold-500 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                    {{ substr(Auth::user()->nama, 0, 2) }}
                </div>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-navy-800">{{ Auth::user()->nama }}</h2>
                <p class="text-gray-600 mt-1">{{ Auth::user()->nip }}</p>
                @if(Auth::user()->role)
                <span class="inline-block mt-2 px-3 py-1 text-sm font-semibold rounded-full
                    {{ Auth::user()->role == 'superadmin' ? 'bg-purple-100 text-purple-800' :
                       (Auth::user()->role == 'admin' ? 'bg-navy-100 text-navy-800' : 'bg-gold-100 text-gold-800') }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Information -->
    <div class="card">
        <h3 class="text-lg font-semibold text-navy-800 mb-4">Informasi Pribadi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Kemenkeu</label>
                <p class="mt-1 text-gray-900">{{ Auth::user()->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Pribadi</label>
                <p class="mt-1 text-gray-900">{{ Auth::user()->email_pribadi ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">No. HP</label>
                <p class="mt-1 text-gray-900">{{ Auth::user()->no_hp ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Terdaftar Sejak</label>
                <p class="mt-1 text-gray-900">{{ Auth::user()->created_at->format('d F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="card" x-data="{ showPasswordForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-navy-800">Keamanan</h3>
            <button @click="showPasswordForm = !showPasswordForm" class="btn-secondary text-sm">
                <span x-text="showPasswordForm ? 'Batal' : 'Ubah Password'"></span>
            </button>
        </div>

        <div x-show="showPasswordForm" x-collapse>
            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4 border-t pt-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Password Lama</label>
                    <input type="password" name="current_password" id="current_password" class="input-field mt-1" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" name="password" id="password" class="input-field mt-1" required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="input-field mt-1" required>
                </div>

                <button type="submit" class="btn-primary">
                    Simpan Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
