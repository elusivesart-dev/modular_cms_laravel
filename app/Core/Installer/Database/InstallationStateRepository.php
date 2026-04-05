<?php

declare(strict_types=1);

namespace App\Core\Installer\Database;

use Illuminate\Support\Facades\DB;

final class InstallationStateRepository
{
    public function isInstalled(): bool
    {
        if (!$this->tableExists()) {
            return false;
        }

        return DB::table('core_installations')->where('is_installed', true)->exists();
    }

    public function markInstalled(): void
    {
        DB::table('core_installations')->updateOrInsert(
            ['id' => 1],
            [
                'is_installed' => true,
                'installed_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    private function tableExists(): bool
    {
        return DB::getSchemaBuilder()->hasTable('core_installations');
    }
}