<?php

declare(strict_types=1);

namespace App\Core\Events\Providers;

use App\Core\Events\Bus\EventBus;
use App\Core\Events\Dispatcher\EventDispatcher;
use App\Core\Events\Hooks\HookManager;
use App\Core\Events\Registry\ListenerRegistry;
use App\Core\Localization\Events\DefaultLocaleChangedEvent;
use App\Core\Localization\Events\LanguageDeletedEvent;
use App\Core\Localization\Events\LanguageInstalledEvent;
use App\Core\Localization\Listeners\ClearLocalizationCache;
use App\Core\Localization\Listeners\LogDefaultLocaleChanged;
use App\Core\Localization\Listeners\LogLanguageDeleted;
use App\Core\Localization\Listeners\LogLanguageInstalled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ListenerRegistry::class);

        $this->app->singleton(EventDispatcher::class, function ($app) {
            return new EventDispatcher(
                $app->make(ListenerRegistry::class)
            );
        });

        $this->app->singleton(EventBus::class);

        $this->app->singleton(HookManager::class);
    }

    public function boot(): void
    {
        Event::listen(
            DefaultLocaleChangedEvent::class,
            LogDefaultLocaleChanged::class,
        );

        Event::listen(
            LanguageInstalledEvent::class,
            LogLanguageInstalled::class,
        );

        Event::listen(
            LanguageInstalledEvent::class,
            ClearLocalizationCache::class,
        );

        Event::listen(
            LanguageDeletedEvent::class,
            LogLanguageDeleted::class,
        );

        Event::listen(
            LanguageDeletedEvent::class,
            ClearLocalizationCache::class,
        );
    }
}