#!/bin/bash
set -e

# Configure Git to trust the directory
echo "Configuring Git to trust /var/www/html..."
git config --global --add safe.directory /var/www/html

# Run fix-permissions script
echo "Running fix-permissions script..."
bash /usr/local/bin/fix-permissions.sh

# Install Composer dependencies if vendor directory is empty
if [ ! "$(ls -A /var/www/html/vendor)" ]; then
    echo "Vendor directory is empty, installing Composer dependencies..."
    cd /var/www/html && composer install --no-interaction --no-progress

    # Run fix-permissions again after composer install
    bash /usr/local/bin/fix-permissions.sh
fi

# Copy .env.docker to .env
echo "Copying .env.docker to .env..."
cp /var/www/html/.env.docker /var/www/html/.env

# Install Node.js dependencies and build assets
if [ -f /var/www/html/package.json ]; then
    echo "Installing Node.js dependencies and building assets..."
    cd /var/www/html
    if [ ! -d /var/www/html/node_modules ]; then
        npm install
    fi

    # Check if manifest.json exists in public/build
    if [ ! -f /var/www/html/public/build/manifest.json ]; then
        echo "Manifest.json not found, running npm run build..."
        npm run build
    else
        echo "Manifest.json found, skipping npm run build..."
    fi
fi

# Clear cache
echo "Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Create storage link
echo "Creating storage link..."
if [ -L /var/www/html/public/storage ]; then
    echo "Removing existing storage link..."
    rm /var/www/html/public/storage
fi
php artisan storage:link || echo "Storage link already exists or could not be created."

# Fix permissions for PHP-FPM
echo "Setting permissions for PHP-FPM..."
chmod 777 /proc/self/fd/2

# Execute the original command as root
echo "Executing command as root..."
exec "$@"
