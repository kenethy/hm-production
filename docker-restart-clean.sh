#!/bin/bash

# Script untuk membersihkan dan me-restart aplikasi dalam lingkungan Docker
# Gunakan dengan hati-hati karena akan membersihkan semua cache dan me-restart container

echo "====================================================="
echo "  SCRIPT PEMBERSIHAN DAN RESTART APLIKASI (DOCKER)   "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Membersihkan semua cache aplikasi"
echo "2. Mengatur ulang izin file"
echo "3. Membuat ulang symbolic link storage"
echo "4. Membersihkan file sementara"
echo "5. Me-restart container Docker"
echo ""
echo "PERINGATAN: Pastikan tidak ada proses penting yang sedang berjalan!"
echo ""
read -p "Lanjutkan? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operasi dibatalkan."
    exit 1
fi

echo ""
echo "====================================================="
echo "Memulai proses pembersihan dan restart..."
echo "====================================================="

# Mendapatkan nama container web (biasanya berisi php/nginx/apache)
WEB_CONTAINER=$(docker ps --format '{{.Names}}' | grep -E 'web|app|php|nginx|apache' | head -1)

if [ -z "$WEB_CONTAINER" ]; then
    echo "Tidak dapat menemukan container web. Silakan masukkan nama container secara manual:"
    read -p "Nama container: " WEB_CONTAINER
    
    if [ -z "$WEB_CONTAINER" ]; then
        echo "Nama container tidak valid. Keluar."
        exit 1
    fi
fi

echo "Menggunakan container: $WEB_CONTAINER"

# 1. Membersihkan semua cache aplikasi
echo ""
echo ">> Membersihkan cache aplikasi..."
docker exec $WEB_CONTAINER php artisan cache:clear
docker exec $WEB_CONTAINER php artisan config:clear
docker exec $WEB_CONTAINER php artisan route:clear
docker exec $WEB_CONTAINER php artisan view:clear
docker exec $WEB_CONTAINER php artisan optimize:clear
docker exec $WEB_CONTAINER php artisan event:clear
docker exec $WEB_CONTAINER php artisan queue:restart

# 2. Membersihkan file sesi
echo ""
echo ">> Membersihkan file sesi..."
docker exec $WEB_CONTAINER rm -rf /var/www/html/storage/framework/sessions/*
echo "File sesi dibersihkan."

# 3. Membersihkan file log (opsional)
echo ""
echo ">> Membersihkan file log..."
docker exec $WEB_CONTAINER bash -c "echo '' > /var/www/html/storage/logs/laravel.log"
echo "File log dibersihkan."

# 4. Membersihkan file sementara Livewire
echo ""
echo ">> Membersihkan file sementara Livewire..."
docker exec $WEB_CONTAINER rm -rf /var/www/html/storage/app/livewire-tmp/*
docker exec $WEB_CONTAINER mkdir -p /var/www/html/storage/app/livewire-tmp
echo "File sementara Livewire dibersihkan."

# 5. Mengatur ulang izin file
echo ""
echo ">> Mengatur ulang izin file..."
docker exec $WEB_CONTAINER chmod -R 755 /var/www/html/public
docker exec $WEB_CONTAINER chmod -R 777 /var/www/html/storage
docker exec $WEB_CONTAINER chmod -R 777 /var/www/html/bootstrap/cache

# Membuat direktori yang diperlukan
echo ""
echo ">> Membuat direktori yang diperlukan..."
docker exec $WEB_CONTAINER mkdir -p /var/www/html/storage/app/public/gallery
docker exec $WEB_CONTAINER chmod -R 777 /var/www/html/storage/app/public/gallery

# 6. Membuat ulang symbolic link storage
echo ""
echo ">> Membuat ulang symbolic link storage..."
docker exec $WEB_CONTAINER bash -c "if [ -L /var/www/html/public/storage ]; then rm /var/www/html/public/storage; fi"
docker exec $WEB_CONTAINER php artisan storage:link
echo "Symbolic link storage dibuat ulang."

# 7. Memperbaiki konfigurasi .env
echo ""
echo ">> Memperbaiki konfigurasi .env..."
docker exec $WEB_CONTAINER sed -i 's/SESSION_SECURE_COOKIE=false/SESSION_SECURE_COOKIE=true/g' /var/www/html/.env
echo "Konfigurasi .env diperbaiki."

# 8. Mempublikasikan ulang aset Livewire
echo ""
echo ">> Mempublikasikan ulang aset Livewire..."
docker exec $WEB_CONTAINER php artisan vendor:publish --force --tag=livewire:assets
echo "Aset Livewire dipublikasikan ulang."

# 9. Me-restart layanan dalam container
echo ""
echo ">> Me-restart layanan dalam container..."

# Restart PHP-FPM jika ada
docker exec $WEB_CONTAINER bash -c "if command -v service >/dev/null 2>&1; then service php8.2-fpm restart || service php8.1-fpm restart || service php8.0-fpm restart || service php7.4-fpm restart || echo 'Tidak dapat me-restart PHP-FPM'; fi"

# Restart Nginx jika ada
docker exec $WEB_CONTAINER bash -c "if command -v service >/dev/null 2>&1; then service nginx restart || echo 'Tidak dapat me-restart Nginx'; fi"

# Restart Apache jika ada
docker exec $WEB_CONTAINER bash -c "if command -v service >/dev/null 2>&1 && service --status-all | grep -Fq 'apache2'; then service apache2 restart || echo 'Tidak dapat me-restart Apache'; fi"

# 10. Me-restart container Docker
echo ""
echo ">> Me-restart container Docker..."
docker restart $WEB_CONTAINER
echo "Container $WEB_CONTAINER di-restart."

# 11. Menunggu container siap
echo ""
echo ">> Menunggu container siap..."
sleep 10

# 12. Verifikasi status aplikasi
echo ""
echo ">> Verifikasi status aplikasi..."
docker exec $WEB_CONTAINER php artisan about

echo ""
echo "====================================================="
echo "      PEMBERSIHAN DAN RESTART SELESAI                "
echo "====================================================="
echo ""
echo "Aplikasi dalam Docker telah dibersihkan dan di-restart."
echo "Silakan buka browser dan periksa apakah masalah telah teratasi."
echo ""
echo "Jika masih ada masalah, periksa log dengan perintah:"
echo "docker exec $WEB_CONTAINER cat /var/www/html/storage/logs/laravel.log"
echo "====================================================="
