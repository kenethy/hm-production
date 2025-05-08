#!/bin/bash
# register-events.sh - Script untuk mendaftarkan event di Laravel

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Mendaftarkan event di Laravel...${NC}\n"

# 1. Clear cache
echo -e "${YELLOW}1. Membersihkan cache...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan view:clear
echo -e "   ${GREEN}✓${NC} Cache dibersihkan"

# 2. Optimize
echo -e "\n${YELLOW}2. Mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Aplikasi dioptimalkan"

# 3. Restart queue worker
echo -e "\n${YELLOW}3. Me-restart queue worker...${NC}"
docker-compose exec -T app php artisan queue:restart
echo -e "   ${GREEN}✓${NC} Queue worker di-restart"

# 4. Verifikasi event terdaftar
echo -e "\n${YELLOW}4. Memverifikasi event terdaftar...${NC}"
docker-compose exec -T app php artisan event:list | grep -i "ServiceUpdated"
if [ $? -eq 0 ]; then
  echo -e "   ${GREEN}✓${NC} Event ServiceUpdated terdaftar dengan benar"
else
  echo -e "   ${RED}✗${NC} Event ServiceUpdated TIDAK terdaftar"
  echo -e "   ${YELLOW}!${NC} Mencoba mendaftarkan event secara manual..."
  
  # Coba mendaftarkan event secara manual
  docker-compose exec -T app php artisan event:generate
  docker-compose exec -T app php artisan event:cache
  
  # Verifikasi lagi
  docker-compose exec -T app php artisan event:list | grep -i "ServiceUpdated"
  if [ $? -eq 0 ]; then
    echo -e "   ${GREEN}✓${NC} Event ServiceUpdated berhasil didaftarkan secara manual"
  else
    echo -e "   ${RED}✗${NC} Gagal mendaftarkan event ServiceUpdated"
    echo -e "   ${YELLOW}!${NC} Coba restart container aplikasi..."
    
    # Restart container aplikasi
    docker-compose restart app
    
    # Tunggu beberapa detik
    sleep 5
    
    # Verifikasi lagi
    docker-compose exec -T app php artisan event:list | grep -i "ServiceUpdated"
    if [ $? -eq 0 ]; then
      echo -e "   ${GREEN}✓${NC} Event ServiceUpdated berhasil didaftarkan setelah restart container"
    else
      echo -e "   ${RED}✗${NC} Gagal mendaftarkan event ServiceUpdated"
      echo -e "   ${YELLOW}!${NC} Coba periksa file EventServiceProvider.php dan pastikan event terdaftar dengan benar"
    fi
  fi
fi

echo -e "\n${GREEN}Proses pendaftaran event selesai!${NC}"
