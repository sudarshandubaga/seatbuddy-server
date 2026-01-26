<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\SlotPackageController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionHistoryController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('users', \App\Http\Controllers\UserController::class);

    Route::apiResource('libraries', LibraryController::class);
    Route::apiResource('subscription-plans', SubscriptionPlanController::class);
    Route::apiResource('subscription-histories', SubscriptionHistoryController::class);
});


// Library App Android Routes
Route::group(['prefix' => 'library-app'], function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);

        Route::post('/attendance', [AttendanceController::class, 'store']); // mark attendance
        // Route::get('/attendance', [AttendanceController::class, 'index']); // library data
        Route::get('/attendance/show', [AttendanceController::class, 'show']); // student data

        Route::apiResources([
            'slot-package' => SlotPackageController::class,
            'student' => StudentController::class
        ]);

    });
});