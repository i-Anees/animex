<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@animex.test'],
            [
                'name' => 'ANIMEX Admin',
                'password' => Hash::make('password'),
            ]
        );

        $this->call([
            CatalogSeeder::class,
            CommerceSeeder::class,
            HeroSlideSeeder::class,
        ]);

        // Render the anime graphic-tee SVG artwork for the seeded catalog.
        \Illuminate\Support\Facades\Artisan::call('designs:generate');
    }
}
