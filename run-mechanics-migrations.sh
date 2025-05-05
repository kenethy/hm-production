#!/bin/bash
set -e

echo "Running migrations for mechanics feature..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run specific migrations for mechanics
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_104827_create_mechanics_table.php
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_104839_create_mechanic_service_table.php

echo "Done! Migrations for mechanics feature have been applied."
