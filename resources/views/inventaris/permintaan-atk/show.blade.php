{{-- resources/views/inventaris/permintaan-atk/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Permintaan ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Permintaan ATK', 'url' => route('inventaris.permintaan-atk.index'), 'active' => false],
        ['title' => $permintaanAtk->nomor_permintaan, 'url' => null, 'active' => true]
    ]" />
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="page-title font-mono">{{ $permintaanAtk->nomor_permintaan }}</h1>
            @php
                $badgeMap = ['pending'=>'badge-warning','disetujui'=>'badge-success','ditolak'=>'badge-danger','selesai'=>'badge-info'];
                $labelMap = ['pending'=>'Pending','disetujui'=>'Disetujui','ditolak'=>'Ditolak','selesai'=>'Selesai'];
            @endphp
            <span class="{{ $badgeMap[$permintaanAtk->status] ?? 'badge-gray' }} text-sm px-3 py-1">
                {{ $labelMap[$permintaanAtk->status] ?? $permintaanAtk->status }}
            </span>
        </div>
        <p class="page-subtitle mt-1">
            Diajukan {{ format_tanggal($permintaanAtk->created_at, 'd F Y, H:i') }}
            oleh {{ $permintaanAtk->user->nama ?? '-' }}
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
        @if($permintaanAtk->status == 'pending')
            <a href="{{ route('inventaris.permintaan-atk.edit', $permintaanAtk) }}" class="btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @hasrole('superadmin|admin')
            <button type="button" @click="$refs.approveModal.classList.remove('hidden')" x-data
                    class="btn-success btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Setujui
            </button>
            <button type="button" @click="$refs.rejectModal.classList.remove('hidden')" x-data
                    class="btn-danger btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tolak
            </button>
            @endhasrole
        @endif

        @if($permintaanAtk->status == 'disetujui')
            @hasrole('superadmin|admin')
            <form action="{{ route('inventaris.permintaan-atk.complete', $permintaanAtk) }}"
                  method="POST" onsubmit="return confirm('Tandai permintaan ini sebagai selesai?')">
                @csrf
                <button type="submit" class="btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tandai Selesai
                </button>
            </form>
            @endhasrole
        @endif
    </div>
</div>
@endsection

@section('content')
<div x-data="{
    approveModal: false,
    rejectModal: false
}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Alasan Penolakan (hanya jika ditolak) --}}
            @if($permintaanAtk->alasan_penolakan)
            <div class="alert alert-danger">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-sm">Alasan Penolakan</p>
                    <p class="text-sm mt-0.5">{{ $permintaanAtk->alasan_penolakan }}</p>
                </div>
            </div>
            @endif

            {{-- Informasi Permintaan --}}
            <div class="card">
                <h3 class="section-title mb-4">Informasi Permintaan</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Pegawai Peminta</dt>
                        <dd>
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-full bg-navy-100 dark:bg-navy-700 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-navy-700 dark:text-navy-300">
                                        {{ get_initials($permintaanAtk->pegawai->nama ?? '?') }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $permintaanAtk->pegawai->nama ?? '-' }}
                                    </p>
                                    @if($permintaanAtk->pegawai?->jabatan)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $permintaanAtk->pegawai->jabatan }}</p>
                                    @endif
                                </div>
                            </div>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Dibuat Oleh</dt>
                        <dd>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $permintaanAtk->user->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ format_tanggal($permintaanAtk->created_at, 'd F Y, H:i') }}</p>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tanggal Permintaan</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ format_tanggal($permintaanAtk->tanggal_permintaan, 'd F Y') }}
                        </dd>
                    </div>

                    @if($permintaanAtk->penyetuju)
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            {{ $permintaanAtk->status === 'ditolak' ? 'Ditolak Oleh' : 'Disetujui Oleh' }}
                        </dt>
                        <dd>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $permintaanAtk->penyetuju->nama }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ format_tanggal($permintaanAtk->tanggal_disetujui, 'd F Y, H:i') }}</p>
                        </dd>
                    </div>
                    @endif

                    @if($permintaanAtk->keterangan)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Keterangan</dt>
                        <dd class="text-sm text-gray-700 dark:text-gray-300 p-3 bg-gray-50 dark:bg-navy-800/60 rounded-lg">
                            {{ $permintaanAtk->keterangan }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Daftar Item --}}
            <div class="card !p-0 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700">
                    <h3 class="section-title">Daftar Item ATK</h3>
                    <p class="section-desc">{{ $permintaanAtk->details->count() }} item · {{ $permintaanAtk->details->sum('jumlah') }} unit total</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-10">No</th>
                                <th>Nama ATK</th>
                                <th>Kategori</th>
                                <th class="text-center">Diminta</th>
                                <th class="text-center">Stok Tersedia</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permintaanAtk->details as $i => $detail)
                            <tr>
                                <td class="text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $detail->atk->nama }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $detail->atk->kode_atk }}</p>
                                </td>
                                <td>
                                    <span class="badge-gray text-xs">{{ $detail->atk->kategori->nama ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $detail->jumlah }}</span>
                                    <span class="text-xs text-gray-400 ml-1">{{ $detail->atk->satuan }}</span>
                                </td>
                                <td class="text-center">
                                    @php $stok = $detail->atk->stok_tersedia; $cukup = $stok >= $detail->jumlah; @endphp
                                    @if($stok <= 0)
                                    <span class="badge-danger">Kosong</span>
                                    @elseif($cukup)
                                    <span class="badge-success">{{ $stok }} {{ $detail->atk->satuan }}</span>
                                    @else
                                    <span class="badge-warning">{{ $stok }} {{ $detail->atk->satuan }}</span>
                                    @endif
                                </td>
                                <td class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $detail->keterangan ?: '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Status Timeline --}}
            <div class="card">
                <h3 class="section-title mb-4">Alur Status</h3>
                @php
                    $steps = [
                        ['key' => 'pending',   'label' => 'Diajukan',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['key' => 'disetujui', 'label' => 'Disetujui', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['key' => 'selesai',   'label' => 'Selesai',   'icon' => 'M5 13l4 4L19 7'],
                    ];
                    $statusOrder = ['pending' => 0, 'disetujui' => 1, 'ditolak' => 1, 'selesai' => 2];
                    $currentOrder = $statusOrder[$permintaanAtk->status] ?? 0;
                @endphp
                <div class="space-y-1">
                    @foreach($steps as $si => $step)
                    @php
                        $done = $currentOrder > $si || ($permintaanAtk->status === $step['key']);
                        $active = $permintaanAtk->status === $step['key'] || ($si === 1 && $permintaanAtk->status === 'ditolak');
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $done ? 'bg-navy-600 dark:bg-navy-500' : 'bg-gray-100 dark:bg-navy-800 border-2 border-gray-200 dark:border-navy-700' }}">
                                @if($done)
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                @else
                                <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-navy-600"></div>
                                @endif
                            </div>
                            @if($si < count($steps) - 1)
                            <div class="w-0.5 h-5 {{ $currentOrder > $si ? 'bg-navy-500' : 'bg-gray-200 dark:bg-navy-700' }} my-0.5"></div>
                            @endif
                        </div>
                        <div class="pb-1">
                            <p class="text-sm font-semibold {{ $done ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $step['label'] }}
                                @if($permintaanAtk->status === 'ditolak' && $si === 1)
                                    <span class="badge-danger ml-1.5 text-xs">Ditolak</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="card">
                <h3 class="section-title mb-4">Ringkasan</h3>
                <dl class="space-y-3">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Total Item</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $permintaanAtk->details->count() }} item
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Total Unit</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $permintaanAtk->details->sum('jumlah') }} unit
                        </dd>
                    </div>
                    @if($permintaanAtk->status === 'pending')
                    <div class="divider !my-2"></div>
                    <div class="flex items-start gap-2">
                        @php
                            $stokKurang = $permintaanAtk->details->filter(fn($d) => $d->atk->stok_tersedia < $d->jumlah)->count();
                        @endphp
                        @if($stokKurang > 0)
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            {{ $stokKurang }} item memiliki stok tidak mencukupi
                        </p>
                        @else
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-green-700 dark:text-green-400">Semua stok tersedia</p>
                        @endif
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Quick nav --}}
            <div class="card !p-3">
                <a href="{{ route('inventaris.permintaan-atk.index') }}"
                   class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-navy-600 dark:hover:text-navy-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Permintaan
                </a>
            </div>
        </div>
    </div>

    {{-- Modals (Admin only) --}}
    @hasrole('superadmin|admin')

    {{-- Approve Modal --}}
    <div x-ref="approveModal" x-data
         class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-100 dark:border-navy-700">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Setujui Permintaan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $permintaanAtk->nomor_permintaan }}</p>
                </div>
            </div>

            <div class="alert alert-warning mb-4">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-sm">Menyetujui akan <strong>mengurangi stok ATK</strong> secara otomatis.</p>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">
                Apakah Anda yakin ingin menyetujui permintaan ini?
            </p>

            <div class="flex items-center justify-end gap-3">
                <button type="button"
                        @click="$refs.approveModal.classList.add('hidden')"
                        class="btn-outline btn-sm">Batal</button>
                <form action="{{ route('inventaris.permintaan-atk.approve', $permintaanAtk) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-success btn-sm">Ya, Setujui</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div x-ref="rejectModal" x-data
         class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-100 dark:border-navy-700">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Tolak Permintaan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $permintaanAtk->nomor_permintaan }}</p>
                </div>
            </div>

            <form action="{{ route('inventaris.permintaan-atk.reject', $permintaanAtk) }}" method="POST">
                @csrf
                <div class="input-group mb-5">
                    <label class="input-label">
                        Alasan Penolakan
                        <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <textarea name="alasan_penolakan" rows="4"
                              class="input-field"
                              placeholder="Jelaskan alasan penolakan..."
                              required></textarea>
                    <span class="input-hint">Alasan ini akan ditampilkan kepada pemohon.</span>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="button"
                            @click="$refs.rejectModal.classList.add('hidden')"
                            class="btn-outline btn-sm">Batal</button>
                    <button type="submit" class="btn-danger btn-sm">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>

    @endhasrole
</div>
@endsection
