#!/bin/bash
set -e

echo "Fixing mechanics validation and labor cost calculation..."

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

echo "Done! Mechanics validation and labor cost calculation have been fixed."
echo "Changes made:"
echo "1. Fixed mechanics validation when marking a service as completed"
echo "2. Changed labor cost calculation - each mechanic now gets the full labor cost"
echo "3. Added better error messages for validation failures"
