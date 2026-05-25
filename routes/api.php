<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes - ERP Management System
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC ROUTES ---
// Pintu masuk utama tanpa token
Route::post('/login', [AuthController::class, 'login']);

// --- 2. PROTECTED ROUTES (Sanctum Authenticated) ---
Route::middleware('auth:sanctum')->group(function () {

    // A. Common Routes (Bisa diakses semua Role yang sudah Login)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // B. ADMIN & STAFF ONLY (Role-Based Access Control)
    // Hanya user dengan role 'super_admin' atau 'staff' yang bisa mengelola data
    Route::middleware('role:super_admin,staff')->group(function () {

        // Resource Management
        Route::apiResource('users', UserController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('services', ServiceController::class);
        Route::apiResource('subscriptions', SubscriptionController::class);

        // Custom Service Actions
        Route::patch("services/{service}/activate", [ServiceController::class, "activate"]);
        Route::patch("services/{service}/deactivate", [ServiceController::class, "deactivate"]);
    });

    // C. CUSTOMER ONLY
    // Akses terbatas hanya untuk melihat data milik sendiri
    Route::middleware('role:customer')->group(function () {
        Route::get('/my-subscription', [SubscriptionController::class, 'mySub']);
        // Tambahkan rute portal pelanggan lainnya di sini nanti
    });

});
