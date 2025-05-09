#!/bin/bash

# Script untuk memeriksa dan memperbaiki konfigurasi PHP-FPM dan Nginx

echo "====================================================="
echo "      PERBAIKAN KONFIGURASI PHP-FPM DAN NGINX        "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Memeriksa dan memperbaiki konfigurasi PHP-FPM"
echo "2. Memeriksa dan memperbaiki konfigurasi Nginx"
echo "3. Me-restart layanan PHP-FPM dan Nginx"
echo ""

# Deteksi apakah berjalan di Docker
IN_DOCKER=false
if [ -f /.dockerenv ] || grep -q docker /proc/1/cgroup 2>/dev/null; then
    IN_DOCKER=true
    echo "Terdeteksi berjalan di dalam container Docker."
else
    echo "Terdeteksi berjalan di lingkungan host biasa."
fi

# 1. Memeriksa dan memperbaiki konfigurasi PHP-FPM
echo ">> Memeriksa konfigurasi PHP-FPM..."

# Cari versi PHP yang digunakan
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "Versi PHP terdeteksi: $PHP_VERSION"

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
        cp "$PHP_FPM_POOL_CONF" "$PHP_FPM_POOL_CONF.bak"
        echo "Backup file konfigurasi dibuat: $PHP_FPM_POOL_CONF.bak"
        
        # Periksa dan perbaiki konfigurasi
        # 1. Pastikan listen menggunakan socket atau port yang benar
        if grep -q "listen = 127.0.0.1:9000" "$PHP_FPM_POOL_CONF"; then
            echo "PHP-FPM dikonfigurasi untuk mendengarkan pada 127.0.0.1:9000"
        elif grep -q "listen = /run/php/php$PHP_VERSION-fpm.sock" "$PHP_FPM_POOL_CONF"; then
            echo "PHP-FPM dikonfigurasi untuk mendengarkan pada socket /run/php/php$PHP_VERSION-fpm.sock"
        else
            echo "Konfigurasi listen PHP-FPM tidak standar. Memeriksa..."
            LISTEN_LINE=$(grep "listen = " "$PHP_FPM_POOL_CONF")
            echo "Konfigurasi listen saat ini: $LISTEN_LINE"
        fi
        
        # 2. Pastikan user dan group benar
        USER_LINE=$(grep "^user = " "$PHP_FPM_POOL_CONF")
        GROUP_LINE=$(grep "^group = " "$PHP_FPM_POOL_CONF")
        echo "Konfigurasi user: $USER_LINE"
        echo "Konfigurasi group: $GROUP_LINE"
        
        # 3. Pastikan pm.max_children cukup
        MAX_CHILDREN_LINE=$(grep "pm.max_children" "$PHP_FPM_POOL_CONF")
        echo "Konfigurasi pm.max_children: $MAX_CHILDREN_LINE"
        
        # 4. Pastikan request_terminate_timeout cukup
        TIMEOUT_LINE=$(grep "request_terminate_timeout" "$PHP_FPM_POOL_CONF")
        if [ -z "$TIMEOUT_LINE" ]; then
            echo "request_terminate_timeout tidak dikonfigurasi. Menambahkan..."
            sed -i '/^pm.max_children/a request_terminate_timeout = 300' "$PHP_FPM_POOL_CONF"
            echo "request_terminate_timeout = 300 ditambahkan."
        else
            echo "Konfigurasi request_terminate_timeout: $TIMEOUT_LINE"
        fi
    else
        echo "File konfigurasi pool PHP-FPM tidak ditemukan."
    fi
else
    echo "Direktori konfigurasi PHP-FPM tidak ditemukan."
fi
echo ""

# 2. Memeriksa dan memperbaiki konfigurasi Nginx
echo ">> Memeriksa konfigurasi Nginx..."

# Cari lokasi konfigurasi Nginx
if [ "$IN_DOCKER" = true ]; then
    NGINX_CONF_DIR="/etc/nginx"
else
    NGINX_CONF_DIR="/etc/nginx"
fi

echo "Direktori konfigurasi Nginx: $NGINX_CONF_DIR"

