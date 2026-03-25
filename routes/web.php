<?php

use App\Http\Controllers\FeesCronController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/fee-cron', [FeesCronController::class, 'store']);

Route::get('/privacy-policy/{code?}', function ($code = null) {
    $content = null;
    if ($code) {
        $library = \App\Models\Library::where('code', $code)->first();
        $content = $library?->privacy_policy;
    }
    return view('privacy', compact('content'));
})->name('privacy');

Route::get('/terms-conditions/{code?}', function ($code = null) {
    $content = null;
    if ($code) {
        $library = \App\Models\Library::where('code', $code)->first();
        $content = $library?->terms_conditions;
    }
    return view('terms', compact('content'));
})->name('terms');

Route::get('/disclaimer/{code?}', function ($code = null) {
    $content = null;
    if ($code) {
        $library = \App\Models\Library::where('code', $code)->first();
        $content = $library?->disclaimer;
    }
    return view('disclaimer', compact('content'));
})->name('disclaimer');

Route::get('/admin/login', function () {
    return view('app');
})->name('login');

Route::get('/admin/{any?}', function () {
    return view('app');
})->where('any', '.*');
