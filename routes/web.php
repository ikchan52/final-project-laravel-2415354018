<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


// --- 1. Redirect Halaman Utama ---
Route::get('/', function () {
    return redirect('/login');
});

// --- 2. Halaman Publik (Tanpa Login) ---
Route::view('/login', 'auth.login')->name('login');
// Tambahkan rute POST untuk proses login
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'loginWeb']);

// --- 3. Halaman Terproteksi (Wajib Login) ---
// Kita pakai middleware 'auth' standar Laravel agar sinkron dengan Session
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    });

    // Halaman Manajemen (Khusus Admin/Staff)
    Route::middleware(['role:super_admin,staff'])->group(function () {
        Route::get('/customers', function () {
            return view('pages.customers.index');
        });
        Route::get('/services', function () {
            return view('pages.services.index');
        });
        Route::get('/subscriptions', function () {
            return view('pages.subscriptions.index');
        });
    });

    // Halaman Portal (Khusus Customer)
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/my-subscription', function () {
            return view('pages.customer-portal.index');
        });
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
