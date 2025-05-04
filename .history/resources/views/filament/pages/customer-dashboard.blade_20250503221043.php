<x-filament::page>
    <div class="flex flex-col gap-y-8">
        @if ($this->hasInfolist())
            {{ $this->infolist }}
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.customer-dashboard.widgets.start') }}

        @if ($this->hasHeader())
            {{ $this->header }}
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.customer-dashboard.widgets.end') }}
    </div>
</x-filament::page>
