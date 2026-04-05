<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Settings\Application\Http\Controllers\SettingController;

Route::middleware(array_merge(config('settings.middleware', ['web', 'auth']), ['permission:settings.view']))
    ->prefix(config('settings.route_prefix', 'admin/settings'))
    ->group(static function (): void {
        Route::get('/', [SettingController::class, 'index'])
            ->name('settings.index');

        Route::get('/group/{group}', [SettingController::class, 'editGroup'])
            ->middleware('permission:settings.update')
            ->name('settings.group.edit');

        Route::put('/group/{group}', [SettingController::class, 'updateGroup'])
            ->middleware('permission:settings.update')
            ->name('settings.group.update');

        Route::post('/group/system/languages/upload', [SettingController::class, 'uploadLanguage'])
            ->middleware('permission:settings.update')
            ->name('settings.languages.upload');

        Route::delete('/group/system/languages/{code}', [SettingController::class, 'destroyLanguage'])
            ->middleware('permission:settings.update')
            ->name('settings.languages.destroy');
    });