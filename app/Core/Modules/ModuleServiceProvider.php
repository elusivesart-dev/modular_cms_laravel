<?php

declare(strict_types=1);

namespace App\Core\Modules;

use App\Core\Modules\Activator\ModuleActivator;
use App\Core\Modules\Config\ModuleConfigLoader;
use App\Core\Modules\Deactivator\ModuleDeactivator;
use App\Core\Modules\Dependency\ModuleDependencyResolver;
use App\Core\Modules\Discovery\ModuleDiscovery;
use App\Core\Modules\Lifecycle\ModuleLifecycleManager;
use App\Core\Modules\Loader\ModuleLoader;
use App\Core\Modules\Migration\ModuleMigrationRunner;
use App\Core\Modules\Registry\ModuleRegistry;
use App\Core\Modules\Seeder\ModuleSeederRunner;
use App\Core\Modules\Uninstaller\ModuleUninstaller;
use App\Core\Modules\Updater\ModuleUpdater;
use App\Core\Modules\Versioning\ModuleVersionManager;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleRegistry::class);
        $this->app->singleton(ModuleDiscovery::class);
        $this->app->singleton(ModuleDependencyResolver::class);
        $this->app->singleton(ModuleMigrationRunner::class);
        $this->app->singleton(ModuleSeederRunner::class);
        $this->app->singleton(ModuleVersionManager::class);
        $this->app->singleton(ModuleConfigLoader::class);
        $this->app->singleton(ModuleActivator::class);
        $this->app->singleton(ModuleDeactivator::class);
        $this->app->singleton(ModuleUpdater::class);
        $this->app->singleton(ModuleUninstaller::class);
        $this->app->singleton(ModuleLoader::class);
        $this->app->singleton(ModuleLifecycleManager::class);
    }

    public function boot(ModuleLifecycleManager $manager): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', 'core-modules');

        $manager->boot();
    }
}