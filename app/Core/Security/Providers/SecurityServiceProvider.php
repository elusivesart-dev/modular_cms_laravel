<?php

declare(strict_types=1);

namespace App\Core\Security\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\Security\Authentication\AuthenticationManager;

final class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthenticationManager::class);
    }

    public function boot(): void
    {
    }
}