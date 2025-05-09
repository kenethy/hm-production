#!/bin/sh

# Script untuk memperbaiki error 502 Bad Gateway di container Alpine Linux
# Script ini menggunakan /bin/sh yang tersedia di hampir semua image Docker

echo "====================================================="
echo "  PERBAIKAN ERROR 502 BAD GATEWAY (ALPINE DOCKER)    "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Mengatur ulang konfigurasi Nginx"
echo "2. Mengatur ulang konfigurasi PHP-FPM"
echo "3. Mengatur ulang file-file penting Laravel"
echo "4. Mengatur ulang izin file"
echo "5. Me-restart container Docker"
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

# 2. Mengatur ulang file-file penting Laravel
echo ">> Mengatur ulang file-file penting Laravel..."

# Buat file .htaccess baru
echo "Membuat file .htaccess baru..."
docker exec $WEB_CONTAINER sh -c "cat > /var/www/html/public/.htaccess << 'EOD'
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
EOD"

# Buat file index.php baru
echo "Membuat file index.php baru..."
docker exec $WEB_CONTAINER sh -c "cat > /var/www/html/public/index.php << 'EOD'
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
EOD"

echo "File-file penting Laravel diatur ulang."
echo ""

# 3. Mengatur ulang izin file
echo ">> Mengatur ulang izin file..."
docker exec $WEB_CONTAINER sh -c "
chmod -R 755 /var/www/html/public
chmod -R 755 /var/www/html/bootstrap
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Buat direktori yang diperlukan
mkdir -p /var/www/html/storage/app/public/gallery
mkdir -p /var/www/html/storage/app/livewire-tmp
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
chmod -R 777 /var/www/html/storage/app/public/gallery
chmod -R 777 /var/www/html/storage/app/livewire-tmp
chmod -R 777 /var/www/html/storage/framework/sessions
chmod -R 777 /var/www/html/storage/framework/views
chmod -R 777 /var/www/html/storage/framework/cache
"
echo "Izin file diatur ulang."
echo ""

# 4. Mengatur ulang konfigurasi Nginx
echo ">> Mengatur ulang konfigurasi Nginx..."

# Cari lokasi konfigurasi Nginx
echo "Mencari lokasi konfigurasi Nginx..."
NGINX_CONF_LOCATIONS="/etc/nginx/conf.d/default.conf /etc/nginx/sites-available/default /etc/nginx/nginx.conf"

for NGINX_CONF in $NGINX_CONF_LOCATIONS; do
    if docker exec $WEB_CONTAINER sh -c "[ -f $NGINX_CONF ]"; then
        echo "Konfigurasi Nginx ditemukan di: $NGINX_CONF"
        
        # Backup konfigurasi lama
        docker exec $WEB_CONTAINER sh -c "cp $NGINX_CONF ${NGINX_CONF}.bak"
        echo "Konfigurasi lama di-backup ke: ${NGINX_CONF}.bak"
        
        # Buat konfigurasi Nginx baru
        docker exec $WEB_CONTAINER sh -c "cat > $NGINX_CONF << 'EOD'
