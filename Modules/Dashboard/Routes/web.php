<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Application\Http\Controllers\DashboardController;

Route::prefix('admin')->group(static function (): void {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');
});