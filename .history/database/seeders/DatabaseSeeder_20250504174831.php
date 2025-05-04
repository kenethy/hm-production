<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'hartonomotor1979@gmail.com',
            'password' => 'hmbengkel1979'
        ]);

        $this->call([
            PromoSeeder::class,
            GalleryCategorySeeder::class,
            BlogCategorySeeder::class,
            BlogTagSeeder::class,
        ]);
    }
}
