<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Language;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_language_can_be_created()
    {
        $language = Language::factory()->create();
        $this->assertDatabaseHas('languages', ['id' => $language->id]);
    }

    public function test_language_belongs_to_many_countries()
    {
        $language = Language::factory()->create();
        $countries = Country::factory()->count(2)->create();
        $language->countries()->attach($countries);

        $this->assertCount(2, $language->countries);
    }

    public function it_does_not_allow_duplicate_languages_for_a_country()
    {
        $country = Country::factory()->create();
        $language = Language::factory()->create(['language' => 'en']);

        $country->languages()->attach($language->id);
        $country->languages()->attach($language->id);

        $this->assertCount(1, $country->languages); 
    }

}
