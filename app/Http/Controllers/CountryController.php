<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Cache::remember('countries', 60, function () {
            return Country::with(['languages', 'categories'])->get();
        });

        $data = $countries->map(function ($country) {
            return [
                'name' => $country->name,
                'code' => strtoupper($country->code),
                'languages' => $country->languages->pluck('language')->toArray(),
                'categories' => $country->categories->pluck('name')->toArray(),
            ];
        });

        return response()->json([
            'countries' => $data
        ]);
    }

    public function show($code)
    {
        $cacheKey = "country_{$code}";

        $country = Cache::remember($cacheKey, 60, function () use ($code) {
            return Country::with(['languages', 'categories'])->where('code', $code)->first();
        });

        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        return response()->json(['country' => $country], 200);
    }
}
