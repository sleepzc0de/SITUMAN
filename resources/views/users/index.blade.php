{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen User')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Administrasi', 'url' => null, 'active' => false],
        ['title' => 'Manajemen User', 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Manajemen User</h1>
        <p class="page-subtitle">Kelola pengguna dan hak akses sistem</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn-primary self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah User
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $statCards = [
            ['label' => 'Total User',    'value' => $users->total(),                           'color' => 'navy',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Superadmin',    'value' => $roleCounts['superadmin']    ?? 0,          'color' => 'red',    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['label' => 'Administrator', 'value' => $roleCounts['admin']         ?? 0,          'color' => 'blue',   'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'PIC & Lainnya', 'value' => ($users->total()) - ($roleCounts['superadmin'] ?? 0) - ($roleCounts['admin'] ?? 0), 'color' => 'green', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ];
        $colorMap = [
            'navy'  => ['bg' => 'bg-navy-100 dark:bg-navy-700',   'icon' => 'text-navy-600 dark:text-navy-300',   'val' => 'text-navy-700 dark:text-white'],
            'red'   => ['bg' => 'bg-red-100 dark:bg-red-900/30',  'icon' => 'text-red-600 dark:text-red-400',    'val' => 'text-red-600 dark:text-red-400'],
            'blue'  => ['bg' => 'bg-blue-100 dark:bg-blue-900/30','icon' => 'text-blue-600 dark:text-blue-400',  'val' => 'text-blue-600 dark:text-blue-400'],
            'green' => ['bg' => 'bg-green-100 dark:bg-green-900/30','icon' => 'text-green-600 dark:text-green-400','val' => 'text-green-600 dark:text-green-400'],
        ];
        @endphp

        @foreach($statCards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        <div class="card flex items-center gap-4">
            <div class="p-3 {{ $c['bg'] }} rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold {{ $c['val'] }} mt-0.5">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="card">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama, email, atau NIP..."
                       class="input-field pl-9">
            </div>

            <select name="role" class="input-field md:w-52">
                <option value="">Semua Role</option>
                @foreach($availableRoles as $value => $label)
                    <option value="{{ $value }}" {{ request('role') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cari
            </button>

            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('users.index') }}" class="btn-outline whitespace-nowrap">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- ── Tabel ── --}}
    <div class="card !p-0 overflow-hidden">
        {{-- Table header --}}
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between gap-3">
            <div>
                <h3 class="section-title">Daftar Pengguna</h3>
                <p class="section-desc">
                    {{ $users->total() }} user ditemukan
                    @if(request()->hasAny(['search', 'role']))
                        <span class="text-navy-500 dark:text-navy-400">
                            (difilter dari seluruh data)
                        </span>
                    @endif
                </p>
            </div>
            {{-- Active filter chips --}}
            @if(request()->hasAny(['search', 'role']))
            <div class="flex flex-wrap gap-1.5">
                @if(request('search'))
                    <span class="badge-info">Cari: "{{ request('search') }}"</span>
                @endif
                @if(request('role'))
                    <span class="badge-info">Role: {{ $availableRoles[request('role')] ?? request('role') }}</span>
                @endif
            </div>
            @endif
        </div>

        <div class="table-wrapper !rounded-none !border-0">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Pengguna</th>
                        <th>NIP</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td class="text-gray-400 text-xs">
                            {{ $users->firstItem() + $index }}
                        </td>

                        {{-- Pengguna --}}
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-navy-500 to-navy-700
                                            flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="text-xs font-bold text-white uppercase">
                                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[180px]">
                                        {{ $user->nama }}
                                    </p>
                                    @if(!$user->canBeDeleted())
                                        <span class="text-xs font-medium text-red-500 dark:text-red-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Protected
                                        </span>
                                    @else
                                        @if($user->id === auth()->id())
                                            <span class="text-xs text-navy-500 dark:text-navy-400">(Anda)</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- NIP --}}
                        <td>
                            <code class="text-xs bg-gray-100 dark:bg-navy-700 text-gray-600 dark:text-gray-300
                                         px-2 py-0.5 rounded font-mono">
                                {{ $user->nip }}
                            </code>
                        </td>

                        {{-- Email --}}
                        <td>
                            <p class="text-sm text-gray-900 dark:text-white truncate max-w-[200px]">
                                {{ $user->email }}
                            </p>
                            @if($user->email_pribadi)
                                <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[200px]">
                                    {{ $user->email_pribadi }}
                                </p>
                            @endif
                        </td>

                        {{-- No HP --}}
                        <td class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $user->no_hp ?? '—' }}
                        </td>

                        {{-- Role --}}
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role_color }}">
                                {{ $user->role_label }}
                            </span>
                        </td>

                        {{-- Terdaftar --}}
                        <td class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        {{-- Aksi --}}
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('users.edit', $user) }}"
                                   class="table-action-edit" title="Edit user">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                @if($user->canBeDeleted() && $user->id !== auth()->id())
                                <button type="button"
                                        x-data="confirmDelete('{{ route('users.destroy', $user) }}', '{{ addslashes($user->nama) }}')"
                                        @click="submit()"
                                        class="table-action-delete" title="Hapus user">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @else
                                <span class="w-7 h-7 flex items-center justify-center text-gray-300 dark:text-gray-600
                                             cursor-not-allowed" title="{{ $user->id === auth()->id() ? 'Tidak bisa hapus akun sendiri' : 'Akun terlindungi' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="empty-state-title">Tidak ada data user</p>
                                <p class="empty-state-desc">
                                    @if(request()->hasAny(['search', 'role']))
                                        Coba ubah filter pencarian Anda
                                    @else
                                        Mulai dengan menambahkan user baru
                                    @endif
                                </p>
                                @if(!request()->hasAny(['search', 'role']))
                                <a href="{{ route('users.create') }}" class="btn-primary btn-sm mt-2">
                                    Tambah User Pertama
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-navy-700">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
