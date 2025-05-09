<?php

use App\Models\Promo;
use Carbon\Carbon;

// Buat promo dummy
Promo::create([
    'title' => 'Promo Tune Up Spesial',
    'slug' => 'promo-tune-up-spesial',
    'description' => 'Dapatkan diskon 20% untuk layanan tune up mesin. Berlaku hingga akhir bulan.',
    'image_path' => 'promos/tune-up-promo.jpg',
    'original_price' => 500000,
    'promo_price' => 400000,
    'discount_percentage' => 20,
    'start_date' => Carbon::now()->subDays(5),
    'end_date' => Carbon::now()->addDays(25),
    'is_featured' => true,
    'is_active' => true,
    'promo_code' => 'TUNEUP20',
    'remaining_slots' => 10
]);

Promo::create([
    'title' => 'Paket Servis AC Hemat',
    'slug' => 'paket-servis-ac-hemat',
    'description' => 'Servis AC mobil lengkap dengan diskon 15%. Termasuk pengecekan, isi freon, dan pembersihan.',
    'image_path' => 'promos/ac-service-promo.jpg',
    'original_price' => 350000,
    'promo_price' => 297500,
    'discount_percentage' => 15,
    'start_date' => Carbon::now()->subDays(10),
    'end_date' => Carbon::now()->addDays(20),
    'is_featured' => true,
    'is_active' => true,
    'promo_code' => 'AC15',
    'remaining_slots' => 15
]);

Promo::create([
    'title' => 'Ganti Oli Premium',
    'slug' => 'ganti-oli-premium',
    'description' => 'Ganti oli dengan merek premium + filter oli baru dengan harga spesial.',
    'image_path' => 'promos/oil-change-promo.jpg',
    'original_price' => 250000,
    'promo_price' => 200000,
    'discount_percentage' => 20,
    'start_date' => Carbon::now()->subDays(15),
    'end_date' => Carbon::now()->addDays(15),
    'is_featured' => true,
    'is_active' => true,
    'promo_code' => 'OLI20',
    'remaining_slots' => 20
]);

echo 'Berhasil membuat 3 promo dummy!';
