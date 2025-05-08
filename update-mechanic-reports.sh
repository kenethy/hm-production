#!/bin/bash
# update-mechanic-reports.sh - Script untuk memperbarui rekap montir

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan rekap montir...${NC}\n"

# Jalankan script PHP
docker-compose exec -T app php update-mechanic-reports.php

echo -e "\n${GREEN}Pembaruan rekap montir selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Periksa log untuk memastikan tidak ada error: docker-compose exec app cat storage/logs/laravel.log | tail -n 100"
echo -e "2. Periksa rekap montir di database: docker-compose exec app php artisan tinker --execute=\"DB::table('mechanic_reports')->get()\""
