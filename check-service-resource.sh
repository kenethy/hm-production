#!/bin/bash
set -e

echo "Memeriksa ServiceResource.php di server..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Periksa apakah file ServiceResource.php ada
echo "Memeriksa keberadaan file ServiceResource.php..."
if ! docker exec $CONTAINER_NAME ls app/Filament/Resources/ServiceResource.php > /dev/null 2>&1; then
    echo "Error: File app/Filament/Resources/ServiceResource.php tidak ditemukan!"
    exit 1
fi

# Cari semua baris yang memanggil generateWeeklyReport
echo "Mencari panggilan ke generateWeeklyReport dalam ServiceResource.php..."
docker exec $CONTAINER_NAME grep -n "generateWeeklyReport" app/Filament/Resources/ServiceResource.php

echo "Selesai memeriksa ServiceResource.php."
