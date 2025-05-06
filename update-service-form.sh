#!/bin/bash
set -e

echo "Updating service form..."

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

echo "Done! Service form has been updated."
echo "Changes made:"
echo "1. Removed fields: biaya jasa total, biaya sparepart, and sparepart yang digunakan"
echo "2. Added optional mechanic selection with individual labor costs"
echo "3. Updated total cost calculation based on mechanic labor costs"
