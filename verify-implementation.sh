#!/bin/bash
# verify-implementation.sh

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai verifikasi implementasi event-listener untuk pembaruan laporan montir...${NC}\n"

# Direktori proyek - sesuaikan dengan path di VPS Anda
PROJECT_DIR="."
cd $PROJECT_DIR

# 1. Periksa keberadaan file-file kunci
echo -e "${YELLOW}1. Memeriksa keberadaan file-file kunci:${NC}"

files_to_check=(
  "app/Events/ServiceUpdated.php"
  "app/Listeners/UpdateMechanicReports.php"
  "app/Providers/EventServiceProvider.php"
)

files_not_expected=(
  "app/Observers/ServiceObserver.php"
)

for file in "${files_to_check[@]}"; do
  if [ -f "$file" ]; then
    echo -e "   ${GREEN}✓${NC} File $file ditemukan"
  else
    echo -e "   ${RED}✗${NC} File $file TIDAK ditemukan"
  fi
done

echo ""
for file in "${files_not_expected[@]}"; do
  if [ -f "$file" ]; then
    echo -e "   ${RED}✗${NC} File $file masih ada (seharusnya sudah dihapus)"
  else
    echo -e "   ${GREEN}✓${NC} File $file sudah dihapus dengan benar"
  fi
done

# 2. Periksa konten file-file kunci
echo -e "\n${YELLOW}2. Memeriksa konten file-file kunci:${NC}"

# Periksa ServiceUpdated event
if [ -f "app/Events/ServiceUpdated.php" ]; then
  if grep -q "class ServiceUpdated" "app/Events/ServiceUpdated.php" && 
     grep -q "public \$service;" "app/Events/ServiceUpdated.php"; then
    echo -e "   ${GREEN}✓${NC} File ServiceUpdated.php memiliki konten yang benar"
  else
    echo -e "   ${RED}✗${NC} File ServiceUpdated.php TIDAK memiliki konten yang diharapkan"
  fi
fi

# Periksa UpdateMechanicReports listener
if [ -f "app/Listeners/UpdateMechanicReports.php" ]; then
  if grep -q "class UpdateMechanicReports implements ShouldQueue" "app/Listeners/UpdateMechanicReports.php" && 
     grep -q "public function handle(ServiceUpdated \$event)" "app/Listeners/UpdateMechanicReports.php"; then
    echo -e "   ${GREEN}✓${NC} File UpdateMechanicReports.php memiliki konten yang benar"
  else
    echo -e "   ${RED}✗${NC} File UpdateMechanicReports.php TIDAK memiliki konten yang diharapkan"
  fi
fi

# Periksa EventServiceProvider
if [ -f "app/Providers/EventServiceProvider.php" ]; then
  if grep -q "ServiceUpdated::class" "app/Providers/EventServiceProvider.php" && 
     grep -q "UpdateMechanicReports::class" "app/Providers/EventServiceProvider.php"; then
    echo -e "   ${GREEN}✓${NC} File EventServiceProvider.php memiliki konfigurasi event yang benar"
  else
    echo -e "   ${RED}✗${NC} File EventServiceProvider.php TIDAK memiliki konfigurasi event yang diharapkan"
  fi
fi

# Periksa Service model
if [ -f "app/Models/Service.php" ]; then
  if grep -q "use App\\Events\\ServiceUpdated;" "app/Models/Service.php" && 
     grep -q "event(new ServiceUpdated(\$service));" "app/Models/Service.php"; then
    echo -e "   ${GREEN}✓${NC} File Service.php memiliki implementasi event yang benar"
  else
    echo -e "   ${RED}✗${NC} File Service.php TIDAK memiliki implementasi event yang diharapkan"
  fi
fi

# Periksa AppServiceProvider
if [ -f "app/Providers/AppServiceProvider.php" ]; then
  if grep -q "Service::observe(ServiceObserver::class);" "app/Providers/AppServiceProvider.php"; then
    echo -e "   ${RED}✗${NC} File AppServiceProvider.php masih memiliki referensi ke ServiceObserver"
  else
    echo -e "   ${GREEN}✓${NC} File AppServiceProvider.php tidak memiliki referensi ke ServiceObserver"
  fi
fi

# 3. Periksa konfigurasi Laravel
echo -e "\n${YELLOW}3. Memeriksa konfigurasi Laravel:${NC}"

# Periksa apakah cache konfigurasi sudah di-clear
echo -e "   ${YELLOW}!${NC} Menjalankan artisan config:clear untuk memastikan konfigurasi terbaru dimuat..."
php artisan config:clear > /dev/null 2>&1
if [ $? -eq 0 ]; then
  echo -e "   ${GREEN}✓${NC} Berhasil menjalankan config:clear"
else
  echo -e "   ${RED}✗${NC} Gagal menjalankan config:clear"
fi

# Periksa apakah event listener terdaftar
echo -e "   ${YELLOW}!${NC} Memeriksa event listener yang terdaftar..."
php artisan event:list | grep -q "App\\\\Events\\\\ServiceUpdated"
if [ $? -eq 0 ]; then
  echo -e "   ${GREEN}✓${NC} Event ServiceUpdated terdaftar dengan benar"
else
  echo -e "   ${RED}✗${NC} Event ServiceUpdated TIDAK terdaftar"
fi

# 4. Periksa log Laravel untuk error terkait
echo -e "\n${YELLOW}4. Memeriksa log Laravel untuk error terkait:${NC}"

if [ -f "storage/logs/laravel.log" ]; then
  # Periksa error terkait ServiceUpdated atau UpdateMechanicReports dalam 100 baris terakhir log
  tail -n 100 storage/logs/laravel.log | grep -i -E "error.*ServiceUpdated|error.*UpdateMechanicReports|exception.*ServiceUpdated|exception.*UpdateMechanicReports" > /dev/null
  if [ $? -eq 0 ]; then
    echo -e "   ${RED}✗${NC} Ditemukan error terkait ServiceUpdated atau UpdateMechanicReports dalam log"
    echo -e "   ${YELLOW}!${NC} Cek detail error dengan: tail -n 100 storage/logs/laravel.log | grep -i -E \"error.*ServiceUpdated|error.*UpdateMechanicReports|exception.*ServiceUpdated|exception.*UpdateMechanicReports\""
  else
    echo -e "   ${GREEN}✓${NC} Tidak ditemukan error terkait ServiceUpdated atau UpdateMechanicReports dalam log"
  fi
else
  echo -e "   ${YELLOW}!${NC} File log Laravel tidak ditemukan di lokasi standar"
fi

# 5. Ringkasan
echo -e "\n${YELLOW}5. Ringkasan:${NC}"
echo -e "   - Pastikan semua file kunci ada dan memiliki konten yang benar"
echo -e "   - Pastikan ServiceObserver sudah dihapus"
echo -e "   - Pastikan event ServiceUpdated terdaftar dengan benar"
echo -e "   - Pastikan tidak ada error terkait di log Laravel"
echo -e "\n   Jika semua pemeriksaan di atas menunjukkan hasil positif, implementasi baru seharusnya sudah terpasang dengan benar."
echo -e "   Untuk memastikan lebih lanjut, lakukan pengujian manual dengan membuat servis baru, menandainya sebagai selesai, dan memeriksa rekap montir."

echo -e "\n${YELLOW}Verifikasi selesai.${NC}"
