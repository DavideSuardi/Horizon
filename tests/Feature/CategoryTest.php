<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_created()
    {
        $category = Category::factory()->create();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_category_belongs_to_many_countries()
    {
        $category = Category::factory()->create();
        $countries = Country::factory()->count(2)->create();
        $category->countries()->attach($countries);

        $this->assertCount(2, $category->countries);
    }

    public function it_fails_to_create_category_without_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Category::create([]);
    }

}
