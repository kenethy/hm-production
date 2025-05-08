#!/bin/bash
# update-app-with-sync-events.sh - Script untuk memperbarui aplikasi dengan event sinkron

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dengan event sinkron...${NC}\n"

# 1. Pull perubahan terbaru dari Git
echo -e "${YELLOW}1. Mengambil perubahan terbaru dari Git...${NC}"
git pull
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal mengambil perubahan terbaru dari Git"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Perubahan terbaru berhasil diambil"

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

# 4. Buat script PHP untuk membangun ulang rekap montir
echo -e "\n${YELLOW}4. Membuat script PHP untuk membangun ulang rekap montir...${NC}"
cat > rebuild-mechanic-reports-sync.php << 'EOL'
<?php

// Script untuk membangun ulang rekap montir dengan event sinkron

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
Log::info("=== SYNC: Memulai pembangunan ulang rekap montir dengan event sinkron ===");

// 1. Truncate tabel mechanic_reports
Log::info("SYNC: Truncating mechanic_reports table");
DB::table('mechanic_reports')->truncate();
Log::info("SYNC: mechanic_reports table truncated");

// 2. Ambil semua servis dengan status 'completed'
$completedServices = Service::where('status', 'completed')
    ->with('mechanics')
    ->get();

Log::info("SYNC: Found {$completedServices->count()} completed services with mechanics");

// 3. Buat listener
$listener = new UpdateMechanicReports();

// 4. Proses setiap servis
$count = 0;
foreach ($completedServices as $service) {
    try {
        Log::info("SYNC: Processing service #{$service->id}");
        
        // Dispatch event secara sinkron
        $event = new ServiceStatusChanged($service, 'in_progress');
        $listener->handle($event);
        
        $count++;
    } catch (\Exception $e) {
        Log::error("SYNC: Error processing service #{$service->id}: " . $e->getMessage());
    }
}

// 5. Tampilkan rekap montir
$mechanicReports = DB::table('mechanic_reports')->get();
Log::info("SYNC: Created {$mechanicReports->count()} mechanic reports");

foreach ($mechanicReports as $report) {
    Log::info("SYNC: Report #{$report->id}", [
        'mechanic_id' => $report->mechanic_id,
        'week_start' => $report->week_start,
        'week_end' => $report->week_end,
        'services_count' => $report->services_count,
        'total_labor_cost' => $report->total_labor_cost,
    ]);
}

Log::info("=== SYNC: Pembangunan ulang rekap montir dengan event sinkron selesai ===");

echo "Pembangunan ulang rekap montir dengan event sinkron selesai. {$count} servis diproses.\n";
EOL

echo -e "   ${GREEN}✓${NC} Script PHP berhasil dibuat"

# 5. Salin script PHP ke container
echo -e "\n${YELLOW}5. Menyalin script PHP ke container...${NC}"
docker cp rebuild-mechanic-reports-sync.php $(docker-compose ps -q app):/var/www/html/
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menyalin script PHP ke container"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil disalin ke container"

# 6. Jalankan script PHP
echo -e "\n${YELLOW}6. Menjalankan script PHP...${NC}"
docker-compose exec -T app php rebuild-mechanic-reports-sync.php
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script PHP"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil dijalankan"

echo -e "\n${GREEN}Pembaruan aplikasi dengan event sinkron selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log untuk memastikan tidak ada error: docker-compose exec app cat storage/logs/laravel.log | grep -i \"SYNC:\" | tail -n 100"
echo -e "2. Periksa rekap montir di database: docker-compose exec app php artisan tinker --execute=\"DB::table('mechanic_reports')->get()\""
echo -e "3. Buat servis baru dan tandai sebagai selesai untuk menguji apakah rekap montir diperbarui secara otomatis"
