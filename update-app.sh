#!/bin/bash
# update-app.sh - Script untuk memperbarui aplikasi yang sudah berjalan

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi...${NC}\n"

# 1. Pull perubahan terbaru
echo -e "${YELLOW}1. Mengambil perubahan terbaru dari repository...${NC}"
git pull
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal mengambil perubahan terbaru"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Perubahan terbaru berhasil diambil"

# 2. Install dependencies dengan Composer (jika ada perubahan)
echo -e "\n${YELLOW}2. Memperbarui dependencies Composer...${NC}"
docker-compose exec -T app composer install --no-interaction --optimize-autoloader --no-dev
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal memperbarui dependencies Composer"
else
  echo -e "   ${GREEN}✓${NC} Dependencies Composer berhasil diperbarui"
fi

# 3. Jalankan migrasi database
echo -e "\n${YELLOW}3. Menjalankan migrasi database...${NC}"
docker-compose exec -T app php artisan migrate --force
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan migrasi database"
else
  echo -e "   ${GREEN}✓${NC} Migrasi database berhasil dijalankan"
fi

# 4. Clear cache dan optimize
echo -e "\n${YELLOW}4. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 5. Restart container aplikasi
echo -e "\n${YELLOW}5. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
else
  echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"
fi

# 6. Verifikasi implementasi
echo -e "\n${YELLOW}6. Memverifikasi implementasi...${NC}"
# Salin script verify-implementation.sh ke container
docker cp verify-implementation.sh $(docker-compose ps -q app):/var/www/html/
# Jalankan script verifikasi di dalam container
docker-compose exec -T app bash -c "chmod +x verify-implementation.sh && ./verify-implementation.sh"

echo -e "\n${GREEN}Pembaruan aplikasi selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log untuk memastikan tidak ada error: docker-compose logs -f app"
echo -e "2. Lakukan pengujian manual untuk memastikan fitur berfungsi dengan benar"
echo -e "3. Jika terjadi masalah, Anda dapat melihat log Laravel: docker-compose exec app cat storage/logs/laravel.log | tail -n 100"
