<?php

// Script untuk memeriksa status aplikasi dan memverifikasi semua komponen

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "===================================================\n";
echo "          PEMERIKSAAN STATUS APLIKASI              \n";
echo "===================================================\n\n";

// 1. Informasi Umum Aplikasi
echo ">> INFORMASI UMUM APLIKASI\n";
echo "Nama Aplikasi: " . config('app.name') . "\n";
echo "Environment: " . app()->environment() . "\n";
echo "Debug Mode: " . (config('app.debug') ? 'Aktif' : 'Nonaktif') . "\n";
echo "URL Aplikasi: " . config('app.url') . "\n";
echo "Versi Laravel: " . app()->version() . "\n";
echo "Versi PHP: " . phpversion() . "\n\n";

// 2. Konfigurasi Filesystem
echo ">> KONFIGURASI FILESYSTEM\n";
echo "Default Disk: " . config('filesystems.default') . "\n";
echo "Public Disk URL: " . config('filesystems.disks.public.url') . "\n";
echo "Public Disk Root: " . config('filesystems.disks.public.root') . "\n\n";

// 3. Konfigurasi Session
echo ">> KONFIGURASI SESSION\n";
echo "Driver Session: " . config('session.driver') . "\n";
echo "Lifetime Session: " . config('session.lifetime') . " menit\n";
echo "Secure Cookie: " . (config('session.secure') ? 'Ya' : 'Tidak') . "\n";
echo "Same Site: " . config('session.same_site') . "\n\n";

// 4. Konfigurasi Livewire
echo ">> KONFIGURASI LIVEWIRE\n";
if (class_exists('Livewire\Livewire')) {
    $livewireConfig = config('livewire') ?? [];
    echo "Livewire Terinstal: Ya\n";
    echo "Direktori Upload Sementara: " . ($livewireConfig['temporary_file_upload']['directory'] ?? 'Tidak dikonfigurasi') . "\n";
    echo "Disk Upload: " . ($livewireConfig['temporary_file_upload']['disk'] ?? 'Tidak dikonfigurasi') . "\n";
} else {
    echo "Livewire Terinstal: Tidak\n";
}
echo "\n";

// 5. Pemeriksaan Symbolic Link Storage
echo ">> PEMERIKSAAN SYMBOLIC LINK STORAGE\n";
$publicStoragePath = public_path('storage');
$storageAppPublicPath = storage_path('app/public');

echo "Public Storage Path: " . $publicStoragePath . "\n";
echo "Storage App Public Path: " . $storageAppPublicPath . "\n";
echo "Symbolic Link Ada: " . (file_exists($publicStoragePath) && is_link($publicStoragePath) ? 'Ya' : 'Tidak') . "\n";

if (file_exists($publicStoragePath) && is_link($publicStoragePath)) {
    echo "Target Symbolic Link: " . readlink($publicStoragePath) . "\n";
    echo "Symbolic Link Valid: " . (readlink($publicStoragePath) == $storageAppPublicPath ? 'Ya' : 'Tidak') . "\n";
}
echo "\n";

// 6. Pemeriksaan Direktori
echo ">> PEMERIKSAAN DIREKTORI\n";
$directories = [
    'storage' => storage_path(),
    'storage/app' => storage_path('app'),
    'storage/app/public' => storage_path('app/public'),
    'storage/app/public/gallery' => storage_path('app/public/gallery'),
    'storage/app/livewire-tmp' => storage_path('app/livewire-tmp'),
    'bootstrap/cache' => base_path('bootstrap/cache'),
    'public/storage' => public_path('storage'),
];

foreach ($directories as $name => $path) {
    $exists = file_exists($path);
    $writable = $exists && is_writable($path);
    $permissions = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    
    echo "$name:\n";
    echo "  Path: $path\n";
    echo "  Ada: " . ($exists ? 'Ya' : 'Tidak') . "\n";
    echo "  Dapat Ditulis: " . ($writable ? 'Ya' : 'Tidak') . "\n";
    echo "  Izin: $permissions\n\n";
}

// 7. Pemeriksaan Database
echo ">> PEMERIKSAAN DATABASE\n";
try {
    $connection = DB::connection();
    $connection->getPdo();
    echo "Koneksi Database: Berhasil\n";
    echo "Driver Database: " . config('database.default') . "\n";
    echo "Nama Database: " . config('database.connections.' . config('database.default') . '.database') . "\n";
    
    // Cek tabel users
    $usersCount = DB::table('users')->count();
    echo "Jumlah User: $usersCount\n";
    
    // Cek tabel migrations
    $migrationsCount = DB::table('migrations')->count();
    echo "Jumlah Migrasi: $migrationsCount\n";
} catch (Exception $e) {
    echo "Koneksi Database: Gagal\n";
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 8. Pemeriksaan Route
echo ">> PEMERIKSAAN ROUTE\n";
$routes = Route::getRoutes();
$routeCount = count($routes);
echo "Jumlah Route: $routeCount\n";

// Cek route Livewire
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

echo "Route Livewire:\n";
if (count($livewireRoutes) > 0) {
    foreach ($livewireRoutes as $route) {
        echo "  {$route['name']} ({$route['methods']}): {$route['uri']}\n";
    }
} else {
    echo "  Tidak ada route Livewire yang ditemukan\n";
}
echo "\n";

// 9. Pemeriksaan File Upload
echo ">> PEMERIKSAAN FILE UPLOAD\n";
try {
    // Buat file test
    $testContent = 'Test file content - ' . date('Y-m-d H:i:s');
    $testPath = 'test-' . time() . '.txt';
    
    // Coba upload ke disk public
    $result = Storage::disk('public')->put($testPath, $testContent);
    echo "Upload ke Public Disk: " . ($result ? 'Berhasil' : 'Gagal') . "\n";
    
    if ($result) {
        $fileExists = Storage::disk('public')->exists($testPath);
        echo "File Ada di Disk: " . ($fileExists ? 'Ya' : 'Tidak') . "\n";
        
        if ($fileExists) {
            $fileUrl = Storage::disk('public')->url($testPath);
            echo "URL File: $fileUrl\n";
            
            // Hapus file test
            Storage::disk('public')->delete($testPath);
            echo "File Test Dihapus\n";
        }
    }
} catch (Exception $e) {
    echo "Error Saat Upload: " . $e->getMessage() . "\n";
}
echo "\n";

// 10. Pemeriksaan CSRF Token
echo ">> PEMERIKSAAN CSRF TOKEN\n";
$session = app('session');
$token = $session->token();
echo "CSRF Token: " . ($token ? substr($token, 0, 10) . '...' : 'Tidak tersedia') . "\n\n";

echo "===================================================\n";
echo "          PEMERIKSAAN SELESAI                      \n";
echo "===================================================\n\n";

echo "Hasil pemeriksaan menunjukkan status aplikasi Anda.\n";
echo "Jika ada masalah, perbaiki sesuai dengan informasi di atas.\n";
echo "Untuk masalah upload file, pastikan direktori storage dapat ditulis\n";
echo "dan symbolic link storage sudah dibuat dengan benar.\n";
