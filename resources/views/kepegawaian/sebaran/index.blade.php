@extends('layouts.app')

@section('title', 'Sebaran Pegawai')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-navy-800">Sebaran Pegawai</h1>
                <p class="text-gray-600 mt-1">Distribusi pegawai per bagian dan unit kerja</p>
            </div>
            <div class="flex space-x-2">
                <button class="btn-secondary">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export Excel
                </button>
            </div>
        </div>
        <!-- Filter & Search -->
        <div class="card" x-data="{ showFilter: false }">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                <form method="GET" class="flex-1 flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama atau NIP..." class="input-field">
                    </div>

                    <select name="bagian" class="input-field md:w-48">
                        <option value="">Semua Bagian</option>
                        @foreach ($bagianList as $bagian)
                            <option value="{{ $bagian }}" {{ request('bagian') == $bagian ? 'selected' : '' }}>
                                {{ $bagian }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="input-field md:w-40">
                        <option value="">Semua Status</option>
                        <option value="AKTIF" {{ request('status') == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                        <option value="CLTN" {{ request('status') == 'CLTN' ? 'selected' : '' }}>CLTN</option>
                    </select>

                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-gradient-to-br from-navy-500 to-navy-700 text-white">
                <h4 class="text-sm text-navy-200">Sebaran Per Bagian</h4>
                <p class="text-3xl font-bold mt-2">{{ $sebaranStats['per_bagian']->count() }}</p>
                <p class="text-sm text-navy-200 mt-1">Total Bagian</p>
            </div>

            <div class="card bg-gradient-to-br from-gold-400 to-gold-600 text-white">
                <h4 class="text-sm text-gold-100">Sebaran Per Eselon</h4>
                <p class="text-3xl font-bold mt-2">{{ $sebaranStats['per_eselon']->count() }}</p>
                <p class="text-sm text-gold-100 mt-1">Kategori Eselon</p>
            </div>

            <div class="card bg-gradient-to-br from-green-500 to-green-700 text-white">
                <h4 class="text-sm text-green-100">Rasio Gender</h4>
                <div class="flex items-center mt-2 space-x-4">
                    @foreach ($sebaranStats['per_jenis_kelamin'] as $jk)
                        <div>
                            <p class="text-2xl font-bold">{{ $jk->total }}</p>
                            <p class="text-sm text-green-100">{{ $jk->jenis_kelamin }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bagian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jabatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grading</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pegawai as $index => $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $pegawai->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $p->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ $p->email_kemenkeu }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $p->nip }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $p->bagian }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>{{ $p->jabatan }}</div>
                                    @if ($p->eselon)
                                        <span class="text-xs text-gray-500">{{ $p->eselon }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-navy-100 text-navy-800">
                                        Grade {{ $p->grading }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full
                            {{ $p->status == 'AKTIF' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('kepegawaian.sebaran.show', $p) }}"
                                        class="text-navy-600 hover:text-navy-900">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada data pegawai
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pegawai->links() }}
            </div>
        </div>
    </div>
@endsection
