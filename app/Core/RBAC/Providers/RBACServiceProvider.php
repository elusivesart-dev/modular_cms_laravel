<?php

declare(strict_types=1);

namespace App\Core\RBAC\Providers;

use App\Core\RBAC\Contracts\PermissionManagerInterface;
use App\Core\RBAC\Contracts\RBACResolverInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use App\Core\RBAC\Managers\PermissionManager;
use App\Core\RBAC\Managers\RoleManager;
use App\Core\RBAC\Middleware\AccessControlMiddleware;
use App\Core\RBAC\Resolver\RBACResolver;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

final class RBACServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RoleManagerInterface::class, RoleManager::class);
        $this->app->singleton(PermissionManagerInterface::class, PermissionManager::class);
        $this->app->singleton(RBACResolverInterface::class, RBACResolver::class);
        $this->app->singleton(RBACResolver::class);
        $this->app->singleton(RoleManager::class);
        $this->app->singleton(PermissionManager::class);
    }

    public function boot(Router $router): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'rbac');

        $router->aliasMiddleware('permission', AccessControlMiddleware::class);
    }
}