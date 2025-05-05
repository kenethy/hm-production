#!/bin/bash
set -e

echo "Running migrations for mechanic reports feature..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run specific migrations
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_112535_add_labor_cost_to_mechanic_service_table.php
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_112624_create_mechanic_reports_table.php

echo "Done! Migrations for mechanic reports feature have been applied."
