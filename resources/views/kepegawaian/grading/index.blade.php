@extends('layouts.app')

@section('title', 'Rekomendasi Kenaikan Grading')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-800">Rekomendasi Kenaikan Grading</h1>
            <p class="text-gray-600 mt-1">Analisis dan rekomendasi kenaikan grading pegawai</p>
        </div>
        <div class="flex space-x-2">
            <form method="GET" class="flex space-x-2">
                <select name="tahun" class="input-field" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                        Tahun {{ $y }}
                    </option>
                    @endfor
                </select>
            </form>
            <button class="btn-secondary">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card bg-gradient-to-br from-green-500 to-green-700 text-white">
            <h4 class="text-sm text-green-100">Total Rekomendasi</h4>
            <p class="text-4xl font-bold mt-2">{{ $rekomendasi->count() }}</p>
            <p class="text-sm text-green-100 mt-1">Pegawai eligible</p>
        </div>

        <div class="card bg-gradient-to-br from-navy-500 to-navy-700 text-white">
            <h4 class="text-sm text-navy-200">Kenaikan 1 Grade</h4>
            <p class="text-4xl font-bold mt-2">
                {{ $rekomendasi->filter(fn($p) => ($p->rekomendasi['grading_baru'] - $p->rekomendasi['grading_sekarang']) == 1)->count() }}
            </p>
            <p class="text-sm text-navy-200 mt-1">Pegawai</p>
        </div>

        <div class="card bg-gradient-to-br from-gold-400 to-gold-600 text-white">
            <h4 class="text-sm text-gold-100">Rata-rata Masa Kerja</h4>
            <p class="text-4xl font-bold mt-2">
                {{ $rekomendasi->avg('masa_kerja_tahun') ? round($rekomendasi->avg('masa_kerja_tahun')) : 0 }}
            </p>
            <p class="text-sm text-gold-100 mt-1">Tahun</p>
        </div>
    </div>

    <!-- Rekomendasi Table -->
    <div class="card">
        <h3 class="text-lg font-semibold text-navy-800 mb-4">Daftar Rekomendasi Kenaikan Grading {{ $tahun }}</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade Sekarang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade Baru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masa Kerja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rekomendasi as $index => $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $p->nama }}</div>
                            <div class="text-sm text-gray-500">{{ $p->bagian }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $p->nip }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $p->jabatan }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                Grade {{ $p->rekomendasi['grading_sekarang'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Grade {{ $p->rekomendasi['grading_baru'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $p->masa_kerja_tahun ?? 0 }} tahun
                        </td>
                        <td class="px-6 py-4">
                            <ul class="text-sm text-gray-900 list-disc list-inside">
                                @foreach($p->rekomendasi['alasan'] as $alasan)
                                <li>{{ $alasan }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('kepegawaian.grading.show', $p) }}"
                               class="text-navy-600 hover:text-navy-900">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada rekomendasi kenaikan grading untuk tahun {{ $tahun }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Box -->
    <div class="card bg-navy-50 border border-navy-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-navy-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-navy-800">Kriteria Rekomendasi</h3>
                <div class="mt-2 text-sm text-navy-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Masa kerja minimal 4 tahun</li>
                        <li>Memiliki pendidikan S2/S3 untuk grade tertentu</li>
                        <li>Memiliki jabatan struktural (eselon)</li>
                        <li>Grading maksimal yang dapat dicapai adalah Grade 16</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
