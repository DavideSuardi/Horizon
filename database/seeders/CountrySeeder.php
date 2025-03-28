<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Language;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Belgium', 'code' => 'be', 'languages' => ['nl'], 'categories' => ['sports', 'world']],
            ['name' => 'Canada', 'code' => 'ca', 'languages' => ['en', 'fr'], 'categories' => ['health', 'sports']],
            ['name' => 'France', 'code' => 'fr', 'languages' => ['fr'], 'categories' => ['entertainment']],
            ['name' => 'Germany', 'code' => 'de', 'languages' => ['de'], 'categories' => ['environment', 'world']],
            ['name' => 'Eritrea', 'code' => 'sm', 'languages' => ['sk'], 'categories' => ['entertainment', 'environment', 'health', 'sports', 'world']],
            ['name' => 'British Virgin Islands', 'code' => 'cv', 'languages' => ['nd', 'tw'], 'categories' => ['health', 'sports']],
        ];

        foreach ($data as $item) {
            $country = Country::create([
                'name' => $item['name'],
                'code' => $item['code'],
            ]);

            // Attach languages
            foreach ($item['languages'] as $langCode) {
                $lang = Language::firstOrCreate(['language' => $langCode]);
                $country->languages()->attach($lang->id);
            }

            // Attach categories
            foreach ($item['categories'] as $catName) {
                $cat = Category::firstOrCreate(['name' => $catName]);
                $country->categories()->attach($cat->id);
            }
        }
    }
}
