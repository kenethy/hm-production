<?php

// Script untuk memperbaiki masalah route Livewire

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "===================================================\n";
echo "     PERBAIKAN MASALAH ROUTE LIVEWIRE              \n";
echo "===================================================\n\n";

// 1. Periksa route Livewire yang terdaftar
echo ">> Memeriksa route Livewire yang terdaftar...\n";
$routes = Route::getRoutes();
$livewireRoutes = [];

foreach ($routes as $route) {
    $name = $route->getName();
    if ($name && strpos($name, 'livewire') !== false) {
        $livewireRoutes[] = [
            'name' => $name,
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
        ];
    }
}

if (count($livewireRoutes) > 0) {
    echo "Route Livewire yang terdaftar:\n";
    foreach ($livewireRoutes as $route) {
        echo "  {$route['name']} ({$route['methods']}): {$route['uri']}\n";
    }
} else {
    echo "Tidak ada route Livewire yang terdaftar. Ini adalah masalah!\n";
}
echo "\n";

// 2. Periksa file Livewire JavaScript
echo ">> Memeriksa file Livewire JavaScript...\n";
$livewireJsPath = public_path('vendor/livewire/livewire.js');
$livewireJsMinPath = public_path('vendor/livewire/livewire.min.js');

if (file_exists($livewireJsPath)) {
    echo "File livewire.js ditemukan.\n";
    echo "Ukuran file: " . filesize($livewireJsPath) . " bytes\n";
    echo "Terakhir dimodifikasi: " . date("Y-m-d H:i:s", filemtime($livewireJsPath)) . "\n";
} else {
    echo "File livewire.js tidak ditemukan. Ini adalah masalah!\n";
}

if (file_exists($livewireJsMinPath)) {
    echo "File livewire.min.js ditemukan.\n";
    echo "Ukuran file: " . filesize($livewireJsMinPath) . " bytes\n";
    echo "Terakhir dimodifikasi: " . date("Y-m-d H:i:s", filemtime($livewireJsMinPath)) . "\n";
} else {
    echo "File livewire.min.js tidak ditemukan. Ini mungkin masalah.\n";
}
echo "\n";

// 3. Mempublikasikan ulang aset Livewire
echo ">> Mempublikasikan ulang aset Livewire...\n";
try {
    Artisan::call('vendor:publish', [
        '--force' => true,
        '--tag' => 'livewire:assets',
    ]);
    echo Artisan::output();
    echo "Aset Livewire berhasil dipublikasikan ulang.\n";
} catch (Exception $e) {
    echo "Error saat mempublikasikan ulang aset Livewire: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Memperbaiki route cache
echo ">> Memperbaiki route cache...\n";
try {
    Artisan::call('route:clear');
    echo Artisan::output();
    echo "Cache route berhasil dibersihkan.\n";
} catch (Exception $e) {
    echo "Error saat membersihkan cache route: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Memeriksa dan memperbaiki file .htaccess
echo ">> Memeriksa file .htaccess...\n";
$htaccessPath = public_path('.htaccess');

if (file_exists($htaccessPath)) {
    echo "File .htaccess ditemukan.\n";
    
    // Baca konten .htaccess
    $htaccessContent = file_get_contents($htaccessPath);
    
    // Periksa apakah ada aturan untuk menangani POST request
    if (strpos($htaccessContent, 'RewriteCond %{REQUEST_METHOD} POST') === false) {
        echo "Menambahkan aturan untuk menangani POST request ke .htaccess...\n";
        
        // Tambahkan aturan untuk menangani POST request
        $newHtaccessContent = str_replace(
            "RewriteEngine On",
            "RewriteEngine On\n\n    # Handle POST requests properly\n    RewriteCond %{REQUEST_METHOD} POST\n    RewriteRule ^ - [L]",
            $htaccessContent
        );
        
        // Tulis kembali file .htaccess
        file_put_contents($htaccessPath, $newHtaccessContent);
        echo "File .htaccess berhasil diperbarui.\n";
    } else {
        echo "Aturan untuk menangani POST request sudah ada di .htaccess.\n";
    }
} else {
    echo "File .htaccess tidak ditemukan. Ini adalah masalah!\n";
}
echo "\n";

// 6. Memeriksa dan memperbaiki konfigurasi Livewire
echo ">> Memeriksa konfigurasi Livewire...\n";
$livewireConfigPath = config_path('livewire.php');

if (file_exists($livewireConfigPath)) {
    echo "File konfigurasi Livewire ditemukan.\n";
} else {
    echo "File konfigurasi Livewire tidak ditemukan. Membuat file konfigurasi...\n";
    
    try {
        Artisan::call('vendor:publish', [
            '--tag' => 'livewire:config',
        ]);
        echo Artisan::output();
        echo "File konfigurasi Livewire berhasil dibuat.\n";
    } catch (Exception $e) {
        echo "Error saat membuat file konfigurasi Livewire: " . $e->getMessage() . "\n";
    }
}
echo "\n";

// 7. Membersihkan cache aplikasi
echo ">> Membersihkan cache aplikasi...\n";
try {
    Artisan::call('optimize:clear');
    echo Artisan::output();
    echo "Cache aplikasi berhasil dibersihkan.\n";
} catch (Exception $e) {
    echo "Error saat membersihkan cache aplikasi: " . $e->getMessage() . "\n";
}
echo "\n";

// 8. Memeriksa dan memperbaiki file index.php
echo ">> Memeriksa file index.php...\n";
$indexPhpPath = public_path('index.php');

if (file_exists($indexPhpPath)) {
    echo "File index.php ditemukan.\n";
    
    // Baca konten index.php
    $indexPhpContent = file_get_contents($indexPhpPath);
    
    // Periksa apakah menggunakan handleRequest atau run
    if (strpos($indexPhpContent, '$app->handleRequest(Request::capture());') !== false) {
        echo "File index.php menggunakan handleRequest. Ini adalah format Laravel 12.\n";
    } else if (strpos($indexPhpContent, '$kernel->handle(') !== false) {
        echo "File index.php menggunakan format lama. Memperbarui ke format Laravel 12...\n";
        
        // Buat backup
        file_put_contents($indexPhpPath . '.bak', $indexPhpContent);
        
        // Buat konten baru
        $newIndexPhpContent = <<<'EOD'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
EOD;
        
        // Tulis kembali file index.php
        file_put_contents($indexPhpPath, $newIndexPhpContent);
        echo "File index.php berhasil diperbarui.\n";
    } else {
        echo "Format file index.php tidak dikenali.\n";
    }
} else {
    echo "File index.php tidak ditemukan. Ini adalah masalah serius!\n";
}
echo "\n";

echo "===================================================\n";
echo "     PERBAIKAN SELESAI                             \n";
echo "===================================================\n\n";

echo "Silakan restart web server Anda dan coba lagi.\n";
echo "Jika masalah masih berlanjut, periksa log di storage/logs/laravel.log\n";
