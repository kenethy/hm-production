#!/bin/bash
set -e

echo "Fixing mechanic labor cost in service completion form..."

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

echo "Done! Mechanic labor cost in service completion form has been fixed."
echo "Changes made:"
echo "1. Improved default labor cost values (minimum 50,000 if not set)"
echo "2. Enhanced labor cost initialization for existing mechanics"
echo "3. Fixed labor cost calculation and storage"
echo "4. Added better logging for debugging"
echo ""
echo "Now when you click the 'Selesai' button, the mechanic costs will be properly displayed"
echo "with appropriate default values, and the values will be correctly saved to the database."
