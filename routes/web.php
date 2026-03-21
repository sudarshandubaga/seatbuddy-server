<?php

use App\Http\Controllers\FeesCronController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/fee-cron', [FeesCronController::class, 'store']);

Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms-conditions', function () {
    return view('terms');
})->name('terms');

Route::get('/admin/login', function () {
    return view('app');
})->name('login');

Route::get('/admin/{any?}', function () {
    return view('app');
})->where('any', '.*');
