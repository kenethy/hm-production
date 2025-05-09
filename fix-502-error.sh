#!/bin/bash

# Script untuk memperbaiki error 502 Bad Gateway

echo "====================================================="
echo "      PERBAIKAN ERROR 502 BAD GATEWAY                "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Memeriksa dan memperbaiki izin file"
echo "2. Memeriksa dan memperbaiki konfigurasi PHP-FPM"
echo "3. Memeriksa dan memperbaiki konfigurasi web server"
echo "4. Me-restart layanan yang diperlukan"
echo ""

# Deteksi apakah berjalan di Docker
IN_DOCKER=false
if [ -f /.dockerenv ] || grep -q docker /proc/1/cgroup 2>/dev/null; then
    IN_DOCKER=true
    echo "Terdeteksi berjalan di dalam container Docker."
    APP_ROOT="/var/www/html"
else
    echo "Terdeteksi berjalan di lingkungan host biasa."
    APP_ROOT="."
fi

echo "Menggunakan path root aplikasi: $APP_ROOT"
echo ""

# 1. Memeriksa dan memperbaiki izin file
echo ">> Memeriksa dan memperbaiki izin file..."
chmod -R 755 $APP_ROOT/public
chmod -R 755 $APP_ROOT/bootstrap
chmod -R 777 $APP_ROOT/storage
chmod -R 777 $APP_ROOT/bootstrap/cache

# Pastikan www-data memiliki akses ke file-file yang diperlukan
if [ "$IN_DOCKER" = false ] && command -v id >/dev/null 2>&1 && id -u www-data >/dev/null 2>&1; then
    chown -R www-data:www-data $APP_ROOT/storage
    chown -R www-data:www-data $APP_ROOT/bootstrap/cache
    echo "Izin file diperbaiki dan kepemilikan diubah ke www-data."
else
    echo "Izin file diperbaiki."
fi
echo ""

# 2. Memeriksa dan memperbaiki konfigurasi PHP-FPM
echo ">> Memeriksa dan memperbaiki konfigurasi PHP-FPM..."

# Cek apakah PHP-FPM berjalan
if [ "$IN_DOCKER" = false ]; then
    if command -v systemctl >/dev/null 2>&1; then
        PHP_FPM_STATUS=$(systemctl is-active php*-fpm 2>/dev/null || echo "inactive")
        if [ "$PHP_FPM_STATUS" = "active" ]; then
            echo "PHP-FPM berjalan."
        else
            echo "PHP-FPM tidak berjalan. Mencoba me-restart..."
            systemctl restart php*-fpm
            echo "PHP-FPM di-restart."
        fi
    elif command -v service >/dev/null 2>&1; then
        service php*-fpm restart
        echo "PHP-FPM di-restart menggunakan service."
    else
        echo "Tidak dapat mendeteksi cara me-restart PHP-FPM."
    fi
else
    echo "Berjalan di Docker, melewati pemeriksaan PHP-FPM."
fi
echo ""

# 3. Memeriksa dan memperbaiki konfigurasi web server
echo ">> Memeriksa dan memperbaiki konfigurasi web server..."

# Cek apakah Nginx atau Apache berjalan
if [ "$IN_DOCKER" = false ]; then
    if command -v systemctl >/dev/null 2>&1; then
        # Cek Nginx
        NGINX_STATUS=$(systemctl is-active nginx 2>/dev/null || echo "inactive")
        if [ "$NGINX_STATUS" = "active" ]; then
            echo "Nginx berjalan. Mencoba me-restart..."
            systemctl restart nginx
            echo "Nginx di-restart."
        fi
        
        # Cek Apache
        APACHE_STATUS=$(systemctl is-active apache2 2>/dev/null || echo "inactive")
        if [ "$APACHE_STATUS" = "active" ]; then
            echo "Apache berjalan. Mencoba me-restart..."
            systemctl restart apache2
            echo "Apache di-restart."
        fi
    elif command -v service >/dev/null 2>&1; then
        # Coba restart Nginx
        service nginx restart 2>/dev/null && echo "Nginx di-restart menggunakan service."
        
        # Coba restart Apache
        service apache2 restart 2>/dev/null && echo "Apache di-restart menggunakan service."
    else
        echo "Tidak dapat mendeteksi cara me-restart web server."
    fi
