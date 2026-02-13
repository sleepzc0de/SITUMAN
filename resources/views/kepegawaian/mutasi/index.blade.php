@extends('layouts.app')

@section('title', 'Proyeksi Mutasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-800">Proyeksi Mutasi Pegawai</h1>
            <p class="text-gray-600 mt-1">Analisis dan proyeksi mutasi berdasarkan masa jabatan</p>
        </div>
        <div class="flex space-x-2">
            <form method="GET" class="flex space-x-2">
                <select name="tahun" class="input-field" onchange="this.form.submit()">
                    @for($y = date('Y'); $y <= date('Y') + 2; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                        {{ $y }}
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

    <!-- Priority Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card bg-gradient-to-br from-red-500 to-red-700 text-white">
            <h4 class="text-sm text-red-100">Prioritas Tinggi</h4>
            <p class="text-4xl font-bold mt-2">
                {{ $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] >= 5)->count() }}
            </p>
            <p class="text-sm text-red-100 mt-1">Segera dimutasi</p>
        </div>

        <div class="card bg-gradient-to-br from-orange-500 to-orange-700 text-white">
            <h4 class="text-sm text-orange-100">Prioritas Sedang</h4>
            <p class="text-4xl font-bold mt-2">
                {{ $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] >= 3 && $p->analisis_mutasi['prioritas'] < 5)->count() }}
            </p>
            <p class="text-sm text-orange-100 mt-1">Dipertimbangkan</p>
        </div>

        <div class="card bg-gradient-to-br from-yellow-400 to-yellow-600 text-white">
            <h4 class="text-sm text-yellow-100">Prioritas Rendah</h4>
            <p class="text-4xl font-bold mt-2">
                {{ $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] < 3)->count() }}
            </p>
            <p class="text-sm text-yellow-100 mt-1">Dipantau</p>
        </div>

        <div class="card bg-gradient-to-br from-navy-500 to-navy-700 text-white">
            <h4 class="text-sm text-navy-200">Total Proyeksi</h4>
            <p class="text-4xl font-bold mt-2">{{ $proyeksi->count() }}</p>
            <p class="text-sm text-navy-200 mt-1">Pegawai</p>
        </div>
    </div>

    <!-- Proyeksi Table -->
    <div class="card">
        <h3 class="text-lg font-semibold text-navy-800 mb-4">Daftar Proyeksi Mutasi {{ $tahun }}</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bagian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rekomendasi Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($proyeksi as $index => $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $p->nama }}</div>
                            @if($p->usia)
                            <div class="text-sm text-gray-500">{{ $p->usia }} tahun</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $p->nip }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>{{ $p->jabatan }}</div>
                            @if($p->eselon)
                            <span class="text-xs text-gray-500">{{ $p->eselon }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $p->bagian }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $priority = $p->analisis_mutasi['prioritas'];
                                if ($priority >= 5) {
                                    $badgeClass = 'bg-red-100 text-red-800';
                                    $label = 'Tinggi';
                                } elseif ($priority >= 3) {
                                    $badgeClass = 'bg-orange-100 text-orange-800';
                                    $label = 'Sedang';
                                } else {
                                    $badgeClass = 'bg-yellow-100 text-yellow-800';
                                    $label = 'Rendah';
                                }
                            @endphp
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $badgeClass }}">
                                {{ $label }} ({{ $priority }})
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $p->analisis_mutasi['rekomendasi_waktu'] }}
                        </td>
                        <td class="px-6 py-4">
                            <ul class="text-sm text-gray-900 list-disc list-inside">
                                @foreach($p->analisis_mutasi['alasan'] as $alasan)
                                <li>{{ $alasan }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('kepegawaian.mutasi.show', $p) }}"
                               class="text-navy-600 hover:text-navy-900">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada proyeksi mutasi untuk tahun {{ $tahun }}
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
                <h3 class="text-sm font-medium text-navy-800">Kriteria Proyeksi Mutasi</h3>
                <div class="mt-2 text-sm text-navy-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Prioritas Tinggi (5+):</strong> Masa jabatan ≥24 bulan atau mendekati pensiun (≤12 bulan)</li>
                        <li><strong>Prioritas Sedang (3-4):</strong> Masa jabatan 18-24 bulan</li>
                        <li><strong>Prioritas Rendah (<3):</strong> Pejabat struktural dengan masa jabatan normal</li>
                        <li>Waktu mutasi umumnya dilakukan pada bulan April atau Oktober setiap tahun</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
