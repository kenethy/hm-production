#!/bin/bash
set -e

echo "Implementing automatic vehicle creation..."

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

echo "Done! Automatic vehicle creation has been implemented."
echo "Changes made:"
echo "1. Added findOrCreateByPhoneAndPlate method to Vehicle model"
echo "2. Modified ServiceResource to automatically create vehicles based on phone and license plate"
echo "3. Ensured vehicles are displayed in customer details"
echo ""
echo "Now when a service is created with a new license plate for an existing phone number,"
echo "a new vehicle will be automatically created and associated with the customer."
