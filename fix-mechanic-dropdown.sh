#!/bin/bash
set -e

echo "Fixing mechanic dropdown in service completion..."

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

echo "Done! Mechanic dropdown in service completion has been fixed."
echo "Changes made:"
echo "1. Fixed the way mechanic costs are initialized when mechanics are selected"
echo "2. Changed the structure of mechanic_costs data to include mechanic_id"
echo "3. Made mechanic_id field dehydrated and required"
echo "4. Updated the processing of mechanic costs data"
