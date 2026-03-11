<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\EnquiryController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\SlotPackageController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\FeesCronController;
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
Route::get('/fees-cron', [FeesCronController::class, 'store']);

Route::group(['prefix' => 'library-app'], function () {
    Route::post('/login', [LoginController::class, 'doLogin']);
    Route::post('/register', [\App\Http\Controllers\Api\RegistrationController::class, 'register']);
    Route::post('/verify-payment', [\App\Http\Controllers\Api\RegistrationController::class, 'verifyPayment']);
    Route::post('/check-uniqueness', [\App\Http\Controllers\Api\RegistrationController::class, 'checkUniqueness']);
    Route::post('/forgot-password', [LoginController::class, 'forgotPassword']);
    Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);

        Route::post('/purchase-subscription', [\App\Http\Controllers\Api\SubscriptionController::class, 'purchase']);
        Route::post('/verify-subscription', [\App\Http\Controllers\Api\SubscriptionController::class, 'verify']);


        Route::post('/startup', [HomeController::class, 'startup']);
        Route::get('/dashboard', [HomeController::class, 'dashboard']);
        Route::post('/attendance', [AttendanceController::class, 'store']); // mark attendance
        Route::get('/attendance/show', [AttendanceController::class, 'show']); // student data
        Route::get('/attendance/history', [AttendanceController::class, 'history']);
        Route::post('/enquiry/bulk-destroy', [EnquiryController::class, 'bulkDestroy']);
        Route::post('/seat/unallocate', [SeatController::class, 'unallocate']);

        // Profile Routes
        Route::post('/profile/update', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::post('/profile/change-password', [\App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
        Route::post('/profile/update-avatar', [\App\Http\Controllers\Api\ProfileController::class, 'updateAvatar']);
        Route::post('/profile/update-logo', [\App\Http\Controllers\Api\ProfileController::class, 'updateLogo']);

        Route::post('/student/{id}/toggle-status', [\App\Http\Controllers\Api\StudentController::class, 'toggleStatus']);

        Route::apiResources([
            'slot-package' => SlotPackageController::class,
            'student' => StudentController::class,
            'enquiry' => EnquiryController::class,
            'seat' => SeatController::class,
            'fee' => FeeController::class,
            'expense' => \App\Http\Controllers\Api\ExpenseController::class,
            'notification' => \App\Http\Controllers\Api\NotificationController::class,
        ]);
    });
});