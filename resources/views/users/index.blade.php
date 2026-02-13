@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-800">Manajemen User</h1>
            <p class="text-gray-600 mt-1">Kelola pengguna dan hak akses sistem</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah User
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Filter & Search -->
    <div class="card">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama, email, atau NIP..."
                       class="input-field">
            </div>

            <select name="role" class="input-field md:w-48">
                <option value="">Semua Role</option>
                <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
            </select>

            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Cari
            </button>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card bg-gradient-to-br from-purple-500 to-purple-700 text-white">
            <h4 class="text-sm text-purple-100">Superadmin</h4>
            <p class="text-3xl font-bold mt-2">{{ $users->where('role', 'superadmin')->count() }}</p>
        </div>
        <div class="card bg-gradient-to-br from-navy-500 to-navy-700 text-white">
            <h4 class="text-sm text-navy-200">Admin</h4>
            <p class="text-3xl font-bold mt-2">{{ $users->where('role', 'admin')->count() }}</p>
        </div>
        <div class="card bg-gradient-to-br from-gold-400 to-gold-600 text-white">
            <h4 class="text-sm text-gold-100">User</h4>
            <p class="text-3xl font-bold mt-2">{{ $users->where('role', 'user')->count() }}</p>
        </div>
        <div class="card bg-gradient-to-br from-gray-400 to-gray-600 text-white">
            <h4 class="text-sm text-gray-100">Tanpa Role</h4>
            <p class="text-3xl font-bold mt-2">{{ $users->whereNull('role')->count() }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terdaftar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-navy-600 to-gold-500 flex items-center justify-center text-white font-semibold">
                                        {{ substr($user->nama, 0, 2) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->nama }}</div>
                                    @if(!$user->canBeDeleted())
                                    <span class="text-xs text-red-600">(Protected)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->nip }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->no_hp ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role)
                                @php
                                    $roleColors = [
                                        'superadmin' => 'bg-purple-100 text-purple-800',
                                        'admin' => 'bg-navy-100 text-navy-800',
                                        'user' => 'bg-gold-100 text-gold-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Tanpa Role
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-navy-600 hover:text-navy-900">
                                    Edit
                                </a>
                                @if($user->canBeDeleted())
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data user
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
