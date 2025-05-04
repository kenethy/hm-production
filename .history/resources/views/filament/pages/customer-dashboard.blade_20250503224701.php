<x-filament::page>
    <div>
        @if (method_exists($this, 'getHeaderWidgets'))
        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->getHeaderWidgets() as $widget)
            {{ $widget }}
            @endforeach
        </div>
        @endif

        @if (method_exists($this, 'getFooterWidgets'))
        <div class="grid grid-cols-1 gap-4 mt-6">
            @foreach ($this->getFooterWidgets() as $widget)
            {{ $widget }}
            @endforeach
        </div>
        @endif
    </div>
</x-filament::page>