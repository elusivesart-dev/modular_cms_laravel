<?php

declare(strict_types=1);

namespace Modules\Dashboard\Application\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            base_path('Modules/Dashboard/UI/Resources/views'),
            'dashboard'
        );

        Route::middleware(['web', 'auth', 'role:super-admin,admin'])
            ->group(base_path('Modules/Dashboard/Routes/web.php'));
    }
}