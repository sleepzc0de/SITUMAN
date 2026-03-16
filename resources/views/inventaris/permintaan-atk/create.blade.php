{{-- resources/views/inventaris/permintaan-atk/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Permintaan ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Permintaan ATK', 'url' => route('inventaris.permintaan-atk.index'), 'active' => false],
        ['title' => 'Buat Permintaan', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('page_header')
<div>
    <h1 class="page-title">Buat Permintaan ATK</h1>
    <p class="page-subtitle">Isi formulir di bawah untuk mengajukan permintaan ATK baru</p>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('inventaris.permintaan-atk.store') }}" method="POST"
          x-data="permintaanForm()" @submit="validateForm">
        @csrf

        <div class="space-y-5">

            {{-- Section: Informasi Permintaan --}}
            <div class="card">
                <div class="section-header !mb-4">
                    <div>
                        <h2 class="section-title">Informasi Permintaan</h2>
                        <p class="section-desc">Data dasar pengajuan permintaan ATK</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="input-group">
                        <label class="input-label">
                            Pegawai Peminta
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <select name="pegawai_id"
                                class="input-field @error('pegawai_id') input-error @enderror"
                                required>
                            <option value="">— Pilih Pegawai —</option>
                            @foreach($pegawai as $p)
                            <option value="{{ $p->id }}" {{ old('pegawai_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }}{{ $p->nip ? ' · ' . $p->nip : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('pegawai_id')
                        <span class="input-hint-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label class="input-label">
                            Tanggal Permintaan
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <input type="date" name="tanggal_permintaan"
                               value="{{ old('tanggal_permintaan', date('Y-m-d')) }}"
                               class="input-field @error('tanggal_permintaan') input-error @enderror"
                               required>
                        @error('tanggal_permintaan')
                        <span class="input-hint-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group md:col-span-2">
                        <label class="input-label">Keterangan</label>
                        <textarea name="keterangan" rows="2"
                                  class="input-field @error('keterangan') input-error @enderror"
                                  placeholder="Keterangan tambahan (opsional)...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <span class="input-hint-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section: Daftar Item ATK --}}
            <div class="card">
                <div class="section-header !mb-4">
                    <div>
                        <h2 class="section-title">Daftar Item ATK</h2>
                        <p class="section-desc">
                            Tambahkan item ATK yang dibutuhkan
                            <span class="font-medium text-navy-600 dark:text-navy-300"
                                  x-text="'(' + items.length + ' item)'"></span>
                        </p>
                    </div>
                    <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Item
                    </button>
                </div>

                {{-- Header kolom --}}
                <div class="hidden md:grid md:grid-cols-12 gap-3 px-1 mb-2">
                    <div class="md:col-span-5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ATK</div>
                    <div class="md:col-span-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</div>
                    <div class="md:col-span-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</div>
                    <div class="md:col-span-1"></div>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3 rounded-xl bg-gray-50 dark:bg-navy-800/60 border border-gray-100 dark:border-navy-700"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0">

                            {{-- No item (mobile only) --}}
                            <div class="md:hidden flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400"
                                      x-text="'Item #' + (index + 1)"></span>
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="btn-ghost btn-sm !p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="md:col-span-5">
                                <label class="input-label text-xs md:hidden">ATK <span class="text-red-500">*</span></label>
                                <select :name="'atk_id[' + index + ']'" class="input-field" required>
                                    <option value="">— Pilih ATK —</option>
                                    @foreach($atk as $a)
                                    <option value="{{ $a->id }}"
                                            data-stok="{{ $a->stok_tersedia }}"
                                            data-satuan="{{ $a->satuan }}"
                                            data-status="{{ $a->status }}">
                                        {{ $a->nama }}
                                        ({{ $a->kategori->nama ?? '-' }} · Stok: {{ $a->stok_tersedia }} {{ $a->satuan }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="input-label text-xs md:hidden">Jumlah <span class="text-red-500">*</span></label>
                                <input type="number" :name="'jumlah[' + index + ']'"
                                       class="input-field" min="1" placeholder="0" required>
                            </div>

                            <div class="md:col-span-4">
                                <label class="input-label text-xs md:hidden">Keterangan</label>
                                <input type="text" :name="'keterangan_item[' + index + ']'"
                                       class="input-field" placeholder="Keterangan (opsional)">
                            </div>

                            <div class="md:col-span-1 hidden md:flex items-center justify-center">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="table-action-delete" title="Hapus item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Tambah item shortcut --}}
                <button type="button" @click="addItem()"
                        class="mt-3 w-full py-2.5 rounded-xl border-2 border-dashed border-gray-200 dark:border-navy-700
                               text-sm text-gray-400 dark:text-gray-500 hover:border-navy-400 hover:text-navy-600
                               dark:hover:border-navy-500 dark:hover:text-navy-400 transition-colors duration-200">
                    + Tambah Item Lainnya
                </button>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between gap-3 pt-1">
                <a href="{{ route('inventaris.permintaan-atk.index') }}" class="btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Permintaan
                </button>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function permintaanForm() {
    return {
        items: [{ atk_id: '', jumlah: '', keterangan: '' }],

        addItem() {
            this.items.push({ atk_id: '', jumlah: '', keterangan: '' });
        },

        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },

        validateForm(e) {
            const hasItem = this.items.length > 0;
            if (!hasItem) {
                e.preventDefault();
                showToast('Tambahkan minimal 1 item ATK', 'error');
            }
        }
    }
}
</script>
@endpush
