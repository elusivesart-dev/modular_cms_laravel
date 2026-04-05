<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Users\Application\Http\Controllers\UserController;

Route::middleware(array_merge(config('users.middleware', ['web', 'auth']), ['role:super-admin,admin']))
    ->prefix(config('users.route_prefix', 'admin/users'))
    ->group(static function (): void {
        Route::get('/', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/{user}', [UserController::class, 'show'])
            ->name('users.show');

        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });