<?php

declare(strict_types=1);

namespace App\Core\Themes\Providers;

use App\Core\Themes\Contracts\ThemeManagerInterface;
use App\Core\Themes\Contracts\ThemeSettingsRepositoryInterface;
use App\Core\Themes\Discovery\ThemeDiscovery;
use App\Core\Themes\Managers\ThemeManager;
use App\Core\Themes\Registry\ThemeRegistry;
use App\Core\Themes\Repositories\ConfigThemeSettingsRepository;
use App\Core\Themes\Repositories\DatabaseThemeSettingsRepository;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

final class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(config_path('themes.php'), 'themes');

        require_once app_path('Core/Themes/Support/ThemeAssetsHelper.php');

        $this->app->singleton(ThemeRegistry::class);
        $this->app->singleton(ThemeDiscovery::class);

        $this->app->singleton(ThemeSettingsRepositoryInterface::class, function () {
            return $this->canUseDatabaseSettings()
                ? $this->app->make(DatabaseThemeSettingsRepository::class)
                : new ConfigThemeSettingsRepository();
        });

        $this->app->singleton(ThemeManagerInterface::class, ThemeManager::class);
    }

    public function boot(ViewFactory $view): void
    {
        $this->loadViewsFrom(app_path('Core/Themes/Resources/views'), 'core-themes');
        $this->loadTranslationsFrom(app_path('Core/Themes/Resources/lang'), 'core-themes');

        Route::middleware(['web'])
            ->group(app_path('Core/Themes/Routes/web.php'));

        $themeManager = $this->app->make(ThemeManagerInterface::class);

        $activePublicTheme = $themeManager->active('public');
        $activeAdminTheme = $themeManager->active('admin');

        if (is_dir($activePublicTheme->viewsPath)) {
            $this->loadViewsFrom($activePublicTheme->viewsPath, 'public-theme');
        }

        if (is_dir($activeAdminTheme->viewsPath)) {
            $this->loadViewsFrom($activeAdminTheme->viewsPath, 'admin-theme');
        }

        $view->share('activePublicTheme', $activePublicTheme);
        $view->share('activeAdminTheme', $activeAdminTheme);
        $view->share('availablePublicThemes', $themeManager->all('public'));
        $view->share('availableAdminThemes', $themeManager->all('admin'));
    }

    private function canUseDatabaseSettings(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Throwable) {
            return false;
        }
    }
}