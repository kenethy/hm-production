#!/bin/bash

# Script untuk mengatur ulang semua konfigurasi dari nol

echo "====================================================="
echo "      RESET KONFIGURASI DARI NOL                     "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Mengatur ulang konfigurasi Nginx"
echo "2. Mengatur ulang konfigurasi PHP-FPM"
echo "3. Mengatur ulang file-file penting Laravel"
echo "4. Mengatur ulang izin file"
echo "5. Membersihkan cache dan mengoptimalkan aplikasi"
echo "6. Me-restart semua layanan"
echo ""
echo "PERINGATAN: Ini akan menimpa konfigurasi yang ada!"
echo ""
read -p "Lanjutkan? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operasi dibatalkan."
    exit 1
fi

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

# 1. Mengatur ulang konfigurasi Nginx
echo ">> Mengatur ulang konfigurasi Nginx..."

# Cari versi PHP yang digunakan
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "Versi PHP terdeteksi: $PHP_VERSION"

# Buat konfigurasi Nginx baru
if [ "$IN_DOCKER" = true ]; then
    NGINX_CONF_DIR="/etc/nginx"
    NGINX_SITES_DIR="$NGINX_CONF_DIR/conf.d"
else
    NGINX_CONF_DIR="/etc/nginx"
    NGINX_SITES_DIR="$NGINX_CONF_DIR/sites-available"
fi

# Buat direktori jika tidak ada
mkdir -p $NGINX_SITES_DIR

# Buat konfigurasi Nginx baru
NGINX_CONF="$NGINX_SITES_DIR/default.conf"
if [ "$IN_DOCKER" = false ] && [ -d "$NGINX_CONF_DIR/sites-enabled" ]; then
    NGINX_CONF="$NGINX_CONF_DIR/sites-available/default"
fi

echo "Membuat konfigurasi Nginx baru di: $NGINX_CONF"

# Backup konfigurasi lama jika ada
if [ -f "$NGINX_CONF" ]; then
    mv "$NGINX_CONF" "$NGINX_CONF.old"
    echo "Konfigurasi lama di-backup ke: $NGINX_CONF.old"
fi

# Buat konfigurasi Nginx baru
cat > "$NGINX_CONF" << EOD
server {
    listen 80;
    listen [::]:80;
    
    # Ganti dengan domain atau IP Anda
    server_name _;
    
    root $APP_ROOT/public;
    index index.php index.html index.htm;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        
        # Gunakan socket jika tersedia, jika tidak gunakan port
        fastcgi_pass unix:/run/php/php$PHP_VERSION-fpm.sock;
        # Jika socket tidak tersedia, gunakan port
        # fastcgi_pass 127.0.0.1:9000;
        
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        
        # Tambahkan timeout yang cukup
        fastcgi_read_timeout 300;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        
        # Tambahkan buffer yang cukup
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }
    
    location ~ /\.ht {
        deny all;
    }
    
    # Tambahkan konfigurasi untuk file statis
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp)$ {
        expires 1y;
        add_header Cache-Control "public, max-age=31536000";
        access_log off;
    }
    
    # Tambahkan konfigurasi untuk Livewire
    location /livewire {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
}
EOD

echo "Konfigurasi Nginx baru dibuat."

# Aktifkan site jika menggunakan sites-enabled
if [ "$IN_DOCKER" = false ] && [ -d "$NGINX_CONF_DIR/sites-enabled" ]; then
    if [ -L "$NGINX_CONF_DIR/sites-enabled/default" ]; then
        rm "$NGINX_CONF_DIR/sites-enabled/default"
    fi
    ln -s "$NGINX_CONF_DIR/sites-available/default" "$NGINX_CONF_DIR/sites-enabled/default"
    echo "Site diaktifkan di sites-enabled."
fi

# 2. Mengatur ulang konfigurasi PHP-FPM
echo ""
echo ">> Mengatur ulang konfigurasi PHP-FPM..."

