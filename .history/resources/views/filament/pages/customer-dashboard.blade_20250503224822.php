<x-filament::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @livewire(App\Filament\Widgets\CustomerStatsWidget::class)
            @livewire(App\Filament\Widgets\CustomerChartWidget::class)
        </div>

        <div class="grid grid-cols-1 gap-4">
            @livewire(App\Filament\Widgets\CustomersNeedingFollowUpWidget::class)
        </div>
    </div>
</x-filament::page>