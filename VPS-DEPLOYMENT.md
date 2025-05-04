# VPS Deployment Instructions

## Prerequisites

1. Node.js and npm installed on your VPS
2. Docker and Docker Compose installed on your VPS
3. Git installed on your VPS

## Deployment Steps

### First-time Deployment

1. Clone the repository on your VPS:
```bash
git clone <repository-url> /path/to/application
cd /path/to/application
```

2. Install Node.js dependencies:
```bash
npm install
```

3. Build the assets:
```bash
npm run build
```

4. Save the manifest file for Docker:
```bash
bash ./save-manifest.sh
```

5. Deploy with Docker:
```bash
docker-compose up -d
```

### Subsequent Deployments

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

## Important Notes

- Always run `npm run build` before deploying to VPS
- The manifest file must be generated on the same architecture as the VPS
- If you make changes to CSS or JavaScript files, you need to rebuild the assets
- If you encounter permission issues, make sure the user running the deployment script has the necessary permissions

## Troubleshooting

If you encounter issues with the Vite manifest, try the following:

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
