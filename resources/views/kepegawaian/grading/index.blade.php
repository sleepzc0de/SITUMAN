@extends('layouts.app')
@section('title', 'Rekomendasi Kenaikan Grading')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Kenaikan Grading', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1">Rekomendasi Kenaikan Grading</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Analisis dan rekomendasi kenaikan grading pegawai</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <form method="GET">
                <select name="tahun" class="py-2 px-3 text-sm border-2 border-gray-200 dark:border-navy-600 rounded-xl bg-white dark:bg-navy-700 text-gray-900 dark:text-white focus:border-navy-400 transition-all" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
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

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-100 uppercase tracking-wide">Total Rekomendasi</p>
                    <p class="text-4xl font-bold mt-1">{{ $rekomendasi->count() }}</p>
                    <p class="text-xs text-green-100 mt-1">Pegawai eligible</p>
                </div>
                <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-navy-600 to-navy-800 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-navy-200 uppercase tracking-wide">Kenaikan 1 Grade</p>
                    <p class="text-4xl font-bold mt-1">
                        {{ $rekomendasi->filter(fn($p) => ($p->rekomendasi['grading_baru'] - $p->rekomendasi['grading_sekarang']) == 1)->count() }}
                    </p>
                    <p class="text-xs text-navy-200 mt-1">Pegawai</p>
                </div>
                <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gold-500 to-gold-600 rounded-2xl p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gold-100 uppercase tracking-wide">Rata-rata Masa Kerja</p>
                    <p class="text-4xl font-bold mt-1">
                        {{ $rekomendasi->avg('masa_kerja_tahun') ? round($rekomendasi->avg('masa_kerja_tahun')) : 0 }}
                    </p>
                    <p class="text-xs text-gold-100 mt-1">Tahun</p>
                </div>
                <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50/50 dark:bg-navy-750">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Daftar Rekomendasi Kenaikan Grading {{ $tahun }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-navy-750 border-b border-gray-200 dark:border-navy-700">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10">No</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pegawai</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIP</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jabatan</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade Sekarang</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade Baru</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Masa Kerja</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-navy-700">
                    @forelse($rekomendasi as $index => $p)
                    <tr class="hover:bg-navy-50/30 dark:hover:bg-navy-700/30 transition-colors">
                        <td class="px-5 py-4 text-sm text-gray-400 dark:text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-white uppercase">{{ substr($p->nama, 0, 2) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-40">{{ $p->nama }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $p->bagian ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">{{ $p->nip }}</td>
                        <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $p->jabatan ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                G{{ $p->rekomendasi['grading_sekarang'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                    G{{ $p->rekomendasi['grading_baru'] }}
                                </span>
                                @if($p->rekomendasi['grading_baru'] > $p->rekomendasi['grading_sekarang'])
                                <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $p->masa_kerja_tahun ?? 0 }} tahun</td>
                        <td class="px-5 py-4 max-w-xs">
                            <ul class="space-y-0.5">
                                @foreach($p->rekomendasi['alasan'] as $alasan)
                                <li class="text-xs text-gray-600 dark:text-gray-400 flex items-start gap-1.5">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full mt-1 flex-shrink-0"></span>
                                    {{ $alasan }}
                                </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('kepegawaian.grading.show', $p) }}"
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-semibold">Tidak ada rekomendasi untuk tahun {{ $tahun }}</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Coba pilih tahun yang berbeda</p>
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
                <h4 class="text-sm font-semibold text-navy-800 dark:text-navy-200 mb-2">Kriteria Rekomendasi</h4>
                <ul class="space-y-1 text-sm text-navy-700 dark:text-navy-300">
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-navy-400 rounded-full flex-shrink-0"></span>Masa kerja minimal 4 tahun</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-navy-400 rounded-full flex-shrink-0"></span>Memiliki pendidikan S2/S3 untuk grade tertentu</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-navy-400 rounded-full flex-shrink-0"></span>Memiliki jabatan struktural (eselon)</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-navy-400 rounded-full flex-shrink-0"></span>Grading maksimal yang dapat dicapai adalah Grade 16</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
