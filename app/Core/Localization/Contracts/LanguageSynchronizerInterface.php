<?php

declare(strict_types=1);

namespace App\Core\Localization\Contracts;

interface LanguageSynchronizerInterface
{
    public function syncFallbackLanguages(): void;
}