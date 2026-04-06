<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Core\Modules\Dependency\ModuleDependencyResolver;
use App\Core\Modules\Discovery\ModuleDiscovery;
use App\Core\Modules\Lifecycle\ModuleLifecycleManager;
use App\Core\Modules\Loader\ModuleLoader;
use App\Core\Modules\Registry\ModuleRegistry;
use Illuminate\Foundation\Application;
use Mockery;
use PHPUnit\Framework\TestCase;

final class ModuleLifecycleManagerTest extends TestCase
{
    public function test_it_boots_module_loader(): void
    {
        $app = new Application(dirname(__DIR__, 3));

        $registry = new ModuleRegistry();
        $discovery = new ModuleDiscovery();
        $resolver = new ModuleDependencyResolver($registry);

        $appMock = Mockery::mock($app)->makePartial();
        $appMock->shouldReceive('register')
            ->atLeast()
            ->once()
            ->andReturnNull();

        $loader = new ModuleLoader(
            discovery: $discovery,
            registry: $registry,
            resolver: $resolver,
            app: $appMock,
        );

        $manager = new ModuleLifecycleManager($loader);

        $manager->boot();

        $this->assertNotNull($registry->get('Users'));
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}