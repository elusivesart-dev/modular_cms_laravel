<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Audit\Application\Http\Controllers\AuditLogController;

Route::prefix('admin/audit')
    ->middleware(['web', 'auth', 'role:super-admin'])
    ->group(static function (): void {
        Route::get('/', [AuditLogController::class, 'index'])
            ->name('audit.index');

        Route::delete('/bulk-delete', [AuditLogController::class, 'bulkDelete'])
            ->name('audit.bulk-delete');

        Route::delete('/{audit_log}', [AuditLogController::class, 'destroy'])
            ->name('audit.destroy');
    });