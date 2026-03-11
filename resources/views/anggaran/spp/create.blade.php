@extends('layouts.app')

@section('title', 'Tambah SPP')

@section('breadcrumb')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('anggaran.spp.index') }}" class="breadcrumb-item">Data SPP</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Tambah SPP</span>
</nav>
@endsection

@section('page_header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="page-title">Tambah SPP</h1>
        <p class="page-subtitle">Isi formulir untuk menambah Surat Perintah Pembayaran baru</p>
    </div>
    <a href="{{ route('anggaran.spp.index') }}" class="btn btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('anggaran.spp.store') }}" method="POST"
      x-data="sppForm()" class="space-y-5">
    @csrf

    {{-- ===== INFORMASI DASAR ===== --}}
    <div class="card">
        <div class="section-header">
            <div>
                <p class="section-title">Informasi Dasar</p>
                <p class="section-desc">Data identitas dan detail SPP</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">No SPP <span class="text-red-500">*</span></label>
                <input type="text" name="no_spp" value="{{ old('no_spp') }}"
                       class="input-field @error('no_spp') input-error @enderror"
                       placeholder="Contoh: SPP-001/2025" required>
                @error('no_spp')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Nominatif</label>
                <input type="text" name="nominatif" value="{{ old('nominatif') }}"
                       class="input-field" placeholder="Nama nominatif (opsional)">
            </div>

            <div class="input-group">
                <label class="input-label">Tanggal SPP <span class="text-red-500">*</span></label>
                <input type="date" name="tgl_spp" value="{{ old('tgl_spp') }}"
                       class="input-field @error('tgl_spp') input-error @enderror" required>
                @error('tgl_spp')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Bulan <span class="text-red-500">*</span></label>
                <select name="bulan" class="input-field @error('bulan') input-error @enderror" required>
                    <option value="">Pilih Bulan</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan }}" {{ old('bulan') == $bulan ? 'selected' : '' }}>
                            {{ ucfirst($bulan) }}
                        </option>
                    @endforeach
                </select>
                @error('bulan')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Jenis Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="jenis_kegiatan" value="{{ old('jenis_kegiatan') }}"
                       class="input-field @error('jenis_kegiatan') input-error @enderror"
                       placeholder="Contoh: Perjalanan Dinas" required>
                @error('jenis_kegiatan')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Jenis Belanja <span class="text-red-500">*</span></label>
                <select name="jenis_belanja" class="input-field @error('jenis_belanja') input-error @enderror" required>
                    <option value="">Pilih Jenis Belanja</option>
                    @foreach($jenisBelanja as $jenis)
                        <option value="{{ $jenis }}" {{ old('jenis_belanja') == $jenis ? 'selected' : '' }}>
                            {{ $jenis }}
                        </option>
                    @endforeach
                </select>
                @error('jenis_belanja')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Nomor Kontrak / SPBy</label>
                <input type="text" name="nomor_kontrak" value="{{ old('nomor_kontrak') }}"
                       class="input-field" placeholder="Nomor kontrak jika kontraktual">
            </div>

            <div class="input-group">
                <label class="input-label">No BAST / Kuitansi</label>
                <input type="text" name="no_bast" value="{{ old('no_bast') }}"
                       class="input-field" placeholder="Nomor BAST atau Kuitansi">
            </div>

            <div class="input-group">
                <label class="input-label">ID e-Perjadin</label>
                <input type="text" name="id_eperjadin" value="{{ old('id_eperjadin') }}"
                       class="input-field" placeholder="ID e-Perjadin jika ada">
            </div>

            <div class="input-group">
                <label class="input-label">Bagian <span class="text-red-500">*</span></label>
                <input type="text" name="bagian" value="{{ old('bagian') }}"
                       class="input-field @error('bagian') input-error @enderror"
                       placeholder="Nama bagian" required>
                @error('bagian')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Nama PIC <span class="text-red-500">*</span></label>
                <input type="text" name="nama_pic" value="{{ old('nama_pic') }}"
                       class="input-field @error('nama_pic') input-error @enderror"
                       placeholder="Nama PIC" required>
                @error('nama_pic')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group md:col-span-2">
                <label class="input-label">Uraian SPP <span class="text-red-500">*</span></label>
                <textarea name="uraian_spp" rows="3"
                          class="input-field @error('uraian_spp') input-error @enderror"
                          placeholder="Uraian lengkap kegiatan SPP" required>{{ old('uraian_spp') }}</textarea>
                @error('uraian_spp')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ===== KODE ANGGARAN ===== --}}
    <div class="card">
        <div class="section-header">
            <div>
                <p class="section-title">Kode Anggaran (COA)</p>
                <p class="section-desc">Pilih RO, Sub Komponen, dan MAK untuk menentukan COA</p>
            </div>
            {{-- Info sisa anggaran --}}
            <div x-show="sisaInfo.show"
                 x-transition
                 class="flex-shrink-0">
                <div :class="sisaInfo.isWarning ? 'alert-warning' : 'alert-success'" class="alert text-xs !py-2 !px-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold" x-text="'Sisa: ' + sisaInfo.sisa"></p>
                        <p class="opacity-75" x-text="'Pagu: ' + sisaInfo.pagu + ' | Outstanding: ' + sisaInfo.outstanding"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">Kode Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="kode_kegiatan" id="kode_kegiatan"
                       value="{{ old('kode_kegiatan', '4753') }}"
                       class="input-field @error('kode_kegiatan') input-error @enderror"
                       placeholder="Contoh: 4753" required>
                @error('kode_kegiatan')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">KRO <span class="text-red-500">*</span></label>
                <input type="text" name="kro" id="kro"
                       value="{{ old('kro', 'EBA') }}"
                       class="input-field @error('kro') input-error @enderror"
                       placeholder="Contoh: EBA" required>
                @error('kro')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">RO <span class="text-red-500">*</span></label>
                <select name="ro" id="ro"
                        class="input-field @error('ro') input-error @enderror"
                        @change="onRoChange($event.target.value)" required>
                    <option value="">Pilih RO</option>
                    @foreach($roList as $ro)
                        <option value="{{ $ro }}" {{ old('ro') == $ro ? 'selected' : '' }}>
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
                        @change="onSubkomponenChange($event.target.value)"
                        :disabled="loadingSubkomp" required>
                    <option value="">
                        <span x-show="loadingSubkomp">Memuat…</span>
                        <span x-show="!loadingSubkomp">Pilih Sub Komponen</span>
                    </option>
                </select>
                @error('sub_komponen')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group md:col-span-2">
                <label class="input-label">MAK (Kode Akun) <span class="text-red-500">*</span></label>
                <select name="mak" id="mak"
                        class="input-field @error('mak') input-error @enderror"
                        @change="onMakChange($event)"
                        :disabled="loadingMak" required>
                    <option value="">Pilih MAK</option>
                </select>
                @error('mak')<p class="input-hint-error">{{ $message }}</p>@enderror
                <p class="input-hint">Sisa anggaran efektif akan ditampilkan setelah MAK dipilih</p>
            </div>
        </div>
    </div>

    {{-- ===== DOKUMEN PENDUKUNG ===== --}}
    <div class="card">
        <div class="section-header">
            <div>
                <p class="section-title">Dokumen Pendukung</p>
                <p class="section-desc">Nomor surat, BAST, dan informasi waktu kegiatan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">Nomor Surat Tugas / BAST / SK</label>
                <input type="text" name="nomor_surat_tugas" value="{{ old('nomor_surat_tugas') }}"
                       class="input-field" placeholder="Nomor surat tugas">
            </div>

            <div class="input-group">
                <label class="input-label">Tanggal ST / SK</label>
                <input type="date" name="tanggal_st" value="{{ old('tanggal_st') }}" class="input-field">
            </div>

            <div class="input-group">
                <label class="input-label">Nomor Undangan</label>
                <input type="text" name="nomor_undangan" value="{{ old('nomor_undangan') }}"
                       class="input-field" placeholder="Nomor undangan jika ada">
            </div>

            <div class="input-group">{{-- spacer --}}</div>

            <div class="input-group">
                <label class="input-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="input-field">
            </div>

            <div class="input-group">
                <label class="input-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="input-field">
            </div>
        </div>
    </div>

    {{-- ===== NILAI & PAJAK ===== --}}
    <div class="card">
        <div class="section-header">
            <div>
                <p class="section-title">Nilai & Pajak</p>
                <p class="section-desc">Netto dihitung otomatis dari Bruto dikurangi PPN dan PPh</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">Bruto <span class="text-red-500">*</span></label>
                <input type="text" id="bruto_display"
                       class="input-field @error('bruto') input-error @enderror"
                       placeholder="0" autocomplete="off">
                <input type="hidden" name="bruto" id="bruto" value="{{ old('bruto') }}">
                @error('bruto')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">PPN (%)</label>
                <div class="flex gap-2 items-center">
                    <input type="number" id="ppn_percent" class="input-field"
                           placeholder="0" step="0.01" min="0" max="100">
                    <span class="text-sm text-gray-500 flex-shrink-0">%</span>
                </div>
                <input type="hidden" name="ppn" id="ppn" value="{{ old('ppn', 0) }}">
                <p class="input-hint">Nilai PPN: <span id="ppn_display" class="font-medium text-gray-700 dark:text-gray-300">Rp 0</span></p>
            </div>

            <div class="input-group">
                <label class="input-label">PPh (%)</label>
                <div class="flex gap-2 items-center">
                    <input type="number" id="pph_percent" class="input-field"
                           placeholder="0" step="0.01" min="0" max="100">
                    <span class="text-sm text-gray-500 flex-shrink-0">%</span>
                </div>
                <input type="hidden" name="pph" id="pph" value="{{ old('pph', 0) }}">
                <p class="input-hint">Nilai PPh: <span id="pph_display" class="font-medium text-gray-700 dark:text-gray-300">Rp 0</span></p>
            </div>

            <div class="input-group">
                <label class="input-label">Netto <span class="text-red-500">*</span></label>
                <input type="text" id="netto_display"
                       class="input-field-readonly font-semibold"
                       placeholder="0" readonly>
                <input type="hidden" name="netto" id="netto" value="{{ old('netto') }}">
                @error('netto')<p class="input-hint-error">{{ $message }}</p>@enderror
                <p class="input-hint">= Bruto − PPN − PPh</p>
            </div>
        </div>

        {{-- Warning jika netto melebihi sisa --}}
        <div x-show="nettoMelebihiSisa" x-transition class="mt-4">
            <div class="alert alert-danger">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-sm">Nilai Netto melebihi sisa anggaran efektif! Periksa kembali nilai yang diinput.</p>
            </div>
        </div>
    </div>

    {{-- ===== STATUS PEMBAYARAN ===== --}}
    <div class="card">
        <div class="section-header">
            <div>
                <p class="section-title">Status Pembayaran</p>
                <p class="section-desc">Informasi SP2D dan posisi uang</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="input-group">
                <label class="input-label">LS / Bendahara <span class="text-red-500">*</span></label>
                <select name="ls_bendahara" class="input-field @error('ls_bendahara') input-error @enderror" required>
                    <option value="">Pilih</option>
                    @foreach($lsBendahara as $ls)
                        <option value="{{ $ls }}" {{ old('ls_bendahara') == $ls ? 'selected' : '' }}>{{ $ls }}</option>
                    @endforeach
                </select>
                @error('ls_bendahara')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Staff PPK</label>
                <input type="text" name="staff_ppk" value="{{ old('staff_ppk') }}"
                       class="input-field" placeholder="Nama staff PPK">
            </div>

            <div class="input-group">
                <label class="input-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status"
                        class="input-field @error('status') input-error @enderror"
                        @change="onStatusChange($event.target.value)" required>
                    <option value="">Pilih Status</option>
                    <option value="Tagihan Belum SP2D" {{ old('status') == 'Tagihan Belum SP2D' ? 'selected' : '' }}>
                        Tagihan Belum SP2D
                    </option>
                    <option value="Tagihan Telah SP2D" {{ old('status') == 'Tagihan Telah SP2D' ? 'selected' : '' }}>
                        Tagihan Telah SP2D
                    </option>
                </select>
                @error('status')<p class="input-hint-error">{{ $message }}</p>@enderror
            </div>

            <div class="input-group">
                <label class="input-label">Posisi Uang</label>
                <input type="text" name="posisi_uang" value="{{ old('posisi_uang') }}"
                       class="input-field" placeholder="Keterangan posisi uang">
            </div>

            {{-- SP2D Fields (conditional) --}}
            <div x-show="showSP2D" x-transition class="input-group">
                <label class="input-label">No SP2D</label>
                <input type="text" name="no_sp2d" value="{{ old('no_sp2d') }}"
                       class="input-field" placeholder="Nomor SP2D">
            </div>

            <div x-show="showSP2D" x-transition class="input-group">
                <label class="input-label">Tanggal SP2D</label>
                <input type="date" name="tgl_sp2d" value="{{ old('tgl_sp2d') }}" class="input-field">
            </div>

            <div x-show="showSP2D" x-transition class="input-group md:col-span-2">
                <label class="input-label">Tanggal Selesai SP2D</label>
                <input type="date" name="tgl_selesai_sp2d" value="{{ old('tgl_selesai_sp2d') }}" class="input-field">
            </div>
        </div>
    </div>

    {{-- ===== ACTION BUTTONS ===== --}}
    <div class="flex justify-end gap-3">
        <a href="{{ route('anggaran.spp.index') }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan SPP
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
function sppForm() {
    return {
        showSP2D: {{ old('status') == 'Tagihan Telah SP2D' ? 'true' : 'false' }},
        loadingSubkomp: false,
        loadingMak: false,
        sisaInfo: { show: false, sisa: '', pagu: '', outstanding: '', efektif: 0, isWarning: false },
        nettoMelebihiSisa: false,

        // ── COA Cascade ──────────────────────────────────────
        onRoChange(ro) {
            const subSelect = document.getElementById('sub_komponen');
            const makSelect = document.getElementById('mak');
            subSelect.innerHTML = '<option value="">Pilih Sub Komponen</option>';
            makSelect.innerHTML = '<option value="">Pilih MAK</option>';
            this.sisaInfo.show = false;
            if (!ro) return;

            this.loadingSubkomp = true;
            fetch(`{{ route('anggaran.spp.ajax.subkomponen') }}?ro=${ro}`)
                .then(r => r.json())
                .then(data => {
                    subSelect.innerHTML = '<option value="">Pilih Sub Komponen</option>';
                    if (!data.error && data.length) {
                        data.forEach(item => {
                            const opt = new Option(
                                `${item.kode_subkomponen} – ${item.program_kegiatan}`,
                                item.kode_subkomponen
                            );
                            subSelect.add(opt);
                        });
                    }
                })
                .catch(() => showToast('Gagal memuat sub komponen', 'error'))
                .finally(() => this.loadingSubkomp = false);
        },

        onSubkomponenChange(subkomponen) {
            const ro = document.getElementById('ro').value;
            const makSelect = document.getElementById('mak');
            makSelect.innerHTML = '<option value="">Pilih MAK</option>';
            this.sisaInfo.show = false;
            if (!ro || !subkomponen) return;

            this.loadingMak = true;
            fetch(`{{ route('anggaran.spp.ajax.akun') }}?ro=${ro}&subkomponen=${subkomponen}`)
                .then(r => r.json())
                .then(data => {
                    makSelect.innerHTML = '<option value="">Pilih MAK</option>';
                    if (!data.error && data.length) {
                        data.forEach(item => {
                            const opt = new Option(
                                `${item.kode_akun} – ${item.program_kegiatan}`,
                                item.kode_akun
                            );
                            opt.dataset.kegiatan   = item.kegiatan ?? '';
                            opt.dataset.kro        = item.kro ?? '';
                            opt.dataset.pagu       = item.pagu_anggaran ?? 0;
                            opt.dataset.sisa       = item.sisa ?? 0;
                            opt.dataset.outstanding = item.tagihan_outstanding ?? 0;
                            opt.dataset.efektif    = item.sisa_efektif ?? 0;
                            makSelect.add(opt);
                        });
                    }
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
            if (opt.value && efektif >= 0) {
                const pagu        = parseFloat(opt.dataset.pagu) || 0;
                const outstanding = parseFloat(opt.dataset.outstanding) || 0;
                const sisa        = parseFloat(opt.dataset.sisa) || 0;
                this.sisaInfo = {
                    show: true,
                    sisa: window.formatCurrency(efektif),
                    pagu: window.formatCurrency(pagu),
                    outstanding: window.formatCurrency(outstanding),
                    efektif,
                    isWarning: efektif < (pagu * 0.2),
                };
                this.checkNettoLimit();
            } else {
                this.sisaInfo.show = false;
            }
        },

        // ── Status SP2D ──────────────────────────────────────
        onStatusChange(val) {
            this.showSP2D = val === 'Tagihan Telah SP2D';
        },

        // ── Netto Warning ────────────────────────────────────
        checkNettoLimit() {
            const netto = parseFloat(document.getElementById('netto').value) || 0;
            this.nettoMelebihiSisa = this.sisaInfo.efektif > 0 && netto > this.sisaInfo.efektif;
        },

        init() {
            // Format rupiah pada bruto display
            const brutoDisplay = document.getElementById('bruto_display');
            const brutoHidden  = document.getElementById('bruto');
            const ppnPct       = document.getElementById('ppn_percent');
            const ppnHidden    = document.getElementById('ppn');
            const ppnDisp      = document.getElementById('ppn_display');
            const pphPct       = document.getElementById('pph_percent');
            const pphHidden    = document.getElementById('pph');
            const pphDisp      = document.getElementById('pph_display');
            const nettoDisp    = document.getElementById('netto_display');
            const nettoHidden  = document.getElementById('netto');

            const fmt    = v => new Intl.NumberFormat('id-ID').format(v);
            const unfmt  = s => parseInt(s.replace(/\D/g,''), 10) || 0;

            const calcNetto = () => {
                const bruto = parseFloat(brutoHidden.value) || 0;
                const ppnV  = bruto * (parseFloat(ppnPct.value) || 0) / 100;
                const pphV  = bruto * (parseFloat(pphPct.value) || 0) / 100;
                const netto = bruto - ppnV - pphV;
                ppnHidden.value  = ppnV.toFixed(2);
                pphHidden.value  = pphV.toFixed(2);
                nettoHidden.value = netto.toFixed(2);
                ppnDisp.textContent  = 'Rp ' + fmt(Math.round(ppnV));
                pphDisp.textContent  = 'Rp ' + fmt(Math.round(pphV));
                nettoDisp.value      = fmt(Math.round(netto));
                this.checkNettoLimit();
            };

            brutoDisplay.addEventListener('input', e => {
                const v = unfmt(e.target.value);
                brutoHidden.value = v;
                e.target.value    = v ? fmt(v) : '';
                calcNetto();
            });

            // Restore old value
            if (brutoHidden.value) {
                brutoDisplay.value = fmt(parseInt(brutoHidden.value) || 0);
                calcNetto();
            }

            ppnPct.addEventListener('input', calcNetto);
            pphPct.addEventListener('input', calcNetto);
        }
    };
}
</script>
@endpush