else
    echo "Berjalan di Docker, melewati pemeriksaan web server."
fi
echo ""

# 4. Memperbaiki file .htaccess jika ada
echo ">> Memeriksa dan memperbaiki file .htaccess..."
if [ -f "$APP_ROOT/public/.htaccess.bak" ] && [ ! -f "$APP_ROOT/public/.htaccess" ]; then
    cp "$APP_ROOT/public/.htaccess.bak" "$APP_ROOT/public/.htaccess"
    echo "File .htaccess dipulihkan dari backup."
elif [ ! -f "$APP_ROOT/public/.htaccess" ]; then
    # Buat file .htaccess default
    cat > "$APP_ROOT/public/.htaccess" << 'EOD'
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
    echo "File .htaccess default dibuat."
fi
echo ""

# 5. Memperbaiki file index.php jika ada masalah
echo ">> Memeriksa dan memperbaiki file index.php..."
if [ -f "$APP_ROOT/public/index.php.bak" ] && [ ! -f "$APP_ROOT/public/index.php" ]; then
    cp "$APP_ROOT/public/index.php.bak" "$APP_ROOT/public/index.php"
    echo "File index.php dipulihkan dari backup."
elif [ ! -f "$APP_ROOT/public/index.php" ]; then
    # Buat file index.php default untuk Laravel 12
    cat > "$APP_ROOT/public/index.php" << 'EOD'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
EOD
    echo "File index.php default dibuat."
fi
echo ""

# 6. Bersihkan cache Laravel
echo ">> Membersihkan cache Laravel..."
php $APP_ROOT/artisan config:clear
php $APP_ROOT/artisan cache:clear
php $APP_ROOT/artisan route:clear
php $APP_ROOT/artisan view:clear
php $APP_ROOT/artisan optimize:clear
echo "Cache Laravel dibersihkan."
echo ""

# 7. Periksa log untuk error
echo ">> Memeriksa log untuk error..."
if [ -f "$APP_ROOT/storage/logs/laravel.log" ]; then
    LAST_ERRORS=$(tail -n 50 "$APP_ROOT/storage/logs/laravel.log" | grep -i "error\|exception\|fatal" | tail -n 5)
    if [ -n "$LAST_ERRORS" ]; then
        echo "Ditemukan error terakhir di log:"
        echo "$LAST_ERRORS"
    else
        echo "Tidak ditemukan error terbaru di log."
    fi
else
    echo "File log Laravel tidak ditemukan."
fi
echo ""

# 8. Me-restart container Docker jika berjalan di Docker
if [ "$IN_DOCKER" = true ]; then
    echo ">> Me-restart container Docker..."
    echo "Karena script ini berjalan di dalam container, Anda perlu me-restart container dari host."
    echo "Jalankan perintah berikut di host:"
    echo "docker restart NAMA_CONTAINER"
    echo ""
else
    # 9. Me-restart seluruh server jika diperlukan
    echo ">> Apakah Anda ingin me-restart seluruh server? (y/n): "
    read -r restart_server
    if [[ "$restart_server" == "y" || "$restart_server" == "Y" ]]; then
        if command -v systemctl >/dev/null 2>&1; then
            systemctl restart php*-fpm
            systemctl restart nginx || systemctl restart apache2
            echo "Server di-restart."
        elif command -v service >/dev/null 2>&1; then
            service php*-fpm restart
            service nginx restart || service apache2 restart
            echo "Server di-restart menggunakan service."
        else
            echo "Tidak dapat mendeteksi cara me-restart server."
        fi
    else
        echo "Server tidak di-restart."
    fi
fi

echo ""
echo "====================================================="
echo "      PERBAIKAN SELESAI                              "
echo "====================================================="
echo ""
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log error web server (Nginx/Apache)"
echo "2. Log error PHP-FPM"
echo "3. Pastikan port dan socket PHP-FPM dikonfigurasi dengan benar"
echo "====================================================="
