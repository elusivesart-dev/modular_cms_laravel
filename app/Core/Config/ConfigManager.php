<?php

declare(strict_types=1);

namespace App\Core\Config;

use Illuminate\Contracts\Config\Repository;

final class ConfigManager
{
    public function __construct(
        private readonly Repository $config
    ) {}

    public function load(): void
    {
        $this->config->set('app.booted', true);
    }
}