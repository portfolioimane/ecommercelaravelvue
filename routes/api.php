<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Full Controller Path Imports
use App\Http\Controllers\Api\Customer\AuthController;
use App\Http\Controllers\Api\Customer\ProductController;
use App\Http\Controllers\Api\Customer\CartController;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\Customer\KeysController;
use App\Http\Controllers\Api\Customer\ReviewController; 


use App\Http\Controllers\Api\Customer\ResetPasswordController;

use App\Http\Controllers\Api\Backend\ProductsController as BackendProductsController;
use App\Http\Controllers\Api\Backend\CategoriesController as BackendCategoriesController;
use App\Http\Controllers\Api\Backend\OrdersController as BackendOrdersController;
use App\Http\Controllers\Api\Backend\PaymentSettingController as BackendPaymentSettingController;

use App\Http\Controllers\Api\Backend\HomePageHeaderController as BackendHomePageHeaderController;

use App\Http\Controllers\Api\Backend\VariantCombinationController;
use App\Http\Controllers\Api\Backend\ProductVariantController;
use App\Http\Controllers\Api\Backend\ReviewController as BackendReviewController; 
use App\Http\Controllers\Api\Backend\UsersController;

use App\Http\Controllers\Api\Backend\GeneralCustomizeController;


use App\Http\Controllers\Api\Customer\WishlistController;


Route::middleware('auth:sanctum')->group(function () {
Route::get('/wishlist', [WishlistController::class, 'index']);
Route::post('/wishlist', [WishlistController::class, 'store']);
Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);
});

Route::get('/store/products/featured', [ProductController::class, 'getFeaturedProducts']);
Route::get('/store/categories', [BackendCategoriesController::class, 'index']);

// routes/api.php





// routes/api.php


use App\Http\Controllers\Api\Backend\VariantController;
use App\Http\Controllers\Api\Backend\VariantValueController;


// routes/api.php


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);
    Route::post('/products/{productId}/reviews', [ReviewController::class, 'store']);
});


Route::get('reviews/latest-featured', [ReviewController::class, 'latestFeaturedReviews']);

Route::get('/homepage-header', [BackendHomePageHeaderController::class, 'getHeader']);

Route::post('/password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset']);
// Authentication Routes



Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getUser']);
// Public Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


// Product Routes
Route::prefix('store')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/{productId}/variants', [ProductController::class, 'getProductVariants']);
});

// Cart Routes
// Routes for authenticated users
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'viewAuthenticatedCart']);  // View cart for authenticated users
    Route::post('/add', [CartController::class, 'addToAuthenticatedCart']);  // Add product to cart for authenticated users

});

// Routes for guest users (no auth required)
Route::prefix('cartguest')->group(function () {
    Route::get('/', [CartController::class, 'viewGuestCart']);  // View cart for guest users
    Route::post('/add', [CartController::class, 'addToGuestCart']);  // Add product to cart for guest users
});

Route::prefix('cart')->group(function () {
    Route::post('/remove', [CartController::class, 'remove']);  // Remove product from cart for guest users
    Route::post('/update', [CartController::class, 'update']);  // Update product quantity for guest users
});



// Order Routes
Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/create', [OrderController::class, 'create']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::post('/create-stripe-payment', [OrderController::class, 'createStripePayment']);
    Route::post('/confirm-paypal-payment', [OrderController::class, 'confirmPaypalPayment']);



});


 
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/stripe/public-key', [KeysController::class, 'getStripePublicKey']);
    Route::get('/paypal/public-key', [KeysController::class, 'getPaypalPublicKey']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::get('/myorders', [OrderController::class, 'myorders']);


});   


Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin routes
    Route::apiResource('products', BackendProductsController::class);
    Route::apiResource('categories', BackendCategoriesController::class);
    
    Route::put('/products/{productId}/toggle-featured', [BackendProductsController::class, 'toggleFeatured']);

Route::get('product/{productId}/productvariants', [ProductVariantController::class, 'index']);
Route::put('productvariantupdate/{id}', [ProductVariantController::class, 'update']);
Route::delete('productvariant/{id}', [ProductVariantController::class, 'destroy']);


    Route::post('/variant-combinations/update', [VariantCombinationController::class, 'updateAllCombinations']);
    // In routes/api.php
    Route::get('/existing-combination-values/{productId}', [VariantCombinationController::class, 'getExistingCombinationValues']);


    
       Route::get('/variants', [VariantController::class, 'index']);
Route::post('/variants', [VariantController::class, 'store']);
Route::delete('/variants/{id}', [VariantController::class, 'destroy']); // Delete a variant
Route::get('/variants/{id}', [VariantController::class, 'show']);


// Variant Values Routes
Route::get('/variant-values', [VariantValueController::class, 'index']);
Route::post('/variant-values', [VariantValueController::class, 'store']);
Route::delete('/variant-values/{id}', [VariantValueController::class, 'destroy']); // Delete a variant value
    

    Route::get('/orders', [BackendOrdersController::class, 'index']);
    
    // Get details of a specific order
    Route::get('/orders/{orderId}', [BackendOrdersController::class, 'show']);
    
    // Update the status of an order
    Route::put('/orders/{orderId}/status', [BackendOrdersController::class, 'updateStatus']);



Route::prefix('users')->group(function () {
    Route::get('/', [UsersController::class, 'index']);
    Route::get('/current-user', [UsersController::class, 'currentUser']);
    Route::get('/customers', [UsersController::class, 'customers']);
    Route::post('/', [UsersController::class, 'store']);
    Route::put('/{user}', [UsersController::class, 'update']);
    Route::delete('/{user}', [UsersController::class, 'destroy']);
});



      Route::prefix('reviews')->group(function () {
    Route::get('/', [BackendReviewController::class, 'index']);
    Route::put('/{id}', [BackendReviewController::class, 'update']);
    Route::put('/{id}/feature', [BackendReviewController::class, 'feature']);
    Route::delete('/{id}', [BackendReviewController::class, 'destroy']);
    Route::post('/', [BackendReviewController::class, 'store']);
      });
    
        Route::prefix('customize')->group(function () {
        Route::put('/homepage-header', [BackendHomePageHeaderController::class, 'update']);
        Route::get('/homepage-header', [BackendHomePageHeaderController::class, 'getHeader']);
       
        });


  Route::prefix('paymentsetting')->group(function () {
    Route::put('/update', [BackendPaymentSettingController::class, 'update']); // Update payment provider settings
    Route::get('/', [BackendPaymentSettingController::class, 'getSettings']); // Fetch payment provider settings
});


Route::prefix('general-customizes')->group(function () {
    Route::get('/', [GeneralCustomizeController::class, 'index']);
    Route::post('/upload-logo', [GeneralCustomizeController::class, 'uploadLogo']);
    Route::post('/update-or-create', [GeneralCustomizeController::class, 'updateOrCreate']);

});



});







