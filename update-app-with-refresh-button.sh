#!/bin/bash
# update-app-with-refresh-button.sh - Script untuk memperbarui aplikasi dengan tombol refresh rekap montir

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dengan tombol refresh rekap montir...${NC}\n"

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

echo -e "\n${GREEN}Pembaruan aplikasi dengan tombol refresh rekap montir selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Tombol refresh rekap montir telah ditambahkan di beberapa tempat:"
echo -e "   - Di halaman daftar rekap montir (tombol 'Refresh Rekap Montir' di bagian atas)"
echo -e "   - Di halaman edit rekap montir (tombol 'Refresh Rekap' di bagian atas)"
echo -e "   - Di setiap baris rekap montir (tombol 'Refresh' di kolom aksi)"
echo -e "2. Tombol refresh akan menjalankan command 'mechanic:sync-reports' untuk memperbarui rekap montir berdasarkan data servis terbaru"
echo -e "3. Untuk menguji tombol refresh, buat servis baru, tambahkan montir, dan tandai sebagai selesai"
echo -e "4. Kemudian klik tombol refresh di halaman rekap montir untuk memperbarui data"
