#!/bin/bash

# Script to fix file upload issues in Docker environment

echo "Starting file upload fix script for Docker environment..."

# 1. Update session configuration
echo "Updating session configuration..."
sed -i 's/SESSION_SECURE_COOKIE=false/SESSION_SECURE_COOKIE=true/g' .env

# 2. Ensure storage directory has proper permissions
echo "Setting proper permissions for storage directory..."
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# 3. Recreate storage link
echo "Recreating storage link..."
if [ -L public/storage ]; then
    echo "Removing existing storage link..."
    rm public/storage
fi
php artisan storage:link

# 4. Create gallery directory if it doesn't exist
echo "Creating gallery directory..."
mkdir -p storage/app/public/gallery
chmod -R 777 storage/app/public/gallery
chown -R www-data:www-data storage/app/public/gallery

# 5. Clear all caches
echo "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# 6. Check if Livewire temporary upload directory exists and has proper permissions
echo "Setting up Livewire temporary upload directory..."
mkdir -p storage/app/livewire-tmp
chmod -R 777 storage/app/livewire-tmp
chown -R www-data:www-data storage/app/livewire-tmp

echo "File upload fix completed!"
