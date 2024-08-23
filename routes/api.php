<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware(['auth:sanctum']);
});

Route::controller(UserController::class)->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/users', 'index');
});

Route::controller(UserController::class)->middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', 'showProfile');
    Route::post('/profile/update', 'updateProfile');
    Route::get('/student/groups', 'getStudentGroups');
});


Route::controller(GroupController::class)->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/groups', 'index');  // Get all groups  
    Route::post('/groups', 'store'); // Create group
    Route::post('/groups/{groupId}/students', 'addStudents'); // Add students to group
    Route::put('/groups/{groupId}', 'update'); // Update group (assign teachers, change name)
    Route::delete('/groups/{id}', 'destroy');
});
