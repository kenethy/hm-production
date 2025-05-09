#!/bin/bash

# Script untuk mengatur ulang semua konfigurasi dari nol di lingkungan Docker

echo "====================================================="
echo "  RESET KONFIGURASI DARI NOL (DOCKER)                "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Mengatur ulang konfigurasi Nginx di dalam container"
echo "2. Mengatur ulang konfigurasi PHP-FPM di dalam container"
echo "3. Mengatur ulang file-file penting Laravel"
echo "4. Mengatur ulang izin file"
echo "5. Membersihkan cache dan mengoptimalkan aplikasi"
echo "6. Me-restart container Docker"
echo ""
echo "PERINGATAN: Ini akan menimpa konfigurasi yang ada!"
echo ""
read -p "Lanjutkan? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operasi dibatalkan."
    exit 1
fi

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

# 1. Mengatur ulang konfigurasi Nginx dan PHP-FPM di dalam container
echo ">> Mengatur ulang konfigurasi di dalam container..."

# Salin script reset-all-config.sh ke container
echo "Menyalin script reset-all-config.sh ke container..."
docker cp reset-all-config.sh $WEB_CONTAINER:/tmp/
docker exec $WEB_CONTAINER chmod +x /tmp/reset-all-config.sh

# Jalankan script di dalam container
echo "Menjalankan script reset-all-config.sh di dalam container..."
docker exec $WEB_CONTAINER bash -c "cd /tmp && echo 'y' | ./reset-all-config.sh"

# 2. Mengatur ulang file-file penting Laravel
echo ""
echo ">> Mengatur ulang file-file penting Laravel..."

# Buat file .env baru jika tidak ada
echo "Memeriksa file .env..."
ENV_EXISTS=$(docker exec $WEB_CONTAINER bash -c "if [ -f /var/www/html/.env ]; then echo 'yes'; else echo 'no'; fi")

if [ "$ENV_EXISTS" == "no" ]; then
    echo "File .env tidak ditemukan. Membuat file .env baru dari .env.example..."
    docker exec $WEB_CONTAINER bash -c "if [ -f /var/www/html/.env.example ]; then cp /var/www/html/.env.example /var/www/html/.env; echo 'File .env dibuat dari .env.example.'; else echo 'File .env.example tidak ditemukan.'; fi"
fi

# 3. Mengatur ulang izin file
echo ""
echo ">> Mengatur ulang izin file..."
docker exec $WEB_CONTAINER bash -c "
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

# 4. Membersihkan cache dan mengoptimalkan aplikasi
echo ""
echo ">> Membersihkan cache dan mengoptimalkan aplikasi..."
docker exec $WEB_CONTAINER bash -c "
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
"
echo "Cache dibersihkan dan symbolic link storage dibuat ulang."

# 5. Memeriksa dan memperbaiki file composer.json
echo ""
echo ">> Memeriksa dan memperbaiki file composer.json..."
docker exec $WEB_CONTAINER bash -c "
if [ -f /var/www/html/composer.json ]; then
    echo 'File composer.json ditemukan.'
    
    # Periksa apakah composer.lock ada
    if [ ! -f /var/www/html/composer.lock ]; then
        echo 'File composer.lock tidak ditemukan. Menjalankan composer update...'
        cd /var/www/html && composer update --no-interaction
    fi
else
    echo 'File composer.json tidak ditemukan. Ini adalah masalah serius!'
fi
"

# 6. Memeriksa dan memperbaiki file package.json
echo ""
echo ">> Memeriksa dan memperbaiki file package.json..."
docker exec $WEB_CONTAINER bash -c "
if [ -f /var/www/html/package.json ]; then
    echo 'File package.json ditemukan.'
    
    # Periksa apakah node_modules ada
    if [ ! -d /var/www/html/node_modules ]; then
        echo 'Direktori node_modules tidak ditemukan. Menjalankan npm install...'
        if command -v npm >/dev/null 2>&1; then
            cd /var/www/html && npm install
        else
            echo 'npm tidak tersedia di container.'
        fi
    fi
else
    echo 'File package.json tidak ditemukan. Ini mungkin bukan masalah jika Anda tidak menggunakan npm.'
fi
"

# 7. Me-restart container
echo ""
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

# 8. Periksa log untuk error
echo ""
echo ">> Memeriksa log untuk error..."
sleep 5  # Tunggu beberapa detik agar log terupdate
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
echo "====================================================="
echo "      RESET KONFIGURASI SELESAI                      "
echo "====================================================="
echo ""
echo "Semua konfigurasi telah diatur ulang dari nol."
echo "Jika masih mengalami error 502 Bad Gateway, silakan periksa:"
echo "1. Log Docker dengan perintah: docker logs $WEB_CONTAINER"
echo "2. Log error web server di dalam container"
echo "3. Log error PHP-FPM di dalam container"
echo "4. Pastikan port dan socket PHP-FPM dikonfigurasi dengan benar"
echo "====================================================="
