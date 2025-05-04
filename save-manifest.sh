#!/bin/bash
set -e

echo "Saving manifest.json for Docker deployment..."

# Check if manifest.json exists
if [ -f public/build/manifest.json ]; then
    echo "Found manifest.json, copying to manifest.json.host..."
    cp public/build/manifest.json public/build/manifest.json.host
    chmod 777 public/build/manifest.json.host
    echo "Manifest saved successfully!"
else
    echo "Error: manifest.json not found. Please run 'npm run build' first."
    exit 1
fi
