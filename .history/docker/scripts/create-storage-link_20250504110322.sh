#!/bin/bash

# Remove existing storage link if it exists
if [ -L /var/www/html/public/storage ]; then
    rm /var/www/html/public/storage
fi

# Create storage link
cd /var/www/html && php artisan storage:link

echo "Storage link created successfully!"
