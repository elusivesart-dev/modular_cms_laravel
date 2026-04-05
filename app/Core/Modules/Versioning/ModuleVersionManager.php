<?php

declare(strict_types=1);

namespace App\Core\Modules\Versioning;

final class ModuleVersionManager
{
    public function validate(string $version): bool
    {
        return (bool) preg_match('/^\d+\.\d+\.\d+$/', $version);
    }
}