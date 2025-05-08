#!/bin/bash
# rebuild-mechanic-reports.sh - Script untuk membangun ulang rekap montir

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembangunan ulang rekap montir...${NC}\n"

# 1. Jalankan migrasi untuk menghapus data rekap montir
echo -e "${YELLOW}1. Menjalankan migrasi untuk menghapus data rekap montir...${NC}"
docker-compose exec -T app php artisan migrate
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan migrasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Migrasi berhasil dijalankan"

# 2. Clear cache dan optimize
echo -e "\n${YELLOW}2. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 3. Restart queue worker
echo -e "\n${YELLOW}3. Me-restart queue worker...${NC}"
docker-compose exec -T app php artisan queue:restart
echo -e "   ${GREEN}✓${NC} Queue worker di-restart"

# 4. Buat script PHP untuk memperbarui rekap montir
echo -e "\n${YELLOW}4. Membuat script PHP untuk memperbarui rekap montir...${NC}"
cat > rebuild-mechanic-reports.php << 'EOL'
<?php

// Script untuk membangun ulang rekap montir

// Load Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Events\ServiceStatusChanged;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

// Mulai log
Log::info("Memulai pembangunan ulang rekap montir...");

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
        
        // Dispatch event untuk memperbarui rekap montir
        event(new ServiceStatusChanged($service, 'in_progress'));
        
        $count++;
    } catch (\Exception $e) {
        Log::error("Error saat memproses servis #{$service->id}: " . $e->getMessage());
    }
}

Log::info("Pembangunan ulang rekap montir selesai. {$count} servis diproses.");

echo "Pembangunan ulang rekap montir selesai. {$count} servis diproses.\n";
EOL

echo -e "   ${GREEN}✓${NC} Script PHP berhasil dibuat"

# 5. Jalankan script PHP
echo -e "\n${YELLOW}5. Menjalankan script PHP...${NC}"
docker-compose exec -T app php rebuild-mechanic-reports.php
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script PHP"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script PHP berhasil dijalankan"

echo -e "\n${GREEN}Pembangunan ulang rekap montir selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log untuk memastikan tidak ada error: docker-compose exec app cat storage/logs/laravel.log | tail -n 100"
echo -e "2. Periksa rekap montir di database: docker-compose exec app php artisan tinker --execute=\"DB::table('mechanic_reports')->get()\""
