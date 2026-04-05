<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Media\Application\Http\Controllers\MediaController;

Route::middleware(array_merge(
    config('media.middleware', ['web', 'auth']),
    ['role:super-admin,admin', 'permission:media.view']
))
    ->prefix(config('media.route_prefix', 'admin/media'))
    ->group(static function (): void {
        Route::get('/', [MediaController::class, 'index'])
            ->name('media.index');

        Route::post('/', [MediaController::class, 'store'])
            ->middleware('permission:media.create')
            ->name('media.store');

        Route::delete('/{media}', [MediaController::class, 'destroy'])
            ->middleware('permission:media.delete')
            ->name('media.destroy');
    });