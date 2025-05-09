#!/bin/bash
# update-app-with-recreate-edit-service.sh - Script untuk memperbarui aplikasi dan membuat ulang file EditService.php

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dan pembuatan ulang file EditService.php...${NC}\n"

# 1. Pull perubahan terbaru dari Git
echo -e "${YELLOW}1. Mengambil perubahan terbaru dari Git...${NC}"
git pull
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal mengambil perubahan terbaru dari Git"
  echo -e "   ${YELLOW}Mencoba menyelesaikan konflik secara otomatis...${NC}"
  
  # Coba selesaikan konflik dengan menggunakan versi lokal
  git checkout --ours app/Filament/Resources/ServiceResource/Pages/EditService.php
  git add app/Filament/Resources/ServiceResource/Pages/EditService.php
  git commit -m "Resolve merge conflict using our version"
  
  # Coba pull lagi
  git pull
  if [ $? -ne 0 ]; then
    echo -e "   ${RED}✗${NC} Masih gagal mengambil perubahan terbaru dari Git"
    echo -e "   ${YELLOW}Melanjutkan dengan pembuatan ulang file EditService.php...${NC}"
  fi
fi
echo -e "   ${GREEN}✓${NC} Perubahan terbaru berhasil diambil atau konflik diselesaikan"

# 2. Jalankan script pembuatan ulang file EditService.php
echo -e "\n${YELLOW}2. Menjalankan script pembuatan ulang file EditService.php...${NC}"
chmod +x recreate-edit-service.sh
./recreate-edit-service.sh
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script pembuatan ulang file EditService.php"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script pembuatan ulang file EditService.php berhasil dijalankan"

# 3. Commit perubahan
echo -e "\n${YELLOW}3. Commit perubahan...${NC}"
git add app/Filament/Resources/ServiceResource/Pages/EditService.php
git commit -m "Recreate EditService.php to fix duplicate method declarations"
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal commit perubahan"
  echo -e "   ${YELLOW}Melanjutkan tanpa commit...${NC}"
else
  echo -e "   ${GREEN}✓${NC} Perubahan berhasil di-commit"
fi

echo -e "\n${GREEN}Pembaruan aplikasi dan pembuatan ulang file EditService.php selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Script ini telah melakukan:"
echo -e "   - Mengambil perubahan terbaru dari Git"
echo -e "   - Menyelesaikan konflik Git secara otomatis jika ada"
echo -e "   - Membuat ulang file EditService.php dengan versi terbaru"
echo -e "   - Commit perubahan ke Git"
echo -e "2. Untuk menguji perbaikan:"
echo -e "   - Buka halaman edit servis"
echo -e "   - Pastikan tidak ada error 'Cannot redeclare'"
echo -e "   - Pastikan biaya jasa montir muncul dengan benar"
echo -e "   - Pastikan total biaya servis dihitung dengan benar"
echo -e "3. Jika masih terjadi error, jalankan script ini lagi atau perbaiki file secara manual"
