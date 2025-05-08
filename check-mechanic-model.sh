#!/bin/bash
set -e

echo "Memeriksa model Mechanic.php di server..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Periksa apakah file Mechanic.php ada
echo "Memeriksa keberadaan file Mechanic.php..."
if ! docker exec $CONTAINER_NAME ls app/Models/Mechanic.php > /dev/null 2>&1; then
    echo "Error: File app/Models/Mechanic.php tidak ditemukan!"
    exit 1
fi

# Tampilkan isi file Mechanic.php
echo "Menampilkan isi file Mechanic.php..."
docker exec $CONTAINER_NAME cat app/Models/Mechanic.php

# Periksa apakah metode generateWeeklyReport ada
echo -e "\nMemeriksa metode generateWeeklyReport..."
if docker exec $CONTAINER_NAME grep -q "function generateWeeklyReport" app/Models/Mechanic.php; then
    echo "Metode generateWeeklyReport ditemukan dalam file."
else
    echo "Error: Metode generateWeeklyReport TIDAK ditemukan dalam file!"
fi

echo "Selesai memeriksa model Mechanic.php."
