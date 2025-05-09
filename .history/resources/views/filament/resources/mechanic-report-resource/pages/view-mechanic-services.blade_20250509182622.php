<x-filament-panels::page>
    <div class="mb-6">
        <h2 class="text-xl font-bold">Riwayat Servis Montir: {{ $record->mechanic->name }}</h2>
        <p class="text-sm text-gray-500">Periode: {{ $record->week_start->format('d M Y') }} - {{
            $record->week_end->format('d M Y') }}</p>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">Total Servis</h3>
                <p class="text-2xl font-bold">{{ $record->services_count }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">Total Biaya Jasa</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($record->total_labor_cost, 0, ',', '.') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">Status Pembayaran</h3>
                <p class="text-2xl font-bold">
                    @if($record->is_paid)
                    <span class="text-green-600">Sudah Dibayar</span>
                    @else
                    <span class="text-red-600">Belum Dibayar</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700">
                Berikut adalah daftar servis yang telah diselesaikan oleh montir pada periode ini.
                Secara default, hanya menampilkan servis dengan status "Selesai".
            </p>
        </div>
    </div>

    <div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>