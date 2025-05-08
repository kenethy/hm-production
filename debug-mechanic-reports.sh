#!/bin/bash
# debug-mechanic-reports.sh - Script untuk men-debug sistem rekap montir

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai debugging sistem rekap montir...${NC}\n"

# 1. Clear cache dan optimize
echo -e "${YELLOW}1. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 2. Buat script PHP untuk men-debug rekap montir
echo -e "\n${YELLOW}2. Membuat script PHP untuk men-debug rekap montir...${NC}"
cat > debug-mechanic-reports.php << 'EOL'
<?php

// Script untuk men-debug rekap montir

// Load Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Events\ServiceStatusChanged;
use App\Listeners\UpdateMechanicReports;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Mulai log
Log::info("=== DEBUG: Memulai debugging rekap montir ===");

// 1. Periksa tabel mechanic_reports
$mechanicReports = DB::table('mechanic_reports')->get();
Log::info("DEBUG: Jumlah rekap montir: " . $mechanicReports->count());

// 2. Periksa tabel mechanic_service
$mechanicService = DB::table('mechanic_service')->get();
Log::info("DEBUG: Jumlah relasi montir-servis: " . $mechanicService->count());

// 3. Periksa servis dengan status 'completed'
$completedServices = Service::where('status', 'completed')->get();
Log::info("DEBUG: Jumlah servis dengan status 'completed': " . $completedServices->count());

// 4. Periksa servis dengan status 'completed' yang memiliki montir
$completedServicesWithMechanics = Service::where('status', 'completed')
    ->whereHas('mechanics')
    ->get();
Log::info("DEBUG: Jumlah servis dengan status 'completed' yang memiliki montir: " . $completedServicesWithMechanics->count());

// 5. Periksa event listener
$listener = new UpdateMechanicReports();
Log::info("DEBUG: Event listener dibuat");

// 6. Jalankan event listener untuk setiap servis dengan status 'completed'
$count = 0;
foreach ($completedServicesWithMechanics as $service) {
    try {
        Log::info("DEBUG: Memproses servis #{$service->id}...");
        
        // Periksa montir untuk servis ini
        $mechanics = $service->mechanics;
        Log::info("DEBUG: Servis #{$service->id} memiliki " . $mechanics->count() . " montir");
        
        // Periksa detail montir
        foreach ($mechanics as $mechanic) {
            Log::info("DEBUG: Montir #{$mechanic->id} terkait dengan servis #{$service->id}", [
                'labor_cost' => $mechanic->pivot->labor_cost,
                'week_start' => $mechanic->pivot->week_start,
                'week_end' => $mechanic->pivot->week_end,
            ]);
        }
        
        // Jalankan event listener secara manual
        $event = new ServiceStatusChanged($service, 'in_progress');
        $listener->handle($event);
        
        $count++;
    } catch (\Exception $e) {
        Log::error("DEBUG: Error saat memproses servis #{$service->id}: " . $e->getMessage(), [
            'exception' => $e,
        ]);
    }
}

// 7. Periksa tabel mechanic_reports setelah pemrosesan
$mechanicReportsAfter = DB::table('mechanic_reports')->get();
Log::info("DEBUG: Jumlah rekap montir setelah pemrosesan: " . $mechanicReportsAfter->count());

// 8. Tampilkan detail rekap montir
foreach ($mechanicReportsAfter as $report) {
    Log::info("DEBUG: Rekap montir #{$report->id}", [
        'mechanic_id' => $report->mechanic_id,
        'week_start' => $report->week_start,
        'week_end' => $report->week_end,
        'services_count' => $report->services_count,
        'total_labor_cost' => $report->total_labor_cost,
    ]);
}

Log::info("=== DEBUG: Debugging rekap montir selesai ===");

echo "Debugging rekap montir selesai. {$count} servis diproses.\n";
EOL

echo -e "   ${GREEN}✓${NC} Script PHP berhasil dibuat"

# 3. Salin script PHP ke container
echo -e "\n${YELLOW}3. Menyalin script PHP ke container...${NC}"
docker cp debug-mechanic-reports.php $(docker-compose ps -q app):/var/www/html/
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menyalin script PHP ke container"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil disalin ke container"

# 4. Jalankan script PHP
echo -e "\n${YELLOW}4. Menjalankan script PHP...${NC}"
docker-compose exec -T app php debug-mechanic-reports.php
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script PHP"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil dijalankan"

# 5. Tampilkan log Laravel
echo -e "\n${YELLOW}5. Menampilkan log Laravel...${NC}"
docker-compose exec -T app cat storage/logs/laravel.log | grep -i "DEBUG:" | tail -n 50
echo -e "   ${GREEN}✓${NC} Log Laravel berhasil ditampilkan"

echo -e "\n${GREEN}Debugging sistem rekap montir selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log lengkap untuk informasi lebih detail: docker-compose exec app cat storage/logs/laravel.log | grep -i \"DEBUG:\" | tail -n 100"
echo -e "2. Periksa rekap montir di database: docker-compose exec app php artisan tinker --execute=\"DB::table('mechanic_reports')->get()\""
