#!/bin/bash
# update-app-with-staff-restrictions.sh - Script untuk memperbarui aplikasi dengan pembatasan akses staff

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dengan pembatasan akses staff...${NC}\n"

# 1. Pull perubahan terbaru dari Git
echo -e "${YELLOW}1. Mengambil perubahan terbaru dari Git...${NC}"
git pull
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal mengambil perubahan terbaru dari Git"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Perubahan terbaru berhasil diambil"

# 2. Clear cache dan optimize
echo -e "\n${YELLOW}2. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 3. Restart container aplikasi
echo -e "\n${YELLOW}3. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

echo -e "\n${GREEN}Pembaruan aplikasi dengan pembatasan akses staff selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Perbaikan yang telah dilakukan:"
echo -e "   - Membuat ServicePolicy untuk mengatur akses ke ServiceResource"
echo -e "   - Memperbaiki metode isAdmin dan isStaff di model User"
echo -e "   - Membatasi akses staff ke bagian montir di form servis"
echo -e "   - Membatasi akses staff ke status 'completed' di form servis"
echo -e "   - Menyembunyikan tombol 'Selesai' dan 'Tandai Selesai' untuk staff"
echo -e "2. Untuk menguji perbaikan:"
echo -e "   - Login sebagai staff (hartonomotor1979@user.com)"
echo -e "   - Buka halaman edit servis"
echo -e "   - Pastikan bagian montir tidak muncul"
echo -e "   - Pastikan opsi status 'Selesai' tidak muncul"
echo -e "   - Pastikan tombol 'Selesai' dan 'Tandai Selesai' tidak muncul"
echo -e "3. Untuk menguji sebagai admin:"
echo -e "   - Login sebagai admin (admin@hartonomotor.com atau hartonomotor@gmail.com)"
echo -e "   - Buka halaman edit servis"
echo -e "   - Pastikan semua fitur tersedia"
