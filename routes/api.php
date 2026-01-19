<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\OfficerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserInfoController;
use App\Http\Controllers\Api\DetailBookController;
use App\Http\Controllers\Api\BookProductController;
use App\Http\Controllers\Api\DetailBookProductController;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public routes (tidak perlu authentication)
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('packages', PackageController::class)->only(['index', 'show']);

// Protected routes (perlu authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth user info
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // User management
    Route::apiResource('users', UserController::class);
    Route::apiResource('user-info', UserInfoController::class);
    
    // Admin & Officer management
    Route::apiResource('admins', AdminController::class);
    Route::apiResource('officers', OfficerController::class);
    
    // Category, Product, Package management (protected write operations)
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::apiResource('packages', PackageController::class)->except(['index', 'show']);
    
    // Booking management
    Route::apiResource('books', BookController::class);
    Route::apiResource('detail-books', DetailBookController::class);
    Route::apiResource('book-products', BookProductController::class);
    Route::apiResource('detail-book-products', DetailBookProductController::class);
});
