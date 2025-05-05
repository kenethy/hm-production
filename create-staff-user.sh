#!/bin/bash
set -e

echo "Creating staff user for Filament..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run the PHP script directly
docker-compose exec app php create-staff-user.php

echo "Done!"