server {
    listen 80;
    listen [::]:80;
    
    # Ganti dengan domain atau IP Anda
    server_name _;
    
    root /var/www/html/public;
    index index.php index.html index.htm;
    
    # Tambahkan buffer yang cukup
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    
    # Tambahkan timeout yang cukup
    keepalive_timeout 300;
    send_timeout 300;
    
    # Konfigurasi gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml application/javascript;
    gzip_disable \"MSIE [1-6]\\\\.\\";
    
    location / {
        try_files \\\$uri \\\$uri/ /index.php?\\\$query_string;
    }
    
    location ~ \\\\.php$ {
        try_files \\\$uri =404;
        fastcgi_split_path_info ^(.+\\\\.php)(/.+)$;
        
        # Coba gunakan socket terlebih dahulu
        fastcgi_pass unix:/run/php/php-fpm.sock;
        # Jika socket tidak tersedia, gunakan baris di bawah ini sebagai gantinya
        # fastcgi_pass 127.0.0.1:9000;
        
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \\\$document_root\\\$fastcgi_script_name;
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
    
    location ~ /\\\\.ht {
        deny all;
    }
    
    # Tambahkan konfigurasi untuk file statis
    location ~* \\\\.(jpg|jpeg|png|gif|ico|css|js|webp)$ {
        expires 1y;
        add_header Cache-Control \"public, max-age=31536000\";
        access_log off;
    }
    
    # Tambahkan konfigurasi untuk Livewire
    location /livewire {
        try_files \\\$uri \\\$uri/ /index.php?\\\$query_string;
    }
}
EOD"
        echo "Konfigurasi Nginx baru dibuat di: $NGINX_CONF"
        break
    fi
done

if [ -z "$NGINX_CONF" ]; then
    echo "Tidak dapat menemukan file konfigurasi Nginx. Mencoba membuat file baru..."
    docker exec $WEB_CONTAINER sh -c "mkdir -p /etc/nginx/conf.d"
    docker exec $WEB_CONTAINER sh -c "cat > /etc/nginx/conf.d/default.conf << 'EOD'
server {
    listen 80;
    listen [::]:80;
    
    # Ganti dengan domain atau IP Anda
    server_name _;
    
    root /var/www/html/public;
    index index.php index.html index.htm;
    
    # Tambahkan buffer yang cukup
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    
    # Tambahkan timeout yang cukup
    keepalive_timeout 300;
    send_timeout 300;
    
    # Konfigurasi gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml application/javascript;
    gzip_disable \"MSIE [1-6]\\\\.\\";
    
    location / {
        try_files \\\$uri \\\$uri/ /index.php?\\\$query_string;
    }
    
    location ~ \\\\.php$ {
        try_files \\\$uri =404;
        fastcgi_split_path_info ^(.+\\\\.php)(/.+)$;
        
        # Coba gunakan socket terlebih dahulu
        fastcgi_pass unix:/run/php/php-fpm.sock;
        # Jika socket tidak tersedia, gunakan baris di bawah ini sebagai gantinya
        # fastcgi_pass 127.0.0.1:9000;
        
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \\\$document_root\\\$fastcgi_script_name;
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
    
    location ~ /\\\\.ht {
        deny all;
    }
    
    # Tambahkan konfigurasi untuk file statis
    location ~* \\\\.(jpg|jpeg|png|gif|ico|css|js|webp)$ {
        expires 1y;
        add_header Cache-Control \"public, max-age=31536000\";
        access_log off;
    }
    
    # Tambahkan konfigurasi untuk Livewire
    location /livewire {
        try_files \\\$uri \\\$uri/ /index.php?\\\$query_string;
    }
}
EOD"
    echo "File konfigurasi Nginx baru dibuat di: /etc/nginx/conf.d/default.conf"
fi

# 5. Periksa dan perbaiki konfigurasi PHP-FPM
echo ""
echo ">> Memeriksa dan memperbaiki konfigurasi PHP-FPM..."

# Cari lokasi konfigurasi PHP-FPM
echo "Mencari lokasi konfigurasi PHP-FPM..."
PHP_FPM_CONF_LOCATIONS="/etc/php/*/fpm/pool.d/www.conf /etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf"

for PHP_FPM_CONF in $PHP_FPM_CONF_LOCATIONS; do
    if docker exec $WEB_CONTAINER sh -c "[ -f $PHP_FPM_CONF ]"; then
        echo "Konfigurasi PHP-FPM ditemukan di: $PHP_FPM_CONF"
        
        # Backup konfigurasi lama
        docker exec $WEB_CONTAINER sh -c "cp $PHP_FPM_CONF ${PHP_FPM_CONF}.bak"
        echo "Konfigurasi lama di-backup ke: ${PHP_FPM_CONF}.bak"
        
        # Modifikasi konfigurasi PHP-FPM untuk menggunakan port 9000
        docker exec $WEB_CONTAINER sh -c "sed -i 's/listen = .*/listen = 127.0.0.1:9000/g' $PHP_FPM_CONF"
        echo "Konfigurasi PHP-FPM diubah untuk menggunakan port 9000."
        
        # Modifikasi konfigurasi Nginx untuk menggunakan port 9000
        for NGINX_CONF in $NGINX_CONF_LOCATIONS; do
            if docker exec $WEB_CONTAINER sh -c "[ -f $NGINX_CONF ]"; then
                docker exec $WEB_CONTAINER sh -c "sed -i 's/fastcgi_pass unix:.*/fastcgi_pass 127.0.0.1:9000;/g' $NGINX_CONF"
                echo "Konfigurasi Nginx diubah untuk menggunakan port 9000."
                break
            fi
        done
        
        break
    fi
done

if [ -z "$PHP_FPM_CONF" ]; then
    echo "Tidak dapat menemukan file konfigurasi PHP-FPM."
fi

# 6. Bersihkan cache Laravel
echo ""
echo ">> Membersihkan cache Laravel..."
docker exec $WEB_CONTAINER sh -c "
if [ -f /var/www/html/artisan ]; then
    php /var/www/html/artisan config:clear
    php /var/www/html/artisan cache:clear
    php /var/www/html/artisan route:clear
    php /var/www/html/artisan view:clear
    php /var/www/html/artisan optimize:clear
    
    # Buat ulang symbolic link storage
    if [ -L /var/www/html/public/storage ]; then
        rm /var/www/html/public/storage
    fi
    php /var/www/html/artisan storage:link
    echo 'Cache Laravel dibersihkan dan symbolic link storage dibuat ulang.'
else
    echo 'File artisan tidak ditemukan.'
fi
"

# 7. Me-restart container
echo ""
echo ">> Me-restart container..."
read -p "Apakah Anda ingin me-restart container $WEB_CONTAINER? (y/n): " restart_container
if [ "$restart_container" = "y" ] || [ "$restart_container" = "Y" ]; then
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
echo "Semua konfigurasi telah diatur ulang."
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log Docker dengan perintah: docker logs $WEB_CONTAINER"
echo "2. Pastikan port dan socket PHP-FPM dikonfigurasi dengan benar"
echo "====================================================="
