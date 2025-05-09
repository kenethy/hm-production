<?php

// Script to check Livewire upload functionality

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Livewire Upload Diagnostics ===\n\n";

// Check Livewire configuration
echo "Checking Livewire configuration...\n";
$livewireConfig = config('livewire');
echo "Livewire Temporary Upload Directory: " . ($livewireConfig['temporary_file_upload']['directory'] ?? 'Not configured') . "\n";
echo "Livewire Upload Middleware: " . implode(', ', $livewireConfig['temporary_file_upload']['middleware'] ?? ['Not configured']) . "\n\n";

// Check temporary upload directory
$tempUploadDir = $livewireConfig['temporary_file_upload']['directory'] ?? 'livewire-tmp';
$fullTempUploadPath = storage_path('app/' . $tempUploadDir);

echo "Checking temporary upload directory...\n";
echo "Path: $fullTempUploadPath\n";
echo "Exists: " . (file_exists($fullTempUploadPath) ? 'Yes' : 'No') . "\n";

if (!file_exists($fullTempUploadPath)) {
    echo "Creating directory...\n";
    try {
        mkdir($fullTempUploadPath, 0777, true);
        echo "Directory created with permissions 0777\n";
    } catch (Exception $e) {
        echo "Error creating directory: " . $e->getMessage() . "\n";
    }
} else {
    $writable = is_writable($fullTempUploadPath);
    $permissions = substr(sprintf('%o', fileperms($fullTempUploadPath)), -4);
    echo "Writable: " . ($writable ? 'Yes' : 'No') . "\n";
    echo "Permissions: $permissions\n";
    
    if (!$writable) {
        echo "Setting writable permissions...\n";
        try {
            chmod($fullTempUploadPath, 0777);
            echo "Permissions set to 0777\n";
        } catch (Exception $e) {
            echo "Error setting permissions: " . $e->getMessage() . "\n";
        }
    }
}
echo "\n";

// Check CSRF token generation
echo "Checking CSRF token generation...\n";
$session = $app->make('session');
$session->start();
$token = $session->token();
echo "CSRF Token: " . ($token ? substr($token, 0, 10) . '...' : 'Not available') . "\n\n";

// Check upload URL signing
echo "Checking upload URL signing...\n";
try {
    $expiresAt = now()->addMinutes(5);
    $url = URL::temporarySignedRoute(
        'livewire.upload-file', 
        $expiresAt
    );
    echo "Signed URL generated: " . substr($url, 0, 50) . "...\n";
    echo "URL expires at: " . $expiresAt . "\n";
} catch (Exception $e) {
    echo "Error generating signed URL: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== Diagnostics Complete ===\n";
