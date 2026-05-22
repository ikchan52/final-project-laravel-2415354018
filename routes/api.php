<?php

use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

// --- Modul Services ---
Route::apiResource("services", ServiceController::class);
Route::patch("services/{service}/activate", [ServiceController::class, "activate"]);
Route::patch("services/{service}/deactivate", [ServiceController::class, "deactivate"]);

// --- Modul Customers ---
Route::apiResource("customers", CustomerController::class);

// --- Modul Subscriptions ---
Route::apiResource("subscriptions", SubscriptionController::class);
