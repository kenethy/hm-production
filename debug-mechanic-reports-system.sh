#!/bin/bash
# debug-mechanic-reports-system.sh - Script untuk men-debug sistem rekap montir

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai debugging sistem rekap montir...${NC}\n"

# 1. Deploy perubahan
echo -e "${YELLOW}1. Men-deploy perubahan...${NC}"
git add .
git commit -m "Add debugging tools for mechanic reports system"
git push
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal men-deploy perubahan"
  echo -e "   ${YELLOW}!${NC} Melanjutkan tanpa men-deploy perubahan"
else
  echo -e "   ${GREEN}✓${NC} Perubahan berhasil di-deploy"
fi

# 2. Clear cache dan optimize
echo -e "\n${YELLOW}2. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 3. Restart container aplikasi
echo -e "\n${YELLOW}3. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

# 4. Buat script PHP untuk men-debug sistem rekap montir
echo -e "\n${YELLOW}4. Membuat script PHP untuk men-debug sistem rekap montir...${NC}"
cat > debug-mechanic-reports-system.php << 'EOL'
<?php

// Script untuk men-debug sistem rekap montir

// Load Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Events\ServiceStatusChanged;
use App\Helpers\DebugHelper;
use App\Listeners\UpdateMechanicReports;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

// Mulai log
Log::info("=== DEBUG_SYSTEM: Memulai debugging sistem rekap montir ===");

// 1. Periksa konfigurasi event
Log::info("DEBUG_SYSTEM: Memeriksa konfigurasi event");
$eventServiceProvider = app(\App\Providers\EventServiceProvider::class);
$events = $eventServiceProvider->listens();
Log::info("DEBUG_SYSTEM: Event listeners", [
    'events' => $events,
]);

// 2. Periksa tabel mechanic_reports
$mechanicReports = DB::table('mechanic_reports')->get();
Log::info("DEBUG_SYSTEM: Jumlah rekap montir: " . $mechanicReports->count());

// 3. Periksa tabel mechanic_service
$mechanicService = DB::table('mechanic_service')->get();
Log::info("DEBUG_SYSTEM: Jumlah relasi montir-servis: " . $mechanicService->count());

// 4. Periksa servis dengan status 'completed'
$completedServices = Service::where('status', 'completed')->get();
Log::info("DEBUG_SYSTEM: Jumlah servis dengan status 'completed': " . $completedServices->count());

// 5. Periksa servis dengan status 'completed' yang memiliki montir
$completedServicesWithMechanics = Service::where('status', 'completed')
    ->whereHas('mechanics')
    ->get();
Log::info("DEBUG_SYSTEM: Jumlah servis dengan status 'completed' yang memiliki montir: " . $completedServicesWithMechanics->count());

// 6. Periksa event listener
$listener = new UpdateMechanicReports();
Log::info("DEBUG_SYSTEM: Event listener dibuat");

// 7. Jalankan event listener untuk setiap servis dengan status 'completed'
$count = 0;
foreach ($completedServicesWithMechanics as $service) {
    try {
        Log::info("DEBUG_SYSTEM: Memproses servis #{$service->id}");
        
        // Log service details before processing
        DebugHelper::logServiceDetails($service->id);
        
        // Jalankan event listener secara manual
        $event = new ServiceStatusChanged($service, 'in_progress');
        $listener->handle($event);
        
        // Log service details after processing
        DebugHelper::logServiceDetails($service->id);
        
        $count++;
    } catch (\Exception $e) {
        Log::error("DEBUG_SYSTEM: Error saat memproses servis #{$service->id}: " . $e->getMessage(), [
            'exception' => $e,
        ]);
    }
}

// 8. Periksa tabel mechanic_reports setelah pemrosesan
$mechanicReportsAfter = DB::table('mechanic_reports')->get();
Log::info("DEBUG_SYSTEM: Jumlah rekap montir setelah pemrosesan: " . $mechanicReportsAfter->count());

// 9. Tampilkan detail rekap montir
foreach ($mechanicReportsAfter as $report) {
    Log::info("DEBUG_SYSTEM: Rekap montir #{$report->id}", [
        'mechanic_id' => $report->mechanic_id,
        'week_start' => $report->week_start,
        'week_end' => $report->week_end,
        'services_count' => $report->services_count,
        'total_labor_cost' => $report->total_labor_cost,
    ]);
}

// 10. Periksa event dispatcher
Log::info("DEBUG_SYSTEM: Memeriksa event dispatcher");
$dispatcher = Event::getFacadeRoot();
Log::info("DEBUG_SYSTEM: Event dispatcher class: " . get_class($dispatcher));

// 11. Periksa apakah event listener terdaftar
$listeners = Event::getListeners(ServiceStatusChanged::class);
Log::info("DEBUG_SYSTEM: Jumlah listeners untuk ServiceStatusChanged: " . count($listeners));
foreach ($listeners as $index => $listener) {
    if (is_array($listener) && is_object($listener[0])) {
        Log::info("DEBUG_SYSTEM: Listener #" . ($index + 1) . ": " . get_class($listener[0]));
    } else {
        Log::info("DEBUG_SYSTEM: Listener #" . ($index + 1) . ": " . gettype($listener));
    }
}

Log::info("=== DEBUG_SYSTEM: Debugging sistem rekap montir selesai ===");

echo "Debugging sistem rekap montir selesai. {$count} servis diproses.\n";
EOL

echo -e "   ${GREEN}✓${NC} Script PHP berhasil dibuat"

# 5. Salin script PHP ke container
echo -e "\n${YELLOW}5. Menyalin script PHP ke container...${NC}"
docker cp debug-mechanic-reports-system.php $(docker-compose ps -q app):/var/www/html/
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menyalin script PHP ke container"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil disalin ke container"

# 6. Jalankan script PHP
echo -e "\n${YELLOW}6. Menjalankan script PHP...${NC}"
docker-compose exec -T app php debug-mechanic-reports-system.php
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script PHP"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil dijalankan"

# 7. Tampilkan log Laravel
echo -e "\n${YELLOW}7. Menampilkan log Laravel...${NC}"
docker-compose exec -T app cat storage/logs/laravel.log | grep -i "DEBUG_" | tail -n 50
echo -e "   ${GREEN}✓${NC} Log Laravel berhasil ditampilkan"

echo -e "\n${GREEN}Debugging sistem rekap montir selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log lengkap untuk informasi lebih detail: docker-compose exec app cat storage/logs/laravel.log | grep -i \"DEBUG_\" | tail -n 100"
echo -e "2. Periksa rekap montir di database: docker-compose exec app php artisan tinker --execute=\"DB::table('mechanic_reports')->get()\""
echo -e "3. Buat servis baru dan tandai sebagai selesai untuk menguji apakah rekap montir diperbarui secara otomatis"
echo -e "4. Periksa log setelah membuat servis baru: docker-compose exec app cat storage/logs/laravel.log | grep -i \"DEBUG_\" | tail -n 100"
