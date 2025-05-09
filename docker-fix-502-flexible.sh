#!/bin/bash

# Script fleksibel untuk memperbaiki error 502 Bad Gateway di lingkungan Docker

echo "====================================================="
echo "  PERBAIKAN ERROR 502 BAD GATEWAY (DOCKER)           "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Memeriksa dan memperbaiki izin file"
echo "2. Memeriksa dan memperbaiki file konfigurasi"
echo "3. Mendeteksi dan me-restart layanan yang benar"
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

# 2. Memeriksa dan memperbaiki file .htaccess dan index.php
echo ">> Memeriksa dan memperbaiki file konfigurasi..."
docker exec $WEB_CONTAINER bash -c "
# Periksa dan perbaiki .htaccess
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

# Periksa dan perbaiki index.php
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
echo "File konfigurasi diperiksa dan diperbaiki."
echo ""

# 3. Bersihkan cache Laravel
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

# 4. Deteksi dan me-restart layanan yang benar
echo ">> Mendeteksi dan me-restart layanan..."
docker exec $WEB_CONTAINER bash -c "
# Deteksi web server
echo 'Mendeteksi web server...'
if ps aux | grep -v grep | grep -q nginx; then
    echo 'Nginx terdeteksi berjalan.'
    WEBSERVER='nginx'
elif ps aux | grep -v grep | grep -q apache; then
    echo 'Apache terdeteksi berjalan.'
    if [ -d '/etc/apache2' ]; then
        WEBSERVER='apache2'
    else
        WEBSERVER='httpd'
    fi
else
    echo 'Tidak dapat mendeteksi web server yang berjalan.'
    WEBSERVER='unknown'
fi

# Deteksi PHP-FPM
echo 'Mendeteksi PHP-FPM...'
PHP_VERSION=\$(php -r \"echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;\")
echo \"Versi PHP terdeteksi: \$PHP_VERSION\"

# Me-restart PHP-FPM
echo 'Me-restart PHP-FPM...'
if command -v service >/dev/null 2>&1; then
    service php\$PHP_VERSION-fpm restart 2>/dev/null || service php-fpm restart 2>/dev/null || echo 'Tidak dapat me-restart PHP-FPM dengan service.'
else
    # Coba cari PID PHP-FPM dan kirim sinyal reload
    PHP_FPM_PID=\$(pgrep -f \"php-fpm: master\")
    if [ -n \"\$PHP_FPM_PID\" ]; then
        echo \"Mengirim sinyal reload ke PHP-FPM (PID: \$PHP_FPM_PID)...\"
        kill -USR2 \$PHP_FPM_PID
        echo \"Sinyal reload dikirim ke PHP-FPM.\"
    else
        echo \"Tidak dapat menemukan PID PHP-FPM.\"
    fi
fi

# Me-restart web server
echo \"Me-restart web server (\$WEBSERVER)...\"
if [ \"\$WEBSERVER\" = \"nginx\" ]; then
    if command -v service >/dev/null 2>&1; then
        service nginx restart 2>/dev/null || echo 'Tidak dapat me-restart Nginx dengan service.'
    else
        # Coba cari PID Nginx dan kirim sinyal reload
        NGINX_PID=\$(pgrep -f \"nginx: master\")
        if [ -n \"\$NGINX_PID\" ]; then
            echo \"Mengirim sinyal reload ke Nginx (PID: \$NGINX_PID)...\"
            kill -HUP \$NGINX_PID
            echo \"Sinyal reload dikirim ke Nginx.\"
        else
            echo \"Tidak dapat menemukan PID Nginx.\"
        fi
    fi
elif [ \"\$WEBSERVER\" = \"apache2\" ] || [ \"\$WEBSERVER\" = \"httpd\" ]; then
    if command -v service >/dev/null 2>&1; then
        service \$WEBSERVER restart 2>/dev/null || echo 'Tidak dapat me-restart Apache dengan service.'
    else
        # Coba cari PID Apache dan kirim sinyal reload
        APACHE_PID=\$(pgrep -f \"apache2: master\" || pgrep -f \"httpd: master\")
        if [ -n \"\$APACHE_PID\" ]; then
            echo \"Mengirim sinyal reload ke Apache (PID: \$APACHE_PID)...\"
            kill -HUP \$APACHE_PID
            echo \"Sinyal reload dikirim ke Apache.\"
        else
            echo \"Tidak dapat menemukan PID Apache.\"
        fi
    fi
else
    echo \"Mencoba me-restart Nginx dan Apache...\"
    if command -v service >/dev/null 2>&1; then
        service nginx restart 2>/dev/null
        service apache2 restart 2>/dev/null || service httpd restart 2>/dev/null
    fi
fi
"
echo "Layanan dideteksi dan di-restart."
echo ""

# 5. Periksa log untuk error
echo ">> Memeriksa log untuk error..."
docker exec $WEB_CONTAINER bash -c "
if [ -f /var/www/html/storage/logs/laravel.log ]; then
    echo 'Error terakhir di log Laravel:'
    tail -n 50 /var/www/html/storage/logs/laravel.log | grep -i 'error\|exception\|fatal' | tail -n 5
else
    echo 'File log Laravel tidak ditemukan.'
fi

# Cek log web server
if [ -f /var/log/nginx/error.log ]; then
    echo 'Error terakhir di log Nginx:'
    tail -n 20 /var/log/nginx/error.log
elif [ -f /var/log/apache2/error.log ]; then
    echo 'Error terakhir di log Apache:'
    tail -n 20 /var/log/apache2/error.log
elif [ -f /var/log/httpd/error_log ]; then
    echo 'Error terakhir di log Apache:'
    tail -n 20 /var/log/httpd/error_log
else
    echo 'Log web server tidak ditemukan.'
fi
"
echo ""

# 6. Me-restart container
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
