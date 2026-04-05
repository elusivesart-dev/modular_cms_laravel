<?php

declare(strict_types=1);

namespace App\Core\Cache;

use Illuminate\Contracts\Cache\Factory;

final class CacheManager
{
    public function __construct(
        private readonly Factory $cache
    ) {}

    public function register(): void
    {
        $this->cache->store();
    }
}