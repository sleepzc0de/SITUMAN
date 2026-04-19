{{-- resources/views/roles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kelola Role')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Administrasi', 'url' => null, 'active' => false],
        ['title' => 'Kelola Role', 'url' => null, 'active' => true],
    ]" />
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Kelola Role</h1>
        <p class="page-subtitle">Atur role dan permission pengguna sistem</p>
    </div>
    {{-- Hanya superadmin yang bisa buat role baru --}}
    @if(auth()->user()->isSuperadmin())
    <button onclick="document.getElementById('createRoleModal').classList.remove('hidden')"
            class="btn-primary self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Role Baru
    </button>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Banner info untuk admin --}}
    @if(!auth()->user()->isSuperadmin())
    <div class="alert-info flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm">
            <p class="font-semibold">Mode Administrator</p>
            <p class="mt-0.5 opacity-90">
                Anda hanya dapat melihat dan mengedit role non-sistem. Role
                <strong>Super Administrator</strong> dan <strong>Administrator</strong>
                hanya dapat dikelola oleh Super Administrator.
            </p>
        </div>
    </div>
    @endif

    {{-- ── Role Cards ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($roles as $role)
        @php
            $roleColorMap = [
                'superadmin'    => ['bg' => 'bg-red-100 dark:bg-red-900/30',      'icon' => 'text-red-600 dark:text-red-400'],
                'admin'         => ['bg' => 'bg-navy-100 dark:bg-navy-700',       'icon' => 'text-navy-600 dark:text-navy-300'],
                'eksekutif'     => ['bg' => 'bg-purple-100 dark:bg-purple-900/30','icon' => 'text-purple-600 dark:text-purple-400'],
                'picpegawai'    => ['bg' => 'bg-blue-100 dark:bg-blue-900/30',    'icon' => 'text-blue-600 dark:text-blue-400'],
                'pickeuangan'   => ['bg' => 'bg-green-100 dark:bg-green-900/30',  'icon' => 'text-green-600 dark:text-green-400'],
                'picinventaris' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30','icon' => 'text-orange-600 dark:text-orange-400'],
                'user'          => ['bg' => 'bg-gray-100 dark:bg-gray-700',       'icon' => 'text-gray-600 dark:text-gray-400'],
            ];
            $rc = $roleColorMap[$role->name] ?? ['bg' => 'bg-gray-100 dark:bg-gray-700', 'icon' => 'text-gray-600'];
            $canEdit   = auth()->user()->isSuperadmin() && !$role->isProtected();
            $canDelete = auth()->user()->isSuperadmin() && !$role->isUndeletable() && $role->users_count === 0;
        @endphp

        <div class="card hover:shadow-md transition-shadow" x-data="{ showPerms: false }">

            {{-- Card Header --}}
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $rc['bg'] }}">
                        <svg class="w-5 h-5 {{ $rc['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-sm">{{ $role->display_name }}</h3>
                        <code class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-navy-700 px-1.5 py-0.5 rounded">
                            {{ $role->name }}
                        </code>
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="flex items-center gap-1">
                    @if($canEdit)
                    <button onclick="document.getElementById('editRoleModal-{{ $role->id }}').classList.remove('hidden')"
                            class="table-action-edit" title="Edit role">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    @endif

                    @if($canDelete)
                    <button type="button"
                            x-data="confirmDelete('{{ route('roles.destroy', $role) }}', '{{ addslashes($role->display_name) }}')"
                            @click="submit()"
                            class="table-action-delete" title="Hapus role">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            @if($role->description)
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $role->description }}</p>
            @endif

            {{-- Stats --}}
            <div class="flex items-center justify-between text-xs mb-3">
                <span class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                    </svg>
                    {{ $role->users_count }} user
                </span>
                <span class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    {{ $role->permissions->count() }} permission
                </span>
                <span class="px-2 py-0.5 rounded-full text-xs
                    {{ $role->is_active
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                    {{ $role->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            {{-- Toggle Permissions --}}
            <button @click="showPerms = !showPerms"
                    class="w-full text-xs text-left text-navy-600 dark:text-navy-400 hover:underline flex items-center gap-1">
                <span x-text="showPerms ? 'Sembunyikan permissions' : 'Lihat permissions'"></span>
                <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': showPerms }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="showPerms" x-collapse class="mt-2">
                <div class="flex flex-wrap gap-1.5">
                    @forelse($role->permissions->sortBy('module') as $permission)
                    <span class="px-2 py-0.5 text-xs bg-navy-50 dark:bg-navy-700
                                 text-navy-700 dark:text-navy-300 rounded-lg">
                        {{ $permission->display_name }}
                    </span>
                    @empty
                    <span class="text-xs text-gray-400 italic">Tidak ada permission</span>
                    @endforelse
                </div>
            </div>

            {{-- Protected badge --}}
            @if($role->isProtected())
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700">
                <span class="flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Role sistem — dilindungi
                </span>
            </div>
            @endif
        </div>

        {{-- Edit Modal — hanya render jika boleh diedit --}}
        @if($canEdit)
        <div id="editRoleModal-{{ $role->id }}"
             class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             @keydown.escape.window="document.getElementById('editRoleModal-{{ $role->id }}').classList.add('hidden')">
            <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-navy-700">
                <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-navy-700">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        Edit Role: {{ $role->display_name }}
                    </h3>
                    <button onclick="document.getElementById('editRoleModal-{{ $role->id }}').classList.add('hidden')"
                            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('roles.update', $role) }}">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

                        <div class="input-group">
                            <label class="input-label">Nama Tampilan <span class="text-red-500">*</span></label>
                            <input type="text" name="display_name"
                                   value="{{ old('display_name', $role->display_name) }}"
                                   class="input-field" required>
                        </div>

                        <div class="input-group">
                            <label class="input-label">Deskripsi</label>
                            <textarea name="description" rows="2" class="input-field resize-none">{{ old('description', $role->description) }}</textarea>
                        </div>

                        <div class="input-group">
                            <label class="input-label mb-2">Permissions</label>
                            <div class="space-y-4 max-h-64 overflow-y-auto border border-gray-200 dark:border-navy-600 rounded-xl p-3">
                                @foreach($permissionsGrouped as $module => $perms)
                                {{-- Admin tidak bisa assign permission users & roles --}}
                                @if(!auth()->user()->isSuperadmin() && in_array($module, ['users', 'roles']))
                                    @continue
                                @endif
                                <div>
                                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                        {{ ucfirst($module) }}
                                    </p>
                                    <div class="grid grid-cols-2 gap-1.5">
                                        @foreach($perms as $perm)
                                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg
                                                      hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                   {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-navy-600 focus:ring-navy-500">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $perm->display_name }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-navy-700">
                        <button type="button"
                                onclick="document.getElementById('editRoleModal-{{ $role->id }}').classList.add('hidden')"
                                class="btn-ghost btn-sm">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary btn-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @endforeach
    </div>

    {{-- ── Create Role Modal — hanya superadmin ── --}}
    @if(auth()->user()->isSuperadmin())
    <div id="createRoleModal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @keydown.escape.window="document.getElementById('createRoleModal').classList.add('hidden')"
         x-data>
        <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-navy-700">
            <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-navy-700">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Tambah Role Baru</h3>
                <button onclick="document.getElementById('createRoleModal').classList.add('hidden')"
                        class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

                    <div class="input-group">
                        <label class="input-label">
                            Nama Role (slug) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required
                               placeholder="contoh: piclain"
                               pattern="[a-z0-9_]+"
                               title="Hanya huruf kecil, angka, dan underscore"
                               class="input-field font-mono">
                        <p class="input-hint">Hanya huruf kecil, angka, dan underscore. Tidak dapat diubah setelah dibuat.</p>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Nama Tampilan <span class="text-red-500">*</span></label>
                        <input type="text" name="display_name" required
                               placeholder="contoh: PIC Lainnya"
                               class="input-field">
                    </div>

                    <div class="input-group">
                        <label class="input-label">Deskripsi</label>
                        <textarea name="description" rows="2"
                                  placeholder="Deskripsi singkat tentang role ini..."
                                  class="input-field resize-none"></textarea>
                    </div>

                    <div class="input-group">
                        <label class="input-label mb-2">Permissions Awal</label>
                        <div class="space-y-4 max-h-48 overflow-y-auto border border-gray-200 dark:border-navy-600 rounded-xl p-3">
                            @foreach($permissionsGrouped as $module => $perms)
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                    {{ ucfirst($module) }}
                                </p>
                                <div class="grid grid-cols-2 gap-1">
                                    @foreach($perms as $perm)
                                    <label class="flex items-center gap-2 cursor-pointer p-1.5 rounded-lg
                                                  hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                               class="rounded border-gray-300 text-navy-600 focus:ring-navy-500">
                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $perm->display_name }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-navy-700">
                    <button type="button"
                            onclick="document.getElementById('createRoleModal').classList.add('hidden')"
                            class="btn-ghost btn-sm">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Role
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ── Permission Matrix ── --}}
    <div class="card !p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50 dark:bg-navy-700/50">
            <h3 class="section-title">Matrix Permission per Role</h3>
            <p class="section-desc">Ringkasan akses modul untuk setiap role</p>
        </div>
        <div class="overflow-x-auto">
            <table class="table min-w-max">
                <thead>
                    <tr>
                        <th class="text-left min-w-[140px]">Modul</th>
                        @foreach($allRoles as $role)
                        <th class="text-center whitespace-nowrap min-w-[100px]">
                            {{ Str::limit($role->display_name, 12) }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(['dashboard','kepegawaian','anggaran','inventaris','users','roles'] as $module)
                    <tr>
                        <td class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $module }}</td>
                        @foreach($allRoles as $role)
                        <td class="text-center">
                            @if($role->permissions->where('module', $module)->count() > 0)
                            <svg class="w-4 h-4 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

    {{-- Quick link ke halaman permissions --}}
    <div class="card-flat flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Detail Permission</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Lihat matrix lengkap distribusi setiap permission ke semua role
            </p>
        </div>
        <a href="{{ route('permissions.index') }}" class="btn-outline btn-sm flex-shrink-0">
            Lihat Detail →
        </a>
    </div>

</div>
@endsection
