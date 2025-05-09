<?php

// Script to fix Livewire routes issue

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Fixing Livewire Issue ===\n\n";

// Clear all caches
echo "Clearing all caches...\n";
Artisan::call('optimize:clear');
echo Artisan::output();

// Publish Livewire assets
echo "Publishing Livewire assets...\n";
Artisan::call('vendor:publish', [
    '--force' => true,
    '--tag' => 'livewire:assets',
]);
echo Artisan::output();

// List routes to verify Livewire routes
echo "Listing Livewire routes...\n";
Artisan::call('route:list', [
    '--name' => 'livewire',
]);
echo Artisan::output();

// Check if the FixLivewireRoutes middleware exists
$middlewarePath = app_path('Http/Middleware/FixLivewireRoutes.php');
if (file_exists($middlewarePath)) {
    echo "FixLivewireRoutes middleware exists.\n";
} else {
    echo "FixLivewireRoutes middleware does not exist. Please create it.\n";
}

// Check if the middleware is registered in Kernel.php
$kernelPath = app_path('Http/Kernel.php');
$kernelContent = file_get_contents($kernelPath);
if (strpos($kernelContent, 'FixLivewireRoutes') !== false) {
    echo "FixLivewireRoutes middleware is registered in Kernel.php.\n";
} else {
    echo "FixLivewireRoutes middleware is not registered in Kernel.php. Please register it.\n";
}

// Check if Livewire routes are excluded from CSRF verification
$verifyCsrfTokenPath = app_path('Http/Middleware/VerifyCsrfToken.php');
$verifyCsrfTokenContent = file_get_contents($verifyCsrfTokenPath);
if (strpos($verifyCsrfTokenContent, 'livewire/*') !== false) {
    echo "Livewire routes are excluded from CSRF verification.\n";
} else {
    echo "Livewire routes are not excluded from CSRF verification. Please exclude them.\n";
}

echo "\n=== Fix Complete ===\n";
echo "Please restart your web server and clear your browser cache.\n";
