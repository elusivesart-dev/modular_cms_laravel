<?php

declare(strict_types=1);

namespace App\Core\Localization\Support;

use App\Core\Localization\DTO\LanguageData;

final class LocaleLabelResolver
{
    public function resolve(LanguageData $language): string
    {
        $nativeName = trim($language->nativeName);
        $name = trim($language->name);

        if ($nativeName !== '') {
            return $nativeName;
        }

        if ($name !== '') {
            return $name;
        }

        return strtoupper($language->code);
    }
}