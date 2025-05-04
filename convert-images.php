<?php

// Function to convert an image to WebP
function convertToWebP($inputPath, $quality = 80) {
    $outputPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $inputPath);
    
    // Check if WebP version already exists
    if (file_exists($outputPath)) {
        echo "WebP version already exists: $outputPath\n";
        return;
    }
    
    echo "Converting $inputPath to WebP...\n";
    
    // Load the image based on its extension
    $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));
    $image = null;
    
    switch ($extension) {
        case 'png':
            $image = imagecreatefrompng($inputPath);
            break;
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($inputPath);
            break;
        default:
            echo "Unsupported image format: $extension\n";
            return;
    }
    
    // Save as WebP
    imagewebp($image, $outputPath, $quality);
    imagedestroy($image);
    
    // Get file sizes for comparison
    $originalSize = filesize($inputPath);
    $webpSize = filesize($outputPath);
    
    $savingsPercent = round(($originalSize - $webpSize) / $originalSize * 100, 2);
    
    echo "Converted $inputPath to $outputPath\n";
    echo "Original size: " . round($originalSize / 1024, 2) . " KB\n";
    echo "WebP size: " . round($webpSize / 1024, 2) . " KB\n";
    echo "Savings: $savingsPercent%\n";
    echo "-----------------------------------\n";
}

// Main function to process images
function processImages() {
    // Convert hero image
    convertToWebP('public/images/hero-bg.png', 85);
    
    // Convert logo
    convertToWebP('public/images/logo/logo.png', 90);
}

// Run the conversion
processImages();
