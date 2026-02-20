@extends('layouts.app')
@section('title', 'Proyeksi Mutasi Pegawai')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Proyeksi Mutasi', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1">Proyeksi Mutasi Pegawai</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Analisis dan proyeksi mutasi berdasarkan masa jabatan</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <form method="GET">
                <select name="tahun" class="py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 transition-all" onchange="this.form.submit()">
                    @for($y = date('Y'); $y <= date('Y') + 2; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            <button class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border-2 border-gold-400 text-gold-700 dark:text-gold-400 hover:bg-gold-50 dark:hover:bg-navy-700 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export
            </button>
        </div>
    </div>

    {{-- Priority Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-red-100 uppercase tracking-wide">Prioritas Tinggi</p>
                    <p class="text-4xl font-bold mt-1">{{ $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] >= 5)->count() }}</p>
                    <p class="text-xs text-red-100 mt-1">Segera dimutasi</p>
                </div>
                <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-orange-100 uppercase tracking-wide">Prioritas Sedang</p>
                    <p class="text-4xl font-bold mt-1">{{ $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] >= 3 && $p->analisis_mutasi['prioritas'] < 5)->count() }}</p>
                    <p class="text-xs text-orange-100 mt-1">Dipertimbangkan</p>
                </div>
                <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-amber-100 uppercase tracking-wide">Prioritas Rendah</p>
                    <p class="text-4xl font-bold mt-1">{{ $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] < 3)->count() }}</p>
                    <p class="text-xs text-amber-100 mt-1">Dipantau</p>
                </div>
                <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-navy-600 to-navy-800 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-navy-200 uppercase tracking-wide">Total Proyeksi</p>
                    <p class="text-4xl font-bold mt-1">{{ $proyeksi->count() }}</p>
                    <p class="text-xs text-navy-200 mt-1">Pegawai</p>
                </div>
                <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50/50 dark:bg-navy-750">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Daftar Proyeksi Mutasi {{ $tahun }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-750 border-b border-gray-200 dark:border-navy-700">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10">No</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pegawai</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIP</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jabatan</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bagian</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioritas</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rekomendasi Waktu</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @forelse($proyeksi as $index => $p)
                    <tr class="hover:bg-navy-50/30 dark:hover:bg-navy-700/30 transition-colors">
                        <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-white uppercase">{{ substr($p->nama, 0, 2) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-40">{{ $p->nama }}</p>
                                    @if($p->usia)<p class="text-xs text-gray-500 dark:text-gray-400">{{ $p->usia }} tahun</p>@endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">{{ $p->nip }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-900 dark:text-white">{{ $p->jabatan ?? '—' }}</p>
                            @if($p->eselon)<p class="text-xs text-purple-600 dark:text-purple-400 mt-0.5">{{ $p->eselon }}</p>@endif
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $p->bagian ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @php
                            $priority = $p->analisis_mutasi['prioritas'];
                            if ($priority >= 5) {
                                $badgeClass = 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400';
                                $label = 'Tinggi';
                            } elseif ($priority >= 3) {
                                $badgeClass = 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400';
                                $label = 'Sedang';
                            } else {
                                $badgeClass = 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400';
                                $label = 'Rendah';
                            }
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $badgeClass }}">
                                {{ $label }} ({{ $priority }})
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            {{ $p->analisis_mutasi['rekomendasi_waktu'] }}
                        </td>
                        <td class="px-5 py-4 max-w-xs">
                            <ul class="space-y-0.5">
                                @foreach($p->analisis_mutasi['alasan'] as $alasan)
                                <li class="text-xs text-gray-600 dark:text-gray-400 flex items-start gap-1.5">
                                    <span class="w-1.5 h-1.5 bg-orange-400 rounded-full mt-1 flex-shrink-0"></span>
                                    {{ $alasan }}
                                </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('kepegawaian.mutasi.show', $p) }}"
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
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-navy-700 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-semibold">Tidak ada proyeksi mutasi untuk tahun {{ $tahun }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-navy-50 dark:bg-navy-800/50 border border-navy-200 dark:border-navy-700 rounded-2xl p-5">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-navy-800 dark:text-navy-200 mb-2">Kriteria Proyeksi Mutasi</h4>
                <ul class="space-y-1 text-sm text-navy-700 dark:text-navy-300">
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 bg-red-400 rounded-full mt-1.5 flex-shrink-0"></span><span><strong>Prioritas Tinggi (5+):</strong> Masa jabatan ≥24 bulan atau mendekati pensiun (≤12 bulan)</span></li>
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 bg-orange-400 rounded-full mt-1.5 flex-shrink-0"></span><span><strong>Prioritas Sedang (3–4):</strong> Masa jabatan 18–24 bulan</span></li>
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 bg-amber-400 rounded-full mt-1.5 flex-shrink-0"></span><span><strong>Prioritas Rendah (&lt;3):</strong> Pejabat struktural dengan masa jabatan normal</span></li>
                    <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 bg-navy-400 rounded-full mt-1.5 flex-shrink-0"></span><span>Mutasi umumnya dilakukan pada bulan <strong>April</strong> atau <strong>Oktober</strong> setiap tahun</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
