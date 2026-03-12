@extends('layouts.app')
@section('title', 'Edit Pegawai — '.$pegawai->nama)

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Kelola Data Pegawai', 'url' => route('kepegawaian.pegawai.index'), 'active' => false],
                ['title' => 'Edit Pegawai', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="page-title mt-1">Edit Data Pegawai</h1>
            <div class="flex items-center gap-2 mt-1">
                <div class="w-6 h-6 bg-gradient-to-br from-navy-500 to-navy-700 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold uppercase">{{ substr($pegawai->nama, 0, 1) }}</span>
                </div>
                <p class="page-subtitle !mt-0">{{ $pegawai->nama }} · <span class="font-mono">{{ $pegawai->nip }}</span></p>
            </div>
        </div>
        <a href="{{ route('kepegawaian.pegawai.index') }}" class="btn-ghost btn-sm self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert-danger">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="font-semibold text-sm">Terdapat {{ $errors->count() }} kesalahan:</p>
            <ul class="mt-1.5 space-y-1">
                @foreach($errors->all() as $error)
                <li class="text-sm flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>{{ $error }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('kepegawaian.pegawai.update', $pegawai) }}" x-data="{ activeTab: 'identitas' }">
        @csrf @method('PUT')
        @php $p = $pegawai; @endphp

        <div class="card p-0 overflow-hidden mb-5">
            {{-- Tabs --}}
            <div class="border-b border-gray-100 dark:border-navy-700 px-5 pt-4 bg-gray-50/50 dark:bg-navy-800/60">
                <nav class="-mb-px flex space-x-1 overflow-x-auto scrollbar-none">
                    @php
                    $tabs = [
                        ['id' => 'identitas',   'label' => 'Identitas',      'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['id' => 'jabatan',     'label' => 'Jabatan & Unit', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['id' => 'kepegawaian', 'label' => 'Kepegawaian',    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['id' => 'pendidikan',  'label' => 'Pendidikan',     'icon' => 'M12 14l9-5-9-5-9 5 9 5z'],
                    ];
                    @endphp
                    @foreach($tabs as $tab)
                    <button type="button"
                        @click="activeTab = '{{ $tab['id'] }}'"
                        :class="activeTab === '{{ $tab['id'] }}'
                            ? 'border-navy-600 text-navy-700 dark:text-gold-400 bg-white dark:bg-navy-800'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="flex items-center gap-2 py-3 px-3 border-b-2 text-sm font-medium transition-all whitespace-nowrap rounded-t-lg">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                        </svg>
                        {{ $tab['label'] }}
                    </button>
                    @endforeach
                </nav>
            </div>

            {{-- Tab: Identitas --}}
            <div x-show="activeTab === 'identitas'" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="input-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $p->nama) }}"
                            class="input-field @error('nama') input-error @enderror" required>
                        @error('nama')<p class="input-hint-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="input-label">Nama dengan Gelar</label>
                        <input type="text" name="nama_gelar" value="{{ old('nama_gelar', $p->nama_gelar) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">NIP <span class="text-red-500">*</span></label>
                        <input type="text" name="nip" value="{{ old('nip', $p->nip) }}"
                            class="input-field font-mono @error('nip') input-error @enderror" required>
                        @error('nip')<p class="input-hint-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="input-label">Jenis Kelamin</label>
                        <div class="flex gap-5 mt-1.5">
                            @foreach(['Laki-laki','Perempuan'] as $jk)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="jenis_kelamin" value="{{ $jk }}"
                                    {{ old('jenis_kelamin', $p->jenis_kelamin) == $jk ? 'checked' : '' }}
                                    class="w-4 h-4 text-navy-600 border-gray-300 dark:border-navy-500 focus:ring-navy-500 bg-white dark:bg-navy-700">
                                <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-navy-600 dark:group-hover:text-gold-400 transition-colors">{{ $jk }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="input-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $p->tanggal_lahir?->format('Y-m-d')) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Usia <span class="text-gray-400 font-normal">(tahun)</span></label>
                        <input type="number" name="usia" value="{{ old('usia', $p->usia) }}" class="input-field" min="17" max="70">
                    </div>
                    <div>
                        <label class="input-label">Email Kemenkeu</label>
                        <input type="email" name="email_kemenkeu" value="{{ old('email_kemenkeu', $p->email_kemenkeu) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Email Pribadi</label>
                        <input type="email" name="email_pribadi" value="{{ old('email_pribadi', $p->email_pribadi) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $p->no_hp) }}" class="input-field">
                    </div>
                </div>
            </div>

            {{-- Tab: Jabatan --}}
            <div x-show="activeTab === 'jabatan'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="input-label">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $p->jabatan) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Jenis Jabatan</label>
                        <select name="jenis_jabatan" class="input-field">
                            <option value="">— Pilih —</option>
                            @foreach(['Struktural','Fungsional','Pelaksana'] as $jj)
                            <option value="{{ $jj }}" {{ old('jenis_jabatan', $p->jenis_jabatan) == $jj ? 'selected' : '' }}>{{ $jj }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="input-label">Nama Jabatan Lengkap</label>
                        <input type="text" name="nama_jabatan" value="{{ old('nama_jabatan', $p->nama_jabatan) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Eselon</label>
                        <select name="eselon" class="input-field">
                            <option value="">— Pilih —</option>
                            @foreach(['Eselon I','Eselon II','Eselon III','Eselon IV','Non Eselon'] as $e)
                            <option value="{{ $e }}" {{ old('eselon', $p->eselon) == $e ? 'selected' : '' }}>{{ $e }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Jenis Pegawai</label>
                        <select name="jenis_pegawai" class="input-field">
                            <option value="">— Pilih —</option>
                            @foreach(['PNS','PPPK','Honorer'] as $jp)
                            <option value="{{ $jp }}" {{ old('jenis_pegawai', $p->jenis_pegawai) == $jp ? 'selected' : '' }}>{{ $jp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Bagian / Unit</label>
                        <input type="text" name="bagian" value="{{ old('bagian', $p->bagian) }}" class="input-field" list="bagian-list">
                        <datalist id="bagian-list">@foreach($bagianList as $b)<option value="{{ $b }}">@endforeach</datalist>
                    </div>
                    <div>
                        <label class="input-label">Subbagian</label>
                        <input type="text" name="subbagian" value="{{ old('subbagian', $p->subbagian) }}" class="input-field" list="subbagian-list">
                        <datalist id="subbagian-list">@foreach($subbagianList as $s)<option value="{{ $s }}">@endforeach</datalist>
                    </div>
                    <div>
                        <label class="input-label">Lokasi / Kantor</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $p->lokasi) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Status Kepegawaian</label>
                        <select name="status" class="input-field">
                            <option value="">— Pilih —</option>
                            @foreach(['AKTIF','CLTN','PENSIUN','NON AKTIF'] as $s)
                            <option value="{{ $s }}" {{ old('status', $p->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Grading <span class="text-gray-400 font-normal">(1–16)</span></label>
                        <div class="relative">
                            <input type="number" name="grading" value="{{ old('grading', $p->grading) }}" class="input-field pr-12" min="1" max="16">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">/ 16</span>
                        </div>
                    </div>
                    <div>
                        <label class="input-label">Pangkat / Golongan</label>
                        <input type="text" name="pangkat" value="{{ old('pangkat', $p->pangkat) }}" class="input-field">
                    </div>
                </div>
            </div>

            {{-- Tab: Kepegawaian --}}
            <div x-show="activeTab === 'kepegawaian'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="input-label">TMT CPNS</label>
                        <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', $p->tmt_cpns?->format('Y-m-d')) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Masa Kerja</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="relative">
                                <input type="number" name="masa_kerja_tahun" value="{{ old('masa_kerja_tahun', $p->masa_kerja_tahun) }}" class="input-field pr-14" min="0">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">tahun</span>
                            </div>
                            <div class="relative">
                                <input type="number" name="masa_kerja_bulan" value="{{ old('masa_kerja_bulan', $p->masa_kerja_bulan) }}" class="input-field pr-14" min="0" max="11">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">bulan</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="input-label">Tanggal Pensiun</label>
                        <input type="date" name="tanggal_pensiun" value="{{ old('tanggal_pensiun', $p->tanggal_pensiun?->format('Y-m-d')) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Tahun Pensiun</label>
                        <input type="number" name="tahun_pensiun" value="{{ old('tahun_pensiun', $p->tahun_pensiun) }}" class="input-field" min="{{ date('Y') }}">
                    </div>
                    <div>
                        <label class="input-label">Proyeksi KP 1</label>
                        <input type="text" name="proyeksi_kp_1" value="{{ old('proyeksi_kp_1', $p->proyeksi_kp_1) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Proyeksi KP 2</label>
                        <input type="text" name="proyeksi_kp_2" value="{{ old('proyeksi_kp_2', $p->proyeksi_kp_2) }}" class="input-field">
                    </div>
                    <div class="md:col-span-2">
                        <label class="input-label">Keterangan KP</label>
                        <textarea name="keterangan_kp" rows="3" class="input-field resize-none">{{ old('keterangan_kp', $p->keterangan_kp) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Tab: Pendidikan --}}
            <div x-show="activeTab === 'pendidikan'" class="p-6" style="display:none">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="input-label">Pendidikan Terakhir</label>
                        <select name="pendidikan" class="input-field">
                            <option value="">— Pilih Jenjang —</option>
                            @foreach(['SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3'] as $pd)
                            <option value="{{ $pd }}" {{ old('pendidikan', $p->pendidikan) == $pd ? 'selected' : '' }}>{{ $pd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Jurusan / Prodi S1</label>
                        <input type="text" name="jurusan_s1" value="{{ old('jurusan_s1', $p->jurusan_s1) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Jurusan / Prodi S2</label>
                        <input type="text" name="jurusan_s2" value="{{ old('jurusan_s2', $p->jurusan_s2) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label">Jurusan / Prodi S3</label>
                        <input type="text" name="jurusan_s3" value="{{ old('jurusan_s3', $p->jurusan_s3) }}" class="input-field">
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Terakhir diperbarui {{ $pegawai->updated_at->diffForHumans() }}
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}" class="btn-ghost">Lihat Detail</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
