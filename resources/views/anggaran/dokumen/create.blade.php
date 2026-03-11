@extends('layouts.app')

@section('title', 'Upload Dokumen Capaian Output')

@section('breadcrumb')
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.dokumen.index') }}" class="breadcrumb-item">Dokumen Capaian Output</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Upload Dokumen</span>
</nav>
@endsection

@section('page_header')
<div class="flex items-center gap-3">
    <a href="{{ route('anggaran.dokumen.index') }}"
       class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 dark:border-navy-600
              text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h1 class="page-title">Upload Dokumen Capaian Output</h1>
        <p class="page-subtitle">Tambah dokumen bukti capaian output kegiatan</p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('anggaran.dokumen.store') }}" method="POST" enctype="multipart/form-data"
      x-data="createDokumen()" @submit.prevent="submitForm()">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Kolom Kiri: Form Utama ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Informasi Dokumen --}}
            <div class="card">
                <div class="section-header !mb-4">
                    <div>
                        <h3 class="section-title">Informasi Dokumen</h3>
                        <p class="section-desc">Isi data dokumen capaian output</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- RO --}}
                    <div class="input-group">
                        <label class="input-label" for="ro">
                            RO <span class="text-red-500">*</span>
                        </label>
                        <select name="ro" id="ro"
                                class="input-field @error('ro') input-error @enderror"
                                x-model="selectedRO"
                                @change="onRoChange()"
                                required>
                            <option value="">— Pilih RO —</option>
                            @foreach ($roList as $ro)
                                <option value="{{ $ro }}" {{ old('ro') == $ro ? 'selected' : '' }}>
                                    {{ $ro }} — {{ get_ro_name($ro) }}
                                </option>
                            @endforeach
                        </select>
                        @error('ro')
                            <p class="input-hint-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sub Komponen --}}
                    <div class="input-group">
                        <label class="input-label" for="sub_komponen">
                            Sub Komponen <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="sub_komponen" id="sub_komponen"
                                    class="input-field @error('sub_komponen') input-error @enderror"
                                    x-model="selectedSubkomp"
                                    @change="onSubkompChange()"
                                    :disabled="!selectedRO || loadingSubkomp"
                                    required>
                                <option value="">
                                    <span x-text="!selectedRO ? '— Pilih RO terlebih dahulu —'
                                        : (loadingSubkomp ? 'Memuat…' : '— Pilih Sub Komponen —')">
                                    </span>
                                </option>
                                <template x-for="item in subkomponens" :key="item.kode_subkomponen">
                                    <option :value="item.kode_subkomponen"
                                            x-text="`${item.kode_subkomponen} — ${item.program_kegiatan}`">
                                    </option>
                                </template>
                            </select>
                            <div x-show="loadingSubkomp"
                                 class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                            </div>
                        </div>
                        @error('sub_komponen')
                            <p class="input-hint-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bulan --}}
                    <div class="input-group">
                        <label class="input-label" for="bulan">
                            Bulan <span class="text-red-500">*</span>
                        </label>
                        <select name="bulan" id="bulan"
                                class="input-field @error('bulan') input-error @enderror"
                                required>
                            <option value="">— Pilih Bulan —</option>
                            @foreach ($bulanList as $bulan)
                                <option value="{{ $bulan }}" {{ old('bulan') == $bulan ? 'selected' : '' }}>
                                    {{ ucfirst($bulan) }}
                                </option>
                            @endforeach
                        </select>
                        @error('bulan')
                            <p class="input-hint-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Dokumen --}}
                    <div class="input-group">
                        <label class="input-label" for="nama_dokumen">
                            Nama Dokumen <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_dokumen" id="nama_dokumen"
                               value="{{ old('nama_dokumen') }}"
                               class="input-field @error('nama_dokumen') input-error @enderror"
                               placeholder="Contoh: Laporan Capaian Output Maret 2026"
                               required>
                        @error('nama_dokumen')
                            <p class="input-hint-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="input-group sm:col-span-2">
                        <label class="input-label" for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="input-field @error('keterangan') input-error @enderror"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="input-hint-error">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Upload File --}}
            <div class="card">
                <div class="section-header !mb-4">
                    <div>
                        <h3 class="section-title">File Dokumen</h3>
                        <p class="section-desc">Upload satu atau beberapa file sekaligus</p>
                    </div>
                </div>

                <label for="file-input"
                       class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed
                              border-gray-300 dark:border-navy-600 rounded-xl cursor-pointer
                              hover:border-navy-400 dark:hover:border-navy-500
                              bg-gray-50 dark:bg-navy-900/50 hover:bg-gray-100 dark:hover:bg-navy-800/60
                              transition-all duration-200">
                    <div class="flex flex-col items-center gap-2 pointer-events-none text-gray-500 dark:text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <div class="text-center">
                            <p class="text-sm font-medium">
                                Klik untuk pilih file
                                <span class="text-navy-600 dark:text-navy-400">atau drag & drop</span>
                            </p>
                            <p class="text-xs mt-0.5">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG — Maks. 10MB/file</p>
                        </div>
                    </div>
                    <input type="file" id="file-input" name="files[]"
                           class="hidden"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                           multiple required
                           @change="onFilePick($event)">
                </label>

                @error('files.*')
                    <p class="input-hint-error mt-2">{{ $message }}</p>
                @enderror

                {{-- Preview file terpilih --}}
                <div x-show="pickedFiles.length > 0" x-transition class="mt-3 space-y-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        <span x-text="pickedFiles.length"></span> file dipilih
                    </p>
                    <template x-for="(f, i) in pickedFiles" :key="i">
                        <div class="flex items-center gap-3 p-2.5 rounded-lg
                                    bg-navy-50 dark:bg-navy-800
                                    border border-navy-100 dark:border-navy-700">
                            <svg class="w-4 h-4 text-navy-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate"
                                  x-text="f.name"></span>
                            <span class="text-xs text-gray-400 flex-shrink-0"
                                  x-text="(f.size/1024/1024).toFixed(2)+' MB'"></span>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- ── Kolom Kanan: Sidebar ── --}}
        <div class="space-y-5">

            {{-- Info Anggaran —  muncul setelah sub komponen dipilih --}}
            <div x-show="anggaranInfo" x-transition
                 class="card border-l-4 border-l-navy-500">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-navy-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Posisi Anggaran
                </h4>
                <template x-if="anggaranInfo">
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between gap-2">
                            <span class="text-gray-500">Pagu</span>
                            <span class="font-semibold text-gray-900 dark:text-white text-right"
                                  x-text="rp(anggaranInfo.pagu_anggaran)"></span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-gray-500">Realisasi</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400 text-right"
                                  x-text="rp(anggaranInfo.total_penyerapan)"></span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-gray-500">Sisa</span>
                            <span class="font-semibold text-right"
                                  :class="anggaranInfo.sisa < 0 ? 'text-red-500' : 'text-gray-700 dark:text-gray-300'"
                                  x-text="rp(anggaranInfo.sisa)"></span>
                        </div>
                        <div class="pt-2 border-t border-gray-100 dark:border-navy-700">
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="text-gray-500">Penyerapan</span>
                                <span class="font-semibold"
                                      :class="{
                                          'text-green-600 dark:text-green-400': persen >= 80,
                                          'text-amber-600 dark:text-amber-400': persen >= 50 && persen < 80,
                                          'text-red-500  dark:text-red-400':    persen < 50
                                      }"
                                      x-text="persen.toFixed(1)+'%'"></span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar"
                                     :class="{
                                         'bg-green-500': persen >= 80,
                                         'bg-amber-500': persen >= 50 && persen < 80,
                                         'bg-red-400':   persen < 50
                                     }"
                                     :style="`width:${Math.min(persen,100)}%`">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Panduan --}}
            <div class="card bg-navy-50 dark:bg-navy-900/50 !border-navy-200 dark:!border-navy-700">
                <h4 class="text-sm font-semibold text-navy-800 dark:text-navy-300 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Panduan Upload
                </h4>
                <ul class="text-xs text-navy-700 dark:text-navy-400 space-y-1.5">
                    <li class="flex gap-2"><span class="text-navy-400 flex-shrink-0">1.</span>Pilih RO terlebih dahulu</li>
                    <li class="flex gap-2"><span class="text-navy-400 flex-shrink-0">2.</span>Pilih sub komponen yang sesuai</li>
                    <li class="flex gap-2"><span class="text-navy-400 flex-shrink-0">3.</span>Pilih bulan periode capaian</li>
                    <li class="flex gap-2"><span class="text-navy-400 flex-shrink-0">4.</span>Isi nama dokumen dengan jelas</li>
                    <li class="flex gap-2"><span class="text-navy-400 flex-shrink-0">5.</span>Upload satu atau beberapa file</li>
                </ul>
            </div>

            {{-- Tombol Aksi --}}
            <div class="card">
                <div class="flex flex-col gap-2">
                    <button type="submit"
                            class="btn btn-primary w-full"
                            :class="{ 'opacity-60 cursor-not-allowed': pickedFiles.length === 0 }"
                            :disabled="pickedFiles.length === 0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Dokumen
                        <template x-if="pickedFiles.length > 0">
                            <span class="ml-1 px-1.5 py-0.5 text-xs bg-white/25 rounded-md"
                                  x-text="`${pickedFiles.length} file`"></span>
                        </template>
                    </button>
                    <a href="{{ route('anggaran.dokumen.index') }}" class="btn btn-ghost w-full">
                        Batal
                    </a>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function createDokumen() {
    return {
        selectedRO:      '{{ old('ro') }}',
        selectedSubkomp: '{{ old('sub_komponen') }}',
        subkomponens:    [],
        loadingSubkomp:  false,
        anggaranInfo:    null,
        pickedFiles:     [],

        get persen() {
            if (!this.anggaranInfo?.pagu_anggaran) return 0;
            return (this.anggaranInfo.total_penyerapan / this.anggaranInfo.pagu_anggaran) * 100;
        },

        rp(val) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0
            }).format(val ?? 0);
        },

        async onRoChange() {
            this.subkomponens    = [];
            this.selectedSubkomp = '';
            this.anggaranInfo    = null;
            if (!this.selectedRO) return;

            this.loadingSubkomp = true;
            try {
                const res  = await axios.get('{{ route('anggaran.dokumen.ajax.subkomponen') }}', {
                    params: { ro: this.selectedRO }
                });
                this.subkomponens = res.data;

                // Restore old value jika ada (setelah validasi gagal)
                const oldVal = '{{ old('sub_komponen') }}';
                if (oldVal) {
                    this.selectedSubkomp = oldVal;
                    this.onSubkompChange();
                }
            } catch(e) {
                window.showToast('Gagal memuat sub komponen', 'error');
            } finally {
                this.loadingSubkomp = false;
            }
        },

        onSubkompChange() {
            this.anggaranInfo = this.subkomponens.find(
                s => s.kode_subkomponen === this.selectedSubkomp
            ) ?? null;
        },

        onFilePick(e) {
            this.pickedFiles = Array.from(e.target.files);
        },

        submitForm() {
            // Lepas @submit.prevent lalu submit native agar enctype multipart jalan
            this.$el.removeEventListener('submit', () => {});
            this.$el.submit();
        },

        init() {
            if (this.selectedRO) this.onRoChange();
        },
    }
}
</script>
@endpush
