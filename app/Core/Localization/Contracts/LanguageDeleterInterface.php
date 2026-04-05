<?php

declare(strict_types=1);

namespace App\Core\Localization\Contracts;

interface LanguageDeleterInterface
{
    public function deleteByCode(string $code, ?string $currentDefaultLocale = null): void;
}