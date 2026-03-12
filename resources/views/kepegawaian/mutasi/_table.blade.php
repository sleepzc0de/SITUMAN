@forelse($proyeksi as $index => $p)
@php
    $skor = $p->analisis_mutasi['prioritas'];
    $lama = $p->analisis_mutasi['lama_jabatan'] ?? null;
    $prog = $p->analisis_mutasi['progress_jabatan'] ?? null;

    [$ringColor, $badgeClass, $badgeLabel, $barColor] = match(true) {
        $skor >= 5 => ['ring-rose-200 dark:ring-rose-800',   'badge-danger',  'Tinggi', 'from-rose-400 to-red-500'],
        $skor >= 3 => ['ring-orange-200 dark:ring-orange-800','badge-warning', 'Sedang', 'from-orange-400 to-amber-500'],
        default    => ['ring-amber-200 dark:ring-amber-800',  'badge-yellow',  'Rendah', 'from-amber-300 to-yellow-400'],
    };
@endphp
<tr class="group hover:bg-navy-50/50 dark:hover:bg-navy-700/20 transition-colors duration-150">

    {{-- No --}}
    <td class="pl-6 pr-3 py-4">
        <span class="text-xs font-bold text-gray-300 dark:text-gray-600 tabular-nums">
            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
        </span>
    </td>

    {{-- Pegawai --}}
    <td class="px-3 py-4">
        <div class="flex items-center gap-3">
            <div class="relative flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-br from-navy-500 to-navy-700 rounded-xl
                            flex items-center justify-center shadow-sm ring-2 ring-white
                            dark:ring-navy-800 group-hover:shadow-md transition-shadow">
                    <span class="text-xs font-black text-white uppercase tracking-wider">
                        {{ get_initials($p->nama) }}
                    </span>
                </div>
                {{-- Priority dot --}}
                <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2
                             border-white dark:border-navy-800
                             {{ $skor >= 5 ? 'bg-rose-500' : ($skor >= 3 ? 'bg-orange-500' : 'bg-amber-400') }}">
                </span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[150px]
                          group-hover:text-navy-700 dark:group-hover:text-navy-200 transition-colors">
                    {{ $p->nama }}
                </p>
                <p class="text-xs font-mono text-gray-400 dark:text-gray-500 mt-0.5">
                    {{ $p->nip }}
                </p>
            </div>
        </div>
    </td>

    {{-- Jabatan --}}
    <td class="px-3 py-4 max-w-[180px]">
        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
            {{ $p->jabatan ?? '—' }}
        </p>
        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
            @if($p->bagian)
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs
                         bg-navy-50 dark:bg-navy-700 text-navy-600 dark:text-navy-300
                         border border-navy-100 dark:border-navy-600 font-medium">
                {{ $p->bagian }}
            </span>
            @endif
            @if($p->eselon)
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs
                         bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400
                         border border-purple-100 dark:border-purple-800 font-medium">
                {{ $p->eselon }}
            </span>
            @endif
        </div>
    </td>

    {{-- Masa Jabatan --}}
    <td class="px-3 py-4 min-w-[140px]">
        @if($lama !== null)
        <div class="space-y-1.5">
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-black text-gray-900 dark:text-white tabular-nums">{{ $lama }}</span>
                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">/ 24 bln</span>
            </div>
            <div class="relative h-1.5 bg-gray-100 dark:bg-navy-700 rounded-full overflow-hidden">
                <div class="absolute inset-y-0 left-0 rounded-full
                            bg-gradient-to-r {{ $barColor }} transition-all duration-700"
                     style="width: {{ min(100, $prog) }}%"></div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 tabular-nums">{{ min(100,$prog) }}% terlampaui</p>
        </div>
        @else
        <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs text-gray-400 dark:text-gray-500">Tidak tersedia</span>
        </div>
        @endif
    </td>

    {{-- Prioritas --}}
    <td class="px-3 py-4">
        <div class="flex flex-col gap-1.5">
            <span class="{{ $badgeClass }} font-bold">{{ $badgeLabel }}</span>
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= 6; $i++)
                <div class="w-2.5 h-2.5 rounded-sm transition-colors
                            {{ $i <= $skor
                                ? ($skor >= 5 ? 'bg-rose-400' : ($skor >= 3 ? 'bg-orange-400' : 'bg-amber-400'))
                                : 'bg-gray-100 dark:bg-navy-700' }}">
                </div>
                @endfor
            </div>
        </div>
    </td>

    {{-- Rekomendasi --}}
    <td class="px-3 py-4">
        <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg
                    bg-gold-50 dark:bg-gold-900/10 border border-gold-200 dark:border-gold-900/30
                    w-fit">
            <svg class="w-3.5 h-3.5 text-gold-500 flex-shrink-0"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs font-semibold text-gold-700 dark:text-gold-400 whitespace-nowrap">
                {{ $p->analisis_mutasi['rekomendasi_waktu'] }}
            </span>
        </div>
    </td>

    {{-- Pertimbangan --}}
    <td class="px-3 py-4 max-w-[200px]">
        <ul class="space-y-1.5">
            @foreach(array_slice($p->analisis_mutasi['alasan'], 0, 2) as $alasan)
            <li class="flex items-start gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-orange-400 mt-1.5 flex-shrink-0"></div>
                <span class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $alasan }}</span>
            </li>
            @endforeach
            @if(count($p->analisis_mutasi['alasan']) > 2)
            <li class="text-xs text-navy-500 dark:text-navy-400 pl-3.5 font-medium">
                +{{ count($p->analisis_mutasi['alasan']) - 2 }} lainnya
            </li>
            @endif
        </ul>
    </td>

    {{-- Aksi --}}
    <td class="pl-3 pr-6 py-4">
        <a href="{{ route('kepegawaian.mutasi.show', $p) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                  text-navy-600 dark:text-navy-300 bg-navy-50 dark:bg-navy-700/50
                  border border-navy-100 dark:border-navy-600
                  hover:bg-navy-100 dark:hover:bg-navy-700 hover:border-navy-200
                  transition-all duration-150 group/btn whitespace-nowrap">
            <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-x-0.5"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
            Detail
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="8">
        <div class="flex flex-col items-center justify-center py-20 gap-4">
            <div class="relative">
                <div class="w-20 h-20 rounded-3xl bg-gray-100 dark:bg-navy-700/50
                            flex items-center justify-center">
                    <svg class="w-9 h-9 text-gray-300 dark:text-gray-600"
                         fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                    Tidak Ada Data Proyeksi
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 max-w-xs">
                    Tidak ditemukan pegawai yang memenuhi kriteria mutasi dengan filter saat ini
                </p>
            </div>
        </div>
    </td>
</tr>
@endforelse
