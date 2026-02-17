@extends('layouts.app')
@section('title', 'Kelola Role')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Role</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Atur role dan permission pengguna sistem</p>
        </div>
        <button @click="$dispatch('open-modal', 'create-role')"
            class="inline-flex items-center px-4 py-2 bg-navy-600 text-white text-sm font-medium rounded-xl hover:bg-navy-700 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Role Baru
        </button>
    </div>

    {{-- Role Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($roles as $role)
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-navy-700 hover:shadow-md transition-shadow"
            x-data="{ showPerms: false }">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center
                        @if($role->name === 'superadmin') bg-red-100 dark:bg-red-900/30
                        @elseif($role->name === 'admin') bg-navy-100 dark:bg-navy-700
                        @elseif($role->name === 'eksekutif') bg-purple-100 dark:bg-purple-900/30
                        @elseif($role->name === 'picpegawai') bg-blue-100 dark:bg-blue-900/30
                        @elseif($role->name === 'pickeuangan') bg-green-100 dark:bg-green-900/30
                        @elseif($role->name === 'picinventaris') bg-orange-100 dark:bg-orange-900/30
                        @else bg-gray-100 dark:bg-gray-700
                        @endif">
                        <svg class="w-5 h-5 @if($role->name === 'superadmin') text-red-600
                            @elseif($role->name === 'admin') text-navy-600
                            @elseif($role->name === 'eksekutif') text-purple-600
                            @elseif($role->name === 'picpegawai') text-blue-600
                            @elseif($role->name === 'pickeuangan') text-green-600
                            @elseif($role->name === 'picinventaris') text-orange-600
                            @else text-gray-600 @endif"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-sm">{{ $role->display_name }}</h3>
                        <code class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-navy-700 px-1.5 py-0.5 rounded">{{ $role->name }}</code>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    @if(!$role->isProtected())
                    <button @click="$dispatch('open-modal', 'edit-role-{{ $role->id }}')"
                        class="p-1.5 text-gray-400 hover:text-navy-600 dark:hover:text-navy-400 hover:bg-gray-100 dark:hover:bg-navy-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    @endif
                    @if(!$role->isProtected() && $role->users_count === 0)
                    <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Hapus role ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            @if($role->description)
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $role->description }}</p>
            @endif

            {{-- Stats --}}
            <div class="flex items-center justify-between text-xs mb-3">
                <span class="flex items-center space-x-1 text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" /></svg>
                    <span>{{ $role->users_count }} user</span>
                </span>
                <span class="flex items-center space-x-1 text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    <span>{{ $role->permissions->count() }} permission</span>
                </span>
                <span class="px-2 py-0.5 rounded-full text-xs {{ $role->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500' }}">
                    {{ $role->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            {{-- Permissions Toggle --}}
            <button @click="showPerms = !showPerms"
                class="w-full text-xs text-left text-navy-600 dark:text-navy-400 hover:underline flex items-center space-x-1">
                <span x-text="showPerms ? 'Sembunyikan' : 'Lihat Permissions'"></span>
                <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': showPerms }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>

            <div x-show="showPerms" x-collapse class="mt-2">
                <div class="flex flex-wrap gap-1.5">
                    @forelse($role->permissions->sortBy('module') as $permission)
                    <span class="px-2 py-0.5 text-xs bg-navy-50 dark:bg-navy-700 text-navy-700 dark:text-navy-300 rounded-lg">
                        {{ $permission->display_name }}
                    </span>
                    @empty
                    <span class="text-xs text-gray-400 italic">Tidak ada permission</span>
                    @endforelse
                </div>
            </div>

            @if($role->isProtected())
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700">
                <span class="flex items-center space-x-1 text-xs text-amber-600 dark:text-amber-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    <span>Role sistem (dilindungi)</span>
                </span>
            </div>
            @endif
        </div>

        {{-- Edit Modal per Role --}}
        @if(!$role->isProtected())
        <div x-data="{ open: false }"
            @open-modal.window="if ($event.detail === 'edit-role-{{ $role->id }}') open = true"
            x-show="open"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display: none;">
            <div @click="open = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-navy-800 rounded-2xl shadow-2xl w-full max-w-lg p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Role: {{ $role->display_name }}</h3>
                    <button @click="open = false" class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('roles.update', $role) }}">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Nama Tampilan</label>
                            <input type="text" name="display_name" value="{{ $role->display_name }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-navy-400 focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Deskripsi</label>
                            <textarea name="description" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-navy-400 focus:border-transparent text-sm resize-none">{{ $role->description }}</textarea>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Permissions</label>
                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                @foreach($permissionsGrouped as $module => $perms)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1.5">{{ ucfirst($module) }}</p>
                                    <div class="grid grid-cols-2 gap-1.5">
                                        @foreach($perms as $perm)
                                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-700">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-navy-600 focus:ring-navy-500">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $perm->display_name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-3 mt-5 pt-4 border-t border-gray-100 dark:border-navy-700">
                        <button type="button" @click="open = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-navy-700 rounded-xl hover:bg-gray-200 dark:hover:bg-navy-600 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-navy-600 rounded-xl hover:bg-navy-700 transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    {{-- Create Role Modal --}}
    <div x-data="{ open: false }"
        @open-modal.window="if ($event.detail === 'create-role') open = true"
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div @click="open = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative bg-white dark:bg-navy-800 rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Role Baru</h3>
                <button @click="open = false" class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">
                            Nama Role <span class="text-xs text-gray-400">(slug, contoh: picsarpras)</span>
                        </label>
                        <input type="text" name="name" required placeholder="namarolebaru"
                            pattern="[a-z0-9_]+" title="Hanya huruf kecil, angka, dan underscore"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-navy-400 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Nama Tampilan</label>
                        <input type="text" name="display_name" required placeholder="PIC Sarpras"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-navy-400 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Deskripsi</label>
                        <textarea name="description" rows="2" placeholder="Deskripsi role..."
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-navy-400 focus:border-transparent text-sm resize-none"></textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-2">Permissions Awal</label>
                        <div class="space-y-3 max-h-48 overflow-y-auto border border-gray-100 dark:border-navy-700 rounded-xl p-3">
                            @foreach($permissionsGrouped as $module => $perms)
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">{{ ucfirst($module) }}</p>
                                <div class="grid grid-cols-2 gap-1">
                                    @foreach($perms as $perm)
                                    <label class="flex items-center space-x-2 cursor-pointer p-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-700">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                            class="rounded border-gray-300 text-navy-600 focus:ring-navy-500">
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $perm->display_name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 mt-5 pt-4 border-t border-gray-100 dark:border-navy-700">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-navy-700 rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-navy-600 rounded-xl hover:bg-navy-700 transition-colors">
                        Buat Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Permission Matrix --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50 dark:bg-navy-700/50">
            <h3 class="font-bold text-gray-900 dark:text-white">Matrix Permission per Role</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ringkasan akses modul untuk setiap role</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-700/30">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Modul</th>
                        @foreach($roles as $role)
                        <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap">
                            {{ Str::limit($role->display_name, 10) }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @php
                    $modules = ['dashboard', 'kepegawaian', 'anggaran', 'inventaris', 'users', 'roles'];
                    @endphp
                    @foreach($modules as $module)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/30">
                        <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $module }}</td>
                        @foreach($roles as $role)
                        <td class="px-3 py-3 text-center">
                            @php
                            $hasAccess = $role->permissions->where('module', $module)->count() > 0;
                            @endphp
                            @if($hasAccess)
                            <svg class="w-4 h-4 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
