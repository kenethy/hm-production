#!/bin/bash
set -e

echo "Memperbaiki masalah tanda kutip di Mechanic.php dengan pendekatan langsung..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Buat backup file asli
echo "Membuat backup file Mechanic.php..."
docker exec $CONTAINER_NAME cp app/Models/Mechanic.php app/Models/Mechanic.php.bak.direct.$(date +%Y%m%d%H%M%S)

# Perbaiki masalah tanda kutip langsung dengan sed
echo "Memperbaiki masalah tanda kutip dengan sed..."
docker exec $CONTAINER_NAME sed -i "s/week_start => \\\$weekStart/'week_start' => \\\$weekStart/g" app/Models/Mechanic.php
docker exec $CONTAINER_NAME sed -i "s/week_end => \\\$weekEnd/'week_end' => \\\$weekEnd/g" app/Models/Mechanic.php

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! Masalah tanda kutip di Mechanic.php telah diperbaiki."
