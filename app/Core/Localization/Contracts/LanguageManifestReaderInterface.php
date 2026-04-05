<?php

declare(strict_types=1);

namespace App\Core\Localization\Contracts;

use App\Core\Localization\DTO\LanguageManifestData;

interface LanguageManifestReaderInterface
{
    public function readFromDirectory(string $directory): LanguageManifestData;
}