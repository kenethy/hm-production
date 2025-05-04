#!/bin/bash
set -e

# Fix ownership issues
echo "Setting correct ownership for /var/www/html..."
chown -R www:www /var/www/html

# Configure Git to trust the directory
echo "Configuring Git to trust /var/www/html..."
git config --global --add safe.directory /var/www/html

# Ensure vendor directory exists with proper permissions
echo "Ensuring vendor directory exists with proper permissions..."
mkdir -p /var/www/html/vendor
chown -R www:www /var/www/html/vendor

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

# Switch to www user and execute the original command
echo "Switching to www user..."
exec gosu www "$@"
