@extends('layouts.app')

@section('title', 'Edit Revisi Anggaran')

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.revisi.index') }}" class="breadcrumb-item">Revisi Anggaran</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Edit</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card text-center py-16">
        <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            Revisi Tidak Dapat Diedit
        </h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm max-w-md mx-auto leading-relaxed">
            Revisi anggaran dikunci setelah disimpan untuk menjaga integritas
            <strong>audit trail</strong>. Setiap perubahan pagu harus dicatat sebagai revisi baru.
        </p>

        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30 rounded-xl text-left max-w-sm mx-auto">
            <p class="text-xs font-semibold text-amber-700 dark:text-amber-400 mb-2 uppercase tracking-wide">
                Informasi Revisi
            </p>
            <dl class="space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Jenis</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $revisi->jenis_revisi }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ formatTanggalIndo($revisi->tanggal_revisi) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Pagu Sesudah</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ format_rupiah($revisi->pagu_sesudah) }}</dd>
                </div>
            </dl>
        </div>

        <div class="flex items-center justify-center gap-3 mt-8">
            <a href="{{ route('anggaran.revisi.show', $revisi) }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Lihat Detail
            </a>
            <a href="{{ route('anggaran.revisi.create') }}" class="btn btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Revisi Baru
            </a>
            <a href="{{ route('anggaran.revisi.index') }}" class="btn btn-ghost">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
