#!/bin/bash
set -e

echo "Running migrations for mechanics feature..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run migrations
docker-compose exec app php artisan migrate

echo "Done! Migrations have been applied."
