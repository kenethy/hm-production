#!/bin/bash
# update-app-with-total-cost-fix.sh - Script untuk memperbarui aplikasi dengan perbaikan total biaya

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dengan perbaikan total biaya...${NC}\n"

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

echo -e "\n${GREEN}Pembaruan aplikasi dengan perbaikan total biaya selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Perbaikan yang telah dilakukan:"
echo -e "   - Memperbaiki perhitungan total biaya di ServiceResource.php"
echo -e "   - Memperbaiki perhitungan total biaya di EditService.php"
echo -e "   - Menambahkan log untuk memantau nilai total biaya"
echo -e "2. Untuk menguji perbaikan:"
echo -e "   - Buat servis baru dengan montir"
echo -e "   - Isi biaya jasa montir dengan nilai tertentu (misalnya 13)"
echo -e "   - Periksa apakah total biaya servis menampilkan nilai yang sama (13)"
echo -e "   - Ubah status menjadi selesai"
echo -e "   - Periksa rekap montir untuk memastikan biaya jasa montir dipertahankan (13, bukan 50.000)"
echo -e "3. Periksa log untuk memastikan tidak ada error: docker-compose exec app cat storage/logs/laravel.log | grep -i \"total_cost\" | tail -n 100"
