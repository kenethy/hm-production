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
            </p>
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium">
                @php
                $status = request()->query('status', 'completed');
                $title = match($status) {
                'completed' => 'Servis yang Telah Diselesaikan',
                'in_progress' => 'Servis dalam Pengerjaan',
                'cancelled' => 'Servis yang Dibatalkan',
                default => 'Semua Servis',
                };
                @endphp
                {{ $title }}
            </h3>
            <a href="{{ MechanicReportResource::getUrl('edit', ['record' => $record]) }}"
                class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600">
                Kembali
            </a>
        </div>

        <div class="flex space-x-2 mb-4">
            <a href="?status=completed"
                class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'completed' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
                Selesai
            </a>
            <a href="?status=in_progress"
                class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'in_progress' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
                Dalam Pengerjaan
            </a>
            <a href="?status=cancelled"
                class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'cancelled' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
                Dibatalkan
            </a>
            <a href="?status=all"
                class="px-4 py-2 text-sm font-medium rounded-md {{ $status === 'all' ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' }}">
                Semua Status
            </a>
        </div>

        <div class="overflow-hidden overflow-x-auto border border-gray-300 rounded-xl">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis
                            Servis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor
                            Plat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor
                            Nota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya
                            Jasa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($services as $service)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{
                            $service->service_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->customer_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->license_plate }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->invoice_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{
                            number_format($service->labor_cost, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($service->status == 'completed')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                            @elseif($service->status == 'in_progress')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Dalam
                                Pengerjaan</span>
                            @elseif($service->status == 'cancelled')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->created_at->format('d
                            M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $service->completed_at ?
                            $service->completed_at->format('d M Y H:i') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('filament.admin.resources.services.edit', ['record' => $service->id]) }}"
                                class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>