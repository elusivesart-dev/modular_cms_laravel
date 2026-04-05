<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Core\Modules\Manifest\ModuleManifest;
use App\Core\Modules\Registry\ModuleRegistry;
use Tests\TestCase;

final class ModuleRegistryTest extends TestCase
{
    public function test_it_registers_and_returns_module_manifest(): void
    {
        $registry = new ModuleRegistry();

        $manifest = new ModuleManifest(
            name: 'Blog',
            provider: 'Modules\\Blog\\BlogServiceProvider',
            version: '1.0.0',
            dependencies: [],
        );

        $registry->register($manifest);

        $this->assertSame($manifest, $registry->get('Blog'));
        $this->assertCount(1, $registry->all());
    }

    public function test_it_returns_null_for_unknown_module(): void
    {
        $registry = new ModuleRegistry();

        $this->assertNull($registry->get('Unknown'));
    }
}