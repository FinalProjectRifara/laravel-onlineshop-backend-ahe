<?php

use App\Http\Controllers\Api\ProductController;
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

// Register
Route::post("/register", [\App\Http\Controllers\Api\AuthController::class, 'register']);

// Logout
Route::post("/logout", [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware("auth:sanctum");

// Login
Route::post("/login", [\App\Http\Controllers\Api\AuthController::class, 'login']);

// Category
Route::get("/categories", [\App\Http\Controllers\Api\CategoryController::class, 'index']);

// Products
Route::get("/products", [\App\Http\Controllers\Api\ProductController::class, 'index']);

// Address apiResource dibuatkan crudnya
Route::apiResource("/addresses", \App\Http\Controllers\Api\AddressController::class)->middleware("auth:sanctum");

// Order
Route::post("/order", [\App\Http\Controllers\Api\OrderController::class, 'order'])->middleware("auth:sanctum");

// Callback
Route::post('/callback', [\App\Http\Controllers\Api\CallbackController::class, 'callback']);

// Check Status Order by id Order
Route::get('/order/status/{id}', [\App\Http\Controllers\Api\OrderController::class, 'checkStatusOrder'])->middleware("auth:sanctum");

// Update fcm_id
Route::post('/update-fcm', [\App\Http\Controllers\Api\AuthController::class, 'updateFcmId'])->middleware("auth:sanctum");

// Get all order by user
Route::get('/orders', [\App\Http\Controllers\Api\OrderController::class, 'getOrderByUser'])->middleware("auth:sanctum");

// get order by id
Route::get('/order/{id}', [\App\Http\Controllers\Api\OrderController::class, 'getOrderById'])->middleware("auth:sanctum");

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
