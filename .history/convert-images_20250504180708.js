import webpConverter from 'webp-converter';
import fs from 'fs';
import path from 'path';

const webp = webpConverter;

// Set webp-converter binary path
webp.grant_permission();

// Function to convert an image to WebP
async function convertToWebP(inputPath, quality = 80) {
    const outputPath = inputPath.replace(/\.(png|jpg|jpeg)$/i, '.webp');

    try {
        // Check if WebP version already exists
        if (fs.existsSync(outputPath)) {
            console.log(`WebP version already exists: ${outputPath}`);
            return;
        }

        console.log(`Converting ${inputPath} to WebP...`);

        // Convert image to WebP
        await webp.cwebp(inputPath, outputPath, `-q ${quality}`);

        // Get file sizes for comparison
        const originalSize = fs.statSync(inputPath).size;
        const webpSize = fs.statSync(outputPath).size;

        const savingsPercent = ((originalSize - webpSize) / originalSize * 100).toFixed(2);

        console.log(`Converted ${inputPath} to ${outputPath}`);
        console.log(`Original size: ${(originalSize / 1024).toFixed(2)} KB`);
        console.log(`WebP size: ${(webpSize / 1024).toFixed(2)} KB`);
        console.log(`Savings: ${savingsPercent}%`);
        console.log('-----------------------------------');
    } catch (error) {
        console.error(`Error converting ${inputPath}:`, error);
    }
}

// Main function to process images
async function processImages() {
    // Convert hero image
    await convertToWebP('public/images/hero-bg.png', 85);

    // Convert logo
    await convertToWebP('public/images/logo/logo.png', 90);
}

// Run the conversion
processImages().catch(console.error);
