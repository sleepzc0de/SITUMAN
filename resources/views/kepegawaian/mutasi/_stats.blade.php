@php
    $cntTinggi = $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] >= 5)->count();
    $cntSedang = $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] >= 3 && $p->analisis_mutasi['prioritas'] < 5)->count();
    $cntRendah = $proyeksi->filter(fn($p) => $p->analisis_mutasi['prioritas'] < 3)->count();
    $cntTotal  = $proyeksi->count();

    $cards = [
        ['label' => 'Prioritas Tinggi', 'sub' => 'Segera dimutasi',  'count' => $cntTinggi,
         'grad' => 'from-rose-500 to-red-600',     'glow' => 'shadow-rose-500/25',
         'light' => 'text-rose-100',               'dot' => 'bg-rose-300',
         'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'],
        ['label' => 'Prioritas Sedang', 'sub' => 'Dipertimbangkan',  'count' => $cntSedang,
         'grad' => 'from-orange-500 to-amber-600', 'glow' => 'shadow-orange-500/25',
         'light' => 'text-orange-100',             'dot' => 'bg-orange-300',
         'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Prioritas Rendah', 'sub' => 'Dalam pemantauan', 'count' => $cntRendah,
         'grad' => 'from-amber-400 to-yellow-500', 'glow' => 'shadow-amber-400/25',
         'light' => 'text-amber-100',              'dot' => 'bg-amber-300',
         'icon' => 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Total Proyeksi',   'sub' => 'Pegawai aktif',    'count' => $cntTotal,
         'grad' => 'from-navy-600 to-navy-800',    'glow' => 'shadow-navy-600/25',
         'light' => 'text-navy-200',               'dot' => 'bg-navy-300',
         'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
    ];
@endphp

@foreach($cards as $card)
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $card['grad'] }}
            shadow-xl {{ $card['glow'] }} p-5 text-white
            transition-transform duration-200 hover:-translate-y-0.5">
    {{-- Decorative ring --}}
    <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white/10 pointer-events-none"></div>
    <div class="absolute -bottom-6 -left-2 w-20 h-20 rounded-full bg-black/10 pointer-events-none"></div>

    <div class="relative flex items-start justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $card['dot'] }} animate-pulse"></span>
                <p class="text-xs font-semibold {{ $card['light'] }} uppercase tracking-widest">
                    {{ $card['label'] }}
                </p>
            </div>
            <p class="text-4xl font-black tabular-nums tracking-tight">{{ $card['count'] }}</p>
            <p class="text-xs {{ $card['light'] }} font-medium">{{ $card['sub'] }}</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-white/15 backdrop-blur-sm
                    flex items-center justify-center flex-shrink-0 border border-white/20">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                 stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
            </svg>
        </div>
    </div>
</div>
@endforeach
