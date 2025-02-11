<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('auth/register', RegisterController::class)->name('auth.register');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
