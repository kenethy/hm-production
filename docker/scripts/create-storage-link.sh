#!/bin/bash

# Remove existing storage link if it exists
if [ -L /var/www/html/public/storage ]; then
    echo "Removing existing storage link..."
    rm /var/www/html/public/storage
fi

# Create storage link
echo "Creating storage link..."
cd /var/www/html && php artisan storage:link || echo "Storage link already exists or could not be created."

echo "Storage link setup completed!"
