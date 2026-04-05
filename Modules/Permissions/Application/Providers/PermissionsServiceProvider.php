<?php

declare(strict_types=1);

namespace Modules\Permissions\Application\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Permissions\Application\Policies\PermissionPolicy;
use Modules\Permissions\Domain\Contracts\PermissionRepositoryInterface;
use Modules\Permissions\Infrastructure\Models\Permission;
use Modules\Permissions\Infrastructure\Repositories\PermissionRepository;

final class PermissionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../Config/permissions.php',
            'permissions'
        );

        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../Infrastructure/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../../UI/Resources/views', 'permissions');
        $this->loadTranslationsFrom(__DIR__ . '/../../UI/Resources/lang', 'permissions');
        $this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');

        Gate::policy(Permission::class, PermissionPolicy::class);
    }
}