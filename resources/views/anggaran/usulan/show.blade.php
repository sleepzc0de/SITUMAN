@extends('layouts.app')

@section('title', 'Detail Usulan Penarikan Dana')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.usulan.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Usulan Penarikan Dana
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Detail Usulan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Usulan Penarikan Dana</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Diajukan oleh {{ $usulan->user->nama }}
                </p>
            </div>

            <div class="flex gap-2">
                @if($usulan->status === 'pending')
                    <a href="{{ route('anggaran.usulan.edit', $usulan) }}" class="btn btn-secondary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                @endif
                <a href="{{ route('anggaran.usulan.index') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="card">
        <div class="flex items-center justify-between p-4 bg-gradient-to-r {{ $usulan->status === 'approved' ? 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20' : ($usulan->status === 'rejected' ? 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20' : 'from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20') }} rounded-xl border {{ $usulan->status === 'approved' ? 'border-green-200 dark:border-green-700' : ($usulan->status === 'rejected' ? 'border-red-200 dark:border-red-700' : 'border-yellow-200 dark:border-yellow-700') }}">
            <div>
                <p class="text-sm font-medium {{ $usulan->status === 'approved' ? 'text-green-600 dark:text-green-400' : ($usulan->status === 'rejected' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400') }}">
                    Status Usulan
                </p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ status_badge_class($usulan->status) }} mt-1">
                    {{ status_text($usulan->status) }}
                </span>
            </div>
            <div class="text-right">
                <p class="text-sm {{ $usulan->status === 'approved' ? 'text-green-600 dark:text-green-400' : ($usulan->status === 'rejected' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400') }}">
                    Nilai Usulan
                </p>
                <p class="text-2xl font-bold {{ $usulan->status === 'approved' ? 'text-green-900 dark:text-green-300' : ($usulan->status === 'rejected' ? 'text-red-900 dark:text-red-300' : 'text-yellow-900 dark:text-yellow-300') }}">
                    {{ format_rupiah($usulan->nilai_usulan) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Detail Usulan -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
            Detail Usulan
        </h3>

        <div class="space-y-3">
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">RO</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $usulan->ro }} - {{ get_ro_name($usulan->ro) }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Sub Komponen</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $usulan->sub_komponen }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Bulan</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($usulan->bulan) }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Nilai Usulan</span>
                <span class="font-semibold text-green-600 dark:text-green-400 text-lg">{{ format_rupiah($usulan->nilai_usulan) }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Pengusul</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $usulan->user->nama }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Tanggal Pengajuan</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($usulan->created_at) }}</span>
            </div>
        </div>

        @if($usulan->keterangan)
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-navy-700">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan</p>
            <div class="bg-gray-50 dark:bg-navy-800 rounded-lg p-4">
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $usulan->keterangan }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Actions for Admin -->
    @if($usulan->status === 'pending' && has_role(['superadmin', 'admin']))
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tindakan</h3>

        <div class="flex gap-3">
            <form action="{{ route('anggaran.usulan.approve', $usulan) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                        onclick="return confirm('Apakah Anda yakin ingin menyetujui usulan ini?')"
                        class="btn btn-primary w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Setujui Usulan
                </button>
            </form>

            <button type="button"
                    onclick="showRejectModal()"
                    class="btn btn-danger flex-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Tolak Usulan
            </button>
        </div>
    </div>

    <!-- Modal Tolak Usulan -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-navy-800 rounded-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tolak Usulan</h3>
            <form action="{{ route('anggaran.usulan.reject', $usulan) }}" method="POST">
                @csrf
                <div class="input-group">
                    <label class="input-label">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="keterangan" rows="4" class="input-field" placeholder="Masukkan alasan penolakan" required></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Usulan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function showRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
    </script>
    @endpush
    @endif
</div>
@endsection
