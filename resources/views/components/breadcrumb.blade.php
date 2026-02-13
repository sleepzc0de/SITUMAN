@props(['items' => []])

<nav class="flex items-center text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="flex items-center flex-wrap space-x-2">
        <li class="flex items-center">
            <a href="{{ route('dashboard') }}" class="hover:text-navy-600 dark:hover:text-gold-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </a>
        </li>

        @foreach($items as $item)
            <li class="flex items-center">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>

                @if($item['url'] && !$item['active'])
                    <a href="{{ $item['url'] }}" class="ml-2 hover:text-navy-600 dark:hover:text-gold-400 transition-colors">
                        {{ $item['title'] }}
                    </a>
                @else
                    <span class="ml-2 text-gray-700 dark:text-gray-300 font-medium" aria-current="page">
                        {{ $item['title'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
