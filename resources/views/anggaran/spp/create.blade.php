@extends('layouts.app')

@section('title', 'Tambah SPP')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('anggaran.spp.index') }}" class="text-gray-600 hover:text-navy-600 dark:text-gray-400 dark:hover:text-navy-400">
                    Data SPP
                </a>
            </li>
            <li>
                <span class="mx-2 text-gray-400">/</span>
            </li>
            <li class="text-navy-600 dark:text-navy-400 font-medium">Tambah SPP</li>
        </ol>
    </nav>

    <form action="{{ route('anggaran.spp.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Informasi Dasar -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Dasar</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">No SPP <span class="text-red-500">*</span></label>
                    <input type="text" name="no_spp" value="{{ old('no_spp') }}"
                           class="input-field @error('no_spp') border-red-500 @enderror"
                           placeholder="Contoh: SPP-001/2024" required>
                    @error('no_spp')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Nominatif</label>
                    <input type="text" name="nominatif" value="{{ old('nominatif') }}"
                           class="input-field @error('nominatif') border-red-500 @enderror"
                           placeholder="Nama nominatif">
                    @error('nominatif')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Tanggal SPP <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_spp" value="{{ old('tgl_spp') }}"
                           class="input-field @error('tgl_spp') border-red-500 @enderror" required>
                    @error('tgl_spp')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Bulan <span class="text-red-500">*</span></label>
                    <select name="bulan" class="input-field @error('bulan') border-red-500 @enderror" required>
                        <option value="">Pilih Bulan</option>
                        @foreach($bulanList as $bulan)
                            <option value="{{ $bulan }}" {{ old('bulan') == $bulan ? 'selected' : '' }}>
                                {{ ucfirst($bulan) }}
                            </option>
                        @endforeach
                    </select>
                    @error('bulan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Jenis Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="jenis_kegiatan" value="{{ old('jenis_kegiatan') }}"
                           class="input-field @error('jenis_kegiatan') border-red-500 @enderror"
                           placeholder="Contoh: Perjalanan Dinas" required>
                    @error('jenis_kegiatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Jenis Belanja <span class="text-red-500">*</span></label>
                    <select name="jenis_belanja" class="input-field @error('jenis_belanja') border-red-500 @enderror" required>
                        <option value="">Pilih Jenis Belanja</option>
                        @foreach($jenisBelanja as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_belanja') == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_belanja')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Nomor Kontrak/SPBy</label>
                    <input type="text" name="nomor_kontrak" value="{{ old('nomor_kontrak') }}"
                           class="input-field @error('nomor_kontrak') border-red-500 @enderror"
                           placeholder="Nomor kontrak jika kontraktual">
                    @error('nomor_kontrak')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">No BAST/Kuitansi</label>
                    <input type="text" name="no_bast" value="{{ old('no_bast') }}"
                           class="input-field @error('no_bast') border-red-500 @enderror"
                           placeholder="Nomor BAST atau Kuitansi">
                    @error('no_bast')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">ID e-Perjadin</label>
                    <input type="text" name="id_eperjadin" value="{{ old('id_eperjadin') }}"
                           class="input-field @error('id_eperjadin') border-red-500 @enderror"
                           placeholder="ID e-Perjadin jika ada">
                    @error('id_eperjadin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group md:col-span-2">
                    <label class="input-label">Uraian SPP <span class="text-red-500">*</span></label>
                    <textarea name="uraian_spp" rows="3"
                              class="input-field @error('uraian_spp') border-red-500 @enderror"
                              placeholder="Uraian lengkap SPP" required>{{ old('uraian_spp') }}</textarea>
                    @error('uraian_spp')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Bagian <span class="text-red-500">*</span></label>
                    <input type="text" name="bagian" value="{{ old('bagian') }}"
                           class="input-field @error('bagian') border-red-500 @enderror"
                           placeholder="Nama bagian" required>
                    @error('bagian')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Nama PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pic" value="{{ old('nama_pic') }}"
                           class="input-field @error('nama_pic') border-red-500 @enderror"
                           placeholder="Nama PIC" required>
                    @error('nama_pic')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Kode Anggaran -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Kode Anggaran (COA)</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">Kode Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_kegiatan" value="{{ old('kode_kegiatan', '4753') }}"
                           class="input-field @error('kode_kegiatan') border-red-500 @enderror"
                           placeholder="Contoh: 4753" required>
                    @error('kode_kegiatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">KRO <span class="text-red-500">*</span></label>
                    <input type="text" name="kro" value="{{ old('kro', 'EBA') }}"
                           class="input-field @error('kro') border-red-500 @enderror"
                           placeholder="Contoh: EBA" required>
                    @error('kro')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">RO <span class="text-red-500">*</span></label>
                    <select name="ro" id="ro" class="input-field @error('ro') border-red-500 @enderror" required>
                        <option value="">Pilih RO</option>
                        @foreach($roList as $ro)
                            <option value="{{ $ro }}" {{ old('ro') == $ro ? 'selected' : '' }}>
                                {{ $ro }} - {{ get_ro_name($ro) }}
                            </option>
                        @endforeach
                    </select>
                    @error('ro')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Sub Komponen <span class="text-red-500">*</span></label>
                    <select name="sub_komponen" id="sub_komponen" class="input-field @error('sub_komponen') border-red-500 @enderror" required>
                        <option value="">Pilih Sub Komponen</option>
                    </select>
                    @error('sub_komponen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group md:col-span-2">
                    <label class="input-label">MAK (Kode Akun) <span class="text-red-500">*</span></label>
                    <select name="mak" id="mak" class="input-field @error('mak') border-red-500 @enderror" required>
                        <option value="">Pilih MAK</option>
                    </select>
                    @error('mak')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Dokumen Pendukung -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Dokumen Pendukung</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">Nomor Surat Tugas/BAST/SK</label>
                    <input type="text" name="nomor_surat_tugas" value="{{ old('nomor_surat_tugas') }}"
                           class="input-field @error('nomor_surat_tugas') border-red-500 @enderror"
                           placeholder="Nomor surat tugas">
                    @error('nomor_surat_tugas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Tanggal ST/SK</label>
                    <input type="date" name="tanggal_st" value="{{ old('tanggal_st') }}"
                           class="input-field @error('tanggal_st') border-red-500 @enderror">
                    @error('tanggal_st')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Nomor Undangan</label>
                    <input type="text" name="nomor_undangan" value="{{ old('nomor_undangan') }}"
                           class="input-field @error('nomor_undangan') border-red-500 @enderror"
                           placeholder="Nomor undangan jika ada">
                    @error('nomor_undangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                           class="input-field @error('tanggal_mulai') border-red-500 @enderror">
                    @error('tanggal_mulai')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                           class="input-field @error('tanggal_selesai') border-red-500 @enderror">
                    @error('tanggal_selesai')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Nilai & Pajak -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Nilai & Pajak</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">Bruto <span class="text-red-500">*</span></label>
                    <input type="number" name="bruto" id="bruto" value="{{ old('bruto') }}"
                           class="input-field @error('bruto') border-red-500 @enderror"
                           placeholder="0" step="0.01" required>
                    @error('bruto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">PPN</label>
                    <input type="number" name="ppn" id="ppn" value="{{ old('ppn', 0) }}"
                           class="input-field @error('ppn') border-red-500 @enderror"
                           placeholder="0" step="0.01">
                    @error('ppn')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">PPh</label>
                    <input type="number" name="pph" id="pph" value="{{ old('pph', 0) }}"
                           class="input-field @error('pph') border-red-500 @enderror"
                           placeholder="0" step="0.01">
                    @error('pph')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Netto <span class="text-red-500">*</span></label>
                    <input type="number" name="netto" id="netto" value="{{ old('netto') }}"
                           class="input-field @error('netto') border-red-500 @enderror"
                           placeholder="0" step="0.01" required readonly>
                    @error('netto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Akan dihitung otomatis: Bruto - PPN - PPh</p>
                </div>
            </div>
        </div>

        <!-- Status & SP2D -->
        <div class="card">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Status Pembayaran</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">LS/Bendahara <span class="text-red-500">*</span></label>
                    <select name="ls_bendahara" class="input-field @error('ls_bendahara') border-red-500 @enderror" required>
                        <option value="">Pilih</option>
                        @foreach($lsBendahara as $ls)
                            <option value="{{ $ls }}" {{ old('ls_bendahara') == $ls ? 'selected' : '' }}>
                                {{ $ls }}
                            </option>
                        @endforeach
                    </select>
                    @error('ls_bendahara')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Staff PPK</label>
                    <input type="text" name="staff_ppk" value="{{ old('staff_ppk') }}"
                           class="input-field @error('staff_ppk') border-red-500 @enderror"
                           placeholder="Nama staff PPK">
                    @error('staff_ppk')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="input-field @error('status') border-red-500 @enderror" required>
                        <option value="">Pilih Status</option>
                        <option value="Tagihan Belum SP2D" {{ old('status') == 'Tagihan Belum SP2D' ? 'selected' : '' }}>
                            Tagihan Belum SP2D
                        </option>
                        <option value="Tagihan Telah SP2D" {{ old('status') == 'Tagihan Telah SP2D' ? 'selected' : '' }}>
                            Tagihan Telah SP2D
                        </option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group" id="sp2d_fields" style="display: none;">
                    <label class="input-label">No SP2D</label>
                    <input type="text" name="no_sp2d" value="{{ old('no_sp2d') }}"
                           class="input-field @error('no_sp2d') border-red-500 @enderror"
                           placeholder="Nomor SP2D">
                    @error('no_sp2d')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group" id="tgl_sp2d_fields" style="display: none;">
                    <label class="input-label">Tanggal SP2D</label>
                    <input type="date" name="tgl_sp2d" value="{{ old('tgl_sp2d') }}"
                           class="input-field @error('tgl_sp2d') border-red-500 @enderror">
                    @error('tgl_sp2d')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group" id="tgl_selesai_fields" style="display: none;">
                    <label class="input-label">Tanggal Selesai SP2D</label>
                    <input type="date" name="tgl_selesai_sp2d" value="{{ old('tgl_selesai_sp2d') }}"
                           class="input-field @error('tgl_selesai_sp2d') border-red-500 @enderror">
                    @error('tgl_selesai_sp2d')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Posisi Uang</label>
                    <input type="text" name="posisi_uang" value="{{ old('posisi_uang') }}"
                           class="input-field @error('posisi_uang') border-red-500 @enderror"
                           placeholder="Keterangan posisi uang">
                    @error('posisi_uang')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="flex justify-end gap-3">
                <a href="{{ route('anggaran.spp.index') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Data SPP
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roSelect = document.getElementById('ro');
    const subKomponenSelect = document.getElementById('sub_komponen');
    const makSelect = document.getElementById('mak');
    const brutoInput = document.getElementById('bruto');
    const ppnInput = document.getElementById('ppn');
    const pphInput = document.getElementById('pph');
    const nettoInput = document.getElementById('netto');
    const statusSelect = document.getElementById('status');
    const sp2dFields = document.getElementById('sp2d_fields');
    const tglSp2dFields = document.getElementById('tgl_sp2d_fields');
    const tglSelesaiFields = document.getElementById('tgl_selesai_fields');

    // Load sub komponen when RO changes
    roSelect.addEventListener('change', function() {
        const ro = this.value;
        subKomponenSelect.innerHTML = '<option value="">Pilih Sub Komponen</option>';
        makSelect.innerHTML = '<option value="">Pilih MAK</option>';

        if (ro) {
            fetch(`{{ route('anggaran.spp.get-subkomponen') }}?ro=${ro}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.kode_subkomponen;
                        option.textContent = `${item.kode_subkomponen} - ${item.program_kegiatan}`;
                        subKomponenSelect.appendChild(option);
                    });
                });
        }
    });

    // Load MAK when sub komponen changes
    subKomponenSelect.addEventListener('change', function() {
        const ro = roSelect.value;
        const subkomponen = this.value;
        makSelect.innerHTML = '<option value="">Pilih MAK</option>';

        if (ro && subkomponen) {
            fetch(`{{ route('anggaran.spp.get-akun') }}?ro=${ro}&subkomponen=${subkomponen}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.kode_akun;
                        option.textContent = `${item.kode_akun} - ${item.program_kegiatan}`;
                        option.dataset.kegiatan = item.kode_kegiatan;
                        option.dataset.kro = item.kro;
                        makSelect.appendChild(option);
                    });
                });
        }
    });

    // Auto fill kode kegiatan and kro when MAK is selected
    makSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.kegiatan) {
            document.querySelector('[name="kode_kegiatan"]').value = selectedOption.dataset.kegiatan;
            document.querySelector('[name="kro"]').value = selectedOption.dataset.kro;
        }
    });

    // Calculate netto automatically
    function calculateNetto() {
        const bruto = parseFloat(brutoInput.value) || 0;
        const ppn = parseFloat(ppnInput.value) || 0;
        const pph = parseFloat(pphInput.value) || 0;
        const netto = bruto - ppn - pph;
        nettoInput.value = netto.toFixed(2);
    }

    brutoInput.addEventListener('input', calculateNetto);
    ppnInput.addEventListener('input', calculateNetto);
    pphInput.addEventListener('input', calculateNetto);

    // Show/hide SP2D fields based on status
    statusSelect.addEventListener('change', function() {
        if (this.value === 'Tagihan Telah SP2D') {
            sp2dFields.style.display = 'block';
            tglSp2dFields.style.display = 'block';
            tglSelesaiFields.style.display = 'block';
        } else {
            sp2dFields.style.display = 'none';
            tglSp2dFields.style.display = 'none';
            tglSelesaiFields.style.display = 'none';
        }
    });

    // Trigger on page load
    if (statusSelect.value === 'Tagihan Telah SP2D') {
        sp2dFields.style.display = 'block';
        tglSp2dFields.style.display = 'block';
        tglSelesaiFields.style.display = 'block';
    }
});
</script>
@endpush
@endsection
