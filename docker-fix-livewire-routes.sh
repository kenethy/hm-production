#!/bin/bash

# Script untuk memperbaiki masalah route Livewire di lingkungan Docker

echo "====================================================="
echo "  PERBAIKAN MASALAH ROUTE LIVEWIRE (DOCKER)          "
echo "====================================================="
echo ""

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
echo ""

# 1. Memperbaiki file .htaccess
echo ">> Memperbaiki file .htaccess..."
docker exec $WEB_CONTAINER bash -c "
if [ -f /var/www/html/public/.htaccess ]; then
    echo 'File .htaccess ditemukan.'
    
    # Periksa apakah ada aturan untuk menangani POST request
    if ! grep -q 'RewriteCond %{REQUEST_METHOD} POST' /var/www/html/public/.htaccess; then
        echo 'Menambahkan aturan untuk menangani POST request ke .htaccess...'
        
        # Buat backup
        cp /var/www/html/public/.htaccess /var/www/html/public/.htaccess.bak
        
        # Tambahkan aturan untuk menangani POST request
        sed -i 's/RewriteEngine On/RewriteEngine On\\n\\n    # Handle POST requests properly\\n    RewriteCond %{REQUEST_METHOD} POST\\n    RewriteRule ^ - [L]/g' /var/www/html/public/.htaccess
        
        echo 'File .htaccess berhasil diperbarui.'
    else
        echo 'Aturan untuk menangani POST request sudah ada di .htaccess.'
    fi
else
    echo 'File .htaccess tidak ditemukan. Ini adalah masalah!'
fi
"
echo ""

# 2. Mempublikasikan ulang aset Livewire
echo ">> Mempublikasikan ulang aset Livewire..."
docker exec $WEB_CONTAINER php /var/www/html/artisan vendor:publish --force --tag=livewire:assets
echo "Aset Livewire berhasil dipublikasikan ulang."
echo ""

# 3. Memperbaiki route cache
echo ">> Memperbaiki route cache..."
docker exec $WEB_CONTAINER php /var/www/html/artisan route:clear
echo "Cache route berhasil dibersihkan."
echo ""

# 4. Memperbaiki konfigurasi Livewire
echo ">> Memperbaiki konfigurasi Livewire..."
docker exec $WEB_CONTAINER bash -c "
if [ ! -f /var/www/html/config/livewire.php ]; then
    echo 'File konfigurasi Livewire tidak ditemukan. Membuat file konfigurasi...'
    php /var/www/html/artisan vendor:publish --tag=livewire:config
    echo 'File konfigurasi Livewire berhasil dibuat.'
else
    echo 'File konfigurasi Livewire ditemukan.'
fi
"
echo ""

# 5. Membersihkan cache aplikasi
echo ">> Membersihkan cache aplikasi..."
docker exec $WEB_CONTAINER php /var/www/html/artisan optimize:clear
echo "Cache aplikasi berhasil dibersihkan."
echo ""

# 6. Memperbaiki file index.php
echo ">> Memperbaiki file index.php..."
docker exec $WEB_CONTAINER bash -c "
if [ -f /var/www/html/public/index.php ]; then
    echo 'File index.php ditemukan.'
    
    # Buat backup
    cp /var/www/html/public/index.php /var/www/html/public/index.php.bak
    
    # Buat konten baru
    cat > /var/www/html/public/index.php << 'EOD'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists(\$maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require \$maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application \$app */
\$app = require_once __DIR__.'/../bootstrap/app.php';

\$app->handleRequest(Request::capture());
EOD
    
    echo 'File index.php berhasil diperbarui.'
else
    echo 'File index.php tidak ditemukan. Ini adalah masalah serius!'
fi
"
echo ""

# 7. Memperbaiki izin file
echo ">> Memperbaiki izin file..."
docker exec $WEB_CONTAINER bash -c "
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/public
"
echo "Izin file berhasil diperbaiki."
echo ""

# 8. Me-restart container
echo ">> Me-restart container..."
docker restart $WEB_CONTAINER
echo "Container $WEB_CONTAINER berhasil di-restart."
echo ""

echo "====================================================="
echo "     PERBAIKAN SELESAI                               "
echo "====================================================="
echo ""
echo "Silakan coba lagi mengakses aplikasi Anda."
echo "Jika masalah masih berlanjut, periksa log dengan perintah:"
echo "docker exec $WEB_CONTAINER cat /var/www/html/storage/logs/laravel.log"
echo "====================================================="
