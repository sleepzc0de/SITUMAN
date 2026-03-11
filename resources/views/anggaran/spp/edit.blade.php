@extends('layouts.app')

@section('title', 'Edit SPP – ' . $spp->no_spp)

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.spp.index') }}" class="breadcrumb-item">Data SPP</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.spp.show', $spp) }}" class="breadcrumb-item">{{ $spp->no_spp }}</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Edit</span>
</nav>
@endsection

@section('page_header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="page-title">Edit SPP</h1>
        <p class="page-subtitle">{{ $spp->no_spp }} &mdash; {{ ucfirst($spp->bulan) }} &mdash;
            <span class="{{ $spp->status === 'Tagihan Telah SP2D' ? 'text-emerald-600' : 'text-orange-500' }} font-medium">
                {{ $spp->status === 'Tagihan Telah SP2D' ? 'Sudah SP2D' : 'Belum SP2D' }}
            </span>
        </p>
    </div>
    <a href="{{ route('anggaran.spp.show', $spp) }}" class="btn btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Batal
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('anggaran.spp.update', $spp) }}" method="POST"
      x-data="sppEditForm()" class="space-y-5">
    @csrf
    @method('PUT')

    {{-- ===== INFORMASI DASAR ===== --}}
    <div class="card">
        <div class="section-header mb-4">
            <div>
                <p class="section-title">Informasi Dasar</p>
                <p class="section-desc">Data identitas dan detail SPP</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">No SPP <span class="text-red-500">*</span></label>
                <input type="text" name="no_spp" value="{{ old('no_spp', $spp->no_spp) }}"
                       class="input-field @error('no_spp') input-error @enderror" required>
                @error('no_spp')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Nominatif</label>
                <input type="text" name="nominatif" value="{{ old('nominatif', $spp->nominatif) }}"
                       class="input-field" placeholder="Nama nominatif (opsional)">
            </div>

            <div class="input-group">
                <label class="input-label">Tanggal SPP <span class="text-red-500">*</span></label>
                <input type="date" name="tgl_spp"
                       value="{{ old('tgl_spp', $spp->tgl_spp?->format('Y-m-d')) }}"
                       class="input-field @error('tgl_spp') input-error @enderror" required>
                @error('tgl_spp')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Bulan <span class="text-red-500">*</span></label>
                <select name="bulan" class="input-field @error('bulan') input-error @enderror" required>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan }}" {{ old('bulan', $spp->bulan) == $bulan ? 'selected' : '' }}>
                            {{ ucfirst($bulan) }}
                        </option>
                    @endforeach
                </select>
                @error('bulan')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Jenis Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="jenis_kegiatan" value="{{ old('jenis_kegiatan', $spp->jenis_kegiatan) }}"
                       class="input-field @error('jenis_kegiatan') input-error @enderror" required>
                @error('jenis_kegiatan')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Jenis Belanja <span class="text-red-500">*</span></label>
                <select name="jenis_belanja" class="input-field @error('jenis_belanja') input-error @enderror" required>
                    @foreach($jenisBelanja as $jenis)
                        <option value="{{ $jenis }}" {{ old('jenis_belanja', $spp->jenis_belanja) == $jenis ? 'selected' : '' }}>
                            {{ $jenis }}
                        </option>
                    @endforeach
                </select>
                @error('jenis_belanja')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Nomor Kontrak / SPBy</label>
                <input type="text" name="nomor_kontrak" value="{{ old('nomor_kontrak', $spp->nomor_kontrak) }}"
                       class="input-field" placeholder="Nomor kontrak jika kontraktual">
            </div>

            <div class="input-group">
                <label class="input-label">No BAST / Kuitansi</label>
                <input type="text" name="no_bast" value="{{ old('no_bast', $spp->no_bast) }}"
                       class="input-field" placeholder="Nomor BAST atau Kuitansi">
            </div>

            <div class="input-group">
                <label class="input-label">ID e-Perjadin</label>
                <input type="text" name="id_eperjadin" value="{{ old('id_eperjadin', $spp->id_eperjadin) }}"
                       class="input-field" placeholder="ID e-Perjadin jika ada">
            </div>

            <div class="input-group">
                <label class="input-label">Bagian <span class="text-red-500">*</span></label>
                <input type="text" name="bagian" value="{{ old('bagian', $spp->bagian) }}"
                       class="input-field @error('bagian') input-error @enderror" required>
                @error('bagian')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Nama PIC <span class="text-red-500">*</span></label>
                <input type="text" name="nama_pic" value="{{ old('nama_pic', $spp->nama_pic) }}"
                       class="input-field @error('nama_pic') input-error @enderror" required>
                @error('nama_pic')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group md:col-span-2">
                <label class="input-label">Uraian SPP <span class="text-red-500">*</span></label>
                <textarea name="uraian_spp" rows="3"
                          class="input-field @error('uraian_spp') input-error @enderror" required>{{ old('uraian_spp', $spp->uraian_spp) }}</textarea>
                @error('uraian_spp')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ===== KODE ANGGARAN ===== --}}
    <div class="card">
        <div class="section-header mb-4">
            <div>
                <p class="section-title">Kode Anggaran (COA)</p>
                <p class="section-desc">COA saat ini: <code class="font-mono text-navy-600 dark:text-navy-400">{{ $spp->coa }}</code></p>
            </div>
            <div x-show="sisaInfo.show" x-transition class="flex-shrink-0">
                <div :class="sisaInfo.isWarning ? 'alert-warning' : 'alert-success'" class="alert text-xs !py-2 !px-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold" x-text="'Sisa Efektif: ' + sisaInfo.sisa"></p>
                        <p class="opacity-75" x-text="'Pagu: ' + sisaInfo.pagu + ' | Outstanding: ' + sisaInfo.outstanding"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">Kode Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="kode_kegiatan" id="kode_kegiatan"
                       value="{{ old('kode_kegiatan', $spp->kode_kegiatan) }}"
                       class="input-field @error('kode_kegiatan') input-error @enderror" required>
                @error('kode_kegiatan')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">KRO <span class="text-red-500">*</span></label>
                <input type="text" name="kro" id="kro"
                       value="{{ old('kro', $spp->kro) }}"
                       class="input-field @error('kro') input-error @enderror" required>
                @error('kro')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">RO <span class="text-red-500">*</span></label>
                <select name="ro" id="ro"
                        class="input-field @error('ro') input-error @enderror"
                        @change="onRoChange($event.target.value)" required>
                    <option value="">Pilih RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}" {{ old('ro', $spp->ro) == $ro ? 'selected' : '' }}>
                            {{ $ro }} – {{ get_ro_name($ro) }}
                        </option>
                    @endforeach
                </select>
                @error('ro')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Sub Komponen <span class="text-red-500">*</span></label>
                <select name="sub_komponen" id="sub_komponen"
                        class="input-field @error('sub_komponen') input-error @enderror"
                        @change="onSubkomponenChange($event.target.value)" required>
                    <option value="">Pilih Sub Komponen</option>
                    @foreach($subkomponenList as $subkomp)
                        <option value="{{ $subkomp->kode_subkomponen }}"
                                {{ old('sub_komponen', $spp->sub_komponen) == $subkomp->kode_subkomponen ? 'selected' : '' }}>
                            {{ $subkomp->kode_subkomponen }} – {{ $subkomp->program_kegiatan }}
                        </option>
                    @endforeach
                </select>
                @error('sub_komponen')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group md:col-span-2">
                <label class="input-label">MAK (Kode Akun) <span class="text-red-500">*</span></label>
                <select name="mak" id="mak"
                        class="input-field @error('mak') input-error @enderror"
                        @change="onMakChange($event)" required>
                    <option value="">Pilih MAK</option>
                    @foreach($akunList as $akun)
                        <option value="{{ $akun->kode_akun }}"
                                data-kegiatan="{{ $akun->kegiatan ?? $spp->kode_kegiatan }}"
                                data-kro="{{ $akun->kro }}"
                                data-pagu="{{ $akun->pagu_anggaran }}"
                                data-sisa="{{ $akun->sisa }}"
                                data-outstanding="{{ $akun->tagihan_outstanding }}"
                                data-efektif="{{ $akun->sisa_efektif ?? ($akun->sisa - $akun->tagihan_outstanding) }}"
                                {{ old('mak', $spp->mak) == $akun->kode_akun ? 'selected' : '' }}>
                            {{ $akun->kode_akun }} – {{ $akun->program_kegiatan }}
                        </option>
                    @endforeach
                </select>
                @error('mak')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ===== DOKUMEN PENDUKUNG ===== --}}
    <div class="card">
        <div class="section-header mb-4">
            <div>
                <p class="section-title">Dokumen Pendukung</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">Nomor Surat Tugas / BAST / SK</label>
                <input type="text" name="nomor_surat_tugas"
                       value="{{ old('nomor_surat_tugas', $spp->nomor_surat_tugas) }}"
                       class="input-field" placeholder="Nomor surat tugas">
            </div>

            <div class="input-group">
                <label class="input-label">Tanggal ST / SK</label>
                <input type="date" name="tanggal_st"
                       value="{{ old('tanggal_st', $spp->tanggal_st?->format('Y-m-d')) }}"
                       class="input-field">
            </div>

            <div class="input-group">
                <label class="input-label">Nomor Undangan</label>
                <input type="text" name="nomor_undangan"
                       value="{{ old('nomor_undangan', $spp->nomor_undangan) }}"
                       class="input-field" placeholder="Nomor undangan jika ada">
            </div>

            <div class="input-group">{{-- spacer --}}</div>

            <div class="input-group">
                <label class="input-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai"
                       value="{{ old('tanggal_mulai', $spp->tanggal_mulai?->format('Y-m-d')) }}"
                       class="input-field">
            </div>

            <div class="input-group">
                <label class="input-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai"
                       value="{{ old('tanggal_selesai', $spp->tanggal_selesai?->format('Y-m-d')) }}"
                       class="input-field">
            </div>
        </div>
    </div>

    {{-- ===== NILAI & PAJAK ===== --}}
    <div class="card">
        <div class="section-header mb-4">
            <div>
                <p class="section-title">Nilai & Pajak</p>
                <p class="section-desc">Netto dihitung otomatis</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">Bruto <span class="text-red-500">*</span></label>
                <input type="text" id="bruto_display"
                       class="input-field @error('bruto') input-error @enderror"
                       value="{{ number_format(old('bruto', $spp->bruto), 0, ',', '.') }}"
                       autocomplete="off">
                <input type="hidden" name="bruto" id="bruto" value="{{ old('bruto', $spp->bruto) }}">
                @error('bruto')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">PPN (%)</label>
                <div class="flex gap-2 items-center">
                    <input type="number" id="ppn_percent" class="input-field"
                           placeholder="0" step="0.01" min="0" max="100"
                           value="{{ $spp->bruto > 0 ? round(($spp->ppn / $spp->bruto) * 100, 4) : 0 }}">
                    <span class="text-sm text-gray-500 flex-shrink-0">%</span>
                </div>
                <input type="hidden" name="ppn" id="ppn" value="{{ old('ppn', $spp->ppn) }}">
                <p class="input-hint">Nilai PPN: <span id="ppn_display" class="font-medium text-gray-700 dark:text-gray-300">{{ format_rupiah($spp->ppn) }}</span></p>
            </div>

            <div class="input-group">
                <label class="input-label">PPh (%)</label>
                <div class="flex gap-2 items-center">
                    <input type="number" id="pph_percent" class="input-field"
                           placeholder="0" step="0.01" min="0" max="100"
                           value="{{ $spp->bruto > 0 ? round(($spp->pph / $spp->bruto) * 100, 4) : 0 }}">
                    <span class="text-sm text-gray-500 flex-shrink-0">%</span>
                </div>
                <input type="hidden" name="pph" id="pph" value="{{ old('pph', $spp->pph) }}">
                <p class="input-hint">Nilai PPh: <span id="pph_display" class="font-medium text-gray-700 dark:text-gray-300">{{ format_rupiah($spp->pph) }}</span></p>
            </div>

            <div class="input-group">
                <label class="input-label">Netto <span class="text-red-500">*</span></label>
                <input type="text" id="netto_display"
                       class="input-field-readonly font-semibold"
                       value="{{ number_format(old('netto', $spp->netto), 0, ',', '.') }}"
                       readonly>
                <input type="hidden" name="netto" id="netto" value="{{ old('netto', $spp->netto) }}">
                @error('netto')<p class="input-hint-error">{{ $message }}</p>@enderror
                <p class="input-hint">= Bruto − PPN − PPh</p>
            </div>
        </div>
    </div>

    {{-- ===== STATUS PEMBAYARAN ===== --}}
    <div class="card">
        <div class="section-header mb-4">
            <div>
                <p class="section-title">Status Pembayaran</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">LS / Bendahara <span class="text-red-500">*</span></label>
                <select name="ls_bendahara" class="input-field @error('ls_bendahara') input-error @enderror" required>
                    @foreach($lsBendahara as $ls)
                        <option value="{{ $ls }}" {{ old('ls_bendahara', $spp->ls_bendahara) == $ls ? 'selected' : '' }}>
                            {{ $ls }}
                        </option>
                    @endforeach
                </select>
                @error('ls_bendahara')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Staff PPK</label>
                <input type="text" name="staff_ppk" value="{{ old('staff_ppk', $spp->staff_ppk) }}"
                       class="input-field" placeholder="Nama staff PPK">
            </div>

            <div class="input-group">
                <label class="input-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status"
                        class="input-field @error('status') input-error @enderror"
                        @change="onStatusChange($event.target.value)" required>
                    <option value="Tagihan Belum SP2D" {{ old('status', $spp->status) == 'Tagihan Belum SP2D' ? 'selected' : '' }}>
                        Tagihan Belum SP2D
                    </option>
                    <option value="Tagihan Telah SP2D" {{ old('status', $spp->status) == 'Tagihan Telah SP2D' ? 'selected' : '' }}>
                        Tagihan Telah SP2D
                    </option>
                </select>
                @error('status')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Posisi Uang</label>
                <input type="text" name="posisi_uang" value="{{ old('posisi_uang', $spp->posisi_uang) }}"
                       class="input-field" placeholder="Keterangan posisi uang">
            </div>

            <div x-show="showSP2D" x-transition class="input-group">
                <label class="input-label">No SP2D</label>
                <input type="text" name="no_sp2d" value="{{ old('no_sp2d', $spp->no_sp2d) }}"
                       class="input-field" placeholder="Nomor SP2D">
            </div>

            <div x-show="showSP2D" x-transition class="input-group">
                <label class="input-label">Tanggal SP2D</label>
                <input type="date" name="tgl_sp2d"
                       value="{{ old('tgl_sp2d', $spp->tgl_sp2d?->format('Y-m-d')) }}"
                       class="input-field">
            </div>

            <div x-show="showSP2D" x-transition class="input-group md:col-span-2">
                <label class="input-label">Tanggal Selesai SP2D</label>
                <input type="date" name="tgl_selesai_sp2d"
                       value="{{ old('tgl_selesai_sp2d', $spp->tgl_selesai_sp2d?->format('Y-m-d')) }}"
                       class="input-field">
            </div>
        </div>
    </div>

    {{-- ===== ACTION ===== --}}
    <div class="flex justify-end gap-3">
        <a href="{{ route('anggaran.spp.show', $spp) }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Update SPP
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function sppEditForm() {
    return {
        showSP2D: {{ old('status', $spp->status) == 'Tagihan Telah SP2D' ? 'true' : 'false' }},
        loadingSubkomp: false,
        loadingMak: false,
        sisaInfo: { show: false, sisa: '', pagu: '', outstanding: '', efektif: 0, isWarning: false },

        onStatusChange(val) { this.showSP2D = val === 'Tagihan Telah SP2D'; },

        onRoChange(ro) {
            const subSel = document.getElementById('sub_komponen');
            const makSel = document.getElementById('mak');
            subSel.innerHTML = '<option value="">Pilih Sub Komponen</option>';
            makSel.innerHTML  = '<option value="">Pilih MAK</option>';
            this.sisaInfo.show = false;
            if (!ro) return;
            this.loadingSubkomp = true;
            fetch(`{{ route('anggaran.spp.ajax.subkomponen') }}?ro=${ro}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.error) data.forEach(item => {
                        subSel.add(new Option(`${item.kode_subkomponen} – ${item.program_kegiatan}`, item.kode_subkomponen));
                    });
                })
                .catch(() => showToast('Gagal memuat sub komponen', 'error'))
                .finally(() => this.loadingSubkomp = false);
        },

        onSubkomponenChange(subkomponen) {
            const ro    = document.getElementById('ro').value;
            const makSel = document.getElementById('mak');
            makSel.innerHTML = '<option value="">Pilih MAK</option>';
            this.sisaInfo.show = false;
            if (!ro || !subkomponen) return;
            this.loadingMak = true;
            fetch(`{{ route('anggaran.spp.ajax.akun') }}?ro=${ro}&subkomponen=${subkomponen}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.error) data.forEach(item => {
                        const opt = new Option(`${item.kode_akun} – ${item.program_kegiatan}`, item.kode_akun);
                        opt.dataset.kegiatan    = item.kegiatan ?? '';
                        opt.dataset.kro         = item.kro ?? '';
                        opt.dataset.pagu        = item.pagu_anggaran ?? 0;
                        opt.dataset.sisa        = item.sisa ?? 0;
                        opt.dataset.outstanding = item.tagihan_outstanding ?? 0;
                        opt.dataset.efektif     = item.sisa_efektif ?? 0;
                        makSel.add(opt);
                    });
                })
                .catch(() => showToast('Gagal memuat akun', 'error'))
                .finally(() => this.loadingMak = false);
        },

        onMakChange(event) {
            const opt = event.target.options[event.target.selectedIndex];
            if (opt.dataset.kegiatan) {
                document.getElementById('kode_kegiatan').value = opt.dataset.kegiatan;
                document.getElementById('kro').value           = opt.dataset.kro;
            }
            const efektif = parseFloat(opt.dataset.efektif) || 0;
            if (opt.value) {
                this.sisaInfo = {
                    show: true,
                    sisa: window.formatCurrency(efektif),
                    pagu: window.formatCurrency(parseFloat(opt.dataset.pagu) || 0),
                    outstanding: window.formatCurrency(parseFloat(opt.dataset.outstanding) || 0),
                    efektif,
                    isWarning: efektif < (parseFloat(opt.dataset.pagu) * 0.2),
                };
            } else {
                this.sisaInfo.show = false;
            }
        },

        init() {
            // Show sisa untuk MAK yang sudah ter-select
            const makSel = document.getElementById('mak');
            if (makSel && makSel.value) {
                const opt = makSel.options[makSel.selectedIndex];
                if (opt.dataset.pagu) {
                    const efektif = parseFloat(opt.dataset.efektif) || 0;
                    this.sisaInfo = {
                        show: true,
                        sisa: window.formatCurrency(efektif),
                        pagu: window.formatCurrency(parseFloat(opt.dataset.pagu) || 0),
                        outstanding: window.formatCurrency(parseFloat(opt.dataset.outstanding) || 0),
                        efektif,
                        isWarning: efektif < (parseFloat(opt.dataset.pagu) * 0.2),
                    };
                }
            }

            // Format rupiah
            const brutoDisp    = document.getElementById('bruto_display');
            const brutoHidden  = document.getElementById('bruto');
            const ppnPct       = document.getElementById('ppn_percent');
            const ppnHidden    = document.getElementById('ppn');
            const ppnDisp      = document.getElementById('ppn_display');
            const pphPct       = document.getElementById('pph_percent');
            const pphHidden    = document.getElementById('pph');
            const pphDisp      = document.getElementById('pph_display');
            const nettoDisp    = document.getElementById('netto_display');
            const nettoHidden  = document.getElementById('netto');

            const fmt   = v => new Intl.NumberFormat('id-ID').format(v);
            const unfmt = s => parseInt(String(s).replace(/\D/g,''), 10) || 0;

            const calcNetto = () => {
                const bruto = parseFloat(brutoHidden.value) || 0;
                const ppnV  = bruto * (parseFloat(ppnPct.value) || 0) / 100;
                const pphV  = bruto * (parseFloat(pphPct.value) || 0) / 100;
                const netto = bruto - ppnV - pphV;
                ppnHidden.value   = ppnV.toFixed(2);
                pphHidden.value   = pphV.toFixed(2);
                nettoHidden.value = netto.toFixed(2);
                ppnDisp.textContent  = 'Rp ' + fmt(Math.round(ppnV));
                pphDisp.textContent  = 'Rp ' + fmt(Math.round(pphV));
                nettoDisp.value      = fmt(Math.round(netto));
            };

            brutoDisp.addEventListener('input', e => {
                const v = unfmt(e.target.value);
                brutoHidden.value = v;
                e.target.value    = v ? fmt(v) : '';
                calcNetto();
            });

            ppnPct.addEventListener('input', calcNetto);
            pphPct.addEventListener('input', calcNetto);
            calcNetto(); // initial render
        }
    };
}
</script>
@endpush
