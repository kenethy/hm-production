#!/bin/bash
set -e

echo "Fixing getRawState() error in service completion..."

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

echo "Done! getRawState() error in service completion has been fixed."
echo "Changes made:"
echo "1. Fixed itemLabel function to properly check if mechanic_id exists"
echo "2. Added type checking in afterStateUpdated callbacks"
echo "3. Removed unused parameters from callback functions"
echo "4. Removed unused imports"
