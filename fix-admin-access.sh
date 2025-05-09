#!/bin/bash
# fix-admin-access.sh - Script untuk memperbaiki masalah akses admin

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai perbaikan masalah akses admin...${NC}\n"

# 1. Jalankan migrasi
echo -e "${YELLOW}1. Menjalankan migrasi...${NC}"
docker-compose exec app php artisan migrate
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan migrasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Migrasi berhasil dijalankan"

# 2. Bersihkan cache Laravel
echo -e "\n${YELLOW}2. Membersihkan cache Laravel...${NC}"
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache Laravel berhasil dibersihkan"

# 3. Restart container aplikasi
echo -e "\n${YELLOW}3. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

# 4. Restart container web server
echo -e "\n${YELLOW}4. Me-restart container web server...${NC}"
docker-compose restart webserver
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container web server"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container web server berhasil di-restart"

# 5. Periksa rute admin
echo -e "\n${YELLOW}5. Memeriksa rute admin...${NC}"
docker-compose exec app php artisan route:list | grep -c "admin"
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Rute admin tidak ditemukan"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Rute admin ditemukan"

# 6. Buat user admin jika belum ada
echo -e "\n${YELLOW}6. Membuat user admin jika belum ada...${NC}"
docker-compose exec app php artisan tinker --execute="
if (\\App\\Models\\User::where('email', 'admin@hartonomotor.com')->count() === 0) {
    \\App\\Models\\User::create([
        'name' => 'Admin',
        'email' => 'admin@hartonomotor.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
    echo 'User admin berhasil dibuat.';
} else {
    echo 'User admin sudah ada.';
}
"
echo -e "   ${GREEN}✓${NC} User admin berhasil diperiksa/dibuat"

echo -e "\n${GREEN}Perbaikan masalah akses admin selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Migrasi telah dijalankan untuk membuat tabel yang diperlukan"
echo -e "2. Cache Laravel telah dibersihkan"
echo -e "3. Container aplikasi dan web server telah di-restart"
echo -e "4. User admin telah dibuat/diperiksa"
echo -e "5. Untuk mengakses admin panel:"
echo -e "   - Buka http://localhost/admin di browser"
echo -e "   - Login dengan email: admin@hartonomotor.com dan password: password"
echo -e "   - Jika masih ada masalah, periksa log Laravel dengan perintah:"
echo -e "     docker-compose exec app tail -n 100 storage/logs/laravel.log"
