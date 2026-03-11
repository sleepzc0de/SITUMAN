@extends('layouts.app')
@section('title', 'Edit Data Anggaran')
@section('subtitle', 'Perbarui informasi data anggaran')

@section('breadcrumb')
<nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li><a href="{{ route('anggaran.data.index') }}" class="breadcrumb-item">Kelola Data Anggaran</a></li>
        <li><svg class="w-3.5 h-3.5 breadcrumb-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
        <li><a href="{{ route('anggaran.data.show', $data) }}" class="breadcrumb-item">Detail Anggaran</a></li>
        <li><svg class="w-3.5 h-3.5 breadcrumb-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
        <li><span class="breadcrumb-current">Edit Data</span></li>
    </ol>
</nav>
@endsection

@section('content')
@php
    $isRO          = !$data->kode_akun && !$data->kode_subkomponen;
    $isSubkomponen = !$data->kode_akun && $data->kode_subkomponen;
    $isAkun        = (bool) $data->kode_akun;

    if ($isRO) {
        $levelLabel      = 'RO (Parent)';
        $levelBadge      = 'badge-blue';
        $levelBorderColor= 'border-l-blue-500';
        $levelDesc       = 'Pagu dihitung otomatis dari total Sub Komponen';
        $levelIcon       = 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10';
    } elseif ($isSubkomponen) {
        $levelLabel      = 'Sub Komponen';
        $levelBadge      = 'badge-purple';
        $levelBorderColor= 'border-l-purple-500';
        $levelDesc       = 'Pagu dihitung otomatis dari total Akun';
        $levelIcon       = 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z';
    } else {
        $levelLabel      = 'Akun (Detail)';
        $levelBadge      = 'badge-green';
        $levelBorderColor= 'border-l-green-500';
        $levelDesc       = 'Pagu dapat diedit secara langsung';
        $levelIcon       = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
    }

    $hasRealisasi = $data->total_penyerapan > 0 || $data->tagihan_outstanding > 0;
    $persen       = $data->pagu_anggaran > 0
        ? round(($data->total_penyerapan / $data->pagu_anggaran) * 100, 1)
        : 0;
@endphp

