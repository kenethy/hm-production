#!/bin/bash
set -e

echo "Creating admin user for Filament..."
docker-compose exec app php artisan app:create-admin-user

echo "Done! You can now log in to Filament with:"
echo "Email: hartonomotor1979@gmail.com"
echo "Password: hmbengkel1979"
