#!/bin/bash
set -e

echo "Running migration for time tracking feature..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run specific migration for time tracking
docker-compose exec app php artisan migrate --path=database/migrations/2025_05_05_111911_add_time_tracking_to_services_table.php

echo "Done! Migration for time tracking feature has been applied."
