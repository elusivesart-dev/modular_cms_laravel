<?php

declare(strict_types=1);

namespace App\Core\Installer\Migration;

use Illuminate\Support\Facades\Artisan;
use RuntimeException;

final class InstallerMigrationRunner
{
    public function run(): void
    {
        $exitCode = Artisan::call('migrate', [
            '--force' => true,
        ]);

        if ($exitCode !== 0) {
            throw new RuntimeException('Migration execution failed.');
        }
    }
}