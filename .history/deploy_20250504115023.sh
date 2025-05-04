#!/bin/bash

# Pastikan script berhenti jika ada error
set -e

# Variabel
APP_DIR="/var/www/hartono-motor"
DOMAIN="hartonomotor.xyz"
EMAIL="admin@hartonomotor.xyz"

# Pesan
echo "Memulai deployment Hartono Motor ke VPS..."

# Buat direktori SSL options jika belum ada
mkdir -p docker/certbot/conf

# Download file konfigurasi SSL dari Let's Encrypt jika belum ada
if [ ! -f docker/certbot/conf/options-ssl-nginx.conf ]; then
    echo "Downloading options-ssl-nginx.conf..."
    curl -s https://raw.githubusercontent.com/certbot/certbot/master/certbot-nginx/certbot_nginx/_internal/tls_configs/options-ssl-nginx.conf > docker/certbot/conf/options-ssl-nginx.conf
fi

if [ ! -f docker/certbot/conf/ssl-dhparams.pem ]; then
    echo "Downloading ssl-dhparams.pem..."
    curl -s https://raw.githubusercontent.com/certbot/certbot/master/certbot/certbot/ssl-dhparams.pem > docker/certbot/conf/ssl-dhparams.pem
fi

# Jalankan container untuk pertama kali dengan konfigurasi HTTP
echo "Starting containers with HTTP configuration..."
docker-compose up -d webserver

# Tunggu beberapa detik
echo "Waiting for webserver to start..."
sleep 5

# Jalankan certbot untuk mendapatkan sertifikat SSL
echo "Obtaining SSL certificate..."
docker-compose up --force-recreate -d certbot

# Tunggu beberapa detik
echo "Waiting for certbot to complete..."
sleep 30

# Restart webserver untuk menggunakan sertifikat SSL
echo "Restarting webserver with SSL configuration..."
docker-compose restart webserver

# Pastikan script create-storage-link.sh dapat dieksekusi
echo "Making create-storage-link.sh executable..."
chmod +x docker/scripts/create-storage-link.sh

# Jalankan container aplikasi
echo "Starting application container..."
docker-compose up -d app

# Jalankan container database
echo "Starting database container..."
docker-compose up -d db

# Tunggu database siap
echo "Waiting for database to be ready..."
sleep 10

# Jalankan container phpmyadmin
echo "Starting phpMyAdmin container..."
docker-compose up -d phpmyadmin

# Jalankan migrasi database
echo "Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Jalankan perintah lain yang diperlukan
echo "Running additional commands..."
docker-compose exec -T app php artisan key:generate --force
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache
docker-compose exec -T app php artisan storage:link

# Set permissions (skip if running on Windows)
echo "Setting permissions..."
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" && "$OSTYPE" != "cygwin" ]]; then
    docker-compose exec -T app chown -R www:www storage bootstrap/cache
else
    echo "Skipping permission changes on Windows..."
fi

echo "Deployment selesai!"
echo "Aplikasi dapat diakses di https://$DOMAIN"
echo "PhpMyAdmin dapat diakses di http://$DOMAIN:8080"
