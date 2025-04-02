<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class NewsController extends Controller
{

    public function getNewsByCountryCategory($code, $category)
    {
        $country = Country::where('code', $code)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }
    
        $categoryModel = Category::where('name', $category)->first();
        if (!$categoryModel) {
            return response()->json(['error' => 'Category not found'], 404);
        }
    
        $languages = $country->languages->pluck('language')->toArray();
    
        $cacheKey = "news_{$code}_{$category}_" . implode('_', $languages);
    
        $formatted = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($code, $category, $languages) {
            $response = Http::withoutVerifying()->get('https://newsdata.io/api/1/news', [
                'apikey' => env('NEWSDATA_API_KEY'),
                'country' => $code,
                'language' => implode(',', $languages),
                'category' => $category,
            ]);
    
            if ($response->failed()) {
                return null;
            }
    
            $body = $response->json();
            $articles = $body['results'] ?? [];
    
            return collect($articles)->map(function ($article) {
                return [
                    'id' => $article['article_id'] ?? null,
                    'title' => $article['title'] ?? null,
                    'description' => $article['description'] ?? null,
                    'link' => $article['link'] ?? null,
                    'image' => $article['image_url'] ?? null,
                    'source' => $article['source_name'] ?? null,
                    'published_at' => $article['pubDate'] ?? null,
                ];
            });
        });
    
        if (!$formatted) {
            return response()->json(['error' => 'Failed to fetch news data'], 500);
        }
    
        return response()->json([
            'status' => 'success',
            'totalResults' => count($formatted),
            'results' => $formatted,
        ]);
    }
    


    public function getPaginatedNews(Request $request, $code = null, $page = 1)
    {
        if (is_numeric($code)) {
            $page = (int) $code;
            $code = null;
        }

        $query = [
            'apikey' => env('NEWSDATA_API_KEY'),
        ];

        $cacheKeyPrefix = $code ? strtolower($code) : 'global';

        if ($page > 1) {
            $tokenKey = "{$cacheKeyPrefix}_page_{$page}";
            $pageToken = Cache::get($tokenKey);

            if (!$pageToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pagination token not found for this page',
                    'page' => $page,
                ], 404);
            }

            $query['page'] = $pageToken;
        }

        if ($code) {
            $country = Country::where('code', $code)->first();
            if (!$country) {
                return response()->json(['error' => 'Country not found'], 404);
            }

            $languages = $country->languages->pluck('language')->toArray();
            $categories = $country->categories->pluck('name')->map(fn($c) => strtolower($c))->toArray();

            if (!empty($languages)) {
                $query['language'] = implode(',', $languages);
            }

            if (!empty($categories)) {
                $query['category'] = implode(',', $categories);
            }

            $query['country'] = $code;
        }

        logger()->info('Calling NewsData API', $query);

        $response = Http::withoutVerifying()->get('https://newsdata.io/api/1/news', $query);

        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch data',
                'details' => $response->json()
            ], $response->status());
        }

        $body = $response->json();
        $articles = $body['results'] ?? [];

        if (isset($body['nextPage'])) {
            $nextToken = $body['nextPage'];
            $nextTokenKey = "{$cacheKeyPrefix}_page_" . ($page + 1);
            Cache::put($nextTokenKey, $nextToken, now()->addHours(2));
        }

        $formatted = collect($articles)->map(function ($article) {
            return [
                'id' => $article['article_id'] ?? null,
                'title' => $article['title'] ?? null,
                'description' => $article['description'] ?? null,
                'link' => $article['link'] ?? null,
                'image' => $article['image_url'] ?? null,
                'source' => $article['source_name'] ?? null,
                'published_at' => $article['pubDate'] ?? null,
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'currentPage' => (int) $page,
            'nextPage' => isset($body['nextPage']) ? $page + 1 : null,
            'results' => $formatted,
        ]);
    }
}
