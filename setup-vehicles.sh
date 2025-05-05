#!/bin/bash
set -e

echo "Setting up vehicles feature..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run migrations
echo "Running migrations..."
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_115130_create_vehicles_table.php
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_115343_add_vehicle_id_to_services_table.php

# Clear cache
echo "Clearing cache..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan filament:clear-cache

echo "Done! Vehicles feature has been set up successfully."
echo "You can now access the vehicles feature in the admin panel."
echo "- Admin can manage vehicles in the 'Kendaraan' menu in the 'Manajemen Pelanggan' group"
echo "- Admin can see vehicles owned by a customer in the customer detail page"
echo "- When creating a service, admin can select a vehicle from the customer"
