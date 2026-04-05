<?php

declare(strict_types=1);

namespace App\Core\Modules\Migration;

use Illuminate\Support\Facades\Artisan;

final class ModuleMigrationRunner
{
    public function run(string $path): void
    {
        Artisan::call('migrate', [
            '--path' => $path,
            '--force' => true
        ]);
    }
}