#!/bin/bash
set -e

# Build assets first
echo "Building assets..."
bash ./build-assets.sh

# Save manifest.json for Docker deployment
echo "Saving manifest.json..."
bash ./save-manifest.sh

# Deploy with Docker
echo "Deploying with Docker..."
docker-compose down
docker-compose build app
docker-compose up -d

echo "Deployment completed successfully!"
