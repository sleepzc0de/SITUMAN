@extends('layouts.app')
@section('title', 'Kelola Permission')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <a href="{{ route('roles.index') }}"
                    class="text-sm text-gray-500 dark:text-gray-400 hover:text-navy-600 dark:hover:text-gold-400 transition-colors flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kelola Role</span>
                </a>
                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Permissions</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Permission</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Daftar semua permission yang tersedia beserta distribusinya per role
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1.5 bg-navy-100 dark:bg-navy-700 text-navy-700 dark:text-navy-300 text-sm font-medium rounded-xl">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                {{ $permissions->flatten()->count() }} Total Permission
            </span>
            <a href="{{ route('roles.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Kelola Role
            </a>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach($permissions as $module => $perms)
        @php
            $moduleColors = [
                'dashboard'   => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-700',
                'kepegawaian' => 'bg-navy-50 dark:bg-navy-700 text-navy-700 dark:text-navy-300 border-navy-200 dark:border-navy-600',
                'anggaran'    => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-700',
                'inventaris'  => 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-700',
                'users'       => 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-700',
                'roles'       => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-700',
            ];
            $colorClass = $moduleColors[$module] ?? 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600';
        @endphp
        <div class="border rounded-xl p-3 text-center {{ $colorClass }}">
            <p class="text-2xl font-bold">{{ $perms->count() }}</p>
            <p class="text-xs font-medium capitalize mt-0.5">{{ $module }}</p>
        </div>
        @endforeach
    </div>

    {{-- Permission Matrix (Role vs Permission) --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-navy-700 bg-gradient-to-r from-navy-50 to-navy-100 dark:from-navy-700 dark:to-navy-600">
            <h3 class="font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                <svg class="w-5 h-5 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                <span>Matrix Permission per Role</span>
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                ✅ = Role memiliki permission ini &nbsp;|&nbsp; ✗ = Role tidak memiliki permission ini
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-max">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-700/50">
                        <th class="sticky left-0 bg-gray-50 dark:bg-navy-700 text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide border-r border-gray-200 dark:border-navy-600 min-w-[220px]">
                            Permission
                        </th>
                        @foreach($roles as $role)
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap min-w-[120px]">
                            <div class="flex flex-col items-center space-y-1">
                                <span>{{ $role->display_name }}</span>
                                <span class="text-xs font-normal text-gray-400 dark:text-gray-500 normal-case">
                                    ({{ $role->users_count ?? $role->users()->count() }} user)
                                </span>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @foreach($permissions as $module => $modulePerms)
                    {{-- Module Header Row --}}
                    <tr class="bg-gradient-to-r
                        @if($module === 'dashboard') from-blue-50 to-blue-50/50 dark:from-blue-900/20 dark:to-blue-900/10
                        @elseif($module === 'kepegawaian') from-navy-50 to-navy-50/50 dark:from-navy-700/50 dark:to-navy-700/30
                        @elseif($module === 'anggaran') from-green-50 to-green-50/50 dark:from-green-900/20 dark:to-green-900/10
                        @elseif($module === 'inventaris') from-orange-50 to-orange-50/50 dark:from-orange-900/20 dark:to-orange-900/10
                        @elseif($module === 'users') from-purple-50 to-purple-50/50 dark:from-purple-900/20 dark:to-purple-900/10
                        @elseif($module === 'roles') from-red-50 to-red-50/50 dark:from-red-900/20 dark:to-red-900/10
                        @else from-gray-50 to-gray-50/50 dark:from-gray-700/50 dark:to-gray-700/30
                        @endif">
                        <td class="sticky left-0 px-5 py-2.5 border-r border-gray-200 dark:border-navy-600
                            @if($module === 'dashboard') bg-blue-50 dark:bg-blue-900/20
                            @elseif($module === 'kepegawaian') bg-navy-50 dark:bg-navy-700/50
                            @elseif($module === 'anggaran') bg-green-50 dark:bg-green-900/20
                            @elseif($module === 'inventaris') bg-orange-50 dark:bg-orange-900/20
                            @elseif($module === 'users') bg-purple-50 dark:bg-purple-900/20
                            @elseif($module === 'roles') bg-red-50 dark:bg-red-900/20
                            @else bg-gray-50 dark:bg-gray-700/50
                            @endif">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full flex-shrink-0
                                    @if($module === 'dashboard') bg-blue-500
                                    @elseif($module === 'kepegawaian') bg-navy-500
                                    @elseif($module === 'anggaran') bg-green-500
                                    @elseif($module === 'inventaris') bg-orange-500
                                    @elseif($module === 'users') bg-purple-500
                                    @elseif($module === 'roles') bg-red-500
                                    @else bg-gray-400
                                    @endif"></span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider capitalize">
                                    Modul: {{ $module }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $modulePerms->count() }})</span>
                            </div>
                        </td>
                        @foreach($roles as $role)
                        <td class="px-4 py-2.5"></td>
                        @endforeach
                    </tr>

                    {{-- Permission Rows --}}
                    @foreach($modulePerms as $permission)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/30 transition-colors">
                        <td class="sticky left-0 bg-white dark:bg-navy-800 hover:bg-gray-50 dark:hover:bg-navy-700/30 px-5 py-3 border-r border-gray-200 dark:border-navy-600">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ $permission->display_name }}
                                </span>
                                <code class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    {{ $permission->name }}
                                </code>
                            </div>
                        </td>
                        @foreach($roles as $role)
                        <td class="px-4 py-3 text-center">
                            @php
                                $hasPermission = $role->permissions->contains('id', $permission->id);
                            @endphp
                            @if($hasPermission)
                                <div class="flex items-center justify-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full">
                                        <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center justify-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-100 dark:bg-navy-700 rounded-full">
                                        <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </span>
                                </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>

                {{-- Footer: Total per Role --}}
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-navy-700/50 border-t-2 border-gray-200 dark:border-navy-600">
                        <td class="sticky left-0 bg-gray-50 dark:bg-navy-700 px-5 py-3 border-r border-gray-200 dark:border-navy-600">
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                                Total Permission
                            </span>
                        </td>
                        @foreach($roles as $role)
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-navy-600 dark:bg-navy-500 text-white text-xs font-bold rounded-full shadow-sm">
                                {{ $role->permissions->count() }}
                            </span>
                        </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Permission List per Module (Cards) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($permissions as $module => $modulePerms)
        @php
            $moduleConfig = [
                'dashboard'   => ['color' => 'blue',   'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                'kepegawaian' => ['color' => 'navy',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                'anggaran'    => ['color' => 'green',  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'inventaris'  => ['color' => 'orange', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                'users'       => ['color' => 'purple', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                'roles'       => ['color' => 'red',    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ];
            $cfg = $moduleConfig[$module] ?? ['color' => 'gray', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'];
            $c = $cfg['color'];
        @endphp

        <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
            {{-- Card Header --}}
            <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between
                @if($c === 'blue') bg-blue-50 dark:bg-blue-900/20
                @elseif($c === 'navy') bg-navy-50 dark:bg-navy-700/50
                @elseif($c === 'green') bg-green-50 dark:bg-green-900/20
                @elseif($c === 'orange') bg-orange-50 dark:bg-orange-900/20
                @elseif($c === 'purple') bg-purple-50 dark:bg-purple-900/20
                @elseif($c === 'red') bg-red-50 dark:bg-red-900/20
                @else bg-gray-50 dark:bg-gray-700/50
                @endif">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center
                        @if($c === 'blue') bg-blue-100 dark:bg-blue-900/40
                        @elseif($c === 'navy') bg-navy-100 dark:bg-navy-600
                        @elseif($c === 'green') bg-green-100 dark:bg-green-900/40
                        @elseif($c === 'orange') bg-orange-100 dark:bg-orange-900/40
                        @elseif($c === 'purple') bg-purple-100 dark:bg-purple-900/40
                        @elseif($c === 'red') bg-red-100 dark:bg-red-900/40
                        @else bg-gray-100 dark:bg-gray-600
                        @endif">
                        <svg class="w-4 h-4
                            @if($c === 'blue') text-blue-600 dark:text-blue-400
                            @elseif($c === 'navy') text-navy-600 dark:text-navy-300
                            @elseif($c === 'green') text-green-600 dark:text-green-400
                            @elseif($c === 'orange') text-orange-600 dark:text-orange-400
                            @elseif($c === 'purple') text-purple-600 dark:text-purple-400
                            @elseif($c === 'red') text-red-600 dark:text-red-400
                            @else text-gray-600 dark:text-gray-400
                            @endif"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white capitalize text-sm">
                            Modul {{ ucfirst($module) }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $modulePerms->count() }} permission</p>
                    </div>
                </div>
                {{-- Role coverage badge --}}
                @php
                    $rolesWithAccess = $roles->filter(fn($r) =>
                        $r->permissions->where('module', $module)->count() > 0
                    )->count();
                @endphp
                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                    @if($c === 'blue') bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400
                    @elseif($c === 'navy') bg-navy-100 dark:bg-navy-600 text-navy-700 dark:text-navy-300
                    @elseif($c === 'green') bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400
                    @elseif($c === 'orange') bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400
                    @elseif($c === 'purple') bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400
                    @elseif($c === 'red') bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400
                    @else bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300
                    @endif">
                    {{ $rolesWithAccess }}/{{ $roles->count() }} role
                </span>
            </div>

            {{-- Permission List --}}
            <div class="divide-y divide-gray-100 dark:divide-navy-700">
                @foreach($modulePerms as $permission)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-navy-700/30 transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                            {{ $permission->display_name }}
                        </p>
                        <code class="text-xs text-gray-400 dark:text-gray-500">{{ $permission->name }}</code>
                    </div>
                    {{-- Role Avatars yang punya permission ini --}}
                    <div class="flex items-center -space-x-1 ml-4 flex-shrink-0">
                        @foreach($roles as $role)
                            @if($role->permissions->contains('id', $permission->id))
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
                                $avatarColor = $avatarColors[$role->name] ?? 'bg-gray-400';
                            @endphp
                            <div class="w-6 h-6 {{ $avatarColor }} rounded-full border-2 border-white dark:border-navy-800 flex items-center justify-center"
                                title="{{ $role->display_name }}">
                                <span class="text-white text-xs font-bold">
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

    {{-- Info Box --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-blue-800 dark:text-blue-300">Informasi Penting</p>
                <ul class="mt-1 text-sm text-blue-700 dark:text-blue-400 space-y-1 list-disc list-inside">
                    <li>Permission hanya dapat diubah melalui halaman <strong>Kelola Role</strong> dengan mengedit masing-masing role.</li>
                    <li>Permission pada role <code class="bg-blue-100 dark:bg-blue-900/40 px-1 rounded">superadmin</code> dan <code class="bg-blue-100 dark:bg-blue-900/40 px-1 rounded">admin</code> mencakup seluruh akses sistem.</li>
                    <li>Untuk menambah permission baru, tambahkan melalui seeder atau migrasi, kemudian assign ke role yang sesuai.</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
