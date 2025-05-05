<?php

// Script untuk menambahkan metode canAccess ke resource admin

// Daftar resource yang hanya boleh diakses oleh admin
$adminResources = [
    'PromoResource',
    'GalleryResource',
    'GalleryCategoryResource',
    'BlogPostResource',
    'BlogCategoryResource',
    'BlogTagResource',
];

// Path ke direktori resources
$resourcesPath = __DIR__ . '/app/Filament/Resources';

// Metode canAccess yang akan ditambahkan
$canAccessMethod = '
    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user && $user->role === \'admin\';
    }
';

// Tambahkan metode canAccess ke setiap resource
foreach ($adminResources as $resource) {
    $filePath = $resourcesPath . '/' . $resource . '.php';
    
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Periksa apakah metode canAccess sudah ada
        if (strpos($content, 'canAccess') === false) {
            // Tambahkan use statement untuk Auth jika belum ada
            if (strpos($content, 'use Illuminate\Support\Facades\Auth;') === false) {
                $content = str_replace(
                    'use Illuminate\Support\Collection;',
                    "use Illuminate\Support\Collection;\nuse Illuminate\Support\Facades\Auth;",
                    $content
                );
            }
            
            // Tambahkan metode canAccess sebelum kurung kurawal penutup terakhir
            $content = substr_replace($content, $canAccessMethod . '}', strrpos($content, '}'), 1);
            
            // Simpan perubahan
            file_put_contents($filePath, $content);
            
            echo "Added canAccess method to $resource\n";
        } else {
            echo "canAccess method already exists in $resource\n";
        }
    } else {
        echo "File not found: $filePath\n";
    }
}

echo "Done adding access control to admin resources!\n";
