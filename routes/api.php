<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
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
    Route::resource('category', App\Http\Controllers\CategoryController::class, ['except' => ['index']]);
    Route::get('category/attribute/{category}', [App\Http\Controllers\CategoryController::class, 'attribute']);



    Route::resource('product', App\Http\Controllers\ProductController::class, ['except' => ['index', 'show']]);
    Route::post('product/image', [App\Http\Controllers\ProductController::class, 'image']);
    Route::post('product-status/{product}', [App\Http\Controllers\ProductController::class, 'changeStatus']);
    Route::get('user-product', [App\Http\Controllers\ProductController::class, 'userProduct']);


    //orders
    Route::post('/order', [App\Http\Controllers\OrderController::class, 'placeOrder']);
    Route::get('/user-order', [App\Http\Controllers\OrderController::class, 'getOrder']);

    //ads
    Route::post('ad', [App\Http\Controllers\AdController::class, 'store']);
    Route::get('ad', [App\Http\Controllers\AdController::class, 'index']);
    Route::post('ad/update-ad', [App\Http\Controllers\AdController::class, 'updateAd']);


    //users resource  
    Route::resource('/users', App\Http\Controllers\UserController::class);


    Route::post('pickup-address', [App\Http\Controllers\AddressController::class, 'store']);
    Route::get('pickup-address/{id}', [App\Http\Controllers\AddressController::class, 'show']);

    // Payment Route
    Route::post('handle-payment', [App\Http\Controllers\PaymentController::class, 'handlePayment'])->name('make.payment');
    Route::get('cancel-payment', [App\Http\Controllers\PaymentController::class, 'paymentCancel'])->name('cancel.payment');
    Route::get('payment-success', [App\Http\Controllers\PaymentController::class, 'paymentSuccess'])->name('success.payment');
});


// Product Management Routes
Route::get('/product', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('product/{product}', [App\Http\Controllers\ProductController::class, 'show']);

Route::get('category', [App\Http\Controllers\CategoryController::class, 'index']);
Route::get('category/product/{category}', [App\Http\Controllers\CategoryController::class, 'product']);


//Search
Route::get('/search/{value}', [App\Http\Controllers\SearchController::class, 'search']);
Route::get('ad/active-ad', [App\Http\Controllers\AdController::class, 'activeAd']);

Route::get('/test', function () {
    return auth()->user();
});


Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendEmail']);
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPassword']);
