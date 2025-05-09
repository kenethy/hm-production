#!/bin/bash
# update-app-with-fix-mount.sh - Script untuk memperbarui aplikasi dan memperbaiki metode mount

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dan perbaikan metode mount...${NC}\n"

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
    echo -e "   ${YELLOW}Melanjutkan dengan perbaikan metode mount...${NC}"
  fi
fi
echo -e "   ${GREEN}✓${NC} Perubahan terbaru berhasil diambil atau konflik diselesaikan"

# 2. Jalankan script perbaikan metode mount
echo -e "\n${YELLOW}2. Menjalankan script perbaikan metode mount...${NC}"
chmod +x fix-edit-service-mount.sh
./fix-edit-service-mount.sh
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script perbaikan metode mount"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script perbaikan metode mount berhasil dijalankan"

# 3. Commit perubahan
echo -e "\n${YELLOW}3. Commit perubahan...${NC}"
git add app/Filament/Resources/ServiceResource/Pages/EditService.php
git commit -m "Fix 'Attempt to read property id on string' error in EditService.php"
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal commit perubahan"
  echo -e "   ${YELLOW}Melanjutkan tanpa commit...${NC}"
else
  echo -e "   ${GREEN}✓${NC} Perubahan berhasil di-commit"
fi

echo -e "\n${GREEN}Pembaruan aplikasi dan perbaikan metode mount selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Script ini telah melakukan:"
echo -e "   - Mengambil perubahan terbaru dari Git"
echo -e "   - Menyelesaikan konflik Git secara otomatis jika ada"
echo -e "   - Memperbaiki metode mount di EditService.php"
echo -e "   - Memperbaiki metode fillMechanicCosts di EditService.php"
echo -e "   - Commit perubahan ke Git"
echo -e "2. Untuk menguji perbaikan:"
echo -e "   - Buka halaman edit servis"
echo -e "   - Pastikan tidak ada error 'Attempt to read property \"id\" on string'"
echo -e "3. Jika masih terjadi error, jalankan script ini lagi atau perbaiki file secara manual"
