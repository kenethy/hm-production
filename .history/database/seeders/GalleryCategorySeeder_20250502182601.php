<?php

namespace Database\Seeders;

use App\Models\GalleryCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GalleryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bengkel',
                'description' => 'Foto-foto fasilitas dan area bengkel Hartono Motor',
                'order' => 1,
            ],
            [
                'name' => 'Mekanik',
                'description' => 'Foto-foto tim mekanik Hartono Motor',
                'order' => 2,
            ],
            [
                'name' => 'Hasil Servis',
                'description' => 'Foto-foto hasil servis dan perbaikan kendaraan',
                'order' => 3,
            ],
            [
                'name' => 'Sparepart',
                'description' => 'Foto-foto koleksi sparepart yang tersedia',
                'order' => 4,
            ],
            [
                'name' => 'Kegiatan',
                'description' => 'Foto-foto kegiatan dan event Hartono Motor',
                'order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            GalleryCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'order' => $category['order'],
            ]);
        }
    }
}
