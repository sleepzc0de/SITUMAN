{{-- Partial: _detail-row.blade.php
     Usage: @include('kepegawaian.pegawai._detail-row', ['rows' => [...]])
     Row format: ['label' => 'Label', 'value' => $val, 'mono' => true, 'link' => 'mailto:']
--}}
<dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
    @foreach($rows as $row)
    @if(!empty($row['value']))
    <div class="{{ ($row['span'] ?? false) ? 'sm:col-span-2' : '' }} border-b border-gray-50 dark:border-navy-700/60 pb-4 last:border-0">
        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1.5 flex items-center gap-1.5">
            @if(isset($row['icon']))
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $row['icon'] }}"/>
            </svg>
            @endif
            {{ $row['label'] }}
        </dt>
        <dd class="text-sm font-medium text-gray-900 dark:text-white {{ ($row['mono'] ?? false) ? 'font-mono' : '' }} {{ ($row['class'] ?? '') }}">
            @if(isset($row['badge']))
            <span class="badge {{ $row['badge'] }}">{{ $row['value'] }}</span>
            @elseif(isset($row['link']))
            <a href="{{ $row['link'] }}{{ $row['value'] }}"
               class="text-navy-600 dark:text-navy-400 hover:underline underline-offset-2 break-all">
                {{ $row['value'] }}
            </a>
            @else
            {{ $row['value'] }}
            @endif
        </dd>
    </div>
    @endif
    @endforeach
</dl>
