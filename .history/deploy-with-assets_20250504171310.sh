#!/bin/bash
set -e

# Build assets first
echo "Building assets..."
bash ./build-assets.sh

# Deploy with Docker
echo "Deploying with Docker..."
docker-compose down
docker-compose build app
docker-compose up -d

echo "Deployment completed successfully!"
