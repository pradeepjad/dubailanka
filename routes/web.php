<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegistrationController;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/design-system', function () {
    return Inertia::render('DesignSystem');
});

Route::middleware('guest')->group(function () {
    Route::post('/register', [RegistrationController::class, 'store'])
        ->name('register.store');

    Route::post('/register/verify', [RegistrationController::class, 'verify'])
        ->name('register.verify');

    Route::post('/register/complete', [RegistrationController::class, 'complete'])
        ->name('register.complete');
});
