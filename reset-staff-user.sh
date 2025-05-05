#!/bin/bash
set -e

echo "Resetting staff user for Filament..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Create staff user
echo "Creating staff user..."
docker-compose exec app php artisan app:create-staff-user

echo "Done! You can now log in to Filament with:"
echo "Email: hartonomotor1979@user.com"
echo "Password: hmbengkel1979user"
echo ""
echo "This user now has limited access to only Services and Bookings resources."
