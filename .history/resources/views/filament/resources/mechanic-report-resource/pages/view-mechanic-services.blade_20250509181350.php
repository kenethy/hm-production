<x-filament-panels::page>
    <div class="mb-4">
        <h2 class="text-xl font-bold">Riwayat Servis Montir: {{ $record->mechanic->name }}</h2>
        <p class="text-sm text-gray-500">Periode: {{ $record->week_start->format('d M Y') }} - {{ $record->week_end->format('d M Y') }}</p>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
