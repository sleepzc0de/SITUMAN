{{-- resources/views/inventaris/permintaan-atk/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Permintaan ATK')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['title' => 'Inventaris', 'url' => null, 'active' => false],
        ['title' => 'Permintaan ATK', 'url' => route('inventaris.permintaan-atk.index'), 'active' => false],
        ['title' => 'Edit Permintaan', 'url' => null, 'active' => true]
    ]" />
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="card">
        <div class="border-b border-gray-200 dark:border-navy-700 pb-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Permintaan ATK</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $permintaanAtk->nomor_permintaan }}</p>
        </div>

        <form action="{{ route('inventaris.permintaan-atk.update', $permintaanAtk) }}" method="POST" x-data="permintaanForm()">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Pegawai -->
                <div class="input-group">
                    <label class="input-label">Pegawai <span class="text-red-500">*</span></label>
                    <select name="pegawai_id" class="input-field @error('pegawai_id') border-red-500 @enderror" required>
                        <option value="">Pilih Pegawai</option>
                        @foreach($pegawai as $p)
                            <option value="{{ $p->id }}"
                                {{ old('pegawai_id', $permintaanAtk->pegawai_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }} - {{ $p->nip }}
                            </option>
                        @endforeach
                    </select>
                    @error('pegawai_id')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tanggal Permintaan -->
                <div class="input-group">
                    <label class="input-label">Tanggal Permintaan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_permintaan"
                        value="{{ old('tanggal_permintaan', $permintaanAtk->tanggal_permintaan->format('Y-m-d')) }}"
                        class="input-field @error('tanggal_permintaan') border-red-500 @enderror" required>
                    @error('tanggal_permintaan')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="input-group md:col-span-2">
                    <label class="input-label">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                        class="input-field @error('keterangan') border-red-500 @enderror"
                        placeholder="Keterangan tambahan...">{{ old('keterangan', $permintaanAtk->keterangan) }}</textarea>
                    @error('keterangan')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Item ATK -->
            <div class="border-t border-gray-200 dark:border-navy-700 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Item ATK</h3>
                    <button type="button" @click="addItem()" class="btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Item
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 bg-gray-50 dark:bg-navy-800 rounded-xl">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <!-- ATK -->
                                <div class="md:col-span-5">
                                    <label class="input-label text-xs">ATK <span class="text-red-500">*</span></label>
                                    <select :name="'atk_id[' + index + ']'" class="input-field" required x-model="item.atk_id">
                                        <option value="">Pilih ATK</option>
                                        @foreach($atk as $a)
                                            <option value="{{ $a->id }}">
                                                {{ $a->nama }} (Stok: {{ $a->stok_tersedia }} {{ $a->satuan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jumlah -->
                                <div class="md:col-span-2">
                                    <label class="input-label text-xs">Jumlah <span class="text-red-500">*</span></label>
                                    <input type="number" :name="'jumlah[' + index + ']'"
                                        class="input-field" min="1" required x-model="item.jumlah">
                                </div>

                                <!-- Keterangan Item -->
                                <div class="md:col-span-4">
                                    <label class="input-label text-xs">Keterangan</label>
                                    <input type="text" :name="'keterangan_item[' + index + ']'"
                                        class="input-field" placeholder="Opsional" x-model="item.keterangan">
                                </div>

                                <!-- Remove Button -->
                                <div class="md:col-span-1 flex items-end">
                                    <button type="button" @click="removeItem(index)"
                                        class="w-full btn-danger text-sm py-3" x-show="items.length > 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('inventaris.permintaan-atk.show', $permintaanAtk) }}" class="btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Permintaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function permintaanForm() {
    return {
        items: [
            @foreach($permintaanAtk->details as $detail)
            {
                atk_id: '{{ $detail->atk_id }}',
                jumlah: {{ $detail->jumlah }},
                keterangan: '{{ $detail->keterangan }}'
            },
            @endforeach
        ],

        addItem() {
            this.items.push({
                atk_id: '',
                jumlah: '',
                keterangan: ''
            });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        }
    }
}
</script>
@endpush