<div class="max-w-4xl">
<form action="{{ route('anggaran.data.update', $data) }}" method="POST" id="formEdit" novalidate>
    @csrf @method('PUT')

    {{-- Hidden: kode tidak boleh diubah --}}
    @if($data->kode_subkomponen)
        <input type="hidden" name="kode_subkomponen" value="{{ $data->kode_subkomponen }}">
    @endif
    @if($data->kode_akun)
        <input type="hidden" name="kode_akun" value="{{ $data->kode_akun }}">
    @endif

    <div class="space-y-5">

        {{-- ===== STATUS BANNER ===== --}}
        <div class="card-flat border-l-4 {{ $levelBorderColor }} p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                {{-- Level info --}}
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl
                        {{ $isRO ? 'bg-blue-100 dark:bg-blue-900/30' : ($isSubkomponen ? 'bg-purple-100 dark:bg-purple-900/30' : 'bg-green-100 dark:bg-green-900/30') }}
                        flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 {{ $isRO ? 'text-blue-600 dark:text-blue-400' : ($isSubkomponen ? 'text-purple-600 dark:text-purple-400' : 'text-green-600 dark:text-green-400') }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $levelIcon }}"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $levelBadge }}">{{ $levelLabel }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $levelDesc }}</span>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-mono">
                            RO: <span class="font-semibold">{{ $data->ro }}</span>
                            @if($data->kode_subkomponen)
                                · Sub: <span class="font-semibold">{{ $data->kode_subkomponen }}</span>
                            @endif
                            @if($data->kode_akun)
                                · Akun: <span class="font-semibold">{{ $data->kode_akun }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                {{-- Stat mini --}}
                @if($data->pagu_anggaran > 0)
                <div class="flex items-center gap-4 text-right">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Pagu</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ format_rupiah($data->pagu_anggaran) }}</p>
                    </div>
                    @if($data->total_penyerapan > 0)
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Realisasi</p>
                        <p class="text-sm font-bold text-green-600 dark:text-green-400">{{ format_rupiah($data->total_penyerapan) }}</p>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-xs text-gray-400 dark:text-gray-500 text-right">Serapan</p>
                        <p class="text-sm font-bold {{ $persen >= 80 ? 'text-green-600 dark:text-green-400' : ($persen >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-500 dark:text-red-400') }}">
                            {{ $persen }}%
                        </p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- ===== WARNING: Ada Realisasi ===== --}}
        @if($hasRealisasi)
        <div class="alert-warning rounded-xl p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-yellow-800 dark:text-yellow-300 text-sm">Perhatian — Item Ini Sudah Memiliki Realisasi</p>
                    <div class="mt-1.5 flex flex-wrap gap-4 text-sm text-yellow-700 dark:text-yellow-400">
                        @if($data->total_penyerapan > 0)
                        <span>Realisasi: <strong>{{ format_rupiah($data->total_penyerapan) }}</strong></span>
                        @endif
                        @if($data->tagihan_outstanding > 0)
                        <span>Outstanding: <strong>{{ format_rupiah($data->tagihan_outstanding) }}</strong></span>
                        @endif
                    </div>
                    @if($isAkun)
                    <p class="text-xs text-yellow-600 dark:text-yellow-500 mt-1.5">
                        Mengubah pagu akan mempengaruhi sisa anggaran. Pastikan perubahan sudah sesuai dengan DIPA/revisi terbaru.
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ===== SECTION 1: KODE IDENTIFIKASI ===== --}}
        <div class="card">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-navy-700">
                <div class="w-7 h-7 rounded-lg bg-navy-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-white">1</span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Kode Identifikasi</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kegiatan, KRO, dan RO</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Kode Kegiatan --}}
                <div class="input-group">
                    <label class="input-label" for="kegiatan">
                        Kode Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kegiatan" name="kegiatan"
                           value="{{ old('kegiatan', $data->kegiatan) }}"
                           class="input-field font-mono @error('kegiatan') input-error @enderror"
                           placeholder="Contoh: 4753" required>
                    @error('kegiatan')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- KRO --}}
                <div class="input-group">
                    <label class="input-label" for="kro">
                        KRO <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kro" name="kro"
                           value="{{ old('kro', $data->kro) }}"
                           class="input-field font-mono @error('kro') input-error @enderror"
                           placeholder="Contoh: EBA" required>
                    @error('kro')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- RO --}}
                <div class="input-group">
                    <label class="input-label" for="ro">
                        RO <span class="text-red-500">*</span>
                    </label>
                    <select id="ro" name="ro"
                            class="input-field @error('ro') input-error @enderror" required>
                        @foreach($roList as $ro)
                            <option value="{{ $ro }}" {{ old('ro', $data->ro) == $ro ? 'selected' : '' }}>
                                {{ $ro }} – {{ get_ro_name($ro) }}
                            </option>
                        @endforeach
                    </select>
                    @error('ro')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sub Komponen (readonly) --}}
                @if($data->kode_subkomponen)
                <div class="input-group">
                    <label class="input-label">Sub Komponen</label>
                    <div class="input-field-readonly flex items-center gap-2">
                        <span class="font-mono font-semibold text-purple-700 dark:text-purple-400">
                            {{ $data->kode_subkomponen }}
                        </span>
                        <span class="badge badge-purple text-xs">Tetap</span>
                    </div>
                    <p class="input-hint">Kode sub komponen tidak dapat diubah</p>
                </div>
                @endif

                {{-- Kode Akun (readonly) --}}
                @if($data->kode_akun)
                <div class="input-group">
                    <label class="input-label">Kode Akun</label>
                    <div class="input-field-readonly flex items-center gap-2">
                        <span class="font-mono font-semibold text-green-700 dark:text-green-400">
                            {{ $data->kode_akun }}
                        </span>
                        <span class="badge badge-green text-xs">Tetap</span>
                    </div>
                    <p class="input-hint">Kode akun tidak dapat diubah</p>
                </div>
                @endif

            </div>
        </div>

        {{-- ===== SECTION 2: INFORMASI DETAIL ===== --}}
        <div class="card">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-navy-700">
                <div class="w-7 h-7 rounded-lg bg-navy-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-white">2</span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Informasi Detail</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Uraian, PIC, dan pagu anggaran</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Uraian --}}
                <div class="input-group sm:col-span-2">
                    <label class="input-label" for="program_kegiatan">
                        Uraian Program / Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="program_kegiatan" name="program_kegiatan" rows="3"
                              class="input-field resize-none @error('program_kegiatan') input-error @enderror"
                              placeholder="Tuliskan uraian lengkap..." required>{{ old('program_kegiatan', $data->program_kegiatan) }}</textarea>
                    @error('program_kegiatan')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PIC --}}
                <div class="input-group">
                    <label class="input-label" for="pic">
                        PIC <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="pic" name="pic"
                           value="{{ old('pic', $data->pic) }}"
                           class="input-field @error('pic') input-error @enderror"
                           placeholder="Penanggung jawab anggaran" required>
                    <p class="input-hint">Kode unit / nama penanggung jawab</p>
                    @error('pic')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pagu Anggaran --}}
                <div class="input-group">
                    <label class="input-label" for="pagu_anggaran">
                        Pagu Anggaran
                        @if($isAkun)<span class="text-red-500">*</span>@endif
                    </label>

                    @if($isAkun)
                        {{-- Editable --}}
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="text-sm font-medium text-gray-400 dark:text-gray-500">Rp</span>
                            </div>
                            <input type="number" id="pagu_anggaran" name="pagu_anggaran"
                                   value="{{ old('pagu_anggaran', $data->pagu_anggaran) }}"
                                   class="input-field pl-10 @error('pagu_anggaran') input-error @enderror"
                                   step="1" min="0" required>
                        </div>
                        <p class="input-hint" id="pagu_preview_hint">
                            <span class="text-green-600 dark:text-green-400">
                                ✏ Nilai saat ini: {{ format_rupiah($data->pagu_anggaran) }}
                            </span>
                        </p>
                        @error('pagu_anggaran')
                            <p class="input-hint-error">{{ $message }}</p>
                        @enderror
                    @else
                        {{-- Readonly --}}
                        <div class="input-field-readonly font-mono">
                            {{ format_rupiah($data->pagu_anggaran) }}
                        </div>
                        <p class="input-hint">
                            <span class="text-amber-500 dark:text-amber-400">
                                🔒 Dihitung otomatis dari {{ $isRO ? 'Sub Komponen' : 'Akun' }} di bawahnya
                            </span>
                        </p>
                    @endif
                </div>

            </div>

            {{-- Preview perubahan pagu (hanya level akun) --}}
            @if($isAkun)
            <div id="pagu_change_preview" class="mt-4 p-3 rounded-xl bg-navy-50 dark:bg-navy-900/40 border border-navy-100 dark:border-navy-700"
                 style="display:none;">
                <p class="text-xs font-semibold text-navy-700 dark:text-navy-300 mb-2">Preview Perubahan</p>
                <div class="grid grid-cols-3 gap-3 text-xs">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Pagu Lama</p>
                        <p class="font-semibold text-gray-700 dark:text-gray-300 mt-0.5">{{ format_rupiah($data->pagu_anggaran) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Pagu Baru</p>
                        <p class="font-semibold text-navy-700 dark:text-navy-300 mt-0.5" id="preview_new_pagu">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Selisih</p>
                        <p class="font-semibold mt-0.5" id="preview_selisih">—</p>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ===== ACTIONS ===== --}}
        <div class="flex items-center justify-between pt-1">
            <a href="{{ route('anggaran.data.index') }}" class="btn btn-ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('anggaran.data.show', $data) }}" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Lihat Detail
                </a>
                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>

    </div>
