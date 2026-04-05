<?php

declare(strict_types=1);

namespace App\Core\Installer\Contracts;

use App\Core\Installer\DTO\InstallData;

interface AdminCreatorInterface
{
    public function create(InstallData $data): void;
}