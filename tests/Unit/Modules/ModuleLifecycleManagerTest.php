<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Core\Modules\Lifecycle\ModuleLifecycleManager;
use App\Core\Modules\Loader\ModuleLoader;
use Mockery;
use Tests\TestCase;

final class ModuleLifecycleManagerTest extends TestCase
{
    public function test_it_boots_module_loader(): void
    {
        $loader = Mockery::mock(ModuleLoader::class);
        $loader->shouldReceive('load')->once();

        $manager = new ModuleLifecycleManager($loader);

        $manager->boot();

        $this->assertTrue(true);
    }
}