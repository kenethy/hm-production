#!/bin/bash

# Script untuk mendeteksi dan me-restart web server yang benar

echo "====================================================="
echo "      DETEKSI DAN RESTART WEB SERVER                 "
echo "====================================================="
echo ""

# Deteksi apakah berjalan di Docker
IN_DOCKER=false
if [ -f /.dockerenv ] || grep -q docker /proc/1/cgroup 2>/dev/null; then
    IN_DOCKER=true
    echo "Terdeteksi berjalan di dalam container Docker."
else
    echo "Terdeteksi berjalan di lingkungan host biasa."
fi

# Fungsi untuk mendeteksi web server
detect_webserver() {
    echo "Mendeteksi web server yang digunakan..."
    
    # Cek proses yang berjalan
    NGINX_RUNNING=false
    APACHE_RUNNING=false
    
    if ps aux | grep -v grep | grep -q nginx; then
        NGINX_RUNNING=true
        echo "Nginx terdeteksi berjalan."
    fi
    
    if ps aux | grep -v grep | grep -q apache; then
        APACHE_RUNNING=true
        echo "Apache terdeteksi berjalan."
    fi
    
    # Cek file konfigurasi
    NGINX_CONFIG_EXISTS=false
    APACHE_CONFIG_EXISTS=false
    
    if [ -d "/etc/nginx" ]; then
        NGINX_CONFIG_EXISTS=true
        echo "Konfigurasi Nginx ditemukan di /etc/nginx."
    fi
    
    if [ -d "/etc/apache2" ] || [ -d "/etc/httpd" ]; then
        APACHE_CONFIG_EXISTS=true
        if [ -d "/etc/apache2" ]; then
            echo "Konfigurasi Apache ditemukan di /etc/apache2."
        else
            echo "Konfigurasi Apache ditemukan di /etc/httpd."
        fi
    fi
    
    # Cek service
    NGINX_SERVICE_EXISTS=false
    APACHE_SERVICE_EXISTS=false
    
    if command -v systemctl >/dev/null 2>&1; then
        if systemctl list-unit-files | grep -q nginx; then
            NGINX_SERVICE_EXISTS=true
            echo "Service Nginx ditemukan."
        fi
        
        if systemctl list-unit-files | grep -q apache; then
            APACHE_SERVICE_EXISTS=true
            echo "Service Apache ditemukan."
        fi
    elif command -v service >/dev/null 2>&1; then
        if service --status-all 2>&1 | grep -q nginx; then
            NGINX_SERVICE_EXISTS=true
            echo "Service Nginx ditemukan."
        fi
        
        if service --status-all 2>&1 | grep -q apache; then
            APACHE_SERVICE_EXISTS=true
            echo "Service Apache ditemukan."
        fi
    fi
    
    # Tentukan web server yang digunakan
    if [ "$NGINX_RUNNING" = true ] || [ "$NGINX_CONFIG_EXISTS" = true ] || [ "$NGINX_SERVICE_EXISTS" = true ]; then
        echo "Nginx terdeteksi sebagai web server utama."
        WEBSERVER="nginx"
    elif [ "$APACHE_RUNNING" = true ] || [ "$APACHE_CONFIG_EXISTS" = true ] || [ "$APACHE_SERVICE_EXISTS" = true ]; then
        echo "Apache terdeteksi sebagai web server utama."
        if [ -d "/etc/apache2" ]; then
            WEBSERVER="apache2"
        else
            WEBSERVER="httpd"
        fi
    else
        echo "Tidak dapat mendeteksi web server. Mencoba Nginx dan Apache..."
        WEBSERVER="both"
    fi
    
    echo "Web server terdeteksi: $WEBSERVER"
}

