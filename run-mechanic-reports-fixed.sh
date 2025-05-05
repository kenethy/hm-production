#!/bin/bash
set -e

echo "Running fixed mechanic reports generation..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Clear cache first
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Run the command to generate mechanic reports
docker-compose exec app php artisan app:generate-mechanic-reports

echo "Done! Mechanic reports have been generated with the fixed implementation."
