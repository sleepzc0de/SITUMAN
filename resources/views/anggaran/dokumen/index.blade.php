@extends('layouts.app')

@section('title', 'Dokumen Capaian Output')

@section('content')
    <div class="space-y-6">
        <!-- Header & Actions -->
        <div class="card">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Dokumen Capaian Output</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola dokumen capaian output per kegiatan</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('anggaran.dokumen.create') }}" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Upload Dokumen
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="input-group">
                    <label class="input-label">RO</label>
                    <select name="ro" class="input-field" onchange="this.form.submit()">
                        <option value="all">Semua RO</option>
                        @foreach ($roList as $ro)
                            <option value="{{ $ro }}" {{ request('ro') == $ro ? 'selected' : '' }}>
                                {{ $ro }} - {{ get_ro_name($ro) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group">
                    <label class="input-label">Bulan</label>
                    <select name="bulan" class="input-field" onchange="this.form.submit()">
                        <option value="all">Semua Bulan</option>
                        @foreach ($bulanList as $bulan)
                            <option value="{{ $bulan }}" {{ request('bulan') == $bulan ? 'selected' : '' }}>
                                {{ ucfirst($bulan) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    @if (request()->hasAny(['ro', 'bulan']))
                        <a href="{{ route('anggaran.dokumen.index') }}" class="btn btn-outline w-full">
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-navy-800">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Nama Dokumen</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                RO</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Sub Komponen</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Bulan</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Upload Oleh</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Tanggal Upload</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-900 divide-y divide-gray-200 dark:divide-navy-700">
                        @forelse($dokumens as $index => $dokumen)
                            <tr class="hover:bg-gray-50 dark:hover:bg-navy-800 transition-colors">
                                <td class="px-4 py-3 text-gray-900 dark:text-white">
                                    {{ table_row_number($dokumens, $index) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 {{ file_icon_class($dokumen->file_path) }}" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $dokumen->nama_dokumen }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ $dokumen->ro }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">
                                    {{ truncate_text($dokumen->sub_komponen, 30) }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">
                                    {{ ucfirst($dokumen->bulan) }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $dokumen->user->nama }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ format_tanggal_short($dokumen->created_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Detail --}}
                                        <a href="{{ route('anggaran.dokumen.show', $dokumen->id) }}"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                            title="Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('anggaran.dokumen.edit', $dokumen->id) }}"
                                            class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>

                                        {{-- Download --}}
                                        <a href="{{ route('anggaran.dokumen.download', $dokumen->id) }}"
                                            class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                                            title="Download">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('anggaran.dokumen.destroy', $dokumen->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada dokumen capaian output</p>
                                    <p class="mt-1">Silakan upload dokumen baru</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($dokumens->hasPages())
                <div class="mt-6">
                    {{ $dokumens->appends(request()->query())->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
