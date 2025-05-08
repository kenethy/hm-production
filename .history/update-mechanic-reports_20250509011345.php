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

// Ambil semua servis yang memiliki montir
$services = Service::whereHas('mechanics')
    ->orderBy('id')
    ->get();

Log::info("Ditemukan {$services->count()} servis yang memiliki montir");

// Perbarui rekap montir untuk setiap servis
$count = 0;
$completedCount = 0;
$cancelledCount = 0;
$inProgressCount = 0;

foreach ($services as $service) {
    try {
        Log::info("Memproses servis #{$service->id} dengan status {$service->status}...");
        MechanicReportHelper::updateReports($service);
        $count++;

        // Hitung berdasarkan status
        if ($service->status === 'completed') {
            $completedCount++;
        } else if ($service->status === 'cancelled') {
            $cancelledCount++;
        } else if ($service->status === 'in_progress') {
            $inProgressCount++;
        }
    } catch (\Exception $e) {
        Log::error("Error saat memproses servis #{$service->id}: " . $e->getMessage());
    }
}

Log::info("Statistik pembaruan rekap montir:");
Log::info("- Total servis diproses: {$count}");
Log::info("- Servis completed: {$completedCount}");
Log::info("- Servis cancelled: {$cancelledCount}");
Log::info("- Servis in_progress: {$inProgressCount}");

Log::info("Pembaruan rekap montir selesai. {$count} servis diproses.");

echo "Pembaruan rekap montir selesai. {$count} servis diproses.\n";
