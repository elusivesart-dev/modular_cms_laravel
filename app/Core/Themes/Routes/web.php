<?php

declare(strict_types=1);

use App\Core\Themes\Http\Controllers\ThemeAssetController;
use App\Core\Themes\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/themes')
    ->middleware(['auth', 'role:super-admin'])
    ->group(static function (): void {
        Route::get('/', [ThemeController::class, 'index'])
            ->name('themes.index');

        Route::put('/', [ThemeController::class, 'update'])
            ->name('themes.update');
    });

Route::get('/themes/{group}/{theme}/{path}', [ThemeAssetController::class, 'show'])
    ->where('path', '.*')
    ->name('theme.asset');