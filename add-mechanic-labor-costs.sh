#!/bin/bash
set -e

echo "Adding mechanic labor costs feature..."

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

echo "Done! Mechanic labor costs feature has been added."
echo "Changes made:"
echo "1. Added ability to set different labor costs for each mechanic"
echo "2. Modified the 'Selesai' form to include labor cost inputs for each mechanic"
echo "3. Updated the logic to save individual labor costs for each mechanic"
echo "4. Added detailed logging for better debugging"
