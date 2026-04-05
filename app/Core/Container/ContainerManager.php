<?php

declare(strict_types=1);

namespace App\Core\Container;

use Illuminate\Contracts\Foundation\Application;

final class ContainerManager
{
    public function __construct(
        private readonly Application $app
    ) {}

    public function register(): void
    {
        $this->app->singleton(self::class, fn () => $this);
    }
}