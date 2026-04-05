<?php

declare(strict_types=1);

namespace App\Core\Themes\DTO;

final readonly class ThemeData
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $group,
        public string $path,
        public string $viewsPath,
        public string $assetsPath,
        public array $meta = [],
    ) {
    }
}