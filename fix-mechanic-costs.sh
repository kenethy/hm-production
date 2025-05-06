#!/bin/bash
set -e

echo "Fixing mechanic costs in service completion form..."

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

echo "Done! Mechanic costs in service completion form has been fixed."
echo "Changes made:"
echo "1. Improved initialization of mechanic_costs for existing mechanics"
echo "2. Added default mechanic_costs to the form"
echo "3. Enhanced afterStateUpdated to preserve existing labor costs"
echo ""
echo "Now when you click the 'Selesai' button, the mechanic costs will be properly displayed"
echo "for mechanics that were previously selected, without needing to delete and re-add them."
