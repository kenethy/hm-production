#!/bin/bash
set -e

echo "Fixing view compilation permissions..."

# Find the correct container name
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Cannot find PHP/Laravel container. Please check running containers with 'docker ps'."
    exit 1
fi

echo "Using container: $CONTAINER_NAME"

# Fix permissions for storage directory
echo "Setting permissions for storage directory..."
docker exec $CONTAINER_NAME bash -c "
    # Ensure directories exist
    mkdir -p /var/www/html/storage/framework/views
    
    # Set permissions - make everything writable
    chmod -R 777 /var/www/html/storage
    
    # Clear view cache
    php artisan view:clear
"

echo "Permissions fixed successfully!"
echo "Now try accessing the page again."
