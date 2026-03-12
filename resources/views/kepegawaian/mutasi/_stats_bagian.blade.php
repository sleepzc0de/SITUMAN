@if($statsBagian->isNotEmpty())
@php $total = $proyeksi->count(); @endphp
<div class="space-y-3">
    @foreach($statsBagian->take(6) as $namaBagian => $jumlah)
    @php $pct = $total > 0 ? round(($jumlah / $total) * 100) : 0; @endphp
    <div class="flex items-center gap-3 group">
        <p class="text-sm text-gray-600 dark:text-gray-400 w-40 truncate flex-shrink-0
                  group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">
            {{ $namaBagian ?: '—' }}
        </p>
        <div class="flex-1 h-2 bg-gray-100 dark:bg-navy-700 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-navy-500 to-navy-600 rounded-full
                        transition-all duration-500"
                 style="width: {{ $pct }}%"></div>
        </div>
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums w-4 text-right">
                {{ $jumlah }}
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500 w-8">({{ $pct }}%)</span>
        </div>
    </div>
    @endforeach
</div>
@else
<p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Tidak ada data</p>
@endif
