#!/bin/bash
# update-app-with-fix-duplicate-methods.sh - Script untuk memperbarui aplikasi dan memperbaiki masalah duplikasi metode

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Memulai pembaruan aplikasi dan perbaikan masalah duplikasi metode...${NC}\n"

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
    echo -e "   ${YELLOW}Silakan selesaikan konflik secara manual${NC}"
    exit 1
  fi
fi
echo -e "   ${GREEN}✓${NC} Perubahan terbaru berhasil diambil"

# 2. Jalankan script perbaikan duplikasi metode
echo -e "\n${YELLOW}2. Menjalankan script perbaikan duplikasi metode...${NC}"
chmod +x fix-duplicate-methods.sh
./fix-duplicate-methods.sh
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script perbaikan duplikasi metode"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script perbaikan duplikasi metode berhasil dijalankan"

# 3. Jalankan script pembatasan akses staff
echo -e "\n${YELLOW}3. Menjalankan script pembatasan akses staff...${NC}"
chmod +x update-app-with-staff-restrictions.sh
./update-app-with-staff-restrictions.sh
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal menjalankan script pembatasan akses staff"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Script pembatasan akses staff berhasil dijalankan"

echo -e "\n${GREEN}Pembaruan aplikasi dan perbaikan masalah duplikasi metode selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Script ini telah melakukan:"
echo -e "   - Mengambil perubahan terbaru dari Git"
echo -e "   - Menyelesaikan konflik Git secara otomatis jika ada"
echo -e "   - Memperbaiki masalah duplikasi metode di file PHP"
echo -e "   - Menerapkan pembatasan akses staff"
echo -e "2. Jika masih terjadi error, jalankan script ini lagi atau perbaiki file secara manual"
echo -e "3. Untuk menguji perbaikan:"
echo -e "   - Login sebagai staff (hartonomotor1979@user.com)"
echo -e "   - Buka halaman edit servis"
echo -e "   - Pastikan bagian montir tidak muncul"
echo -e "   - Pastikan opsi status 'Selesai' tidak muncul"
echo -e "   - Pastikan tombol 'Selesai' dan 'Tandai Selesai' tidak muncul"
