#!/bin/bash
# cleanup-backup-files.sh - Script untuk memeriksa dan menghapus file cadangan secara berkala

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pemeriksaan dan penghapusan file cadangan...${NC}\n"

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
  echo -e "   ${YELLOW}!${NC} Mencoba menghapus file-file tersebut dengan perintah lain..."
  
  for file in $REMAINING_FILES; do
    echo -e "   ${YELLOW}Menghapus${NC} $file dengan perintah lain"
    rm -f "$file"
    if [ $? -ne 0 ]; then
      echo -e "   ${RED}✗${NC} Gagal menghapus $file"
    else
      echo -e "   ${GREEN}✓${NC} Berhasil menghapus $file"
    fi
  done
fi

# 4. Verifikasi penghapusan lagi
echo -e "\n${YELLOW}4. Memverifikasi penghapusan lagi...${NC}"
REMAINING_FILES=$(find app -type f -name "*.php*" | grep -v ".php$")
if [ -z "$REMAINING_FILES" ]; then
  echo -e "   ${GREEN}✓${NC} Semua file cadangan berhasil dihapus"
else
  echo -e "   ${RED}✗${NC} Masih ada file cadangan yang tersisa:"
  echo "$REMAINING_FILES"
  echo -e "   ${YELLOW}!${NC} Anda mungkin perlu menghapus file-file tersebut secara manual"
fi

echo -e "\n${GREEN}Pemeriksaan dan penghapusan file cadangan selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Jalankan script ini secara berkala untuk mencegah masalah dengan file cadangan"
echo -e "2. Anda juga dapat menambahkan script ini ke cron job untuk menjalankannya secara otomatis"
echo -e "3. Contoh cron job untuk menjalankan script ini setiap hari pada pukul 00:00:"
echo -e "   ${YELLOW}0 0 * * * /path/to/cleanup-backup-files.sh >> /path/to/cleanup-backup-files.log 2>&1${NC}"
