<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\TimetableController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WorkingHoursController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware(['auth:sanctum']);
});

Route::controller(UserController::class)->middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', 'showProfile');
    Route::post('/profile/update', 'updateProfile');
    Route::get('/student/groups', 'getStudentGroups');
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);

    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::post('/groups/{groupId}/students', [GroupController::class, 'addStudents']);
    Route::put('/groups/{groupId}', [GroupController::class, 'update']);
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']);

    Route::get('/working-hours',  [WorkingHoursController::class, 'index']);
    Route::put('/working-hours', [WorkingHoursController::class, 'bulkUpdate']);

    Route::apiResource('rooms', RoomController::class);

    Route::get('/available-rooms', [TimetableController::class, 'getAvailableRooms']);
    Route::post('/group-classes', [TimetableController::class, 'createGroupClass']);
    Route::get('/group-timetable/{groupId}', [TimetableController::class, 'getGroupTimetable']);
});
