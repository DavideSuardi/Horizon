<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Category;


class CountryCategoryController extends Controller
{
    public function addCategory($code, $category)
    {
        $country = Country::where('code', $code)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $categoryModel = Category::where('name', $category)->first();
        if (!$categoryModel) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        if ($country->categories->contains($categoryModel)) {
            return response()->json([
                'message' => 'Category already added to country',
                'categories' => $country->categories()->pluck('name'),
                'languages' => $country->languages()->pluck('language'),
            ]);
        }

        $country->categories()->attach($categoryModel);

        return response()->json([
            'message' => "Category '{$category}' added to country '{$code}'",
            'categories' => $country->categories()->pluck('name'),
            'languages' => $country->languages()->pluck('language'),
        ]);
    }

    public function removeCategory($countryCode, $categoryName)
    {
        $country = Country::where('code', $countryCode)->first();
        $category = Category::where('name', $categoryName)->first();

        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        if (!$country->categories->contains($category->id)) {
            return response()->json(['error' => 'Category not associated with country'], 404);
        }

        $country->categories()->detach($category->id);

        return response()->json([
            'message' => "Category '{$categoryName}' removed from country '{$countryCode}'"
        ]);

    }
}
