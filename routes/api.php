<?php

use App\Http\Controllers\ListingsController;
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

Route::get('listings', [ListingsController::class, 'index'])->name('listings.index');
Route::get('listings/{listing}', [ListingsController::class, 'show'])->name('listings.show');
Route::post('listings', [ListingsController::class, 'store'])->name('listings.store');
Route::put('listings/{listing}', [ListingsController::class, 'update'])->name('listings.update');
Route::delete('listings/{listing}', [ListingsController::class, 'destroy'])->name('listings.destroy');
