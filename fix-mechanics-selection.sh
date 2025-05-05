#!/bin/bash
set -e

echo "Fixing mechanics selection in service completion..."

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

echo "Done! Mechanics selection in service completion has been fixed."
echo "Changes made:"
echo "1. Improved validation for mechanics selection"
echo "2. Added support for using existing mechanics if none are selected"
echo "3. Added detailed logging for better debugging"
echo "4. Fixed the issue with the 'Selesai' button in the services list"
