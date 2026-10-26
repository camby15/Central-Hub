<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\WidgetDemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect('/admin')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/invite/{token}', [AuthController::class, 'showInvite'])->name('invite.show');
    Route::post('/invite/{token}', [AuthController::class, 'acceptInvite'])->name('invite.store');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('/two-factor', [AuthController::class, 'showTwoFactor'])->name('two-factor.show');
    Route::post('/two-factor', [AuthController::class, 'storeTwoFactor'])->name('two-factor.store');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/status/{token}', [StatusController::class, 'show'])->name('status.show');
Route::post('/status/{token}/reply', [StatusController::class, 'reply'])->name('status.reply');

Route::get('/s/{slug}', [PortalController::class, 'show'])->name('portal.show');
Route::post('/s/{slug}/submit', [PortalController::class, 'submit'])->name('portal.submit');
Route::post('/s/{slug}/track', [PortalController::class, 'track'])->name('portal.track');

Route::get('/widget-demo', WidgetDemoController::class)->name('widget.demo');
