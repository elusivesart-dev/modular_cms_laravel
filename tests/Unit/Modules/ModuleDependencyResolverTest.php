<?php

declare(strict_types=1);

namespace Tests\Unit\Modules;

use App\Core\Modules\Dependency\ModuleDependencyResolver;
use App\Core\Modules\Manifest\ModuleManifest;
use App\Core\Modules\Registry\ModuleRegistry;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ModuleDependencyResolverTest extends TestCase
{
    public function test_it_validates_module_without_dependencies(): void
    {
        $registry = new ModuleRegistry();
        $resolver = new ModuleDependencyResolver($registry);

        $registry->register(new ModuleManifest(
            name: 'Users',
            version: '1.0.0',
            dependencies: [],
            provider: 'Modules\\Users\\Application\\Providers\\UsersServiceProvider',
        ));

        $resolver->validate('Users');

        $this->assertTrue(true);
    }

    public function test_it_validates_module_with_existing_dependencies(): void
    {
        $registry = new ModuleRegistry();
        $resolver = new ModuleDependencyResolver($registry);

        $registry->register(new ModuleManifest(
            name: 'Users',
            version: '1.0.0',
            dependencies: [],
            provider: 'Modules\\Users\\Application\\Providers\\UsersServiceProvider',
        ));

        $registry->register(new ModuleManifest(
            name: 'Posts',
            version: '1.0.0',
            dependencies: ['Users'],
            provider: 'Modules\\Posts\\Application\\Providers\\PostsServiceProvider',
        ));

        $resolver->validate('Posts');

        $this->assertTrue(true);
    }

    public function test_it_throws_when_module_manifest_is_missing(): void
    {
        $registry = new ModuleRegistry();
        $resolver = new ModuleDependencyResolver($registry);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Module manifest not found: MissingModule');

        $resolver->validate('MissingModule');
    }

    public function test_it_throws_when_dependency_is_missing(): void
    {
        $registry = new ModuleRegistry();
        $resolver = new ModuleDependencyResolver($registry);

        $registry->register(new ModuleManifest(
            name: 'Posts',
            version: '1.0.0',
            dependencies: ['Users'],
            provider: 'Modules\\Posts\\Application\\Providers\\PostsServiceProvider',
        ));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing module dependency [Users] required by [Posts]');

        $resolver->validate('Posts');
    }
}