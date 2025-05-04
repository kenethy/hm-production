<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tips Perawatan Mobil',
                'description' => 'Berbagai tips dan trik untuk merawat mobil Anda agar tetap prima',
                'order' => 1,
            ],
            [
                'name' => 'Teknologi Otomotif',
                'description' => 'Informasi terkini tentang teknologi dan inovasi di dunia otomotif',
                'order' => 2,
            ],
            [
                'name' => 'Servis dan Perbaikan',
                'description' => 'Panduan servis dan perbaikan mobil untuk berbagai masalah umum',
                'order' => 3,
            ],
            [
                'name' => 'Sparepart dan Aksesoris',
                'description' => 'Informasi tentang sparepart dan aksesoris mobil terbaru',
                'order' => 4,
            ],
            [
                'name' => 'Berita Otomotif',
                'description' => 'Berita terkini seputar dunia otomotif dalam dan luar negeri',
                'order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'order' => $category['order'],
            ]);
        }
    }
}
