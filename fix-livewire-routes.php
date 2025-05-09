<?php

// Script to fix Livewire routes issue

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Fixing Livewire Routes ===\n\n";

// Clear route cache
echo "Clearing route cache...\n";
Artisan::call('route:clear');
echo Artisan::output();

// Clear config cache
echo "Clearing config cache...\n";
Artisan::call('config:clear');
echo Artisan::output();

// Clear view cache
echo "Clearing view cache...\n";
Artisan::call('view:clear');
echo Artisan::output();

// Clear application cache
echo "Clearing application cache...\n";
Artisan::call('cache:clear');
echo Artisan::output();

// Create Livewire temporary upload directory
echo "Creating Livewire temporary upload directory...\n";
$livewireTmpDir = storage_path('app/livewire-tmp');
if (!file_exists($livewireTmpDir)) {
    mkdir($livewireTmpDir, 0777, true);
    echo "Directory created: $livewireTmpDir\n";
} else {
    chmod($livewireTmpDir, 0777);
    echo "Directory permissions updated: $livewireTmpDir\n";
}

// Check if storage link exists
echo "Checking storage link...\n";
$publicStoragePath = public_path('storage');
if (!file_exists($publicStoragePath)) {
    echo "Creating storage link...\n";
    Artisan::call('storage:link');
    echo Artisan::output();
} else {
    echo "Storage link already exists.\n";
}

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

echo "\n=== Fix Complete ===\n";
echo "Please restart your web server and clear your browser cache.\n";
