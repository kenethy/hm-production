#!/bin/bash
set -e

echo "Memperbaiki masalah tanda kutip di Mechanic.php..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Buat backup file asli
echo "Membuat backup file Mechanic.php..."
docker exec $CONTAINER_NAME cp app/Models/Mechanic.php app/Models/Mechanic.php.bak.quotes.$(date +%Y%m%d%H%M%S)

# Buat file PHP sementara untuk memperbaiki masalah tanda kutip
echo "Membuat file PHP sementara untuk perbaikan..."
docker exec $CONTAINER_NAME bash -c 'cat > /tmp/fix_quotes.php << "EOF"
<?php

// Baca file Mechanic.php
$file = file_get_contents("app/Models/Mechanic.php");

// Perbaiki masalah tanda kutip di updateOrCreate
$pattern1 = "/updateOrCreate\\(\\s*\\[\\s*week_start => \\\$weekStart/";
$replacement1 = "updateOrCreate(\n                [\n                    'week_start' => \$weekStart";
$file = preg_replace($pattern1, $replacement1, $file);

$pattern2 = "/week_end => \\\$weekEnd/";
$replacement2 = "'week_end' => \$weekEnd";
$file = preg_replace($pattern2, $replacement2, $file);

// Simpan file yang sudah dimodifikasi
file_put_contents("app/Models/Mechanic.php", $file);

echo "File Mechanic.php berhasil diperbarui dengan tanda kutip yang benar.";
EOF'

# Jalankan script PHP
echo "Menjalankan script perbaikan..."
docker exec $CONTAINER_NAME php /tmp/fix_quotes.php

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! Masalah tanda kutip di Mechanic.php telah diperbaiki."
