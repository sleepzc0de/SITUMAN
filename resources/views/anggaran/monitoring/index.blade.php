@extends('layouts.app')

@section('title', 'Monitoring Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Header & Filters -->
    <div class="card">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Monitoring Anggaran</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pantau realisasi dan sisa anggaran per RO</p>
            </div>

            <div class="flex gap-2">
                <button onclick="window.print()" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak
                </button>
                <button onclick="exportToExcel('table-anggaran', 'monitoring-anggaran')" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </button>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="input-group">
                <label class="input-label">RO</label>
                <select name="ro" class="input-field" onchange="this.form.submit()">
                    <option value="all">Semua RO</option>
                    @foreach($roList as $roCode => $roName)
                        <option value="{{ $roCode }}" {{ $ro == $roCode ? 'selected' : '' }}>
                            {{ $roCode }} - {{ $roName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Sub Komponen</label>
                <select name="subkomponen" class="input-field" onchange="this.form.submit()">
                    <option value="all">Semua Sub Komponen</option>
                    @foreach($subkomponenList as $subCode => $subName)
                        <option value="{{ $subCode }}" {{ $subkomponen == $subCode ? 'selected' : '' }}>
                            {{ $subCode }} - {{ $subName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('anggaran.monitoring.index') }}" class="btn btn-outline w-full">
                    Reset Filter
                </a>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-4 rounded-xl border border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Pagu</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-300 mt-1">
                            {{ formatRupiah($totalPagu) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-4 rounded-xl border border-green-200 dark:border-green-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Total Realisasi</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-300 mt-1">
                            {{ formatRupiah($totalRealisasi) }}
                        </p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                            {{ $totalPagu > 0 ? number_format(($totalRealisasi / $totalPagu) * 100, 2) : 0 }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 p-4 rounded-xl border border-orange-200 dark:border-orange-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Tagihan Outstanding</p>
                        <p class="text-2xl font-bold text-orange-900 dark:text-orange-300 mt-1">
                            {{ formatRupiah($totalOutstanding) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-4 rounded-xl border border-purple-200 dark:border-purple-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Sisa Anggaran</p>
                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-300 mt-1">
                            {{ formatRupiah($totalSisa) }}
                        </p>
                        <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                            {{ $totalPagu > 0 ? number_format(($totalSisa / $totalPagu) * 100, 2) : 0 }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table per RO -->
    @foreach($groupedData as $roCode => $roData)
        <div class="card">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    RO {{ $roCode }} - {{ $roList[$roCode] ?? $roCode }}
                </h3>
                <div class="flex items-center gap-4 mt-2 text-sm">
                    <span class="text-gray-600 dark:text-gray-400">
                        Pagu: <span class="font-semibold text-gray-900 dark:text-white">{{ formatRupiah($roData->sum('pagu_anggaran')) }}</span>
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">
                        Realisasi: <span class="font-semibold text-green-600">{{ formatRupiah($roData->sum('total_penyerapan')) }}</span>
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">
                        Sisa: <span class="font-semibold text-purple-600">{{ formatRupiah($roData->sum('sisa')) }}</span>
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="table-anggaran-{{ $roCode }}">
                    <thead class="bg-gray-50 dark:bg-navy-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Sub Komponen / Akun</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Pagu Anggaran</th>
                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $bulan)
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ $bulan }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Outstanding</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total Realisasi</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Sisa</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-900 divide-y divide-gray-200 dark:divide-navy-700">
                        @foreach($roData as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-navy-800 transition-colors {{ $item->kode_akun ? '' : 'bg-blue-50 dark:bg-blue-900/20 font-semibold' }}">
                                <td class="px-4 py-3 text-gray-900 dark:text-white {{ $item->kode_akun ? 'pl-8' : '' }}">
                                    {{ $item->kode_akun ? $item->kode_akun . ' - ' : '' }}{{ Str::limit($item->program_kegiatan, 50) }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                    {{ formatRupiah($item->pagu_anggaran) }}
                                </td>
                                @foreach(['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'] as $bulan)
                                    <td class="px-4 py-3 text-right {{ $item->$bulan > 0 ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-500 dark:text-gray-500' }}">
                                        {{ $item->$bulan > 0 ? formatRupiah($item->$bulan) : '-' }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-right {{ $item->tagihan_outstanding > 0 ? 'text-orange-600 dark:text-orange-400 font-medium' : 'text-gray-500 dark:text-gray-500' }}">
                                    {{ $item->tagihan_outstanding > 0 ? formatRupiah($item->tagihan_outstanding) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-green-600 dark:text-green-400">
                                    {{ formatRupiah($item->total_penyerapan) }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-purple-600 dark:text-purple-400">
                                    {{ formatRupiah($item->sisa) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->persentase_penyerapan >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($item->persentase_penyerapan >= 50 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                        {{ number_format($item->persentase_penyerapan, 2) }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
function formatRupiah(angka) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}
</script>
@endpush
@endsection
