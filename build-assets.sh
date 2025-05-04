#!/bin/bash
set -e

echo "Building assets for production..."

# Install Node.js dependencies
if [ ! -d "node_modules" ]; then
    echo "Installing Node.js dependencies..."
    npm install
fi

# Build assets
echo "Building Vite assets..."
npm run build

# Create public/build directory if it doesn't exist
mkdir -p public/build

# Ensure proper permissions
chmod -R 777 public/build

echo "Assets built successfully!"
