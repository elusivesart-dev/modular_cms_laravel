<?php

declare(strict_types=1);

namespace App\Core\Themes\Registry;

use App\Core\Themes\DTO\ThemeData;

final class ThemeRegistry
{
    /**
     * @var array<string, array<string, ThemeData>>
     */
    private array $themes = [];

    /**
     * @param array<int, ThemeData> $themes
     */
    public function registerMany(string $group, array $themes): void
    {
        foreach ($themes as $theme) {
            $this->themes[$group][$theme->slug] = $theme;
        }
    }

    /**
     * @return array<int, ThemeData>
     */
    public function all(string $group): array
    {
        return array_values($this->themes[$group] ?? []);
    }

    public function get(string $group, string $slug): ?ThemeData
    {
        return $this->themes[$group][$slug] ?? null;
    }

    public function has(string $group, string $slug): bool
    {
        return array_key_exists($slug, $this->themes[$group] ?? []);
    }
}