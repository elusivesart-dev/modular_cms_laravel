<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Permissions\Application\Http\Controllers\PermissionController;

Route::middleware(config('permissions.middleware', ['web', 'auth']))
    ->prefix(config('permissions.route_prefix', 'admin/permissions'))
    ->group(static function (): void {
        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('role:super-admin')
            ->name('permissions.index');

        Route::get('/create', [PermissionController::class, 'create'])
            ->middleware('role:super-admin')
            ->name('permissions.create');

        Route::post('/', [PermissionController::class, 'store'])
            ->middleware('role:super-admin')
            ->name('permissions.store');

        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])
            ->middleware('role:super-admin')
            ->name('permissions.edit');

        Route::put('/{permission}', [PermissionController::class, 'update'])
            ->middleware('role:super-admin')
            ->name('permissions.update');

        Route::delete('/{permission}', [PermissionController::class, 'destroy'])
            ->middleware('role:super-admin')
            ->name('permissions.destroy');
    });