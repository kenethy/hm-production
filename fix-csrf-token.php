<?php

// Script untuk memperbaiki masalah CSRF token

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "===================================================\n";
echo "     PERBAIKAN MASALAH CSRF TOKEN                  \n";
echo "===================================================\n\n";

// 1. Periksa konfigurasi session
echo ">> Memeriksa konfigurasi session...\n";
$sessionDriver = config('session.driver');
$sessionLifetime = config('session.lifetime');
$sessionSecure = config('session.secure');
$sessionSameSite = config('session.same_site');

echo "Driver Session: $sessionDriver\n";
echo "Lifetime Session: $sessionLifetime menit\n";
echo "Secure Cookie: " . ($sessionSecure ? 'Ya' : 'Tidak') . "\n";
echo "Same Site: $sessionSameSite\n\n";

// 2. Periksa CSRF token
echo ">> Memeriksa CSRF token...\n";
$session = app('session');
$token = $session->token();
echo "CSRF Token: " . ($token ? substr($token, 0, 10) . '...' : 'Tidak tersedia') . "\n";

if (!$token) {
    echo "CSRF token tidak tersedia. Mencoba regenerasi token...\n";
    $session->regenerateToken();
    $token = $session->token();
    echo "CSRF Token baru: " . ($token ? substr($token, 0, 10) . '...' : 'Masih tidak tersedia') . "\n";
}
echo "\n";

// 3. Periksa middleware VerifyCsrfToken
echo ">> Memeriksa middleware VerifyCsrfToken...\n";
$verifyClass = 'App\Http\Middleware\VerifyCsrfToken';
if (class_exists($verifyClass)) {
    echo "Class VerifyCsrfToken ditemukan.\n";
    
    try {
        $reflector = new ReflectionClass($verifyClass);
        $exceptProperty = $reflector->getProperty('except');
        $exceptProperty->setAccessible(true);
        
        $instance = app($verifyClass);
        $except = $exceptProperty->getValue($instance);
        
        echo "Route yang dikecualikan dari verifikasi CSRF:\n";
        if (count($except) > 0) {
            foreach ($except as $route) {
                echo "  - $route\n";
            }
        } else {
            echo "  Tidak ada route yang dikecualikan.\n";
        }
    } catch (Exception $e) {
        echo "Error saat memeriksa property 'except': " . $e->getMessage() . "\n";
    }
} else {
    echo "Class VerifyCsrfToken tidak ditemukan. Ini adalah masalah!\n";
}
echo "\n";

// 4. Periksa file JavaScript Livewire
echo ">> Memeriksa file JavaScript Livewire...\n";
$livewireJsPath = public_path('vendor/livewire/livewire.js');

if (file_exists($livewireJsPath)) {
    echo "File livewire.js ditemukan.\n";
    
    // Periksa apakah file berisi fungsi untuk menangani CSRF token
    $livewireJsContent = file_get_contents($livewireJsPath);
    
    if (strpos($livewireJsContent, 'X-CSRF-TOKEN') !== false) {
        echo "File livewire.js berisi kode untuk menangani CSRF token.\n";
    } else {
        echo "File livewire.js mungkin tidak berisi kode untuk menangani CSRF token. Ini adalah masalah!\n";
    }
} else {
    echo "File livewire.js tidak ditemukan. Ini adalah masalah!\n";
}
echo "\n";

// 5. Perbaiki konfigurasi session
echo ">> Memperbaiki konfigurasi session...\n";
$envPath = base_path('.env');

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Pastikan SESSION_SECURE_COOKIE=true
    if (strpos($envContent, 'SESSION_SECURE_COOKIE=false') !== false) {
        $envContent = str_replace('SESSION_SECURE_COOKIE=false', 'SESSION_SECURE_COOKIE=true', $envContent);
        file_put_contents($envPath, $envContent);
        echo "Konfigurasi SESSION_SECURE_COOKIE diubah menjadi true.\n";
    } else if (strpos($envContent, 'SESSION_SECURE_COOKIE=true') !== false) {
        echo "Konfigurasi SESSION_SECURE_COOKIE sudah diatur ke true.\n";
    } else {
        $envContent .= "\nSESSION_SECURE_COOKIE=true\n";
        file_put_contents($envPath, $envContent);
        echo "Konfigurasi SESSION_SECURE_COOKIE ditambahkan dengan nilai true.\n";
    }
    
    // Pastikan SESSION_SAME_SITE=lax
    if (strpos($envContent, 'SESSION_SAME_SITE=') !== false) {
        $envContent = preg_replace('/SESSION_SAME_SITE=.*/', 'SESSION_SAME_SITE=lax', $envContent);
        file_put_contents($envPath, $envContent);
        echo "Konfigurasi SESSION_SAME_SITE diubah menjadi lax.\n";
    } else {
        $envContent .= "\nSESSION_SAME_SITE=lax\n";
        file_put_contents($envPath, $envContent);
        echo "Konfigurasi SESSION_SAME_SITE ditambahkan dengan nilai lax.\n";
    }
} else {
    echo "File .env tidak ditemukan!\n";
}
echo "\n";

// 6. Bersihkan cache konfigurasi
echo ">> Membersihkan cache konfigurasi...\n";
try {
    Artisan::call('config:clear');
    echo Artisan::output();
    echo "Cache konfigurasi berhasil dibersihkan.\n";
} catch (Exception $e) {
    echo "Error saat membersihkan cache konfigurasi: " . $e->getMessage() . "\n";
}
echo "\n";

// 7. Tambahkan route Livewire ke pengecualian CSRF
echo ">> Menambahkan route Livewire ke pengecualian CSRF...\n";
$verifyCsrfTokenPath = app_path('Http/Middleware/VerifyCsrfToken.php');

if (file_exists($verifyCsrfTokenPath)) {
    $verifyCsrfTokenContent = file_get_contents($verifyCsrfTokenPath);
    
    // Periksa apakah route Livewire sudah dikecualikan
    if (strpos($verifyCsrfTokenContent, 'livewire/*') === false) {
        // Tambahkan route Livewire ke pengecualian
        $verifyCsrfTokenContent = str_replace(
            'protected $except = [',
            "protected \$except = [\n        'livewire/*',",
            $verifyCsrfTokenContent
        );
        
        file_put_contents($verifyCsrfTokenPath, $verifyCsrfTokenContent);
        echo "Route Livewire ditambahkan ke pengecualian CSRF.\n";
    } else {
        echo "Route Livewire sudah dikecualikan dari verifikasi CSRF.\n";
    }
} else {
    echo "File VerifyCsrfToken.php tidak ditemukan!\n";
}
echo "\n";

echo "===================================================\n";
echo "     PERBAIKAN SELESAI                             \n";
echo "===================================================\n\n";

echo "Silakan restart web server Anda dan coba lagi.\n";
echo "Jika masalah masih berlanjut, periksa log di storage/logs/laravel.log\n";
