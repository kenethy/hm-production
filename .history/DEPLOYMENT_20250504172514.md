# Deployment Instructions

## Problem: Vite manifest not found

If you encounter the error "Vite manifest not found at: /var/www/html/public/build/manifest.json", it means that the Vite build process has not been run or the manifest file is not accessible to the application.

## Solution

### Local Development

1. Run `npm install` to install Node.js dependencies
2. Run `npm run build` to generate the Vite manifest file
3. Run `php artisan serve` to start the development server

### Docker Development

1. Run `npm install` and `npm run build` to generate the Vite manifest file
2. Run `bash ./save-manifest.sh` to save the manifest file for Docker
3. Run `docker-compose up -d` to start the Docker containers

### VPS Deployment

Use the provided deployment script:

```bash
bash ./deploy-vps.sh
```

This script will:
1. Pull the latest changes from Git
2. Install Node.js dependencies
3. Build the assets using Vite
4. Save the manifest file for Docker
5. Deploy the application using Docker

## Troubleshooting

If you still encounter issues with the Vite manifest, try the following:

1. Check if the manifest file exists:
```bash
bash ./check-manifest.sh
```

2. Check if Vite is configured correctly:
```bash
php check-vite.php
```

3. Manually build the assets and save the manifest:
```bash
npm run build
bash ./save-manifest.sh
```

4. Restart the Docker containers:
```bash
docker-compose down
docker-compose up -d
```

## Important Notes

- Always run `npm run build` before deploying to VPS
- The manifest file must be generated on the same architecture as the VPS
- If you make changes to CSS or JavaScript files, you need to rebuild the assets
