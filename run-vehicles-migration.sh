#!/bin/bash
set -e

echo "Running migrations for vehicles feature..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run specific migrations
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_115130_create_vehicles_table.php
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_115343_add_vehicle_id_to_services_table.php

echo "Done! Migrations for vehicles feature have been applied."
