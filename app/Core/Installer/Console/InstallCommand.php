<?php

declare(strict_types=1);

namespace App\Core\Installer\Console;

use App\Core\Installer\DTO\InstallData;
use App\Core\Installer\InstallerManager;
use Illuminate\Console\Command;
use Throwable;

final class InstallCommand extends Command
{
    protected $signature = 'cms:install
                            {--app-name=MCMS}
                            {--admin-name=Administrator}
                            {--admin-email=admin@example.com}
                            {--admin-password=}';

    protected $description = 'Install the CMS application';

    public function handle(InstallerManager $installerManager): int
    {
        $password = (string) $this->option('admin-password');

        if ($password === '') {
            $password = (string) $this->secret('Admin password');
        }

        $data = new InstallData(
            appName: (string) $this->option('app-name'),
            adminName: (string) $this->option('admin-name'),
            adminEmail: (string) $this->option('admin-email'),
            adminPassword: $password
        );

        try {
            $installerManager->install($data);
            $this->info('CMS installation completed successfully.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            report($exception);

            return self::FAILURE;
        }
    }
}