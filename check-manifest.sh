#!/bin/bash
set -e

echo "Checking Vite manifest.json..."

# Check if manifest.json exists in public/build
if [ -f public/build/manifest.json ]; then
    echo "Manifest.json found in public/build!"
    echo "Content of manifest.json:"
    cat public/build/manifest.json
else
    echo "Error: manifest.json not found in public/build."
    echo "Please run 'npm run build' to generate the manifest file."
    exit 1
fi

# Check if manifest.json.host exists in public/build
if [ -f public/build/manifest.json.host ]; then
    echo "Manifest.json.host found in public/build!"
    echo "Content of manifest.json.host:"
    cat public/build/manifest.json.host
else
    echo "Warning: manifest.json.host not found in public/build."
    echo "Please run 'bash ./save-manifest.sh' to save the manifest file for Docker."
fi
