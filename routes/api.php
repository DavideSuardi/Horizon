<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CountryCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/country/{code}', [CountryController::class, 'show']);
Route::get('/country/{code}/{category}', [NewsController::class, 'getNewsByCountryCategory']);
Route::get('/news/{code?}/{page?}', [NewsController::class, 'getPaginatedNews']);



Route::post('/country/{code}/{category}', [CountryCategoryController::class, 'addCategory']);
Route::delete('/country/{code}/{category}', [CountryCategoryController::class, 'removeCategory']);

