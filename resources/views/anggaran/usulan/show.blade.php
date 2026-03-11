@extends('layouts.app')

@section('title', 'Detail Usulan Penarikan Dana')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('anggaran.usulan.index') }}" class="breadcrumb-item">Usulan Penarikan Dana</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Detail #{{ substr($usulan->id, 0, 8) }}</span>
    </nav>

    {{-- ===== HERO STATUS CARD ===== --}}
    @php
        $statusConfig = [
            'pending'  => ['from' => 'from-yellow-50', 'to' => 'to-amber-50',   'dark_from' => 'dark:from-yellow-900/10', 'dark_to' => 'dark:to-amber-900/10', 'border' => 'border-yellow-200 dark:border-yellow-700/50', 'text' => 'text-yellow-700 dark:text-yellow-400',  'icon_bg' => 'bg-yellow-100 dark:bg-yellow-900/30'],
            'approved' => ['from' => 'from-emerald-50','to' => 'to-green-50',   'dark_from' => 'dark:from-emerald-900/10','dark_to' => 'dark:to-green-900/10',  'border' => 'border-emerald-200 dark:border-emerald-700/50','text' => 'text-emerald-700 dark:text-emerald-400','icon_bg' => 'bg-emerald-100 dark:bg-emerald-900/30'],
            'rejected' => ['from' => 'from-red-50',    'to' => 'to-rose-50',    'dark_from' => 'dark:from-red-900/10',    'dark_to' => 'dark:to-rose-900/10',   'border' => 'border-red-200 dark:border-red-700/50',        'text' => 'text-red-700 dark:text-red-400',        'icon_bg' => 'bg-red-100 dark:bg-red-900/30'],
        ];
        $sc = $statusConfig[$usulan->status] ?? $statusConfig['pending'];
    @endphp

    <div class="rounded-2xl border {{ $sc['border'] }} bg-gradient-to-r {{ $sc['from'] }} {{ $sc['to'] }} {{ $sc['dark_from'] }} {{ $sc['dark_to'] }} p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl {{ $sc['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                    @if($usulan->status === 'approved')
                    <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @elseif($usulan->status === 'rejected')
                    <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @else
                    <svg class="w-7 h-7 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-medium {{ $sc['text'] }} mb-1">Status Usulan</p>
                    @php $badgeMap = ['pending' => 'badge-pending', 'approved' => 'badge-approved', 'rejected' => 'badge-rejected']; @endphp
                    <span class="text-base font-bold {{ $sc['text'] }}">{{ status_text($usulan->status) }}</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Diajukan {{ formatTanggalIndo($usulan->created_at) }}
                    </p>
                </div>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-xs font-medium {{ $sc['text'] }} mb-1">Nilai Usulan</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">
                    {{ format_rupiah($usulan->nilai_usulan) }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== KOLOM KIRI: Detail + Aksi ===== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Detail Usulan --}}
            <div class="card">
                <div class="section-header">
                    <div>
                        <h3 class="section-title">Detail Usulan</h3>
                        <p class="section-desc">ID: {{ substr($usulan->id, 0, 8) }}...</p>
                    </div>
                    <div class="flex gap-2">
                        @if($usulan->status === 'pending')
                        <a href="{{ route('anggaran.usulan.edit', $usulan) }}" class="btn btn-secondary btn-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        @endif
                        <a href="{{ route('anggaran.usulan.index') }}" class="btn btn-outline btn-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>

                <dl class="divide-y divide-gray-100 dark:divide-navy-700">
                    @php
                        $details = [
                            ['label' => 'RO',             'value' => $usulan->ro . ' – ' . get_ro_name($usulan->ro)],
                            ['label' => 'Sub Komponen',   'value' => $usulan->sub_komponen],
                            ['label' => 'Bulan',          'value' => ucfirst($usulan->bulan)],
                            ['label' => 'Pengusul',       'value' => $usulan->user->nama],
                            ['label' => 'Tanggal Ajuan',  'value' => formatTanggalIndo($usulan->created_at)],
                        ];
                    @endphp
                    @foreach($details as $item)
                    <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4 py-3">
                        <dt class="text-sm text-gray-500 dark:text-gray-400 sm:w-36 flex-shrink-0">{{ $item['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white flex-1">{{ $item['value'] }}</dd>
                    </div>
                    @endforeach

                    <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4 py-3">
                        <dt class="text-sm text-gray-500 dark:text-gray-400 sm:w-36 flex-shrink-0">Nilai Usulan</dt>
                        <dd class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                            {{ format_rupiah($usulan->nilai_usulan) }}
                        </dd>
                    </div>
                </dl>

                @if($usulan->keterangan)
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-navy-700">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Keterangan</p>
                    <div class="bg-gray-50 dark:bg-navy-900/50 rounded-xl p-4 border border-gray-100 dark:border-navy-700">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $usulan->keterangan }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Aksi Admin --}}
            @if($usulan->status === 'pending' && has_role(['superadmin', 'admin']))
            <div class="card" x-data="{ rejectOpen: false }">
                <h3 class="section-title mb-4">⚡ Tindakan Admin</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <form action="{{ route('anggaran.usulan.approve', $usulan) }}" method="POST"
                          x-data @submit.prevent="if(confirm('Setujui usulan senilai {{ format_rupiah($usulan->nilai_usulan) }}?')) $el.submit()">
                        @csrf
                        <button type="submit" class="btn btn-success w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Setujui Usulan
                        </button>
                    </form>

                    <button type="button" @click="rejectOpen = true" class="btn btn-danger w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                        </svg>
                        Tolak Usulan
                    </button>
                </div>

                {{-- Inline Reject Modal --}}
                <div x-show="rejectOpen" x-transition.opacity
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                     style="display:none;">
                    <div @click.away="rejectOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="modal-box w-full max-w-md">
                        <div class="modal-header">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Tolak Usulan</h3>
                            <button @click="rejectOpen = false" class="btn btn-ghost btn-icon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <form action="{{ route('anggaran.usulan.reject', $usulan) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-danger mb-4">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm">Penolakan bersifat final dan tidak dapat diubah kembali.</span>
                                </div>
                                <div class="input-group">
                                    <label class="input-label">Alasan Penolakan <span class="text-red-500">*</span></label>
                                    <textarea name="keterangan" rows="4" class="input-field"
                                              placeholder="Jelaskan alasan penolakan secara detail..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="rejectOpen = false" class="btn btn-outline">Batal</button>
                                <button type="submit" class="btn btn-danger">Tolak Usulan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ===== KOLOM KANAN: Info Anggaran ===== --}}
        <div class="space-y-4">

            {{-- Info anggaran subkomponen --}}
            @if($anggaranSubkomp)
            <div class="card">
                <h4 class="section-title mb-3">📊 Anggaran Sub Komponen</h4>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Program Kegiatan</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-3">
                            {{ $anggaranSubkomp->program_kegiatan }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-gray-100 dark:border-navy-700 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Pagu Anggaran</span>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 tabular-nums">
                                {{ format_rupiah($anggaranSubkomp->pagu_anggaran) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Terpakai</span>
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">
                                {{ format_rupiah($anggaranSubkomp->total_penyerapan) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Sisa Anggaran</span>
                            @php $sisa = sisa_anggaran_info($anggaranSubkomp->pagu_anggaran, $anggaranSubkomp->sisa); @endphp
                            <span class="text-xs font-bold tabular-nums {{ $sisa['class'] }}">
                                {{ $sisa['value'] }}
                                @if($sisa['warning'])<span class="ml-1 text-[10px]">{{ $sisa['warning'] }}</span>@endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Nilai Usulan Ini</span>
                            <span class="text-xs font-bold text-navy-700 dark:text-navy-300 tabular-nums">
                                {{ format_rupiah($usulan->nilai_usulan) }}
                            </span>
                        </div>
                    </div>

                    {{-- Progress penyerapan --}}
                    @php $persen = calculate_percentage($anggaranSubkomp->total_penyerapan, $anggaranSubkomp->pagu_anggaran); @endphp
                    <div class="pt-2">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-xs text-gray-500">Penyerapan</span>
                            <span class="text-xs font-semibold {{ percentage_text_class($persen) }}">
                                {{ format_percentage($persen) }}
                            </span>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="{{ progress_bar_color($persen) }} h-2 rounded-full transition-all duration-500"
                                 style="width: {{ min($persen, 100) }}%"></div>
                        </div>
                    </div>

                    {{-- Porsi usulan dari sisa --}}
                    @if($anggaranSubkomp->sisa > 0)
                    @php $porsi = calculate_percentage($usulan->nilai_usulan, $anggaranSubkomp->sisa); @endphp
                    <div class="pt-2 border-t border-gray-100 dark:border-navy-700">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-xs text-gray-500">Porsi dari Sisa</span>
                            <span class="text-xs font-semibold {{ percentage_text_class(100 - $porsi) }}">
                                {{ format_percentage($porsi) }}
                            </span>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="{{ $porsi > 80 ? 'bg-red-400' : 'bg-navy-400' }} h-2 rounded-full transition-all duration-500"
                                 style="width: {{ min($porsi, 100) }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card">
                <div class="empty-state py-8">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title text-sm">Data Anggaran Tidak Ditemukan</p>
                    <p class="empty-state-desc text-xs">Sub komponen tidak terhubung ke data anggaran</p>
                </div>
            </div>
            @endif

            {{-- Timeline / Riwayat --}}
            <div class="card">
                <h4 class="section-title mb-3">🕒 Timeline</h4>
                <div class="relative pl-5 space-y-4">
                    <div class="absolute left-1.5 top-1 bottom-1 w-px bg-gray-200 dark:bg-navy-700"></div>

                    <div class="relative">
                        <div class="absolute -left-[14px] top-1 w-3 h-3 rounded-full bg-navy-500 ring-2 ring-white dark:ring-navy-800"></div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Usulan Dibuat</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ format_datetime($usulan->created_at) }}</p>
                        <p class="text-xs text-gray-500">oleh {{ $usulan->user->nama }}</p>
                    </div>

                    @if($usulan->updated_at != $usulan->created_at)
                    <div class="relative">
                        <div class="absolute -left-[14px] top-1 w-3 h-3 rounded-full ring-2 ring-white dark:ring-navy-800
                            {{ $usulan->status === 'approved' ? 'bg-emerald-500' : ($usulan->status === 'rejected' ? 'bg-red-500' : 'bg-yellow-500') }}"></div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                            {{ $usulan->status === 'approved' ? 'Disetujui' : ($usulan->status === 'rejected' ? 'Ditolak' : 'Diperbarui') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ format_datetime($usulan->updated_at) }}</p>
                    </div>
                    @endif

                    @if($usulan->status === 'pending')
                    <div class="relative">
                        <div class="absolute -left-[14px] top-1 w-3 h-3 rounded-full bg-gray-300 dark:bg-navy-600 ring-2 ring-white dark:ring-navy-800 animate-pulse"></div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500">Menunggu Keputusan...</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quick link --}}
            <a href="{{ route('anggaran.monitoring.index') }}"
               class="card-hover flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-navy-100 dark:bg-navy-700 flex items-center justify-center flex-shrink-0 group-hover:bg-navy-200 dark:group-hover:bg-navy-600 transition-colors">
                    <svg class="w-5 h-5 text-navy-600 dark:text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Monitoring Anggaran</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Lihat realisasi anggaran keseluruhan</p>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-navy-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
