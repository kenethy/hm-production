#!/bin/bash
# update-app-with-mechanic-costs-fix-v3.sh - Script untuk memperbarui aplikasi dengan perbaikan tampilan biaya jasa montir (versi 3)

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dengan perbaikan tampilan biaya jasa montir (versi 3)...${NC}\n"

# 1. Pull perubahan terbaru dari Git
echo -e "${YELLOW}1. Mengambil perubahan terbaru dari Git...${NC}"
git pull
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal mengambil perubahan terbaru dari Git"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Perubahan terbaru berhasil diambil"

# 2. Buat direktori jika belum ada
echo -e "\n${YELLOW}2. Membuat direktori untuk file JavaScript kustom...${NC}"
mkdir -p public/js
echo -e "   ${GREEN}✓${NC} Direktori berhasil dibuat"

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

echo -e "\n${GREEN}Pembaruan aplikasi dengan perbaikan tampilan biaya jasa montir (versi 3) selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Perbaikan yang telah dilakukan:"
echo -e "   - Menambahkan metode afterStateHydrated pada komponen Repeater untuk mengisi mechanic_costs secara otomatis"
echo -e "   - Menambahkan tombol Refresh Biaya Jasa di halaman edit servis"
echo -e "   - Menambahkan script JavaScript kustom untuk memicu tampilan biaya jasa montir secara otomatis"
echo -e "2. Untuk menguji perbaikan:"
echo -e "   - Buka halaman edit servis langsung melalui URL (https://hartonomotor.xyz/admin/services/36/edit)"
echo -e "   - Jika biaya jasa per montir tidak muncul otomatis, klik tombol Refresh Biaya Jasa"
echo -e "   - Edit biaya jasa dan simpan perubahan"
echo -e "   - Periksa rekap montir untuk memastikan biaya jasa montir dipertahankan"
