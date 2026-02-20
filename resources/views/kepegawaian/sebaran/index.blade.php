@extends('layouts.app')
@section('title', 'Sebaran Pegawai')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Sebaran Pegawai', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1">Sebaran Pegawai</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Distribusi pegawai per bagian dan unit kerja</p>
        </div>
        <div class="flex-shrink-0">
            <button class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border-2 border-gold-400 text-gold-700 dark:text-gold-400 hover:bg-gold-50 dark:hover:bg-navy-700 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-navy-600 to-navy-800 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-navy-200 uppercase tracking-wide">Sebaran per Bagian</p>
                    <p class="text-4xl font-bold mt-1">{{ $sebaranStats['per_bagian']->count() }}</p>
                    <p class="text-xs text-navy-200 mt-1">Total bagian</p>
                </div>
                <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gold-500 to-gold-600 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gold-100 uppercase tracking-wide">Sebaran per Eselon</p>
                    <p class="text-4xl font-bold mt-1">{{ $sebaranStats['per_eselon']->count() }}</p>
                    <p class="text-xs text-gold-100 mt-1">Kategori eselon</p>
                </div>
                <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-100 uppercase tracking-wide">Rasio Gender</p>
                    <div class="flex items-baseline gap-3 mt-1">
                        @foreach($sebaranStats['per_jenis_kelamin'] as $jk)
                        <div>
                            <span class="text-2xl font-bold">{{ $jk->total }}</span>
                            <p class="text-xs text-green-100">{{ $jk->jenis_kelamin ?? '-' }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 p-5">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-52">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Cari</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau NIP..."
                        class="w-full pl-9 pr-4 py-2 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl focus:border-navy-400 focus:ring-4 focus:ring-navy-100 dark:focus:ring-navy-700/50 bg-white dark:bg-navy-700 text-gray-900 dark:text-white transition-all">
                </div>
            </div>
            <div class="min-w-40">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Bagian</label>
                <select name="bagian" class="w-full py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 focus:ring-4 focus:ring-navy-100 dark:focus:ring-navy-700/50 transition-all">
                    <option value="">Semua Bagian</option>
                    @foreach($bagianList as $bagian)
                    <option value="{{ $bagian }}" {{ request('bagian') == $bagian ? 'selected' : '' }}>{{ $bagian }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-32">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1.5">Status</label>
                <select name="status" class="w-full py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 transition-all">
                    <option value="">Semua Status</option>
                    @foreach(['AKTIF','CLTN','PENSIUN','NON AKTIF'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-navy-700 to-navy-800 text-white text-sm font-medium rounded-xl hover:from-navy-800 hover:to-navy-900 focus:ring-4 focus:ring-navy-200 dark:focus:ring-navy-700 shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search','bagian','status']))
                <a href="{{ route('kepegawaian.sebaran') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border-2 border-gray-200 dark:border-navy-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700 transition-all">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">

        {{-- Result Info --}}
        <div class="px-6 py-3 border-b border-gray-100 dark:border-navy-700 bg-gray-50/50 dark:bg-navy-750 flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan
                <span class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->firstItem() ?? 0 }}</span>–<span class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->total() }}</span> pegawai
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-750 border-b border-gray-200 dark:border-navy-700">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10">No</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pegawai</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIP</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bagian</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jabatan</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grading</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @forelse($pegawai as $index => $p)
                    <tr class="hover:bg-navy-50/30 dark:hover:bg-navy-700/30 transition-colors">
                        <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500">
                            {{ $pegawai->firstItem() + $index }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="text-xs font-bold text-white uppercase">{{ substr($p->nama, 0, 2) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-44">{{ $p->nama }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $p->email_kemenkeu ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">{{ $p->nip }}</td>
                        <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $p->bagian ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-900 dark:text-white">{{ $p->jabatan ?? '—' }}</p>
                            @if($p->eselon)
                            <p class="text-xs text-purple-600 dark:text-purple-400 mt-0.5">{{ $p->eselon }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($p->grading)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-navy-100 dark:bg-navy-700 text-navy-700 dark:text-navy-200">
                                G{{ $p->grading }}
                            </span>
                            @else
                            <span class="text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php
                            $sc = ['AKTIF' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', 'CLTN' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', 'PENSIUN' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400', 'NON AKTIF' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'];
                            $cls = $sc[$p->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $cls }}">
                                {{ $p->status ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('kepegawaian.sebaran.show', $p) }}"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-navy-600 dark:text-navy-400 bg-navy-50 dark:bg-navy-700 hover:bg-navy-100 dark:hover:bg-navy-600 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-navy-700 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-semibold">Tidak ada data pegawai</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Coba ubah filter pencarian</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pegawai->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-navy-700 bg-gray-50/50 dark:bg-navy-750">
            {{ $pegawai->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
