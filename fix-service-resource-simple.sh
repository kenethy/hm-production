#!/bin/bash
set -e

echo "Memperbaiki ServiceResource.php di server dengan pendekatan sederhana..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Buat backup file asli
echo "Membuat backup file ServiceResource.php..."
docker exec $CONTAINER_NAME cp app/Filament/Resources/ServiceResource.php app/Filament/Resources/ServiceResource.php.bak.$(date +%Y%m%d%H%M%S)

# Buat file PHP sementara di server untuk menambahkan try-catch
echo "Membuat file PHP sementara untuk perbaikan..."
docker exec $CONTAINER_NAME bash -c 'cat > /tmp/fix_service_resource.php << "EOF"
<?php

// Baca file ServiceResource.php
$file = file_get_contents("app/Filament/Resources/ServiceResource.php");

// Cari semua panggilan ke generateWeeklyReport
$pattern = "/(\s+)(\\\$mechanic->generateWeeklyReport\(\\\$weekStart, \\\$weekEnd\);)/";
$replacement = "$1try {\n$1    $2\n$1} catch (\\Exception \$e) {\n$1    \\Illuminate\\Support\\Facades\\Log::error(\"Error generating weekly report: \" . \$e->getMessage(), [\n$1        \"mechanic_id\" => \$mechanic->id,\n$1        \"week_start\" => \$weekStart,\n$1        \"week_end\" => \$weekEnd\n$1    ]);\n$1}";

// Ganti semua panggilan dengan versi yang dibungkus try-catch
$newFile = preg_replace($pattern, $replacement, $file);

// Simpan file yang sudah dimodifikasi
file_put_contents("app/Filament/Resources/ServiceResource.php", $newFile);

echo "File ServiceResource.php berhasil diperbarui.";
EOF'

# Jalankan script PHP
echo "Menjalankan script perbaikan..."
docker exec $CONTAINER_NAME php /tmp/fix_service_resource.php

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! ServiceResource.php telah diperbarui."
