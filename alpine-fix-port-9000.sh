#!/bin/sh

# Script untuk memperbaiki konfigurasi PHP-FPM dan Nginx di container Alpine Linux
# dengan fokus pada penggunaan port 9000

echo "====================================================="
echo "  PERBAIKAN KONFIGURASI PORT 9000 (ALPINE DOCKER)    "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Mengubah konfigurasi PHP-FPM untuk menggunakan port 9000"
echo "2. Mengubah konfigurasi Nginx untuk menggunakan port 9000"
echo "3. Me-restart layanan PHP-FPM dan Nginx"
echo "4. Me-restart container Docker"
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

# 1. Memeriksa dan memperbaiki konfigurasi PHP-FPM
echo ">> Memeriksa dan memperbaiki konfigurasi PHP-FPM..."

# Cari lokasi konfigurasi PHP-FPM
echo "Mencari lokasi konfigurasi PHP-FPM..."
PHP_FPM_CONF_LOCATIONS="/etc/php/*/fpm/pool.d/www.conf /etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf"

PHP_FPM_CONF_FOUND=false
for PHP_FPM_CONF in $PHP_FPM_CONF_LOCATIONS; do
    if docker exec $WEB_CONTAINER sh -c "[ -f $PHP_FPM_CONF ]"; then
        echo "Konfigurasi PHP-FPM ditemukan di: $PHP_FPM_CONF"
        
        # Backup konfigurasi lama
        docker exec $WEB_CONTAINER sh -c "cp $PHP_FPM_CONF ${PHP_FPM_CONF}.bak"
        echo "Konfigurasi lama di-backup ke: ${PHP_FPM_CONF}.bak"
        
        # Modifikasi konfigurasi PHP-FPM untuk menggunakan port 9000
        docker exec $WEB_CONTAINER sh -c "sed -i 's/listen = .*/listen = 127.0.0.1:9000/g' $PHP_FPM_CONF"
        echo "Konfigurasi PHP-FPM diubah untuk menggunakan port 9000."
        
        PHP_FPM_CONF_FOUND=true
        break
    fi
done

if [ "$PHP_FPM_CONF_FOUND" = false ]; then
    echo "Tidak dapat menemukan file konfigurasi PHP-FPM. Mencoba membuat file baru..."
    
    # Cari direktori konfigurasi PHP-FPM
    PHP_FPM_DIR="/etc/php-fpm.d"
    if docker exec $WEB_CONTAINER sh -c "[ -d $PHP_FPM_DIR ]"; then
        echo "Direktori konfigurasi PHP-FPM ditemukan di: $PHP_FPM_DIR"
        
        # Buat file konfigurasi PHP-FPM baru
        docker exec $WEB_CONTAINER sh -c "cat > $PHP_FPM_DIR/www.conf << 'EOD'
[www]
user = www-data
group = www-data

listen = 127.0.0.1:9000

pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
pm.max_requests = 500

request_terminate_timeout = 300
request_slowlog_timeout = 60s

php_admin_value[error_log] = /var/log/php-fpm.log
php_admin_flag[log_errors] = on

php_value[session.save_handler] = files
php_value[session.save_path] = /var/www/html/storage/framework/sessions

php_value[upload_max_filesize] = 100M
php_value[post_max_size] = 100M
php_value[memory_limit] = 256M
php_value[max_execution_time] = 300
php_value[max_input_time] = 300
EOD"
        echo "File konfigurasi PHP-FPM baru dibuat di: $PHP_FPM_DIR/www.conf"
    else
        echo "Tidak dapat menemukan direktori konfigurasi PHP-FPM."
    fi
fi

# 2. Memeriksa dan memperbaiki konfigurasi Nginx
echo ""
echo ">> Memeriksa dan memperbaiki konfigurasi Nginx..."

# Cari lokasi konfigurasi Nginx
echo "Mencari lokasi konfigurasi Nginx..."
NGINX_CONF_LOCATIONS="/etc/nginx/conf.d/default.conf /etc/nginx/sites-available/default /etc/nginx/nginx.conf"

NGINX_CONF_FOUND=false
for NGINX_CONF in $NGINX_CONF_LOCATIONS; do
    if docker exec $WEB_CONTAINER sh -c "[ -f $NGINX_CONF ]"; then
        echo "Konfigurasi Nginx ditemukan di: $NGINX_CONF"
        
        # Backup konfigurasi lama
        docker exec $WEB_CONTAINER sh -c "cp $NGINX_CONF ${NGINX_CONF}.bak"
        echo "Konfigurasi lama di-backup ke: ${NGINX_CONF}.bak"
        
        # Modifikasi konfigurasi Nginx untuk menggunakan port 9000
        docker exec $WEB_CONTAINER sh -c "sed -i 's/fastcgi_pass unix:.*/fastcgi_pass 127.0.0.1:9000;/g' $NGINX_CONF"
        docker exec $WEB_CONTAINER sh -c "sed -i 's/fastcgi_pass unix:.*/fastcgi_pass 127.0.0.1:9000;/g' $NGINX_CONF"
        echo "Konfigurasi Nginx diubah untuk menggunakan port 9000."
        
        NGINX_CONF_FOUND=true
        break
    fi
done

if [ "$NGINX_CONF_FOUND" = false ]; then
    echo "Tidak dapat menemukan file konfigurasi Nginx. Mencoba membuat file baru..."
    
    # Cari direktori konfigurasi Nginx
    NGINX_DIR="/etc/nginx/conf.d"
    if docker exec $WEB_CONTAINER sh -c "[ -d $NGINX_DIR ]"; then
        echo "Direktori konfigurasi Nginx ditemukan di: $NGINX_DIR"
        
        # Buat file konfigurasi Nginx baru
        docker exec $WEB_CONTAINER sh -c "cat > $NGINX_DIR/default.conf << 'EOD'
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
        
        # Gunakan port 9000
        fastcgi_pass 127.0.0.1:9000;
        
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
        echo "File konfigurasi Nginx baru dibuat di: $NGINX_DIR/default.conf"
    else
        echo "Tidak dapat menemukan direktori konfigurasi Nginx."
    fi
fi

# 3. Me-restart layanan PHP-FPM dan Nginx
echo ""
echo ">> Me-restart layanan PHP-FPM dan Nginx..."

# Restart PHP-FPM
echo "Me-restart PHP-FPM..."
docker exec $WEB_CONTAINER sh -c "if command -v service >/dev/null 2>&1; then service php-fpm restart || echo 'Tidak dapat me-restart PHP-FPM dengan service'; else pkill -USR2 php-fpm || echo 'Tidak dapat me-restart PHP-FPM dengan pkill'; fi"

# Restart Nginx
echo "Me-restart Nginx..."
docker exec $WEB_CONTAINER sh -c "if command -v service >/dev/null 2>&1; then service nginx restart || echo 'Tidak dapat me-restart Nginx dengan service'; else pkill -HUP nginx || echo 'Tidak dapat me-restart Nginx dengan pkill'; fi"

# 4. Me-restart container
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
echo "Konfigurasi PHP-FPM dan Nginx telah diubah untuk menggunakan port 9000."
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log Docker dengan perintah: docker logs $WEB_CONTAINER"
echo "2. Pastikan PHP-FPM dan Nginx berjalan dengan benar"
echo "====================================================="
