<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login')->name('login');
    Route::get('login', 'login')->name('login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('listings/create', [ListingController::class, 'create']);
    Route::post('listings', [ListingController::class, 'store']);
    Route::get('product-classifications', [ProductController::class, 'index']);
});

Route::get('listings/{id}', [ListingController::class, 'show']);
Route::get('listings', [ListingController::class, 'index']);
// Route::resource('listings', ListingController::class);

// Route::middleware('auth:sanctum')->group( function () {
//     Route::resource('listings', ListingController::class);
// });

// Route::apiResource('listings', ListingController::class);
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');