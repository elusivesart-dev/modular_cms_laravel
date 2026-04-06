<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Core\Modules\Dependency\ModuleDependencyResolver;
use App\Core\Modules\Discovery\ModuleDiscovery;
use App\Core\Modules\Loader\ModuleLoader;
use App\Core\Modules\Registry\ModuleRegistry;
use Illuminate\Foundation\Application;
use Mockery;
use PHPUnit\Framework\TestCase;

final class ModuleLoaderTest extends TestCase
{
    public function test_it_registers_discovers_validates_and_registers_providers(): void
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

        $loader->load();

        $this->assertNotNull($registry->get('Auth'));
        $this->assertNotNull($registry->get('Users'));
        $this->assertNotNull($registry->get('Roles'));
        $this->assertNotNull($registry->get('Permissions'));

        $appMock->shouldHaveReceived('register')->atLeast()->once();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}