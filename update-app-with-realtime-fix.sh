#!/bin/bash
# update-app-with-realtime-fix.sh - Script untuk memperbarui aplikasi dengan perbaikan real-time

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dengan perbaikan real-time...${NC}\n"

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
cat > rebuild-mechanic-reports-realtime.php << 'EOL'
<?php

// Script untuk membangun ulang rekap montir dengan perbaikan real-time

// Load Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mechanic;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Mulai log
Log::info("=== REBUILD_REALTIME: Memulai pembangunan ulang rekap montir dengan perbaikan real-time ===");

// 1. Truncate tabel mechanic_reports
Log::info("REBUILD_REALTIME: Truncating mechanic_reports table");
DB::table('mechanic_reports')->truncate();
Log::info("REBUILD_REALTIME: mechanic_reports table truncated");

// 2. Ambil semua servis dengan status 'completed'
$completedServices = Service::where('status', 'completed')
    ->whereHas('mechanics')
    ->get();

Log::info("REBUILD_REALTIME: Found {$completedServices->count()} completed services with mechanics");

// 3. Proses setiap servis
$count = 0;
foreach ($completedServices as $service) {
    try {
        Log::info("REBUILD_REALTIME: Processing service #{$service->id}");
        
        // Proses setiap montir
        foreach ($service->mechanics as $mechanic) {
            try {
                // Set week dates jika belum diatur
                if (empty($mechanic->pivot->week_start) || empty($mechanic->pivot->week_end)) {
                    $weekStart = now()->startOfWeek()->format('Y-m-d');
                    $weekEnd = now()->endOfWeek()->format('Y-m-d');
                    
                    Log::info("REBUILD_REALTIME: Setting week dates for mechanic #{$mechanic->id} on service #{$service->id}");
                    
                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                    ]);
                } else {
                    $weekStart = $mechanic->pivot->week_start;
                    $weekEnd = $mechanic->pivot->week_end;
                }
                
                // Set labor_cost jika belum diatur
                $laborCost = $mechanic->pivot->labor_cost;
                if (empty($laborCost) || $laborCost == 0) {
                    $defaultLaborCost = 50000;
                    
                    Log::info("REBUILD_REALTIME: Setting default labor cost for mechanic #{$mechanic->id} on service #{$service->id}");
                    
                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'labor_cost' => $defaultLaborCost,
                    ]);
                    
                    $laborCost = $defaultLaborCost;
                }
                
                Log::info("REBUILD_REALTIME: Mechanic #{$mechanic->id} on service #{$service->id}", [
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'labor_cost' => $laborCost,
                ]);
                
                // Cari atau buat rekap montir
                $report = DB::table('mechanic_reports')
                    ->where('mechanic_id', $mechanic->id)
                    ->where('week_start', $weekStart)
                    ->where('week_end', $weekEnd)
                    ->first();
                
                if ($report) {
                    // Update rekap yang sudah ada
                    $currentLaborCost = $report->total_labor_cost;
                    $newLaborCost = $currentLaborCost + $laborCost;
                    $newServicesCount = $report->services_count + 1;
                    
                    DB::table('mechanic_reports')
                        ->where('id', $report->id)
                        ->update([
                            'services_count' => $newServicesCount,
                            'total_labor_cost' => $newLaborCost,
                            'updated_at' => now(),
                        ]);
                    
                    Log::info("REBUILD_REALTIME: Updated report #{$report->id} for mechanic #{$mechanic->id}", [
                        'services_count' => $newServicesCount,
                        'total_labor_cost' => $newLaborCost,
                    ]);
                } else {
                    // Buat rekap baru
                    $reportId = DB::table('mechanic_reports')->insertGetId([
                        'mechanic_id' => $mechanic->id,
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                        'services_count' => 1,
                        'total_labor_cost' => $laborCost,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    Log::info("REBUILD_REALTIME: Created new report #{$reportId} for mechanic #{$mechanic->id}", [
                        'services_count' => 1,
                        'total_labor_cost' => $laborCost,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("REBUILD_REALTIME: Error processing mechanic #{$mechanic->id} on service #{$service->id}: " . $e->getMessage());
            }
        }
        
        $count++;
    } catch (\Exception $e) {
        Log::error("REBUILD_REALTIME: Error processing service #{$service->id}: " . $e->getMessage());
    }
}

// 4. Tampilkan rekap montir
$mechanicReports = DB::table('mechanic_reports')->get();
Log::info("REBUILD_REALTIME: Created {$mechanicReports->count()} mechanic reports");

foreach ($mechanicReports as $report) {
    Log::info("REBUILD_REALTIME: Report #{$report->id}", [
        'mechanic_id' => $report->mechanic_id,
        'week_start' => $report->week_start,
        'week_end' => $report->week_end,
        'services_count' => $report->services_count,
        'total_labor_cost' => $report->total_labor_cost,
    ]);
}

Log::info("=== REBUILD_REALTIME: Pembangunan ulang rekap montir dengan perbaikan real-time selesai ===");

echo "Pembangunan ulang rekap montir dengan perbaikan real-time selesai. {$count} servis diproses.\n";
EOL

echo -e "   ${GREEN}✓${NC} Script PHP berhasil dibuat"

# 5. Salin script PHP ke container
echo -e "\n${YELLOW}5. Menyalin script PHP ke container...${NC}"
docker cp rebuild-mechanic-reports-realtime.php $(docker-compose ps -q app):/var/www/html/
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menyalin script PHP ke container"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil disalin ke container"

# 6. Jalankan script PHP
echo -e "\n${YELLOW}6. Menjalankan script PHP...${NC}"
docker-compose exec -T app php rebuild-mechanic-reports-realtime.php
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script PHP"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil dijalankan"

echo -e "\n${GREEN}Pembaruan aplikasi dengan perbaikan real-time selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log untuk memastikan tidak ada error: docker-compose exec app cat storage/logs/laravel.log | grep -i \"REBUILD_REALTIME:\" | tail -n 100"
echo -e "2. Periksa rekap montir di database: docker-compose exec app php artisan tinker --execute=\"DB::table('mechanic_reports')->get()\""
echo -e "3. Buat servis baru, tandai sebagai selesai, lalu ubah menjadi dibatalkan untuk menguji apakah rekap montir diperbarui dengan benar"
