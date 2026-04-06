<?php

declare(strict_types=1);

namespace Modules\Users\Application\Providers;

use App\Core\Auth\Contracts\AuthenticatableUserProviderInterface;
use App\Core\Installer\Contracts\AdminCreatorInterface;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Users\Application\Contracts\UserAdministrationWorkflowInterface;
use Modules\Users\Application\Contracts\UserProfileWorkflowInterface;
use Modules\Users\Application\Contracts\UserServiceInterface;
use Modules\Users\Application\Policies\UserPolicy;
use Modules\Users\Application\Services\InstallerAdminCreator;
use Modules\Users\Application\Services\UserAdministrationWorkflowService;
use Modules\Users\Application\Services\UserProfileWorkflowService;
use Modules\Users\Application\Services\UserService;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Infrastructure\Auth\UserAuthenticatableProvider;
use Modules\Users\Infrastructure\Models\User;
use Modules\Users\Infrastructure\Repositories\UserRepository;

final class UsersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../Config/users.php', 'users');

        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(AuthenticatableUserProviderInterface::class, UserAuthenticatableProvider::class);
        $this->app->singleton(UserAuthenticatableProvider::class);
        $this->app->singleton(UserServiceInterface::class, UserService::class);
        $this->app->singleton(UserService::class);
        $this->app->singleton(UserAdministrationWorkflowInterface::class, UserAdministrationWorkflowService::class);
        $this->app->singleton(UserAdministrationWorkflowService::class);
        $this->app->singleton(UserProfileWorkflowInterface::class, UserProfileWorkflowService::class);
        $this->app->singleton(UserProfileWorkflowService::class);
        $this->app->bind(AdminCreatorInterface::class, InstallerAdminCreator::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../../UI/Resources/views', 'users');
        $this->loadTranslationsFrom(__DIR__ . '/../../UI/Resources/lang', 'users');
        $this->loadMigrationsFrom(__DIR__ . '/../../Infrastructure/Database/Migrations');

        $this->app['auth']->provider('users-module', function (): EloquentUserProvider {
            return new EloquentUserProvider($this->app['hash'], User::class);
        });

        Gate::policy(User::class, UserPolicy::class);

        $this->publishes([
            __DIR__ . '/../../Config/users.php' => config_path('users.php'),
        ], 'users-config');
    }
}