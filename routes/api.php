<?php

use App\Http\Controllers\Api\V1\ReplyController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Middleware\AuthenticateSite;
use App\Http\Middleware\VerifyHmacSignature;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::middleware([
        AuthenticateSite::class.':tickets:create',
        VerifyHmacSignature::class,
        'throttle:site-api',
    ])->post('tickets', [TicketController::class, 'store']);

    Route::middleware([
        AuthenticateSite::class.':tickets:read',
        VerifyHmacSignature::class,
        'throttle:site-api',
    ])->get('tickets', [TicketController::class, 'index']);

    Route::middleware([
        AuthenticateSite::class.':tickets:read',
        VerifyHmacSignature::class,
        'throttle:site-api',
    ])->get('tickets/{number}', [TicketController::class, 'show']);

    Route::middleware([
        AuthenticateSite::class.':tickets:reply',
        VerifyHmacSignature::class,
        'throttle:site-api',
    ])->post('tickets/{number}/replies', [ReplyController::class, 'store']);
});
