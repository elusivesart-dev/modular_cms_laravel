<?php

declare(strict_types=1);

namespace App\Core\Themes\Contracts;

interface ThemeSettingsRepositoryInterface
{
    public function getActiveThemeSlug(string $group): ?string;

    public function setActiveThemeSlug(string $group, string $slug): void;
}