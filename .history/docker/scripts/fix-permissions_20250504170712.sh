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

# Create node_modules and public/build directories if they don't exist
mkdir -p /var/www/html/node_modules
mkdir -p /var/www/html/public/build

# Set permissions for Node.js directories
chmod -R 777 /var/www/html/node_modules
chmod -R 777 /var/www/html/public/build

echo "Permissions fixed successfully!"
