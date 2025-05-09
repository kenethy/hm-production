#!/bin/bash

# Script to fix file upload issues in Filament

echo "Starting file upload fix script..."

# 1. Ensure storage directory has proper permissions
echo "Setting proper permissions for storage directory..."
chmod -R 777 storage
chmod -R 777 bootstrap/cache

# 2. Recreate storage link
echo "Recreating storage link..."
if [ -L public/storage ]; then
    echo "Removing existing storage link..."
    rm public/storage
fi
php artisan storage:link

# 3. Clear all caches
echo "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Restart the application
echo "Restarting the application..."
php artisan down
php artisan up

echo "File upload fix completed!"
