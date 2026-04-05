<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Application\Kernel;
//use App\Core\Localization\Providers\LocalizationServiceProvider;
use Illuminate\Support\ServiceProvider;

final class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //$this->app->register(LocalizationServiceProvider::class);

        $this->app->singleton(Kernel::class);

        $this->app->afterResolving(Kernel::class, static function (Kernel $kernel): void {
            $kernel->boot();
        });
    }

    public function boot(): void
    {
    }
}