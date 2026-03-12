@extends('layouts.app')
@section('title', 'Import Data Pegawai')

@section('content')
<div class="space-y-6 animate-fade-in" x-data="importPage()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <x-breadcrumb :items="[
                ['title' => 'Kepegawaian', 'url' => null, 'active' => false],
                ['title' => 'Kelola Data Pegawai', 'url' => route('kepegawaian.pegawai.index'), 'active' => false],
                ['title' => 'Import Data', 'url' => null, 'active' => true],
            ]"/>
            <h1 class="page-title mt-1">Import Data Pegawai</h1>
            <p class="page-subtitle">Upload file Excel untuk menambah atau memperbarui data pegawai secara massal</p>
        </div>
        <a href="{{ route('kepegawaian.pegawai.index') }}" class="btn-ghost btn-sm self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Step Guide --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @php
        $steps = [
            ['num'=>'1','title'=>'Download Template','desc'=>'Unduh file template Excel sebagai panduan format kolom yang benar','color'=>'navy'],
            ['num'=>'2','title'=>'Isi Data Pegawai', 'desc'=>'Lengkapi template dengan data pegawai yang akan diimport ke sistem','color'=>'gold'],
            ['num'=>'3','title'=>'Upload & Proses',  'desc'=>'Upload file dan sistem akan memproses data secara otomatis','color'=>'green'],
        ];
        $colors = [
            'navy'  => ['wrap'=>'border-navy-200 dark:border-navy-600 bg-navy-50 dark:bg-navy-700/40',  'badge'=>'bg-navy-600 text-white'],
            'gold'  => ['wrap'=>'border-gold-200 dark:border-navy-600 bg-gold-50 dark:bg-navy-700/40',  'badge'=>'bg-gold-500 text-navy-900'],
            'green' => ['wrap'=>'border-green-200 dark:border-navy-600 bg-green-50 dark:bg-navy-700/40','badge'=>'bg-emerald-600 text-white'],
        ];
        @endphp
        @foreach($steps as $step)
        @php $sc = $colors[$step['color']]; @endphp
        <div class="flex items-start gap-4 p-5 rounded-2xl border-2 {{ $sc['wrap'] }}">
            <div class="w-9 h-9 {{ $sc['badge'] }} rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                {{ $step['num'] }}
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $step['title'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">{{ $step['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Upload (2/3) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Template Banner --}}
            <div class="bg-gradient-to-r from-navy-700 to-navy-900 rounded-2xl p-5 text-white flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">Template Import Pegawai (.xlsx)</p>
                        <p class="text-navy-300 text-xs mt-0.5">34 kolom · Format data yang benar · Contoh pengisian</p>
                    </div>
                </div>
                <a href="{{ route('kepegawaian.pegawai.template') }}"
                    class="btn btn-secondary btn-sm flex-shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
            </div>

            {{-- Upload Form --}}
            <div class="card p-0 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-navy-700 bg-gray-50/50 dark:bg-navy-800/60">
                    <h3 class="section-title">Upload File Excel</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('kepegawaian.pegawai.import') }}" enctype="multipart/form-data"
                        @submit.prevent="submitForm">
                        @csrf

                        {{-- Dropzone --}}
                        <label
                            class="relative flex flex-col items-center justify-center w-full rounded-2xl cursor-pointer transition-all duration-200 border-2 border-dashed min-h-52"
                            :class="dragover
                                ? 'border-navy-400 bg-navy-50 dark:bg-navy-700/60 scale-[1.005]'
                                : file
                                    ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20'
                                    : 'border-gray-200 dark:border-navy-600 hover:border-navy-300 dark:hover:border-navy-500 bg-gray-50/60 dark:bg-navy-700/20'"
                            @dragover.prevent="dragover = true"
                            @dragleave.prevent="dragover = false"
                            @drop.prevent="handleDrop($event)">

                            <input type="file" name="file" accept=".xlsx,.xls,.csv" x-ref="fileInput"
                                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                @change="handleFile($event.target.files[0])">

                            {{-- Empty state --}}
                            <div x-show="!file" class="text-center p-8 pointer-events-none">
                                <div class="w-16 h-16 bg-navy-100 dark:bg-navy-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Drag & drop file ke sini</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">atau <span class="text-navy-600 dark:text-navy-400 font-medium underline underline-offset-2">klik untuk memilih file</span></p>
                                <div class="flex items-center justify-center gap-2 mt-4 flex-wrap">
                                    @foreach(['.xlsx','.xls','.csv'] as $ext)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-navy-600 rounded-full text-xs text-gray-600 dark:text-gray-300 font-medium">
                                        <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        {{ $ext }}
                                    </span>
                                    @endforeach
                                    <span class="text-xs text-gray-400">Maks. 10 MB</span>
                                </div>
                            </div>

                            {{-- File selected --}}
                            <div x-show="file" class="text-center p-8 pointer-events-none w-full">
                                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-900 dark:text-white truncate max-w-xs mx-auto" x-text="fileName"></p>
                                <div class="flex items-center justify-center gap-3 mt-2">
                                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="fileSize"></span>
                                    <span class="badge badge-success">Siap diimport</span>
                                </div>
                                <button type="button" @click.stop="clearFile()"
                                    class="mt-3 pointer-events-auto inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Ganti file
                                </button>
                            </div>
                        </label>

                        @error('file')
                        <p class="input-hint-error mt-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror

                        {{-- Progress --}}
                        <div x-show="uploading" class="mt-4">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                <span>Memproses file...</span>
                                <span x-text="progress + '%'"></span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-navy" :style="'width:' + progress + '%'"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-5 pt-5 border-t border-gray-100 dark:border-navy-700">
                            <a href="{{ route('kepegawaian.pegawai.index') }}" class="btn-ghost">Batal</a>
                            <button type="submit" :disabled="!file || uploading"
                                class="btn-primary disabled:opacity-40 disabled:cursor-not-allowed disabled:transform-none">
                                <span x-show="!uploading" class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Proses Import
                                </span>
                                <span x-show="uploading" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: Info (1/3) --}}
        <div class="space-y-4">

            {{-- Warning --}}
            <div class="alert-warning">
                <svg class="w-5 h-5 flex-shrink-0 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-bold mb-2">Perhatian Sebelum Import</p>
                    <ul class="space-y-1.5">
                        @php
                        $warnings = [
                            ['🔄','NIP yang sudah ada akan <strong>diperbarui otomatis</strong>'],
                            ['⚠️','Kolom <strong>nama</strong> dan <strong>nip</strong> wajib diisi'],
                            ['📅','Format tanggal: <strong>dd/mm/yyyy</strong>'],
                            ['🔢','Grading berupa angka <strong>1–16</strong>'],
                            ['✅','Status: <strong>AKTIF</strong>, <strong>CLTN</strong>, <strong>PENSIUN</strong>, <strong>NON AKTIF</strong>'],
                        ];
                        @endphp
                        @foreach($warnings as [$icon,$text])
                        <li class="flex items-start gap-2 text-xs">
                            <span class="flex-shrink-0">{{ $icon }}</span>
                            <span>{!! $text !!}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Kolom Template --}}
            <div class="card p-0 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-navy-700 flex items-center justify-between bg-gray-50/50 dark:bg-navy-800/60">
                    <h4 class="section-title">Kolom Template</h4>
                    <span class="badge badge-info">34 kolom</span>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto scrollbar-thin">
                    @php
                    $columnGroups = [
                        'Wajib'       => [['nama', true], ['nip', true]],
                        'Identitas'   => [['nama_gelar',false],['jenis_kelamin',false],['tanggal_lahir',false],['bulan_lahir',false],['tahun_lahir',false],['usia',false],['no_hp',false],['email_kemenkeu',false],['email_pribadi',false]],
                        'Jabatan'     => [['jabatan',false],['jenis_jabatan',false],['nama_jabatan',false],['eselon',false],['jenis_pegawai',false],['status',false],['grading',false],['pangkat',false],['bagian',false],['subbagian',false],['lokasi',false]],
                        'Kepegawaian' => [['tmt_cpns',false],['masa_kerja_tahun',false],['masa_kerja_bulan',false],['tanggal_pensiun',false],['tahun_pensiun',false],['proyeksi_kp_1',false],['proyeksi_kp_2',false],['keterangan_kp',false]],
                        'Pendidikan'  => [['pendidikan',false],['jurusan_s1',false],['jurusan_s2',false],['jurusan_s3',false]],
                    ];
                    @endphp
                    @foreach($columnGroups as $group => $cols)
                    <div class="mb-3 last:mb-0">
                        <p class="section-label mb-1.5">{{ $group }}</p>
                        <div class="space-y-1">
                            @foreach($cols as [$col, $required])
                            <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg {{ $required ? 'bg-red-50 dark:bg-red-900/20' : 'bg-gray-50 dark:bg-navy-700' }}">
                                <span class="text-xs font-mono {{ $required ? 'text-red-700 dark:text-red-400 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">{{ $col }}</span>
                                @if($required)<span class="badge badge-danger text-xs">Wajib</span>@endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function importPage() {
    return {
        file: null, fileName: '', fileSize: '',
        dragover: false, uploading: false, progress: 0,

        handleFile(f) {
            if (!f) return;
            const ext = f.name.split('.').pop().toLowerCase();
            if (!['xlsx','xls','csv'].includes(ext)) {
                alert('Format tidak didukung. Gunakan .xlsx, .xls, atau .csv');
                return;
            }
            if (f.size > 10 * 1024 * 1024) { alert('Ukuran file melebihi 10 MB'); return; }
            this.file = f;
            this.fileName = f.name;
            this.fileSize = (f.size / 1024 / 1024).toFixed(2) + ' MB';
        },
        handleDrop(e) {
            this.dragover = false;
            const f = e.dataTransfer.files[0];
            if (f) { this.$refs.fileInput.files = e.dataTransfer.files; this.handleFile(f); }
        },
        clearFile() {
            this.file = null; this.fileName = ''; this.fileSize = '';
            this.$refs.fileInput.value = '';
        },
        submitForm(e) {
            if (!this.file) return;
            this.uploading = true; this.progress = 0;
            const iv = setInterval(() => { this.progress = Math.min(this.progress + Math.random() * 15, 90); }, 200);
            setTimeout(() => { clearInterval(iv); this.progress = 100; e.target.submit(); }, 1500);
        }
    }
}
</script>
@endpush
