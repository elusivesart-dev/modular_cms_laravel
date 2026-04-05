<?php

declare(strict_types=1);

namespace App\Core\Localization\Contracts;

use App\Core\Localization\Models\Language;

interface LanguageInstallerInterface
{
    public function installFromDirectory(string $directory, bool $activate = true): Language;
}