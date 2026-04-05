<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Roles\Application\Http\Controllers\RoleController;

Route::middleware(config('roles.middleware', ['web', 'auth']))
    ->prefix(config('roles.route_prefix', 'admin/roles'))
    ->group(static function (): void {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('role:super-admin')
            ->name('roles.index');

        Route::get('/create', [RoleController::class, 'create'])
            ->middleware('role:super-admin')
            ->name('roles.create');

        Route::post('/', [RoleController::class, 'store'])
            ->middleware('role:super-admin')
            ->name('roles.store');

        Route::get('/{role}', [RoleController::class, 'show'])
            ->middleware('role:super-admin')
            ->name('roles.show');

        Route::get('/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('role:super-admin')
            ->name('roles.edit');

        Route::put('/{role}', [RoleController::class, 'update'])
            ->middleware('role:super-admin')
            ->name('roles.update');

        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->middleware('role:super-admin')
            ->name('roles.destroy');
    });