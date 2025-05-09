#!/bin/bash
set -e

echo "Clearing application cache..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Clear all caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan livewire:discover

# Optimize the application
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan optimize

echo "Done! Application cache has been cleared and optimized."
