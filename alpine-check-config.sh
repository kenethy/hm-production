#!/bin/sh

# Script untuk memeriksa konfigurasi di container Alpine Linux
# Script ini menggunakan /bin/sh yang tersedia di hampir semua image Docker

echo "====================================================="
echo "  PEMERIKSAAN KONFIGURASI (ALPINE DOCKER)            "
echo "====================================================="
echo ""

# Mendapatkan nama container web
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

# 1. Memeriksa image Docker yang digunakan
echo ">> Memeriksa image Docker yang digunakan..."
IMAGE_INFO=$(docker inspect --format='{{.Config.Image}}' $WEB_CONTAINER)
echo "Image: $IMAGE_INFO"

# Periksa apakah shell tersedia
echo "Memeriksa shell yang tersedia..."
SHELL_TYPE=$(docker exec $WEB_CONTAINER sh -c "if [ -f /bin/bash ]; then echo 'bash'; elif [ -f /bin/ash ]; then echo 'ash'; else echo 'sh'; fi")
echo "Shell tersedia: $SHELL_TYPE"
echo ""

# 2. Memeriksa versi Nginx
echo ">> Memeriksa versi Nginx..."
NGINX_VERSION=$(docker exec $WEB_CONTAINER sh -c "nginx -v 2>&1 | grep -o '[0-9]\.[0-9]*\.[0-9]*'" || echo "Nginx tidak ditemukan")
echo "Versi Nginx: $NGINX_VERSION"
echo ""

# 3. Memeriksa versi PHP
echo ">> Memeriksa versi PHP..."
PHP_VERSION=$(docker exec $WEB_CONTAINER sh -c "php -v 2>&1 | grep -o 'PHP [0-9]\.[0-9]*\.[0-9]*'" || echo "PHP tidak ditemukan")
echo "Versi PHP: $PHP_VERSION"
echo ""

# 4. Memeriksa konfigurasi Nginx
echo ">> Memeriksa konfigurasi Nginx..."
NGINX_CONF_LOCATIONS="/etc/nginx/conf.d/default.conf /etc/nginx/sites-available/default /etc/nginx/nginx.conf"

for NGINX_CONF in $NGINX_CONF_LOCATIONS; do
    if docker exec $WEB_CONTAINER sh -c "[ -f $NGINX_CONF ]"; then
        echo "Konfigurasi Nginx ditemukan di: $NGINX_CONF"
        echo "Isi file konfigurasi Nginx:"
        docker exec $WEB_CONTAINER sh -c "cat $NGINX_CONF"
        
        # Periksa konfigurasi fastcgi_pass
        FASTCGI_PASS=$(docker exec $WEB_CONTAINER sh -c "grep -o 'fastcgi_pass [^;]*' $NGINX_CONF" || echo "fastcgi_pass tidak ditemukan")
        echo "Konfigurasi fastcgi_pass: $FASTCGI_PASS"
        break
    fi
done

if [ -z "$NGINX_CONF" ]; then
    echo "Tidak dapat menemukan file konfigurasi Nginx."
fi
echo ""

# 5. Memeriksa konfigurasi PHP-FPM
echo ">> Memeriksa konfigurasi PHP-FPM..."
PHP_FPM_CONF_LOCATIONS="/etc/php/*/fpm/pool.d/www.conf /etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf"

for PHP_FPM_CONF in $PHP_FPM_CONF_LOCATIONS; do
    if docker exec $WEB_CONTAINER sh -c "[ -f $PHP_FPM_CONF ]"; then
        echo "Konfigurasi PHP-FPM ditemukan di: $PHP_FPM_CONF"
        
        # Periksa konfigurasi listen
        LISTEN_CONFIG=$(docker exec $WEB_CONTAINER sh -c "grep -o 'listen = [^;]*' $PHP_FPM_CONF" || echo "listen tidak ditemukan")
        echo "Konfigurasi listen: $LISTEN_CONFIG"
        break
    fi
done

if [ -z "$PHP_FPM_CONF" ]; then
    echo "Tidak dapat menemukan file konfigurasi PHP-FPM."
fi
echo ""

# 6. Memeriksa proses yang berjalan
echo ">> Memeriksa proses yang berjalan..."
echo "Proses Nginx:"
docker exec $WEB_CONTAINER sh -c "ps | grep nginx | grep -v grep" || echo "Tidak ada proses Nginx yang berjalan"

echo "Proses PHP-FPM:"
docker exec $WEB_CONTAINER sh -c "ps | grep php-fpm | grep -v grep" || echo "Tidak ada proses PHP-FPM yang berjalan"
echo ""

# 7. Memeriksa socket atau port yang digunakan
echo ">> Memeriksa socket atau port yang digunakan..."
echo "Socket PHP-FPM:"
docker exec $WEB_CONTAINER sh -c "find / -name '*.sock' | grep php" || echo "Tidak ada socket PHP-FPM yang ditemukan"

echo "Port yang digunakan:"
docker exec $WEB_CONTAINER sh -c "netstat -tulpn 2>/dev/null | grep -E ':(80|9000)'" || echo "Command netstat tidak tersedia atau tidak ada port yang terbuka"
echo ""

# 8. Memeriksa log error
echo ">> Memeriksa log error..."
echo "Log error Nginx:"
docker exec $WEB_CONTAINER sh -c "if [ -f /var/log/nginx/error.log ]; then tail -n 20 /var/log/nginx/error.log; else echo 'Log error Nginx tidak ditemukan'; fi"

echo "Log error PHP-FPM:"
docker exec $WEB_CONTAINER sh -c "find /var/log -name '*php*' -type f | xargs cat 2>/dev/null | tail -n 20" || echo "Tidak ada log PHP-FPM yang ditemukan"

echo "Log Laravel:"
docker exec $WEB_CONTAINER sh -c "if [ -f /var/www/html/storage/logs/laravel.log ]; then tail -n 20 /var/www/html/storage/logs/laravel.log; else echo 'Log Laravel tidak ditemukan'; fi"
echo ""

# 9. Memeriksa izin file
echo ">> Memeriksa izin file..."
echo "Izin direktori public:"
docker exec $WEB_CONTAINER sh -c "ls -la /var/www/html/public" || echo "Direktori public tidak ditemukan"

echo "Izin direktori storage:"
docker exec $WEB_CONTAINER sh -c "ls -la /var/www/html/storage" || echo "Direktori storage tidak ditemukan"

echo "Izin direktori bootstrap/cache:"
docker exec $WEB_CONTAINER sh -c "ls -la /var/www/html/bootstrap/cache" || echo "Direktori bootstrap/cache tidak ditemukan"
echo ""

echo "====================================================="
echo "      PEMERIKSAAN SELESAI                            "
echo "====================================================="
echo ""
echo "Berdasarkan hasil pemeriksaan di atas, Anda dapat menentukan masalah yang terjadi."
echo "Jika Anda melihat masalah dengan konfigurasi Nginx atau PHP-FPM,"
echo "jalankan script alpine-docker-fix.sh untuk memperbaikinya."
echo "====================================================="
