<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Seller\BusinessEntityController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/design-system', fn () => Inertia::render('DesignSystem'));

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');
    Route::post('/register/verify', [RegistrationController::class, 'verify'])->name('register.verify');
    Route::post('/register/resend', [RegistrationController::class, 'resend'])->name('register.resend');
    Route::post('/register/cancel', [RegistrationController::class, 'cancel'])->name('register.cancel');
    Route::post('/register/complete', [RegistrationController::class, 'complete'])->name('register.complete');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::post('/login/otp', [LoginController::class, 'sendOtp'])->name('login.otp.send');
    Route::post('/login/otp/verify', [LoginController::class, 'verifyOtp'])->name('login.otp.verify');
    Route::post('/login/otp/resend', [LoginController::class, 'resendOtp'])->name('login.otp.resend');
    Route::post('/login/otp/cancel', [LoginController::class, 'cancelOtp'])->name('login.otp.cancel');

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'send'])->name('password.otp.send');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/forgot-password/resend', [PasswordResetController::class, 'resend'])->name('password.otp.resend');
    Route::post('/forgot-password/cancel', [PasswordResetController::class, 'cancel'])->name('password.otp.cancel');
});

Route::middleware('auth')->group(function () {
    Route::get('/account/suspended', fn () => Inertia::render('Auth/Suspended'))->name('account.suspended');
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::prefix('seller')->name('seller.')->group(function () {
        Route::get('/start', [BusinessEntityController::class, 'index'])->name('start');
        Route::get('/businesses/create', [BusinessEntityController::class, 'create'])->name('businesses.create');
        Route::post('/businesses', [BusinessEntityController::class, 'store'])->name('businesses.store');
    });
});