# Periksa file konfigurasi Nginx
if [ -d "$NGINX_CONF_DIR" ]; then
    # Periksa file konfigurasi default
    NGINX_DEFAULT_CONF="$NGINX_CONF_DIR/sites-enabled/default"
    if [ ! -f "$NGINX_DEFAULT_CONF" ]; then
        NGINX_DEFAULT_CONF="$NGINX_CONF_DIR/conf.d/default.conf"
    fi
    
    if [ -f "$NGINX_DEFAULT_CONF" ]; then
        echo "File konfigurasi default Nginx ditemukan: $NGINX_DEFAULT_CONF"
        
        # Backup file konfigurasi
        cp "$NGINX_DEFAULT_CONF" "$NGINX_DEFAULT_CONF.bak"
        echo "Backup file konfigurasi dibuat: $NGINX_DEFAULT_CONF.bak"
        
        # Periksa dan perbaiki konfigurasi
        # 1. Pastikan fastcgi_pass menggunakan socket atau port yang benar
        if grep -q "fastcgi_pass unix:/run/php/php$PHP_VERSION-fpm.sock" "$NGINX_DEFAULT_CONF"; then
            echo "Nginx dikonfigurasi untuk menggunakan socket PHP-FPM"
        elif grep -q "fastcgi_pass 127.0.0.1:9000" "$NGINX_DEFAULT_CONF"; then
            echo "Nginx dikonfigurasi untuk menggunakan port PHP-FPM"
        else
            echo "Konfigurasi fastcgi_pass Nginx tidak standar. Memeriksa..."
            FASTCGI_LINE=$(grep "fastcgi_pass" "$NGINX_DEFAULT_CONF")
            echo "Konfigurasi fastcgi_pass saat ini: $FASTCGI_LINE"
        fi
        
        # 2. Pastikan fastcgi_read_timeout cukup
        TIMEOUT_LINE=$(grep "fastcgi_read_timeout" "$NGINX_DEFAULT_CONF")
        if [ -z "$TIMEOUT_LINE" ]; then
            echo "fastcgi_read_timeout tidak dikonfigurasi. Menambahkan..."
            sed -i '/fastcgi_pass/a \        fastcgi_read_timeout 300;' "$NGINX_DEFAULT_CONF"
            echo "fastcgi_read_timeout = 300 ditambahkan."
        else
            echo "Konfigurasi fastcgi_read_timeout: $TIMEOUT_LINE"
        fi
        
        # 3. Pastikan root path benar
        ROOT_LINE=$(grep "root" "$NGINX_DEFAULT_CONF")
        echo "Konfigurasi root: $ROOT_LINE"
    else
        echo "File konfigurasi default Nginx tidak ditemukan."
    fi
else
    echo "Direktori konfigurasi Nginx tidak ditemukan."
fi
echo ""

# 3. Me-restart layanan PHP-FPM dan Nginx
echo ">> Me-restart layanan PHP-FPM dan Nginx..."

if [ "$IN_DOCKER" = true ]; then
    if command -v service >/dev/null 2>&1; then
        service php$PHP_VERSION-fpm restart || echo "Tidak dapat me-restart PHP-FPM"
        service nginx restart || echo "Tidak dapat me-restart Nginx"
        echo "Layanan di-restart di dalam container."
    else
        echo "Command service tidak tersedia di container."
    fi
else
    if command -v systemctl >/dev/null 2>&1; then
        systemctl restart php$PHP_VERSION-fpm || echo "Tidak dapat me-restart PHP-FPM"
        systemctl restart nginx || echo "Tidak dapat me-restart Nginx"
        echo "Layanan di-restart menggunakan systemctl."
    elif command -v service >/dev/null 2>&1; then
        service php$PHP_VERSION-fpm restart || echo "Tidak dapat me-restart PHP-FPM"
        service nginx restart || echo "Tidak dapat me-restart Nginx"
        echo "Layanan di-restart menggunakan service."
    else
        echo "Tidak dapat mendeteksi cara me-restart layanan."
    fi
fi
echo ""

echo "====================================================="
echo "      PERBAIKAN SELESAI                              "
echo "====================================================="
echo ""
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log error Nginx: /var/log/nginx/error.log"
echo "2. Log error PHP-FPM: /var/log/php$PHP_VERSION-fpm.log"
echo "3. Pastikan socket atau port PHP-FPM dan Nginx cocok"
echo "====================================================="
