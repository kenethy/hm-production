#!/bin/bash
set -e

echo "Memperbaiki laporan montir..."

# Cek apakah docker-compose berjalan
if ! docker ps | grep -q "app\|laravel\|php"; then
    echo "Error: Container Docker tidak terdeteksi. Pastikan container Docker berjalan."
    echo "Coba jalankan 'docker ps' untuk melihat container yang aktif."
    exit 1
fi

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear

# Jalankan perintah untuk memperbaiki laporan montir
echo "Menjalankan perintah fix-mechanic-reports..."
docker exec $CONTAINER_NAME php artisan app:fix-mechanic-reports

echo "Selesai! Laporan montir telah diperbaiki."