# Cari lokasi konfigurasi PHP-FPM
if [ "$IN_DOCKER" = true ]; then
    PHP_FPM_CONF_DIR="/etc/php/$PHP_VERSION/fpm"
    if [ ! -d "$PHP_FPM_CONF_DIR" ]; then
        PHP_FPM_CONF_DIR="/etc/php-fpm.d"
    fi
else
    PHP_FPM_CONF_DIR="/etc/php/$PHP_VERSION/fpm"
    if [ ! -d "$PHP_FPM_CONF_DIR" ]; then
        PHP_FPM_CONF_DIR="/etc/php-fpm.d"
    fi
fi

echo "Direktori konfigurasi PHP-FPM: $PHP_FPM_CONF_DIR"

# Periksa file konfigurasi PHP-FPM
if [ -d "$PHP_FPM_CONF_DIR" ]; then
    # Periksa file www.conf
    PHP_FPM_POOL_CONF="$PHP_FPM_CONF_DIR/pool.d/www.conf"
    if [ ! -f "$PHP_FPM_POOL_CONF" ]; then
        PHP_FPM_POOL_CONF="$PHP_FPM_CONF_DIR/www.conf"
    fi
    
    if [ -f "$PHP_FPM_POOL_CONF" ]; then
        echo "File konfigurasi pool PHP-FPM ditemukan: $PHP_FPM_POOL_CONF"
        
        # Backup file konfigurasi
        cp "$PHP_FPM_POOL_CONF" "$PHP_FPM_POOL_CONF.old"
        echo "Konfigurasi lama di-backup ke: $PHP_FPM_POOL_CONF.old"
        
        # Buat konfigurasi PHP-FPM baru
        cat > "$PHP_FPM_POOL_CONF" << EOD
[www]
user = www-data
group = www-data

listen = /run/php/php$PHP_VERSION-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

; Jika socket tidak tersedia, gunakan port
;listen = 127.0.0.1:9000

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

request_terminate_timeout = 300
request_slowlog_timeout = 60s
slowlog = /var/log/php$PHP_VERSION-fpm.slow.log

php_admin_value[error_log] = /var/log/php$PHP_VERSION-fpm.log
php_admin_flag[log_errors] = on

php_value[session.save_handler] = files
php_value[session.save_path] = $APP_ROOT/storage/framework/sessions
php_value[soap.wsdl_cache_dir] = $APP_ROOT/storage/framework/wsdlcache

php_value[opcache.enable] = 1
php_value[opcache.memory_consumption] = 128
php_value[opcache.interned_strings_buffer] = 8
php_value[opcache.max_accelerated_files] = 10000
php_value[opcache.validate_timestamps] = 1
php_value[opcache.revalidate_freq] = 2

php_value[upload_max_filesize] = 100M
php_value[post_max_size] = 100M
php_value[memory_limit] = 256M
php_value[max_execution_time] = 300
php_value[max_input_time] = 300
EOD
        echo "Konfigurasi PHP-FPM baru dibuat."
    else
        echo "File konfigurasi pool PHP-FPM tidak ditemukan."
    fi
else
    echo "Direktori konfigurasi PHP-FPM tidak ditemukan."
fi

# 3. Mengatur ulang file-file penting Laravel
echo ""
echo ">> Mengatur ulang file-file penting Laravel..."

# Buat file .htaccess baru
echo "Membuat file .htaccess baru..."
cat > "$APP_ROOT/public/.htaccess" << EOD
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
echo "File .htaccess baru dibuat."

# Buat file index.php baru
echo "Membuat file index.php baru..."
cat > "$APP_ROOT/public/index.php" << EOD
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
echo "File index.php baru dibuat."

# 4. Mengatur ulang izin file
echo ""
echo ">> Mengatur ulang izin file..."
chmod -R 755 "$APP_ROOT/public"
chmod -R 755 "$APP_ROOT/bootstrap"
chmod -R 777 "$APP_ROOT/storage"
chmod -R 777 "$APP_ROOT/bootstrap/cache"

