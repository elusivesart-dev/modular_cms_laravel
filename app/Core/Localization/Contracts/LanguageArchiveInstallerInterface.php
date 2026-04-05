<?php

declare(strict_types=1);

namespace App\Core\Localization\Contracts;

use App\Core\Localization\Models\Language;

interface LanguageArchiveInstallerInterface
{
    public function installFromArchive(string $archivePath): Language;
}