<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = ['en', 'fr', 'de', 'nl', 'sk', 'nd', 'tw'];

        foreach ($languages as $lang) {
            Language::firstOrCreate(['language' => $lang]);
        }
    }
}
