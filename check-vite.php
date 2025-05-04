<?php

// Load Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

// Check if manifest.json exists
$manifestPath = public_path('build/manifest.json');
echo "Checking Vite manifest.json at: $manifestPath\n";

if (file_exists($manifestPath)) {
    echo "Manifest.json found!\n";
    echo "Content of manifest.json:\n";
    echo file_get_contents($manifestPath) . "\n";
} else {
    echo "Error: manifest.json not found.\n";
    echo "Please run 'npm run build' to generate the manifest file.\n";
}

// Check if Vite is configured correctly
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
