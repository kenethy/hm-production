#!/bin/bash
# fix-app-after-backup-removal.sh - Script untuk memperbaiki aplikasi setelah penghapusan file cadangan

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai perbaikan aplikasi setelah penghapusan file cadangan...${NC}\n"

# 1. Periksa apakah masih ada file cadangan
echo -e "${YELLOW}1. Memeriksa apakah masih ada file cadangan...${NC}"
BACKUP_FILES=$(find app -type f -name "*.php*" | grep -v ".php$")
if [ -z "$BACKUP_FILES" ]; then
  echo -e "   ${GREEN}✓${NC} Tidak ada file cadangan"
else
  echo -e "   ${RED}✗${NC} Masih ada file cadangan:"
  echo "$BACKUP_FILES"
  echo -e "   ${YELLOW}!${NC} Jalankan script remove-all-backup-files.sh terlebih dahulu"
  exit 1
fi

# 2. Bersihkan cache composer
echo -e "\n${YELLOW}2. Membersihkan cache composer...${NC}"
composer clear-cache
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal membersihkan cache composer"
  echo -e "   ${YELLOW}!${NC} Melanjutkan proses..."
else
  echo -e "   ${GREEN}✓${NC} Cache composer berhasil dibersihkan"
fi

# 3. Dump autoload
echo -e "\n${YELLOW}3. Melakukan dump autoload...${NC}"
composer dump-autoload
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal melakukan dump autoload"
  echo -e "   ${YELLOW}!${NC} Melanjutkan proses..."
else
  echo -e "   ${GREEN}✓${NC} Dump autoload berhasil dilakukan"
fi

# 4. Clear cache dan optimize
echo -e "\n${YELLOW}4. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 5. Restart container aplikasi
echo -e "\n${YELLOW}5. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

# 6. Periksa status container
echo -e "\n${YELLOW}6. Memeriksa status container...${NC}"
docker-compose ps
echo -e "   ${GREEN}✓${NC} Status container berhasil diperiksa"

echo -e "\n${GREEN}Perbaikan aplikasi setelah penghapusan file cadangan selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Semua cache telah dibersihkan"
echo -e "2. Autoload telah di-dump ulang"
echo -e "3. Aplikasi telah dioptimalkan"
echo -e "4. Container aplikasi telah di-restart"
echo -e "5. Jika masih ada masalah, coba restart seluruh stack Docker:"
echo -e "   ${YELLOW}docker-compose down${NC}"
echo -e "   ${YELLOW}docker-compose up -d${NC}"
