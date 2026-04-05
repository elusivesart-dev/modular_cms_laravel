<?php

declare(strict_types=1);

namespace App\Core\Modules\Seeder;

use Illuminate\Support\Facades\Artisan;

final class ModuleSeederRunner
{
    public function run(string $class): void
    {
        Artisan::call('db:seed', [
            '--class' => $class,
            '--force' => true
        ]);
    }
}