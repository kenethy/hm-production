#!/bin/bash
# rollback.sh - Script untuk rollback ke versi sebelumnya

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai rollback ke versi sebelumnya...${NC}\n"

# Cek apakah commit hash disediakan
if [ -z "$1" ]; then
  echo -e "${RED}Error: Commit hash tidak disediakan${NC}"
  echo -e "Penggunaan: ./rollback.sh <commit_hash>"
  echo -e "Contoh: ./rollback.sh abc1234"
  
  # Tampilkan 5 commit terakhir untuk membantu pengguna
  echo -e "\n${YELLOW}5 commit terakhir:${NC}"
  git log -5 --oneline
  exit 1
fi

COMMIT_HASH=$1

# 1. Verifikasi commit hash
echo -e "${YELLOW}1. Memverifikasi commit hash...${NC}"
git cat-file -e $COMMIT_HASH
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Commit hash tidak valid"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Commit hash valid"

# 2. Reset ke commit yang ditentukan
echo -e "\n${YELLOW}2. Melakukan reset ke commit $COMMIT_HASH...${NC}"
git reset --hard $COMMIT_HASH
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal melakukan reset ke commit $COMMIT_HASH"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Berhasil melakukan reset ke commit $COMMIT_HASH"

# 3. Install dependencies dengan Composer
echo -e "\n${YELLOW}3. Memperbarui dependencies Composer...${NC}"
docker-compose exec -T app composer install --no-interaction --optimize-autoloader --no-dev
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal memperbarui dependencies Composer"
else
  echo -e "   ${GREEN}✓${NC} Dependencies Composer berhasil diperbarui"
fi

# 4. Jalankan migrasi database
echo -e "\n${YELLOW}4. Menjalankan migrasi database...${NC}"
docker-compose exec -T app php artisan migrate --force
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan migrasi database"
else
  echo -e "   ${GREEN}✓${NC} Migrasi database berhasil dijalankan"
fi

# 5. Clear cache dan optimize
echo -e "\n${YELLOW}5. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 6. Restart container aplikasi
echo -e "\n${YELLOW}6. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
else
  echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"
fi

echo -e "\n${GREEN}Rollback selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log untuk memastikan tidak ada error: docker-compose logs -f app"
echo -e "2. Lakukan pengujian manual untuk memastikan aplikasi berfungsi dengan benar"
echo -e "3. Jika masih ada masalah, Anda mungkin perlu memulihkan database dari backup"
