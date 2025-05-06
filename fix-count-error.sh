#!/bin/bash
set -e

echo "Fixing count() error in service completion..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Clear cache
echo "Clearing cache..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan filament:clear-cache

echo "Done! Count() error in service completion has been fixed."
echo "Changes made:"
echo "1. Added null check before calling count() function"
echo "2. Fixed the issue with the 'Selesai' button in the services list"
