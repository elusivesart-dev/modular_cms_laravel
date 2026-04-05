<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Core\Modules\Dependency\ModuleDependencyResolver;
use App\Core\Modules\Manifest\ModuleManifest;
use App\Core\Modules\Registry\ModuleRegistry;
use RuntimeException;
use Tests\TestCase;

final class ModuleDependencyResolverTest extends TestCase
{
    public function test_it_validates_module_without_dependencies(): void
    {
        $registry = new ModuleRegistry();

        $registry->register(new ModuleManifest(
            name: 'Users',
            provider: 'Modules\\Users\\UsersServiceProvider',
            version: '1.0.0',
            dependencies: [],
        ));

        $resolver = new ModuleDependencyResolver($registry);

        $resolver->validate('Users');

        $this->assertTrue(true);
    }

    public function test_it_validates_module_with_existing_dependencies(): void
    {
        $registry = new ModuleRegistry();

        $registry->register(new ModuleManifest(
            name: 'Users',
            provider: 'Modules\\Users\\UsersServiceProvider',
            version: '1.0.0',
            dependencies: [],
        ));

        $registry->register(new ModuleManifest(
            name: 'Posts',
            provider: 'Modules\\Posts\\PostsServiceProvider',
            version: '1.0.0',
            dependencies: ['Users'],
        ));

        $resolver = new ModuleDependencyResolver($registry);

        $resolver->validate('Posts');

        $this->assertTrue(true);
    }

    public function test_it_throws_when_module_manifest_is_missing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Module manifest not found: Missing');

        $registry = new ModuleRegistry();
        $resolver = new ModuleDependencyResolver($registry);

        $resolver->validate('Missing');
    }

    public function test_it_throws_when_dependency_is_missing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing module dependency: Users');

        $registry = new ModuleRegistry();

        $registry->register(new ModuleManifest(
            name: 'Posts',
            provider: 'Modules\\Posts\\PostsServiceProvider',
            version: '1.0.0',
            dependencies: ['Users'],
        ));

        $resolver = new ModuleDependencyResolver($registry);

        $resolver->validate('Posts');
    }
}