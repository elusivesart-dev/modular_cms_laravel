<?php

declare(strict_types=1);

namespace Modules\Media\Application\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Media\Application\Contracts\MediaServiceInterface;
use Modules\Media\Application\Policies\MediaPolicy;
use Modules\Media\Application\Services\MediaService;
use Modules\Media\Domain\Contracts\MediaRepositoryInterface;
use Modules\Media\Infrastructure\Models\Media;
use Modules\Media\Infrastructure\Repositories\MediaRepository;

final class MediaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../Config/media.php',
            'media'
        );

        $this->app->singleton(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->singleton(MediaServiceInterface::class, MediaService::class);
        $this->app->singleton(MediaService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../../UI/Resources/views', 'media');
        $this->loadTranslationsFrom(__DIR__ . '/../../UI/Resources/lang', 'media');
        $this->loadMigrationsFrom(__DIR__ . '/../../Infrastructure/Database/Migrations');

        Gate::policy(Media::class, MediaPolicy::class);

        $this->publishes([
            __DIR__ . '/../../Config/media.php' => config_path('media.php'),
        ], 'media-config');
    }
}