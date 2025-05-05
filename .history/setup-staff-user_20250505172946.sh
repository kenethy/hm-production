#!/bin/bash
set -e

echo "Setting up staff user for Filament..."

# Check if docker-compose is running
if ! docker-compose ps | grep -q "app.*Up"; then
    echo "Error: Docker containers are not running. Please start them with 'docker-compose up -d' first."
    exit 1
fi

# Add role column to users table
echo "Step 1: Adding role column to users table..."
docker-compose exec app php artisan app:add-role-to-users

# Create staff user
echo "Step 2: Creating staff user..."
docker-compose exec app php artisan app:create-staff-user

echo "Done! You can now log in to Filament with:"
echo "Email: hartonomotor1979@user.com"
echo "Password: hmbengkel1979user"
