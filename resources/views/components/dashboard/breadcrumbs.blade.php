@props([
    'breadcrumbs',
])

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
        <li>
            <a href="{{ route('home') }}" class="flex items-center hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                <x-heroicon-s-home class="w-4 h-4 mr-2" />
                Home
            </a>
        </li>
        @foreach($breadcrumbs ?? [] as $breadcrumb)
            <li class="flex items-center">
                <x-heroicon-s-chevron-right class="w-5 h-5 text-gray-400" />
                @if(!$loop->last)
                    <a href="{{ $breadcrumb['url'] }}" class="hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        {{ $breadcrumb['name'] }}
                    </a>
                @else
                    <span class="text-gray-800 dark:text-gray-200 font-medium" aria-current="page">
                        {{ $breadcrumb['name'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
