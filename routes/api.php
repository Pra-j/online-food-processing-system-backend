<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KitchenLogController;
use App\Models\Order;

// ----------------------------------------------------
// AUTHENTICATION ROUTES
// ----------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// ----------------------------------------------------
// CATEGORY ROUTES
// ----------------------------------------------------
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/statistics', [CategoryController::class, 'categoryStatsOverAll']);
Route::get('/categories/hourly/stats', [CategoryController::class, 'categoryHourlyStats']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store'])->middleware('auth:sanctum');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/categories/stats/{id}', [CategoryController::class, 'categoryStats']);
Route::get('/categories/chart/data', [CategoryController::class, 'categoryChartData']);

// ----------------------------------------------------
// PRODUCT ROUTES
// ----------------------------------------------------
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/products/status/{id}', [ProductController::class, 'productsStatus']);
Route::get('/products/sales/summary', [ProductController::class, 'productSalesSummary']);

// PRODUCT RECOMMENDATION ROUTE.
Route::get('/products/{id}/recommendations', [ProductController::class, 'productRecommendations']);


// ----------------------------------------------------
// OFFER ROUTES
// ----------------------------------------------------
Route::get('/offers', [OfferController::class, 'index']);
Route::get('/offers/{id}', [OfferController::class, 'show']);
Route::post('/offers', [OfferController::class, 'store']);
Route::put('/offers/{id}', [OfferController::class, 'update']);
Route::delete('/offers/{id}', [OfferController::class, 'destroy']);

// ----------------------------------------------------
// ORDER ROUTES
// ----------------------------------------------------

Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/status/{id}', [OrderController::class, 'orderStats'])->where('id', '[0-9]+');
Route::get('/orders/status/{status}', [OrderController::class, 'getOrderByStatus'])->where('status', '[a-zA-Z]+');
Route::get('/orders/stats',  [OrderController::class, 'orderStatisticsOverall']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
Route::get('/orders/monthly/data', [OrderController::class, 'monthlyOrderStats']);
Route::put('/orders/{id}/status', [OrderController::class, 'updateOrderStatus']);



// ----------------------------------------------------
// ORDER ITEM ROUTES
// ----------------------------------------------------
Route::get('/orders/{orderId}/items', [OrderItemController::class, 'index']);
Route::post('/orders/{orderId}/items', [OrderItemController::class, 'store']);
Route::put('/orders/{orderId}/items/{id}', [OrderItemController::class, 'update']);
Route::delete('/orders/{orderId}/items/{id}', [OrderItemController::class, 'destroy']);

// ----------------------------------------------------
// EMPLOYEE ROUTES
// ----------------------------------------------------

Route::get('/employees', [EmployeeController::class, 'index']);
Route::get('/employees/{id}', [EmployeeController::class, 'show']);
Route::post('/employees', [EmployeeController::class, 'store']);
Route::put('/employees/{id}', [EmployeeController::class, 'update']);
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);


// ----------------------------------------------------
// KITCHEN LOG ROUTES
// ----------------------------------------------------

Route::get('/kitchen/logs', [KitchenLogController::class, 'index']);
Route::get('/kitchen/logs/{id}', [KitchenLogController::class, 'show']);
Route::post('/kitchen/logs', [KitchenLogController::class, 'store']);
Route::put('/kitchen/logs/{id}', [KitchenLogController::class, 'update']);
Route::delete('/kitchen/logs/{id}', [KitchenLogController::class, 'destroy']);
