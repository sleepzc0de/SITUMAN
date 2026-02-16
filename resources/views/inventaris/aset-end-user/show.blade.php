{{-- resources/views/inventaris/aset-end-user/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Aset')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Aset End User', 'url' => route('inventaris.aset-end-user.index'), 'active' => false],
        ['title' => 'Detail', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $asetEndUser->nama_aset }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kode: {{ $asetEndUser->kode_aset }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('inventaris.aset-end-user.edit', $asetEndUser) }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            @if($asetEndUser->status == 'tersedia')
                <button type="button"
                    onclick="document.getElementById('pinjamModal').classList.remove('hidden')"
                    class="btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Pinjamkan
                </button>
            @elseif($asetEndUser->status == 'dipinjam')
                <button type="button"
                    onclick="document.getElementById('kembalikanModal').classList.remove('hidden')"
                    class="btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    Kembalikan
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Aset -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Aset</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kategori</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $asetEndUser->kategori->nama }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Merek</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $asetEndUser->merek ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tipe/Model</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $asetEndUser->tipe ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nomor Seri</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $asetEndUser->nomor_seri ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal Perolehan</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $asetEndUser->tanggal_perolehan ? format_tanggal($asetEndUser->tanggal_perolehan, 'd F Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nilai Perolehan</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ format_rupiah($asetEndUser->nilai_perolehan) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kondisi</p>
                        <div class="mt-1">
                            @if($asetEndUser->kondisi == 'baik')
                                <span class="badge-success">Baik</span>
                            @elseif($asetEndUser->kondisi == 'rusak ringan')
                                <span class="badge-warning">Rusak Ringan</span>
                            @elseif($asetEndUser->kondisi == 'rusak berat')
                                <span class="badge-danger">Rusak Berat</span>
                            @else
                                <span class="badge badge-dark">Hilang</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <div class="mt-1">
                            @if($asetEndUser->status == 'tersedia')
                                <span class="badge-success">Tersedia</span>
                            @elseif($asetEndUser->status == 'dipinjam')
                                <span class="badge-warning">Dipinjam</span>
                            @elseif($asetEndUser->status == 'diperbaiki')
                                <span class="badge-info">Diperbaiki</span>
                            @else
                                <span class="badge badge-dark">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>
                    @if($asetEndUser->deskripsi)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Deskripsi</p>
                            <p class="text-base text-gray-900 dark:text-white mt-1">
                                {{ $asetEndUser->deskripsi }}
                            </p>
                        </div>
                    @endif
                    @if($asetEndUser->catatan)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Catatan</p>
                            <p class="text-base text-gray-900 dark:text-white mt-1">
                                {{ $asetEndUser->catatan }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pengguna Saat Ini -->
            @if($asetEndUser->pegawai)
                <div class="card">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pengguna Saat Ini</h3>
                    <div class="flex items-center space-x-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
                        <div class="w-12 h-12 bg-gradient-to-br from-navy-500 to-navy-700 dark:from-navy-400 dark:to-navy-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-lg font-bold">
                                {{ strtoupper(substr($asetEndUser->pegawai->nama, 0, 2)) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $asetEndUser->pegawai->nama }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $asetEndUser->pegawai->jabatan ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Sejak: {{ format_tanggal($asetEndUser->tanggal_peminjaman, 'd F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Riwayat Aset -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Aset</h3>
                <div class="space-y-4">
                    @forelse($asetEndUser->riwayat as $riwayat)
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-navy-800 rounded-xl">
                            <div class="flex-shrink-0">
                                @if($riwayat->jenis_aktivitas == 'peminjaman')
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </div>
                                @elseif($riwayat->jenis_aktivitas == 'pengembalian')
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ ucfirst($riwayat->jenis_aktivitas) }}
                                </p>
                                @if($riwayat->pegawai)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $riwayat->pegawai->nama }}
                                    </p>
                                @endif
                                @if($riwayat->keterangan)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $riwayat->keterangan }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ format_tanggal($riwayat->tanggal, 'd F Y') }} oleh {{ $riwayat->user->nama }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">Belum ada riwayat</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status</h3>
                <div class="p-4 rounded-xl
                    {{ $asetEndUser->status == 'tersedia' ? 'bg-green-50 dark:bg-green-900/20' : '' }}
                    {{ $asetEndUser->status == 'dipinjam' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}
                    {{ $asetEndUser->status == 'diperbaiki' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}
                    {{ $asetEndUser->status == 'tidak aktif' ? 'bg-gray-50 dark:bg-gray-800' : '' }}">

                    <div class="flex items-center justify-center mb-2">
                        @if($asetEndUser->status == 'tersedia')
                            <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif($asetEndUser->status == 'dipinjam')
                            <svg class="w-12 h-12 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        @else
                            <svg class="w-12 h-12 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>

                    <p class="text-center text-lg font-bold
                        {{ $asetEndUser->status == 'tersedia' ? 'text-green-800 dark:text-green-300' : '' }}
                        {{ $asetEndUser->status == 'dipinjam' ? 'text-yellow-800 dark:text-yellow-300' : '' }}
                        {{ $asetEndUser->status == 'diperbaiki' ? 'text-blue-800 dark:text-blue-300' : '' }}
                        {{ $asetEndUser->status == 'tidak aktif' ? 'text-gray-800 dark:text-gray-300' : '' }}">
                        {{ ucfirst($asetEndUser->status) }}
                    </p>
                </div>
            </div>

            <!-- Metadata -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Metadata</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Dibuat</p>
                        <p class="text-gray-900 dark:text-white mt-1">
                            {{ format_tanggal($asetEndUser->created_at, 'd F Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Terakhir Update</p>
                        <p class="text-gray-900 dark:text-white mt-1">
                            {{ format_tanggal($asetEndUser->updated_at, 'd F Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pinjam -->
<div id="pinjamModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Pinjamkan Aset</h3>

        <form action="{{ route('inventaris.aset-end-user.pinjam', $asetEndUser) }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div class="input-group">
                    <label class="input-label">Pegawai <span class="text-red-500">*</span></label>
                    <select name="pegawai_id" class="input-field" required>
                        <option value="">Pilih Pegawai</option>
                        @foreach(\App\Models\Pegawai::orderBy('nama')->get() as $pegawai)
                            <option value="{{ $pegawai->id }}">{{ $pegawai->nama }} - {{ $pegawai->nip }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group">
                    <label class="input-label">Tanggal Peminjaman <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_peminjaman" value="{{ date('Y-m-d') }}"
                        class="input-field" required>
                </div>

                <div class="input-group">
                    <label class="input-label">Catatan</label>
                    <textarea name="catatan" rows="3" class="input-field" placeholder="Catatan peminjaman..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6">
                <button type="button"
                    onclick="document.getElementById('pinjamModal').classList.add('hidden')"
                    class="btn-outline">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Pinjamkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kembalikan -->
<div id="kembalikanModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Kembalikan Aset</h3>

        <form action="{{ route('inventaris.aset-end-user.kembalikan', $asetEndUser) }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div class="input-group">
                    <label class="input-label">Kondisi Saat Dikembalikan <span class="text-red-500">*</span></label>
                    <select name="kondisi" class="input-field" required>
                        <option value="baik">Baik</option>
                        <option value="rusak ringan">Rusak Ringan</option>
                        <option value="rusak berat">Rusak Berat</option>
                        <option value="hilang">Hilang</option>
                    </select>
                </div>

                <div class="input-group">
                    <label class="input-label">Catatan</label>
                    <textarea name="catatan" rows="3" class="input-field" placeholder="Catatan pengembalian..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6">
                <button type="button"
                    onclick="document.getElementById('kembalikanModal').classList.add('hidden')"
                    class="btn-outline">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Kembalikan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
