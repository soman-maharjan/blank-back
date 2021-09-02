<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    //Category Routes
    Route::resource('category', App\Http\Controllers\CategoryController::class);
    Route::get('category/attribute/{category}', [App\Http\Controllers\CategoryController::class, 'attribute']);



    Route::resource('product', App\Http\Controllers\ProductController::class, ['except' => ['index', 'show']]);
    Route::post('product/image', [App\Http\Controllers\ProductController::class, 'image']);
    Route::post('product-status/{product}', [App\Http\Controllers\ProductController::class, 'changeStatus']);
    Route::get('user-product', [App\Http\Controllers\ProductController::class, 'userProduct']);



    Route::post('/order', [App\Http\Controllers\OrderController::class, 'placeOrder']);
    Route::get('/get-order', [App\Http\Controllers\OrderController::class, 'getOrder']);
});


// Product Management Routes
Route::get('product', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('product/{product}', [App\Http\Controllers\ProductController::class, 'show']);


//Search
Route::get('/search/{value}', [App\Http\Controllers\SearchController::class, 'search']);

Route::get('/test', function () {
    return auth()->user();
});
