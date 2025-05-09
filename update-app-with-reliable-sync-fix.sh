#!/bin/bash
# update-app-with-reliable-sync-fix.sh - Script untuk memperbarui aplikasi dengan sistem sinkronisasi yang handal (dengan perbaikan)

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dengan sistem sinkronisasi yang handal (dengan perbaikan)...${NC}\n"

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

# 4. Perbaiki data pivot yang bermasalah
echo -e "\n${YELLOW}4. Memperbaiki data pivot yang bermasalah...${NC}"
cat > fix-pivot-data.php << 'EOL'
<?php

// Script untuk memperbaiki data pivot yang bermasalah

// Load Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Mulai log
Log::info("=== FIX_PIVOT: Memulai perbaikan data pivot yang bermasalah ===");

// 1. Ambil semua relasi mechanic_service yang tidak memiliki week_start atau week_end
$invalidPivots = DB::table('mechanic_service')
    ->whereNull('week_start')
    ->orWhereNull('week_end')
    ->get();

Log::info("FIX_PIVOT: Found {$invalidPivots->count()} invalid pivot records without week dates");

// 2. Perbaiki relasi yang bermasalah
$count = 0;
foreach ($invalidPivots as $pivot) {
    try {
        // Set week dates
        $weekStart = Carbon::now()->startOfWeek()->format('Y-m-d');
        $weekEnd = Carbon::now()->endOfWeek()->format('Y-m-d');
        
        Log::info("FIX_PIVOT: Setting week dates for mechanic #{$pivot->mechanic_id} on service #{$pivot->service_id}");
        
        DB::table('mechanic_service')
            ->where('service_id', $pivot->service_id)
            ->where('mechanic_id', $pivot->mechanic_id)
            ->update([
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
            ]);
        
        $count++;
    } catch (\Exception $e) {
        Log::error("FIX_PIVOT: Error fixing pivot for mechanic #{$pivot->mechanic_id} on service #{$pivot->service_id}: " . $e->getMessage());
    }
}

Log::info("FIX_PIVOT: Fixed {$count} pivot records");

// 3. Ambil semua relasi mechanic_service yang tidak memiliki labor_cost
$invalidLaborCosts = DB::table('mechanic_service')
    ->whereNull('labor_cost')
    ->orWhere('labor_cost', 0)
    ->get();

Log::info("FIX_PIVOT: Found {$invalidLaborCosts->count()} invalid pivot records without labor cost");

// 4. Perbaiki relasi yang bermasalah
$count = 0;
foreach ($invalidLaborCosts as $pivot) {
    try {
        // Set default labor cost
        $defaultLaborCost = 50000;
        
        Log::info("FIX_PIVOT: Setting default labor cost for mechanic #{$pivot->mechanic_id} on service #{$pivot->service_id}");
        
        DB::table('mechanic_service')
            ->where('service_id', $pivot->service_id)
            ->where('mechanic_id', $pivot->mechanic_id)
            ->update([
                'labor_cost' => $defaultLaborCost,
            ]);
        
        $count++;
    } catch (\Exception $e) {
        Log::error("FIX_PIVOT: Error fixing labor cost for mechanic #{$pivot->mechanic_id} on service #{$pivot->service_id}: " . $e->getMessage());
    }
}

Log::info("FIX_PIVOT: Fixed {$count} labor cost records");

Log::info("=== FIX_PIVOT: Perbaikan data pivot selesai ===");

echo "Perbaikan data pivot selesai.\n";
EOL

docker cp fix-pivot-data.php $(docker-compose ps -q app):/var/www/html/
docker-compose exec -T app php fix-pivot-data.php
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal memperbaiki data pivot yang bermasalah"
  echo -e "   ${YELLOW}!${NC} Melanjutkan proses..."
else
  echo -e "   ${GREEN}✓${NC} Data pivot berhasil diperbaiki"
fi

# 5. Jalankan command untuk membangun ulang rekap montir
echo -e "\n${YELLOW}5. Membangun ulang rekap montir...${NC}"
docker-compose exec -T app php artisan mechanic:sync-reports --force
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal membangun ulang rekap montir"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Rekap montir berhasil dibangun ulang"

# 6. Jalankan cron job untuk memastikan scheduler berjalan
echo -e "\n${YELLOW}6. Mengatur cron job untuk menjalankan scheduler...${NC}"
(crontab -l 2>/dev/null | grep -v "artisan schedule:run" ; echo "* * * * * cd /hm-new/hm-production && docker-compose exec -T app php artisan schedule:run >> /dev/null 2>&1") | crontab -
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal mengatur cron job"
  echo -e "   ${YELLOW}!${NC} Anda perlu mengatur cron job secara manual dengan perintah:"
  echo -e "   ${YELLOW}!${NC} crontab -e"
  echo -e "   ${YELLOW}!${NC} Lalu tambahkan baris berikut:"
  echo -e "   ${YELLOW}!${NC} * * * * * cd /hm-new/hm-production && docker-compose exec -T app php artisan schedule:run >> /dev/null 2>&1"
else
  echo -e "   ${GREEN}✓${NC} Cron job berhasil diatur"
fi

echo -e "\n${GREEN}Pembaruan aplikasi dengan sistem sinkronisasi yang handal selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Sistem rekap montir sekarang menggunakan pendekatan multi-layer untuk memastikan data selalu akurat:"
echo -e "   - Observer untuk mendeteksi perubahan pada servis dan relasi montir-servis"
echo -e "   - Command untuk memperbarui rekap montir secara langsung"
echo -e "   - Scheduler untuk memvalidasi dan memperbaiki data secara berkala"
echo -e "2. Untuk menguji sistem, buat servis baru, tambahkan montir, dan tandai sebagai selesai"
echo -e "3. Periksa rekap montir untuk memastikan data terupdate dengan benar"
echo -e "4. Ubah status servis atau tambah/hapus montir untuk memastikan rekap montir tetap akurat"
echo -e "5. Periksa log untuk memastikan tidak ada error: docker-compose exec app cat storage/logs/laravel.log | grep -i \"ServiceObserver\|MechanicServiceObserver\|SyncMechanicReports\" | tail -n 100"
