#!/bin/bash

# Script untuk memeriksa dan memperbaiki masalah dengan Nginx 1.27.1

echo "====================================================="
echo "      PERBAIKAN NGINX 1.27.1                         "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Memeriksa dan memperbaiki konfigurasi Nginx 1.27.1"
echo "2. Memeriksa dan memperbaiki kompatibilitas dengan PHP-FPM"
echo "3. Mengatur ulang konfigurasi Nginx yang optimal"
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

# 1. Memeriksa versi Nginx
echo ">> Memeriksa versi Nginx..."
NGINX_VERSION=$(nginx -v 2>&1 | grep -o '[0-9]\.[0-9]*\.[0-9]*')
echo "Versi Nginx terdeteksi: $NGINX_VERSION"

# Periksa apakah ini benar-benar Nginx 1.27.1
if [ "$NGINX_VERSION" = "1.27.1" ]; then
    echo "Terdeteksi Nginx versi 1.27.1 (versi development/unstable)."
    echo "Versi ini mungkin memiliki masalah kompatibilitas. Disarankan untuk menggunakan versi stabil."
else
    echo "Versi Nginx bukan 1.27.1. Script ini tetap akan berjalan untuk memperbaiki konfigurasi."
fi
echo ""

# 2. Memeriksa dan memperbaiki konfigurasi Nginx
echo ">> Memeriksa konfigurasi Nginx..."

# Cari lokasi konfigurasi Nginx
if [ "$IN_DOCKER" = true ]; then
    NGINX_CONF_DIR="/etc/nginx"
    NGINX_SITES_DIR="$NGINX_CONF_DIR/conf.d"
else
    NGINX_CONF_DIR="/etc/nginx"
    NGINX_SITES_DIR="$NGINX_CONF_DIR/sites-available"
fi

# Buat direktori jika tidak ada
mkdir -p $NGINX_SITES_DIR

# Cari file konfigurasi default
NGINX_CONF="$NGINX_SITES_DIR/default.conf"
if [ "$IN_DOCKER" = false ] && [ -d "$NGINX_CONF_DIR/sites-enabled" ]; then
    NGINX_CONF="$NGINX_CONF_DIR/sites-available/default"
fi

echo "File konfigurasi Nginx: $NGINX_CONF"

# Backup konfigurasi lama jika ada
if [ -f "$NGINX_CONF" ]; then
    cp "$NGINX_CONF" "$NGINX_CONF.bak.$(date +%Y%m%d%H%M%S)"
    echo "Konfigurasi lama di-backup."
fi

# 3. Memeriksa dan memperbaiki kompatibilitas dengan PHP-FPM
echo ""
echo ">> Memeriksa kompatibilitas dengan PHP-FPM..."

# Cari versi PHP yang digunakan
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "Versi PHP terdeteksi: $PHP_VERSION"

# Periksa apakah PHP-FPM berjalan
if [ "$IN_DOCKER" = true ]; then
    PHP_FPM_RUNNING=$(ps aux | grep -v grep | grep -c "php-fpm")
else
    if command -v systemctl >/dev/null 2>&1; then
        PHP_FPM_STATUS=$(systemctl is-active php$PHP_VERSION-fpm 2>/dev/null || echo "inactive")
        PHP_FPM_RUNNING=$([ "$PHP_FPM_STATUS" = "active" ] && echo "1" || echo "0")
    else
        PHP_FPM_RUNNING=$(ps aux | grep -v grep | grep -c "php-fpm")
    fi
fi

if [ "$PHP_FPM_RUNNING" -gt 0 ]; then
    echo "PHP-FPM terdeteksi berjalan."
else
    echo "PHP-FPM tidak terdeteksi berjalan. Mencoba me-restart..."
    if [ "$IN_DOCKER" = true ]; then
        if command -v service >/dev/null 2>&1; then
            service php$PHP_VERSION-fpm restart || service php-fpm restart || echo "Tidak dapat me-restart PHP-FPM."
        fi
    else
        if command -v systemctl >/dev/null 2>&1; then
            systemctl restart php$PHP_VERSION-fpm || echo "Tidak dapat me-restart PHP-FPM."
        elif command -v service >/dev/null 2>&1; then
            service php$PHP_VERSION-fpm restart || echo "Tidak dapat me-restart PHP-FPM."
        fi
    fi
fi

