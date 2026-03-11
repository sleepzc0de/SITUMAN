@extends('layouts.app')

@section('title', 'Data SPP')

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Data SPP</span>
</nav>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <h1 class="page-title">Data SPP</h1>
        <p class="page-subtitle">Kelola Surat Perintah Pembayaran dan pantau status tagihan</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('anggaran.monitoring.index') }}" class="btn btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Monitoring
        </a>
        <a href="{{ route('anggaran.spp.create') }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah SPP
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card border-l-4 border-blue-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Total Bruto</p>
                    <p class="stat-card-value text-xl">{{ format_rupiah_short($totalBruto) }}</p>
                    <p class="stat-card-sub text-gray-400">{{ format_rupiah($totalBruto) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-navy-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Total Netto</p>
                    <p class="stat-card-value text-xl">{{ format_rupiah_short($totalNetto) }}</p>
                    <p class="stat-card-sub text-gray-400">{{ format_rupiah($totalNetto) }}</p>
                </div>
                <div class="w-10 h-10 bg-navy-100 dark:bg-navy-700 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-emerald-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Sudah SP2D</p>
                    <p class="stat-card-value text-xl text-emerald-600 dark:text-emerald-400">{{ format_rupiah_short($totalSP2D) }}</p>
                    @if($totalNetto > 0)
                    <p class="stat-card-sub text-emerald-500">{{ formatPersen($totalSP2D, $totalNetto) }}</p>
                    @endif
                </div>
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-orange-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Belum SP2D</p>
                    <p class="stat-card-value text-xl text-orange-600 dark:text-orange-400">{{ format_rupiah_short($totalBelumSP2D) }}</p>
                    @if($totalNetto > 0)
                    <p class="stat-card-sub text-orange-500">{{ formatPersen($totalBelumSP2D, $totalNetto) }}</p>
                    @endif
                </div>
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress bar SP2D --}}
    @if($totalNetto > 0)
    <div class="card-flat">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Progress SP2D</p>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatPersen($totalSP2D, $totalNetto) }}</p>
        </div>
        <div class="progress-bar-wrap">
            <div class="{{ progress_bar_color(($totalSP2D / $totalNetto) * 100) }} h-2 rounded-full transition-all duration-700"
                 style="width: {{ min(100, ($totalSP2D / $totalNetto) * 100) }}%"></div>
        </div>
        <div class="flex justify-between mt-1.5 text-xs text-gray-400">
            <span>SP2D: {{ format_rupiah_short($totalSP2D) }}</span>
            <span>Outstanding: {{ format_rupiah_short($totalBelumSP2D) }}</span>
        </div>
    </div>
    @endif

    {{-- ===== FILTER ===== --}}
    <div class="card-flat">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="input-group">
                <label class="input-label">Bulan</label>
                <select name="bulan" class="input-field" data-auto-submit>
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
                <select name="status" class="input-field" data-auto-submit>
                    <option value="all">Semua Status</option>
                    <option value="Tagihan Telah SP2D" {{ request('status') == 'Tagihan Telah SP2D' ? 'selected' : '' }}>Sudah SP2D</option>
                    <option value="Tagihan Belum SP2D" {{ request('status') == 'Tagihan Belum SP2D' ? 'selected' : '' }}>Belum SP2D</option>
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">RO</label>
                <select name="ro" class="input-field" data-auto-submit>
                    <option value="all">Semua RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}" {{ request('ro') == $ro ? 'selected' : '' }}>
                            {{ $ro }} – {{ get_ro_name($ro) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label class="input-label">Cari</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="No SPP, Uraian, PIC…"
                           class="input-field">
                    <button type="submit" class="btn btn-primary btn-icon" title="Cari">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    @if(request()->hasAny(['bulan', 'status', 'ro', 'search']))
                        <a href="{{ route('anggaran.spp.index') }}" class="btn btn-ghost btn-icon" title="Reset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="card-flat overflow-hidden">
        @if(request()->hasAny(['bulan', 'status', 'ro', 'search']))
        <div class="px-5 py-2 bg-navy-50 dark:bg-navy-700/40 border-b border-gray-100 dark:border-navy-700 text-xs text-navy-600 dark:text-navy-300 flex items-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter aktif — menampilkan <strong>{{ $spps->total() }}</strong> data
        </div>
        @endif

        <div class="table-wrapper rounded-none border-0">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-10">#</th>
                        <th>No SPP</th>
                        <th>Tanggal</th>
                        <th>Bulan</th>
                        <th>Uraian</th>
                        <th>PIC</th>
                        <th>RO</th>
                        <th class="text-right">Netto</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spps as $index => $spp)
                    <tr>
                        <td class="text-gray-400 text-xs">{{ table_row_number($spps, $index) }}</td>
                        <td>
                            <a href="{{ route('anggaran.spp.show', $spp) }}"
                               class="font-semibold text-navy-600 dark:text-navy-400 hover:text-navy-800 dark:hover:text-navy-200 hover:underline">
                                {{ $spp->no_spp }}
                            </a>
                            @if($spp->jenis_belanja)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $spp->jenis_belanja }}</p>
                            @endif
                        </td>
                        <td class="text-gray-600 dark:text-gray-400 text-sm">{{ format_tanggal_short($spp->tgl_spp) }}</td>
                        <td>
                            <span class="badge badge-gray">{{ ucfirst($spp->bulan) }}</span>
                        </td>
                        <td class="max-w-xs">
                            <p class="text-gray-900 dark:text-white text-sm line-clamp-2" title="{{ $spp->uraian_spp }}">
                                {{ truncate_text($spp->uraian_spp, 60) }}
                            </p>
                        </td>
                        <td class="text-sm text-gray-600 dark:text-gray-400">{{ $spp->nama_pic }}</td>
                        <td>
                            <span class="badge badge-info">{{ $spp->ro }}</span>
                        </td>
                        <td class="text-right font-semibold text-gray-900 dark:text-white text-sm whitespace-nowrap">
                            {{ format_rupiah($spp->netto) }}
                        </td>
                        <td>
                            @if($spp->status === 'Tagihan Telah SP2D')
                                <span class="badge badge-success">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    SP2D
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Outstanding
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('anggaran.spp.show', $spp) }}"
                                   class="table-action-view" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('anggaran.spp.edit', $spp) }}"
                                   class="table-action-edit" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button x-data="confirmDelete('{{ route('anggaran.spp.destroy', $spp) }}', 'SPP {{ $spp->no_spp }}')"
                                        @click="submit()"
                                        class="table-action-delete" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="empty-state-title">Tidak ada data SPP</p>
                                <p class="empty-state-desc">
                                    @if(request()->hasAny(['bulan','status','ro','search']))
                                        Tidak ada data yang cocok dengan filter. <a href="{{ route('anggaran.spp.index') }}" class="text-navy-600 hover:underline">Reset filter</a>
                                    @else
                                        Belum ada data SPP. Klik <strong>Tambah SPP</strong> untuk memulai.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($spps->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-navy-700">
            {{ $spps->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
