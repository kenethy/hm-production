#!/bin/bash
# fix-duplicate-method.sh - Script untuk memperbaiki masalah duplicate method

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai perbaikan masalah duplicate method...${NC}\n"

# 1. Hapus file cadangan yang menyebabkan konflik
echo -e "${YELLOW}1. Menghapus file cadangan yang menyebabkan konflik...${NC}"
if [ -f app/Filament/Resources/ServiceResource/Pages/EditService.php.debug ]; then
  rm app/Filament/Resources/ServiceResource/Pages/EditService.php.debug
  echo -e "   ${GREEN}✓${NC} File cadangan berhasil dihapus"
else
  echo -e "   ${GREEN}✓${NC} File cadangan sudah tidak ada"
fi

# 2. Periksa apakah ada file cadangan lain
echo -e "\n${YELLOW}2. Memeriksa apakah ada file cadangan lain...${NC}"
BACKUP_FILES=$(find app -type f -name "*.php*" | grep -v ".php$")
if [ -z "$BACKUP_FILES" ]; then
  echo -e "   ${GREEN}✓${NC} Tidak ada file cadangan lain"
else
  echo -e "   ${YELLOW}!${NC} Ditemukan file cadangan lain:"
  echo "$BACKUP_FILES"
  echo -e "   ${YELLOW}!${NC} Anda mungkin perlu menghapus file-file tersebut secara manual"
fi

# 3. Clear cache dan optimize
echo -e "\n${YELLOW}3. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 4. Restart container aplikasi
echo -e "\n${YELLOW}4. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

echo -e "\n${GREEN}Perbaikan masalah duplicate method selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Masalah duplicate method disebabkan oleh adanya file cadangan yang juga mendefinisikan metode afterSave()"
echo -e "2. File cadangan tersebut telah dihapus dan aplikasi telah di-restart"
echo -e "3. Jika masih ada masalah, periksa apakah ada file cadangan lain yang perlu dihapus"
