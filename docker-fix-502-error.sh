#!/bin/bash

# Script untuk memperbaiki error 502 Bad Gateway di lingkungan Docker

echo "====================================================="
echo "  PERBAIKAN ERROR 502 BAD GATEWAY (DOCKER)           "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Memeriksa dan memperbaiki izin file"
echo "2. Memeriksa dan memperbaiki konfigurasi PHP-FPM"
echo "3. Memeriksa dan memperbaiki konfigurasi web server"
echo "4. Me-restart container Docker"
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

# 1. Memeriksa dan memperbaiki izin file
echo ">> Memeriksa dan memperbaiki izin file..."
docker exec $WEB_CONTAINER bash -c "
chmod -R 755 /var/www/html/public
chmod -R 755 /var/www/html/bootstrap
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache
"
echo "Izin file diperbaiki."
echo ""

# 2. Memeriksa dan memperbaiki file .htaccess
echo ">> Memeriksa dan memperbaiki file .htaccess..."
docker exec $WEB_CONTAINER bash -c "
if [ -f /var/www/html/public/.htaccess.bak ] && [ ! -f /var/www/html/public/.htaccess ]; then
    cp /var/www/html/public/.htaccess.bak /var/www/html/public/.htaccess
    echo 'File .htaccess dipulihkan dari backup.'
elif [ ! -f /var/www/html/public/.htaccess ]; then
    # Buat file .htaccess default
    cat > /var/www/html/public/.htaccess << 'EOD'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOD
    echo 'File .htaccess default dibuat.'
fi
"
echo ""

# 3. Memeriksa dan memperbaiki file index.php
echo ">> Memeriksa dan memperbaiki file index.php..."
docker exec $WEB_CONTAINER bash -c "
if [ -f /var/www/html/public/index.php.bak ] && [ ! -f /var/www/html/public/index.php ]; then
    cp /var/www/html/public/index.php.bak /var/www/html/public/index.php
    echo 'File index.php dipulihkan dari backup.'
elif [ ! -f /var/www/html/public/index.php ]; then
    # Buat file index.php default untuk Laravel 12
    cat > /var/www/html/public/index.php << 'EOD'
<?php

use Illuminate\\Foundation\\Application;
use Illuminate\\Http\\Request;

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
    echo 'File index.php default dibuat.'
fi
"
echo ""

# 4. Bersihkan cache Laravel
echo ">> Membersihkan cache Laravel..."
docker exec $WEB_CONTAINER bash -c "
php /var/www/html/artisan config:clear
php /var/www/html/artisan cache:clear
php /var/www/html/artisan route:clear
php /var/www/html/artisan view:clear
php /var/www/html/artisan optimize:clear
"
echo "Cache Laravel dibersihkan."
echo ""

# 5. Periksa log untuk error
echo ">> Memeriksa log untuk error..."
docker exec $WEB_CONTAINER bash -c "
if [ -f /var/www/html/storage/logs/laravel.log ]; then
    tail -n 50 /var/www/html/storage/logs/laravel.log | grep -i 'error\|exception\|fatal' | tail -n 5
else
    echo 'File log Laravel tidak ditemukan.'
fi
"
echo ""

# 6. Periksa status layanan di dalam container
echo ">> Memeriksa status layanan di dalam container..."
docker exec $WEB_CONTAINER bash -c "
# Cek PHP-FPM
if command -v service >/dev/null 2>&1; then
    echo 'Status PHP-FPM:'
    service php*-fpm status || echo 'PHP-FPM tidak ditemukan atau tidak berjalan'
    
    echo 'Status Nginx:'
    service nginx status || echo 'Nginx tidak ditemukan atau tidak berjalan'
    
    echo 'Status Apache:'
    service apache2 status || echo 'Apache tidak ditemukan atau tidak berjalan'
else
    echo 'Command service tidak tersedia di container.'
    
    echo 'Proses PHP-FPM:'
    ps aux | grep php-fpm | grep -v grep || echo 'Tidak ada proses PHP-FPM yang berjalan'
    
    echo 'Proses Nginx:'
    ps aux | grep nginx | grep -v grep || echo 'Tidak ada proses Nginx yang berjalan'
    
    echo 'Proses Apache:'
    ps aux | grep apache | grep -v grep || echo 'Tidak ada proses Apache yang berjalan'
fi
"
echo ""

# 7. Me-restart layanan di dalam container
echo ">> Me-restart layanan di dalam container..."
docker exec $WEB_CONTAINER bash -c "
if command -v service >/dev/null 2>&1; then
    service php*-fpm restart || echo 'Tidak dapat me-restart PHP-FPM'
    service nginx restart || service apache2 restart || echo 'Tidak dapat me-restart web server'
    echo 'Layanan di-restart.'
else
    echo 'Command service tidak tersedia di container.'
fi
"
echo ""

# 8. Me-restart container
echo ">> Me-restart container..."
read -p "Apakah Anda ingin me-restart container $WEB_CONTAINER? (y/n): " restart_container
if [[ "$restart_container" == "y" || "$restart_container" == "Y" ]]; then
    docker restart $WEB_CONTAINER
    echo "Container $WEB_CONTAINER di-restart."
    
    # Tunggu container siap
    echo "Menunggu container siap..."
    sleep 10
    
    # Periksa status container
    CONTAINER_STATUS=$(docker inspect --format='{{.State.Status}}' $WEB_CONTAINER)
    echo "Status container: $CONTAINER_STATUS"
else
    echo "Container tidak di-restart."
fi
echo ""

echo "====================================================="
echo "      PERBAIKAN SELESAI                              "
echo "====================================================="
echo ""
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log Docker dengan perintah: docker logs $WEB_CONTAINER"
echo "2. Log error web server di dalam container"
echo "3. Log error PHP-FPM di dalam container"
echo "4. Pastikan port dan socket PHP-FPM dikonfigurasi dengan benar"
echo "====================================================="
