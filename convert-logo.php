<?php

// Function to convert an image to WebP with high quality
function convertToHighQualityWebP($inputPath, $quality = 100) {
    $outputPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $inputPath);
    
    echo "Converting $inputPath to WebP with maximum quality...\n";
    
    // Load the image
    $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));
    $image = null;
    
    switch ($extension) {
        case 'png':
            $image = imagecreatefrompng($inputPath);
            // Preserve transparency
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($inputPath);
            break;
        default:
            echo "Unsupported image format: $extension\n";
            return;
    }
    
    // Get original dimensions
    $width = imagesx($image);
    $height = imagesy($image);
    echo "Original dimensions: {$width}x{$height}\n";
    
    // Save as WebP with maximum quality
    imagewebp($image, $outputPath, $quality);
    imagedestroy($image);
    
    // Get file sizes for comparison
    $originalSize = filesize($inputPath);
    $webpSize = filesize($outputPath);
    
    echo "Converted $inputPath to $outputPath with quality $quality\n";
    echo "Original size: " . round($originalSize / 1024, 2) . " KB\n";
    echo "WebP size: " . round($webpSize / 1024, 2) . " KB\n";
    echo "Original dimensions: {$width}x{$height}\n";
    echo "-----------------------------------\n";
    
    return array($width, $height);
}

// Convert logo with maximum quality
$dimensions = convertToHighQualityWebP('public/images/logo/logo.png', 100);
