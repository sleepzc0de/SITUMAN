@extends('layouts.app')

@section('title', 'Edit Dokumen Capaian Output')

@section('breadcrumb')
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.dokumen.index') }}" class="breadcrumb-item">Dokumen Capaian Output</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.dokumen.show', $dokumen) }}" class="breadcrumb-item">
        {{ \Illuminate\Support\Str::limit($dokumen->nama_dokumen, 30) }}
    </a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Edit</span>
</nav>
@endsection

@section('page_header')
<div class="flex items-center gap-3">
    <a href="{{ route('anggaran.dokumen.show', $dokumen) }}"
       class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 dark:border-navy-600
              text-gray-500 hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h1 class="page-title">Edit Dokumen Capaian Output</h1>
        <p class="page-subtitle">Perbarui informasi dan file dokumen</p>
    </div>
</div>
@endsection

@section('content')
@php $existingFiles = $dokumen->getAllFiles(); @endphp

<form action="{{ route('anggaran.dokumen.update', $dokumen) }}" method="POST"
      enctype="multipart/form-data" x-data="editDokumen()" x-init="init()">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Kolom Kiri ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Informasi Dokumen --}}
            <div class="card">
                <div class="section-header !mb-4">
                    <div>
                        <h3 class="section-title">Informasi Dokumen</h3>
                        <p class="section-desc">Perbarui data dokumen capaian output</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- RO --}}
                    <div class="input-group">
                        <label class="input-label" for="ro">RO <span class="text-red-500">*</span></label>
                        <select name="ro" id="ro"
                                class="input-field @error('ro') input-error @enderror"
                                x-model="selectedRO"
                                @change="onRoChange()"
                                required>
                            <option value="">— Pilih RO —</option>
                            @foreach($roList as $ro)
                                <option value="{{ $ro }}" {{ old('ro', $dokumen->ro) == $ro ? 'selected' : '' }}>
                                    {{ $ro }} — {{ get_ro_name($ro) }}
                                </option>
                            @endforeach
                        </select>
                        @error('ro') <p class="input-hint-error">{{ $message }}</p> @enderror
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
                        @error('sub_komponen') <p class="input-hint-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Bulan --}}
                    <div class="input-group">
                        <label class="input-label" for="bulan">Bulan <span class="text-red-500">*</span></label>
                        <select name="bulan" id="bulan"
                                class="input-field @error('bulan') input-error @enderror"
                                required>
                            <option value="">— Pilih Bulan —</option>
                            @foreach($bulanList as $bulan)
                                <option value="{{ $bulan }}" {{ old('bulan', $dokumen->bulan) == $bulan ? 'selected' : '' }}>
                                    {{ ucfirst($bulan) }}
                                </option>
                            @endforeach
                        </select>
                        @error('bulan') <p class="input-hint-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nama Dokumen --}}
                    <div class="input-group">
                        <label class="input-label" for="nama_dokumen">
                            Nama Dokumen <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_dokumen" id="nama_dokumen"
                               value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}"
                               class="input-field @error('nama_dokumen') input-error @enderror"
                               placeholder="Nama dokumen capaian output"
                               required>
                        @error('nama_dokumen') <p class="input-hint-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="input-group sm:col-span-2">
                        <label class="input-label" for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="input-field @error('keterangan') input-error @enderror"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $dokumen->keterangan) }}</textarea>
                        @error('keterangan') <p class="input-hint-error">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

            {{-- File Tersimpan --}}
            @if(count($existingFiles) > 0)
            <div class="card">
                <div class="section-header !mb-4">
                    <div>
                        <h3 class="section-title">File Tersimpan</h3>
                        <p class="section-desc">Centang file yang ingin dihapus saat menyimpan</p>
                    </div>
                    <span class="badge badge-info">{{ count($existingFiles) }} file</span>
                </div>

                <div class="space-y-2">
                    @foreach($existingFiles as $index => $file)
                    <div x-data="{ checked: false }"
                         class="flex items-center gap-3 p-3 rounded-xl border transition-all duration-150"
                         :class="checked
                             ? 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10'
                             : 'border-gray-100 dark:border-navy-700 hover:bg-gray-50 dark:hover:bg-navy-700/40'">
                        <input type="checkbox"
                               name="remove_files[]"
                               value="{{ $index }}"
                               id="remove_{{ $index }}"
                               x-model="checked"
                               class="w-4 h-4 rounded border-gray-300 dark:border-navy-600
                                      text-red-500 focus:ring-red-400 cursor-pointer flex-shrink-0">
                        <label for="remove_{{ $index }}" class="flex items-center gap-3 flex-1 cursor-pointer min-w-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors"
                                 :class="checked
                                     ? 'bg-red-100 dark:bg-red-900/30'
                                     : 'bg-gray-100 dark:bg-navy-700'">
                                <svg class="w-4 h-4 transition-colors"
                                     :class="checked ? 'text-red-500' : '{{ file_icon_class($file['path']) }}'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate transition-all"
                                   :class="checked
                                       ? 'text-red-500 dark:text-red-400 line-through'
                                       : 'text-gray-900 dark:text-white'">
                                    {{ $file['name'] }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ strtoupper(pathinfo($file['path'], PATHINFO_EXTENSION)) }}
                                </p>
                            </div>
                        </label>
                        <a href="{{ route('anggaran.dokumen.download-single', [$dokumen->id, $index]) }}"
                           class="table-action-download flex-shrink-0"
                           title="Download file ini"
                           :class="checked ? 'opacity-30 pointer-events-none' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </a>
                    </div>
                    @endforeach
                </div>

                <div class="alert alert-warning mt-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="text-xs">File yang dicentang akan dihapus permanen saat Anda menyimpan perubahan.</span>
                </div>
            </div>
            @endif

            {{-- Tambah File Baru --}}
            <div class="card">
                <div class="section-header !mb-4">
                    <div>
                        <h3 class="section-title">Tambah File Baru</h3>
                        <p class="section-desc">Opsional — tambahkan file tambahan ke dokumen ini</p>
                    </div>
                </div>

                <label for="new-file-input"
                       class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed
                              border-gray-300 dark:border-navy-600 rounded-xl cursor-pointer
                              hover:border-navy-400 dark:hover:border-navy-500
                              bg-gray-50 dark:bg-navy-900/50 hover:bg-gray-100 dark:hover:bg-navy-800/60
                              transition-all duration-200">
                    <div class="flex items-center gap-3 pointer-events-none text-gray-500 dark:text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium">Tambah file baru</p>
                            <p class="text-xs">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG — Maks. 10MB</p>
                        </div>
                    </div>
                    <input type="file" id="new-file-input" name="files[]"
                           class="hidden"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                           multiple
                           @change="onNewFilePick($event)">
                </label>

                @error('files.*') <p class="input-hint-error mt-2">{{ $message }}</p> @enderror

                <div x-show="newFiles.length > 0" x-transition class="mt-3 space-y-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        <span x-text="newFiles.length"></span> file baru akan ditambahkan
                    </p>
                    <template x-for="(f, i) in newFiles" :key="i">
                        <div class="flex items-center gap-3 p-2.5 rounded-lg
                                    bg-emerald-50 dark:bg-emerald-900/20
                                    border border-emerald-200 dark:border-emerald-800">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm text-emerald-800 dark:text-emerald-300 flex-1 truncate"
                                  x-text="f.name"></span>
                            <span class="text-xs text-emerald-600 flex-shrink-0"
                                  x-text="(f.size/1024/1024).toFixed(2)+' MB'"></span>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- ── Kolom Kanan ── --}}
        <div class="space-y-5">

            {{-- Info Anggaran --}}
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

            {{-- Ringkasan --}}
            <div class="card">
                <h4 class="section-title mb-3">Ringkasan</h4>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">File tersimpan</span>
                        <span class="font-semibold">{{ count($existingFiles) }} file</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">File baru</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400"
                              x-text="newFiles.length + ' file'"></span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500">Terakhir diubah</span>
                        <span class="font-semibold text-xs">{{ format_tanggal_short($dokumen->updated_at) }}</span>
                    </div>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="card">
                <div class="flex flex-col gap-2">
                    <button type="submit" class="btn btn-primary w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('anggaran.dokumen.show', $dokumen) }}" class="btn btn-ghost w-full">
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
function editDokumen() {
    return {
        selectedRO:      '{{ old('ro', $dokumen->ro) }}',
        selectedSubkomp: '{{ old('sub_komponen', $dokumen->sub_komponen) }}',
        subkomponens:    [],
        loadingSubkomp:  false,
        anggaranInfo:    null,
        newFiles:        [],

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
            this.anggaranInfo    = null;
            if (!this.selectedRO) return;

            this.loadingSubkomp = true;
            try {
                const res = await axios.get('{{ route('anggaran.dokumen.ajax.subkomponen') }}', {
                    params: { ro: this.selectedRO }
                });
                this.subkomponens = res.data;
                await this.$nextTick();
                this.onSubkompChange();
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

        onNewFilePick(e) {
            this.newFiles = Array.from(e.target.files);
        },

        init() {
            if (this.selectedRO) this.onRoChange();
        },
    }
}
</script>
@endpush
