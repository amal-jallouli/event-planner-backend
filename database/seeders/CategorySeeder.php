<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Technologie', 'Musique', 'Sport', 'Art & Culture',
            'Business', 'Formation', 'Gastronomie', 'Bien-être',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
