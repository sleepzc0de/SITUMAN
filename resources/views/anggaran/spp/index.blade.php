@extends('layouts.app')

@section('title', 'Data SPP')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="card">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Data SPP</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola data Surat Perintah Pembayaran</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('anggaran.spp.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah SPP
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-4 rounded-xl border border-blue-200 dark:border-blue-700">
                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Bruto</p>
                <p class="text-xl font-bold text-blue-900 dark:text-blue-300 mt-1">
                    {{ format_rupiah($totalBruto) }}
                </p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-4 rounded-xl border border-green-200 dark:border-green-700">
                <p class="text-sm font-medium text-green-600 dark:text-green-400">Total Netto</p>
                <p class="text-xl font-bold text-green-900 dark:text-green-300 mt-1">
                    {{ format_rupiah($totalNetto) }}
                </p>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 p-4 rounded-xl border border-emerald-200 dark:border-emerald-700">
                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Sudah SP2D</p>
                <p class="text-xl font-bold text-emerald-900 dark:text-emerald-300 mt-1">
                    {{ format_rupiah($totalSP2D) }}
                </p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 p-4 rounded-xl border border-orange-200 dark:border-orange-700">
                <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Belum SP2D</p>
                <p class="text-xl font-bold text-orange-900 dark:text-orange-300 mt-1">
                    {{ format_rupiah($totalBelumSP2D) }}
                </p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="input-group">
                <label class="input-label">Bulan</label>
                <select name="bulan" class="input-field" onchange="this.form.submit()">
                    <option value="all">Semua Bulan</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan }}" {{ request('bulan') == $bulan ? 'selected' : '' }}>
                            {{ ucfirst($bulan) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Status</label>
                <select name="status" class="input-field" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="Tagihan Telah SP2D" {{ request('status') == 'Tagihan Telah SP2D' ? 'selected' : '' }}>Sudah SP2D</option>
                    <option value="Tagihan Belum SP2D" {{ request('status') == 'Tagihan Belum SP2D' ? 'selected' : '' }}>Belum SP2D</option>
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">RO</label>
                <select name="ro" class="input-field" onchange="this.form.submit()">
                    <option value="all">Semua RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}" {{ request('ro') == $ro ? 'selected' : '' }}>
                            {{ $ro }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Cari</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="No SPP, Uraian, PIC..." class="input-field">
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    @if(request()->hasAny(['bulan', 'status', 'ro', 'search']))
                        <a href="{{ route('anggaran.spp.index') }}" class="btn btn-outline">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-navy-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">No SPP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Bulan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Uraian</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">PIC</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">RO</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Netto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-navy-900 divide-y divide-gray-200 dark:divide-navy-700">
                    @forelse($spps as $index => $spp)
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ table_row_number($spps, $index) }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('anggaran.spp.show', $spp) }}"
                                   class="text-navy-600 dark:text-navy-400 hover:text-navy-800 dark:hover:text-navy-300 font-medium">
                                    {{ $spp->no_spp }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ format_tanggal_short($spp->tgl_spp) }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ ucfirst($spp->bulan) }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ truncate_text($spp->uraian_spp, 50) }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $spp->nama_pic }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $spp->ro }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                {{ format_rupiah($spp->netto) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ status_badge_class($spp->status) }}">
                                    {{ $spp->status === 'Tagihan Telah SP2D' ? 'Sudah SP2D' : 'Belum SP2D' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('anggaran.spp.show', $spp) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                       title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('anggaran.spp.edit', $spp) }}"
                                       class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300"
                                       title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('anggaran.spp.destroy', $spp) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data SPP ini?')"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data SPP</p>
                                <p class="mt-1">Silakan tambah data SPP baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($spps->hasPages())
            <div class="mt-6">
                {{ $spps->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
