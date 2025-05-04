<?php

namespace Database\Seeders;

use App\Models\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Perawatan Mobil',
            'Mesin',
            'AC Mobil',
            'Ban',
            'Oli',
            'Rem',
            'Transmisi',
            'Aki',
            'Radiator',
            'Suspensi',
            'Teknologi',
            'Mobil Listrik',
            'Hybrid',
            'Kelistrikan',
            'Servis Berkala',
            'DIY',
            'Sparepart',
            'Aksesoris',
            'Interior',
            'Eksterior',
            'Berita',
            'Tips',
            'Review',
            'Tutorial',
        ];

        foreach ($tags as $tag) {
            BlogTag::create([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);
        }
    }
}
