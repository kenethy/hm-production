#!/bin/bash

# Script to use Docker-specific environment variables

# Copy .env.docker to .env
cp /var/www/html/.env.docker /var/www/html/.env

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Docker environment variables applied successfully!"
