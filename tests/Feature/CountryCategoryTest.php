<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::factory()->create(['code' => 'ca', 'name' => 'Canada']);
        $category = Category::factory()->create(['name' => 'sports']);

        $country->languages()->create(['language' => 'en']);
    }

    /** @test */
    public function it_adds_a_category_to_a_country()
    {
        $response = $this->postJson('/api/country/ca/sports');

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Category 'sports' added to country 'ca'",
            ]);

        $this->assertDatabaseHas('country_category', [
            'country_id' => Country::where('code', 'ca')->first()->id,
            'category_id' => Category::where('name', 'sports')->first()->id,
        ]);
    }

    /** @test */
    public function it_fails_if_country_does_not_exist()
    {
        $response = $this->postJson('/api/country/zz/sports');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Country not found']);
    }

    /** @test */
    public function it_fails_if_category_does_not_exist()
    {
        $response = $this->postJson('/api/country/ca/fake');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Category not found']);
    }

    /** @test */
    public function it_does_not_duplicate_categories()
    {
        $this->postJson('/api/country/ca/sports');

        $response = $this->postJson('/api/country/ca/sports');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category already added to country',
            ]);
    }

    /** @test */
    public function it_removes_a_category_from_a_country()
    {
        $this->postJson('/api/country/ca/sports');

        $response = $this->deleteJson('/api/country/ca/sports');

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Category 'sports' removed from country 'ca'",
            ]);

        $this->assertDatabaseMissing('country_category', [
            'country_id' => Country::where('code', 'ca')->first()->id,
            'category_id' => Category::where('name', 'sports')->first()->id,
        ]);
    }

    /** @test */
    public function test_it_returns_404_when_removing_a_category_not_linked_to_country()
    {
        $country = Country::factory()->create(['code' => 'cg', 'name' => 'Congo']);
        $category = Category::factory()->create(['name' => 'sports']);

        $response = $this->deleteJson("/api/country/cg/sports");

        $response->assertStatus(404)
                ->assertJson(['error' => 'Category not associated with country']);
    }

}
