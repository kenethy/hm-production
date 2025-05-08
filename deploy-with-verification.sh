#!/bin/bash
# deploy-with-verification.sh

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai deployment dengan verifikasi...${NC}\n"

# Simpan commit hash saat ini untuk rollback jika diperlukan
CURRENT_COMMIT=$(git rev-parse HEAD)
echo -e "Commit hash saat ini: ${CURRENT_COMMIT} (disimpan untuk rollback jika diperlukan)"

# 1. Backup database (jika menggunakan MySQL/MariaDB)
echo -e "\n${YELLOW}1. Membuat backup database...${NC}"
echo -e "   ${YELLOW}!${NC} Masukkan password database MySQL/MariaDB:"
read -s DB_PASSWORD
echo -e "   ${YELLOW}!${NC} Masukkan nama database:"
read DB_NAME

BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
docker-compose exec -T db mysqldump -u root -p"$DB_PASSWORD" "$DB_NAME" > "$BACKUP_FILE"
if [ $? -eq 0 ]; then
  echo -e "   ${GREEN}✓${NC} Backup database berhasil disimpan ke $BACKUP_FILE"
else
  echo -e "   ${RED}✗${NC} Backup database gagal"
  echo -e "   ${YELLOW}!${NC} Apakah Anda ingin melanjutkan deployment tanpa backup database? (y/n)"
  read -p "   " continue_without_backup
  if [ "$continue_without_backup" != "y" ]; then
    echo -e "   ${YELLOW}!${NC} Deployment dibatalkan"
    exit 1
  fi
  echo -e "   ${YELLOW}!${NC} Melanjutkan deployment tanpa backup database"
fi

# 2. Aktifkan mode maintenance
echo -e "\n${YELLOW}2. Mengaktifkan mode maintenance...${NC}"
docker-compose exec -T app php artisan down
if [ $? -eq 0 ]; then
  echo -e "   ${GREEN}✓${NC} Mode maintenance diaktifkan"
else
  echo -e "   ${RED}✗${NC} Gagal mengaktifkan mode maintenance"
  echo -e "   ${YELLOW}!${NC} Apakah Anda ingin melanjutkan deployment tanpa mode maintenance? (y/n)"
  read -p "   " continue_without_maintenance
  if [ "$continue_without_maintenance" != "y" ]; then
    echo -e "   ${YELLOW}!${NC} Deployment dibatalkan"
    exit 1
  fi
  echo -e "   ${YELLOW}!${NC} Melanjutkan deployment tanpa mode maintenance"
fi

# 3. Pull perubahan terbaru
echo -e "\n${YELLOW}3. Mengambil perubahan terbaru dari repository...${NC}"
git pull
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal mengambil perubahan terbaru"
  echo -e "   ${YELLOW}!${NC} Menonaktifkan mode maintenance dan membatalkan deployment"
  docker-compose exec -T app php artisan up
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Perubahan terbaru berhasil diambil"

# 4. Rebuild dan restart container Docker
echo -e "\n${YELLOW}4. Membangun ulang dan me-restart container Docker...${NC}"
docker-compose down
docker-compose build
docker-compose up -d
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal membangun ulang dan me-restart container Docker"
  echo -e "   ${YELLOW}!${NC} Melakukan rollback ke commit sebelumnya: ${CURRENT_COMMIT}"
  git reset --hard $CURRENT_COMMIT
  docker-compose up -d
  docker-compose exec -T app php artisan up
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container Docker berhasil dibangun ulang dan di-restart"

# 5. Jalankan migrasi database
echo -e "\n${YELLOW}5. Menjalankan migrasi database...${NC}"
docker-compose exec -T app php artisan migrate --force
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan migrasi database"
  echo -e "   ${YELLOW}!${NC} Apakah Anda ingin melakukan rollback? (y/n)"
  read -p "   " do_rollback
  if [ "$do_rollback" = "y" ]; then
    echo -e "   ${YELLOW}!${NC} Melakukan rollback ke commit sebelumnya: ${CURRENT_COMMIT}"
    git reset --hard $CURRENT_COMMIT
    docker-compose up -d
    docker-compose exec -T app php artisan up
    exit 1
  else
    echo -e "   ${YELLOW}!${NC} Melanjutkan tanpa rollback meskipun migrasi gagal"
  fi
else
  echo -e "   ${GREEN}✓${NC} Migrasi database berhasil dijalankan"
fi

# 6. Clear cache dan optimize
echo -e "\n${YELLOW}6. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 7. Verifikasi implementasi
echo -e "\n${YELLOW}7. Memverifikasi implementasi...${NC}"
# Salin script verify-implementation.sh ke container
docker cp verify-implementation.sh $(docker-compose ps -q app):/var/www/html/
# Jalankan script verifikasi di dalam container
docker-compose exec -T app bash -c "chmod +x verify-implementation.sh && ./verify-implementation.sh"
VERIFY_STATUS=$?

if [ $VERIFY_STATUS -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Verifikasi implementasi gagal"
  echo -e "   ${YELLOW}!${NC} Apakah Anda ingin melakukan rollback? (y/n)"
  read -p "   " do_rollback
  if [ "$do_rollback" = "y" ]; then
    echo -e "   ${YELLOW}!${NC} Melakukan rollback ke commit sebelumnya: ${CURRENT_COMMIT}"
    git reset --hard $CURRENT_COMMIT
    docker-compose up -d
    docker-compose exec -T app php artisan cache:clear
    docker-compose exec -T app php artisan config:clear
    docker-compose exec -T app php artisan view:clear
    docker-compose exec -T app php artisan route:clear
    docker-compose exec -T app php artisan optimize
    docker-compose exec -T app php artisan up
    exit 1
  else
    echo -e "   ${YELLOW}!${NC} Melanjutkan tanpa rollback meskipun verifikasi gagal"
  fi
else
  echo -e "   ${GREEN}✓${NC} Verifikasi implementasi berhasil"
fi

# 8. Nonaktifkan mode maintenance
echo -e "\n${YELLOW}8. Menonaktifkan mode maintenance...${NC}"
docker-compose exec -T app php artisan up
if [ $? -eq 0 ]; then
  echo -e "   ${GREEN}✓${NC} Mode maintenance dinonaktifkan"
else
  echo -e "   ${RED}✗${NC} Gagal menonaktifkan mode maintenance"
  echo -e "   ${YELLOW}!${NC} Coba nonaktifkan mode maintenance secara manual: docker-compose exec app php artisan up"
fi

echo -e "\n${GREEN}Deployment selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log untuk memastikan tidak ada error: docker-compose logs -f app"
echo -e "2. Lakukan pengujian manual untuk memastikan fitur berfungsi dengan benar"
echo -e "3. Jika terjadi masalah, Anda dapat melakukan rollback ke commit sebelumnya: git reset --hard ${CURRENT_COMMIT} && docker-compose up -d"
