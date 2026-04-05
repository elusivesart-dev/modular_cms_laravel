<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Auth\Application\Http\Controllers\AuthController;

Route::middleware(config('auth-module.middleware', ['web']))
    ->group(static function (): void {
        Route::middleware('guest')->group(static function (): void {
            Route::get('/login', [AuthController::class, 'showLoginForm'])
                ->name('login');

            Route::post('/login', [AuthController::class, 'login'])
                ->name('login.attempt');
        });

        Route::middleware('auth')->group(static function (): void {
            Route::post('/logout', [AuthController::class, 'logout'])
                ->name('logout');

            Route::get('/logout', static function () {
                return view('auth-module::auth.logout');
            })->name('logout.fallback');
        });
    });