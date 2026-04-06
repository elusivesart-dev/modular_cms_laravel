<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Settings\Application\Http\Middleware\ApplySystemSettings;
use Modules\Settings\Application\Policies\SettingPolicy;
use Modules\Settings\Application\Services\RuntimeSettingsApplier;
use Modules\Settings\Domain\Contracts\SettingRepositoryInterface;
use Modules\Settings\Infrastructure\Models\Setting;
use Modules\Settings\Infrastructure\Repositories\SettingRepository;

final class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../Config/settings.php',
            'settings'
        );

        require_once base_path('Modules/Settings/Application/Support/helpers.php');

        $this->app->singleton(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->singleton(RuntimeSettingsApplier::class);
    }

    public function boot(Router $router, RuntimeSettingsApplier $runtimeSettingsApplier): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../../UI/Resources/views', 'settings');
        $this->loadTranslationsFrom(__DIR__ . '/../../UI/Resources/lang', 'settings');
        $this->loadMigrationsFrom(__DIR__ . '/../../Infrastructure/Database/Migrations');

        Gate::policy(Setting::class, SettingPolicy::class);

        if (! $this->app->runningUnitTests()) {
            $runtimeSettingsApplier->applySystem();
        }

        $router->aliasMiddleware('system.settings', ApplySystemSettings::class);
    }
}