#!/bin/bash
set -e

echo "Implementing automatic customer identification via phone number..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Clear cache
echo "Clearing cache..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan filament:clear-cache

echo "Done! Automatic customer identification has been implemented."
echo "Changes made:"
echo "1. Added reactivity to phone number field to automatically identify customers"
echo "2. Enhanced beforeSave method to handle customer and vehicle creation"
echo "3. Added informative notifications for users"
echo ""
echo "Now when a phone number is entered:"
echo "- If the phone number exists, customer data will be automatically filled"
echo "- If the phone number is new, a new customer will be created"
echo "- Vehicles will be automatically created and associated with customers"
