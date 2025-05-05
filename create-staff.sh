#!/bin/bash
set -e

echo "Creating staff user for Filament..."
php artisan app:create-staff-user

echo "Done! You can now log in to Filament with:"
echo "Email: hartonomotor1979@user.com"
echo "Password: hmbengkel1979user"
