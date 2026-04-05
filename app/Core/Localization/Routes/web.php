<?php

declare(strict_types=1);

use App\Core\Localization\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'localization.permission:view'])
    ->prefix('admin/localization')
    ->group(static function (): void {
        Route::get('/languages', [LanguageController::class, 'index'])
            ->name('localization.languages.index');

        Route::put('/languages/default-locale', [LanguageController::class, 'updateDefaultLocale'])
            ->middleware('localization.permission:update')
            ->name('localization.languages.default-locale.update');

        Route::post('/languages/upload', [LanguageController::class, 'upload'])
            ->middleware('localization.permission:install')
            ->name('localization.languages.upload');

        Route::delete('/languages/{code}', [LanguageController::class, 'destroy'])
            ->middleware('localization.permission:delete')
            ->name('localization.languages.destroy');
    });