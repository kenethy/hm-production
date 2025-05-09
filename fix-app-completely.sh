#!/bin/bash
# fix-app-completely.sh - Script untuk memperbaiki aplikasi secara menyeluruh

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai perbaikan aplikasi secara menyeluruh...${NC}\n"

# 1. Hapus semua file cadangan
echo -e "${YELLOW}1. Menghapus semua file cadangan...${NC}"
BACKUP_FILES=$(find app -type f -name "*.php*" | grep -v ".php$")
if [ -z "$BACKUP_FILES" ]; then
  echo -e "   ${GREEN}✓${NC} Tidak ada file cadangan"
else
  echo -e "   ${YELLOW}!${NC} Ditemukan file cadangan:"
  echo "$BACKUP_FILES"
  echo -e "   ${YELLOW}!${NC} Menghapus semua file cadangan tersebut..."
  
  for file in $BACKUP_FILES; do
    echo -e "   ${YELLOW}Menghapus${NC} $file"
    rm "$file"
    if [ $? -ne 0 ]; then
      echo -e "   ${RED}✗${NC} Gagal menghapus $file"
    else
      echo -e "   ${GREEN}✓${NC} Berhasil menghapus $file"
    fi
  done
fi

# 2. Verifikasi penghapusan
echo -e "\n${YELLOW}2. Memverifikasi penghapusan...${NC}"
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

# 3. Hentikan semua container
echo -e "\n${YELLOW}3. Menghentikan semua container...${NC}"
docker-compose down
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menghentikan semua container"
  echo -e "   ${YELLOW}!${NC} Melanjutkan proses..."
else
  echo -e "   ${GREEN}✓${NC} Semua container berhasil dihentikan"
fi

# 4. Bersihkan cache composer
echo -e "\n${YELLOW}4. Membersihkan cache composer...${NC}"
composer clear-cache
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal membersihkan cache composer"
  echo -e "   ${YELLOW}!${NC} Melanjutkan proses..."
else
  echo -e "   ${GREEN}✓${NC} Cache composer berhasil dibersihkan"
fi

# 5. Dump autoload
echo -e "\n${YELLOW}5. Melakukan dump autoload...${NC}"
composer dump-autoload
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal melakukan dump autoload"
  echo -e "   ${YELLOW}!${NC} Melanjutkan proses..."
else
  echo -e "   ${GREEN}✓${NC} Dump autoload berhasil dilakukan"
fi

# 6. Jalankan kembali semua container
echo -e "\n${YELLOW}6. Menjalankan kembali semua container...${NC}"
docker-compose up -d
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan kembali semua container"
  exit 1
else
  echo -e "   ${GREEN}✓${NC} Semua container berhasil dijalankan kembali"
fi

# 7. Tunggu beberapa detik
echo -e "\n${YELLOW}7. Menunggu beberapa detik...${NC}"
sleep 10
echo -e "   ${GREEN}✓${NC} Selesai menunggu"

# 8. Clear cache dan optimize
echo -e "\n${YELLOW}8. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 9. Periksa status container
echo -e "\n${YELLOW}9. Memeriksa status container...${NC}"
docker-compose ps
echo -e "   ${GREEN}✓${NC} Status container berhasil diperiksa"

echo -e "\n${GREEN}Perbaikan aplikasi secara menyeluruh selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Semua file cadangan telah dihapus"
echo -e "2. Semua container telah di-restart"
echo -e "3. Semua cache telah dibersihkan"
echo -e "4. Autoload telah di-dump ulang"
echo -e "5. Aplikasi telah dioptimalkan"
echo -e "6. Jika masih ada masalah, coba periksa log aplikasi:"
echo -e "   ${YELLOW}docker-compose logs app${NC}"
