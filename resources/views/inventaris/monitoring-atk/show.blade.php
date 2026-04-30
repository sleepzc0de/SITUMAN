{{-- resources/views/inventaris/monitoring-atk/show.blade.php --}}
@extends('layouts.app')

@section('title', $monitoringAtk->nama)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Monitoring ATK', 'url' => route('inventaris.monitoring-atk.index'), 'active' => false],
        ['title' => $monitoringAtk->nama, 'url' => null, 'active' => true],
    ]" />
@endsection

@section('content')
    <div class="space-y-6">

        {{-- ── Header ── --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                <a href="{{ route('inventaris.monitoring-atk.index') }}"
                    class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors mt-1 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="page-title">{{ $monitoringAtk->nama }}</h1>
                        @if ($monitoringAtk->status === 'tersedia')
                            <span class="badge-success">Tersedia</span>
                        @elseif($monitoringAtk->status === 'menipis')
                            <span class="badge-warning">⚠ Menipis</span>
                        @else
                            <span class="badge-danger">✕ Kosong</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <code
                            class="text-xs bg-navy-50 dark:bg-navy-700 text-navy-700 dark:text-navy-300 px-2 py-0.5 rounded font-mono">
                            {{ $monitoringAtk->kode_atk }}
                        </code>
                        <span class="badge-gray">{{ $monitoringAtk->kategori->nama }}</span>
                        <span class="text-xs text-gray-400">Diperbarui
                            {{ $monitoringAtk->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap flex-shrink-0">
                <button type="button" onclick="document.getElementById('updateStokModal').classList.remove('hidden')"
                    class="btn-secondary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Update Stok
                </button>
                <a href="{{ route('inventaris.monitoring-atk.edit', $monitoringAtk) }}" class="btn-outline btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Kolom Utama ── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Info Umum --}}
                <div class="card">
                    <h3 class="section-title mb-4">Informasi Umum</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $monitoringAtk->kategori->nama }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Satuan</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($monitoringAtk->satuan) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Harga Satuan
                            </p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ format_rupiah($monitoringAtk->harga_satuan) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Dibuat</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ format_tanggal($monitoringAtk->created_at, 'd M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Diperbarui</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ format_tanggal($monitoringAtk->updated_at, 'd M Y H:i') }}</p>
                        </div>
                        @if ($monitoringAtk->deskripsi)
                            <div class="col-span-2 sm:col-span-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Deskripsi
                                </p>
                                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                                    {{ $monitoringAtk->deskripsi }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Riwayat Permintaan --}}
                <div class="card !p-0 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700">
                        <h3 class="section-title">Riwayat Permintaan</h3>
                        <p class="section-desc">5 permintaan terbaru untuk item ini</p>
                    </div>
                    <div class="table-wrapper !rounded-none !border-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. Permintaan</th>
                                    <th>Peminta</th>
                                    <th class="text-right">Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monitoringAtk->permintaanDetail->take(5) as $detail)
                                    <tr>
                                        <td class="text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                            {{ format_tanggal($detail->permintaan->tanggal_permintaan, 'd M Y') }}
                                        </td>
                                        <td>
                                            <code
                                                class="text-xs bg-gray-100 dark:bg-navy-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded font-mono">
                                                {{ $detail->permintaan->nomor_permintaan }}
                                            </code>
                                        </td>
                                        <td class="text-gray-900 dark:text-white">
                                            {{ $detail->permintaan->pegawai->nama ?? ($detail->permintaan->user->nama ?? '-') }}
                                        </td>
                                        <td class="text-right font-semibold text-gray-900 dark:text-white tabular-nums">
                                            {{ number_format($detail->jumlah) }}
                                            <span
                                                class="font-normal text-gray-400 text-xs">{{ $monitoringAtk->satuan }}</span>
                                        </td>
                                        <td>
                                            @php $st = $detail->permintaan->status @endphp
                                            @if ($st === 'pending')
                                                <span class="badge-warning">Pending</span>
                                            @elseif($st === 'disetujui')
                                                <span class="badge-success">Disetujui</span>
                                            @elseif($st === 'ditolak')
                                                <span class="badge-danger">Ditolak</span>
                                            @else
                                                <span class="badge-info">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state !py-10">
                                                <div class="empty-state-icon">
                                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                </div>
                                                <p class="empty-state-title">Belum ada permintaan</p>
                                                <p class="empty-state-desc">ATK ini belum pernah diminta</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($monitoringAtk->permintaanDetail->count() > 5)
                        <div class="px-5 py-3 border-t border-gray-100 dark:border-navy-700 text-center">
                            <a href="{{ route('inventaris.permintaan-atk.index') }}?atk={{ $monitoringAtk->id }}"
                                class="text-sm text-navy-600 dark:text-navy-400 hover:underline font-medium">
                                Lihat semua {{ $monitoringAtk->permintaanDetail->count() }} permintaan →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Sidebar ── --}}
            <div class="space-y-6">

                {{-- Stok visual --}}
                <div class="card">
                    <h3 class="section-title mb-4">Status Stok</h3>

                    @php
                        $pct =
                            $monitoringAtk->stok_minimum > 0
                                ? min(100, round(($monitoringAtk->stok_tersedia / $monitoringAtk->stok_minimum) * 100))
                                : 100;
                        $barColor =
                            $monitoringAtk->status === 'tersedia'
                                ? 'bg-green-500'
                                : ($monitoringAtk->status === 'menipis'
                                    ? 'bg-yellow-500'
                                    : 'bg-red-500');
                    @endphp

                    <div class="space-y-4">
                        <div class="p-4 bg-navy-50 dark:bg-navy-700/50 rounded-xl text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Stok Tersedia</p>
                            <p class="text-4xl font-bold text-navy-700 dark:text-white">
                                {{ number_format($monitoringAtk->stok_tersedia) }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $monitoringAtk->satuan }}</p>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                <span>0</span>
                                <span>Min: {{ $monitoringAtk->stok_minimum }}</span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="{{ $barColor }} h-full rounded-full transition-all duration-500"
                                    style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 text-right">
                                {{ $pct }}% dari minimum
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-gold-50 dark:bg-gold-900/20 rounded-xl text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Minimum</p>
                                <p class="text-xl font-bold text-gold-700 dark:text-gold-400 mt-0.5">
                                    {{ $monitoringAtk->stok_minimum }}</p>
                                <p class="text-xs text-gray-400">{{ $monitoringAtk->satuan }}</p>
                            </div>
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Nilai Stok</p>
                                <p class="text-sm font-bold text-green-700 dark:text-green-400 mt-0.5">
                                    {{ format_rupiah_short($monitoringAtk->stok_tersedia * $monitoringAtk->harga_satuan) }}
                                </p>
                            </div>
                        </div>

                        <button type="button"
                            onclick="document.getElementById('updateStokModal').classList.remove('hidden')"
                            class="btn-secondary w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Update Stok
                        </button>
                    </div>
                </div>

                {{-- Aksi cepat --}}
                <div class="card space-y-2">
                    <h3 class="section-title mb-3">Aksi</h3>
                    <a href="{{ route('inventaris.monitoring-atk.edit', $monitoringAtk) }}"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-navy-700/60 transition-colors group">
                        <span
                            class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Edit ATK</p>
                            <p class="text-xs text-gray-400">Ubah data &amp; harga</p>
                        </div>
                    </a>
                    <a href="{{ route('inventaris.permintaan-atk.create') }}"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-navy-700/60 transition-colors group">
                        <span
                            class="w-8 h-8 bg-navy-100 dark:bg-navy-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-navy-600 dark:text-navy-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Buat Permintaan</p>
                            <p class="text-xs text-gray-400">Request ATK ini</p>
                        </div>
                    </a>
                    <button type="button" x-data="confirmDelete('{{ route('inventaris.monitoring-atk.destroy', $monitoringAtk) }}', '{{ addslashes($monitoringAtk->nama) }}')" @click="submit()"
                        class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left">
                        <span
                            class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">Hapus ATK</p>
                            <p class="text-xs text-gray-400">Tindakan tidak dapat dibatalkan</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal Update Stok ── --}}
    <div id="updateStokModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-data>
        <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-navy-700 w-full max-w-md"
            @click.away="document.getElementById('updateStokModal').classList.add('hidden')">

            <div class="modal-header">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Update Stok ATK</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $monitoringAtk->nama }}</p>
                </div>
                <button type="button" onclick="document.getElementById('updateStokModal').classList.add('hidden')"
                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('inventaris.monitoring-atk.update-stok', $monitoringAtk) }}" method="POST">
                @csrf
                <div class="modal-body space-y-4">

                    {{-- Stok saat ini info --}}
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-navy-700/50 rounded-xl text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Stok saat ini</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ number_format($monitoringAtk->stok_tersedia) }} {{ $monitoringAtk->satuan }}
                        </span>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Jenis Transaksi <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2" x-data="{ jenis: 'tambah' }">
                            <label class="cursor-pointer">
                                <input type="radio" name="jenis" value="tambah" class="sr-only" x-model="jenis"
                                    checked>
                                <div :class="jenis === 'tambah'
                                    ?
                                    'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' :
                                    'border-gray-200 dark:border-navy-600 text-gray-600 dark:text-gray-400 hover:border-gray-300'"
                                    class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 transition-all duration-150 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Stok
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="jenis" value="kurang" class="sr-only" x-model="jenis">
                                <div :class="jenis === 'kurang'
                                    ?
                                    'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' :
                                    'border-gray-200 dark:border-navy-600 text-gray-600 dark:text-gray-400 hover:border-gray-300'"
                                    class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 transition-all duration-150 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                    Kurangi Stok
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="jumlahStok">
                            Jumlah ({{ $monitoringAtk->satuan }}) <span class="text-red-500">*</span>
                        </label>
                        <input id="jumlahStok" type="number" name="jumlah" class="input-field" min="1"
                            max="999999" required placeholder="Masukkan jumlah…">
                    </div>
                    <div class="input-group">
                        <label class="input-label">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="input-field" maxlength="500"
                            placeholder="Keterangan opsional (misal: penerimaan barang, distribusi ke ruangan…)"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('updateStokModal').classList.add('hidden')"
                        class="btn-ghost">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
