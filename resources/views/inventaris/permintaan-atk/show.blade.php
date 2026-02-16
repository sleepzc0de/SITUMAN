{{-- resources/views/inventaris/permintaan-atk/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Permintaan ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Permintaan ATK', 'url' => route('inventaris.permintaan-atk.index'), 'active' => false],
        ['title' => 'Detail', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $permintaanAtk->nomor_permintaan }}</h2>
            <div class="flex items-center space-x-3 mt-2">
                @if($permintaanAtk->status == 'pending')
                    <span class="badge-warning">Pending</span>
                @elseif($permintaanAtk->status == 'disetujui')
                    <span class="badge-success">Disetujui</span>
                @elseif($permintaanAtk->status == 'ditolak')
                    <span class="badge-danger">Ditolak</span>
                @else
                    <span class="badge-info">Selesai</span>
                @endif
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ format_tanggal($permintaanAtk->tanggal_permintaan, 'd F Y') }}
                </span>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            @if($permintaanAtk->status == 'pending')
                <a href="{{ route('inventaris.permintaan-atk.edit', $permintaanAtk) }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>

                @hasrole('superadmin|admin')
                    <button type="button"
                        onclick="document.getElementById('approveModal').classList.remove('hidden')"
                        class="btn-secondary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Setujui
                    </button>

                    <button type="button"
                        onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                        class="btn-danger">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tolak
                    </button>
                @endhasrole
            @endif

            @if($permintaanAtk->status == 'disetujui')
                @hasrole('superadmin|admin')
                    <form action="{{ route('inventaris.permintaan-atk.complete', $permintaanAtk) }}"
                        method="POST"
                        onsubmit="return confirm('Tandai permintaan ini sebagai selesai?')"
                        class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Tandai Selesai
                        </button>
                    </form>
                @endhasrole
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Permintaan -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Permintaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Peminta</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $permintaanAtk->pegawai->nama ?? '-' }}
                        </p>
                        @if($permintaanAtk->pegawai)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $permintaanAtk->pegawai->jabatan ?? '-' }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Dibuat Oleh</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $permintaanAtk->user->nama }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ format_tanggal($permintaanAtk->created_at, 'd F Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal Permintaan</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ format_tanggal($permintaanAtk->tanggal_permintaan, 'd F Y') }}
                        </p>
                    </div>
                    @if($permintaanAtk->penyetuju)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $permintaanAtk->status == 'disetujui' ? 'Disetujui' : 'Ditolak' }} Oleh
                            </p>
                            <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                                {{ $permintaanAtk->penyetuju->nama }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ format_tanggal($permintaanAtk->tanggal_disetujui, 'd F Y H:i') }}
                            </p>
                        </div>
                    @endif
                    @if($permintaanAtk->keterangan)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Keterangan</p>
                            <p class="text-base text-gray-900 dark:text-white mt-1">
                                {{ $permintaanAtk->keterangan }}
                            </p>
                        </div>
                    @endif
                    @if($permintaanAtk->alasan_penolakan)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Alasan Penolakan</p>
                            <div class="mt-1 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <p class="text-base text-red-800 dark:text-red-300">
                                    {{ $permintaanAtk->alasan_penolakan }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Daftar Item -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daftar Item ATK</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-navy-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama ATK</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Stok Tersedia</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-navy-700">
                            @foreach($permintaanAtk->details as $index => $detail)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $detail->atk->nama }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $detail->atk->kode_atk }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $detail->atk->kategori->nama }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $detail->jumlah }} {{ $detail->atk->satuan }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($detail->atk->stok_tersedia >= $detail->jumlah)
                                            <span class="badge-success">
                                                {{ $detail->atk->stok_tersedia }} {{ $detail->atk->satuan }}
                                            </span>
                                        @elseif($detail->atk->stok_tersedia > 0)
                                            <span class="badge-warning">
                                                {{ $detail->atk->stok_tersedia }} {{ $detail->atk->satuan }}
                                            </span>
                                        @else
                                            <span class="badge-danger">Kosong</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $detail->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status</h3>
                <div class="space-y-3">
                    <div class="p-4 rounded-xl
                        {{ $permintaanAtk->status == 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}
                        {{ $permintaanAtk->status == 'disetujui' ? 'bg-green-50 dark:bg-green-900/20' : '' }}
                        {{ $permintaanAtk->status == 'ditolak' ? 'bg-red-50 dark:bg-red-900/20' : '' }}
                        {{ $permintaanAtk->status == 'selesai' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">

                        <div class="flex items-center justify-center mb-2">
                            @if($permintaanAtk->status == 'pending')
                                <svg class="w-12 h-12 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($permintaanAtk->status == 'disetujui')
                                <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($permintaanAtk->status == 'ditolak')
                                <svg class="w-12 h-12 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </div>

                        <p class="text-center text-lg font-bold
                            {{ $permintaanAtk->status == 'pending' ? 'text-yellow-800 dark:text-yellow-300' : '' }}
                            {{ $permintaanAtk->status == 'disetujui' ? 'text-green-800 dark:text-green-300' : '' }}
                            {{ $permintaanAtk->status == 'ditolak' ? 'text-red-800 dark:text-red-300' : '' }}
                            {{ $permintaanAtk->status == 'selesai' ? 'text-blue-800 dark:text-blue-300' : '' }}">
                            {{ ucfirst($permintaanAtk->status) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Item</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $permintaanAtk->details->count() }} item
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Jumlah</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $permintaanAtk->details->sum('jumlah') }} unit
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
@hasrole('superadmin|admin')
<div id="approveModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Setujui Permintaan ATK</h3>

        <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-sm text-yellow-800 dark:text-yellow-300">
                <strong>Perhatian:</strong> Menyetujui permintaan akan mengurangi stok ATK secara otomatis.
            </p>
        </div>

        <form action="{{ route('inventaris.permintaan-atk.approve', $permintaanAtk) }}" method="POST">
            @csrf

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Apakah Anda yakin ingin menyetujui permintaan ATK ini?
            </p>

            <div class="flex items-center justify-end space-x-3">
                <button type="button"
                    onclick="document.getElementById('approveModal').classList.add('hidden')"
                    class="btn-outline">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Ya, Setujui
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Tolak Permintaan ATK</h3>

        <form action="{{ route('inventaris.permintaan-atk.reject', $permintaanAtk) }}" method="POST">
            @csrf

            <div class="input-group mb-6">
                <label class="input-label">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="alasan_penolakan" rows="4"
                    class="input-field"
                    placeholder="Jelaskan alasan penolakan..." required></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="button"
                    onclick="document.getElementById('rejectModal').classList.add('hidden')"
                    class="btn-outline">
                    Batal
                </button>
                <button type="submit" class="btn-danger">
                    Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endhasrole
@endsection
