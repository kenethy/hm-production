<?php

// Script to diagnose file upload issues

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Start the session
$session = $app->make('session');
$session->start();

echo "=== File Upload Diagnostics ===\n\n";

// Check environment
echo "Environment: " . app()->environment() . "\n";
echo "Debug Mode: " . (config('app.debug') ? 'Enabled' : 'Disabled') . "\n";
echo "App URL: " . config('app.url') . "\n\n";

// Check filesystem configuration
echo "=== Filesystem Configuration ===\n";
echo "Default Disk: " . config('filesystems.default') . "\n";
echo "Public Disk URL: " . config('filesystems.disks.public.url') . "\n";
echo "Public Disk Root: " . config('filesystems.disks.public.root') . "\n\n";

// Check storage link
echo "=== Storage Link ===\n";
$publicStoragePath = public_path('storage');
$storageAppPublicPath = storage_path('app/public');

echo "Public Storage Path: " . $publicStoragePath . "\n";
echo "Storage App Public Path: " . $storageAppPublicPath . "\n";
echo "Storage Link Exists: " . (file_exists($publicStoragePath) && is_link($publicStoragePath) ? 'Yes' : 'No') . "\n";

if (file_exists($publicStoragePath) && is_link($publicStoragePath)) {
    echo "Storage Link Target: " . readlink($publicStoragePath) . "\n";
    echo "Storage Link Valid: " . (readlink($publicStoragePath) == $storageAppPublicPath ? 'Yes' : 'No') . "\n";
}
echo "\n";

// Check directory permissions
echo "=== Directory Permissions ===\n";
$directories = [
    'storage' => storage_path(),
    'storage/app' => storage_path('app'),
    'storage/app/public' => storage_path('app/public'),
    'storage/app/public/gallery' => storage_path('app/public/gallery'),
    'bootstrap/cache' => base_path('bootstrap/cache'),
    'public/storage' => public_path('storage'),
];

foreach ($directories as $name => $path) {
    $exists = file_exists($path);
    $writable = $exists && is_writable($path);
    $permissions = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';

    echo "$name:\n";
    echo "  Path: $path\n";
    echo "  Exists: " . ($exists ? 'Yes' : 'No') . "\n";
    echo "  Writable: " . ($writable ? 'Yes' : 'No') . "\n";
    echo "  Permissions: $permissions\n";

    if (!$exists) {
        echo "  Creating directory...\n";
        try {
            mkdir($path, 0777, true);
            echo "  Directory created with permissions 0777\n";
        } catch (Exception $e) {
            echo "  Error creating directory: " . $e->getMessage() . "\n";
        }
    } elseif (!$writable) {
        echo "  Setting writable permissions...\n";
        try {
            chmod($path, 0777);
            echo "  Permissions set to 0777\n";
        } catch (Exception $e) {
            echo "  Error setting permissions: " . $e->getMessage() . "\n";
        }
    }

    echo "\n";
}

// Check session configuration
echo "=== Session Configuration ===\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Lifetime: " . config('session.lifetime') . " minutes\n";
echo "Session Secure Cookie: " . (config('session.secure') ? 'Yes' : 'No') . "\n";
echo "Session Same Site: " . config('session.same_site') . "\n\n";

// Check CSRF configuration
echo "=== CSRF Configuration ===\n";
echo "CSRF Token: " . ($session->token() ? 'Available' : 'Not Available') . "\n";
echo "CSRF Except URLs: " . implode(', ', app('Illuminate\Foundation\Http\Middleware\VerifyCsrfToken')->except) . "\n\n";

echo "=== Diagnostics Complete ===\n";