# Fungsi untuk me-restart PHP-FPM
restart_php_fpm() {
    echo "Me-restart PHP-FPM..."
    
    # Cari versi PHP yang digunakan
    PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
    echo "Versi PHP terdeteksi: $PHP_VERSION"
    
    if command -v systemctl >/dev/null 2>&1; then
        echo "Menggunakan systemctl untuk me-restart PHP-FPM..."
        systemctl restart php$PHP_VERSION-fpm 2>/dev/null || systemctl restart php-fpm 2>/dev/null || echo "Tidak dapat me-restart PHP-FPM dengan systemctl."
    elif command -v service >/dev/null 2>&1; then
        echo "Menggunakan service untuk me-restart PHP-FPM..."
        service php$PHP_VERSION-fpm restart 2>/dev/null || service php-fpm restart 2>/dev/null || echo "Tidak dapat me-restart PHP-FPM dengan service."
    else
        echo "Tidak dapat mendeteksi cara me-restart PHP-FPM."
        
        # Coba cari PID PHP-FPM dan kirim sinyal reload
        PHP_FPM_PID=$(pgrep -f "php-fpm: master")
        if [ -n "$PHP_FPM_PID" ]; then
            echo "Mengirim sinyal reload ke PHP-FPM (PID: $PHP_FPM_PID)..."
            kill -USR2 $PHP_FPM_PID
            echo "Sinyal reload dikirim ke PHP-FPM."
        else
            echo "Tidak dapat menemukan PID PHP-FPM."
        fi
    fi
}

# Fungsi untuk me-restart web server
restart_webserver() {
    echo "Me-restart web server ($WEBSERVER)..."
    
    if [ "$WEBSERVER" = "nginx" ]; then
        if command -v systemctl >/dev/null 2>&1; then
            echo "Menggunakan systemctl untuk me-restart Nginx..."
            systemctl restart nginx 2>/dev/null || echo "Tidak dapat me-restart Nginx dengan systemctl."
        elif command -v service >/dev/null 2>&1; then
            echo "Menggunakan service untuk me-restart Nginx..."
            service nginx restart 2>/dev/null || echo "Tidak dapat me-restart Nginx dengan service."
        else
            echo "Tidak dapat mendeteksi cara me-restart Nginx."
            
            # Coba cari PID Nginx dan kirim sinyal reload
            NGINX_PID=$(pgrep -f "nginx: master")
            if [ -n "$NGINX_PID" ]; then
                echo "Mengirim sinyal reload ke Nginx (PID: $NGINX_PID)..."
                kill -HUP $NGINX_PID
                echo "Sinyal reload dikirim ke Nginx."
            else
                echo "Tidak dapat menemukan PID Nginx."
            fi
        fi
    elif [ "$WEBSERVER" = "apache2" ] || [ "$WEBSERVER" = "httpd" ]; then
        if command -v systemctl >/dev/null 2>&1; then
            echo "Menggunakan systemctl untuk me-restart Apache..."
            systemctl restart $WEBSERVER 2>/dev/null || echo "Tidak dapat me-restart Apache dengan systemctl."
        elif command -v service >/dev/null 2>&1; then
            echo "Menggunakan service untuk me-restart Apache..."
            service $WEBSERVER restart 2>/dev/null || echo "Tidak dapat me-restart Apache dengan service."
        else
            echo "Tidak dapat mendeteksi cara me-restart Apache."
            
            # Coba cari PID Apache dan kirim sinyal reload
            APACHE_PID=$(pgrep -f "apache2: master" || pgrep -f "httpd: master")
            if [ -n "$APACHE_PID" ]; then
                echo "Mengirim sinyal reload ke Apache (PID: $APACHE_PID)..."
                kill -HUP $APACHE_PID
                echo "Sinyal reload dikirim ke Apache."
            else
                echo "Tidak dapat menemukan PID Apache."
            fi
        fi
    elif [ "$WEBSERVER" = "both" ]; then
        echo "Mencoba me-restart Nginx dan Apache..."
        
        if command -v systemctl >/dev/null 2>&1; then
            systemctl restart nginx 2>/dev/null
            systemctl restart apache2 2>/dev/null || systemctl restart httpd 2>/dev/null
        elif command -v service >/dev/null 2>&1; then
            service nginx restart 2>/dev/null
            service apache2 restart 2>/dev/null || service httpd restart 2>/dev/null
        fi
    fi
}

# Deteksi web server
detect_webserver

# Me-restart PHP-FPM
restart_php_fpm

# Me-restart web server
restart_webserver

echo ""
echo "====================================================="
echo "      RESTART SELESAI                                "
echo "====================================================="
echo ""
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log error web server"
echo "2. Log error PHP-FPM"
echo "3. Pastikan socket atau port PHP-FPM dan web server cocok"
echo "====================================================="
