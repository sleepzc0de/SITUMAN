@extends('layouts.app')
@section('title', 'Edit Usulan Penarikan Dana')
@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('anggaran.usulan.index') }}" class="breadcrumb-item">
            Usulan Penarikan Dana
        </a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('anggaran.usulan.show', $usulan) }}" class="breadcrumb-item">
            Detail
        </a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Edit</span>
    </nav>

    {{-- Alert --}}
    <div class="alert alert-warning">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732
                     4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold">Usulan Masih Menunggu Persetujuan</p>
            <p class="text-sm mt-0.5">
                Perubahan hanya bisa dilakukan selama status masih <strong>Pending</strong>.
            </p>
        </div>
    </div>

    <form action="{{ route('anggaran.usulan.update', $usulan) }}"
          method="POST"
          id="form-edit-usulan"
          x-data="usulanEditForm()"
          @submit.prevent="submitForm()"
          autocomplete="off"
          novalidate>
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===== FORM UTAMA ===== --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="card">
                    <div class="section-header">
                        <div>
                            <h3 class="section-title">Ubah Data Usulan</h3>
                            <p class="section-desc">
                                Diajukan {{ format_tanggal_short($usulan->created_at) }}
                                oleh {{ e($usulan->user->nama ?? '-') }}
                            </p>
                        </div>
                        <span class="badge badge-pending">Menunggu</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- ── RO ───────────────────────────────────── --}}
                        <div class="input-group">
                            <label class="input-label">
                                RO <span class="text-red-500">*</span>
                            </label>
                            <select name="ro"
                                    id="select-ro"
                                    x-model="ro"
                                    @change="onRoChange()"
                                    class="input-field @error('ro') input-error @enderror"
                                    required>
                                <option value="">— Pilih RO —</option>
                                @foreach($roList as $roItem)
                                    <option value="{{ e($roItem) }}"
                                            {{ old('ro', $usulan->ro) === $roItem ? 'selected' : '' }}>
                                        {{ e($roItem) }} – {{ e(get_ro_name($roItem)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ro')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ── Sub Komponen ─────────────────────────── --}}
                        {{--
                            SECURITY:
                            - Option di-render server-side dari DB (bukan hanya AJAX)
                            - Setiap kali RO berubah, select di-rebuild via JS dengan
                              data dari server AJAX yang juga divalidasi
                            - Server (controller) memvalidasi nilai dengan Rule::in()
                              berdasarkan query DB langsung
                        --}}
                        <div class="input-group">
                            <label class="input-label">
                                Sub Komponen <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="sub_komponen"
                                        id="select-subkomponen"
                                        x-model="subKomponen"
                                        @change="onSubkompChange($event.target.value)"
                                        class="input-field @error('sub_komponen') input-error @enderror"
                                        :disabled="loading || !ro"
                                        required>
                                    <option value="">— Pilih Sub Komponen —</option>
                                    {{--
                                        Server-side render option awal dari DB.
                                        Ini memastikan nilai valid sejak halaman dimuat.
                                    --}}
                                    @foreach($subkomponenList as $subItem)
                                        <option value="{{ e($subItem->kode_subkomponen) }}"
                                                {{ old('sub_komponen', $usulan->sub_komponen) === $subItem->kode_subkomponen ? 'selected' : '' }}>
                                            {{ e($subItem->kode_subkomponen) }} – {{ e($subItem->program_kegiatan) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div x-show="loading"
                                     class="absolute right-3 top-3"
                                     style="display:none;">
                                    <svg class="w-4 h-4 text-navy-500 animate-spin"
                                         fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('sub_komponen')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ── Bulan ────────────────────────────────── --}}
                        <div class="input-group">
                            <label class="input-label">
                                Bulan <span class="text-red-500">*</span>
                            </label>
                            <select name="bulan"
                                    id="select-bulan"
                                    class="input-field @error('bulan') input-error @enderror"
                                    required>
                                <option value="">— Pilih Bulan —</option>
                                @foreach($bulanList as $bulanItem)
                                    <option value="{{ e($bulanItem) }}"
                                            {{ old('bulan', $usulan->bulan) === $bulanItem ? 'selected' : '' }}>
                                        {{ ucfirst(e($bulanItem)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bulan')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ── Nilai Usulan ─────────────────────────── --}}
                        <div class="input-group">
                            <label class="input-label">
                                Nilai Usulan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2
                                             text-sm text-gray-400 font-medium pointer-events-none">
                                    Rp
                                </span>
                                <input type="text"
                                       id="nilai_display"
                                       class="input-field pl-9 @error('nilai_usulan') input-error @enderror"
                                       :class="{
                                           '!border-red-400 !ring-red-100 dark:!ring-red-900/30':
                                               sisaAnggaranWarning === 'danger',
                                           '!border-yellow-400 !ring-yellow-100 dark:!ring-yellow-900/30':
                                               sisaAnggaranWarning === 'warning'
                                       }"
                                       placeholder="0"
                                       @input="onNilaiInput($event)"
                                       x-init="$el.value = nilaiUsulan > 0
                                           ? parseInt(nilaiUsulan).toLocaleString('id-ID') : ''"
                                       inputmode="numeric"
                                       autocomplete="off">
                                <input type="hidden"
                                       name="nilai_usulan"
                                       id="nilai_usulan_hidden"
                                       :value="nilaiUsulan">
                            </div>
                            <p x-show="sisaAnggaranWarning === 'danger'"
                               class="input-hint-error"
                               style="display:none;">
                                ⚠ Nilai melebihi sisa anggaran sub komponen
                            </p>
                            <p x-show="sisaAnggaranWarning === 'warning'"
                               class="input-hint-warning"
                               style="display:none;">
                                ⚠ Nilai mendekati batas sisa anggaran (&gt;80%)
                            </p>
                            @error('nilai_usulan')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ── Keterangan ───────────────────────────── --}}
                        <div class="input-group md:col-span-2">
                            <label class="input-label">Keterangan</label>
                            <textarea name="keterangan"
                                      rows="3"
                                      maxlength="500"
                                      class="input-field @error('keterangan') input-error @enderror"
                                      placeholder="Jelaskan keperluan penarikan dana ini...">{{ old('keterangan', e($usulan->keterangan)) }}</textarea>
                            @error('keterangan')
                                <p class="input-hint-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('anggaran.usulan.show', $usulan) }}"
                       class="btn btn-outline">
                        Batal
                    </a>
                    <button type="submit"
                            :disabled="submitting"
                            class="btn btn-primary">
                        <svg x-show="!submitting"
                             class="w-4 h-4"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="submitting"
                             class="w-4 h-4 animate-spin"
                             fill="none" viewBox="0 0 24 24"
                             style="display:none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Perubahan'">
                            Simpan Perubahan
                        </span>
                    </button>
                </div>
            </div>

            {{-- ===== PANEL INFO ANGGARAN ===== --}}
            <div class="space-y-4">

                <div class="card" x-show="selectedInfo" style="display:none;" x-transition>
                    <h4 class="section-title mb-3">📊 Info Anggaran Sub Komponen</h4>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">
                                Program Kegiatan
                            </p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-2"
                               x-text="selectedInfo?.program_kegiatan ?? '-'"></p>
                        </div>
                        <div class="grid grid-cols-1 gap-2 pt-2
                                    border-t border-gray-100 dark:border-navy-700">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Pagu Anggaran</span>
                                <span class="text-xs font-semibold
                                             text-gray-700 dark:text-gray-300 tabular-nums"
                                      x-text="selectedInfo
                                          ? 'Rp ' + parseInt(selectedInfo.pagu_anggaran).toLocaleString('id-ID')
                                          : '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Terpakai</span>
                                <span class="text-xs font-semibold
                                             text-emerald-600 dark:text-emerald-400 tabular-nums"
                                      x-text="selectedInfo
                                          ? 'Rp ' + parseInt(selectedInfo.total_penyerapan).toLocaleString('id-ID')
                                          : '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Sisa Anggaran</span>
                                <span class="text-xs font-bold tabular-nums"
                                      :class="parseFloat(selectedInfo?.sisa)
                                              parseFloat(selectedInfo?.pagu_anggaran) * 0.2
                                          ? 'text-red-600 dark:text-red-400'
                                          : 'text-navy-700 dark:text-navy-300'"
                                      x-text="selectedInfo
                                          ? 'Rp ' + parseInt(selectedInfo.sisa).toLocaleString('id-ID')
                                          : '-'"></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500">Penyerapan</span>
                                <span class="text-xs font-semibold"
                                      x-text="selectedInfo && selectedInfo.pagu_anggaran > 0
                                          ? (parseFloat(selectedInfo.total_penyerapan) /
                                             parseFloat(selectedInfo.pagu_anggaran) * 100).toFixed(1) + '%'
                                          : '0%'"></span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar bg-navy-500"
                                     :style="selectedInfo && selectedInfo.pagu_anggaran > 0
                                         ? 'width:' + Math.min(
                                             parseFloat(selectedInfo.total_penyerapan) /
                                             parseFloat(selectedInfo.pagu_anggaran) * 100, 100
                                           ) + '%'
                                         : 'width:0%'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" x-show="!selectedInfo" x-transition>
                    <div class="empty-state py-8">
                        <div class="empty-state-icon">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12
                                         11h.01M15 11h.01M12 7h.01M3 5a2 2 0 012-2h14a2
                                         2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                            </svg>
                        </div>
                        <p class="empty-state-title text-sm">Info Anggaran</p>
                        <p class="empty-state-desc text-xs">
                            Pilih Sub Komponen untuk melihat sisa anggaran
                        </p>
                    </div>
                </div>

                <div class="card">
                    <h4 class="section-title mb-3">🕒 Riwayat Usulan</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-500">ID Usulan</span>
                            <span class="font-mono text-gray-700 dark:text-gray-300">
                                {{ e(substr($usulan->id, 0, 8)) }}...
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ format_datetime($usulan->created_at) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ format_datetime($usulan->updated_at) }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function usulanEditForm() {
    // ── Data dari server (Blade auto-escape) ──────────────────
    const initialRo          = @json(old('ro', $usulan->ro));
    const initialSubKomponen = @json(old('sub_komponen', $usulan->sub_komponen));
    const initialNilai       = @json(old('nilai_usulan', $usulan->nilai_usulan));

    // ── Whitelist dari server — sumber kebenaran tunggal ──────
    const VALID_RO_VALUES    = @json($roList);
    const VALID_BULAN_VALUES = @json($bulanList);

    // ── Data subkomponen awal dari server (server-side render) ─
    // Ini mencegah kondisi race antara halaman load dan AJAX selesai
    const INITIAL_SUBKOMPONEN = @json($subkomponenList);

    return {
        ro:            initialRo    ?? '',
        subKomponen:   initialSubKomponen ?? '',
        subkomponens:  [],
        loading:       false,
        submitting:    false,
        selectedInfo:  null,
        nilaiUsulan:   initialNilai
                           ? parseInt(String(initialNilai).replace(/\D/g, '')) || 0
                           : 0,

        // ── Init ──────────────────────────────────────────────
        init() {
            // Set tampilan nilai awal
            if (this.nilaiUsulan > 0) {
                this.$nextTick(() => {
                    const el = document.getElementById('nilai_display');
                    if (el) el.value = this.nilaiUsulan.toLocaleString('id-ID');
                });
            }

            // Gunakan data subkomponen dari server (sudah di-render Blade)
            // Tidak perlu AJAX lagi saat init — lebih aman
            if (INITIAL_SUBKOMPONEN && INITIAL_SUBKOMPONEN.length > 0) {
                // Convert format dari Eloquent collection ke format yang dipakai JS
                this.subkomponens = INITIAL_SUBKOMPONEN.map(item => ({
                    kode_subkomponen: item.kode_subkomponen,
                    program_kegiatan: item.program_kegiatan,
                    pagu_anggaran:    item.pagu_anggaran,
                    sisa:             item.sisa,
                    total_penyerapan: item.total_penyerapan,
                }));

                // Set selectedInfo untuk nilai yang sudah tersimpan
                if (this.subKomponen) {
                    this.selectedInfo = this.subkomponens.find(
                        s => s.kode_subkomponen === this.subKomponen
                    ) || null;
                }
            }
        },

        // ── Whitelist Checks ──────────────────────────────────
        isValidRo(value) {
            return VALID_RO_VALUES.includes(value);
        },

        isValidBulan(value) {
            return VALID_BULAN_VALUES.includes(value);
        },

        // Cek apakah sub_komponen valid berdasarkan data yang sudah dimuat
        isValidSubkomponen(value) {
            if (!value) return false;
            return this.subkomponens.some(s => s.kode_subkomponen === value);
        },

        // ── Event Handlers ─────────────────────────────────────
        onRoChange() {
            // Reject jika RO tidak ada di whitelist
            if (this.ro && !this.isValidRo(this.ro)) {
                this.ro = '';
                document.getElementById('select-ro').value = '';
                this.clearSubkomponen();
                if (typeof showToast === 'function') {
                    showToast('Nilai RO tidak valid.', 'error');
                }
                return;
            }

            this.clearSubkomponen();

            if (this.ro) {
                this.fetchSubkomponen();
            }
        },

        onSubkompChange(val) {
            // Reject jika nilai tidak ada di list yang dimuat dari server
            if (val && !this.isValidSubkomponen(val)) {
                this.subKomponen = '';
                document.getElementById('select-subkomponen').value = '';
                this.selectedInfo = null;
                if (typeof showToast === 'function') {
                    showToast('Nilai sub komponen tidak valid.', 'error');
                }
                return;
            }

            this.subKomponen  = val;
            this.selectedInfo = val
                ? this.subkomponens.find(s => s.kode_subkomponen === val) || null
                : null;
        },

        onNilaiInput(event) {
            // Hanya terima digit
            const raw    = event.target.value.replace(/\D/g, '');
            const numVal = raw ? parseInt(raw, 10) : 0;

            this.nilaiUsulan   = numVal;
            event.target.value = numVal > 0 ? numVal.toLocaleString('id-ID') : '';
        },

        // ── Clear subkomponen state ────────────────────────────
        clearSubkomponen() {
            this.subKomponen  = '';
            this.selectedInfo = null;
            this.subkomponens = [];
            this.rebuildSubkomponenSelect([]);
        },

        // ── Fetch Subkomponen via AJAX ─────────────────────────
        fetchSubkomponen() {
            if (!this.ro || !this.isValidRo(this.ro)) return;

            this.loading = true;

            fetch(
                `{{ route('anggaran.usulan.ajax.subkomponen') }}?ro=${encodeURIComponent(this.ro)}`,
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Accept': 'application/json',
                    }
                }
            )
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.error);

                // Simpan data dari server sebagai sumber kebenaran
                this.subkomponens = data;

                // Rebuild select dengan data baru
                this.rebuildSubkomponenSelect(data);
            })
            .catch(err => {
                console.error('Gagal memuat subkomponen:', err);
                this.subkomponens = [];
                this.rebuildSubkomponenSelect([]);
            })
            .finally(() => {
                this.loading = false;
            });
        },

        // ── Rebuild Select DOM ─────────────────────────────────
        // Seluruh option di-generate ulang dari data server
        // Mencegah option yang ditambah manual via DevTools tetap ada
        rebuildSubkomponenSelect(data) {
            const sel = document.getElementById('select-subkomponen');
            if (!sel) return;

            // Hapus SEMUA option
            sel.innerHTML = '';

            // Tambah placeholder
            const placeholder       = document.createElement('option');
            placeholder.value       = '';
            placeholder.textContent = this.ro
                ? '— Pilih Sub Komponen —'
                : '— Pilih RO dulu —';
            sel.appendChild(placeholder);

            // Tambah option dari data server menggunakan textContent (auto-escape XSS)
            data.forEach(item => {
                const opt           = document.createElement('option');
                opt.value           = item.kode_subkomponen;
                // textContent otomatis escape HTML — tidak perlu sanitasi manual
                opt.textContent     = `${item.kode_subkomponen} – ${item.program_kegiatan}`;
                sel.appendChild(opt);
            });

            // Restore nilai yang dipilih jika masih valid di data baru
            if (this.subKomponen) {
                const stillValid = data.some(d => d.kode_subkomponen === this.subKomponen);
                if (stillValid) {
                    sel.value = this.subKomponen;
                } else {
                    sel.value         = '';
                    this.subKomponen  = '';
                    this.selectedInfo = null;
                }
            }
        },

        // ── Computed ───────────────────────────────────────────
        get sisaAnggaranWarning() {
            if (!this.selectedInfo || !this.nilaiUsulan) return null;
            const sisa  = parseFloat(this.selectedInfo.sisa) || 0;
            const nilai = this.nilaiUsulan || 0;
            if (nilai > sisa)       return 'danger';
            if (nilai > sisa * 0.8) return 'warning';
            return null;
        },

        // ── Submit ─────────────────────────────────────────────
        submitForm() {
            const roEl    = document.getElementById('select-ro');
            const bulanEl = document.getElementById('select-bulan');
            const subEl   = document.getElementById('select-subkomponen');

            // Validasi RO
            if (!roEl.value || !this.isValidRo(roEl.value)) {
                if (typeof showToast === 'function') {
                    showToast('RO wajib dipilih dari daftar yang tersedia.', 'error');
                }
                roEl.focus();
                return;
            }

            // Validasi Bulan
            if (!bulanEl.value || !this.isValidBulan(bulanEl.value)) {
                if (typeof showToast === 'function') {
                    showToast('Bulan wajib dipilih dari daftar yang tersedia.', 'error');
                }
                bulanEl.focus();
                return;
            }

            // Validasi Sub Komponen — cek dari data server yang dimuat
            if (!subEl.value || !this.isValidSubkomponen(subEl.value)) {
                if (typeof showToast === 'function') {
                    showToast('Sub komponen wajib dipilih dari daftar yang tersedia.', 'error');
                }
                subEl.focus();
                return;
            }

            // Validasi Nilai
            if (!this.nilaiUsulan || this.nilaiUsulan <= 0) {
                if (typeof showToast === 'function') {
                    showToast('Nilai usulan wajib diisi.', 'error');
                }
                document.getElementById('nilai_display')?.focus();
                return;
            }

            // Cegah double submit
            if (this.submitting) return;
            this.submitting = true;

            // Submit — server adalah pertahanan utama (Rule::in dari DB)
            document.getElementById('form-edit-usulan').submit();
        },
    };
}
</script>
@endpush
