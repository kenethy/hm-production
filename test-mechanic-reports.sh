#!/bin/bash
# test-mechanic-reports.sh - Script untuk menguji fitur laporan montir

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Panduan Pengujian Fitur Laporan Montir${NC}\n"

echo -e "${YELLOW}1. Pengujian Servis Baru:${NC}"
echo -e "   a. Buat servis baru dengan status 'in_progress'"
echo -e "   b. Tambahkan montir ke servis"
echo -e "   c. Tandai servis sebagai 'completed'"
echo -e "   d. Periksa rekap montir untuk memastikan biaya jasa terupdate dengan benar"
echo -e "   e. Periksa log Laravel untuk memastikan tidak ada error:"
echo -e "      ${GREEN}docker-compose exec app cat storage/logs/laravel.log | grep -i \"UpdateMechanicReports\"${NC}"

echo -e "\n${YELLOW}2. Pengujian Edit Servis:${NC}"
echo -e "   a. Edit servis yang sudah ada dengan status 'completed'"
echo -e "   b. Ubah montirnya (tambah montir baru atau ganti montir yang ada)"
echo -e "   c. Periksa rekap montir untuk memastikan biaya jasa terupdate dengan benar"
echo -e "   d. Periksa log Laravel untuk memastikan tidak ada error:"
echo -e "      ${GREEN}docker-compose exec app cat storage/logs/laravel.log | grep -i \"UpdateMechanicReports\"${NC}"

echo -e "\n${YELLOW}3. Pengujian Pembatalan Servis:${NC}"
echo -e "   a. Edit servis dengan status 'completed'"
echo -e "   b. Ubah statusnya menjadi 'cancelled'"
echo -e "   c. Periksa rekap montir untuk memastikan biaya jasa dihapus dari rekap"
echo -e "   d. Periksa log Laravel untuk memastikan tidak ada error:"
echo -e "      ${GREEN}docker-compose exec app cat storage/logs/laravel.log | grep -i \"UpdateMechanicReports\"${NC}"

echo -e "\n${YELLOW}4. Periksa Log Event:${NC}"
echo -e "   Jalankan perintah berikut untuk melihat log terkait event ServiceUpdated:"
echo -e "   ${GREEN}docker-compose exec app cat storage/logs/laravel.log | grep -i \"ServiceUpdated\"${NC}"

echo -e "\n${YELLOW}5. Periksa Rekap Montir di Database:${NC}"
echo -e "   Jalankan perintah berikut untuk melihat data rekap montir di database:"
echo -e "   ${GREEN}docker-compose exec app php artisan tinker --execute=\"DB::table('mechanic_reports')->get()\"${NC}"

echo -e "\n${YELLOW}6. Periksa Relasi Montir-Servis di Database:${NC}"
echo -e "   Jalankan perintah berikut untuk melihat data relasi montir-servis di database:"
echo -e "   ${GREEN}docker-compose exec app php artisan tinker --execute=\"DB::table('mechanic_service')->get()\"${NC}"

echo -e "\n${YELLOW}Jika semua pengujian di atas berhasil, fitur laporan montir sudah berfungsi dengan benar.${NC}"
echo -e "${YELLOW}Jika ada masalah, periksa log Laravel untuk informasi lebih lanjut:${NC}"
echo -e "${GREEN}docker-compose exec app cat storage/logs/laravel.log | tail -n 100${NC}"
