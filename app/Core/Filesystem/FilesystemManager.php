<?php

declare(strict_types=1);

namespace App\Core\Filesystem;

use Illuminate\Contracts\Filesystem\Factory;

final class FilesystemManager
{
    public function __construct(
        private readonly Factory $filesystem
    ) {}

    public function register(): void
    {
        $this->filesystem->disk();
    }
}