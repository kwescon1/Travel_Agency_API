<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('travels', \App\Http\Controllers\Api\v1\TravelController::class)->only('index');

//:slug means the travel search should be by slug
Route::get('travels/{travel:slug}/tours', [\App\Http\Controllers\Api\v1\TourController::class, 'index']);

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('travels', \App\Http\Controllers\Api\v1\TravelController::class)->only(['store', 'update']);
    Route::post('travels/{travel}/tours', [\App\Http\Controllers\Api\v1\TourController::class, 'store']);
});

/**
 * Auth routes
 */
Route::post('login', \App\Http\Controllers\Api\v1\Auth\LoginController::class);
