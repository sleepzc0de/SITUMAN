{{-- resources/views/inventaris/monitoring-atk/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Monitoring ATK', 'url' => route('inventaris.monitoring-atk.index'), 'active' => false],
        ['title' => 'Detail ATK', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $monitoringAtk->nama }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kode: {{ $monitoringAtk->kode_atk }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('inventaris.monitoring-atk.edit', $monitoringAtk) }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <button type="button"
                onclick="document.getElementById('updateStokModal').classList.remove('hidden')"
                class="btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Update Stok
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info ATK -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Detail Umum -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Umum</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kategori</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $monitoringAtk->kategori->nama }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Satuan</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ ucfirst($monitoringAtk->satuan) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Harga Satuan</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ format_rupiah($monitoringAtk->harga_satuan) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <div class="mt-1">
                            @if($monitoringAtk->status == 'tersedia')
                                <span class="badge-success">Tersedia</span>
                            @elseif($monitoringAtk->status == 'menipis')
                                <span class="badge-warning">Menipis</span>
                            @else
                                <span class="badge-danger">Kosong</span>
                            @endif
                        </div>
                    </div>
                    @if($monitoringAtk->deskripsi)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Deskripsi</p>
                            <p class="text-base text-gray-900 dark:text-white mt-1">
                                {{ $monitoringAtk->deskripsi }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Riwayat Permintaan -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Permintaan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-navy-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Peminta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-navy-700">
                            @forelse($monitoringAtk->permintaanDetail->take(5) as $detail)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ format_tanggal($detail->permintaan->tanggal_permintaan, 'd/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $detail->permintaan->pegawai->nama ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $detail->jumlah }} {{ $monitoringAtk->satuan }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($detail->permintaan->status == 'pending')
                                            <span class="badge-warning">Pending</span>
                                        @elseif($detail->permintaan->status == 'disetujui')
                                            <span class="badge-success">Disetujui</span>
                                        @elseif($detail->permintaan->status == 'ditolak')
                                            <span class="badge-danger">Ditolak</span>
                                        @else
                                            <span class="badge-info">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada riwayat permintaan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stok Info -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Stok</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-gradient-to-br from-navy-50 to-navy-100 dark:from-navy-800 dark:to-navy-700 rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stok Tersedia</p>
                        <p class="text-3xl font-bold text-navy-700 dark:text-white mt-1">
                            {{ $monitoringAtk->stok_tersedia }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $monitoringAtk->satuan }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-gold-50 to-gold-100 dark:from-gold-900/30 dark:to-gold-800/30 rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stok Minimum</p>
                        <p class="text-3xl font-bold text-gold-700 dark:text-gold-400 mt-1">
                            {{ $monitoringAtk->stok_minimum }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $monitoringAtk->satuan }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Nilai Stok</p>
                        <p class="text-xl font-bold text-green-700 dark:text-green-400 mt-1">
                            {{ format_rupiah($monitoringAtk->stok_tersedia * $monitoringAtk->harga_satuan) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Metadata</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Dibuat</p>
                        <p class="text-gray-900 dark:text-white mt-1">
                            {{ format_tanggal($monitoringAtk->created_at, 'd F Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Terakhir Update</p>
                        <p class="text-gray-900 dark:text-white mt-1">
                            {{ format_tanggal($monitoringAtk->updated_at, 'd F Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Stok -->
<div id="updateStokModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-2xl max-w-md w-full p-6" @click.away="document.getElementById('updateStokModal').classList.add('hidden')">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Update Stok ATK</h3>

        <form action="{{ route('inventaris.monitoring-atk.update-stok', $monitoringAtk) }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div class="input-group">
                    <label class="input-label">Jenis Transaksi</label>
                    <select name="jenis" class="input-field" required>
                        <option value="tambah">Tambah Stok</option>
                        <option value="kurang">Kurangi Stok</option>
                    </select>
                </div>

                <div class="input-group">
                    <label class="input-label">Jumlah ({{ $monitoringAtk->satuan }})</label>
                    <input type="number" name="jumlah" class="input-field" min="1" required>
                </div>

                <div class="input-group">
                    <label class="input-label">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="input-field" placeholder="Opsional"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6">
                <button type="button"
                    onclick="document.getElementById('updateStokModal').classList.add('hidden')"
                    class="btn-outline">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Update Stok
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
