<?php

declare(strict_types=1);

namespace App\Core\Localization\Providers;

use App\Core\Localization\Contracts\LanguageArchiveInstallerInterface;
use App\Core\Localization\Contracts\LanguageDeleterInterface;
use App\Core\Localization\Contracts\LanguageInstallerInterface;
use App\Core\Localization\Contracts\LanguageManifestReaderInterface;
use App\Core\Localization\Contracts\LanguageRegistryInterface;
use App\Core\Localization\Contracts\LanguageRepositoryInterface;
use App\Core\Localization\Contracts\LanguageSynchronizerInterface;
use App\Core\Localization\Http\Middleware\LocalizationPermissionMiddleware;
use App\Core\Localization\Infrastructure\Filesystem\LanguageDirectoryResolver;
use App\Core\Localization\Infrastructure\Filesystem\LanguageFilesystemValidator;
use App\Core\Localization\Infrastructure\Filesystem\LanguagePackageExtractor;
use App\Core\Localization\Infrastructure\Filesystem\LanguagePackageRootResolver;
use App\Core\Localization\Infrastructure\Manifests\JsonLanguageManifestReader;
use App\Core\Localization\Infrastructure\Persistence\LanguageRepository;
use App\Core\Localization\Registry\LanguageRegistry;
use App\Core\Localization\Services\LanguageArchiveInstaller;
use App\Core\Localization\Services\LanguageDeleter;
use App\Core\Localization\Services\LanguageInstaller;
use App\Core\Localization\Services\LanguageSynchronizer;
use App\Core\Localization\Services\LanguageValidator;
use App\Core\Localization\Services\LocalizationAuditLogger;
use App\Core\Localization\Services\LocalizationAuthorizer;
use App\Core\Localization\Support\LocaleCodeNormalizer;
use App\Core\Localization\Support\LocaleLabelResolver;
use Illuminate\Database\QueryException;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use PDOException;
use Throwable;

final class LocalizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            config_path('localization.php'),
            'localization'
        );

        $this->app->singleton(LocaleCodeNormalizer::class);
        $this->app->singleton(LocaleLabelResolver::class);
        $this->app->singleton(LanguageDirectoryResolver::class);
        $this->app->singleton(LanguageFilesystemValidator::class);
        $this->app->singleton(LanguagePackageExtractor::class);
        $this->app->singleton(LanguagePackageRootResolver::class);
        $this->app->singleton(LanguageValidator::class);

        $this->app->singleton(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->singleton(LanguageManifestReaderInterface::class, JsonLanguageManifestReader::class);
        $this->app->singleton(LanguageRegistryInterface::class, LanguageRegistry::class);
        $this->app->singleton(LanguageSynchronizerInterface::class, LanguageSynchronizer::class);
        $this->app->singleton(LanguageInstallerInterface::class, LanguageInstaller::class);
        $this->app->singleton(LanguageArchiveInstallerInterface::class, LanguageArchiveInstaller::class);
        $this->app->singleton(LanguageDeleterInterface::class, LanguageDeleter::class);

        $this->app->singleton(LocalizationAuthorizer::class);
        $this->app->singleton(LocalizationAuditLogger::class);
    }

    public function boot(
        Router $router,
        LanguageSynchronizerInterface $synchronizer,
    ): void {
        $router->aliasMiddleware('localization.permission', LocalizationPermissionMiddleware::class);

        $this->loadMigrationsFrom(app_path('Core/Localization/Migrations'));
        $this->loadTranslationsFrom(app_path('Core/Localization/Resources/lang'), 'core-localization');
        $this->loadViewsFrom(app_path('Core/Localization/Resources/views'), 'core-localization');
        $this->loadRoutesFrom(app_path('Core/Localization/Routes/web.php'));

        if (!$this->shouldRunSynchronizer()) {
            return;
        }

        try {
            $synchronizer->syncFallbackLanguages();
        } catch (Throwable $exception) {
            if ($this->app->runningInConsole()) {
                return;
            }

            throw $exception;
        }
    }

    private function shouldRunSynchronizer(): bool
    {
        try {
            return Schema::hasTable('languages');
        } catch (PDOException|QueryException|Throwable) {
            return false;
        }
    }
}