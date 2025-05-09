<?php

// Script to test file upload functionality

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== File Upload Test ===\n\n";

// Create a test image
echo "Creating test image...\n";
$testImagePath = storage_path('app/public/test-upload.jpg');
$image = imagecreatetruecolor(1200, 675);
$bgColor = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);
imagefill($image, 0, 0, $bgColor);
imagestring($image, 5, 500, 300, 'Test Image', $textColor);
imagejpeg($image, $testImagePath);
imagedestroy($image);
echo "Test image created at: $testImagePath\n\n";

// Test uploading to public disk
echo "Testing upload to public disk...\n";
try {
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $result = $disk->put('gallery/test-upload.jpg', file_get_contents($testImagePath));
    echo "Upload result: " . ($result ? "Success" : "Failed") . "\n";
    
    if ($result) {
        echo "File exists on disk: " . ($disk->exists('gallery/test-upload.jpg') ? "Yes" : "No") . "\n";
        echo "File URL: " . $disk->url('gallery/test-upload.jpg') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test directory permissions
echo "Testing directory permissions...\n";
$galleryDir = storage_path('app/public/gallery');
if (!file_exists($galleryDir)) {
    echo "Creating gallery directory...\n";
    mkdir($galleryDir, 0777, true);
}

echo "Gallery directory permissions: " . substr(sprintf('%o', fileperms($galleryDir)), -4) . "\n";
echo "Gallery directory writable: " . (is_writable($galleryDir) ? "Yes" : "No") . "\n\n";

// Test Livewire temporary upload directory
echo "Testing Livewire temporary upload directory...\n";
$livewireTmpDir = storage_path('app/livewire-tmp');
if (!file_exists($livewireTmpDir)) {
    echo "Creating Livewire temporary upload directory...\n";
    mkdir($livewireTmpDir, 0777, true);
}

echo "Livewire temporary directory permissions: " . substr(sprintf('%o', fileperms($livewireTmpDir)), -4) . "\n";
echo "Livewire temporary directory writable: " . (is_writable($livewireTmpDir) ? "Yes" : "No") . "\n\n";

// Test storage link
echo "Testing storage link...\n";
$publicStoragePath = public_path('storage');
echo "Storage link exists: " . (file_exists($publicStoragePath) ? "Yes" : "No") . "\n";
if (file_exists($publicStoragePath) && is_link($publicStoragePath)) {
    echo "Storage link target: " . readlink($publicStoragePath) . "\n";
}
echo "\n";

// Test accessing the uploaded file via HTTP
echo "Testing HTTP access to uploaded file...\n";
$fileUrl = url('storage/gallery/test-upload.jpg');
echo "File URL: $fileUrl\n";

try {
    $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', $fileUrl);
    echo "HTTP Status: " . $response->getStatusCode() . "\n";
    echo "Content Type: " . $response->getHeaderLine('Content-Type') . "\n";
} catch (Exception $e) {
    echo "Error accessing file via HTTP: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
