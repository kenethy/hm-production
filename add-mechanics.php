<?php

// Script untuk menambahkan data montir awal

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Start the session
$session = $app->make('session');
$session->start();

// Data montir awal
$mechanics = [
    [
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
        'specialization' => 'Mesin',
        'notes' => 'Montir senior dengan pengalaman 10 tahun',
        'is_active' => true,
    ],
    [
        'name' => 'Agus Setiawan',
        'phone' => '082345678901',
        'specialization' => 'Elektrikal',
        'notes' => 'Ahli dalam perbaikan sistem elektrikal mobil',
        'is_active' => true,
    ],
    [
        'name' => 'Dedi Kurniawan',
        'phone' => '083456789012',
        'specialization' => 'AC & Cooling',
        'notes' => 'Spesialis AC dan sistem pendingin',
        'is_active' => true,
    ],
    [
        'name' => 'Eko Prasetyo',
        'phone' => '084567890123',
        'specialization' => 'Balancing & Spooring',
        'notes' => 'Ahli dalam balancing dan spooring',
        'is_active' => true,
    ],
];

echo "Menambahkan data montir awal...\n";

// Tambahkan data montir
$mechanicModel = App\Models\Mechanic::class;
foreach ($mechanics as $mechanicData) {
    // Periksa apakah montir dengan nama tersebut sudah ada
    $mechanic = $mechanicModel::where('name', $mechanicData['name'])->first();
    
    if ($mechanic) {
        echo "Montir {$mechanicData['name']} sudah ada.\n";
        
        // Update data montir
        $mechanic->update($mechanicData);
        echo "Data montir {$mechanicData['name']} diperbarui.\n";
    } else {
        // Buat montir baru
        $mechanic = $mechanicModel::create($mechanicData);
        echo "Montir {$mechanicData['name']} berhasil ditambahkan.\n";
    }
}

echo "Selesai menambahkan data montir awal.\n";
