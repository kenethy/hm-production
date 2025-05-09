#!/bin/bash

# Comprehensive script to fix all issues in Docker environment

echo "=== Starting comprehensive fix script ==="

# 1. Update session configuration
echo "Updating session configuration..."
sed -i 's/SESSION_SECURE_COOKIE=false/SESSION_SECURE_COOKIE=true/g' .env

# 2. Ensure storage directory has proper permissions
echo "Setting proper permissions for storage directory..."
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# 3. Create Livewire temporary upload directory
echo "Creating Livewire temporary upload directory..."
mkdir -p storage/app/livewire-tmp
chmod -R 777 storage/app/livewire-tmp
chown -R www-data:www-data storage/app/livewire-tmp

# 4. Create gallery directory
echo "Creating gallery directory..."
mkdir -p storage/app/public/gallery
chmod -R 777 storage/app/public/gallery
chown -R www-data:www-data storage/app/public/gallery

# 5. Recreate storage link
echo "Recreating storage link..."
if [ -L public/storage ]; then
    echo "Removing existing storage link..."
    rm public/storage
fi
php artisan storage:link

# 6. Clear all caches
echo "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# 7. Publish Livewire assets
echo "Publishing Livewire assets..."
php artisan vendor:publish --force --tag=livewire:assets

# 8. Fix file permissions for public directory
echo "Setting permissions for public directory..."
chmod -R 755 public
chown -R www-data:www-data public

# 9. Restart PHP-FPM (if available)
if command -v service >/dev/null 2>&1; then
    echo "Restarting PHP-FPM..."
    service php8.2-fpm restart || echo "PHP-FPM restart failed or not available"
fi

# 10. Check if we need to restart the web server
if command -v service >/dev/null 2>&1; then
    echo "Restarting Nginx..."
    service nginx restart || echo "Nginx restart failed or not available"
fi

echo "=== Fix complete ==="
echo "Please clear your browser cache and try uploading files again."
