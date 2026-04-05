<?php

declare(strict_types=1);

namespace App\Core\Environment;

final class EnvironmentLoader
{
    public function load(): void
    {
        if (!file_exists(base_path('.env'))) {
            throw new \RuntimeException('.env file missing');
        }
    }
}