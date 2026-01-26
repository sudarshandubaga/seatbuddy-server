<?php

use App\Http\Controllers\FeesCronController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/fee-cron', [FeesCronController::class, 'store']);

Route::get('/admin/{any?}', function () {
    return view('app');
})->where('any', '.*');