</form>
</div>
@endsection

@push('scripts')
@if($isAkun)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const paguInput   = document.getElementById('pagu_anggaran');
    const preview     = document.getElementById('pagu_change_preview');
    const previewNew  = document.getElementById('preview_new_pagu');
    const previewDiff = document.getElementById('preview_selisih');
    const hintEl      = document.getElementById('pagu_preview_hint');
    const oldPagu     = {{ (float) $data->pagu_anggaran }};

    const fmt = v => new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(v);

    paguInput.addEventListener('input', function () {
        const newVal = parseFloat(this.value) || 0;
        const diff   = newVal - oldPagu;

        // Preview hint
        if (newVal > 0) {
            hintEl.innerHTML = `<span class="text-navy-600 dark:text-navy-400">≈ ${fmt(newVal)}</span>`;
        } else {
            hintEl.innerHTML = `<span class="text-green-600 dark:text-green-400">✏ Nilai saat ini: {{ format_rupiah($data->pagu_anggaran) }}</span>`;
        }

        // Preview box
        if (newVal !== oldPagu && newVal > 0) {
            preview.style.display = 'block';
            previewNew.textContent  = fmt(newVal);

            if (diff > 0) {
                previewDiff.textContent  = '+' + fmt(diff);
                previewDiff.className    = 'font-semibold mt-0.5 text-green-600 dark:text-green-400';
            } else if (diff < 0) {
                previewDiff.textContent  = fmt(diff);
                previewDiff.className    = 'font-semibold mt-0.5 text-red-500 dark:text-red-400';
            } else {
                previewDiff.textContent  = 'Tidak berubah';
                previewDiff.className    = 'font-semibold mt-0.5 text-gray-500';
            }
        } else {
            preview.style.display = 'none';
        }
    });

    // Submit loading state
    document.getElementById('formEdit').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Menyimpan...`;
    });
});
</script>
@endif
@endpush
