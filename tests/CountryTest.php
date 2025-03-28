<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Country;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    public function test_country_can_be_created()
    {
        $country = Country::factory()->create();
        $this->assertDatabaseHas('countries', ['id' => $country->id]);
    }

    public function test_country_can_have_languages()
    {
        $country = Country::factory()->create();
        $languages = Language::factory()->count(2)->create();
        $country->languages()->attach($languages);

        $this->assertCount(2, $country->languages);
    }

    public function test_country_can_have_categories()
    {
        $country = Country::factory()->create();
        $categories = Category::factory()->count(3)->create();
        $country->categories()->attach($categories);

        $this->assertCount(3, $country->categories);
    }

    public function it_fails_to_create_country_with_duplicate_code()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Country::factory()->create(['code' => 'fr']);
        Country::factory()->create(['code' => 'fr']); 
    }

}