# Pastikan www-data memiliki akses ke file-file yang diperlukan
if [ "$IN_DOCKER" = false ] && command -v id >/dev/null 2>&1 && id -u www-data >/dev/null 2>&1; then
    chown -R www-data:www-data "$APP_ROOT/storage"
    chown -R www-data:www-data "$APP_ROOT/bootstrap/cache"
    echo "Izin file diperbaiki dan kepemilikan diubah ke www-data."
else
    echo "Izin file diperbaiki."
fi

# Buat direktori yang diperlukan
mkdir -p "$APP_ROOT/storage/app/public/gallery"
mkdir -p "$APP_ROOT/storage/app/livewire-tmp"
mkdir -p "$APP_ROOT/storage/framework/sessions"
mkdir -p "$APP_ROOT/storage/framework/views"
mkdir -p "$APP_ROOT/storage/framework/cache"
chmod -R 777 "$APP_ROOT/storage/app/public/gallery"
chmod -R 777 "$APP_ROOT/storage/app/livewire-tmp"
chmod -R 777 "$APP_ROOT/storage/framework/sessions"
chmod -R 777 "$APP_ROOT/storage/framework/views"
chmod -R 777 "$APP_ROOT/storage/framework/cache"
echo "Direktori yang diperlukan dibuat dan izin diatur."

# 5. Membersihkan cache dan mengoptimalkan aplikasi
echo ""
echo ">> Membersihkan cache dan mengoptimalkan aplikasi..."
php "$APP_ROOT/artisan" config:clear
php "$APP_ROOT/artisan" cache:clear
php "$APP_ROOT/artisan" route:clear
php "$APP_ROOT/artisan" view:clear
php "$APP_ROOT/artisan" optimize:clear

# Buat ulang symbolic link storage
if [ -L "$APP_ROOT/public/storage" ]; then
    rm "$APP_ROOT/public/storage"
fi
php "$APP_ROOT/artisan" storage:link
echo "Cache dibersihkan dan symbolic link storage dibuat ulang."

# 6. Me-restart semua layanan
echo ""
echo ">> Me-restart semua layanan..."

if [ "$IN_DOCKER" = true ]; then
    if command -v service >/dev/null 2>&1; then
        service php$PHP_VERSION-fpm restart || service php-fpm restart || echo "Tidak dapat me-restart PHP-FPM."
        service nginx restart || echo "Tidak dapat me-restart Nginx."
        echo "Layanan di-restart di dalam container."
    else
        echo "Command service tidak tersedia di container."
        
        # Coba cari PID dan kirim sinyal reload
        PHP_FPM_PID=$(pgrep -f "php-fpm: master")
        if [ -n "$PHP_FPM_PID" ]; then
            kill -USR2 $PHP_FPM_PID
            echo "Sinyal reload dikirim ke PHP-FPM."
        fi
        
        NGINX_PID=$(pgrep -f "nginx: master")
        if [ -n "$NGINX_PID" ]; then
            kill -HUP $NGINX_PID
            echo "Sinyal reload dikirim ke Nginx."
        fi
    fi
else
    if command -v systemctl >/dev/null 2>&1; then
        systemctl restart php$PHP_VERSION-fpm || echo "Tidak dapat me-restart PHP-FPM."
        systemctl restart nginx || echo "Tidak dapat me-restart Nginx."
        echo "Layanan di-restart menggunakan systemctl."
    elif command -v service >/dev/null 2>&1; then
        service php$PHP_VERSION-fpm restart || echo "Tidak dapat me-restart PHP-FPM."
        service nginx restart || echo "Tidak dapat me-restart Nginx."
        echo "Layanan di-restart menggunakan service."
    else
        echo "Tidak dapat mendeteksi cara me-restart layanan."
    fi
fi

echo ""
echo "====================================================="
echo "      RESET KONFIGURASI SELESAI                      "
echo "====================================================="
echo ""
echo "Semua konfigurasi telah diatur ulang dari nol."
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log error Nginx: /var/log/nginx/error.log"
echo "2. Log error PHP-FPM: /var/log/php$PHP_VERSION-fpm.log"
echo "3. Log Laravel: $APP_ROOT/storage/logs/laravel.log"
echo "====================================================="
