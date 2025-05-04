#!/bin/bash
set -e

echo "Fixing permissions for Laravel application..."

# Ensure directories exist
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/vendor

# Set permissions - make everything writable
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/vendor

# If running as root, change ownership to www-data (standard web server user)
if [ "$(id -u)" = "0" ]; then
    echo "Running as root, changing ownership to www-data..."
    chown -R www-data:www-data /var/www/html/storage
    chown -R www-data:www-data /var/www/html/bootstrap/cache
    chown -R www-data:www-data /var/www/html/vendor
fi

echo "Permissions fixed successfully!"
