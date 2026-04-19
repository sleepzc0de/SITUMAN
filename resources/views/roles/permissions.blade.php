{{-- resources/views/roles/permissions.blade.php --}}
@extends('layouts.app')

@section('title', 'Kelola Permission')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Administrasi', 'url' => null, 'active' => false],
        ['title' => 'Kelola Role', 'url' => route('roles.index'), 'active' => false],
        ['title' => 'Detail Permission', 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Detail Permission</h1>
        <p class="page-subtitle">
            Distribusi {{ $permissions->flatten()->count() }} permission ke seluruh role
        </p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn-outline btn-sm self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Kelola Role
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Summary Stats ── --}}
    @php
    $moduleColors = [
        'dashboard'   => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-700',
        'kepegawaian' => 'bg-navy-50 dark:bg-navy-700 text-navy-700 dark:text-navy-300 border-navy-200 dark:border-navy-600',
        'anggaran'    => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-700',
        'inventaris'  => 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-700',
        'users'       => 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-700',
        'roles'       => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-700',
    ];
    @endphp

    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
        @foreach($permissions as $module => $perms)
        <div class="border rounded-xl p-3 text-center {{ $moduleColors[$module] ?? 'bg-gray-50 border-gray-200 text-gray-700' }}">
            <p class="text-2xl font-bold">{{ $perms->count() }}</p>
            <p class="text-xs font-medium capitalize mt-0.5">{{ $module }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Permission Matrix (Role vs Permission) ── --}}
    <div class="card !p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50 dark:bg-navy-700/50">
            <h3 class="section-title">Matrix Permission × Role</h3>
            <p class="section-desc">
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Role memiliki permission
                </span>
                &nbsp;·&nbsp;
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Tidak memiliki
                </span>
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-max">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-700/50 border-b border-gray-200 dark:border-navy-600">
                        <th class="sticky left-0 bg-gray-50 dark:bg-navy-700 text-left px-5 py-3
                                   text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide
                                   border-r border-gray-200 dark:border-navy-600 min-w-[220px]">
                            Permission
                        </th>
                        @foreach($roles as $role)
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400
                                   uppercase tracking-wide whitespace-nowrap min-w-[110px]">
                            <div class="flex flex-col items-center gap-1">
                                <span>{{ Str::limit($role->display_name, 14) }}</span>
                                <span class="text-xs font-normal text-gray-400 dark:text-gray-500 normal-case">
                                    {{ $role->users_count ?? $role->users()->count() }} user
                                </span>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @foreach($permissions as $module => $modulePerms)

                    {{-- Module separator row --}}
                    <tr class="{{ $moduleColors[$module] ?? 'bg-gray-50' }}">
                        <td class="sticky left-0 px-5 py-2.5 border-r border-gray-200 dark:border-navy-600
                                   {{ $moduleColors[$module] ?? 'bg-gray-50' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full flex-shrink-0
                                    {{ match($module) {
                                        'dashboard'   => 'bg-blue-500',
                                        'kepegawaian' => 'bg-navy-500',
                                        'anggaran'    => 'bg-green-500',
                                        'inventaris'  => 'bg-orange-500',
                                        'users'       => 'bg-purple-500',
                                        'roles'       => 'bg-red-500',
                                        default       => 'bg-gray-400',
                                    } }}"></span>
                                <span class="text-xs font-bold uppercase tracking-wider capitalize">
                                    Modul: {{ $module }}
                                </span>
                                <span class="text-xs opacity-60">({{ $modulePerms->count() }})</span>
                            </div>
                        </td>
                        @foreach($roles as $role)
                        <td class="py-2.5"></td>
                        @endforeach
                    </tr>

                    {{-- Permission rows --}}
                    @foreach($modulePerms as $permission)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/30 transition-colors">
                        <td class="sticky left-0 bg-white dark:bg-navy-800 hover:bg-gray-50 dark:hover:bg-navy-700/30
                                   px-5 py-3 border-r border-gray-200 dark:border-navy-600">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $permission->display_name }}
                            </p>
                            <code class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 block">
                                {{ $permission->name }}
                            </code>
                        </td>
                        @foreach($roles as $role)
                        <td class="px-4 py-3 text-center">
                            @if($role->permissions->contains('id', $permission->id))
                            <span class="inline-flex items-center justify-center w-6 h-6
                                         bg-green-100 dark:bg-green-900/30 rounded-full">
                                <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            @else
                            <span class="inline-flex items-center justify-center w-6 h-6
                                         bg-gray-100 dark:bg-navy-700 rounded-full">
                                <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach

                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-navy-700/50 border-t-2 border-gray-200 dark:border-navy-600">
                        <td class="sticky left-0 bg-gray-50 dark:bg-navy-700 px-5 py-3
                                   border-r border-gray-200 dark:border-navy-600">
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                                Total Permission
                            </span>
                        </td>
                        @foreach($roles as $role)
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8
                                         bg-navy-600 dark:bg-navy-500 text-white text-xs font-bold rounded-full shadow-sm">
                                {{ $role->permissions->count() }}
                            </span>
                        </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── Permission Cards per Module ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @foreach($permissions as $module => $modulePerms)
        @php
            $moduleIcons = [
                'dashboard'   => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'kepegawaian' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'anggaran'    => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'inventaris'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'users'       => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'roles'       => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            ];
            $colorClass = $moduleColors[$module] ?? 'bg-gray-50 border-gray-200 text-gray-700';
            $iconPath   = $moduleIcons[$module] ?? 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z';
            $rolesWithAccess = $roles->filter(fn($r) => $r->permissions->where('module', $module)->count() > 0)->count();
        @endphp

        <div class="card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between {{ $colorClass }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/60 dark:bg-black/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm capitalize">Modul {{ ucfirst($module) }}</h3>
                        <p class="text-xs opacity-70">{{ $modulePerms->count() }} permission</p>
                    </div>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-white/60 dark:bg-black/20">
                    {{ $rolesWithAccess }}/{{ $roles->count() }} role
                </span>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-navy-700">
                @foreach($modulePerms as $permission)
                <div class="px-5 py-3 flex items-center justify-between
                             hover:bg-gray-50 dark:hover:bg-navy-700/30 transition-colors">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                            {{ $permission->display_name }}
                        </p>
                        <code class="text-xs text-gray-400 dark:text-gray-500">{{ $permission->name }}</code>
                    </div>

                    {{-- Avatar role yang punya permission ini --}}
                    @php
                    $avatarColors = [
                        'superadmin'    => 'bg-red-500',
                        'admin'         => 'bg-navy-500',
                        'eksekutif'     => 'bg-purple-500',
                        'picpegawai'    => 'bg-blue-500',
                        'pickeuangan'   => 'bg-green-500',
                        'picinventaris' => 'bg-orange-500',
                        'user'          => 'bg-gray-400',
                    ];
                    @endphp
                    <div class="flex items-center -space-x-1 ml-4 flex-shrink-0">
                        @foreach($roles as $role)
                            @if($role->permissions->contains('id', $permission->id))
                            <div class="w-6 h-6 {{ $avatarColors[$role->name] ?? 'bg-gray-400' }} rounded-full
                                         border-2 border-white dark:border-navy-800
                                         flex items-center justify-center"
                                 title="{{ $role->display_name }}">
                                <span class="text-white text-xs font-bold leading-none">
                                    {{ strtoupper(substr($role->name, 0, 1)) }}
                                </span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    {{-- Info box --}}
    <div class="alert-info flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm">
            <p class="font-semibold">Informasi</p>
            <ul class="mt-1 space-y-1 list-disc list-inside opacity-90">
                <li>Permission hanya dapat diubah melalui halaman <strong>Kelola Role</strong>.</li>
                <li>Role <code class="bg-blue-100 dark:bg-blue-900/40 px-1 rounded">superadmin</code> dan
                    <code class="bg-blue-100 dark:bg-blue-900/40 px-1 rounded">admin</code>
                    hanya dapat dikelola oleh Super Administrator.</li>
                <li>Untuk menambah permission baru, gunakan seeder atau migrasi database.</li>
            </ul>
        </div>
    </div>

</div>
@endsection
