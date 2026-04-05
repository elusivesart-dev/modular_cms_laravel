<?php

declare(strict_types=1);

namespace App\Core\Localization\Services;

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use App\Core\Localization\Exceptions\UnsupportedLocaleException;

final readonly class LanguageValidator
{
    public function __construct(
        private LanguageRegistryInterface $languages,
    ) {
    }

    public function ensureSupported(?string $locale): void
    {
        try {
            $this->languages->assertSupported($locale);
        } catch (UnsupportedLocaleException $exception) {
            throw $exception;
        }
    }
}