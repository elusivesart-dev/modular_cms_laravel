<?php

declare(strict_types=1);

namespace App\Core\Themes\Managers;

use App\Core\Themes\Contracts\ThemeManagerInterface;
use App\Core\Themes\Contracts\ThemeSettingsRepositoryInterface;
use App\Core\Themes\DTO\ThemeData;
use App\Core\Themes\Discovery\ThemeDiscovery;
use App\Core\Themes\Registry\ThemeRegistry;
use RuntimeException;

final class ThemeManager implements ThemeManagerInterface
{
    /**
     * @var array<string, bool>
     */
    private array $booted = [];

    public function __construct(
        private readonly ThemeDiscovery $discovery,
        private readonly ThemeRegistry $registry,
        private readonly ThemeSettingsRepositoryInterface $settings,
    ) {
    }

    /**
     * @return array<int, ThemeData>
     */
    public function all(string $group): array
    {
        $this->boot($group);

        return $this->registry->all($group);
    }

    public function active(string $group): ThemeData
    {
        $this->boot($group);

        $activeSlug = $this->resolveActiveThemeSlug($group);

        $theme = $this->registry->get($group, $activeSlug);
        if ($theme !== null) {
            return $theme;
        }

        $defaultKey = $group === 'admin' ? 'themes.default_admin' : 'themes.default_public';
        $defaultSlug = (string) config($defaultKey, 'default');

        $defaultTheme = $this->registry->get($group, $defaultSlug);
        if ($defaultTheme !== null) {
            return $defaultTheme;
        }

        $allThemes = $this->registry->all($group);
        if ($allThemes !== []) {
            return $allThemes[0];
        }

        throw new RuntimeException('No active theme could be resolved for group: ' . $group);
    }

    public function find(string $group, string $slug): ?ThemeData
    {
        $this->boot($group);

        return $this->registry->get($group, $slug);
    }

    public function exists(string $group, string $slug): bool
    {
        $this->boot($group);

        return $this->registry->has($group, $slug);
    }

    public function setActive(string $group, string $slug): void
    {
        $this->boot($group);

        $slug = trim($slug);

        if ($slug === '' || ! $this->registry->has($group, $slug)) {
            throw new RuntimeException('Theme not found: ' . $group . '/' . $slug);
        }

        $this->settings->setActiveThemeSlug($group, $slug);
    }

    private function resolveActiveThemeSlug(string $group): string
    {
        $settingsSlug = $this->settings->getActiveThemeSlug($group);

        if (is_string($settingsSlug) && $settingsSlug !== '') {
            return $settingsSlug;
        }

        return (string) config($group === 'admin' ? 'themes.default_admin' : 'themes.default_public', 'default');
    }

    private function boot(string $group): void
    {
        if ($this->booted[$group] ?? false) {
            return;
        }

        $this->registry->registerMany($group, $this->discovery->discover($group));
        $this->booted[$group] = true;
    }
}