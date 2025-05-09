#!/bin/bash

# Script to restart the web server in Docker environment

echo "=== Restarting Web Server ==="

# Stop all running containers
echo "Stopping all running containers..."
docker-compose down

# Start the containers again
echo "Starting containers..."
docker-compose up -d

# Clear caches
echo "Clearing caches..."
docker-compose exec app php artisan optimize:clear

# Run the fix-livewire-issue.php script
echo "Running fix-livewire-issue.php..."
docker-compose exec app php fix-livewire-issue.php

echo "=== Web Server Restarted ==="
echo "Please clear your browser cache and try again."
