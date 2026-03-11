@extends('layouts.app')

@section('title', 'Tambah Usulan Penarikan Dana')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('anggaran.usulan.index') }}" class="breadcrumb-item">Usulan Penarikan Dana</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Tambah Usulan</span>
    </nav>

    <form action="{{ route('anggaran.usulan.store') }}" method="POST"
          x-data="{
              ro: '{{ old('ro') }}',
              subKomponen: '{{ old('sub_komponen') }}',
              subkomponens: [],
              loading: false,
              selectedInfo: null,
              nilaiUsulan: 0,

              fetchSubkomponen() {
                  if (!this.ro) { this.subkomponens = []; this.selectedInfo = null; return; }
                  this.loading = true;
                  this.subKomponen = '';
                  this.selectedInfo = null;
                  fetch(`{{ route('anggaran.usulan.ajax.subkomponen') }}?ro=${this.ro}`)
                      .then(r => r.json())
                      .then(data => { this.subkomponens = data; this.loading = false; })
                      .catch(() => { this.loading = false; });
              },

              onSubkompChange(val) {
                  this.selectedInfo = this.subkomponens.find(s => s.kode_subkomponen === val) || null;
              },

              get sisaAnggaranWarning() {
                  if (!this.selectedInfo || !this.nilaiUsulan) return null;
                  const sisa = parseFloat(this.selectedInfo.sisa) || 0;
                  const nilai = parseFloat(String(this.nilaiUsulan).replace(/\D/g,'')) || 0;
                  if (nilai > sisa) return 'danger';
                  if (nilai > sisa * 0.8) return 'warning';
                  return null;
              }
          }"
          @submit="$store.app.setLoading(true)">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===== FORM UTAMA ===== --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="card">
                    <div class="section-header">
                        <div>
                            <h3 class="section-title">Informasi Usulan</h3>
                            <p class="section-desc">Isi data usulan penarikan dana dengan lengkap</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- RO --}}
                        <div class="input-group">
                            <label class="input-label">RO <span class="text-red-500">*</span></label>
                            <select name="ro" x-model="ro" @change="fetchSubkomponen()"
                                    class="input-field @error('ro') input-error @enderror" required>
                                <option value="">— Pilih RO —</option>
                                @foreach($roList as $ro)
                                    <option value="{{ $ro }}">{{ $ro }} – {{ get_ro_name($ro) }}</option>
                                @endforeach
                            </select>
                            @error('ro')<p class="input-hint-error">{{ $message }}</p>@enderror
                        </div>

                        {{-- Sub Komponen --}}
                        <div class="input-group">
                            <label class="input-label">Sub Komponen <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="sub_komponen" x-model="subKomponen"
                                        @change="onSubkompChange($event.target.value)"
                                        class="input-field @error('sub_komponen') input-error @enderror"
                                        :disabled="loading || !ro" required>
                                    <option value="">
                                        <span x-text="loading ? 'Memuat...' : (ro ? '— Pilih Sub Komponen —' : '— Pilih RO dulu —')"></span>
                                    </option>
                                    <template x-for="item in subkomponens" :key="item.kode_subkomponen">
                                        <option :value="item.kode_subkomponen"
                                                x-text="`${item.kode_subkomponen} – ${item.program_kegiatan}`"></option>
                                    </template>
                                </select>
                                <div x-show="loading" class="absolute right-3 top-3">
                                    <svg class="w-4 h-4 text-navy-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('sub_komponen')<p class="input-hint-error">{{ $message }}</p>@enderror
                        </div>

                        {{-- Bulan --}}
                        <div class="input-group">
                            <label class="input-label">Bulan <span class="text-red-500">*</span></label>
                            <select name="bulan" class="input-field @error('bulan') input-error @enderror" required>
                                <option value="">— Pilih Bulan —</option>
                                @foreach($bulanList as $bulan)
                                    <option value="{{ $bulan }}" {{ old('bulan') == $bulan ? 'selected' : '' }}>
                                        {{ ucfirst($bulan) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bulan')<p class="input-hint-error">{{ $message }}</p>@enderror
                        </div>

                        {{-- Nilai Usulan --}}
                        <div class="input-group">
                            <label class="input-label">Nilai Usulan <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium pointer-events-none">Rp</span>
                                <input type="text" id="nilai_display"
                                       class="input-field pl-9 @error('nilai_usulan') input-error @enderror"
                                       :class="{
                                           '!border-red-400 !ring-red-100': sisaAnggaranWarning === 'danger',
                                           '!border-yellow-400 !ring-yellow-100': sisaAnggaranWarning === 'warning'
                                       }"
                                       placeholder="0"
                                       @input="nilaiUsulan = $event.target.value.replace(/\D/g,''); $event.target.value = nilaiUsulan ? parseInt(nilaiUsulan).toLocaleString('id-ID') : ''"
                                       value="{{ old('nilai_usulan') ? number_format(old('nilai_usulan'), 0, ',', '.') : '' }}"
                                       autocomplete="off">
                                <input type="hidden" name="nilai_usulan"
                                       :value="nilaiUsulan || '{{ old('nilai_usulan') }}'">
                            </div>
                            <p x-show="sisaAnggaranWarning === 'danger'"
                               class="input-hint-error" style="display:none;">
                                ⚠ Nilai melebihi sisa anggaran sub komponen
                            </p>
                            <p x-show="sisaAnggaranWarning === 'warning'"
                               class="input-hint-warning" style="display:none;">
                                ⚠ Nilai mendekati batas sisa anggaran (>80%)
                            </p>
                            @error('nilai_usulan')<p class="input-hint-error">{{ $message }}</p>@enderror
                        </div>

                        {{-- Keterangan --}}
                        <div class="input-group md:col-span-2">
                            <label class="input-label">Keterangan</label>
                            <textarea name="keterangan" rows="3"
                                      class="input-field @error('keterangan') input-error @enderror"
                                      placeholder="Jelaskan keperluan penarikan dana ini (opsional)...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')<p class="input-hint-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('anggaran.usulan.index') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Ajukan Usulan
                    </button>
                </div>
            </div>

            {{-- ===== PANEL INFO ANGGARAN ===== --}}
            <div class="space-y-4">

                {{-- Info SubKomponen Terpilih --}}
                <div class="card" x-show="selectedInfo" style="display:none;" x-transition>
                    <h4 class="section-title mb-3">📊 Info Anggaran Sub Komponen</h4>

                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Program Kegiatan</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-2"
                               x-text="selectedInfo?.program_kegiatan ?? '-'"></p>
                        </div>

                        <div class="grid grid-cols-1 gap-2 pt-2 border-t border-gray-100 dark:border-navy-700">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Pagu Anggaran</span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 tabular-nums"
                                      x-text="selectedInfo ? 'Rp ' + parseInt(selectedInfo.pagu_anggaran).toLocaleString('id-ID') : '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Terpakai</span>
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums"
                                      x-text="selectedInfo ? 'Rp ' + parseInt(selectedInfo.total_penyerapan).toLocaleString('id-ID') : '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Sisa Anggaran</span>
                                <span class="text-xs font-bold tabular-nums"
                                      :class="parseFloat(selectedInfo?.sisa) < parseFloat(selectedInfo?.pagu_anggaran) * 0.2 ? 'text-red-600 dark:text-red-400' : 'text-navy-700 dark:text-navy-300'"
                                      x-text="selectedInfo ? 'Rp ' + parseInt(selectedInfo.sisa).toLocaleString('id-ID') : '-'"></span>
                            </div>
                        </div>

                        {{-- Progress bar penyerapan --}}
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500">Penyerapan</span>
                                <span class="text-xs font-semibold"
                                      x-text="selectedInfo && selectedInfo.pagu_anggaran > 0
                                          ? (parseFloat(selectedInfo.total_penyerapan) / parseFloat(selectedInfo.pagu_anggaran) * 100).toFixed(1) + '%'
                                          : '0%'"></span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar"
                                     :class="selectedInfo && parseFloat(selectedInfo.total_penyerapan)/parseFloat(selectedInfo.pagu_anggaran) >= 0.8
                                         ? 'bg-emerald-500' : 'bg-navy-500'"
                                     :style="selectedInfo && selectedInfo.pagu_anggaran > 0
                                         ? 'width:' + Math.min(parseFloat(selectedInfo.total_penyerapan)/parseFloat(selectedInfo.pagu_anggaran)*100, 100) + '%'
                                         : 'width:0%'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Placeholder saat belum pilih --}}
                <div class="card" x-show="!selectedInfo" x-transition>
                    <div class="empty-state py-8">
                        <div class="empty-state-icon">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                            </svg>
                        </div>
                        <p class="empty-state-title text-sm">Info Anggaran</p>
                        <p class="empty-state-desc text-xs">Pilih RO dan Sub Komponen untuk melihat informasi sisa anggaran</p>
                    </div>
                </div>

                {{-- Panduan --}}
                <div class="alert alert-info text-xs">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold mb-1">Panduan Pengisian</p>
                        <ul class="space-y-1 text-gray-600 dark:text-gray-400">
                            <li>• Pilih RO terlebih dahulu untuk memuat daftar sub komponen</li>
                            <li>• Nilai usulan tidak boleh melebihi sisa anggaran</li>
                            <li>• Usulan akan dikaji oleh admin sebelum disetujui</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
