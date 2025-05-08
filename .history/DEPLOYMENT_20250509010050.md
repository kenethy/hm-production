# Deployment Instructions

## Initial Deployment

### Problem: Vite manifest not found

If you encounter the error "Vite manifest not found at: /var/www/html/public/build/manifest.json", it means that the Vite build process has not been run or the manifest file is not accessible to the application.

### Solution

#### Local Development

1. Run `npm install` to install Node.js dependencies
2. Run `npm run build` to generate the Vite manifest file
3. Run `php artisan serve` to start the development server

#### Docker Development

1. Run `npm install` and `npm run build` to generate the Vite manifest file
2. Run `bash ./save-manifest.sh` to save the manifest file for Docker
3. Run `docker-compose up -d` to start the Docker containers

#### VPS Deployment

Use the provided deployment script:

```bash
bash ./deploy.sh
```

This script will:
1. Pull the latest changes from Git
2. Install Node.js dependencies
3. Build the assets using Vite
4. Save the manifest file for Docker
5. Deploy the application using Docker

## Updating Existing Application

For updating an existing application with new features or bug fixes, use the following scripts:

### 1. Update Application

To update the application that is already running:

```bash
# Give execution permission
chmod +x update-app.sh

# Run the script
./update-app.sh
```

This script will:
- Pull the latest changes from the repository
- Update Composer dependencies
- Run database migrations
- Clear cache and optimize the application
- Restart the application container
- Verify the implementation

### 2. Verify Implementation

To verify that the event-listener implementation for mechanic report updates is correctly installed:

```bash
# Give execution permission
chmod +x verify-implementation.sh

# Run the script
./verify-implementation.sh
```

This script will check:
- Existence of key files
- Content of key files
- Laravel configuration
- Laravel logs for related errors

### 3. Test Mechanic Reports Feature

To test the mechanic reports feature:

```bash
# Give execution permission
chmod +x test-mechanic-reports.sh

# Run the script
./test-mechanic-reports.sh
```

This script will display testing guidelines for:
- Testing new services
- Testing service editing
- Testing service cancellation
- Checking event logs
- Checking mechanic reports in the database
- Checking mechanic-service relationships in the database

### 4. Rollback to Previous Version

If there are problems after deployment, you can rollback to a previous version:

```bash
# Give execution permission
chmod +x rollback.sh

# Run the script with the commit hash you want to rollback to
./rollback.sh <commit_hash>

# Example
./rollback.sh abc1234
```

This script will:
- Verify the commit hash
- Reset to the specified commit
- Update Composer dependencies
- Run database migrations
- Clear cache and optimize the application
- Restart the application container

## Troubleshooting

### Vite Manifest Issues

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

### Checking Laravel Logs

To check Laravel logs:

```bash
# View the last 100 lines of the Laravel log
docker-compose exec app cat storage/logs/laravel.log | tail -n 100

# Check logs related to ServiceUpdated
docker-compose exec app cat storage/logs/laravel.log | grep -i "ServiceUpdated"

# Check logs related to UpdateMechanicReports
docker-compose exec app cat storage/logs/laravel.log | grep -i "UpdateMechanicReports"
```

### Checking Docker Container Logs

To check Docker container logs:

```bash
# View application container logs
docker-compose logs -f app

# View all container logs
docker-compose logs -f
```

## Important Notes

- Always run `npm run build` before deploying to VPS
- The manifest file must be generated on the same architecture as the VPS
- If you make changes to CSS or JavaScript files, you need to rebuild the assets
- Always backup the database before making major changes
- Always perform manual testing after deployment to ensure features work correctly
- If there are problems, use the rollback.sh script to return to a previous version
- Monitor Laravel logs and Docker containers to detect potential issues
