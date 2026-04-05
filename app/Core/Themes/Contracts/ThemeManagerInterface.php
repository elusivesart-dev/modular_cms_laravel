<?php

declare(strict_types=1);

namespace App\Core\Themes\Contracts;

use App\Core\Themes\DTO\ThemeData;

interface ThemeManagerInterface
{
    /**
     * @return array<int, ThemeData>
     */
    public function all(string $group): array;

    public function active(string $group): ThemeData;

    public function find(string $group, string $slug): ?ThemeData;

    public function exists(string $group, string $slug): bool;

    public function setActive(string $group, string $slug): void;
}