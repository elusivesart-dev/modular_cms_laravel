<?php

declare(strict_types=1);

namespace App\Core\Modules\Contracts;

interface ModuleInterface
{
    public function name(): string;

    public function version(): string;

    public function dependencies(): array;

    public function boot(): void;
}