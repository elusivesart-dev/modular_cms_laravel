<?php

declare(strict_types=1);

namespace Modules\Auth\Application\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Application\Contracts\AuthServiceInterface;
use Modules\Auth\Application\Services\AuthService;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../Config/auth.php', 'auth-module');

        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(AuthService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../../UI/Resources/views', 'auth-module');
        $this->loadTranslationsFrom(__DIR__ . '/../../UI/Resources/lang', 'auth-module');

        $this->publishes([
            __DIR__ . '/../../Config/auth.php' => config_path('auth-module.php'),
        ], 'auth-module-config');
    }
}