<?php

namespace Database\Seeders;

use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promos = [
            [
                'title' => 'Paket Servis Berkala Hemat 25%',
                'description' => 'Dapatkan diskon 25% untuk paket servis berkala 10.000 km. Termasuk penggantian oli, filter oli, dan pemeriksaan 20 komponen penting. Promo terbatas hanya untuk 50 pelanggan pertama!',
                'original_price' => 850000,
                'promo_price' => 637500,
                'discount_percentage' => 25,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(20),
                'is_featured' => true,
                'is_active' => true,
                'promo_code' => 'SERVIS25',
                'remaining_slots' => 15,
            ],
            [
                'title' => 'Tune Up Mesin Spesial',
                'description' => 'Tune up mesin lengkap dengan harga spesial. Dapatkan performa maksimal untuk kendaraan Anda dengan pengecekan dan penyetelan komprehensif oleh mekanik berpengalaman kami.',
                'original_price' => 550000,
                'promo_price' => 385000,
                'discount_percentage' => 30,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'is_featured' => true,
                'is_active' => true,
                'promo_code' => 'TUNEUP30',
                'remaining_slots' => 20,
            ],
            [
                'title' => 'Paket Servis AC Sejuk',
                'description' => 'Diskon 20% untuk servis AC lengkap. Termasuk pengecekan kebocoran, pengisian freon, dan pembersihan filter AC. Nikmati kesejukan maksimal di dalam mobil Anda.',
                'original_price' => 450000,
                'promo_price' => 360000,
                'discount_percentage' => 20,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'is_featured' => true,
                'is_active' => true,
                'promo_code' => 'ACSEJUK',
                'remaining_slots' => 10,
            ],
            [
                'title' => 'Ganti Oli + Filter GRATIS',
                'description' => 'Beli oli mesin premium dan dapatkan penggantian filter oli GRATIS. Berlaku untuk semua jenis kendaraan. Jaga performa mesin kendaraan Anda dengan oli berkualitas.',
                'original_price' => 350000,
                'promo_price' => 280000,
                'discount_percentage' => 20,
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(2),
                'is_featured' => false,
                'is_active' => true,
                'promo_code' => 'OLIGRATIS',
                'remaining_slots' => 5,
            ],
            [
                'title' => 'Promo Spesial Akhir Bulan',
                'description' => 'Diskon 15% untuk semua jenis servis dan sparepart. Berlaku hanya sampai akhir bulan. Jangan lewatkan kesempatan terbatas ini untuk merawat kendaraan Anda!',
                'original_price' => null,
                'promo_price' => null,
                'discount_percentage' => 15,
                'start_date' => Carbon::now()->subDays(25),
                'end_date' => Carbon::now()->addDays(5),
                'is_featured' => false,
                'is_active' => true,
                'promo_code' => 'AKHIRBULAN15',
                'remaining_slots' => null,
            ],
            [
                'title' => 'Paket Hemat Rem Depan & Belakang',
                'description' => 'Hemat 30% untuk penggantian kampas rem depan dan belakang. Termasuk pemeriksaan sistem rem secara menyeluruh. Pastikan keamanan berkendara Anda dengan rem yang prima.',
                'original_price' => 1200000,
                'promo_price' => 840000,
                'discount_percentage' => 30,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(1),
                'is_featured' => false,
                'is_active' => true,
                'promo_code' => 'REMAMAN',
                'remaining_slots' => 3,
            ],
            [
                'title' => 'Paket Detailing Eksterior',
                'description' => 'Buat kendaraan Anda kembali berkilau dengan paket detailing eksterior lengkap. Termasuk poles body, coating, dan pembersihan velg. Tampil beda dengan mobil yang selalu terlihat baru!',
                'original_price' => 1500000,
                'promo_price' => 1125000,
                'discount_percentage' => 25,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'is_featured' => false,
                'is_active' => true,
                'promo_code' => 'DETAILING25',
                'remaining_slots' => 8,
            ],
            [
                'title' => 'Promo Spesial Member',
                'description' => 'Diskon 10% untuk semua jenis servis dan sparepart bagi member Hartono Motor. Belum menjadi member? Daftar sekarang dan nikmati berbagai keuntungan menarik!',
                'original_price' => null,
                'promo_price' => null,
                'discount_percentage' => 10,
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(335),
                'is_featured' => false,
                'is_active' => true,
                'promo_code' => 'MEMBER10',
                'remaining_slots' => null,
            ],
        ];

        foreach ($promos as $promo) {
            Promo::create($promo);
        }
    }
}
