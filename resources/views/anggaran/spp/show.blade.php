@extends('layouts.app')

@section('title', 'Detail SPP')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.spp.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Data SPP
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Detail SPP</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail SPP</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $spp->no_spp }}</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('anggaran.spp.edit', $spp) }}" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('anggaran.spp.index') }}" class="btn btn-outline">
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
        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Status Pembayaran</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ status_badge_class($spp->status) }} mt-1">
                        {{ $spp->status === 'Tagihan Telah SP2D' ? 'Sudah SP2D' : 'Belum SP2D' }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-600 dark:text-blue-400">Nilai Netto</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ format_rupiah($spp->netto) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informasi Dasar -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
                Informasi Dasar
            </h3>

            <div class="space-y-3">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">No SPP</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->no_spp }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Nominatif</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->nominatif ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Tanggal SPP</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($spp->tgl_spp) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Bulan</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($spp->bulan) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Jenis Kegiatan</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->jenis_kegiatan }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Jenis Belanja</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                        {{ $spp->jenis_belanja }}
                    </span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Bagian</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->bagian }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">PIC</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->nama_pic }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">LS/Bendahara</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                        {{ $spp->ls_bendahara }}
                    </span>
                </div>
                @if($spp->staff_ppk)
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Staff PPK</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->staff_ppk }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Kode Anggaran -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
                Kode Anggaran (COA)
            </h3>

            <div class="space-y-3">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">COA</span>
                    <span class="font-mono font-semibold text-navy-600 dark:text-navy-400">{{ $spp->coa }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Kode Kegiatan</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->kode_kegiatan }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">KRO</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->kro }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">RO</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->ro }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Sub Komponen</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->sub_komponen }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">MAK</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->mak }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Uraian SPP -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Uraian SPP</h3>
        <div class="bg-gray-50 dark:bg-navy-800 rounded-lg p-4">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $spp->uraian_spp }}</p>
        </div>
    </div>

    <!-- Dokumen Pendukung -->
    @if($spp->nomor_kontrak || $spp->no_bast || $spp->id_eperjadin || $spp->nomor_surat_tugas || $spp->nomor_undangan)
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
            Dokumen Pendukung
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($spp->nomor_kontrak)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Nomor Kontrak/SPBy</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->nomor_kontrak }}</span>
            </div>
            @endif

            @if($spp->no_bast)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">No BAST/Kuitansi</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->no_bast }}</span>
            </div>
            @endif

            @if($spp->id_eperjadin)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">ID e-Perjadin</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->id_eperjadin }}</span>
            </div>
            @endif

            @if($spp->nomor_surat_tugas)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Nomor Surat Tugas</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->nomor_surat_tugas }}</span>
            </div>
            @endif

            @if($spp->tanggal_st)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Tanggal ST/SK</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($spp->tanggal_st) }}</span>
            </div>
            @endif

            @if($spp->nomor_undangan)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Nomor Undangan</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->nomor_undangan }}</span>
            </div>
            @endif

            @if($spp->tanggal_mulai)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Tanggal Mulai</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($spp->tanggal_mulai) }}</span>
            </div>
            @endif

            @if($spp->tanggal_selesai)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Tanggal Selesai</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($spp->tanggal_selesai) }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Nilai & Pajak -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
            Nilai & Pajak
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-4 rounded-xl border border-blue-200 dark:border-blue-700">
                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Bruto</p>
                <p class="text-xl font-bold text-blue-900 dark:text-blue-300 mt-1">{{ format_rupiah($spp->bruto) }}</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 p-4 rounded-xl border border-red-200 dark:border-red-700">
                <p class="text-sm font-medium text-red-600 dark:text-red-400">PPN</p>
                <p class="text-xl font-bold text-red-900 dark:text-red-300 mt-1">{{ format_rupiah($spp->ppn) }}</p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 p-4 rounded-xl border border-orange-200 dark:border-orange-700">
                <p class="text-sm font-medium text-orange-600 dark:text-orange-400">PPh</p>
                <p class="text-xl font-bold text-orange-900 dark:text-orange-300 mt-1">{{ format_rupiah($spp->pph) }}</p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-4 rounded-xl border border-green-200 dark:border-green-700">
                <p class="text-sm font-medium text-green-600 dark:text-green-400">Netto</p>
                <p class="text-xl font-bold text-green-900 dark:text-green-300 mt-1">{{ format_rupiah($spp->netto) }}</p>
            </div>
        </div>
    </div>

    <!-- Informasi SP2D -->
    @if($spp->status === 'Tagihan Telah SP2D')
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
            Informasi SP2D
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($spp->no_sp2d)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">No SP2D</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $spp->no_sp2d }}</span>
            </div>
            @endif

            @if($spp->tgl_sp2d)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Tanggal SP2D</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($spp->tgl_sp2d) }}</span>
            </div>
            @endif

            @if($spp->tgl_selesai_sp2d)
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Tanggal Selesai SP2D</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatTanggalIndo($spp->tgl_selesai_sp2d) }}</span>
            </div>
            @endif
        </div>

        @if($spp->posisi_uang)
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-navy-700">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Posisi Uang</p>
            <div class="bg-gray-50 dark:bg-navy-800 rounded-lg p-3">
                <p class="text-gray-700 dark:text-gray-300">{{ $spp->posisi_uang }}</p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Metadata -->
    <div class="card">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-navy-700">
            Informasi Sistem
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Dibuat Pada</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ format_datetime($spp->created_at) }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600 dark:text-gray-400">Terakhir Diupdate</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ format_datetime($spp->updated_at) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
