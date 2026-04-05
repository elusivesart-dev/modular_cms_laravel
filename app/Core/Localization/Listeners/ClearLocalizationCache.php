<?php

declare(strict_types=1);

namespace App\Core\Localization\Listeners;

use App\Core\Localization\Services\LocalizationCache;

final readonly class ClearLocalizationCache
{
    public function __construct(
        private LocalizationCache $cache,
    ) {
    }

    public function handle(object $event): void
    {
        $this->cache->forgetAll();
    }
}