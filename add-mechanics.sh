#!/bin/bash
set -e

echo "Adding initial mechanics data..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run the PHP script to add mechanics
docker-compose exec app php add-mechanics.php

echo "Done! Initial mechanics data has been added."
