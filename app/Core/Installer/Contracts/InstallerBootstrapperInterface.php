<?php

declare(strict_types=1);

namespace App\Core\Installer\Contracts;

use App\Core\Installer\DTO\InstallData;

interface InstallerBootstrapperInterface
{
    public function bootstrap(InstallData $data): void;
}