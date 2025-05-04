#!/bin/bash
set -e

echo "Deploying to VPS..."

# Pull latest changes
echo "Pulling latest changes..."
git pull

# Install Node.js dependencies
echo "Installing Node.js dependencies..."
npm install

# Build assets
echo "Building assets..."
npm run build

# Save manifest.json for Docker deployment
echo "Saving manifest.json..."
bash ./save-manifest.sh

# Deploy with Docker
echo "Deploying with Docker..."
docker-compose down
docker-compose build app
docker-compose up -d

echo "Deployment completed successfully!"
