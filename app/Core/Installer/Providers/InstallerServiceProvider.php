<?php

declare(strict_types=1);

namespace App\Core\Installer\Providers;

use App\Core\Installer\Admin\DatabaseAdminCreator;
use App\Core\Installer\Admin\NullInstallerBootstrapper;
use App\Core\Installer\Console\InstallCommand;
use App\Core\Installer\Contracts\AdminCreatorInterface;
use App\Core\Installer\Contracts\InstallerBootstrapperInterface;
use App\Core\Installer\InstallerManager;
use Illuminate\Support\ServiceProvider;

final class InstallerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AdminCreatorInterface::class, DatabaseAdminCreator::class);
        $this->app->singleton(InstallerBootstrapperInterface::class, NullInstallerBootstrapper::class);
        $this->app->singleton(InstallerManager::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}