# Periksa socket PHP-FPM
PHP_FPM_SOCK="/run/php/php$PHP_VERSION-fpm.sock"
if [ -S "$PHP_FPM_SOCK" ]; then
    echo "Socket PHP-FPM ditemukan di: $PHP_FPM_SOCK"
    USE_SOCKET=true
else
    echo "Socket PHP-FPM tidak ditemukan di: $PHP_FPM_SOCK"
    echo "Akan menggunakan port 9000 sebagai gantinya."
    USE_SOCKET=false
fi

# 4. Mengatur ulang konfigurasi Nginx yang optimal
echo ""
echo ">> Mengatur ulang konfigurasi Nginx yang optimal..."

# Buat konfigurasi Nginx baru yang optimal untuk Nginx 1.27.1
cat > "$NGINX_CONF" << EOD
server {
    listen 80;
    listen [::]:80;
    
    # Ganti dengan domain atau IP Anda
    server_name _;
    
    root $APP_ROOT/public;
    index index.php index.html index.htm;
    
    # Tambahkan buffer yang cukup untuk Nginx 1.27.1
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
    gzip_disable "MSIE [1-6]\.";
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        
EOD

# Tambahkan konfigurasi fastcgi_pass berdasarkan ketersediaan socket
if [ "$USE_SOCKET" = true ]; then
    cat >> "$NGINX_CONF" << EOD
        # Gunakan socket
        fastcgi_pass unix:$PHP_FPM_SOCK;
EOD
else
    cat >> "$NGINX_CONF" << EOD
        # Gunakan port
        fastcgi_pass 127.0.0.1:9000;
EOD
fi

# Lanjutkan konfigurasi
cat >> "$NGINX_CONF" << EOD
        
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
        
        # Tambahkan parameter untuk Nginx 1.27.1
        fastcgi_keep_conn on;
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

echo "Konfigurasi Nginx baru yang optimal untuk Nginx 1.27.1 dibuat."

# Aktifkan site jika menggunakan sites-enabled
if [ "$IN_DOCKER" = false ] && [ -d "$NGINX_CONF_DIR/sites-enabled" ]; then
    if [ -L "$NGINX_CONF_DIR/sites-enabled/default" ]; then
        rm "$NGINX_CONF_DIR/sites-enabled/default"
    fi
    ln -s "$NGINX_CONF_DIR/sites-available/default" "$NGINX_CONF_DIR/sites-enabled/default"
    echo "Site diaktifkan di sites-enabled."
fi

# 5. Periksa konfigurasi Nginx
echo ""
echo ">> Memeriksa konfigurasi Nginx..."
nginx -t
if [ $? -eq 0 ]; then
    echo "Konfigurasi Nginx valid."
else
    echo "Konfigurasi Nginx tidak valid. Silakan periksa dan perbaiki secara manual."
fi

# 6. Me-restart Nginx
echo ""
echo ">> Me-restart Nginx..."
if [ "$IN_DOCKER" = true ]; then
    if command -v service >/dev/null 2>&1; then
        service nginx restart || echo "Tidak dapat me-restart Nginx dengan service."
    else
        # Coba cari PID Nginx dan kirim sinyal reload
        NGINX_PID=$(pgrep -f "nginx: master")
        if [ -n "$NGINX_PID" ]; then
            kill -HUP $NGINX_PID
            echo "Sinyal reload dikirim ke Nginx."
        else
            echo "Tidak dapat menemukan PID Nginx."
        fi
    fi
else
    if command -v systemctl >/dev/null 2>&1; then
        systemctl restart nginx || echo "Tidak dapat me-restart Nginx dengan systemctl."
    elif command -v service >/dev/null 2>&1; then
        service nginx restart || echo "Tidak dapat me-restart Nginx dengan service."
    else
        echo "Tidak dapat mendeteksi cara me-restart Nginx."
    fi
fi

echo ""
echo "====================================================="
echo "      PERBAIKAN NGINX 1.27.1 SELESAI                 "
echo "====================================================="
echo ""
echo "Konfigurasi Nginx telah dioptimalkan untuk versi 1.27.1."
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log error Nginx: /var/log/nginx/error.log"
echo "2. Log error PHP-FPM: /var/log/php$PHP_VERSION-fpm.log"
echo "3. Pastikan socket atau port PHP-FPM dan Nginx cocok"
echo "====================================================="
