#!/bin/bash
# debug-direct-service.sh - Script untuk men-debug langsung di ServiceResource

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai debugging langsung di ServiceResource...${NC}\n"

# 1. Backup file asli
echo -e "${YELLOW}1. Membuat backup file asli...${NC}"
docker-compose exec -T app cp app/Filament/Resources/ServiceResource/Pages/EditService.php app/Filament/Resources/ServiceResource/Pages/EditService.php.bak
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal membuat backup file asli"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Backup file asli berhasil dibuat"

# 2. Salin file debug ke container
echo -e "\n${YELLOW}2. Menyalin file debug ke container...${NC}"
docker cp app/Filament/Resources/ServiceResource/Pages/EditService.php.debug $(docker-compose ps -q app):/var/www/html/app/Filament/Resources/ServiceResource/Pages/EditService.php
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menyalin file debug ke container"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} File debug berhasil disalin ke container"

# 3. Clear cache dan optimize
echo -e "\n${YELLOW}3. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 4. Restart container aplikasi
echo -e "\n${YELLOW}4. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

echo -e "\n${GREEN}Debugging langsung di ServiceResource selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Buat servis baru dan tandai sebagai selesai untuk menguji apakah rekap montir diperbarui secara otomatis"
echo -e "2. Periksa log setelah membuat servis baru: docker-compose exec app cat storage/logs/laravel.log | grep -i \"DEBUG_EDIT_SERVICE\" | tail -n 100"
echo -e "3. Kembalikan file asli setelah selesai debugging: docker-compose exec app cp app/Filament/Resources/ServiceResource/Pages/EditService.php.bak app/Filament/Resources/ServiceResource/Pages/EditService.php"
