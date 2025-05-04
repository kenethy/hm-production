@props(['breadcrumbs' => []])

<nav class="bg-gray-100 py-3 mb-6">
    <div class="container mx-auto px-4">
        <ol class="flex flex-wrap items-center text-sm">
            <li class="flex items-center">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-red-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
            </li>
            
            @foreach($breadcrumbs as $breadcrumb)
                <li class="flex items-center">
                    <span class="mx-2 text-gray-400">/</span>
                    @if(isset($breadcrumb['url']))
                        <a href="{{ $breadcrumb['url'] }}" class="text-gray-600 hover:text-red-600 transition-colors">
                            {{ $breadcrumb['label'] }}
                        </a>
                    @else
                        <span class="text-gray-800 font-medium">{{ $breadcrumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
