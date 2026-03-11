@extends('layouts.app')
@section('title', 'Tambah Data Anggaran')
@section('subtitle', 'Tambah master data pagu anggaran baru')

@section('breadcrumb')
<nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li><a href="{{ route('anggaran.data.index') }}" class="breadcrumb-item">Kelola Data Anggaran</a></li>
        <li><svg class="w-3.5 h-3.5 breadcrumb-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
        <li><span class="breadcrumb-current">Tambah Data</span></li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-4xl">
<form action="{{ route('anggaran.data.store') }}" method="POST" id="formAnggaran" novalidate>
    @csrf
    <div class="space-y-5">

        {{-- ===== PANDUAN ===== --}}
        <div class="alert-info rounded-xl p-4">
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-navy-200/60 dark:bg-navy-700 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-navy-700 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-navy-800 dark:text-navy-200 text-sm mb-2">Panduan Hierarki Anggaran</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-2">
                        <div class="flex items-start gap-2 text-xs text-navy-700 dark:text-navy-400">
                            <span class="badge badge-blue flex-shrink-0 mt-0.5">RO</span>
                            <span>Level tertinggi. Pagu dihitung otomatis dari Sub Komponen.</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-navy-700 dark:text-navy-400">
                            <span class="badge badge-purple flex-shrink-0 mt-0.5">Sub Komp</span>
                            <span>Di bawah RO. Pagu dihitung otomatis dari Akun.</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-navy-700 dark:text-navy-400">
                            <span class="badge badge-green flex-shrink-0 mt-0.5">Akun</span>
                            <span>Level detail. <strong>Hanya level ini yang input pagu.</strong></span>
                        </div>
                    </div>
                    <p class="text-xs text-navy-600 dark:text-navy-500">
                        ⚡ Urutan input yang disarankan:
                        <span class="font-semibold">RO → Sub Komponen → Akun</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== SECTION 1: KODE IDENTIFIKASI ===== --}}
        <div class="card">
            {{-- Section header --}}
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-navy-700">
                <div class="w-7 h-7 rounded-lg bg-navy-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-white">1</span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Kode Identifikasi</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kegiatan, KRO, RO, dan level hierarki</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Kode Kegiatan --}}
                <div class="input-group">
                    <label class="input-label" for="kegiatan">
                        Kode Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kegiatan" name="kegiatan"
                           value="{{ old('kegiatan', '4753') }}"
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
                           value="{{ old('kro', 'EBA') }}"
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
                        <option value="">— Pilih RO —</option>
                        @foreach($roList as $ro)
                            <option value="{{ $ro }}" {{ old('ro') == $ro ? 'selected' : '' }}>
                                {{ $ro }} – {{ get_ro_name($ro) }}
                            </option>
                        @endforeach
                    </select>
                    @error('ro')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Level --}}
                <div class="input-group">
                    <label class="input-label" for="level_select">
                        Level Item <span class="text-red-500">*</span>
                    </label>
                    <select id="level_select" class="input-field">
                        <option value="">— Pilih Level —</option>
                        <option value="ro">RO (Parent)</option>
                        <option value="subkomponen">Sub Komponen</option>
                        <option value="akun">Akun (Detail)</option>
                    </select>
                    <p class="input-hint" id="level_hint">Tentukan posisi item dalam hierarki anggaran</p>
                </div>

                {{-- Sub Komponen (muncul kondisional) --}}
                <div class="input-group" id="subkomponen_group" style="display:none;">
                    <label class="input-label" id="subkomp_label">
                        Kode Sub Komponen <span class="text-red-500">*</span>
                    </label>
                    {{-- Dropdown: untuk level akun --}}
                    <select id="kode_subkomponen_select" name="_subkomp_select"
                            class="input-field @error('kode_subkomponen') input-error @enderror"
                            style="display:none;">
                        <option value="">— Pilih Sub Komponen —</option>
                    </select>
                    {{-- Input manual: untuk level subkomponen --}}
                    <input type="text" id="kode_subkomponen_input"
                           class="input-field font-mono @error('kode_subkomponen') input-error @enderror"
                           placeholder="Contoh: AA, AB"
                           style="display:none; text-transform:uppercase;" maxlength="10">
                    {{-- Hidden final value --}}
                    <input type="hidden" name="kode_subkomponen" id="kode_subkomponen_final"
                           value="{{ old('kode_subkomponen') }}">
                    <p class="input-hint" id="subkomp_hint"></p>
                    @error('kode_subkomponen')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kode Akun (muncul kondisional) --}}
                <div class="input-group" id="akun_group" style="display:none;">
                    <label class="input-label" for="kode_akun">
                        Kode Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kode_akun" name="kode_akun"
                           value="{{ old('kode_akun') }}"
                           class="input-field font-mono @error('kode_akun') input-error @enderror"
                           placeholder="Contoh: 521211, 524111">
                    <p class="input-hint">6 digit kode akun belanja</p>
                    @error('kode_akun')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Level indicator --}}
            <div id="level_indicator" class="mt-4" style="display:none;">
                <div class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 dark:bg-navy-900/40 border border-gray-100 dark:border-navy-700">
                    <div id="level_badge_container"></div>
                    <p class="text-sm text-gray-600 dark:text-gray-400" id="level_desc_text"></p>
                </div>
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
                    <p class="text-xs text-gray-500 dark:text-gray-400">Uraian kegiatan, PIC, dan pagu anggaran</p>
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
                              placeholder="Tuliskan uraian lengkap program/kegiatan/output/komponen/akun..."
                              required>{{ old('program_kegiatan') }}</textarea>
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
                           value="{{ old('pic', 'SJ.7') }}"
                           class="input-field @error('pic') input-error @enderror"
                           placeholder="Penanggung jawab anggaran" required>
                    <p class="input-hint">Kode unit / nama PIC penanggung jawab</p>
                    @error('pic')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pagu Anggaran --}}
                <div class="input-group" id="pagu_group">
                    <label class="input-label" for="pagu_anggaran">
                        Pagu Anggaran
                        <span class="text-red-500" id="pagu_required_mark" style="display:none;">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="text-sm font-medium text-gray-400 dark:text-gray-500">Rp</span>
                        </div>
                        <input type="number" id="pagu_anggaran" name="pagu_anggaran"
                               value="{{ old('pagu_anggaran') }}"
                               class="input-field pl-10 @error('pagu_anggaran') input-error @enderror"
                               placeholder="0" step="1" min="0">
                    </div>
                    <p class="input-hint" id="pagu_hint">Pilih level terlebih dahulu</p>
                    @error('pagu_anggaran')
                        <p class="input-hint-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===== ACTIONS ===== --}}
        <div class="flex items-center justify-between pt-1">
            <a href="{{ route('anggaran.data.index') }}"
               class="btn btn-ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <div class="flex gap-3">
                <a href="{{ route('anggaran.data.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Data
                </button>
            </div>
        </div>

    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Elemen ── */
    const levelSelect   = document.getElementById('level_select');
    const roSelect      = document.getElementById('ro');
    const subkompGroup  = document.getElementById('subkomponen_group');
    const akunGroup     = document.getElementById('akun_group');
    const subkompSelect = document.getElementById('kode_subkomponen_select');
    const subkompInput  = document.getElementById('kode_subkomponen_input');
    const subkompFinal  = document.getElementById('kode_subkomponen_final');
    const subkompHint   = document.getElementById('subkomp_hint');
    const akunInput     = document.getElementById('kode_akun');
    const paguInput     = document.getElementById('pagu_anggaran');
    const paguRequired  = document.getElementById('pagu_required_mark');
    const paguHint      = document.getElementById('pagu_hint');
    const levelIndicator    = document.getElementById('level_indicator');
    const levelBadgeCont    = document.getElementById('level_badge_container');
    const levelDescText     = document.getElementById('level_desc_text');

    /* ── Config per level ── */
    const levelConfig = {
        ro: {
            badge : '<span class="badge badge-blue">RO</span>',
            desc  : 'Level RO: pagu anggaran dihitung otomatis dari total Sub Komponen di bawahnya.',
            pagu  : { readonly: true, msg: '🔒 Dihitung otomatis dari Sub Komponen' },
        },
        subkomponen: {
            badge : '<span class="badge badge-purple">Sub Komponen</span>',
            desc  : 'Level Sub Komponen: pagu dihitung otomatis dari total Akun di bawahnya.',
            pagu  : { readonly: true, msg: '🔒 Dihitung otomatis dari Akun' },
        },
        akun: {
            badge : '<span class="badge badge-green">Akun</span>',
            desc  : 'Level Akun: masukkan pagu anggaran secara manual.',
            pagu  : { readonly: false },
        },
    };

    /* ── Helpers ── */
    function resetSubkomp() {
        subkompGroup.style.display  = 'none';
        subkompSelect.style.display = 'none';
        subkompInput.style.display  = 'none';
        subkompSelect.removeAttribute('required');
        subkompInput.removeAttribute('required');
        subkompFinal.value = '';
        subkompHint.textContent = '';
    }

    function setPagu(config) {
        if (config.readonly) {
            paguInput.readOnly  = true;
            paguInput.value     = '';
            paguInput.classList.add('input-field-readonly');
            paguInput.removeAttribute('required');
            paguRequired.style.display = 'none';
            paguHint.innerHTML  = `<span class="text-amber-500 dark:text-amber-400">${config.msg}</span>`;
        } else {
            paguInput.readOnly  = false;
            paguInput.classList.remove('input-field-readonly');
            paguInput.setAttribute('required', 'required');
            paguRequired.style.display = 'inline';
            paguHint.innerHTML  = '<span class="text-green-600 dark:text-green-400">✏ Masukkan pagu anggaran untuk akun ini</span>';
        }
    }

    function showLevelIndicator(level) {
        const cfg = levelConfig[level];
        if (!cfg) { levelIndicator.style.display = 'none'; return; }
        levelBadgeCont.innerHTML = cfg.badge;
        levelDescText.textContent = cfg.desc;
        levelIndicator.style.display = 'block';
    }

    /* ── Level change ── */
    levelSelect.addEventListener('change', function () {
        const level = this.value;
        const ro    = roSelect.value;

        resetSubkomp();
        akunGroup.style.display = 'none';
        akunInput.removeAttribute('required');
        showLevelIndicator(level);

        if (level === 'ro') {
            setPagu(levelConfig.ro.pagu);

        } else if (level === 'subkomponen') {
            subkompGroup.style.display  = 'block';
            subkompInput.style.display  = 'block';
            subkompInput.setAttribute('required', 'required');
            subkompHint.textContent = 'Ketik kode baru (2 karakter, contoh: AA, AB)';
            setPagu(levelConfig.subkomponen.pagu);

        } else if (level === 'akun') {
            subkompGroup.style.display = 'block';
            akunGroup.style.display    = 'block';
            akunInput.setAttribute('required', 'required');
            setPagu(levelConfig.akun.pagu);

            if (ro) {
                loadSubkomponen(ro);
            } else {
                subkompSelect.innerHTML     = '<option value="">Pilih RO terlebih dahulu</option>';
                subkompSelect.style.display = 'block';
                subkompHint.innerHTML       = '<span class="text-amber-500">⚠ Pilih RO terlebih dahulu</span>';
            }

        } else {
            // Reset pagu
            paguInput.readOnly  = false;
            paguInput.classList.remove('input-field-readonly');
            paguInput.removeAttribute('required');
            paguRequired.style.display = 'none';
            paguHint.innerHTML  = '<span class="text-gray-500 dark:text-gray-400">Pilih level terlebih dahulu</span>';
            levelIndicator.style.display = 'none';
        }
    });

    /* ── RO change ── */
    roSelect.addEventListener('change', function () {
        if (levelSelect.value === 'akun' && this.value) {
            loadSubkomponen(this.value);
        }
    });

    /* ── Load subkomponen via AJAX ── */
    function loadSubkomponen(ro) {
        subkompSelect.innerHTML     = '<option value="">Memuat...</option>';
        subkompSelect.style.display = 'block';
        subkompSelect.disabled      = true;
        subkompHint.innerHTML       = '<span class="text-gray-400">Memuat daftar sub komponen...</span>';

        fetch(`{{ route('anggaran.data.ajax.subkomponen') }}?ro=${encodeURIComponent(ro)}`)
            .then(r => r.ok ? r.json() : Promise.reject(r))
            .then(data => {
                subkompSelect.disabled  = false;
                subkompSelect.innerHTML = '<option value="">— Pilih Sub Komponen —</option>';

                if (data.error) {
                    subkompHint.innerHTML = `<span class="text-red-500">Error: ${data.error}</span>`;
                    return;
                }
                if (!data.length) {
                    subkompHint.innerHTML = '<span class="text-amber-500">⚠ Belum ada sub komponen. Buat Sub Komponen dahulu.</span>';
                    return;
                }
                data.forEach(item => {
                    const opt = new Option(
                        `${item.kode_subkomponen} – ${item.program_kegiatan}`,
                        item.kode_subkomponen
                    );
                    subkompSelect.add(opt);
                });
                subkompSelect.setAttribute('required', 'required');
                subkompHint.innerHTML = `<span class="text-green-600 dark:text-green-400">${data.length} sub komponen tersedia</span>`;
            })
            .catch(() => {
                subkompSelect.disabled  = false;
                subkompSelect.innerHTML = '<option value="">— Gagal memuat —</option>';
                subkompHint.innerHTML   = '<span class="text-red-500">Gagal memuat data. Coba lagi.</span>';
            });
    }

    /* ── Sync hidden inputs ── */
    subkompSelect.addEventListener('change', () => subkompFinal.value = subkompSelect.value);
    subkompInput.addEventListener('input', function () {
        this.value         = this.value.toUpperCase();
        subkompFinal.value = this.value;
    });

    /* ── Format pagu display (opsional: tampilkan preview Rp) ── */
    const paguPreview = document.createElement('p');
    paguPreview.className = 'text-xs font-medium text-navy-600 dark:text-navy-400 mt-1';
    paguInput.parentElement.parentElement.appendChild(paguPreview);

    paguInput.addEventListener('input', function () {
        const val = parseInt(this.value.replace(/\D/g, ''), 10) || 0;
        paguPreview.textContent = val > 0
            ? '≈ ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val)
            : '';
    });

    /* ── Submit validation ── */
    document.getElementById('formAnggaran').addEventListener('submit', function (e) {
        const level = levelSelect.value;

        if (!level) {
            e.preventDefault();
            showToast('Pilih Level Item terlebih dahulu!', 'warning');
            levelSelect.focus();
            return;
        }
        if (level === 'akun' && !subkompFinal.value) {
            e.preventDefault();
            showToast('Pilih Sub Komponen terlebih dahulu!', 'warning');
            subkompSelect.focus();
            return;
        }
        if (level === 'akun' && (!paguInput.value || parseInt(paguInput.value) < 0)) {
            e.preventDefault();
            showToast('Masukkan Pagu Anggaran untuk level Akun!', 'warning');
            paguInput.focus();
            return;
        }

        // Loading state pada tombol submit
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
@endpush
