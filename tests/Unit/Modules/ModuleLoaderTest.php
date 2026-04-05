<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Core\Modules\Dependency\ModuleDependencyResolver;
use App\Core\Modules\Discovery\ModuleDiscovery;
use App\Core\Modules\Loader\ModuleLoader;
use App\Core\Modules\Manifest\ModuleManifest;
use App\Core\Modules\Registry\ModuleRegistry;
use Illuminate\Contracts\Foundation\Application;
use Mockery;
use Tests\TestCase;

final class ModuleLoaderTest extends TestCase
{
    public function test_it_registers_discovers_validates_and_registers_providers(): void
    {
        $manifestA = new ModuleManifest(
            name: 'Users',
            provider: 'Modules\\Users\\UsersServiceProvider',
            version: '1.0.0',
            dependencies: [],
        );

        $manifestB = new ModuleManifest(
            name: 'Posts',
            provider: 'Modules\\Posts\\PostsServiceProvider',
            version: '1.0.0',
            dependencies: ['Users'],
        );

        $discovery = Mockery::mock(ModuleDiscovery::class);
        $registry = Mockery::mock(ModuleRegistry::class);
        $resolver = Mockery::mock(ModuleDependencyResolver::class);
        $app = Mockery::mock(Application::class);

        $discovery->shouldReceive('discover')
            ->once()
            ->andReturn([$manifestA, $manifestB]);

        $registry->shouldReceive('register')
            ->once()
            ->with($manifestA);

        $registry->shouldReceive('register')
            ->once()
            ->with($manifestB);

        $registry->shouldReceive('all')
            ->once()
            ->andReturn([
                $manifestA,
                $manifestB,
            ]);

        $resolver->shouldReceive('validate')
            ->once()
            ->with('Users');

        $resolver->shouldReceive('validate')
            ->once()
            ->with('Posts');

        $app->shouldReceive('register')
            ->once()
            ->with('Modules\\Users\\UsersServiceProvider');

        $app->shouldReceive('register')
            ->once()
            ->with('Modules\\Posts\\PostsServiceProvider');

        $loader = new ModuleLoader(
            $discovery,
            $registry,
            $resolver,
            $app,
        );

        $loader->load();

        $this->assertTrue(true);
    }
}