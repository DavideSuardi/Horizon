<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'business',
            'entertainment',
            'environment',
            'food',
            'health',
            'politics',
            'science',
            'sports',
            'technology',
            'top',
            'world',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}



