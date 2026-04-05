<?php

declare(strict_types=1);

namespace App\Core\Modules\Manifest;

final class ModuleManifest
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly array $dependencies,
        public readonly string $provider
    ) {}
}