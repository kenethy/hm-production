#!/bin/bash
set -e

echo "Adding access control to admin resources..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Run the PHP script directly
docker-compose exec app php add-access-control.php

echo "Done!"
