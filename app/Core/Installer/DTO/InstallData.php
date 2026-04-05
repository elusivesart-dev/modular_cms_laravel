<?php

declare(strict_types=1);

namespace App\Core\Installer\DTO;

final readonly class InstallData
{
    public function __construct(
        public string $appName,
        public string $adminName,
        public string $adminEmail,
        public string $adminPassword
    ) {
    }

    public function toArray(): array
    {
        return [
            'app_name' => $this->appName,
            'admin_name' => $this->adminName,
            'admin_email' => $this->adminEmail,
            'admin_password' => $this->adminPassword,
        ];
    }
}