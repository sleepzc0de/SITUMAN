@extends('layouts.app')

@section('title', 'Tambah Revisi Anggaran')

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.revisi.index') }}" class="breadcrumb-item">Revisi Anggaran</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Tambah Revisi</span>
</nav>
@endsection

@section('page_header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="page-title">Tambah Revisi Anggaran</h1>
        <p class="page-subtitle">Buat catatan perubahan pagu anggaran (audit trail)</p>
    </div>
    <a href="{{ route('anggaran.revisi.index') }}" class="btn btn-ghost">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')
<div x-data="revisiForm()" class="space-y-6">

    {{-- Info Banner --}}
    <div class="alert alert-info">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>Revisi anggaran <strong>tidak dapat diedit</strong> setelah disimpan untuk menjaga integritas audit trail. Pastikan semua data sudah benar sebelum menyimpan.</p>
    </div>

    <form action="{{ route('anggaran.revisi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Utama (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Item Anggaran --}}
                <div class="card">
                    <div class="section-header">
                        <div>
                            <p class="section-title">Pilih Item Anggaran</p>
                            <p class="section-desc">Pilih akun yang akan direvisi pagunya</p>
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label">
                            Item Anggaran (Level Akun)
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="anggaran_id" id="anggaran_id"
                            class="input-field @error('anggaran_id') input-error @enderror"
                            x-on:change="onAnggaranChange($event)"
                            required>
                            <option value="">-- Pilih item anggaran --</option>
                            @foreach($anggarans as $anggaran)
                                <option value="{{ $anggaran->id }}"
                                    data-pagu="{{ $anggaran->pagu_anggaran }}"
                                    data-ro="{{ $anggaran->ro }}"
                                    data-subkomp="{{ $anggaran->kode_subkomponen }}"
                                    data-akun="{{ $anggaran->kode_akun }}"
                                    data-uraian="{{ $anggaran->program_kegiatan }}"
                                    {{ old('anggaran_id') == $anggaran->id ? 'selected' : '' }}>
                                    [{{ $anggaran->ro }}] {{ $anggaran->kode_subkomponen }} · {{ $anggaran->kode_akun }}
                                    — {{ truncate_text($anggaran->program_kegiatan, 55) }}
                                </option>
                            @endforeach
                        </select>
                        @error('anggaran_id')
                            <p class="input-hint-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Preview detail anggaran terpilih --}}
                    <div x-show="selectedAnggaran.ro" x-transition
                         class="mt-4 p-4 bg-navy-50 dark:bg-navy-900/50 rounded-xl border border-navy-100 dark:border-navy-700">
                        <p class="text-xs font-semibold text-navy-600 dark:text-navy-400 uppercase tracking-wide mb-3">
                            Detail Item Terpilih
                        </p>
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mb-0.5">RO</p>
                                <p class="font-semibold text-gray-900 dark:text-white" x-text="selectedAnggaran.ro"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mb-0.5">Sub Komponen</p>
                                <p class="font-semibold text-gray-900 dark:text-white" x-text="selectedAnggaran.subkomp"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mb-0.5">Kode Akun</p>
                                <p class="font-semibold text-gray-900 dark:text-white" x-text="selectedAnggaran.akun"></p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-gray-500 dark:text-gray-400 text-xs mb-0.5">Uraian</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-text="selectedAnggaran.uraian"></p>
                        </div>
                    </div>
                </div>

                {{-- Perubahan Pagu --}}
                <div class="card">
                    <div class="section-header">
                        <div>
                            <p class="section-title">Perubahan Pagu</p>
                            <p class="section-desc">Masukkan nilai pagu sesudah revisi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="input-group">
                            <label class="input-label">Pagu Sebelum</label>
                            <input type="text" x-model="paguSebelumFormatted"
                                class="input-field-readonly" readonly
                                placeholder="Otomatis dari item terpilih">
                            <input type="hidden" name="pagu_sebelum_hidden" x-bind:value="paguSebelum">
                        </div>

                        <div class="input-group">
                            <label class="input-label">Pagu Sesudah <span class="text-red-500">*</span></label>
                            <input type="text" id="pagu_sesudah_display"
                                x-on:input="onPaguSesudahInput($event)"
                                class="input-field @error('pagu_sesudah') input-error @enderror"
                                placeholder="Masukkan pagu baru"
                                autocomplete="off" required>
                            <input type="hidden" name="pagu_sesudah" x-bind:value="paguSesudah">
                            @error('pagu_sesudah')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Selisih Visualisasi --}}
                    <div x-show="paguSebelum > 0 && paguSesudah > 0" x-transition class="mt-4">
                        <div class="p-4 rounded-xl border"
                             :class="selisih > 0
                                ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700'
                                : selisih < 0
                                    ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700'
                                    : 'bg-gray-50 dark:bg-navy-800 border-gray-200 dark:border-navy-600'">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide"
                                       :class="selisih > 0 ? 'text-emerald-600 dark:text-emerald-400' : selisih < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500'">
                                        Selisih Perubahan
                                    </p>
                                    <p class="text-xl font-bold mt-1"
                                       :class="selisih > 0 ? 'text-emerald-700 dark:text-emerald-300' : selisih < 0 ? 'text-red-700 dark:text-red-300' : 'text-gray-600'">
                                        <span x-text="selisih > 0 ? '+' : ''"></span><span x-text="formatRupiah(selisih)"></span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="badge text-sm"
                                          :class="selisih > 0 ? 'badge-success' : selisih < 0 ? 'badge-danger' : 'badge-gray'">
                                        <span x-text="selisih > 0 ? '↑ Naik' : selisih < 0 ? '↓ Turun' : '= Tetap'"></span>
                                    </span>
                                    <p class="text-xs mt-1"
                                       :class="selisih > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
                                       x-text="persentaseLabel"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Alasan Revisi --}}
                <div class="card">
                    <div class="section-header">
                        <div>
                            <p class="section-title">Alasan & Dokumen</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="input-group">
                            <label class="input-label">
                                Alasan Revisi <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alasan_revisi" rows="4"
                                class="input-field @error('alasan_revisi') input-error @enderror"
                                placeholder="Jelaskan latar belakang dan alasan dilakukan revisi anggaran ini..."
                                required>{{ old('alasan_revisi') }}</textarea>
                            @error('alasan_revisi')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="input-group">
                            <label class="input-label">Dokumen Pendukung</label>
                            <div x-data="fileDropzone()"
                                 class="relative border-2 border-dashed rounded-xl p-6 text-center transition-colors duration-200 cursor-pointer"
                                 :class="dragging ? 'border-navy-400 bg-navy-50 dark:bg-navy-800/50' : 'border-gray-200 dark:border-navy-600 hover:border-navy-300 dark:hover:border-navy-500'"
                                 @dragover.prevent="dragging=true"
                                 @dragleave.prevent="dragging=false"
                                 @drop.prevent="handleDrop($event)"
                                 @click="$refs.fileInput.click()">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <template x-if="!fileName">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                            Klik atau seret file ke sini
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF, maks. 5 MB</p>
                                    </div>
                                </template>
                                <template x-if="fileName">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="fileName"></p>
                                    </div>
                                </template>
                                <input type="file" name="dokumen_pendukung" accept=".pdf"
                                    x-ref="fileInput"
                                    @change="handleChange($event)"
                                    class="hidden">
                            </div>
                            @error('dokumen_pendukung')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Samping (1/3) --}}
            <div class="space-y-6">

                {{-- Jenis & Tanggal --}}
                <div class="card">
                    <p class="section-title mb-4">Detail Revisi</p>

                    <div class="space-y-4">
                        <div class="input-group">
                            <label class="input-label">Jenis Revisi <span class="text-red-500">*</span></label>
                            <select name="jenis_revisi"
                                class="input-field @error('jenis_revisi') input-error @enderror"
                                required>
                                <option value="">Pilih jenis</option>
                                @foreach($jenisRevisi as $jenis)
                                    <option value="{{ $jenis }}" {{ old('jenis_revisi') == $jenis ? 'selected' : '' }}>
                                        {{ $jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_revisi')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="input-group">
                            <label class="input-label">Tanggal Revisi <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_revisi"
                                value="{{ old('tanggal_revisi', date('Y-m-d')) }}"
                                class="input-field @error('tanggal_revisi') input-error @enderror"
                                required>
                            @error('tanggal_revisi')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Ringkasan --}}
                <div class="card bg-navy-50 dark:bg-navy-900/40 border-navy-100 dark:border-navy-700">
                    <p class="section-title mb-4 text-navy-700 dark:text-navy-300">Ringkasan</p>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pagu Sebelum</span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="paguSebelumFormatted || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pagu Sesudah</span>
                            <span class="font-semibold text-gray-900 dark:text-white"
                                x-text="paguSesudah > 0 ? formatRupiah(paguSesudah) : '-'"></span>
                        </div>
                        <div class="divider !my-2"></div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Selisih</span>
                            <span class="font-bold"
                                :class="selisih > 0 ? 'text-emerald-600 dark:text-emerald-400' : selisih < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400'"
                                x-text="(selisih !== 0 ? (selisih > 0 ? '+' : '') : '') + (paguSebelum > 0 ? formatRupiah(selisih) : '-')">
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="card">
                    <div class="space-y-3">
                        <button type="submit" class="btn btn-primary w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Revisi
                        </button>
                        <a href="{{ route('anggaran.revisi.index') }}" class="btn btn-ghost w-full">
                            Batal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function revisiForm() {
    return {
        paguSebelum: 0,
        paguSebelumFormatted: '',
        paguSesudah: 0,
        selectedAnggaran: { ro: '', subkomp: '', akun: '', uraian: '' },

        get selisih() {
            return this.paguSesudah - this.paguSebelum;
        },
        get persentaseLabel() {
            if (!this.paguSebelum) return '';
            const pct = Math.abs((this.selisih / this.paguSebelum) * 100).toFixed(2);
            return (this.selisih > 0 ? '+' : '-') + pct + '%';
        },

        formatRupiah(v) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
        },

        onAnggaranChange(event) {
            const opt = event.target.options[event.target.selectedIndex];
            const pagu = parseFloat(opt.dataset.pagu) || 0;
            this.paguSebelum = pagu;
            this.paguSebelumFormatted = pagu > 0 ? this.formatRupiah(pagu) : '';
            this.selectedAnggaran = {
                ro:      opt.dataset.ro     || '',
                subkomp: opt.dataset.subkomp|| '',
                akun:    opt.dataset.akun   || '',
                uraian:  opt.dataset.uraian || '',
            };
        },

        onPaguSesudahInput(event) {
            const raw = parseInt(event.target.value.replace(/\D/g,''), 10) || 0;
            this.paguSesudah = raw;
            event.target.value = raw > 0 ? new Intl.NumberFormat('id-ID').format(raw) : '';
        },

        init() {
            // Trigger jika ada old value
            const sel = document.getElementById('anggaran_id');
            if (sel && sel.value) {
                sel.dispatchEvent(new Event('change'));
            }
        }
    }
}
</script>
@endpush
@endsection
