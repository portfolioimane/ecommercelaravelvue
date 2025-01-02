<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Full Controller Path Imports
use App\Http\Controllers\Api\Customer\AuthController;
use App\Http\Controllers\Api\Customer\ProductController;
use App\Http\Controllers\Api\Customer\CartController;
use App\Http\Controllers\Api\Customer\OrderController;


use App\Http\Controllers\Api\Customer\ResetPasswordController;

use App\Http\Controllers\Api\Backend\ProductsController as BackendProductsController;
use App\Http\Controllers\Api\Backend\CategoriesController as BackendCategoriesController;
use App\Http\Controllers\Api\Backend\PaymentSettingController as BackendPaymentSettingController;






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
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::get('/myorders', [OrderController::class, 'myorders']);

});   


Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin routes
    Route::apiResource('products', BackendProductsController::class);
    Route::apiResource('categories', BackendCategoriesController::class);

  Route::prefix('paymentsetting')->group(function () {
    Route::put('/update', [BackendPaymentSettingController::class, 'update']); // Update payment provider settings
    Route::get('/', [BackendPaymentSettingController::class, 'getSettings']); // Fetch payment provider settings
});


});







