{{-- resources/views/kepegawaian/pegawai/_detail-row.blade.php --}}
<dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
    @foreach($rows as $row)
    @if(!empty($row['value']))
    <div class="border-b border-gray-50 dark:border-navy-700 pb-4 last:border-0">
        <dt class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">
            {{ $row['label'] }}
        </dt>
        <dd class="text-sm font-medium text-gray-900 dark:text-white {{ isset($row['mono']) && $row['mono'] ? 'font-mono' : '' }}">
            @if(isset($row['link']))
                <a href="{{ $row['link'] }}{{ $row['value'] }}" class="text-navy-600 dark:text-navy-400 hover:underline">
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
