<?php

declare(strict_types=1);

namespace App\Core\Installer\Admin;

use App\Core\Installer\Contracts\InstallerBootstrapperInterface;
use App\Core\Installer\DTO\InstallData;

final class NullInstallerBootstrapper implements InstallerBootstrapperInterface
{
    public function bootstrap(InstallData $data): void
    {
    }
}