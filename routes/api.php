<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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
    $data = $request->user();
    $user = User::where('_id', auth()->user()->_id)->first();
    $data['following'] = $user->followings->followings;
    $data['follower'] = $user->followers->followers;
    $data['success'] = true;
    $data['roles'] = $user->getRoleNames();
    return $data;
});

Route::middleware(['auth:sanctum'])->group(function () {
    //Category Routes

    Broadcast::routes();

    Route::resource('category', App\Http\Controllers\CategoryController::class, ['except' => ['index']]);
    Route::get('category/attribute/{category}', [App\Http\Controllers\CategoryController::class, 'attribute']);

    Route::resource('product', App\Http\Controllers\ProductController::class, ['except' => ['index', 'show', 'update', 'edit']]);
    Route::post('product-status/{product}', [App\Http\Controllers\ProductController::class, 'changeStatus']);
    Route::get('user-product', [App\Http\Controllers\ProductController::class, 'userProduct']);
    Route::get('all-product', [App\Http\Controllers\ProductController::class, 'allProducts']);

    //orders
    // Route::post('/order', [App\Http\Controllers\OrderController::class, 'placeOrder']);
    Route::get('/seller-order', [App\Http\Controllers\OrderController::class, 'getSellerOrder']);
    Route::get('/user-order', [App\Http\Controllers\OrderController::class, 'getUserOrder']);
    Route::get('/sub-order/{order}', [App\Http\Controllers\OrderController::class, 'subOrder']);

    //ads
    Route::post('ad', [App\Http\Controllers\AdController::class, 'store']);
    Route::get('ad', [App\Http\Controllers\AdController::class, 'index']);
    Route::post('ad/update-ad', [App\Http\Controllers\AdController::class, 'updateAd']);

    //users resource
    Route::resource('/users', App\Http\Controllers\UserController::class);
    Route::get('users/username/{user}', [App\Http\Controllers\UserController::class, 'username']);

    //Shipping Routes
    Route::post('validate-address', [App\Http\Controllers\AddressController::class, 'validateAddress']);

    Route::post('pickup-address', [App\Http\Controllers\AddressController::class, 'store']);
    Route::get('pickup-address/{id}', [App\Http\Controllers\AddressController::class, 'show']);

    // Payment Route
    Route::post('handle-payment', [App\Http\Controllers\PaymentController::class, 'handlePayment'])->name('make.payment');
    Route::get('payment', [App\Http\Controllers\PaymentController::class, 'index']);

    //social Routes
    Route::post('follow', [App\Http\Controllers\FollowingController::class, 'follow']);
    Route::post('unfollow', [App\Http\Controllers\FollowingController::class, 'unfollow']);

    Route::post('following', [App\Http\Controllers\FollowingController::class, 'following']);
    Route::get('follower', [App\Http\Controllers\FollowerController::class, 'follower']);

    Route::post('/change-password', [App\Http\Controllers\AuthController::class, 'changePassword']);

    Route::get('/feed', [App\Http\Controllers\FeedController::class, 'feed']);

    //reviews
    Route::get('/review', [App\Http\Controllers\ReviewController::class, 'index']);
    Route::get('/reviews/seller', [App\Http\Controllers\ReviewController::class, 'sellerReviews']);
    Route::post('/review', [App\Http\Controllers\ReviewController::class, 'store']);
    Route::delete('/review/{review}', [App\Http\Controllers\ReviewController::class, 'destroy']);
    Route::get('/unreviewed', [App\Http\Controllers\ReviewController::class, 'unreviewed']);


    Route::get('/product/verify/{product}', [App\Http\Controllers\ProductController::class, 'verify']);


    //sub order
    Route::get('/suborder/{suborder}', [App\Http\Controllers\SubOrderController::class, 'show']);

    //get all available roles
    Route::get('/roles', [App\Http\Controllers\RoleController::class, 'index']);

    //comments
    Route::get('/comments/seller', [App\Http\Controllers\CommentController::class, 'sellerComments']);
    Route::post('/comments/{comment}/reply', [App\Http\Controllers\CommentController::class, 'reply']);
    Route::resource('comments', App\Http\Controllers\CommentController::class);
    Route::get('/comments/verify/{comment}', [App\Http\Controllers\CommentController::class, 'verify']);

    Route::get('/dashboard/admin/data', [App\Http\Controllers\DashboardController::class, 'adminData']);
    Route::get('/dashboard/seller/data', [App\Http\Controllers\DashboardController::class, 'sellerData']);
    Route::get('/dashboard/customer/data', [App\Http\Controllers\DashboardController::class, 'customerData']);
});

Route::get('/reviews/{product}', [App\Http\Controllers\ReviewController::class, 'reviews']);

// Product Management Routes
Route::get('/product', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('/product/top-selling', [App\Http\Controllers\ProductController::class, 'topSelling']);
Route::get('product/{product}', [App\Http\Controllers\ProductController::class, 'show']);
Route::get('product/similar/{product}', [App\Http\Controllers\ProductController::class, 'similar']);

Route::get('category', [App\Http\Controllers\CategoryController::class, 'index']);
Route::post('category/product', [App\Http\Controllers\CategoryController::class, 'product']);
Route::get('category/product/{category}', [App\Http\Controllers\CategoryController::class, 'categoryProduct']);

//comments
Route::get('comments/product/{productId}', [App\Http\Controllers\CommentController::class, 'productComments']);

//Search
Route::post('/search', [App\Http\Controllers\SearchController::class, 'filter']);
Route::get('ad/active-ad', [App\Http\Controllers\AdController::class, 'activeAd']);


//Route::get('/test', [App\Http\Controllers\ProductController::class, 'topSelling']);
Route::get('/test', function (Request $request) {
    return \App\Models\Product::where('productName' , 'like', '%p%r%o%d%u%c%t%n%a%m%e%')->get();
});

Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendEmail']);
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPassword']);
