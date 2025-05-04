#!/bin/bash

# Pastikan script berhenti jika ada error
set -e

# Variabel
REMOTE_USER="root"
REMOTE_HOST="your_vps_ip"
REMOTE_DIR="/var/www/hartono-motor"
REMOTE_ENV_FILE="$REMOTE_DIR/.env"

# Pesan
echo "Memulai deployment ke $REMOTE_HOST..."

# Buat direktori remote jika belum ada
ssh $REMOTE_USER@$REMOTE_HOST "mkdir -p $REMOTE_DIR"

# Copy file-file yang diperlukan
echo "Menyalin file ke server..."
scp -r ./* $REMOTE_USER@$REMOTE_HOST:$REMOTE_DIR/
scp .env.production $REMOTE_USER@$REMOTE_HOST:$REMOTE_ENV_FILE
scp .dockerignore $REMOTE_USER@$REMOTE_HOST:$REMOTE_DIR/

# Jalankan perintah di server
echo "Menjalankan perintah di server..."
ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_DIR && \
    docker-compose down && \
    docker-compose build --no-cache && \
    docker-compose up -d && \
    docker-compose exec -T app composer install --optimize-autoloader --no-dev && \
    docker-compose exec -T app php artisan key:generate && \
    docker-compose exec -T app php artisan config:cache && \
    docker-compose exec -T app php artisan route:cache && \
    docker-compose exec -T app php artisan view:cache && \
    docker-compose exec -T app php artisan migrate --force && \
    docker-compose exec -T app php artisan storage:link && \
    docker-compose exec -T app chown -R www:www storage bootstrap/cache"

echo "Deployment selesai!"
