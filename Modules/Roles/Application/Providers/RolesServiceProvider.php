<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Providers;

use App\Core\RBAC\Contracts\RoleCatalogInterface;
use App\Core\RBAC\Contracts\RoleSubjectServiceInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Roles\Application\Contracts\RoleAdministrationWorkflowInterface;
use Modules\Roles\Application\Http\Middleware\RoleMiddleware;
use Modules\Roles\Application\Policies\RolePolicy;
use Modules\Roles\Application\Services\CoreRoleCatalogService;
use Modules\Roles\Application\Services\CoreRoleSubjectService;
use Modules\Roles\Application\Services\RoleAdministrationWorkflowService;
use Modules\Roles\Domain\Contracts\RoleAssignmentRepositoryInterface;
use Modules\Roles\Domain\Contracts\RoleRepositoryInterface;
use Modules\Roles\Domain\Services\RoleAssignmentService;
use Modules\Roles\Infrastructure\Models\Role;
use Modules\Roles\Infrastructure\Repositories\EloquentRoleAssignmentRepository;
use Modules\Roles\Infrastructure\Repositories\EloquentRoleRepository;

final class RolesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../Config/roles.php', 'roles');

        $this->app->singleton(RoleRepositoryInterface::class, EloquentRoleRepository::class);
        $this->app->singleton(RoleAssignmentRepositoryInterface::class, EloquentRoleAssignmentRepository::class);
        $this->app->singleton(RoleAssignmentService::class);
        $this->app->singleton(RoleAdministrationWorkflowInterface::class, RoleAdministrationWorkflowService::class);
        $this->app->singleton(RoleAdministrationWorkflowService::class);
        $this->app->singleton(RoleCatalogInterface::class, CoreRoleCatalogService::class);
        $this->app->singleton(RoleSubjectServiceInterface::class, CoreRoleSubjectService::class);
        $this->app->singleton(CoreRoleCatalogService::class);
        $this->app->singleton(CoreRoleSubjectService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../../UI/Resources/views', 'roles');
        $this->loadTranslationsFrom(__DIR__ . '/../../UI/Resources/lang', 'roles');
        $this->loadMigrationsFrom(__DIR__ . '/../../Infrastructure/Database/Migrations');

        Gate::policy(Role::class, RolePolicy::class);

        Route::aliasMiddleware('role', RoleMiddleware::class);

        $this->publishes([
            __DIR__ . '/../../Config/roles.php' => config_path('roles.php'),
        ], 'roles-config');
    }
}