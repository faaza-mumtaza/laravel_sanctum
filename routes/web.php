<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Auth\Events\Logout;

Route::get('/', function () {
    return view('pages.auth.login');
});




Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('pages.dashboard');
    })->name('home');

    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
});

    Route::post('/register', [AuthController::class, 'register' ]);
    Route::post('/login', [AuthController::class, 'login' ]);
Route::middleware(['auth:sanctum'])->post('/Logout', [AuthController::class, 'logout']);
