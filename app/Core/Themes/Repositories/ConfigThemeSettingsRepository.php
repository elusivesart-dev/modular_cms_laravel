<?php

declare(strict_types=1);

namespace App\Core\Themes\Repositories;

use App\Core\Themes\Contracts\ThemeSettingsRepositoryInterface;

final class ConfigThemeSettingsRepository implements ThemeSettingsRepositoryInterface
{
    public function getActiveThemeSlug(string $group): ?string
    {
        $key = match ($group) {
            'admin' => 'themes.default_admin',
            default => 'themes.default_public',
        };

        $slug = config($key);

        return is_string($slug) && trim($slug) !== '' ? trim($slug) : null;
    }

    public function setActiveThemeSlug(string $group, string $slug): void
    {
        $key = match ($group) {
            'admin' => 'themes.default_admin',
            default => 'themes.default_public',
        };

        config([$key => $slug]);
    }
}