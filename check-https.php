<?php

// Load Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

// Check environment settings
echo "Checking environment settings...\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "ASSET_URL: " . config('app.asset_url', 'Not set') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "HTTPS Forced: " . (config('app.env') !== 'local' ? 'Yes' : 'No') . "\n";

// Check URL generation
echo "\nChecking URL generation...\n";
echo "url('/'): " . url('/') . "\n";
echo "asset('css/app.css'): " . asset('css/app.css') . "\n";
echo "secure_url('/'): " . secure_url('/') . "\n";

// Check Vite configuration
echo "\nChecking Vite configuration...\n";
try {
    $vite = app(\Illuminate\Foundation\Vite::class);
    echo "Vite class found: " . get_class($vite) . "\n";
    
    // Try to generate a Vite asset URL
    $url = $vite->asset('resources/css/app.css');
    echo "Vite asset URL for resources/css/app.css: $url\n";
    
    echo "Vite configuration looks good!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check if ForceHttps middleware is registered
echo "\nChecking ForceHttps middleware...\n";
$kernel = app(\Illuminate\Contracts\Http\Kernel::class);
$middlewareGroups = $kernel->getMiddlewareGroups();
$webMiddleware = $middlewareGroups['web'] ?? [];

$forceHttpsFound = false;
foreach ($webMiddleware as $middleware) {
    if (is_string($middleware) && strpos($middleware, 'ForceHttps') !== false) {
        $forceHttpsFound = true;
        echo "ForceHttps middleware is registered: $middleware\n";
        break;
    }
}

if (!$forceHttpsFound) {
    echo "ForceHttps middleware is NOT registered in the web middleware group.\n";
}

echo "\nDone!\n";
