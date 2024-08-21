<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware(['auth:sanctum']);
    Route::get('/user', 'userProfile')->middleware(['auth:sanctum']);
});

Route::controller(UserController::class)->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/users', 'index');
});

Route::controller(UserController::class)->middleware(['auth:sanctum'])->group(function () {
    Route::post('/profile/update', 'updateProfile');
});
