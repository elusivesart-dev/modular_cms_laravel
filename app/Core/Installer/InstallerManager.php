<?php

declare(strict_types=1);

namespace App\Core\Installer;

use App\Core\Installer\Contracts\AdminCreatorInterface;
use App\Core\Installer\Contracts\InstallerBootstrapperInterface;
use App\Core\Installer\Database\DatabaseInstaller;
use App\Core\Installer\Database\InstallationStateRepository;
use App\Core\Installer\DTO\InstallData;
use App\Core\Installer\Environment\EnvironmentChecker;
use App\Core\Installer\Migration\InstallerMigrationRunner;
use App\Core\Installer\Modules\ModuleInitializer;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class InstallerManager
{
    public function __construct(
        private readonly EnvironmentChecker $environmentChecker,
        private readonly DatabaseInstaller $databaseInstaller,
        private readonly InstallerMigrationRunner $migrationRunner,
        private readonly InstallationStateRepository $installationStateRepository,
        private readonly ModuleInitializer $moduleInitializer,
        private readonly AdminCreatorInterface $adminCreator,
        private readonly InstallerBootstrapperInterface $bootstrapper,
    ) {
    }

    public function install(InstallData $data): void
    {
        if ($this->installationStateRepository->isInstalled()) {
            throw new RuntimeException('Application is already installed.');
        }

        $this->environmentChecker->check();
        $this->databaseInstaller->checkConnection();
        $this->migrationRunner->run();

        try {
            DB::transaction(function () use ($data): void {
                $this->moduleInitializer->initialize();
                $this->adminCreator->create($data);
                $this->bootstrapper->bootstrap($data);
                $this->installationStateRepository->markInstalled();
            });
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'Installation failed and was rolled back.',
                previous: $exception,
            );
        }
    }
}