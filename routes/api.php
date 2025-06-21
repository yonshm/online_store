<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;

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

// Public routes (no authentication required)
Route::post('/auth/register', [AuthApiController::class, 'register']);
Route::post('/auth/login', [AuthApiController::class, 'login']);

// Product routes (public)
Route::get('/products', [ProductApiController::class, 'getProducts']);
Route::get('/products/{productId}', [ProductApiController::class, 'getProductDetails']);
Route::get('/categories', [ProductApiController::class, 'getCategories']);
Route::get('/categories/{categoryId}/products', [ProductApiController::class, 'getProductsByCategory']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // User profile routes
    Route::get('/auth/profile', [AuthApiController::class, 'profile']);
    Route::put('/auth/profile', [AuthApiController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);

    // Order tracking routes
    Route::get('/orders', [OrderApiController::class, 'getUserOrders']);
    Route::get('/orders/{orderId}', [OrderApiController::class, 'getOrderDetails']);
    Route::get('/orders/{orderId}/track', [OrderApiController::class, 'trackOrder']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
