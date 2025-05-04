#!/bin/bash
set -e

# Configure Git to trust the directory
echo "Configuring Git to trust /var/www/html..."
git config --global --add safe.directory /var/www/html

# Ensure vendor directory exists with proper permissions
echo "Ensuring vendor directory exists with proper permissions..."
mkdir -p /var/www/html/vendor
chmod -R 777 /var/www/html/vendor

# Install Composer dependencies if vendor directory is empty
if [ ! "$(ls -A /var/www/html/vendor)" ]; then
    echo "Vendor directory is empty, installing Composer dependencies..."
    cd /var/www/html && composer install --no-interaction --no-progress
fi

# Ensure all Laravel storage directories exist with proper permissions
echo "Setting up Laravel storage directories with proper permissions..."
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Set permissions for storage and bootstrap/cache directories
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Copy .env.docker to .env
echo "Copying .env.docker to .env..."
cp /var/www/html/.env.docker /var/www/html/.env

# Clear cache
echo "Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Create storage link
echo "Creating storage link..."
if [ -L /var/www/html/public/storage ]; then
    echo "Removing existing storage link..."
    rm /var/www/html/public/storage
fi
php artisan storage:link || echo "Storage link already exists or could not be created."

# Fix permissions for PHP-FPM
echo "Setting permissions for PHP-FPM..."
chmod 777 /proc/self/fd/2

# Switch to www user and execute the original command
echo "Switching to www user..."
exec "$@"
