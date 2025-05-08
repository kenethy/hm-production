<?php

// Script untuk memperbarui rekap montir yang sudah ada

// Load Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helpers\MechanicReportHelper;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

// Mulai log
Log::info("Memulai pembaruan rekap montir...");

// Ambil semua servis dengan status 'completed'
$services = Service::where('status', 'completed')
    ->whereHas('mechanics')
    ->orderBy('id')
    ->get();

Log::info("Ditemukan {$services->count()} servis dengan status 'completed'");

// Perbarui rekap montir untuk setiap servis
$count = 0;
foreach ($services as $service) {
    try {
        Log::info("Memproses servis #{$service->id}...");
        MechanicReportHelper::updateReports($service);
        $count++;
    } catch (\Exception $e) {
        Log::error("Error saat memproses servis #{$service->id}: " . $e->getMessage());
    }
}

Log::info("Pembaruan rekap montir selesai. {$count} servis diproses.");

echo "Pembaruan rekap montir selesai. {$count} servis diproses.\n";
