#!/bin/bash
set -e

echo "Setting up staff user for Filament..."

# Add role column to users table
echo "Step 1: Adding role column to users table..."
php artisan app:add-role-to-users

# Create staff user
echo "Step 2: Creating staff user..."
php artisan app:create-staff-user

echo "Done! You can now log in to Filament with:"
echo "Email: hartonomotor1979@user.com"
echo "Password: hmbengkel1979user"
