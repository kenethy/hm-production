#!/bin/bash
# remove-all-backup-files.sh - Script untuk menghapus semua file cadangan

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai penghapusan semua file cadangan...${NC}\n"

# 1. Temukan semua file cadangan
echo -e "${YELLOW}1. Menemukan semua file cadangan...${NC}"
BACKUP_FILES=$(find app -type f -name "*.php*" | grep -v ".php$")
if [ -z "$BACKUP_FILES" ]; then
  echo -e "   ${GREEN}✓${NC} Tidak ada file cadangan"
  exit 0
else
  echo -e "   ${YELLOW}!${NC} Ditemukan file cadangan:"
  echo "$BACKUP_FILES"
  echo -e "   ${YELLOW}!${NC} Akan menghapus semua file cadangan tersebut..."
fi

# 2. Hapus semua file cadangan
echo -e "\n${YELLOW}2. Menghapus semua file cadangan...${NC}"
for file in $BACKUP_FILES; do
  echo -e "   ${YELLOW}Menghapus${NC} $file"
  rm "$file"
  if [ $? -ne 0 ]; then
    echo -e "   ${RED}✗${NC} Gagal menghapus $file"
  else
    echo -e "   ${GREEN}✓${NC} Berhasil menghapus $file"
  fi
done

# 3. Verifikasi penghapusan
echo -e "\n${YELLOW}3. Memverifikasi penghapusan...${NC}"
REMAINING_FILES=$(find app -type f -name "*.php*" | grep -v ".php$")
if [ -z "$REMAINING_FILES" ]; then
  echo -e "   ${GREEN}✓${NC} Semua file cadangan berhasil dihapus"
else
  echo -e "   ${RED}✗${NC} Masih ada file cadangan yang tersisa:"
  echo "$REMAINING_FILES"
  echo -e "   ${YELLOW}!${NC} Anda mungkin perlu menghapus file-file tersebut secara manual"
fi

echo -e "\n${GREEN}Penghapusan semua file cadangan selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Semua file cadangan telah dihapus"
echo -e "2. Sekarang Anda perlu membersihkan cache dan me-restart aplikasi"
echo -e "3. Jalankan perintah berikut untuk membersihkan cache dan me-restart aplikasi:"
echo -e "   ${YELLOW}php artisan cache:clear${NC}"
echo -e "   ${YELLOW}php artisan config:clear${NC}"
echo -e "   ${YELLOW}php artisan view:clear${NC}"
echo -e "   ${YELLOW}php artisan route:clear${NC}"
echo -e "   ${YELLOW}php artisan optimize${NC}"
echo -e "   ${YELLOW}docker-compose restart app${NC}"
